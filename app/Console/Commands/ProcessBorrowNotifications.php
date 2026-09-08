<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Notifications\OverdueBookReminder;
use App\Notifications\FineIssued;

class ProcessBorrowNotifications extends Command
{
    protected $signature = 'borrow:process-notifications';
    protected $description = 'Send due reminders and automatically issue fines for overdue borrowings';

    public function handle(): int
    {
        if (! Schema::hasTable('borrowing_transactions')) {
            $this->info('No borrowing_transactions table found — skipping.');
            return 0;
        }

        $today = Carbon::today();

        // 0) Notify users whose due date is within the upcoming window (e.g., next 3 days)
        $dueSoonDays = 3; // change this value to adjust reminder window
        $dueSoonEnd = $today->copy()->addDays($dueSoonDays);

        $dueSoon = DB::table('borrowing_transactions')
            ->where('status', 'Borrowed')
            ->whereDate('due_date', '>', $today->toDateString())
            ->whereDate('due_date', '<=', $dueSoonEnd->toDateString())
            ->get();

        foreach ($dueSoon as $b) {
            try {
                $user = User::where('user_id', $b->user_id)->orWhere('email', $b->user_id)->first();
                if ($user) {
                    $dueDate = Carbon::parse($b->due_date);
                    $daysUntil = max(0, (int) \Carbon\Carbon::now()->diffInDays($dueDate, false));

                    // Use a dedicated UpcomingDueReminder so email copy is clear
                    $user->notify(new \App\Notifications\UpcomingDueReminder(
                        bookTitle: $b->title ?? 'Borrowed Book',
                        dueDate: $dueDate->format('M d, Y'),
                        daysUntilDue: $daysUntil
                    ));
                }
            } catch (\Throwable $e) {
                Log::error('Error sending upcoming due reminder: ' . $e->getMessage());
            }
        }

        // 1) Notify users whose due date is today
        $dueToday = DB::table('borrowing_transactions')
            ->where('status', 'Borrowed')
            ->whereDate('due_date', $today->toDateString())
            ->get();

        foreach ($dueToday as $b) {
            try {
                $user = User::where('user_id', $b->user_id)->orWhere('email', $b->user_id)->first();
                if ($user) {
                    $user->notify(new OverdueBookReminder(
                        bookTitle: $b->title ?? 'Borrowed Book',
                        dueDate: Carbon::parse($b->due_date)->format('M d, Y'),
                        daysOverdue: 0
                    ));
                }

                // Add an internal announcement for admins/staff
                if (Schema::hasTable('announcements')) {
                    DB::table('announcements')->insert([
                        'title' => 'Due Reminder: ' . ($b->title ?? 'Borrowed Book'),
                        'body' => "The book '{$b->title}' borrowed by {$b->barcode_id} is due today ({$b->due_date}).",
                        'recipients' => 'staff',
                        'created_by' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Error sending due reminder: ' . $e->getMessage());
            }
        }

        // 2) Issue automatic fine when overdue >= 7 days — PHP 20 per week (cumulative)
        $overdue = DB::table('borrowing_transactions')
            ->where('status', 'Borrowed')
            ->whereDate('due_date', '<', $today->toDateString())
            ->get();

        foreach ($overdue as $b) {
            try {
                $daysOverdue = Carbon::parse($b->due_date)->diffInDays($today);
                if ($daysOverdue < 7) {
                    continue;
                }

                $weeksOverdue = intdiv($daysOverdue, 7);
                $amount = 20.00 * max(1, $weeksOverdue);

                // Find existing fine record for this borrowing (if any)
                $existing = DB::table('financial_transactions')
                    ->where('transaction_type', 'Fine')
                    ->where('description', 'like', "%borrowing #{$b->id}%")
                    ->first();

                if ($existing) {
                    $existingAmount = (float) $existing->amount;
                    if (abs($existingAmount - $amount) > 0.001) {
                        // Update the fine amount to the new cumulative value
                        DB::table('financial_transactions')->where('id', $existing->id)->update([
                            'amount' => $amount,
                            'transaction_date' => now()->toDateString(),
                            'updated_at' => now(),
                        ]);

                        // Notify borrower about updated fine
                        $borrower = User::where('user_id', $b->user_id)->orWhere('email', $b->user_id)->first();
                        if ($borrower) {
                            $borrower->notify(new FineIssued(
                                description: "Updated automatic overdue fine for borrowing #{$b->id}",
                                amount: $amount,
                            ));
                        }

                        // Announce update to staff/admins
                        if (Schema::hasTable('announcements')) {
                            DB::table('announcements')->insert([
                                'title' => 'Automatic Fine Updated',
                                'body' => "An automatic fine was updated to PHP " . number_format($amount,2) . " for borrowing #{$b->id} (" . ($b->title ?? 'book') . ")",
                                'recipients' => 'staff',
                                'created_by' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                    // if existing amount matches, do nothing
                    continue;
                }

                // Insert new fine record
                DB::table('financial_transactions')->insert([
                    'transaction_type' => 'Fine',
                    'patron_name' => $b->barcode_id ?? null,
                    'user_id' => $b->user_id ?? $b->barcode_id,
                    'description' => "Automatic overdue fine for borrowing #{$b->id} (" . ($b->title ?? 'book') . ")",
                    'amount' => $amount,
                    'status' => 'Unpaid',
                    'transaction_date' => now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Notify borrower
                $borrower = User::where('user_id', $b->user_id)->orWhere('email', $b->user_id)->first();
                if ($borrower) {
                    $borrower->notify(new FineIssued(
                        description: "Automatic overdue fine for borrowing #{$b->id}",
                        amount: $amount,
                    ));
                }

                // Announce to staff/admins
                if (Schema::hasTable('announcements')) {
                    DB::table('announcements')->insert([
                        'title' => 'Automatic Fine Issued',
                        'body' => "An automatic fine (PHP " . number_format($amount,2) . ") was issued for borrowing #{$b->id} (" . ($b->title ?? 'book') . ")",
                        'recipients' => 'staff',
                        'created_by' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

            } catch (\Throwable $e) {
                Log::error('Error issuing automatic fine: ' . $e->getMessage());
            }
        }

        $this->info('Borrow notifications processed.');

        return 0;
    }
}
