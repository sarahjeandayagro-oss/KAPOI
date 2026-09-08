replacement = '''public function adminDashboard()
    {
        $pendingUsers = $this->getPendingUsers();
        $verifiedUsers = $this->getVerifiedUsers();
        $availableBarcodes = $this->getAvailableBarcodes();
        $books = $this->getBooks();

        $dashboardCounts = $this->getDashboardCounts();
        $genderData = $this->getGenderDemographics();
        $monthlyBorrowing = $this->getMonthlyBorrowing();
        $financialSummary = $this->getFinancialTransactionsSummary();
        $recentBorrowings = $this->getRecentBorrowings();
        $recentActivity = $this->getRecentActivity();
        $schoolData = $this->getSchoolDistribution();
        $ageData = $this->getAgeBracketTotals();
        $topVisitors = $this->getTopVisitors();
        $topWifiUsers = $this->getTopWifiUsers();
        $gateEntries = $this->getGateEntries();
        $proposalStats = $this->getProposalStats();
        $juneAnalytics = $this->getJuneVisitorAnalytics();

        $mostActiveStudent = $this->getMostActiveMemberByRole('student', $juneAnalytics['juneStart'], $juneAnalytics['juneEnd']);
        $mostActiveResearcher = $this->getMostActiveMemberByRole('researcher', $juneAnalytics['juneStart'], $juneAnalytics['juneEnd']);

        $barcodeRequestSegments = $this->getBarcodeRequestSegments();
        $borrowRequestSegments = $this->getBorrowRequestSegments();
        $bookTransactionFeed = $this->getBookTransactionFeed();
        $rejectedBorrowRequests = $this->getRejectedBorrowRequests();
        $archivedBorrowRequests = $borrowRequestSegments['archivedBorrowRequests'];

        $notificationData = $this->getNotificationsForUser(auth()->user());
        $schoolOptions = $this->getSchoolOptions();

        return view('admin-dashboard', array_merge([
            'pendingUsers' => $pendingUsers,
            'verifiedUsers' => $verifiedUsers,
            'availableBarcodes' => $availableBarcodes,
            'books' => $books,
            'monthlyBorrowing' => $monthlyBorrowing,
            'recentBorrowings' => $recentBorrowings,
            'recentActivity' => $recentActivity,
            'topVisitors' => $topVisitors,
            'topWifiUsers' => $topWifiUsers,
            'gateEntries' => $gateEntries,
            'proposalStats' => $proposalStats,
            'mostActiveStudent' => $mostActiveStudent,
            'mostActiveResearcher' => $mostActiveResearcher,
            'bookTransactionFeed' => $bookTransactionFeed,
            'rejectedBorrowRequests' => $rejectedBorrowRequests,
            'archivedBorrowRequests' => $archivedBorrowRequests,
            'schoolOptions' => $schoolOptions,
        ], $dashboardCounts, $genderData, $schoolData, $ageData, $juneAnalytics, $financialSummary, $notificationData, $barcodeRequestSegments, $borrowRequestSegments));
    }

    private function getPendingUsers()
    {
        if (! Schema::hasTable('users')) {
            return collect();
        }

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

        return $pendingStudents->concat($pendingResearchers)->concat($pendingVisitors);
    }

    private function getVerifiedUsers()
    {
        if (! Schema::hasTable('users')) {
            return collect();
        }

        return User::whereRaw('LOWER(status) = ?', ['approved'])
            ->whereIn('role', ['student', 'researcher', 'visitor'])
            ->latest('updated_at')
            ->get();
    }

    private function getAvailableBarcodes()
    {
        return Schema::hasTable('members')
            ? Member::where('status', 'available')->orderBy('barcode_id')->get()
            : collect();
    }

    private function getBooks()
    {
        return Schema::hasTable('books')
            ? DB::table('books')->whereNull('archived_at')->orderBy('title')->get()
            : collect();
    }

    private function getDashboardCounts()
    {
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

        return compact('totalBooks', 'activeBorrowers', 'returnedToday', 'overdueBooks');
    }

    private function getGenderDemographics()
    {
        $femaleCount = Schema::hasTable('users')
            ? User::where('gender', 'Female')->count()
            : 0;

        $maleCount = Schema::hasTable('users')
            ? User::where('gender', 'Male')->count()
            : 0;

        $totalGender = $femaleCount + $maleCount;
        $femalePct = $totalGender > 0 ? round(($femaleCount / $totalGender) * 100) : 0;
        $malePct = $totalGender > 0 ? round(($maleCount / $totalGender) * 100) : 0;

        return compact('femaleCount', 'maleCount', 'femalePct', 'malePct');
    }

    private function getMonthlyBorrowing()
    {
        $monthlyBorrowing = [];

        if (! Schema::hasTable('borrowing_transactions')) {
            return $monthlyBorrowing;
        }

        for ($m = 1; $m <= 12; $m++) {
            $monthlyBorrowing[$m] = DB::table('borrowing_transactions')
                ->whereYear('borrow_date', now()->year)
                ->whereMonth('borrow_date', $m)
                ->count();
        }

        return $monthlyBorrowing;
    }

    private function getFinancialTransactionsSummary()
    {
        $financialTransactions = Schema::hasTable('financial_transactions')
            ? collect(DB::table('financial_transactions')->get())
            : collect();

        $collectedFines = $financialTransactions->where('transaction_type', 'Fine')->where('status', 'Paid')->sum('amount');
        $unpaidFines = $financialTransactions->where('transaction_type', 'Fine')->where('status', 'Unpaid')->sum('amount');
        $fundIncome = $financialTransactions->whereIn('transaction_type', ['Fund', 'Fund Income'])->sum('amount');
        $fundExpense = $financialTransactions->where('transaction_type', 'Fund Expense')->sum('amount');
        $availableFunds = $fundIncome - $fundExpense;
        $approvedResearchFunds = Schema::hasTable('proposals')
            ? DB::table('proposals')->where('status', 'Approved')->sum('approved_grant')
            : 0;

        return compact('collectedFines', 'unpaidFines', 'fundIncome', 'fundExpense', 'availableFunds', 'approvedResearchFunds');
    }

    private function getRecentBorrowings()
    {
        return Schema::hasTable('borrowing_transactions')
            ? DB::table('borrowing_transactions')
                ->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')
                ->select(
                    'borrowing_transactions.*',
                    'books.title',
                    'books.author',
                    'books.isbn',
                    'books.call_number',
                    'books.accession_number'
                )
                ->latest('borrowing_transactions.created_at')
                ->limit(5)
                ->get()
            : collect();
    }

    private function getRecentActivity()
    {
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

        return $recentActivity->sortByDesc('time_parsed')->take(5);
    }

    private function getSchoolDistribution()
    {
        $schoolDistribution = Schema::hasTable('users')
            ? DB::table('users')
                ->select('school', DB::raw('COUNT(*) as count'))
                ->whereNotNull('school')
                ->groupBy('school')
                ->orderByDesc('count')
                ->get()
            : collect();

        return [
            'schoolDistribution' => $schoolDistribution,
            'totalSchoolUsers' => $schoolDistribution->sum('count'),
        ];
    }

    private function getAgeBracketTotals()
    {
        $ageBrackets = ['18-22' => 0, '23-27' => 0, '28+' => 0];

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'age')) {
            $users = User::whereNotNull('age')->get();
            foreach ($users as $u) {
                $age = (int) $u->age;
                if ($age >= 18 && $age <= 22) {
                    $ageBrackets['18-22']++;
                } elseif ($age >= 23 && $age <= 27) {
                    $ageBrackets['23-27']++;
                } elseif ($age >= 28) {
                    $ageBrackets['28+']++;
                }
            }
        }

        return [
            'ageBrackets' => $ageBrackets,
            'totalAgeUsers' => array_sum($ageBrackets),
        ];
    }

    private function getTopVisitors()
    {
        return Schema::hasTable('entry_logs')
            ? DB::table('entry_logs')
                ->join('members', 'entry_logs.member_id', '=', 'members.id')
                ->select('members.full_name', 'members.barcode_id', DB::raw('COUNT(*) as visit_count'))
                ->groupBy('members.barcode_id', 'members.full_name')
                ->orderByDesc('visit_count')
                ->limit(10)
                ->get()
            : collect();
    }

    private function getTopWifiUsers()
    {
        return Schema::hasTable('wifi_vouchers')
            ? DB::table('wifi_vouchers')
                ->where('status', 'Used')
                ->whereNotNull('name')
                ->select('name', 'user_id', 'bandwidth_gb', 'voucher_code')
                ->orderByDesc('bandwidth_gb')
                ->limit(10)
                ->get()
            : collect();
    }

    private function getGateEntries()
    {
        return Schema::hasTable('entry_logs')
            ? DB::table('entry_logs')
                ->leftJoin('members', 'entry_logs.member_id', '=', 'members.id')
                ->select('entry_logs.*', 'members.full_name', 'members.barcode_id')
                ->latest('entry_logs.entry_time')
                ->limit(20)
                ->get()
            : collect();
    }

    private function getProposalStats()
    {
        if (! Schema::hasTable('proposals')) {
            return ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
        }

        return [
            'total' => DB::table('proposals')->whereNull('archived_at')->count(),
            'pending' => DB::table('proposals')->whereNull('archived_at')->where('status', 'Pending')->count(),
            'approved' => DB::table('proposals')->whereNull('archived_at')->where('status', 'Approved')->count(),
            'rejected' => DB::table('proposals')->whereNull('archived_at')->where('status', 'Rejected')->count(),
        ];
    }

    private function getJuneVisitorAnalytics()
    {
        $juneStart = Carbon::create(2026, 6, 1)->startOfDay();
        $juneEnd = Carbon::create(2026, 6, 30)->endOfDay();
        $daysInJuneSoFar = min(now()->day, 30);

        $totalVisitorsToday = Schema::hasTable('entry_logs')
            ? DB::table('entry_logs')->whereDate('entry_time', today())->count()
            : 0;

        $totalVisitorsThisMonth = Schema::hasTable('entry_logs')
            ? DB::table('entry_logs')->whereBetween('entry_time', [$juneStart, $juneEnd])->count()
            : 0;

        $avgDailyVisitors = $daysInJuneSoFar > 0
            ? round($totalVisitorsThisMonth / $daysInJuneSoFar)
            : 0;

        $topVisitorsJune = Schema::hasTable('entry_logs')
            ? DB::table('entry_logs')
                ->join('members', 'entry_logs.member_id', '=', 'members.id')
                ->whereBetween('entry_logs.entry_time', [$juneStart, $juneEnd])
                ->select('members.full_name', DB::raw('COUNT(*) as visit_count'))
                ->groupBy('members.id', 'members.full_name')
                ->orderByDesc('visit_count')
                ->limit(5)
                ->get()
            : collect();

        return compact('juneStart', 'juneEnd', 'totalVisitorsToday', 'totalVisitorsThisMonth', 'avgDailyVisitors', 'topVisitorsJune');
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
'''
