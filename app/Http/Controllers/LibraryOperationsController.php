<?php

namespace App\Http\Controllers;

use App\Models\BarcodeRequest;
use App\Models\EntryLog;
use App\Models\Member;
use App\Models\User;
use App\Notifications\BorrowRequestStatusUpdated;
use App\Notifications\FineIssued;
use App\Notifications\NewAnnouncement;
use App\Notifications\TransactionPaid;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class LibraryOperationsController extends Controller
{
    public function adminDashboard()
    {
       $startedAt = microtime(true);
        $logStep = function (string $label, callable $callback) {
            $stepStartedAt = microtime(true);
            Log::info("admin-dashboard: start {$label}");
            $result = $callback();
            Log::info('admin-dashboard: end ' . $label, [
                'ms' => (int) ((microtime(true) - $stepStartedAt) * 1000),
            ]);
            return $result;
        };

        try {
            $usersTable = $logStep('Schema::hasTable(users)', fn () => Schema::hasTable('users'));
            $membersTable = $logStep('Schema::hasTable(members)', fn () => Schema::hasTable('members'));
            $booksTable = $logStep('Schema::hasTable(books)', fn () => Schema::hasTable('books'));
            $borrowingTable = $logStep('Schema::hasTable(borrowing_transactions)', fn () => Schema::hasTable('borrowing_transactions'));
            $entryLogsTable = $logStep('Schema::hasTable(entry_logs)', fn () => Schema::hasTable('entry_logs'));
            $wifiTable = $logStep('Schema::hasTable(wifi_vouchers)', fn () => Schema::hasTable('wifi_vouchers'));
            $proposalsTable = $logStep('Schema::hasTable(proposals)', fn () => Schema::hasTable('proposals'));

            $pendingResearchers = $usersTable ? $logStep('pendingResearchers query', fn () => User::where('role', 'researcher')->where('status', 'pending')->get()) : collect();
            $pendingStudents = $usersTable ? $logStep('pendingStudents query', fn () => User::where('role', 'student')->where('status', 'pending')->get()) : collect();
            $verifiedUsers = $usersTable ? $logStep('verifiedUsers query', fn () => User::where('status', 'approved')->whereIn('role', ['student', 'researcher'])->latest('updated_at')->get()) : collect();
            $availableBarcodes = $membersTable ? $logStep('availableBarcodes query', fn () => Member::where('status', 'available')->orderBy('barcode_id')->get()) : collect();

            $books = $booksTable ? $logStep('books query', fn () => DB::table('books')->whereNull('archived_at')->orderBy('title')->get()) : collect();
            $totalBooks = $books->count();
            $activeBorrowers = $borrowingTable ? $logStep('activeBorrowers count', fn () => DB::table('borrowing_transactions')->where('status', 'Borrowed')->distinct()->count('barcode_id')) : 0;
            $returnedToday = $borrowingTable ? $logStep('returnedToday count', fn () => DB::table('borrowing_transactions')->whereDate('returned_at', today())->count()) : 0;
            $overdueBooks = $borrowingTable ? $logStep('overdueBooks count', fn () => DB::table('borrowing_transactions')->where('status', 'Borrowed')->where('due_date', '<', today())->count()) : 0;

            $femaleCount = $usersTable ? $logStep('femaleCount count', fn () => User::where('gender', 'Female')->count()) : 0;
            $maleCount = $usersTable ? $logStep('maleCount count', fn () => User::where('gender', 'Male')->count()) : 0;
            $totalGender = $femaleCount + $maleCount;
            $femalePct = $totalGender > 0 ? round(($femaleCount / $totalGender) * 100) : 0;
            $malePct = $totalGender > 0 ? round(($maleCount / $totalGender) * 100) : 0;

            $monthlyBorrowing = [];
            if ($borrowingTable) {
                for ($m = 1; $m <= 12; $m++) {
                    $month = $m;
                    $monthlyBorrowing[$month] = $logStep("monthlyBorrowing month {$month}", fn () => DB::table('borrowing_transactions')->whereYear('borrow_date', now()->year)->whereMonth('borrow_date', $month)->count());
                }
            }

            $recentBorrowings = $borrowingTable ? $logStep('recentBorrowings query', function () {
                return DB::table('borrowing_transactions')
                    ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
                    ->select(
                        'borrowing_transactions.*',
                        'books.title',
                        'books.author',
                        'books.isbn',
                        'books.call_number',
                        'books.accession_number',
                        'books.cover_image'
                    )
                    ->latest('borrowing_transactions.created_at')
                    ->limit(5)
                    ->get();
            }) : collect();

            $recentActivity = collect();
            if ($borrowingTable) {
                $recentBorrows = $logStep('recentBorrows query', function () {
                    return DB::table('borrowing_transactions')
                        ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
                        ->select('borrowing_transactions.barcode_id', 'books.title', 'borrowing_transactions.created_at', DB::raw("'borrow' as action_type"))
                        ->latest('borrowing_transactions.created_at')
                        ->limit(3)
                        ->get();
                });
                foreach ($recentBorrows as $b) {
                    $recentActivity->push((object) [
                        'text' => "<b>{$b->barcode_id}</b> borrowed <b>{$b->title}</b>",
                        'time' => Carbon::parse($b->created_at)->diffForHumans(),
                        'time_parsed' => Carbon::parse($b->created_at),
                        'type' => 'borrow',
                    ]);
                }
            }
            if ($entryLogsTable) {
                $recentEntries = $logStep('recentEntries query', function () {
                    return DB::table('entry_logs')
                        ->leftJoin('members', 'entry_logs.member_id', '=', 'members.id')
                        ->select('members.full_name', 'entry_logs.entry_time', DB::raw("'entry' as action_type"))
                        ->latest('entry_logs.entry_time')
                        ->limit(3)
                        ->get();
                });
                foreach ($recentEntries as $e) {
                    $recentActivity->push((object) [
                        'text' => "<b>{$e->full_name}</b> entered the library",
                        'time' => Carbon::parse($e->entry_time)->diffForHumans(),
                        'time_parsed' => Carbon::parse($e->entry_time),
                        'type' => 'entry',
                    ]);
                }
            }
            $recentActivity = $recentActivity->sortByDesc('time_parsed')->take(5);

            $schoolDistribution = $usersTable ? $logStep('schoolDistribution query', fn () => DB::table('users')->select('school', DB::raw('COUNT(*) as count'))->whereNotNull('school')->groupBy('school')->orderByDesc('count')->get()) : collect();
            $totalSchoolUsers = $schoolDistribution->sum('count');

            $ageBrackets = ['18-22' => 0, '23-27' => 0, '28+' => 0];
            if ($usersTable && Schema::hasColumn('users', 'age')) {
                $users = $logStep('age users query', fn () => User::whereNotNull('age')->get());
                foreach ($users as $u) {
                    $age = (int) $u->age;
                    if ($age >= 18 && $age <= 22) $ageBrackets['18-22']++;
                    elseif ($age >= 23 && $age <= 27) $ageBrackets['23-27']++;
                    elseif ($age >= 28) $ageBrackets['28+']++;
                }
            }
            $totalAgeUsers = array_sum($ageBrackets);

            $topVisitors = $entryLogsTable ? $logStep('topVisitors query', fn () => DB::table('entry_logs')->join('members', 'entry_logs.member_id', '=', 'members.id')->select('members.full_name', 'members.barcode_id', DB::raw('COUNT(*) as visit_count'))->groupBy('members.barcode_id', 'members.full_name')->orderByDesc('visit_count')->limit(10)->get()) : collect();
            $topWifiUsers = $wifiTable ? $logStep('topWifiUsers query', fn () => DB::table('wifi_vouchers')->where('status', 'Used')->whereNotNull('name')->select('name', 'user_id', 'bandwidth_gb', 'voucher_code')->orderByDesc('bandwidth_gb')->limit(10)->get()) : collect();
            $gateEntries = $entryLogsTable ? $logStep('gateEntries query', fn () => DB::table('entry_logs')->leftJoin('members', 'entry_logs.member_id', '=', 'members.id')->select('entry_logs.*', 'members.full_name', 'members.barcode_id')->latest('entry_logs.entry_time')->limit(20)->get()) : collect();

            $proposalStats = $proposalsTable ? $logStep('proposalStats counts', fn () => [
                'total' => DB::table('proposals')->whereNull('archived_at')->count(),
                'pending' => DB::table('proposals')->whereNull('archived_at')->where('status', 'Pending')->count(),
                'approved' => DB::table('proposals')->whereNull('archived_at')->where('status', 'Approved')->count(),
                'rejected' => DB::table('proposals')->whereNull('archived_at')->where('status', 'Rejected')->count(),
            ]) : ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];

            $monthStart = now()->startOfMonth()->startOfDay();
            $monthEnd = now()->endOfMonth()->endOfDay();
            $currentMonthName = now()->format('F');
            $daysInMonthSoFar = min(now()->day, now()->daysInMonth);

            $totalVisitorsToday = $entryLogsTable ? $logStep('totalVisitorsToday count', fn () => DB::table('entry_logs')->whereDate('entry_time', today())->count()) : 0;
            $totalVisitorsThisMonth = $entryLogsTable ? $logStep('totalVisitorsThisMonth count', fn () => DB::table('entry_logs')->whereBetween('entry_time', [$monthStart, $monthEnd])->count()) : 0;
            $avgDailyVisitors = $daysInMonthSoFar > 0 ? round($totalVisitorsThisMonth / $daysInMonthSoFar) : 0;

            $topVisitorsJune = $entryLogsTable ? $logStep('topVisitorsJune query', fn () => DB::table('entry_logs')->join('members', 'entry_logs.member_id', '=', 'members.id')->whereBetween('entry_logs.entry_time', [$monthStart, $monthEnd])->select('members.full_name', DB::raw('COUNT(*) as visit_count'))->groupBy('members.id', 'members.full_name')->orderByDesc('visit_count')->limit(5)->get()) : collect();

            $mostActiveStudent = null;
            if ($entryLogsTable && $membersTable && $usersTable) {
                $mostActiveStudent = $logStep('mostActiveStudent query', fn () => DB::table('entry_logs')->join('members', 'entry_logs.member_id', '=', 'members.id')->join('users', 'members.barcode_id', '=', 'users.user_id')->where('users.role', 'student')->whereBetween('entry_logs.entry_time', [$monthStart, $monthEnd])->select('members.full_name', DB::raw('COUNT(*) as visit_count'))->groupBy('members.id', 'members.full_name')->orderByDesc('visit_count')->first());
            }

            $mostActiveResearcher = null;
            if ($entryLogsTable && $membersTable && $usersTable) {
                $mostActiveResearcher = $logStep('entryLogsTable && membersTable && usersTable', fn () => DB::table('entry_logs')->join('members', 'entry_logs.member_id', '=', 'members.id')->join('users', 'members.barcode_id', '=', 'users.user_id')->where('users.role', 'researcher')->whereBetween('entry_logs.entry_time', [$monthStart, $monthEnd])->select('members.full_name', DB::raw('COUNT(*) as visit_count'))->groupBy('members.id', 'members.full_name')->orderByDesc('visit_count')->first());
            }

            // Overdue books list for dashboard panel
            $overdueBooksList = collect();
            if ($borrowingTable && $booksTable) {
                $overdueBooksList = $logStep('overdueBooksList query', fn () => DB::table('borrowing_transactions')
                    ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
                    ->leftJoin('members', 'borrowing_transactions.member_id', '=', 'members.id')
                    ->leftJoin('users', 'borrowing_transactions.user_id', '=', 'users.user_id')
                    ->select(
                        'borrowing_transactions.id',
                        'borrowing_transactions.barcode_id',
                        'borrowing_transactions.due_date',
                        'borrowing_transactions.borrow_date',
                        'books.title as book_title',
                        DB::raw("COALESCE(members.full_name, users.name, borrowing_transactions.barcode_id, 'Unknown') as borrower_name")
                    )
                    ->where('borrowing_transactions.status', 'Borrowed')
                    ->where('borrowing_transactions.due_date', '<', now())
                    ->orderBy('borrowing_transactions.due_date', 'asc')
                    ->limit(10)
                    ->get());
            }

            // Collection overview stats
            $availableBooks = 0;
            $issuedBooks = 0;
            $reservedBooks = 0;
            $lostDamagedBooks = 0;
            if ($booksTable && $books->isNotEmpty()) {
                foreach ($books as $book) {
                    $status = trim((string) ($book->status ?? ''));
                    if (strcasecmp($status, 'Available') === 0) {
                        $availableBooks++;
                    } elseif (strcasecmp($status, 'Borrowed') === 0 || strcasecmp($status, 'Issued') === 0) {
                        $issuedBooks++;
                    } elseif (strcasecmp($status, 'Reserved') === 0) {
                        $reservedBooks++;
                    } elseif (in_array(strtolower($status), ['lost', 'damaged'], true)) {
                        $lostDamagedBooks++;
                    }
                }
            } elseif ($booksTable && $books->isNotEmpty() && isset($books->first()->available)) {
                $availableBooks = $books->sum(fn ($book) => max(0, intval($book->available ?? 0)));
                $issuedBooks = max(0, $books->sum('copies') - $availableBooks);
            } else {
                $availableBooks = $books->count();
            }

            // Borrow request segments for admin panel tabs
            $borrowSegments = $logStep('getBorrowRequestSegments', fn () => $this->getBorrowRequestSegments());
            $pendingBorrowRequests = $borrowSegments['pendingBorrowRequests'];
            $approvedBorrowRequests = $borrowSegments['approvedBorrowRequests'];
            $rejectedBorrowRequests = $borrowSegments['rejectedBorrowRequests'];
            $archivedBorrowRequests = $borrowSegments['archivedBorrowRequests'];

           Log::info('admin-dashboard: completed', [
                'ms' => (int) ((microtime(true) - $startedAt) * 1000),
            ]);

            return view('admin-dashboard', compact(
                'pendingResearchers', 'pendingStudents', 'verifiedUsers', 'availableBarcodes',
                'books', 'totalBooks', 'activeBorrowers', 'returnedToday', 'overdueBooks',
                'femaleCount', 'maleCount', 'femalePct', 'malePct',
                'monthlyBorrowing', 'recentBorrowings', 'recentActivity',
                'schoolDistribution', 'totalSchoolUsers',
                'ageBrackets', 'totalAgeUsers',
                'topVisitors', 'topWifiUsers', 'gateEntries',
                'proposalStats',
                'topVisitorsJune', 'totalVisitorsToday', 'totalVisitorsThisMonth',
                'avgDailyVisitors', 'mostActiveStudent', 'mostActiveResearcher',
                'currentMonthName', 'overdueBooksList',
                'availableBooks', 'issuedBooks', 'reservedBooks', 'lostDamagedBooks',
                'pendingBorrowRequests', 'approvedBorrowRequests', 'rejectedBorrowRequests', 'archivedBorrowRequests'
            ));
        } catch (\Throwable $e) {
            Log::error('admin-dashboard: exception', [
                'message' => $e->getMessage(),
                'class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function getMostActiveMemberByRole(string $role, Carbon $start, Carbon $end)
    {
        if (! Schema::hasTable('entry_logs') || ! Schema::hasTable('members') || ! Schema::hasTable('users')) {
            return null;
        }

        return DB::table('entry_logs')
            ->join('members', 'entry_logs.member_id', '=', 'members.id')
            ->join('users', 'members.barcode_id', '=', 'users.user_id')
            ->where('users.role', $role)
            ->whereBetween('entry_logs.entry_time', [$start, $end])
            ->select('members.full_name', DB::raw('COUNT(*) as visit_count'))
            ->groupBy('members.id', 'members.full_name')
            ->orderByDesc('visit_count')
            ->first();
    }

    private function getBarcodeRequestSegments()
    {
        $pendingBarcodeRequests = collect();
        $rejectedBarcodeRequests = collect();
        $approvedBarcodeRequests = collect();
        $barcodeRequests = collect();

        if (Schema::hasTable('barcode_requests')) {
            $baseQuery = DB::table('barcode_requests')
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
                );

            $pendingBarcodeRequests = (clone $baseQuery)
                ->where('barcode_requests.status', 'pending')
                ->when(Schema::hasColumn('barcode_requests', 'archived_at'), function ($query) {
                    return $query->whereNull('barcode_requests.archived_at');
                })
                ->latest('barcode_requests.created_at')
                ->limit(20)
                ->get();

            $rejectedBarcodeRequests = (clone $baseQuery)
                ->where('barcode_requests.status', 'rejected')
                ->latest('barcode_requests.created_at')
                ->limit(20)
                ->get();

            $approvedBarcodeRequests = (clone $baseQuery)
                ->whereRaw("LOWER(barcode_requests.status) IN ('approved','completed')")
                ->latest('barcode_requests.created_at')
                ->limit(20)
                ->get();

            $barcodeRequests = $baseQuery
                ->latest('barcode_requests.created_at')
                ->limit(100)
                ->get();
        }

        return compact('pendingBarcodeRequests', 'rejectedBarcodeRequests', 'approvedBarcodeRequests', 'barcodeRequests');
    }

    private function getBorrowRequestSegments()
    {
        $pendingBorrowRequests = collect();
        $approvedBorrowRequests = collect();
        $rejectedBorrowRequests = collect();
        $archivedBorrowRequests = collect();

        if (Schema::hasTable('borrowing_transactions')) {
            $pendingBorrowRequests = DB::table('borrowing_transactions')
                ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
                ->leftJoin('users', 'borrowing_transactions.user_id', '=', 'users.user_id')
                ->select(
                    'borrowing_transactions.id',
                    'borrowing_transactions.user_id',
                    'borrowing_transactions.book_id',
                    'borrowing_transactions.created_at',
                    'borrowing_transactions.status',
                    'books.title',
                    'books.author',
                    'books.accession_number',
                    'users.name as user_name',
                    'users.email as user_email',
                    'users.role as user_role'
                )
                ->where('borrowing_transactions.status', 'Pending')
                ->when(Schema::hasColumn('borrowing_transactions', 'archived_at'), function ($query) {
                    return $query->whereNull('borrowing_transactions.archived_at');
                })
                ->orderBy('borrowing_transactions.created_at', 'asc')
                ->get();

            $approvedBorrowRequests = DB::table('borrowing_transactions')
                ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
                ->leftJoin('users', 'borrowing_transactions.user_id', '=', 'users.user_id')
                ->select(
                    'borrowing_transactions.id',
                    'borrowing_transactions.user_id',
                    'borrowing_transactions.book_id',
                    'borrowing_transactions.created_at',
                    'borrowing_transactions.status',
                    'books.title',
                    'books.author',
                    'books.accession_number',
                    'users.name as user_name',
                    'users.email as user_email',
                    'users.role as user_role'
                )
                ->when(Schema::hasColumn('borrowing_transactions', 'status'), function ($query) {
                    return $query->whereIn('borrowing_transactions.status', ['Approved', 'Borrowed']);
                })
                ->when(Schema::hasColumn('borrowing_transactions', 'archived_at'), function ($query) {
                    return $query->whereNull('borrowing_transactions.archived_at');
                })
                ->orderBy('borrowing_transactions.created_at', 'asc')
                ->get();

            $rejectedBorrowRequests = DB::table('borrowing_transactions')
                ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
                ->leftJoin('users', 'borrowing_transactions.user_id', '=', 'users.user_id')
                ->select(
                    'borrowing_transactions.id',
                    'borrowing_transactions.user_id',
                    'borrowing_transactions.book_id',
                    'borrowing_transactions.created_at',
                    'borrowing_transactions.status',
                    'books.title',
                    'books.author',
                    'books.accession_number',
                    'users.name as user_name',
                    'users.email as user_email',
                    'users.role as user_role'
                )
                ->when(Schema::hasColumn('borrowing_transactions', 'rejection_reason'), function ($query) {
                    return $query->addSelect('borrowing_transactions.rejection_reason');
                })
                ->when(Schema::hasColumn('borrowing_transactions', 'status'), function ($query) {
                    return $query->where('borrowing_transactions.status', 'Rejected');
                })
                ->when(Schema::hasColumn('borrowing_transactions', 'archived_at'), function ($query) {
                    return $query->whereNull('borrowing_transactions.archived_at');
                })
                ->orderBy('borrowing_transactions.created_at', 'asc')
                ->get();

            $archivedBorrowRequests = DB::table('borrowing_transactions')
                ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
                ->leftJoin('users', 'borrowing_transactions.user_id', '=', 'users.user_id')
                ->select(
                    'borrowing_transactions.id',
                    'borrowing_transactions.user_id',
                    'borrowing_transactions.book_id',
                    'borrowing_transactions.created_at',
                    'borrowing_transactions.status',
                    'books.title',
                    'books.author',
                    'books.accession_number',
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
                ->get();
        }

        return compact('pendingBorrowRequests', 'approvedBorrowRequests', 'rejectedBorrowRequests', 'archivedBorrowRequests');
    }

    private function getBookTransactionFeed()
    {
        $bookTransactionFeed = collect();

        if (! Schema::hasTable('borrowing_transactions')) {
            return $bookTransactionFeed;
        }

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

        return $bookTransactionFeed->sortByDesc(fn ($item) => $item['created_at'])->take(20)->values();
    }

    private function getRejectedBorrowRequests()
    {
        if (! Schema::hasTable('borrowing_transactions')) {
            return collect();
        }

        return DB::table('borrowing_transactions')
            ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
            ->leftJoin('users', 'borrowing_transactions.user_id', '=', 'users.user_id')
            ->select(
                'borrowing_transactions.id',
                'borrowing_transactions.user_id',
                'borrowing_transactions.book_id',
                'borrowing_transactions.created_at',
                'borrowing_transactions.status',
                'books.title',
                'books.author',
                'books.accession_number',
                'users.name as user_name',
                'users.email as user_email',
                'users.role as user_role'
            )
            ->when(Schema::hasColumn('borrowing_transactions', 'rejection_reason'), function ($query) {
                return $query->addSelect('borrowing_transactions.rejection_reason');
            })
            ->when(Schema::hasColumn('borrowing_transactions', 'status'), function ($query) {
                return $query->where('borrowing_transactions.status', 'Rejected');
            })
            ->when(Schema::hasColumn('borrowing_transactions', 'archived_at'), function ($query) {
                return $query->whereNull('borrowing_transactions.archived_at');
            })
            ->orderBy('borrowing_transactions.created_at', 'asc')
            ->get();
    }

    private function getNotificationsForUser($user)
    {
        $notifications = collect();
        $unreadNotificationCount = 0;

        if (! $user) {
            return compact('notifications', 'unreadNotificationCount');
        }

        try {
            $notifications = $user->notifications()->latest()->limit(6)->get();
            $unreadNotificationCount = $user->unreadNotifications()->count();
        } catch (\Throwable $e) {
            $notifications = collect();
            $unreadNotificationCount = 0;
        }

        return compact('notifications', 'unreadNotificationCount');
    }

    private function getSchoolOptions()
    {
        return Cache::remember('library.school.options', 60 * 24, function () {
            if (! Schema::hasTable('school_options')) {
                return [
                    'Davao del Norte State College',
                    'Colegio de Panabo',
                    'University of Mindanao Tagum College',
                    'A Mabini College',
                    'Davao Oriental State University - Panabo Extension',
                    'Sto. Nino College',
                    'New Corella College',
                    'Panabo City National High School',
                    'Panabo City National High School - Senior High',
                    'Panabo City Montessori School',
                    'Other Panabo School',
                ];
            }

            $values = DB::table('school_options')->pluck('name')->filter()->map(fn ($value) => trim((string) $value))->unique()->values()->all();
            return $values ?: [
                'Davao del Norte State College',
                'Colegio de Panabo',
                'University of Mindanao Tagum College',
                'A Mabini College',
                'Davao Oriental State University - Panabo Extension',
                'Sto. Nino College',
                'New Corella College',
                'Panabo City National High School',
                'Panabo City National High School - Senior High',
                'Panabo City Montessori School',
                'Other Panabo School',
            ];
        });
    }

    public function studentDashboard()
    {
        $user = auth()->user();
        $books = Schema::hasTable('books')
            ? DB::table('books')->whereNull('archived_at')->orderBy('title')->get()
            : collect();
        $borrowings = $this->borrowingsForUser($user);
        $fines = $this->finesForUser($user);
        $announcements = $this->announcementsFor($user->role);
        $notifications = $user->notifications()->latest()->limit(6)->get();
        $unreadNotificationCount = $user->unreadNotifications()->count();
        $member = $this->memberForUser($user);
        $wifiVoucher = $member?->wifi_voucher;

        // Prepare upcoming due reminders (server-side) and send email reminders if not already sent recently
        $dueSoonDays = 3; // reminder window in days
        $now = Carbon::now();
        $dueSoonBorrowings = collect();

        foreach ($borrowings as $b) {
            if (empty($b->due_date)) continue;
            try {
                $due = Carbon::parse($b->due_date);
            } catch (\Throwable $e) {
                continue;
            }

            $status = strtolower($b->status ?? '');
            if (in_array($status, ['returned', 'cancelled', 'lost'])) continue;

            if ($due->greaterThanOrEqualTo($now) && $due->lessThanOrEqualTo($now->copy()->addDays($dueSoonDays))) {
                $dueSoonBorrowings->push($b);

                // Check if a similar upcoming reminder was already sent in the last $dueSoonDays days
                try {
                    $recentSent = DB::table('notifications')
                        ->where('notifiable_type', get_class($user))
                        ->where('notifiable_id', $user->id)
                        ->where('type', 'App\\Notifications\\UpcomingDueReminder')
                        ->where('created_at', '>=', now()->subDays($dueSoonDays))
                        ->where(function ($query) use ($b) {
                            $query->where('data->message', 'like', '%' . ($b->title ?? $b->book_barcode ?? '') . '%')
                                  ->orWhere('data->message', 'like', '%' . ($b->accession_number ?? '') . '%');
                        })
                        ->exists();
                } catch (\Throwable $e) {
                    // Fallback: if JSON querying fails, check by type + recent timestamp only
                    $recentSent = DB::table('notifications')
                        ->where('notifiable_type', get_class($user))
                        ->where('notifiable_id', $user->id)
                        ->where('type', 'App\\Notifications\\UpcomingDueReminder')
                        ->where('created_at', '>=', now()->subDays($dueSoonDays))
                        ->exists();
                }

                if (! $recentSent) {
                    try {
                        $daysUntil = max(0, (int) Carbon::now()->diffInDays($due, false));
                        $user->notify(new \App\Notifications\UpcomingDueReminder(
                            bookTitle: $b->title ?? ($b->book_barcode ?? 'Borrowed Book'),
                            dueDate: $due->format('M d, Y'),
                            daysUntilDue: $daysUntil
                        ));
                    } catch (\Throwable $e) {
                        // Don't let notification failures break the dashboard; log and continue
                        \Illuminate\Support\Facades\Log::error('Failed to send upcoming due reminder: ' . $e->getMessage());
                    }
                }
            }
        }

        // Fetch barcode requests for the user
        $barcodeRequests = Schema::hasTable('barcode_requests')
            ? DB::table('barcode_requests')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        $requestHistory = Schema::hasTable('borrowing_transactions')
            ? DB::table('borrowing_transactions')
                ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
                ->where(function ($query) use ($user) {
                    $query->where('borrowing_transactions.user_id', $user->user_id)
                        ->orWhere('borrowing_transactions.barcode_id', $user->user_id);

                    if ($user->barcode_id) {
                        $query->orWhere('borrowing_transactions.user_id', $user->barcode_id)
                            ->orWhere('borrowing_transactions.barcode_id', $user->barcode_id);
                    }
                })
                ->select(
                    'borrowing_transactions.id',
                    'borrowing_transactions.status',
                    'borrowing_transactions.created_at',
                    'borrowing_transactions.due_date',
                    'books.title',
                    'books.accession_number',
                    'books.author'
                )
                ->when(Schema::hasColumn('borrowing_transactions', 'rejection_reason'), function ($query) {
                    return $query->addSelect('borrowing_transactions.rejection_reason');
                })
                ->orderByDesc('borrowing_transactions.created_at')
                ->get()
            : collect();

        $view = $user->role === 'visitor' ? 'visitor-dashboard' : 'student-dashboard';

        return view($view, compact(
            'books',
            'borrowings',
            'fines',
            'announcements',
            'notifications',
            'unreadNotificationCount',
            'wifiVoucher',
            'barcodeRequests',
            'requestHistory'
        ));
    }

    public function opacSearch(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $books = DB::table('books')
            ->whereNull('archived_at')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('title', 'like', "%{$q}%")
                        ->orWhere('author', 'like', "%{$q}%")
                        ->orWhere('isbn', 'like', "%{$q}%")
                        ->orWhere('call_number', 'like', "%{$q}%")
                        ->orWhere('accession_number', 'like', "%{$q}%")
                        ->orWhere('publisher', 'like', "%{$q}%")
                        ->orWhere('category', 'like', "%{$q}%")
                        ->orWhere('section_location', 'like', "%{$q}%")
                        ->orWhere('place_of_publication', 'like', "%{$q}%");
                });
            })
            ->orderBy('title')
            ->limit(50)
            ->get();

        return response()->json($books);
    }

    private function generateUniqueBookBarcode(): string
    {
        $prefix = 'LIB';
        $baseNumber = DB::table('books')->count() + 1000;
        $barcode = $prefix . '-' . str_pad($baseNumber, 5, '0', STR_PAD_LEFT);

        while (DB::table('books')->where('barcode', $barcode)->exists()) {
            $baseNumber++;
            $barcode = $prefix . '-' . str_pad($baseNumber, 5, '0', STR_PAD_LEFT);
        }

        return $barcode;
    }

    public function storeBook(Request $request)
    {
        $validated = $request->validate([
            'barcode' => ['required', 'string', 'max:100', 'unique:books,barcode'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:100', 'unique:books,isbn'],
            'accession_number' => ['required', 'string', 'max:100', 'unique:books,accession_number'],
            'category' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'call_number' => ['nullable', 'string', 'max:100'],
            'section_location' => ['nullable', 'string', 'max:255'],
            'copies' => ['nullable', 'integer', 'min:1'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('covers', 'public');
        }

        $barcode = $this->generateUniqueBookBarcode();

        DB::table('books')->insert([
            'barcode' => $barcode,
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => $validated['isbn'],
            'accession_number' => $validated['accession_number'],
            'category' => $validated['category'],
            'summary' => $validated['summary'] ?? null,
            'call_number' => $validated['call_number'] ?? null,
            'section_location' => $validated['section_location'] ?? null,
            'cover_image' => $coverImagePath,
            'copies' => $validated['copies'] ?? 1,
            'available' => $validated['copies'] ?? 1,
            'status' => 'Available',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()
            ->with('active_panel', 'books')
            ->with('success', 'Book added successfully.');
    }

    public function updateBook(Request $request, int $id)
    {
        $book = DB::table('books')->where('id', $id)->first();
        abort_if(! $book, 404);

        $validated = $request->validate([
            'barcode' => ['required', 'string', 'max:100', Rule::unique('books', 'barcode')->ignore($id)],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:100'],
            'accession_number' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'call_number' => ['nullable', 'string', 'max:100'],
            'section_location' => ['nullable', 'string', 'max:255'],
            'copies' => ['nullable', 'integer', 'min:1'],
            // available is computed by syncBookAvailability() — ignore any user-supplied value
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $payload = [
            'title' => $validated['title'],
            'author' => $validated['author'],
            'isbn' => $validated['isbn'] ?? $book->isbn,
            'accession_number' => $validated['accession_number'],
            'category' => $validated['category'],
            'summary' => $validated['summary'] ?? $book->summary,
            'call_number' => $validated['call_number'] ?? $book->call_number,
            'section_location' => $validated['section_location'] ?? $book->section_location,
            'copies' => $validated['copies'] ?? $book->copies,
            'updated_at' => now(),
        ];

        if (empty($book->barcode)) {
            $payload['barcode'] = $this->generateUniqueBookBarcode();
        }

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $payload['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        DB::table('books')->where('id', $id)->update($payload);

        // Reconcile available count against active loans
        $this->syncBookAvailability($id);

        return back()
            ->with('active_panel', 'books')
            ->with('success', 'Book updated successfully.');
    }

    public function deleteBook(int $id)
    {
        DB::table('books')->where('id', $id)->update([
            'archived_at' => now(),
            'updated_at' => now(),
        ]);

        return back()
            ->with('active_panel', 'books')
            ->with('success', 'Book archived successfully.');
    }

    public function storeVoucher(Request $request)
    {
        $validated = $request->validate([
            'voucher_code' => ['required', 'string', 'max:100', 'unique:wifi_vouchers,voucher_code'],
            'duration_minutes' => ['required', 'integer', 'min:15'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $payload = [
            'voucher_code' => strtoupper($validated['voucher_code']),
            'duration_minutes' => $validated['duration_minutes'],
            'expires_at' => $validated['expires_at'] ?? null,
            'status' => 'Available',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('wifi_vouchers', 'is_active')) {
            $payload['is_active'] = true;
        }

        DB::table('wifi_vouchers')->insert($payload);

        return back()->with('success', 'WiFi voucher created successfully.');
    }

    /*
    // Wi-Fi voucher management retained for reference but disabled in routes.
    public function storeVoucher(Request $request)
    {
        $validated = $request->validate([
            'voucher_code' => ['required', 'string', 'max:100', 'unique:wifi_vouchers,voucher_code'],
            'duration_minutes' => ['required', 'integer', 'min:15'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $payload = [
            'voucher_code' => strtoupper($validated['voucher_code']),
            'duration_minutes' => $validated['duration_minutes'],
            'expires_at' => $validated['expires_at'] ?? null,
            'status' => 'Available',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('wifi_vouchers', 'is_active')) {
            $payload['is_active'] = true;
        }

        DB::table('wifi_vouchers')->insert($payload);

        return back()->with('success', 'WiFi voucher created successfully.');
    }
    */

    public function updateVoucher(Request $request, int $id)
    {
        $voucher = DB::table('wifi_vouchers')->where('id', $id)->first();

        if (! $voucher) {
            return back()->withErrors(['wifi_error' => 'WiFi voucher not found.']);
        }

        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['Available', 'Used', 'Expired'])],
            'duration_minutes' => ['nullable', 'integer', 'min:15'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
            'assign_user_id' => ['nullable', 'string', 'max:100'],
            'assign_name' => ['nullable', 'string', 'max:255'],
            'assign_role' => ['nullable', Rule::in(['admin', 'staff', 'student', 'researcher', 'member'])],
            'clear_assignment' => ['nullable', 'boolean'],
        ]);

        $updates = ['updated_at' => now(), 'status' => $validated['status'] ?? $voucher->status];

        if (($updates['status'] ?? $voucher->status) === 'Used' && $voucher->status !== 'Used') {
            $updates['used_at'] = now();
        }
        if (array_key_exists('duration_minutes', $validated)) {
            $updates['duration_minutes'] = $validated['duration_minutes'];
        }
        if (array_key_exists('expires_at', $validated)) {
            $updates['expires_at'] = $validated['expires_at'] ?? null;
        }
        if (Schema::hasColumn('wifi_vouchers', 'is_active') && array_key_exists('is_active', $validated)) {
            $updates['is_active'] = (bool) $validated['is_active'];
        }
        if ($request->boolean('clear_assignment')) {
            $updates['name'] = null;
            $updates['user_id'] = null;
            $updates['role'] = null;
        } elseif ($request->filled('assign_user_id') || $request->filled('assign_name') || $request->filled('assign_role')) {
            $lookup = $request->filled('assign_user_id')
                ? User::where('user_id', $validated['assign_user_id'])->orWhere('email', $validated['assign_user_id'])->first()
                : null;
            $updates['user_id'] = $validated['assign_user_id'] ?? $voucher->user_id;
            $updates['name'] = $validated['assign_name'] ?? $lookup?->name ?? $voucher->name;
            $updates['role'] = $validated['assign_role'] ?? $lookup?->role ?? $voucher->role;
        }

        DB::table('wifi_vouchers')->where('id', $id)->update($updates);
        return back()->with('success', 'WiFi voucher updated successfully.');
    }

    /*
    // Disabled: updateVoucher kept for reference
    public function updateVoucher(Request $request, int $id)
    {
        $voucher = DB::table('wifi_vouchers')->where('id', $id)->first();

        if (! $voucher) {
            return back()->withErrors(['wifi_error' => 'WiFi voucher not found.']);
        }

        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['Available', 'Used', 'Expired'])],
            'duration_minutes' => ['nullable', 'integer', 'min:15'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
            'assign_user_id' => ['nullable', 'string', 'max:100'],
            'assign_name' => ['nullable', 'string', 'max:255'],
            'assign_role' => ['nullable', Rule::in(['admin', 'staff', 'student', 'researcher', 'member'])],
            'clear_assignment' => ['nullable', 'boolean'],
        ]);

        $updates = ['updated_at' => now(), 'status' => $validated['status'] ?? $voucher->status];

        if (($updates['status'] ?? $voucher->status) === 'Used' && $voucher->status !== 'Used') {
            $updates['used_at'] = now();
        }
        if (array_key_exists('duration_minutes', $validated)) {
            $updates['duration_minutes'] = $validated['duration_minutes'];
        }
        if (array_key_exists('expires_at', $validated)) {
            $updates['expires_at'] = $validated['expires_at'] ?? null;
        }
        if (Schema::hasColumn('wifi_vouchers', 'is_active') && array_key_exists('is_active', $validated)) {
            $updates['is_active'] = (bool) $validated['is_active'];
        }
        if ($request->boolean('clear_assignment')) {
            $updates['name'] = null;
            $updates['user_id'] = null;
            $updates['role'] = null;
        } elseif ($request->filled('assign_user_id') || $request->filled('assign_name') || $request->filled('assign_role')) {
            $lookup = $request->filled('assign_user_id')
                ? User::where('user_id', $validated['assign_user_id'])->orWhere('email', $validated['assign_user_id'])->first()
                : null;
            $updates['user_id'] = $validated['assign_user_id'] ?? $voucher->user_id;
            $updates['name'] = $validated['assign_name'] ?? $lookup?->name ?? $voucher->name;
            $updates['role'] = $validated['assign_role'] ?? $lookup?->role ?? $voucher->role;
        }

        DB::table('wifi_vouchers')->where('id', $id)->update($updates);

        return back()->with('success', 'WiFi voucher updated successfully.');
    }
    */

    public function storeFinancialTransaction(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => ['nullable', 'integer'],
            'transaction_type' => ['required', 'in:Fund,Fund Income,Fund Expense,Fine'],
            'patron_name' => ['nullable', 'string', 'max:255'],
            'user_id' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'received_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:Unpaid,Paid,Expenses'],
            'transaction_date' => ['required', 'date'],
            'send_notification' => ['sometimes', 'in:1'],
        ]);

        $existingTransaction = ! empty($validated['transaction_id'])
            ? DB::table('financial_transactions')->where('id', $validated['transaction_id'])->first()
            : null;

        $receivedAmount = array_key_exists('received_amount', $validated) && $validated['received_amount'] !== null
            ? (float) $validated['received_amount']
            : ($existingTransaction && $validated['transaction_type'] === 'Fine' ? (float) ($existingTransaction->received_amount ?? 0) : null);

        $changeAmount = $existingTransaction && $validated['transaction_type'] === 'Fine' && $validated['status'] === 'Paid' && $receivedAmount === null
            ? (float) ($existingTransaction->change_amount ?? 0)
            : null;
        $changeStatus = 'none';

        if ($validated['transaction_type'] === 'Fine' && $validated['status'] === 'Paid') {
            $fineAmount = (float) $validated['amount'];
            $changeAmount = $receivedAmount !== null ? max(0, $receivedAmount - $fineAmount) : ($changeAmount ?? 0);
            $changeStatus = $changeAmount > 0 ? 'returned' : 'none';
        }

        $payload = [
            'transaction_type' => $validated['transaction_type'],
            'patron_name' => $validated['patron_name'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'received_amount' => $receivedAmount,
            'change_amount' => $changeAmount,
            'change_status' => $changeStatus,
            'status' => $validated['status'],
            'transaction_date' => $validated['transaction_date'],
            'updated_at' => now(),
        ];

        $successMessage = $validated['transaction_type'].' transaction saved.';

        if (! empty($validated['transaction_id'])) {
            DB::table('financial_transactions')
                ->where('id', $validated['transaction_id'])
                ->update($payload);
            $successMessage = $validated['transaction_type'].' transaction updated.';
        } else {
            DB::table('financial_transactions')->insert([
                ...$payload,
                'created_at' => now(),
            ]);
        }

        // If patron_name was not provided, try to resolve it from users or members
        if (empty($payload['patron_name']) && ! empty($payload['user_id'])) {
            $lookup = User::where('user_id', $payload['user_id'])
                ->orWhere('barcode_id', $payload['user_id'])
                ->orWhere('email', $payload['user_id'])
                ->first();

            if ($lookup) {
                $payload['patron_name'] = $lookup->name;
            } else {
                $member = DB::table('members')->where('barcode_id', $payload['user_id'])->first();
                if ($member) {
                    $payload['patron_name'] = $member->full_name;
                }
            }
        }

        if (! empty($validated['user_id'])) {
            $recipient = User::where('user_id', $validated['user_id'])
                ->orWhere('barcode_id', $validated['user_id'])
                ->orWhere('email', $validated['user_id'])
                ->orWhere('name', 'like', "%{$validated['user_id']}%")
                ->first();

            if ($recipient) {
                if ($validated['transaction_type'] === 'Fine' && ! empty($validated['send_notification'])) {
                    $recipient->notify(new FineIssued(
                        description: $validated['description'],
                        amount: $validated['amount'],
                    ));
                }

                if ($validated['status'] === 'Paid') {
                    $recipient->notify(new TransactionPaid(
                        transactionType: $validated['transaction_type'],
                        description: $validated['description'],
                        amount: $validated['amount'],
                    ));
                }
            }
        }

        return back()
            ->with('active_panel', 'fines')
            ->with('success', $successMessage);
    }

    public function archiveFinancialTransaction(Request $request, int $id)
    {
        // Permanently delete the transaction when archived from admin UI
        $exists = DB::table('financial_transactions')->where('id', $id)->exists();
        if (! $exists) {
            return back()->withErrors(['financial_error' => 'Transaction not found.']);
        }

        DB::table('financial_transactions')->where('id', $id)->delete();

        return back()->with('active_panel', 'fines')->with('success', 'Transaction deleted.');
    }

    public function processBarcodeBorrow(string $borrowerBarcode, string $bookReference): array
    {
        $borrowerBarcode = trim($borrowerBarcode);
        $bookReference = trim($bookReference);

        if ($borrowerBarcode === '' || $bookReference === '') {
            return [
                'success' => false,
                'message' => 'Borrower barcode and book reference are required.',
            ];
        }

        \Log::info('Borrow attempt', ['barcode' => $borrowerBarcode, 'book' => $bookReference]);

        $member = Member::where('barcode_id', $borrowerBarcode)->first();
        $user = null;

        if (! $member) {
            $user = User::where(function ($query) use ($borrowerBarcode) {
                    $query->where('user_id', $borrowerBarcode)
                        ->orWhere('barcode_id', $borrowerBarcode)
                        ->orWhere('email', $borrowerBarcode)
                        ->orWhere('name', 'like', "%{$borrowerBarcode}%");
                })
                ->whereRaw('LOWER(status) = ?', ['approved'])
                ->first();
        }

        if (! $member && ! $user) {
            \Log::warning('Borrower not found', ['barcode' => $borrowerBarcode]);
            return [
                'success' => false,
                'message' => 'Borrower not found. Use a verified User ID, barcode, or email from the approved accounts.',
            ];
        }

        if (! $member && $user) {
            $memberBarcode = $user->barcode_id ?? $user->user_id ?? $user->email;
            $member = Member::firstOrCreate(
                ['barcode_id' => $memberBarcode],
                [
                    'full_name' => $user->name,
                    'school' => $user->school ?? null,
                    'profile_picture' => $user->profile_picture ?? null,
                    'wifi_voucher' => null,
                    'status' => strtolower($user->status ?? '') === 'pending' ? 'inactive' : 'active',
                ]
            );
        }

        if ($member->status !== 'active') {
            return [
                'success' => false,
                'message' => 'This member account is not active.',
            ];
        }

        $normalizedIsbn = preg_replace('/[^0-9Xx]/', '', $bookReference);
        $bookQuery = DB::table('books')->whereNull('archived_at');

        if ($normalizedIsbn !== '' && preg_match('/^\d{10}(\d{3})?$/', $normalizedIsbn)) {
            $bookQuery->where(function ($query) use ($normalizedIsbn, $bookReference) {
                $query->where('isbn', $normalizedIsbn)
                      ->orWhere('accession_number', $bookReference)
                      ->orWhere('barcode', $bookReference)
                      ->orWhere('call_number', $bookReference)
                      ->orWhere('title', 'like', "%{$bookReference}%");
            });
        } else {
            $bookQuery->where(function ($query) use ($bookReference) {
                $query->where('accession_number', $bookReference)
                    ->orWhere('barcode', $bookReference)
                    ->orWhere('call_number', $bookReference)
                    ->orWhere('title', 'like', "%{$bookReference}%");
            });
        }

        $book = $bookQuery->orderByRaw("CASE WHEN accession_number = ? THEN 0 ELSE 1 END", [$bookReference])->first();

        if (! $book) {
            \Log::warning('Book not found', ['reference' => $bookReference]);
            return [
                'success' => false,
                'message' => 'Book ISBN or accession number was not found.',
            ];
        }

        if (($book->available ?? 0) <= 0) {
            \Log::warning('Book not available', ['book_id' => $book->id, 'available' => $book->available]);
            return [
                'success' => false,
                'message' => 'This book is not available (no copies left).',
            ];
        }

        if (($book->status ?? 'Available') !== 'Available') {
            \Log::warning('Book status not available', ['book_id' => $book->id, 'status' => $book->status]);
            return [
                'success' => false,
                'message' => 'This book is not available for borrowing.',
            ];
        }

        try {
            $insertData = [
                'member_id' => $member->id ?? null,
                'user_id' => $user->user_id ?? null,
                'book_id' => $book->id,
                'barcode_id' => $borrowerBarcode,
                'book_barcode' => $book->barcode,
                'borrow_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'status' => 'Borrowed',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            \Log::info('Inserting borrow transaction', $insertData);
            DB::table('borrowing_transactions')->insert($insertData);
            \Log::info('Borrow transaction inserted successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to insert borrow transaction', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
            ];
        }

        // Reconcile available count against active loans
        $this->syncBookAvailability($book->id);

        \Log::info('Borrow recorded successfully', [
            'member_id' => $member->id ?? null,
            'user_id' => $user->user_id ?? null,
            'book_id' => $book->id,
        ]);

        return [
            'success' => true,
            'message' => 'Borrowing transaction recorded by barcode scan.',
            'data' => [
                'book_barcode' => $book->barcode,
                'borrower_barcode' => $borrowerBarcode,
            ],
        ];
    }

    public function borrowByBarcode(Request $request)
    {
        $validated = $request->validate([
            'barcode_id' => ['required', 'string', 'max:100'],
            'book_reference' => ['required', 'string', 'max:100'],
        ]);

        $borrowerBarcode = trim($validated['barcode_id']);
        $bookReference = trim($validated['book_reference']);

        \Log::info('Borrow attempt', ['barcode' => $borrowerBarcode, 'book' => $bookReference]);

        // Try to find by barcode_id in members
        $member = Member::where('barcode_id', $borrowerBarcode)->first();

        // If not found in members, try to find user by user_id, barcode_id, email, or name.
        $user = null;
        if (! $member) {
            $user = User::where(function ($query) use ($borrowerBarcode) {
                    $query->where('user_id', $borrowerBarcode)
                        ->orWhere('barcode_id', $borrowerBarcode)
                        ->orWhere('email', $borrowerBarcode)
                        ->orWhere('name', 'like', "%{$borrowerBarcode}%");
                })
                ->whereRaw('LOWER(status) = ?', ['approved'])
                ->first();
        }

        if (! $member && ! $user) {
            \Log::warning('Borrower not found', ['barcode' => $borrowerBarcode]);
            return back()->withErrors(['borrow_error' => 'Borrower not found. Use a verified User ID, barcode, or email from the approved accounts.']);
        }

        $normalizedIsbn = preg_replace('/[^0-9Xx]/', '', $bookReference);

        $bookQuery = DB::table('books')->whereNull('archived_at');
        if ($normalizedIsbn !== '' && preg_match('/^\d{10}(\d{3})?$/', $normalizedIsbn)) {
            $bookQuery->where(function ($query) use ($normalizedIsbn, $bookReference) {
                $query->where('isbn', $normalizedIsbn)
                      ->orWhere('accession_number', $bookReference)
                      ->orWhere('barcode', $bookReference)
                      ->orWhere('call_number', $bookReference)
                      ->orWhere('title', 'like', "%{$bookReference}%");
            });
        } else {
            $bookQuery->where(function ($query) use ($bookReference) {
                $query->where('accession_number', $bookReference)
                    ->orWhere('barcode', $bookReference)
                    ->orWhere('call_number', $bookReference)
                    ->orWhere('title', 'like', "%{$bookReference}%");
            });
        }

        $book = $bookQuery->orderByRaw("CASE WHEN accession_number = ? THEN 0 ELSE 1 END", [$bookReference])->first();

        if (! $book) {
            \Log::warning('Book not found', ['reference' => $bookReference]);
            return back()->withErrors(['borrow_error' => 'Book ISBN or accession number was not found.']);
        }

        if (($book->available ?? 0) <= 0) {
            \Log::warning('Book not available', ['book_id' => $book->id, 'available' => $book->available]);
            return back()->withErrors(['borrow_error' => 'This book is not available (no copies left).']);
        }

        if (($book->status ?? 'Available') !== 'Available') {
            \Log::warning('Book status not available', ['book_id' => $book->id, 'status' => $book->status]);
            return back()->withErrors(['borrow_error' => 'This book is not available for borrowing.']);
        }

        try {
            $insertData = [
                'member_id' => $member->id ?? null,
                'user_id' => $user->user_id ?? null,
                'book_id' => $book->id,
                'barcode_id' => $borrowerBarcode,
                'book_barcode' => $book->barcode,
                'borrow_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'status' => 'Borrowed',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            \Log::info('Inserting borrow transaction', $insertData);
            DB::table('borrowing_transactions')->insert($insertData);

            \Log::info('Borrow transaction inserted successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to insert borrow transaction', ['error' => $e->getMessage()]);
            return back()->withErrors(['borrow_error' => 'Database error: ' . $e->getMessage()]);
        }

        // Reconcile available count against active loans
        $this->syncBookAvailability($book->id);

        \Log::info('Borrow recorded successfully', [
            'member_id' => $member->id ?? null,
            'user_id' => $user->user_id ?? null,
            'book_id' => $book->id,
        ]);

        $borrowerName = $member->full_name ?? $user->name ?? 'Unknown Borrower';
        $borrowerSchool = $member->school ?? null;
        $bookCoverUrl = $book->cover_image ? '/storage/' . ltrim($book->cover_image, '/') : null;

        return back()->with([
            'active_panel' => 'borrowing',
            'success' => 'Borrowing transaction recorded by barcode scan.',
            'borrow_popup' => true,
            'borrower_full_name' => $borrowerName,
            'borrower_school' => $borrowerSchool,
            'borrower_id' => $borrowerBarcode,
            'book_title' => $book->title ?? 'Unknown Book',
            'book_cover' => $bookCoverUrl,
            'transaction_time' => now()->format('F j, Y \a\t h:i A'),
        ]);
    }

    public function returnBorrowing(int $id)
    {
        $borrow = DB::table('borrowing_transactions')->where('id', $id)->first();

        if (! $borrow || $borrow->status === 'Returned') {
            return back()->withErrors(['borrow_error' => 'Borrowing transaction is not active.']);
        }

        DB::transaction(function () use ($borrow) {
            $updatePayload = [
                'returned_at' => now(),
                'status' => 'Returned',
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('borrowing_transactions', 'returned_by')) {
                $updatePayload['returned_by'] = auth()->user()?->name;
            }

            DB::table('borrowing_transactions')->where('id', $borrow->id)->update($updatePayload);

            if ($borrow->book_id) {
                // Reconcile available count — increment by 1 returned copy, not reset to total
                $this->syncBookAvailability($borrow->book_id);
            }

            $overdueDays = max(0, Carbon::parse($borrow->due_date)->diffInDays(now(), false));
            if ($overdueDays > 0) {
                $bookTitle = 'Unknown Book';
                if ($borrow->book_id) {
                    $bookModel = DB::table('books')->where('id', $borrow->book_id)->first();
                    if ($bookModel && ! empty($bookModel->title)) {
                        $bookTitle = $bookModel->title;
                    }
                }
                $fineDescription = "Overdue book fine for {$bookTitle} ({$overdueDays} day/s)";
                $existingFine = Schema::hasTable('financial_transactions')
                    ? DB::table('financial_transactions')
                        ->where('transaction_type', 'Fine')
                        ->where('user_id', $borrow->user_id ?? $borrow->barcode_id)
                        ->where('description', $fineDescription)
                        ->exists()
                    : false;
                if (! $existingFine) {
                    $calculated = $overdueDays * 10;
                    $amountToCharge = max(20, $calculated);

                    $patronName = $borrow->barcode_id;
                    if (! empty($borrow->member_id)) {
                        $member = DB::table('members')->where('id', $borrow->member_id)->first();
                        if ($member) {
                            $patronName = $member->full_name ?? $member->name ?? $patronName;
                        }
                    } elseif (! empty($borrow->user_id)) {
                        $user = User::where('user_id', $borrow->user_id)
                            ->orWhere('email', $borrow->user_id)
                            ->first();
                        if ($user) {
                            $patronName = $user->name ?? $patronName;
                        }
                    }

                    DB::table('financial_transactions')->insert([
                        'transaction_type' => 'Fine',
                        'patron_name' => $patronName,
                        'user_id' => $borrow->user_id ?? $borrow->barcode_id,
                        'description' => $fineDescription,
                        'amount' => $amountToCharge,
                        'status' => 'Unpaid',
                        'transaction_date' => now()->toDateString(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    // Notify borrower about the fine
                    $borrower = User::where('user_id', $borrow->user_id ?? $borrow->barcode_id)
                        ->orWhere('email', $borrow->user_id ?? $borrow->barcode_id)->first();
                    if ($borrower) {
                        $borrower->notify(new FineIssued(
                            description: $fineDescription,
                            amount: $amountToCharge,
                        ));
                    }
                }
            }
        });

        $borrowerName = null;
        $borrowerSchool = null;
        if ($borrow->member_id) {
            $borrower = DB::table('members')->where('id', $borrow->member_id)->first();
            $borrowerName = $borrower->full_name ?? $borrower->name ?? null;
            $borrowerSchool = $borrower->school ?? null;
        } elseif ($borrow->user_id) {
            $borrower = DB::table('users')
                ->where('user_id', $borrow->user_id)
                ->orWhere('barcode_id', $borrow->user_id)
                ->orWhere('email', $borrow->user_id)
                ->first();
            $borrowerName = $borrower->name ?? null;
        }

        $book = $borrow->book_id ? DB::table('books')->where('id', $borrow->book_id)->first() : null;
        $bookCoverUrl = $book && $book->cover_image ? '/storage/' . ltrim($book->cover_image, '/') : null;

        return back()->with([
            'active_panel' => 'borrowing',
            'success' => 'Book return recorded successfully.',
            'return_popup' => true,
            'borrower_full_name' => $borrowerName ?? 'Unknown Borrower',
            'borrower_school' => $borrowerSchool,
            'borrower_id' => $borrow->barcode_id,
            'book_title' => $book->title ?? 'Unknown Book',
            'book_cover' => $bookCoverUrl,
            'transaction_time' => now()->format('F j, Y \a\t h:i A'),
        ]);
    }

    public function markBorrowingOverdue(int $id)
    {
        $borrow = DB::table('borrowing_transactions')->where('id', $id)->first();

        if (! $borrow || $borrow->status === 'Returned') {
            return back()->withErrors(['borrow_error' => 'Borrowing transaction is not active.']);
        }

        $dueDate = Carbon::parse($borrow->due_date);
        if (! $dueDate->isPast()) {
            return back()->withErrors(['borrow_error' => 'This borrowing is not overdue yet.']);
        }

        if (! Schema::hasTable('financial_transactions')) {
            return back()->withErrors(['borrow_error' => 'Financial transactions are not enabled on this system.']);
        }

        $overdueDays = max(0, $dueDate->diffInDays(now()));
        $bookTitle = 'Unknown Book';
        if ($borrow->book_id) {
            $bookModel = DB::table('books')->where('id', $borrow->book_id)->first();
            if ($bookModel && ! empty($bookModel->title)) {
                $bookTitle = $bookModel->title;
            }
        }
        $fineDescription = "Overdue book fine for {$bookTitle} ({$overdueDays} day/s)";
        $userKey = $borrow->user_id ?? $borrow->barcode_id;

        $existingFine = DB::table('financial_transactions')
            ->where('transaction_type', 'Fine')
            ->where('user_id', $userKey)
            ->where('description', $fineDescription)
            ->exists();

        if ($existingFine) {
            return back()->with('warning', 'An overdue fine has already been recorded for this borrow.');
        }

        $amountToCharge = max(20, $overdueDays * 10);
        $patronName = $borrow->barcode_id;
        if ($borrow->member_id) {
            $member = DB::table('members')->where('id', $borrow->member_id)->first();
            if ($member) {
                $patronName = $member->full_name ?? $member->name ?? $patronName;
            }
        } elseif ($borrow->user_id) {
            $user = User::where('user_id', $borrow->user_id)
                ->orWhere('email', $borrow->user_id)
                ->first();
            if ($user) {
                $patronName = $user->name ?? $patronName;
            }
        }

        // Insert the fine record
        DB::table('financial_transactions')->insert([
            'transaction_type' => 'Fine',
            'patron_name' => $patronName,
            'user_id' => $userKey,
            'description' => $fineDescription,
            'amount' => $amountToCharge,
            'status' => 'Unpaid',
            'transaction_date' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update borrowing transaction status so the UI and database reflect the overdue state
        DB::table('borrowing_transactions')->where('id', $id)->update([
            'status' => 'Overdue',
            'updated_at' => now(),
        ]);

        // Notify borrower about the fine
        $borrower = User::where('user_id', $userKey)
            ->orWhere('email', $userKey)
            ->first();
        if ($borrower) {
            $borrower->notify(new FineIssued(
                description: $fineDescription,
                amount: $amountToCharge,
            ));
        }

        return back()->with([
            'active_panel' => 'borrowing',
            'success' => 'Overdue fine recorded and borrowing marked as Overdue.',
        ]);
    }

    /**
     * Mark a borrowing as Due and record the corresponding overdue fine in the fines module.
     */
    public function markBorrowingDue(Request $request, int $id)
    {
        $borrow = DB::table('borrowing_transactions')->where('id', $id)->first();

        if (! $borrow) {
            return back()->withErrors(['borrow_error' => 'Borrowing transaction not found.']);
        }

        if ($borrow->status === 'Returned') {
            return back()->withErrors(['borrow_error' => 'Borrowing has already been returned.']);
        }

        if ($borrow->status === 'Due') {
            return back()->withErrors(['borrow_error' => 'Borrowing has already been marked as Due.']);
        }

        $validated = $request->validate([
            'waive_note' => ['required', 'string', 'max:500'],
        ]);

        $note = trim($validated['waive_note']);

        DB::table('borrowing_transactions')->where('id', $id)->update([
            'status' => 'Due',
            'updated_at' => now(),
        ]);

        $userKey = $borrow->user_id ?? $borrow->barcode_id;
        $dueDate = null;

        try {
            if (! empty($borrow->due_date)) {
                $dueDate = Carbon::parse($borrow->due_date);
            }
        } catch (\Throwable $e) {
            $dueDate = null;
        }

        if ($dueDate && ($dueDate->isPast() || $dueDate->isToday()) && Schema::hasTable('financial_transactions')) {
            $overdueDays = max(0, $dueDate->diffInDays(now()));
            $amountToCharge = max(20, $overdueDays * 10);
            $bookTitle = 'Unknown Book';

            if ($borrow->book_id) {
                $bookModel = DB::table('books')->where('id', $borrow->book_id)->first();
                if ($bookModel && ! empty($bookModel->title)) {
                    $bookTitle = $bookModel->title;
                }
            }

            $patronName = $borrow->barcode_id;
            if (! empty($borrow->member_id)) {
                $member = DB::table('members')->where('id', $borrow->member_id)->first();
                if ($member) {
                    $patronName = $member->full_name ?? $member->name ?? $patronName;
                }
            } elseif (! empty($borrow->user_id)) {
                $user = User::where('user_id', $borrow->user_id)
                    ->orWhere('email', $borrow->user_id)
                    ->first();
                if ($user) {
                    $patronName = $user->name ?? $patronName;
                }
            }

            $fineDescription = "Overdue book fine for {$bookTitle} ({$overdueDays} day/s) - Mark Due: {$note}";

            $existingFine = DB::table('financial_transactions')
                ->where('transaction_type', 'Fine')
                ->where('user_id', $userKey)
                ->where('description', 'like', "Overdue book fine for {$bookTitle} ({$overdueDays} day/s)%")
                ->first();

            if ($existingFine) {
                DB::table('financial_transactions')->where('id', $existingFine->id)->update([
                    'description' => $existingFine->description . ' - Mark Due: ' . $note,
                    'status' => 'Unpaid',
                    'received_amount' => 0,
                    'change_amount' => 0,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('financial_transactions')->insert([
                    'transaction_type' => 'Fine',
                    'patron_name' => $patronName,
                    'user_id' => $userKey,
                    'description' => $fineDescription,
                    'amount' => $amountToCharge,
                    'received_amount' => 0,
                    'change_amount' => 0,
                    'status' => 'Unpaid',
                    'transaction_date' => now()->toDateString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return back()->with([
                'active_panel' => 'fines',
                'success' => 'Borrowing marked as Due and a corresponding fine record was added to the Fines module.',
            ]);
        }

        return back()->with([
            'active_panel' => 'borrowing',
            'success' => 'Borrowing marked as Due.',
        ]);
    }

    public function storeAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'recipients' => ['required', Rule::in(['all', 'student', 'researcher', 'staff'])],
            'expires_at' => ['nullable', 'date'],
        ]);

        DB::table('announcements')->insert([
            ...$validated,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Notify target recipients
        $recipients = $validated['recipients'];
        $userQuery = User::whereRaw('LOWER(status) = ?', ['approved']);
        if ($recipients !== 'all') {
            $userQuery->where('role', $recipients);
        }
        foreach ($userQuery->cursor() as $recipientUser) {
            $recipientUser->notify(new NewAnnouncement(
                title: $validated['title'],
                body: $validated['body'],
            ));
        }

        return back()->with('success', 'Announcement published.');
    }

    public function generateCertificate(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'string', 'max:100'],
        ]);

        $activeBorrowings = DB::table('borrowing_transactions')
            ->where('barcode_id', $validated['user_id'])
            ->where('status', 'Borrowed')
            ->count();
        $unpaidFines = DB::table('financial_transactions')
            ->where('user_id', $validated['user_id'])
            ->where('transaction_type', 'Fine')
            ->where('status', 'Unpaid')
            ->sum('amount');
        $clear = $activeBorrowings === 0 && (float) $unpaidFines <= 0;

        DB::table('certificate_logs')->insert([
            'user_id' => $validated['user_id'],
            'certificate_type' => 'Library Clearance',
            'status' => $clear ? 'Cleared' : 'On Hold',
            'remarks' => $clear ? 'No active borrowings or unpaid fines.' : "Active borrowings: {$activeBorrowings}; unpaid fines: PHP ".number_format((float) $unpaidFines, 2),
            'issued_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with($clear ? 'success' : 'certificate_warning', $clear ? 'Certificate generated: patron is cleared.' : 'Certificate hold: patron has active obligations.');
    }

    public function backup()
    {
        $filename = 'panabo-library-backup-'.now()->format('Ymd-His').'.json';
        $payload = [];
        foreach (['users', 'members', 'books', 'entry_logs', 'borrowing_transactions', 'financial_transactions', 'wifi_vouchers', 'proposals', 'proposal_documents', 'announcements'] as $table) {
            if (Schema::hasTable($table)) {
                $payload[$table] = DB::table($table)->get();
            }
        }

        Storage::disk('local')->put("backups/{$filename}", json_encode($payload, JSON_PRETTY_PRINT));
        $size = Storage::disk('local')->size("backups/{$filename}");

        DB::table('backup_logs')->insert([
            'filename' => $filename,
            'disk' => 'local',
            'size' => $size,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', "Backup generated: {$filename}");
    }

    public function profile()
    {
        $user = auth()->user();
        $member = $this->memberForUser($user);
        $borrowings = $this->borrowingsForUser($user);
        $fines = $this->finesForUser($user);
        $attendance = $member ? EntryLog::where('member_id', $member->id)->latest('entry_time')->get() : collect();
        $notifications = $user->notifications()->latest()->limit(6)->get();
        $unreadNotificationCount = $user->unreadNotifications()->count();

        $proposals = collect();
        if ($user->role === 'researcher' && Schema::hasTable('proposals')) {
            $proposalQuery = DB::table('proposals')->where('user_id', $user->user_id ?? $user->email);
            if (Schema::hasColumn('proposals', 'archived_at')) {
                $proposalQuery->whereNull('archived_at');
            }
            $proposals = $proposalQuery->latest('created_at')->get();
        }

        return view('profile', compact(
            'user',
            'borrowings',
            'fines',
            'attendance',
            'proposals',
            'notifications',
            'unreadNotificationCount'
        ));
    }

    public function editProfile()
    {
        $user = auth()->user();

        return view('profile-edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'contact_no' => ['nullable', 'string', 'max:50'],
            'school' => ['nullable', 'string', 'max:255'],
            'school_id' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other', 'Prefer not to say'])],
            'birthdate' => ['nullable', 'date'],
            'age' => ['nullable', 'integer', 'min:1', 'max:120'],
        ]);

        $user->update($validated);

        $profileRoute = match ($user->role) {
            'researcher' => 'researcher.profile',
            'visitor' => 'visitor.profile',
            'staff', 'admin' => 'staff.profile',
            default => 'student.profile',
        };

        return redirect()->route($profileRoute)
            ->with('success', 'Profile updated successfully.');
    }

    public function updateUserStatus(Request $request)
    {
        $validated = $request->validate([
            'lookup' => ['required', 'string', 'max:100'],
            'status' => ['required', Rule::in(['pending', 'approved', 'retired', 'transferred'])],
        ]);

        $lookup = trim($validated['lookup']);
        $target = User::where('user_id', $lookup)
            ->orWhere('email', $lookup)
            ->orWhere('barcode_id', $lookup)
            ->when(is_numeric($lookup), function ($query) use ($lookup) {
                return $query->orWhere('id', (int) $lookup);
            })
            ->first();

        if (! $target) {
            return back()->withErrors(['user_status_error' => 'No matching user found.']);
        }

        $target->status = $validated['status'];
        $target->save();

        return back()->with('success', 'User status updated to ' . ucfirst($validated['status']) . '.');
    }

    public function storeUserAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'user_id' => ['required', 'string', 'max:100', 'unique:users,user_id'],
            'role' => ['required', Rule::in(['admin', 'staff', 'student', 'researcher', 'visitor'])],
            'status' => ['nullable', Rule::in(['pending', 'approved', 'retired', 'transferred'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'school' => ['nullable', 'string', 'max:255'],
            'contact_no' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other', 'Prefer not to say', 'Non-binary'])],
        ]);

        $user = User::create([
            'name' => trim($validated['name']),
            'email' => trim($validated['email']),
            'user_id' => trim($validated['user_id']),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'] ?? 'approved',
            'school' => $validated['school'] ?? null,
            'contact_no' => $validated['contact_no'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'barcode_id' => Member::generateUniqueBarcodeId(),
            'email_verified_at' => now(),
        ]);

        return back()->with('success', ucfirst($user->role) . ' account created successfully.');
    }

    public function resetUserAccount(Request $request, int $id)
    {
        $target = User::findOrFail($id);

        $validated = $request->validate([
            'user_id' => ['required', 'string', 'max:100', Rule::unique('users', 'user_id')->ignore($target->id)],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $target->user_id = trim($validated['user_id']);
        $target->password = Hash::make($validated['password']);
        $target->save();

        return back()->with('success', 'Account credentials updated for ' . $target->name . '.');
    }

    public function updateUserProfile(Request $request, int $id)
    {
        $target = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($target->id)],
            'role' => ['required', Rule::in(['admin', 'staff', 'student', 'researcher'])],
            'status' => ['required', Rule::in(['pending', 'approved', 'retired', 'transferred'])],
            'user_id' => ['required', 'string', 'max:100', Rule::unique('users', 'user_id')->ignore($target->id)],
            'school' => ['nullable', 'string', 'max:255'],
            'contact_no' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other', 'Prefer not to say'])],
            'birthdate' => ['nullable', 'date'],
        ]);

        $target->update($validated);

        return back()->with('success', 'User profile updated successfully.');
    }

    public function editUserProfile(int $id)
    {
        $target = User::findOrFail($id);

        return view('admin-user-edit', ['user' => $target]);
    }

    public function deleteUserProfile(int $id)
    {
        $target = User::findOrFail($id);
        abort_if($target->id === auth()->id(), 403);

        $target->delete();

        return back()->with('success', 'User profile deleted.');
    }

    public function storeSchoolOption(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        if (! Schema::hasTable('school_options')) {
            Schema::create('school_options', function ($table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        $name = trim($validated['name']);
        if ($name === '') {
            return back()->withErrors(['school_error' => 'School name cannot be empty.']);
        }

        DB::table('school_options')->updateOrInsert(['name' => $name], ['name' => $name, 'updated_at' => now(), 'created_at' => now()]);
        Cache::forget('library.school.options');

        return back()->with('success', 'School added to the registration list.');
    }

    public function deleteSchoolOption(int $id)
    {
        if (! Schema::hasTable('school_options')) {
            return back()->withErrors(['school_error' => 'No school list exists yet.']);
        }

        DB::table('school_options')->where('id', $id)->delete();
        Cache::forget('library.school.options');

        return back()->with('success', 'School removed from the registration list.');
    }

    private function memberForUser(User $user): ?Member
    {
        return Member::where('barcode_id', $user->user_id)
            ->orWhere('barcode_id', $user->email)
            ->orWhere('barcode_id', $user->barcode_id)
            ->first();
    }

    private function borrowingsForUser(User $user)
    {
        return DB::table('borrowing_transactions')
            ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
            ->where(function ($query) use ($user) {
                $query->where('borrowing_transactions.user_id', $user->user_id)
                    ->orWhere('borrowing_transactions.barcode_id', $user->user_id);

                if ($user->barcode_id) {
                    $query->orWhere('borrowing_transactions.user_id', $user->barcode_id)
                        ->orWhere('borrowing_transactions.barcode_id', $user->barcode_id);
                }
            })
            ->select('borrowing_transactions.*', 'books.title', 'books.author', 'books.isbn', 'books.call_number', 'books.accession_number')
            ->latest('borrowing_transactions.created_at')
            ->get();
    }

    public function bookLookup(Request $request)
    {
        $validated = $request->validate([
            'isbn' => ['required', 'string', 'max:100'],
        ]);

        $isbn = preg_replace('/[^0-9Xx]/', '', $validated['isbn']);

        $books = DB::table('books')
            ->whereNull('archived_at')
            ->where('isbn', $isbn)
            ->orderBy('accession_number')
            ->get();

        return response()->json([
            'isbn' => $isbn,
            'books' => $books,
        ]);
    }

    private function finesForUser(User $user)
    {
        return DB::table('financial_transactions')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->user_id);
                if ($user->barcode_id) {
                    $query->orWhere('user_id', $user->barcode_id);
                }
            })
            ->where('transaction_type', 'Fine')
            ->latest('transaction_date')
            ->get();
    }

    private function announcementsFor(string $role)
    {
        return Schema::hasTable('announcements')
            ? DB::table('announcements')
                ->where(function ($query) use ($role) {
                    $query->where('recipients', 'all')->orWhere('recipients', $role);
                })
                ->where(function ($query) {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                })
                ->latest('created_at')
                ->limit(5)
                ->get()
            : collect();
    }

    public function storeBulkVouchers(Request $request)
    {
        $validated = $request->validate([
            'prefix' => ['required', 'string', 'max:20'],
            'count' => ['required', 'integer', 'min:1', 'max:100'],
            'duration_minutes' => ['required', 'integer', 'min:15'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $created = 0;
        for ($i = 1; $i <= $validated['count']; $i++) {
            $code = strtoupper($validated['prefix']) . '-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT);
            if (DB::table('wifi_vouchers')->where('voucher_code', $code)->exists()) {
                continue;
            }
            $payload = [
                'voucher_code' => $code,
                'duration_minutes' => $validated['duration_minutes'],
                'expires_at' => $validated['expires_at'] ?? null,
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('wifi_vouchers', 'is_active')) {
                $payload['is_active'] = true;
            }
            DB::table('wifi_vouchers')->insert($payload);
            $created++;
        }

        return back()->with('success', "{$created} WiFi vouchers created successfully.");
    }

    /*
    // Disabled: bulk wifi voucher creation (kept for reference)
    public function storeBulkVouchers(Request $request)
    {
        $validated = $request->validate([
            'prefix' => ['required', 'string', 'max:20'],
            'count' => ['required', 'integer', 'min:1', 'max:100'],
            'duration_minutes' => ['required', 'integer', 'min:15'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $created = 0;
        for ($i = 1; $i <= $validated['count']; $i++) {
            $code = strtoupper($validated['prefix']) . '-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT);
            if (DB::table('wifi_vouchers')->where('voucher_code', $code)->exists()) {
                continue;
            }
            $payload = [
                'voucher_code' => $code,
                'duration_minutes' => $validated['duration_minutes'],
                'expires_at' => $validated['expires_at'] ?? null,
                'status' => 'Available',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('wifi_vouchers', 'is_active')) {
                $payload['is_active'] = true;
            }
            DB::table('wifi_vouchers')->insert($payload);
            $created++;
        }

        return back()->with('success', "{$created} WiFi vouchers created successfully.");
    }
    */

    /**
     * Create a borrow request from student/visitor through OPAC
     */
    public function requestBorrow(Request $request)
    {
        $validated = $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
        ]);

        $user = auth()->user();
        if (!$user || !in_array($user->role, ['student', 'visitor'])) {
            return response()->json(['error' => 'Unauthorized. Only students and visitors can request books.'], 403);
        }

        $book = DB::table('books')->where('id', $validated['book_id'])->whereNull('archived_at')->first();
        if (!$book) {
            return response()->json(['error' => 'Book not found.'], 404);
        }

        if (($book->available ?? 0) <= 0) {
            return response()->json(['error' => 'Book is not available at the moment.'], 422);
        }

        // Check if user already has a pending request for this book
        $existingRequest = DB::table('borrowing_transactions')
            ->where('user_id', $user->user_id)
            ->where('book_id', $validated['book_id'])
            ->where('status', 'Pending')
            ->first();

        if ($existingRequest) {
            return response()->json(['error' => 'You already have a pending request for this book.'], 422);
        }

        // Create pending borrow request
        DB::table('borrowing_transactions')->insert([
            'user_id' => $user->user_id,
            'book_id' => $book->id,
            'barcode_id' => $user->barcode_id ?? 'PENDING-' . $user->user_id,
            'book_barcode' => $book->accession_number ?? $book->isbn,
            'borrow_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Book borrow request submitted successfully! Staff will process it shortly.',
        ]);
    }

    /**
     * Get pending borrow requests for staff
     */
    public function getPendingBorrows()
    {
        $pendingBorrows = DB::table('borrowing_transactions')
            ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
            ->leftJoin('users', 'borrowing_transactions.user_id', '=', 'users.user_id')
            ->select(
                'borrowing_transactions.*',
                'books.title',
                'books.author',
                'books.accession_number',
                'users.name as user_name',
                'users.email as user_email',
                'users.role as user_role'
            )
            ->where('borrowing_transactions.status', 'Pending')
            ->orderBy('borrowing_transactions.created_at', 'asc')
            ->get();

        return $pendingBorrows;
    }

    /**
     * Approve a pending borrow request
     */
    public function approveBorrowRequest(Request $request)
    {
        $validated = $request->validate([
            'borrowing_transaction_id' => ['required', 'integer', 'exists:borrowing_transactions,id'],
        ]);

        $borrowing = DB::table('borrowing_transactions')->where('id', $validated['borrowing_transaction_id'])->first();
        if (!$borrowing || $borrowing->status !== 'Pending') {
            return back()->withErrors(['borrow_error' => 'Borrow request not found or already processed.']);
        }

        DB::transaction(function () use ($borrowing) {
            // Update borrowing status
            DB::table('borrowing_transactions')->where('id', $borrowing->id)->update([
                'status' => 'Borrowed',
                'borrow_date' => now()->toDateString(),
                'updated_at' => now(),
            ]);

            // Reconcile book availability after approving loan
            if ($borrowing->book_id) {
                $this->syncBookAvailability($borrowing->book_id);
            }
        });

        // Notify borrower
        $borrower = User::where('user_id', $borrowing->user_id)
            ->orWhere('email', $borrowing->user_id)
            ->first();
        if ($borrower) {
            try {
                $bookTitle = $borrowing->book_id
                    ? optional(DB::table('books')->where('id', $borrowing->book_id)->first())->title
                    : null;
                $borrower->notify(new BorrowRequestStatusUpdated('approved', null, $borrowing->id, $bookTitle));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to notify user about borrow approval: ' . $e->getMessage());
            }
        }

        return back()
            ->with('active_panel', 'borrow-requests')
            ->with('success', 'Borrow request approved and book issued.');
    }

    /**
     * Reject a pending borrow request
     */
    public function rejectBorrowRequest(Request $request)
    {
        $validated = $request->validate([
            'borrowing_transaction_id' => ['required', 'integer', 'exists:borrowing_transactions,id'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $borrowing = DB::table('borrowing_transactions')->where('id', $validated['borrowing_transaction_id'])->first();
        if (!$borrowing || $borrowing->status !== 'Pending') {
            return back()->withErrors(['borrow_error' => 'Borrow request not found or already processed.']);
        }

        $updateData = [
            'status' => 'Rejected',
            'updated_at' => now(),
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('borrowing_transactions', 'rejection_reason')) {
            $updateData['rejection_reason'] = trim($validated['reason'] ?? '');
        }

        DB::table('borrowing_transactions')->where('id', $borrowing->id)->update($updateData);

        // Notify borrower
        $borrower = User::where('user_id', $borrowing->user_id)
            ->orWhere('email', $borrowing->user_id)
            ->first();
        if ($borrower) {
            try {
                $bookTitle = $borrowing->book_id
                    ? optional(DB::table('books')->where('id', $borrowing->book_id)->first())->title
                    : null;
                $borrower->notify(new BorrowRequestStatusUpdated(
                    'rejected',
                    trim($validated['reason'] ?? ''),
                    $borrowing->id,
                    $bookTitle
                ));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to notify user about borrow rejection: ' . $e->getMessage());
            }
        }

        return back()
            ->with('active_panel', 'borrow-requests')
            ->with('success', 'Borrow request rejected.');
    }

    public function archiveBorrowRequest(Request $request, int $id)
    {
        $borrowing = DB::table('borrowing_transactions')->where('id', $id)->first();
        if (!$borrowing) {
            return back()->withErrors(['borrow_error' => 'Borrow request not found.']);
        }

        if (Schema::hasColumn('borrowing_transactions', 'archived_at')) {
            DB::table('borrowing_transactions')->where('id', $id)->update([
                'archived_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('borrowing_transactions')->where('id', $id)->update([
                'status' => 'Archived',
                'updated_at' => now(),
            ]);
        }

        return back()
            ->with('active_panel', 'borrow-requests')
            ->with('success', 'Borrow request archived.');
    }

    /**
     * Store a barcode replacement request from student/visitor
     */
    public function storeBarcodeRequest(Request $request)
    {
        $validated = $request->validate([
            'old_barcode_id' => ['nullable', 'string', 'max:100'],
            'reason' => ['required', 'in:lost,damaged,expired,other'],
            'reason_details' => ['nullable', 'string', 'max:500'],
            'proof' => ['nullable', 'file', 'max:4096'],
        ]);

        $user = auth()->user();
        if (!$user || !in_array($user->role, ['student', 'visitor'])) {
            return back()->withErrors(['barcode_error' => 'Unauthorized.']);
        }

        // Get the old barcode ID if user already has one
        $oldBarcodeId = $user->barcode_id ?? $validated['old_barcode_id'] ?? null;

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('barcode-proof', 'public');
        }

        // Record the request as pending review rather than auto-completing it.
        $barcodeRequest = BarcodeRequest::create([
            'user_id' => $user->id,
            'old_barcode_id' => $oldBarcodeId,
            'new_barcode_id' => null,
            'reason' => $validated['reason'] . ($validated['reason_details'] ? ' - ' . $validated['reason_details'] : ''),
            'reason_details' => $validated['reason_details'] ?? null,
            'status' => 'pending',
            'proof_path' => $proofPath,
            'approved_by' => null,
            'staff_notes' => null,
        ]);

        return back()
            ->with('active_panel', 'dashboard')
            ->with('success', 'Barcode replacement request submitted for review.');
    }

    /**
     * Export selected report categories as a PDF file.
     * Only categories present in the 'categories' query parameter are included.
     */
    public function exportReportsPdf(Request $request)
    {
        $allowed = ['schools','ages','proposals','topusers','topwifi','borrowing','genders','gatecheckins'];
        $categories = $request->input('categories', []);
        if (is_string($categories)) {
            $categories = explode(',', $categories);
        }
        $categories = array_intersect($categories, $allowed);

        if (empty($categories)) {
            return back()->withErrors(['report_error' => 'Select at least one report category to export.']);
        }

        $data = ['categories' => $categories, 'generatedAt' => now()->format('F d, Y h:i A')];

        // --- Proposals ---
        if (in_array('proposals', $categories) && Schema::hasTable('proposals')) {
            $data['proposalRows'] = DB::table('proposals')
                ->leftJoin('users', function ($join) {
                    $join->on('users.user_id', '=', 'proposals.user_id')
                         ->orOn('users.email', '=', 'proposals.user_id');
                })
                ->whereNull('proposals.archived_at')
                ->select('users.name as researcher_name', 'proposals.title', 'proposals.created_at', 'proposals.deadline', 'proposals.budget')
                ->orderByDesc('proposals.created_at')
                ->limit(500)
                ->get();
        }

        // --- Borrowing ---
        if (in_array('borrowing', $categories) && Schema::hasTable('borrowing_transactions') && Schema::hasTable('books')) {
            $borrowingRows = DB::table('borrowing_transactions')
                ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
                ->leftJoin('members', 'borrowing_transactions.member_id', '=', 'members.id')
                ->leftJoin('users', function ($join) {
                    $join->on('users.user_id', '=', 'borrowing_transactions.user_id')
                         ->orOn('users.email', '=', 'borrowing_transactions.user_id');
                })
                ->select(
                    'borrowing_transactions.id',
                    'borrowing_transactions.borrow_date',
                    'borrowing_transactions.due_date',
                    'borrowing_transactions.returned_at',
                    'borrowing_transactions.status as borrow_status',
                    'borrowing_transactions.user_id',
                    'borrowing_transactions.barcode_id',
                    'books.title as book_title',
                    'books.author as book_author',
                    'books.category as book_category',
                    'members.full_name as member_name',
                    'users.name as user_name'
                )
                ->orderByDesc('borrowing_transactions.borrow_date')
                ->limit(500)
                ->get();

            // Resolve borrower name and attach fines
            foreach ($borrowingRows as $row) {
                $row->borrower_name = $row->member_name ?: $row->user_name ?: $row->barcode_id ?: 'Unknown';
                // Look up fine for this borrower + book
                $fine = null;
                if (Schema::hasTable('financial_transactions')) {
                    $fine = DB::table('financial_transactions')
                        ->where('transaction_type', 'Fine')
                        ->where(function ($q) use ($row) {
                            $q->where('user_id', $row->user_id)
                              ->orWhere('user_id', $row->barcode_id);
                        })
                        ->where('description', 'like', '%' . ($row->book_title ?? '') . '%')
                        ->sum('amount');
                }
                $row->fine_amount = $fine ?: 0;
            }
            $data['borrowingRows'] = $borrowingRows;
        }

        // --- Schools ---
        if (in_array('schools', $categories) && Schema::hasTable('users')) {
            $data['schoolRows'] = DB::table('users')
                ->select('school', DB::raw('COUNT(*) as count'))
                ->whereNotNull('school')->where('school', '!=', '')
                ->groupBy('school')->orderByDesc('count')->limit(20)->get();
            $data['totalSchoolUsers'] = DB::table('users')->whereNotNull('school')->where('school', '!=', '')->count();
        }

        // --- Ages ---
        if (in_array('ages', $categories) && Schema::hasTable('users') && Schema::hasColumn('users', 'age')) {
            $ageData = [];
            foreach (['18-22','23-27','28+'] as $bracket) {
                [$low, $high] = $bracket === '28+' ? [28, 999] : explode('-', $bracket);
                $ageData[$bracket] = DB::table('users')->whereNotNull('age')->whereBetween('age', [(int)$low, (int)$high])->count();
            }
            $data['ageRows'] = $ageData;
        }

        // --- Top Users ---
        if (in_array('topusers', $categories) && Schema::hasTable('entry_logs') && Schema::hasTable('members')) {
            $data['topVisitorRows'] = DB::table('entry_logs')
                ->join('members', 'entry_logs.member_id', '=', 'members.id')
                ->select('members.full_name', 'members.barcode_id', DB::raw('COUNT(*) as visit_count'))
                ->groupBy('members.barcode_id', 'members.full_name')
                ->orderByDesc('visit_count')->limit(10)->get();
        }

        // --- Top WiFi Users ---
        if (in_array('topwifi', $categories) && Schema::hasTable('wifi_vouchers')) {
            $data['topWifiRows'] = DB::table('wifi_vouchers')
                ->leftJoin('users', 'wifi_vouchers.user_id', '=', 'users.user_id')
                ->select('users.name', 'wifi_vouchers.user_id', 'wifi_vouchers.bandwidth_gb', 'wifi_vouchers.voucher_code')
                ->orderByDesc('wifi_vouchers.bandwidth_gb')->limit(10)->get();
        }

        // --- Gender Demographics ---
        if (in_array('genders', $categories) && Schema::hasTable('users') && Schema::hasColumn('users', 'gender')) {
            $data['femaleCount'] = DB::table('users')->where('gender', 'Female')->count();
            $data['maleCount'] = DB::table('users')->where('gender', 'Male')->count();
            $genderTotal = max(1, $data['femaleCount'] + $data['maleCount']);
            $data['femalePct'] = round(($data['femaleCount'] / $genderTotal) * 100);
            $data['malePct'] = round(($data['maleCount'] / $genderTotal) * 100);
        }

        // --- Gate Check-ins ---
        if (in_array('gatecheckins', $categories) && Schema::hasTable('entry_logs')) {
            $data['gateRows'] = DB::table('entry_logs')
                ->leftJoin('members', 'entry_logs.member_id', '=', 'members.id')
                ->select('members.full_name', 'members.barcode_id', 'entry_logs.entry_time', 'entry_logs.exit_time')
                ->orderByDesc('entry_logs.entry_time')->limit(100)->get();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf-export', $data);
        $pdf->setPaper('A4', 'portrait');

        // Preview mode — render as HTML page first
        if ($request->has('view')) {
            return view('admin.reports.preview', array_merge($data, [
                'categoriesParam' => implode(',', $categories),
            ]));
        }

        return $pdf->download('library-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Synchronize a book's available count and status based on
     * total copies minus currently active (Borrowed) loans.
     *
     * available = copies - active_borrowed_count
     * status    = available > 0 ? 'Available' : 'All Out'
     */
    private function syncBookAvailability(int $bookId): void
    {
        $book = DB::table('books')->where('id', $bookId)->first();
        if (! $book) return;

        $activeLoans = DB::table('borrowing_transactions')
            ->where('book_id', $bookId)
            ->where('status', 'Borrowed')
            ->count();

        $available = max(0, ($book->copies ?? 1) - $activeLoans);

        DB::table('books')->where('id', $bookId)->update([
            'available'   => $available,
            'status'      => $available > 0 ? 'Available' : 'All Out',
            'updated_at'  => now(),
        ]);
    }
}
