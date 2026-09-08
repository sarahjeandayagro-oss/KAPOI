<?php

namespace App\Http\Controllers;

use App\Models\BarcodeRequest;
use App\Models\Member;
use App\Models\StaffAttendanceLog;
use App\Models\User;
use App\Notifications\AccountVerified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- Required for database counts and grouping
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class StaffController extends Controller
{
    public function index()
    {
        // 1. Fetch pending student, researcher, and visitor account registrations
        $pendingResearchers = User::where('role', 'researcher')
            ->whereRaw('LOWER(status) = ?', ['pending'])
            ->orderByDesc('created_at')
            ->get();
        $pendingStudents = User::where('role', 'student')
            ->whereRaw('LOWER(status) = ?', ['pending'])
            ->orderByDesc('created_at')
            ->get();
        $pendingVisitors = User::where('role', 'visitor')
            ->whereRaw('LOWER(status) = ?', ['pending'])
            ->orderByDesc('created_at')
            ->get();
        $pendingUsers = User::whereIn('role', ['student', 'researcher', 'visitor'])
            ->whereRaw('LOWER(status) = ?', ['pending'])
            ->orderByDesc('created_at')
            ->get();

        // 2. Fetch the 10 most recent staff attendance entries
        $realtimeAttendance = Schema::hasTable('staff_attendance_logs')
            ? StaffAttendanceLog::query()
                ->latest('check_in_at')
                ->limit(10)
                ->get()
                ->map(function (StaffAttendanceLog $log) {
                    return (object) [
                        'name' => $log->full_name,
                        'user_id' => $log->user_id,
                        'role' => $log->role,
                        'type' => $log->status === 'Checked Out' ? 'OUT' : 'IN',
                        'created_at' => $log->check_out_at ?? $log->check_in_at,
                        'remarks' => $log->remarks,
                    ];
                })
            : collect();

        $todayAttendanceCount = Schema::hasTable('staff_attendance_logs')
            ? StaffAttendanceLog::whereDate('attendance_date', today())->count()
            : 0;

        $activeStaffCount = Schema::hasTable('staff_attendance_logs')
            ? StaffAttendanceLog::whereDate('attendance_date', today())
                ->where('status', 'Checked In')
                ->count()
            : 0;

        $staffAttendanceSummary = Schema::hasTable('staff_attendance_logs')
            ? DB::table('staff_attendance_logs')
                ->select(
                    'user_id',
                    'full_name as name',
                    'role',
                    DB::raw('COUNT(*) as total_logs'),
                    DB::raw('MAX(check_in_at) as latest_check_in')
                )
                ->groupBy('user_id', 'full_name', 'role')
                ->orderByDesc('total_logs')
                ->limit(5)
                ->get()
            : collect();

        // 3. Calculate repeat visitors based on check-in frequency counts
        $repeatVisitors = Schema::hasTable('entry_logs')
            ? DB::table('entry_logs')
                ->join('members', 'entry_logs.member_id', '=', 'members.id')
                ->select(
                    'members.full_name as name',
                    'members.barcode_id as user_id',
                    DB::raw("'member' as role"),
                    DB::raw('COUNT(*) as total_visits')
                )
                ->groupBy('members.barcode_id', 'members.full_name')
                ->orderBy('total_visits', 'desc')
                ->limit(5)
                ->get()
            : collect();

        // 4. NETWORK LOGIC: Fetch high frequency Wi-Fi voucher consumers based on usage columns
        $wifiConsumers = DB::table('wifi_vouchers')
                           ->select('name', 'user_id', 'role', 'voucher_code', 'bandwidth_gb')
                           ->orderBy('bandwidth_gb', 'desc')
                           ->limit(5)
                           ->get();

        $availableBarcodes = Member::query()
            ->where('status', 'available')
            ->orderBy('barcode_id')
            ->get();

        // Dashboard analytics shared with admin dashboard
        $totalBooks = Schema::hasTable('books')
            ? DB::table('books')->whereNull('archived_at')->count()
            : 0;
        $activeBorrowers = Schema::hasTable('borrowing_transactions')
            ? DB::table('borrowing_transactions')->where('status', 'Borrowed')->distinct()->count('barcode_id')
            : 0;
        $returnedToday = Schema::hasTable('borrowing_transactions')
            ? DB::table('borrowing_transactions')->whereDate('returned_at', today())->count()
            : 0;
        $overdueBooks = Schema::hasTable('borrowing_transactions')
            ? DB::table('borrowing_transactions')->where('status', 'Borrowed')->where('due_date', '<', today())->count()
            : 0;

        $femaleCount = Schema::hasTable('users')
            ? User::where('gender', 'Female')->count()
            : 0;
        $maleCount = Schema::hasTable('users')
            ? User::where('gender', 'Male')->count()
            : 0;
        $totalGender = $femaleCount + $maleCount;
        $femalePct = $totalGender > 0 ? round(($femaleCount / $totalGender) * 100) : 0;
        $malePct = $totalGender > 0 ? round(($maleCount / $totalGender) * 100) : 0;

        $monthlyBorrowing = [];
        if (Schema::hasTable('borrowing_transactions')) {
            for ($m = 1; $m <= 12; $m++) {
                $monthlyBorrowing[$m] = DB::table('borrowing_transactions')
                    ->whereYear('borrow_date', now()->year)
                    ->whereMonth('borrow_date', $m)
                    ->count();
            }
        }

        $recentBorrowings = Schema::hasTable('borrowing_transactions')
            ? DB::table('borrowing_transactions')
                ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
                ->leftJoin('users', 'borrowing_transactions.user_id', '=', 'users.user_id')
                ->leftJoin('members', 'borrowing_transactions.member_id', '=', 'members.id')
                ->select(
                    'borrowing_transactions.*',
                    'books.title',
                    'books.author',
                    'books.isbn',
                    'books.call_number',
                    'books.accession_number',
                    'books.cover_image',
                    DB::raw("COALESCE(users.name, members.full_name, borrowing_transactions.barcode_id, 'Member') as borrower_name")
                )
                ->latest('borrowing_transactions.created_at')
                ->limit(5)
                ->get()
            : collect();

        $recentActivity = collect();
        if (Schema::hasTable('borrowing_transactions')) {
            $recentBorrows = DB::table('borrowing_transactions')
                ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
                ->select('borrowing_transactions.barcode_id', 'books.title', 'borrowing_transactions.created_at', DB::raw("'borrow' as action_type"))
                ->latest('borrowing_transactions.created_at')
                ->limit(3)
                ->get();
            foreach ($recentBorrows as $b) {
                $recentActivity->push((object) [
                    'text' => "<b>{$b->barcode_id}</b> borrowed <b>{$b->title}</b>",
                    'time' => Carbon::parse($b->created_at)->diffForHumans(),
                    'time_parsed' => Carbon::parse($b->created_at)->timestamp,
                    'type' => 'borrow',
                ]);
            }
        }
        if (Schema::hasTable('entry_logs')) {
            $recentEntries = DB::table('entry_logs')
                ->leftJoin('members', 'entry_logs.member_id', '=', 'members.id')
                ->select('members.full_name', 'entry_logs.entry_time', DB::raw("'entry' as action_type"))
                ->latest('entry_logs.entry_time')
                ->limit(3)
                ->get();
            foreach ($recentEntries as $e) {
                $recentActivity->push((object) [
                    'text' => "<b>{$e->full_name}</b> entered the library",
                    'time' => Carbon::parse($e->entry_time)->diffForHumans(),
                    'time_parsed' => Carbon::parse($e->entry_time)->timestamp,
                    'type' => 'entry',
                ]);
            }
        }
        $recentActivity = $recentActivity->sortByDesc('time_parsed')->take(5);

        $juneStart = Carbon::create(2026, 6, 1)->startOfDay();
        $juneEnd = Carbon::create(2026, 6, 30)->endOfDay();
        $totalVisitorsThisMonth = Schema::hasTable('entry_logs')
            ? DB::table('entry_logs')->whereBetween('entry_time', [$juneStart, $juneEnd])->count()
            : 0;

        // Announcements for staff
        $announcements = Schema::hasTable('announcements')
            ? DB::table('announcements')
                ->whereIn('recipients', ['all', 'staff'])
                ->where(function ($query) {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                })
                ->latest('created_at')
                ->limit(5)
                ->get()
            : collect();

        // Verified users list for the verify accounts panel
        $verifiedUsers = User::whereRaw('LOWER(status) = ?', ['approved'])
            ->whereIn('role', ['student', 'researcher', 'visitor'])
            ->latest('updated_at')
            ->get();

        // Rejected users list for the verify accounts panel
        $rejectedUsers = User::whereRaw('LOWER(status) = ?', ['rejected'])
            ->whereIn('role', ['student', 'researcher', 'visitor'])
            ->latest('updated_at')
            ->get();

        // All users (students, researchers, visitors) for unified management table
        $allUsers = User::whereIn('role', ['student', 'researcher', 'visitor'])
            ->latest('created_at')
            ->get();

        // Book collection for staff to manage
        $books = DB::table('books')->whereNull('archived_at')->orderBy('title')->get();

        // Borrowing transactions for staff to manage
        $borrowQuery = DB::table('borrowing_transactions')
            ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
            ->leftJoin('users', 'borrowing_transactions.user_id', '=', 'users.user_id')
            ->leftJoin('members', 'borrowing_transactions.member_id', '=', 'members.id')
            ->select(
                'borrowing_transactions.*',
                'books.title',
                DB::raw("COALESCE(users.name, members.full_name, borrowing_transactions.barcode_id) as borrower_name")
            );

        if (Schema::hasColumn('borrowing_transactions', 'returned_by')) {
            $borrowQuery->addSelect('borrowing_transactions.returned_by');
        }

        if (Schema::hasColumn('borrowing_transactions', 'status')) {
            $borrowQuery->whereNotIn('borrowing_transactions.status', ['Pending']);
        }

        $borrowings = Schema::hasTable('borrowing_transactions')
            ? $borrowQuery->latest('borrowing_transactions.created_at')->get()
            : collect();

        // If no real borrowings, create sample data from verified accounts and books
        if ($borrowings->isEmpty()) {
            $verifiedBorrowers = User::whereRaw('LOWER(status) = ?', ['approved'])
                ->whereIn('role', ['student', 'researcher', 'visitor'])
                ->limit(3)
                ->get();

            $sampleBooks = DB::table('books')->whereNull('archived_at')->limit(3)->get();

            $sampleData = [];
            $borrowerIndex = 0;
            foreach ($sampleBooks as $index => $book) {
                if ($borrowerIndex >= count($verifiedBorrowers)) $borrowerIndex = 0;
                $borrower = $verifiedBorrowers[$borrowerIndex];
                $borrowerIndex++;

                $sampleData[] = (object) [
                    'id' => null,
                    'is_dummy' => true,
                    'barcode_id' => $borrower->user_id,
                    'borrower_name' => $borrower->name,
                    'title' => $book->title,
                    'borrow_date' => Carbon::now()->subDays($index + 1)->toDateString(),
                    'due_date' => Carbon::now()->addDays(14 - $index)->toDateString(),
                    'status' => $index % 2 === 0 ? 'Borrowed' : 'Returned',
                ];
            }

            $borrowings = collect($sampleData);

            // If still no data (no books or verified users), use a simple placeholder
            if ($borrowings->isEmpty()) {
                $borrowings = collect([
                    (object) [
                        'id' => null,
                        'is_dummy' => true,
                        'barcode_id' => 'N/A',
                        'borrower_name' => 'No borrowing records yet',
                        'title' => 'Verify accounts first and add books to start recording borrowings',
                        'borrow_date' => Carbon::now()->toDateString(),
                        'due_date' => Carbon::now()->addDays(14)->toDateString(),
                        'status' => 'Pending',
                    ]
                ]);
            }
        }

        $bookTransactionFeed = collect();

        if (Schema::hasTable('borrowing_transactions')) {
            $transactionRows = DB::table('borrowing_transactions')
                ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
                ->leftJoin('users', 'borrowing_transactions.user_id', '=', 'users.user_id')
                ->leftJoin('members', 'borrowing_transactions.member_id', '=', 'members.id')
                ->select(
                    'borrowing_transactions.id',
                    'borrowing_transactions.created_at',
                    'borrowing_transactions.status',
                    'books.title as book_title',
                    DB::raw("COALESCE(users.name, members.full_name, borrowing_transactions.barcode_id, 'Unknown user') as user_name")
                )
                ->orderByDesc('borrowing_transactions.created_at')
                ->limit(50)
                ->get();

            foreach ($transactionRows as $row) {
                $status = (string) ($row->status ?? '');
                $event = match ($status) {
                    'Returned' => 'Returned',
                    'Rejected' => 'Rejected',
                    'Approved' => 'Approved',
                    'Borrowed' => 'Borrowed',
                    'Archived' => 'Archived',
                    default => 'Pending Request',
                };
                $details = match ($status) {
                    'Returned' => 'Book was returned to the library',
                    'Rejected' => 'Borrow request was rejected',
                    'Approved' => 'Borrow request was approved',
                    'Borrowed' => 'Book was checked out',
                    'Archived' => 'Book request archived',
                    default => 'New borrow request waiting for approval',
                };

                $bookTransactionFeed->push([
                    'id' => $row->id,
                    'created_at' => $row->created_at ?? now(),
                    'event' => $event,
                    'status' => $status ?: 'Pending',
                    'user_name' => $row->user_name ?: 'Unknown user',
                    'book_title' => $row->book_title ?: 'Unknown book',
                    'details' => $details,
                ]);
            }
        }

        if (Schema::hasTable('books')) {
            $recentBooks = DB::table('books')
                ->whereNotNull('created_at')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            foreach ($recentBooks as $book) {
                $bookTransactionFeed->push([
                    'created_at' => $book->created_at ?? now(),
                    'event' => 'New Book Added',
                    'status' => 'New',
                    'user_name' => auth()->user()?->name ?? 'Staff',
                    'book_title' => $book->title ?? 'Untitled book',
                    'details' => 'New book added to the catalog',
                ]);
            }
        }

        if (Schema::hasTable('financial_transactions')) {
            DB::table('financial_transactions')
                ->where('status', 'Waived')
                ->update([
                    'status' => 'Unpaid',
                    'updated_at' => now(),
                ]);

            $financialRows = DB::table('financial_transactions')
                ->leftJoin('users', 'financial_transactions.user_id', '=', 'users.user_id')
                ->leftJoin('members', function ($join) {
                    $join->on('financial_transactions.user_id', '=', 'members.barcode_id')
                         ->orOn('financial_transactions.user_id', '=', 'members.assigned_user_id');
                })
                ->select(
                    'financial_transactions.id',
                    'financial_transactions.created_at',
                    'financial_transactions.transaction_date',
                    'financial_transactions.transaction_type',
                    'financial_transactions.status',
                    'financial_transactions.description',
                    'financial_transactions.amount',
                    DB::raw("COALESCE(users.name, members.full_name, financial_transactions.user_id, 'Unknown user') as user_name")
                )
                ->orderByDesc('financial_transactions.created_at')
                ->limit(40)
                ->get();

            foreach ($financialRows as $row) {
                $event = 'Transaction';
                $details = $row->description ?: 'Financial transaction recorded';
                if (strtolower($row->transaction_type) === 'fine') {
                    $event = $row->status === 'Paid' ? 'Fine Paid' : 'Fine Issued';
                } elseif (in_array($row->transaction_type, ['Fund', 'Fund Income'], true)) {
                    $event = $row->status === 'Paid' || $row->status === 'Available' ? 'Fund Received' : 'Fund Transaction';
                } elseif ($row->transaction_type === 'Fund Expense') {
                    $event = 'Fund Expense';
                } else {
                    $event = $row->transaction_type ?: 'Financial Transaction';
                }

                $bookTransactionFeed->push([
                    'created_at' => $row->created_at ?? ($row->transaction_date ? \Carbon\Carbon::parse($row->transaction_date) : now()),
                    'event' => $event,
                    'status' => $row->status ?: 'Recorded',
                    'user_name' => trim((string) $row->user_name) ?: 'Unknown user',
                    'book_title' => $row->transaction_type === 'Fine' ? 'Fine / Payment' : ($row->transaction_type ?: 'Finance'),
                    'details' => $details . ($row->amount ? ' — PHP ' . number_format($row->amount, 2) : ''),
                ]);
            }
        }

        $bookTransactionFeed = $bookTransactionFeed
            ->sortByDesc(function ($item) {
                return $item['created_at'];
            })
            ->take(20)
            ->values();

        // Borrow requests from students/visitors
        $allBorrowRequests = Schema::hasTable('borrowing_transactions')
            ? DB::table('borrowing_transactions')
                ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
                ->leftJoin('users', 'borrowing_transactions.user_id', '=', 'users.user_id')
                ->select(
                    'borrowing_transactions.id',
                    'borrowing_transactions.user_id',
                    'borrowing_transactions.book_id',
                    'borrowing_transactions.created_at',
                    'borrowing_transactions.status',
                    'books.title',
                    'books.accession_number',
                    'books.author',
                    'users.name as user_name',
                    'users.email as user_email',
                    'users.role as user_role'
                )
                ->when(Schema::hasColumn('borrowing_transactions', 'status'), function ($query) {
                    return $query;
                })
                ->when(Schema::hasColumn('borrowing_transactions', 'rejection_reason'), function ($query) {
                    return $query->addSelect('borrowing_transactions.rejection_reason');
                })
                ->when(Schema::hasColumn('borrowing_transactions', 'archived_at'), function ($query) {
                    return $query->whereNull('borrowing_transactions.archived_at');
                })
                ->orderBy('borrowing_transactions.created_at', 'asc')
                ->get()
            : collect();

        $pendingBorrowRequests = $allBorrowRequests->where('status', 'Pending');
        $approvedBorrowRequests = $allBorrowRequests->whereIn('status', ['Approved', 'Borrowed']);
        $rejectedBorrowRequests = $allBorrowRequests->where('status', 'Rejected');

        $archivedBorrowRequests = Schema::hasTable('borrowing_transactions')
            ? DB::table('borrowing_transactions')
                ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
                ->leftJoin('users', 'borrowing_transactions.user_id', '=', 'users.user_id')
                ->select(
                    'borrowing_transactions.id',
                    'borrowing_transactions.user_id',
                    'borrowing_transactions.created_at',
                    'borrowing_transactions.status',
                    'books.title',
                    'users.name as user_name',
                    'users.email as user_email',
                    'users.role as user_role'
                )
                ->when(Schema::hasColumn('borrowing_transactions', 'archived_at'), function ($query) {
                    return $query->whereNotNull('borrowing_transactions.archived_at');
                }, function ($query) {
                    return $query->where('borrowing_transactions.status', 'Archived');
                })
                ->orderBy('borrowing_transactions.created_at', 'asc')
                ->get()
            : collect();

        if ($pendingBorrowRequests->isEmpty()) {
            $sampleUser = $verifiedUsers->first();
            $sampleBook = $books->first();

            $pendingBorrowRequests = collect([
                (object) [
                    'id' => null,
                    'is_dummy' => true,
                    'user_id' => $sampleUser->user_id ?? 'STU-0000',
                    'user_name' => $sampleUser->name ?? 'Sample User',
                    'user_email' => $sampleUser->email ?? 'sample@pcl.test',
                    'user_role' => $sampleUser->role ?? 'student',
                    'title' => $sampleBook->title ?? 'Sample Borrow Request',
                    'accession_number' => $sampleBook->accession_number ?? 'ACC-0000',
                    'author' => $sampleBook->author ?? 'Sample Author',
                    'created_at' => Carbon::now()->subHours(3),
                ],
            ]);
        }

        if ($approvedBorrowRequests->isEmpty()) {
            $approvedBorrowRequests = collect();
        }

        // Pending barcode replacement requests from students/visitors
        $pendingBarcodeRequests = Schema::hasTable('barcode_requests')
            ? DB::table('barcode_requests')
                ->leftJoin('users', 'barcode_requests.user_id', '=', 'users.id')
                ->select(
                    'barcode_requests.id',
                    'barcode_requests.user_id',
                    'barcode_requests.old_barcode_id',
                    'barcode_requests.new_barcode_id',
                    'barcode_requests.reason',
                    'barcode_requests.reason_details',
                    'barcode_requests.proof_path',
                    'barcode_requests.staff_notes',
                    'barcode_requests.approved_by',
                    'barcode_requests.status',
                    'barcode_requests.created_at',
                    'users.name as user_name',
                    'users.email as user_email',
                    'users.role as user_role',
                    'users.user_id as user_uid'
                )
                ->where('barcode_requests.status', 'pending')
                ->when(Schema::hasColumn('barcode_requests', 'archived_at'), function ($query) {
                    return $query->whereNull('barcode_requests.archived_at');
                })
                ->latest('barcode_requests.created_at')
                ->get()
            : collect();

        $rejectedBarcodeRequests = Schema::hasTable('barcode_requests')
            ? DB::table('barcode_requests')
                ->leftJoin('users', 'barcode_requests.user_id', '=', 'users.id')
                ->select(
                    'barcode_requests.id',
                    'barcode_requests.user_id',
                    'barcode_requests.old_barcode_id',
                    'barcode_requests.new_barcode_id',
                    'barcode_requests.reason',
                    'barcode_requests.reason_details',
                    'barcode_requests.proof_path',
                    'barcode_requests.staff_notes',
                    'barcode_requests.approved_by',
                    'barcode_requests.status',
                    'barcode_requests.created_at',
                    'users.name as user_name',
                    'users.email as user_email',
                    'users.role as user_role',
                    'users.user_id as user_uid'
                )
                ->where('barcode_requests.status', 'rejected')
                ->latest('barcode_requests.created_at')
                ->get()
            : collect();

        $approvedBarcodeRequests = Schema::hasTable('barcode_requests')
            ? DB::table('barcode_requests')
                ->leftJoin('users', 'barcode_requests.user_id', '=', 'users.id')
                ->select(
                    'barcode_requests.id',
                    'barcode_requests.user_id',
                    'barcode_requests.old_barcode_id',
                    'barcode_requests.new_barcode_id',
                    'barcode_requests.reason',
                    'barcode_requests.reason_details',
                    'barcode_requests.proof_path',
                    'barcode_requests.staff_notes',
                    'barcode_requests.approved_by',
                    'barcode_requests.status',
                    'barcode_requests.created_at',
                    'users.name as user_name',
                    'users.email as user_email',
                    'users.role as user_role',
                    'users.user_id as user_uid'
                )
                ->whereRaw("LOWER(barcode_requests.status) IN ('approved','completed')")
                ->latest('barcode_requests.created_at')
                ->get()
            : collect();

        $barcodeRequests = Schema::hasTable('barcode_requests')
            ? DB::table('barcode_requests')
                ->leftJoin('users', 'barcode_requests.user_id', '=', 'users.id')
                ->select(
                    'barcode_requests.id',
                    'barcode_requests.user_id',
                    'barcode_requests.old_barcode_id',
                    'barcode_requests.new_barcode_id',
                    'barcode_requests.reason',
                    'barcode_requests.reason_details',
                    'barcode_requests.proof_path',
                    'barcode_requests.staff_notes',
                    'barcode_requests.approved_by',
                    'barcode_requests.status',
                    'barcode_requests.created_at',
                    'users.name as user_name',
                    'users.email as user_email',
                    'users.role as user_role',
                    'users.user_id as user_uid'
                )
                ->latest('barcode_requests.created_at')
                ->get()
            : collect();

        // Financial data for fines management
        $financialRows = Schema::hasTable('financial_transactions')
            ? collect(DB::table('financial_transactions')->where('transaction_type', 'Fine')->latest('transaction_date')->get())
            : collect();

        // Resolve a patron display name for each financial row to avoid view-level DB lookups
        $financialRows = $financialRows->map(function ($row) {
            $resolved = null;
            $existingPatronName = trim((string) ($row->patron_name ?? ''));
            $lookupKey = trim((string) ($row->user_id ?? ''));

            if (! empty($existingPatronName) && $existingPatronName !== $lookupKey) {
                $row->patron_name = $existingPatronName;
                return $row;
            }

            if (! empty($lookupKey)) {
                $user = User::where('user_id', $lookupKey)
                    ->orWhere('email', $lookupKey)
                    ->orWhere('barcode_id', $lookupKey)
                    ->first();

                if ($user) {
                    $resolved = $user->name;
                } else {
                    $member = DB::table('members')
                        ->where('barcode_id', $lookupKey)
                        ->orWhere('assigned_user_id', $lookupKey)
                        ->first();
                    if ($member) {
                        $resolved = $member->full_name;
                    }
                }
            }

            $row->patron_name = $resolved;
            // Sanitize description: remove any '(... day/s)' fragment (including fractional values)
            if (! empty($row->description)) {
                $row->description = preg_replace('/\([^)]*day\/s\)/i', '', $row->description);
                $row->description = trim(preg_replace('/\s+/', ' ', $row->description));
            }

            // Ensure displayed amount for fines is at least PHP 20 (do not change DB here)
            $row->amount = isset($row->amount) ? max(20, round((float) $row->amount, 2)) : 0;

            return $row;
        });

        $collectedFines = $financialRows->where('status', 'Paid')->sum('amount');
        $unpaidFines = $financialRows->where('status', 'Unpaid')->sum('amount');

        $user = auth()->user();
        $notifications = $user->notifications()->latest()->limit(6)->get();
        $unreadNotificationCount = $user->unreadNotifications()->count();

        // Pass all dynamic database collections straight to your staff blade view
        return view('staff-dashboard', compact(
            'pendingResearchers',
            'pendingStudents',
            'pendingVisitors',
            'pendingUsers',
            'rejectedUsers',
            'realtimeAttendance',
            'todayAttendanceCount',
            'activeStaffCount',
            'staffAttendanceSummary',
            'repeatVisitors',
            'wifiConsumers',
            'availableBarcodes',
            'announcements', 'verifiedUsers',
            'books',
            'borrowings',
            'bookTransactionFeed',
            'pendingBorrowRequests',
            'approvedBorrowRequests',
            'rejectedBorrowRequests',
            'pendingBarcodeRequests',
            'rejectedBarcodeRequests',
            'approvedBarcodeRequests',
            'barcodeRequests',
            'financialRows',
            'collectedFines',
            'unpaidFines',
            'allUsers',
            'notifications',
            'unreadNotificationCount',
            'archivedBorrowRequests',
            'totalBooks',
            'activeBorrowers',
            'returnedToday',
            'overdueBooks',
            'femaleCount',
            'maleCount',
            'femalePct',
            'malePct',
            'monthlyBorrowing',
            'recentBorrowings',
            'recentActivity',
            'totalVisitorsThisMonth'
        ));
    }

    public function generateAndAssignBarcode(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'string'],
        ]);

        $user = User::where('user_id', $validated['user_id'])->firstOrFail();

        // If user already has a barcode, return with message
        if (! empty($user->barcode_id)) {
            return redirect()->route('staff.dashboard')
                ->with('active_panel', 'users')
                ->with('success', 'User already has barcode: ' . $user->barcode_id);
        }

        // Generate a unique 9-digit numeric barcode id
        $barcodeId = Member::generateUniqueBarcodeId();

        // Create member record and assign
        $member = Member::create([
            'barcode_id' => $barcodeId,
            'full_name' => $user->name,
            'school' => $user->school ?? null,
            'profile_picture' => $user->profile_picture ?? null,
            'status' => 'active',
            'assigned_user_id' => $user->user_id,
        ]);

        $user->barcode_id = $member->barcode_id;
        $user->save();

        return redirect()->route('staff.dashboard')
            ->with('active_panel', 'users')
            ->with('success', 'Generated and assigned barcode '.$member->barcode_id.' to '.$user->name);
    }

    public function assignBarcode(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'string'],
            'barcode_id' => ['required', 'string'],
        ]);

        $user = User::where('user_id', $validated['user_id'])->firstOrFail();
        $member = Member::where('barcode_id', $validated['barcode_id'])
            ->where('status', 'available')
            ->firstOrFail();

        if ($user->barcode_id && $user->barcode_id !== $member->barcode_id) {
            return redirect()->route('staff.dashboard')
                ->with('active_panel', 'verify')
                ->withErrors(['barcode_error' => 'This user already has an assigned barcode.']);
        }

        $member->update([
            'assigned_user_id' => $user->user_id,
            'full_name' => $user->name,
            'school' => $user->school ?? $member->school,
            'profile_picture' => $user->profile_picture ?? $member->profile_picture,
            'status' => 'active',
        ]);

        $user->barcode_id = $member->barcode_id;
        $user->save();

        return redirect()->route('staff.dashboard')
            ->with('active_panel', 'verify')
            ->with('success', 'Barcode assigned successfully.');
    }

    public function storeAvailableBarcode(Request $request)
    {
        $validated = $request->validate([
            'barcode_id' => ['required', 'string', 'regex:/^\d{9}$/', 'unique:members,barcode_id'],
        ]);

        Member::create([
            'barcode_id' => $validated['barcode_id'],
            'full_name' => 'Unassigned',
            'school' => null,
            'profile_picture' => null,
            'wifi_voucher' => null,
            'status' => 'available',
        ]);

        return redirect()->route('staff.dashboard')
            ->with('active_panel', 'barcodes')
            ->with('success', 'Barcode '.$validated['barcode_id'].' created successfully.');
    }

    public function updateAvailableBarcode(Request $request, string $id)
    {
        $validated = $request->validate([
            'barcode_id' => ['required', 'string', 'regex:/^\d{9}$/', 'unique:members,barcode_id,' . $id],
        ]);

        $member = Member::whereKey($id)->firstOrFail();

        if ($member->status !== 'available') {
            return redirect()->route('staff.dashboard')
                ->with('active_panel', 'barcodes')
                ->withErrors(['barcode_error' => 'Only available barcodes can be updated.']);
        }

        $member->update([
            'barcode_id' => $validated['barcode_id'],
        ]);

        return redirect()->route('staff.dashboard')
            ->with('active_panel', 'barcodes')
            ->with('success', 'Available barcode updated.');
    }

    public function deleteAvailableBarcode(string $id)
    {
        $member = Member::whereKey($id)->firstOrFail();

        if ($member->status !== 'available') {
            return redirect()->route('staff.dashboard')
                ->with('active_panel', 'barcodes')
                ->withErrors(['barcode_error' => 'Only available barcodes can be deleted.']);
        }

        $member->delete();

        return redirect()->route('staff.dashboard')
            ->with('active_panel', 'barcodes')
            ->with('success', 'Available barcode deleted.');
    }

    // `createVisitorBarcode` and `recordAttendance` removed — functionality replaced by
    // auto-generated barcodes and attendance summary. Routes cleaned up accordingly.

    public function verifyResearcher(Request $request)
    {
        return $this->verifyUser($request);
    }

    private function assignBarcodeToUser(User $user): void
    {
        $member = null;

        if (! empty($user->barcode_id)) {
            $member = Member::where('assigned_user_id', $user->user_id)
                ->orWhere('barcode_id', $user->barcode_id)
                ->first();
        } else {
            $member = Member::where('assigned_user_id', $user->user_id)->first();
        }

        if (! $member) {
            if (empty($user->barcode_id)) {
                $user->barcode_id = Member::generateUniqueBarcodeId();
            }

            $member = new Member();
            $member->barcode_id = $user->barcode_id;
            $member->assigned_user_id = $user->user_id;
        }

        if (empty($user->barcode_id)) {
            $user->barcode_id = Member::generateUniqueBarcodeId();
            $member->barcode_id = $user->barcode_id;
        }

        $member->assigned_user_id = $user->user_id;
        $member->barcode_id = $user->barcode_id;
        $member->full_name = $user->name;
        $member->school = $user->school ?? $member->school;
        $member->profile_picture = $user->profile_picture ?? $member->profile_picture;
        $member->wifi_voucher = $member->wifi_voucher ?? null;
        $member->status = 'active';
        $member->save();
    }

    public function verifyUser(Request $request)
    {
        $validated = $request->validate([
            'lookup' => ['nullable', 'string'],
            'user_id' => ['nullable', 'string'],
        ]);

        $lookup = trim($validated['lookup'] ?? $validated['user_id'] ?? '');
        if ($lookup === '') {
            return redirect()->route('staff.dashboard')
                ->with('active_panel', 'verify')
                ->withErrors(['verify_error' => 'Unable to verify account. Please provide a valid user lookup.']);
        }

        $userQuery = User::where('user_id', $lookup)
            ->orWhere('email', $lookup);

        if (is_numeric($lookup)) {
            $userQuery->orWhere('id', intval($lookup));
        }

        $user = $userQuery->first();
        if (! $user) {
            return redirect()->route('staff.dashboard')
                ->with('active_panel', 'verify')
                ->withErrors(['verify_error' => 'Unable to verify account. The selected user was not found.']);
        }

        $user->status = 'approved';
        $user->rejection_reason = null;
        $this->assignBarcodeToUser($user);
        $user->save();

        // Send notification
        $user->notify(new AccountVerified());

        $payload = [
            'success' => true,
            'message' => 'Account verified successfully! Barcode generated and assigned.'
        ];

        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return redirect()->route('staff.dashboard')
            ->with('active_panel', 'verified')
            ->with('success', $payload['message']);
    }

    public function rejectUser(Request $request)
    {
        $validated = $request->validate([
            'lookup' => ['nullable', 'string'],
            'user_id' => ['nullable', 'string'],
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $lookup = trim($validated['lookup'] ?? $validated['user_id'] ?? '');
        if ($lookup === '') {
            return redirect()->route('staff.dashboard')
                ->with('active_panel', 'verify')
                ->withErrors(['verify_error' => 'Unable to reject account. Please provide a valid user lookup.']);
        }

        $userQuery = User::where('user_id', $lookup)
            ->orWhere('email', $lookup);

        if (is_numeric($lookup)) {
            $userQuery->orWhere('id', intval($lookup));
        }

        $user = $userQuery->first();
        if (! $user) {
            return redirect()->route('staff.dashboard')
                ->with('active_panel', 'verify')
                ->withErrors(['verify_error' => 'Unable to reject account. The selected user was not found.']);
        }

        $user->status = 'rejected';
        $user->rejection_reason = trim($validated['rejection_reason']);
        $user->save();

        $payload = [
            'success' => true,
            'message' => 'Account rejected successfully.'
        ];

        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return redirect()->route('staff.dashboard')
            ->with('active_panel', 'verify')
            ->with('success', $payload['message']);
    }

    public function approveBarcodeRequest(Request $request, string $id)
    {
        $barcodeRequest = BarcodeRequest::findOrFail($id);
        $user = User::findOrFail($barcodeRequest->user_id);

        $newBarcodeId = Member::generateUniqueBarcodeId();

        $barcodeRequest->new_barcode_id = $newBarcodeId;
        $barcodeRequest->status = 'approved';
        $barcodeRequest->approved_by = auth()->user()->id;
        $barcodeRequest->staff_notes = 'Approved by ' . auth()->user()->name;
        $barcodeRequest->save();

        $user->barcode_id = $newBarcodeId;
        $user->save();

        // Notify the requester (DB notification)
        try {
            $user->notify(new \App\Notifications\BarcodeRequestStatusUpdated('approved', $barcodeRequest->staff_notes, $newBarcodeId, $barcodeRequest->id));
        } catch (\Exception $e) {
            logger()->error('Failed to notify user about approval: ' . $e->getMessage());
        }

        if ($barcodeRequest->old_barcode_id) {
            Member::where('barcode_id', $barcodeRequest->old_barcode_id)->delete();
        }

        return redirect()->route('staff.dashboard')
            ->with('active_panel', 'barcode-requests')
            ->with('success', 'Barcode request approved and new barcode assigned to ' . $user->name);
    }

    public function rejectBarcodeRequest(Request $request, string $id)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $barcodeRequest = BarcodeRequest::findOrFail($id);

        $barcodeRequest->status = 'rejected';
        $barcodeRequest->approved_by = auth()->user()->id;
        $barcodeRequest->staff_notes = $validated['rejection_reason'];
        $barcodeRequest->save();

        // Notify the requester (DB notification)
        try {
            $requestUser = User::find($barcodeRequest->user_id);
            if ($requestUser) {
                $requestUser->notify(new \App\Notifications\BarcodeRequestStatusUpdated('rejected', $validated['rejection_reason'], null, $barcodeRequest->id));
            }
        } catch (\Exception $e) {
            logger()->error('Failed to notify user about rejection: ' . $e->getMessage());
        }

        return redirect()->route('staff.dashboard')
            ->with('active_panel', 'barcode-requests')
            ->with('success', 'Barcode request rejected.');
    }

    public function archiveBarcodeRequest(Request $request, string $id)
    {
        $barcodeRequest = BarcodeRequest::findOrFail($id);

        if (Schema::hasColumn('barcode_requests', 'archived_at')) {
            $barcodeRequest->archived_at = now();
        } else {
            $barcodeRequest->status = 'archived';
        }

        $barcodeRequest->save();

        return redirect()->route('staff.dashboard')
            ->with('active_panel', 'barcode-requests')
            ->with('success', 'Barcode request archived.');
    }
}
