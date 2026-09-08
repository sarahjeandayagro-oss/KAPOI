<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\OverdueBookReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendOverdueReminders extends Command
{
    protected $signature = 'library:send-overdue-reminders';
    protected $description = 'Send overdue book reminder notifications to borrowers';

    public function handle(): int
    {
        $overdueBorrows = DB::table('borrowing_transactions')
            ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
            ->where('borrowing_transactions.status', 'Borrowed')
            ->where('borrowing_transactions.due_date', '<', now()->toDateString())
            ->select('borrowing_transactions.*', 'books.title')
            ->get();

        $count = 0;

        foreach ($overdueBorrows as $borrow) {
            $user = User::where('user_id', $borrow->user_id)
                ->orWhere('email', $borrow->user_id)
                ->first();

            if ($user) {
                $dueDate = Carbon::parse($borrow->due_date);
                $daysOverdue = max(1, (int) $dueDate->diffInDays(now()));

                $user->notify(new OverdueBookReminder(
                    bookTitle: $borrow->title ?? $borrow->book_barcode ?? 'Unknown Book',
                    dueDate: $dueDate->format('M d, Y'),
                    daysOverdue: $daysOverdue,
                ));

                $count++;
            }
        }

        $this->info("Sent {$count} overdue reminders.");
        return Command::SUCCESS;
    }
}
