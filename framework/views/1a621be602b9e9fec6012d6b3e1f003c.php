<?php $__env->startSection('title', 'Panabo City Library - Admin Portal'); ?>

<?php
  $pendingUsers = $pendingUsers ?? collect();
  $verifiedUsers = $verifiedUsers ?? collect();
  $availableBarcodes = $availableBarcodes ?? collect();
  $recentBorrowings = $recentBorrowings ?? collect();
  $pendingBarcodeRequests = $pendingBarcodeRequests ?? collect();
  $approvedBarcodeRequests = $approvedBarcodeRequests ?? collect();
  $rejectedBarcodeRequests = $rejectedBarcodeRequests ?? collect();
  $pendingBorrowRequests = $pendingBorrowRequests ?? collect();
  $approvedBorrowRequests = $approvedBorrowRequests ?? collect();
  $archivedBorrowRequests = $archivedBorrowRequests ?? collect();
  $rejectedBorrowRequests = $rejectedBorrowRequests ?? collect();
  $bookTransactionFeed = $bookTransactionFeed ?? collect();

  $sidebar = view('components.sidebar', [
    'brand' => 'Panabo City Library',
    'subtitle' => 'Admin Portal',
    'avatar' => auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A',
    'userName' => auth()->user()?->name ?? 'Admin',
    'userRole' => 'Admin',
    'activePanel' => session('active_panel', 'dashboard'),
  ]);

  $hour = now()->hour;
  $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
  $userFirstName = explode(' ', auth()->user()?->name ?? 'Admin')[0];

  $topbar = view('components.topbar', [
    'title' => $greeting . ', ' . $userFirstName . ' 👋',
    'subtitle' => "Here's what's happening at Panabo City Library today.",
  ]);
?>

<?php $__env->startSection('content'); ?>

<section id="panel-dashboard" class="panel active dashboard-stats-section">
    <div class="stats-grid">
     <!-- <?php echo $__env->make('components.stat-card', ['label' => 'Pending Verifications', 'value' => count($pendingUsers), 'change' => 'Awaiting approval', 'tone' => 'gray', 'icon' => '🕒'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> -->
      <?php echo $__env->make('components.stat-card', ['label' => 'Total Books', 'value' => $totalBooks ?? 0, 'change' => 'In collection', 'tone' => 'blue', 'icon' => '📚'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <?php echo $__env->make('components.stat-card', ['label' => 'Active Borrowers', 'value' => $activeBorrowers ?? 0, 'change' => 'Currently borrowed', 'tone' => 'orange', 'icon' => '👥'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <?php echo $__env->make('components.stat-card', ['label' => 'Overdue Books', 'value' => $overdueBooks ?? 0, 'change' => 'Past due date', 'tone' => 'red', 'icon' => '⚠️'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <?php echo $__env->make('components.stat-card', ['label' => 'Returned Today', 'value' => $returnedToday ?? 0, 'change' => 'Books returned', 'tone' => 'yellow', 'icon' => '🔁'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <?php echo $__env->make('components.stat-card', ['label' => 'Total Visitors (' . ($currentMonthName ?? now()->format('F')) . ')', 'value' => $totalVisitorsThisMonth ?? 0, 'change' => 'This month', 'tone' => 'brown', 'icon' => '👣'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    
    <div class="operations-summary">
      <div class="operations-header">
        <div>
          <div class="operations-title">Today's Library Operations</div>
          <div class="operations-subtitle">Items that need admin attention now</div>
        </div>
        <div class="operations-command-bar" aria-label="Admin shortcuts">
          <button class="operations-command" onclick="showPanel('books'); setTimeout(function(){ openModal('addBookModal'); }, 200);" title="Create a new catalog record">New Catalog Item</button>
          <button class="operations-command" onclick="showPanel('borrowing')" title="Open borrowing records">Borrowing Desk</button>
          <button class="operations-command" onclick="showPanel('system')" title="Open user management">Members</button>
          <button class="operations-command" onclick="showPanel('reports')" title="Open report generator">Reports</button>
        </div>
      </div>
      <div class="operations-grid">
        <a href="#" onclick="showPanel('borrow-requests'); return false;" class="operations-item urgent">
          <span class="operations-item-label">Pending Borrow Requests</span>
          <strong><?php echo e($pendingBorrowRequests->count()); ?></strong>
          <span>Waiting for review</span>
        </a>
        <a href="#" onclick="showPanel('borrowing'); return false;" class="operations-item danger">
          <span class="operations-item-label">Overdue Books</span>
          <strong><?php echo e($overdueBooks ?? 0); ?></strong>
          <span>Past due date</span>
        </a>
        <a href="#" onclick="showPanel('borrowing'); return false;" class="operations-item success">
          <span class="operations-item-label">Returns Processed</span>
          <strong><?php echo e($returnedToday ?? 0); ?></strong>
          <span>Recorded today</span>
        </a>
        <a href="#" onclick="showPanel('system'); return false;" class="operations-item neutral">
          <span class="operations-item-label">Pending Verifications</span>
          <strong><?php echo e($pendingUsers->count()); ?></strong>
          <span>New accounts</span>
        </a>
      </div>
    </div>

    
    <div class="admin-overview-row">
      
      <div class="card admin-collection-overview">
        <div class="card-header">
          <div><div class="card-title">Collection Overview</div><div class="card-sub">Current collection status</div></div>
        </div>
        <div class="card-body">
          <div class="collection-stats">
            <?php
              $totalForOverview = max($totalBooks, 1);
              $availablePct = round(($availableBooks / $totalForOverview) * 100);
              $issuedPct = round(($issuedBooks / $totalForOverview) * 100);
              $reservedPct = round(($reservedBooks / $totalForOverview) * 100);
              $lostPct = round(($lostDamagedBooks / $totalForOverview) * 100);
            ?>
            <div class="collection-total-card">
              <div class="collection-total-num"><?php echo e(number_format($totalBooks)); ?></div>
              <div class="collection-total-label">Total Books</div>
            </div>
            <div class="collection-bars">
              <div class="collection-bar-item">
                <div class="collection-bar-header">
                  <span class="collection-bar-dot" style="background:#22c55e;"></span>
                  <span class="collection-bar-label">Available</span>
                  <span class="collection-bar-val"><?php echo e($availableBooks); ?></span>
                </div>
                <div class="collection-bar-track"><div class="collection-bar-fill" style="width:<?php echo e($availablePct); ?>%; background:#22c55e;"></div></div>
              </div>
              <div class="collection-bar-item">
                <div class="collection-bar-header">
                  <span class="collection-bar-dot" style="background:#3b82f6;"></span>
                  <span class="collection-bar-label">Issued</span>
                  <span class="collection-bar-val"><?php echo e($issuedBooks); ?></span>
                </div>
                <div class="collection-bar-track"><div class="collection-bar-fill" style="width:<?php echo e($issuedPct); ?>%; background:#3b82f6;"></div></div>
              </div>
              <div class="collection-bar-item">
                <div class="collection-bar-header">
                  <span class="collection-bar-dot" style="background:#f59e0b;"></span>
                  <span class="collection-bar-label">Reserved</span>
                  <span class="collection-bar-val"><?php echo e($reservedBooks); ?></span>
                </div>
                <div class="collection-bar-track"><div class="collection-bar-fill" style="width:<?php echo e($reservedPct); ?>%; background:#f59e0b;"></div></div>
              </div>
              <div class="collection-bar-item">
                <div class="collection-bar-header">
                  <span class="collection-bar-dot" style="background:#ef4444;"></span>
                  <span class="collection-bar-label">Lost / Damaged</span>
                  <span class="collection-bar-val"><?php echo e($lostDamagedBooks); ?></span>
                </div>
                <div class="collection-bar-track"><div class="collection-bar-fill" style="width:<?php echo e($lostPct); ?>%; background:#ef4444;"></div></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      
      <div class="card admin-overdue-books">
        <div class="card-header">
          <div><div class="card-title">Overdue Books</div><div class="card-sub">Books past their due date</div></div>
          <?php if($overdueBooksList->isNotEmpty()): ?>
            <span class="overdue-count-badge"><?php echo e($overdueBooks); ?></span>
          <?php endif; ?>
        </div>
        <div class="card-body">
          <?php $__empty_1 = true; $__currentLoopData = $overdueBooksList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $overdue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $daysOverdue = \Carbon\Carbon::parse($overdue->due_date)->diffInDays(now()); ?>
            <div class="overdue-item">
              <div class="overdue-item-left">
                <div class="overdue-borrower"><?php echo e($overdue->borrower_name); ?></div>
                <div class="overdue-book-title"><?php echo e($overdue->book_title); ?></div>
                <div class="overdue-meta">
                  <span class="overdue-days <?php echo e($daysOverdue > 14 ? 'critical' : ($daysOverdue > 7 ? 'warning' : '')); ?>"><?php echo e($daysOverdue); ?> <?php echo e(Str::plural('day', $daysOverdue)); ?> overdue</span>
                  <span class="overdue-due">Due <?php echo e(\Carbon\Carbon::parse($overdue->due_date)->format('M d, Y')); ?></span>
                </div>
              </div>
              <a href="#" onclick="showPanel('borrowing'); return false;" class="overdue-view-btn" title="View in Borrowing panel">View</a>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="empty-state-success">
              <div class="empty-state-icon">🎉</div>
              <div class="empty-state-title">No overdue books</div>
              <div class="empty-state-sub">All borrowed books are currently on schedule.</div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    
    <div class="grid-2 admin-split-cards">
      
      <?php $__env->startComponent('components.card', ['title' => 'Recent Activity', 'subtitle' => 'Latest library actions']); ?>
        <div class="activity-list-redesigned">
          <?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
              $actType = $activity->type ?? 'general';
              $actIcon = match($actType) {
                'borrow' => '📖',
                'entry' => '🚶',
                'return' => '↩️',
                'register' => '👤',
                'payment' => '💰',
                'book_added' => '📚',
                'damaged' => '⚠️',
                default => '📋',
              };
              $actColor = match($actType) {
                'borrow' => '#3b82f6',
                'entry' => '#22c55e',
                'return' => '#f59e0b',
                'register' => '#8b5cf6',
                'payment' => '#10b981',
                'book_added' => '#6366f1',
                'damaged' => '#ef4444',
                default => '#6b7280',
              };
              $actTimestamp = isset($activity->time_parsed)
                ? (method_exists($activity->time_parsed, 'isToday') && $activity->time_parsed->isToday()
                    ? 'Today, ' . $activity->time_parsed->format('g:i A')
                    : $activity->time_parsed->format('M d, g:i A'))
                : ($activity->time ?? '');
            ?>
            <div class="activity-item-redesigned">
              <div class="activity-icon-circle" style="background:<?php echo e($actColor); ?>1a; color:<?php echo e($actColor); ?>;"><?php echo e($actIcon); ?></div>
              <div class="activity-body-redesigned">
                <div class="activity-text-redesigned"><?php echo $activity->text; ?></div>
                <div class="activity-time-redesigned"><?php echo e($actTimestamp); ?></div>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="empty-state">No recent activity available yet.</div>
          <?php endif; ?>
        </div>
      <?php echo $__env->renderComponent(); ?>

      
      <?php if(!empty($monthlyBorrowing) && array_sum($monthlyBorrowing) > 0): ?>
        <?php $__env->startComponent('components.card', ['title' => 'Borrowing Activity', 'subtitle' => 'Borrowed books by month (' . now()->year . ')']); ?>
          <div class="borrowing-activity-chart">
            <?php $maxBorrow = max(max($monthlyBorrowing ?: [0]), 1); ?>
            <?php $__currentLoopData = $monthlyBorrowing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthNum => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
                $monthAbbr = \Carbon\Carbon::createFromDate(null, $monthNum, 1)->format('M');
                $barHeight = round(($count / $maxBorrow) * 100);
                $isCurrentMonth = (int) now()->month === $monthNum;
              ?>
              <div class="ba-bar-col" title="<?php echo e($monthAbbr); ?>: <?php echo e($count); ?>">
                <div class="ba-bar-val"><?php echo e($count); ?></div>
                <div class="ba-bar <?php echo e($isCurrentMonth ? 'current' : ''); ?>" style="height:<?php echo e(max($barHeight, $count > 0 ? 8 : 4)); ?>px;"></div>
                <div class="ba-bar-label"><?php echo e($monthAbbr); ?></div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        <?php echo $__env->renderComponent(); ?>
      <?php else: ?>
        <?php $__env->startComponent('components.card', ['title' => 'Gender Distribution', 'subtitle' => 'Registered user demographics']); ?>
          <div class="gender-table-card">
            <div class="gender-table-row">
              <div class="gender-table-label">
                <span class="gender-pill female">Female</span>
              </div>
              <div class="gender-table-bar">
                <span style="width: <?php echo e($femalePct ?? 0); ?>%; background: #ec4899;"></span>
              </div>
              <div class="gender-table-metric">
                <strong><?php echo e($femalePct ?? 0); ?>%</strong>
                <small>(<?php echo e($femaleCount ?? 0); ?>)</small>
              </div>
            </div>
            <div class="gender-table-row">
              <div class="gender-table-label">
                <span class="gender-pill male">Male</span>
              </div>
              <div class="gender-table-bar">
                <span style="width: <?php echo e($malePct ?? 0); ?>%; background: #3b82f6;"></span>
              </div>
              <div class="gender-table-metric">
                <strong><?php echo e($malePct ?? 0); ?>%</strong>
                <small>(<?php echo e($maleCount ?? 0); ?>)</small>
              </div>
            </div>
          </div>
        <?php echo $__env->renderComponent(); ?>
      <?php endif; ?>
    </div>

    
    <div class="grid-2 admin-split-cards" style="margin-top:0;">
      <?php $__env->startComponent('components.card', ['title' => 'Recent Borrowings', 'subtitle' => 'Latest book issues on file']); ?>
        <div class="recent-borrowings-list">
          <?php $__empty_1 = true; $__currentLoopData = $recentBorrowings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="recent-borrowing-item" style="display:flex; align-items:center; gap:12px;">
              <img
                src="<?php echo e($borrow->cover_image ? asset('storage/' . ltrim($borrow->cover_image, '/')) : asset('images/library logos.jpg')); ?>"
                alt="<?php echo e($borrow->title ?? 'Book cover'); ?>"
                style="width:48px; height:64px; object-fit:cover; border-radius:8px; border:1px solid rgba(148,163,184,0.35); background:#f8fafc;"
              >
              <div style="flex:1; min-width:0;">
                <div class="recent-borrowing-book" style="font-weight:700; color:var(--text);"><?php echo e($borrow->title ?? 'Unknown'); ?></div>
                <div class="recent-borrowing-meta" style="margin-top:4px; color:var(--muted);"><?php echo e($borrow->borrower_name ?? $borrow->member_name ?? 'Member'); ?></div>
                <?php if(!empty($borrow->due_date)): ?>
                  <div class="recent-borrowing-meta" style="margin-top:4px; color:var(--muted);">
                    <strong>Due:</strong> <?php echo e(\Carbon\Carbon::parse($borrow->due_date)->format('M d, Y')); ?>

                  </div>
                <?php endif; ?>
              </div>
              <div class="recent-borrowing-status" style="white-space:nowrap; text-align:right;">
                <?php echo e($borrow->accession_number ?? $borrow->isbn ?? $borrow->barcode ?? '—'); ?>

              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="empty-state">No recent borrowings.</div>
          <?php endif; ?>
        </div>
      <?php echo $__env->renderComponent(); ?>

      <?php $__env->startComponent('components.card', ['title' => 'Gender Distribution', 'subtitle' => 'Registered user demographics']); ?>
        <div class="gender-table-card">
          <div class="gender-table-row">
            <div class="gender-table-label">
              <span class="gender-pill female">Female</span>
            </div>
            <div class="gender-table-bar">
              <span style="width: <?php echo e($femalePct ?? 0); ?>%; background: #ec4899;"></span>
            </div>
            <div class="gender-table-metric">
              <strong><?php echo e($femalePct ?? 0); ?>%</strong>
              <small>(<?php echo e($femaleCount ?? 0); ?>)</small>
            </div>
          </div>

          <div class="gender-table-row">
            <div class="gender-table-label">
              <span class="gender-pill male">Male</span>
            </div>
            <div class="gender-table-bar">
              <span style="width: <?php echo e($malePct ?? 0); ?>%; background: #3b82f6;"></span>
            </div>
            <div class="gender-table-metric">
              <strong><?php echo e($malePct ?? 0); ?>%</strong>
              <small>(<?php echo e($maleCount ?? 0); ?>)</small>
            </div>
          </div>
        </div>
      <?php echo $__env->renderComponent(); ?>
    </div>
  </section>

  <!-- VERIFIED ACCOUNTS PANEL -->
  <section id="panel-verified" class="panel">
    <?php $__env->startComponent('components.card', ['title' => 'Verified Accounts', 'subtitle' => 'Approved student, researcher, and visitor accounts']); ?>
      <?php $__env->slot('actions'); ?>
        <div class="search-bar"><span>Search</span><input type="text" id="verifiedSearchInput" onkeyup="filterVerifiedTable()" placeholder="Search name, email, or barcode..."/></div>
      <?php $__env->endSlot(); ?>
      <table class="verified-accounts-table">
        <thead><tr><th>Name</th><th>Barcode ID</th><th>School</th><th>Role</th><th>Status</th><th>Verified At</th><th></th></tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $verifiedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($user->name); ?><br><span class="text-muted"><?php echo e($user->email); ?></span></td>
              <td><?php echo e($user->barcode_id ?? 'Unassigned'); ?></td>
              <td><?php echo e($user->school ?? 'N/A'); ?></td>
              <td><?php echo e(ucfirst($user->role)); ?></td>
              <td><span class="status-chip approved">Approved</span></td>
              <td><?php echo e($user->updated_at ? \Carbon\Carbon::parse($user->updated_at)->format('M d, Y h:i A') : 'N/A'); ?></td>

            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6">No verified accounts.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    <?php echo $__env->renderComponent(); ?>
  </section>

  <section id="panel-barcode-cards" class="panel">
    <div class="card">
      <div class="card-header"><div class="card-title">Barcode Cards</div><div class="card-sub">Printable barcode ID cards for verified accounts</div></div>
      <div class="card-body" style="padding:24px;">
        <div style="display:flex; justify-content:space-between; flex-wrap:wrap; align-items:center; gap:12px;">
          <div style="max-width:640px; color:#475569;">Generate barcode card layouts for verified users. Each card includes the user name, role, barcode image, and barcode ID.</div>
          <button class="btn-primary" type="button" onclick="printBarcodeCards()">Print Barcode Cards</button>
        </div>
        <div id="barcodeCardsGrid" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:16px; margin-top:20px;">
          <?php $__empty_1 = true; $__currentLoopData = $verifiedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $verifiedUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div style="padding:18px; border:1px solid #e2e8f0; border-radius:18px; background:#ffffff; display:flex; flex-direction:column; justify-content:space-between; min-height:240px;">
              <div>
                <div style="font-size:0.78rem; font-weight:700; color:#0f766e; text-transform:uppercase; letter-spacing:0.08em;"><?php echo e(ucfirst($verifiedUser->role)); ?></div>
                <div style="font-size:1.05rem; font-weight:700; margin-top:10px; line-height:1.2;"><?php echo e($verifiedUser->name); ?></div>
                <div style="font-size:0.85rem; color:#64748b; margin-top:6px; word-break:break-word;"><?php echo e($verifiedUser->email); ?></div>
              </div>
              <div style="margin-top:18px;">
                <svg class="barcode-svg" data-code="<?php echo e($verifiedUser->barcode_id ?? $verifiedUser->user_id); ?>" aria-hidden="true" style="width:100%; height:50px;"></svg>
                <div style="margin-top:12px; font-size:0.92rem; font-weight:700; text-align:center; color:#0f172a; word-break:break-all;"><?php echo e($verifiedUser->barcode_id ?? $verifiedUser->user_id); ?></div>
              </div>
              <div style="margin-top:16px; display:flex; justify-content:space-between; align-items:center; color:#475569; font-size:0.82rem;">
                <div>Verified <?php echo e($verifiedUser->updated_at ? \Carbon\Carbon::parse($verifiedUser->updated_at)->format('M d, Y') : 'N/A'); ?></div>
                <?php if($verifiedUser->barcode_id): ?>
                  <div style="padding:4px 10px; background:#f8fafc; border-radius:999px; font-size:0.72rem; font-weight:700;">Barcode Ready</div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div style="grid-column:1/-1; padding:18px; color:#64748b;">No verified accounts available. Once users are approved, this page will show barcode cards ready to print.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

    <div id="panel-books" class="panel">
      <div class="section-header">
        <div><div class="section-title">Book Collection & Cataloging</div><div class="section-sub"></div></div>
        <div style="display:flex; gap:8px;">
          <div class="search-bar"><span>Search</span><input type="text" id="bookSearchInput" onkeyup="filterBookTable()" placeholder="Search books..."/></div>
          <button class="btn-sm gold" onclick="openModal('addBookModal')">+ Add Book</button>
        </div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:0;">
          <table id="adminBooksTable">
            <thead>
              <tr>
                <th>Barcode</th>
                <th>Call Number</th>
                <th>Title / Classification</th>
                <th>Author</th>
                <th>Type (Genre/Class)</th>
                <th>Copies</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if(\Illuminate\Support\Facades\Schema::hasTable('books')): ?>
                <?php $__empty_1 = true; $__currentLoopData = \Illuminate\Support\Facades\DB::table('books')->whereNull('archived_at')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr>
                    <td><code><?php echo e($book->barcode); ?></code></td>
                    <td><code><?php echo e($book->call_number ?? 'N/A'); ?></code></td>
                    <td><strong><?php echo e($book->title); ?></strong><br><small style="color:var(--text); opacity:0.6;">Accession: #<?php echo e($book->accession_number ?? '000'); ?> | Location: <?php echo e($book->section_location ?? 'General'); ?></small></td>
                    <td><?php echo e($book->author); ?></td>
                    <td><span class="status-chip borrowed" style="background:rgba(66,86,131,0.06); color:var(--text);"><?php echo e($book->category); ?></span></td>
                    <td><?php echo e($book->available); ?> / <?php echo e($book->copies); ?> Available</td>
                    <td>
                      <?php if($book->available > 0): ?> <span class="status-chip returned">Available</span>
                      <?php else: ?> <span class="status-chip overdue">All Out</span> <?php endif; ?>
                    </td>
                    <td>
                      <form action="<?php echo e(route('admin.books.update', $book->id)); ?>" method="POST" style="display:grid; gap:6px; min-width:240px;">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="title" value="<?php echo e($book->title); ?>">
                        <input type="hidden" name="author" value="<?php echo e($book->author); ?>">
                        <input type="hidden" name="barcode" value="<?php echo e($book->barcode); ?>">
                        <input type="hidden" name="call_number" value="<?php echo e($book->call_number); ?>">
                        <input type="hidden" name="accession_number" value="<?php echo e($book->accession_number); ?>">
                        <input type="hidden" name="category" value="<?php echo e($book->category); ?>">
                        <input type="hidden" name="section_location" value="<?php echo e($book->section_location); ?>">
                        <input type="hidden" name="isbn" value="<?php echo e($book->isbn); ?>">
                        <input type="hidden" name="summary" value="<?php echo e($book->summary); ?>">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px;">
                          <div>
                            <small style="color:var(--muted);">Copies</small>
                            <input type="number" name="copies" min="1" value="<?php echo e($book->copies); ?>" style="width:100%;">
                          </div>
                          <div>
                            <small style="color:var(--muted);">Available</small>
                            <input type="number" name="available" min="0" value="<?php echo e($book->available); ?>" style="width:100%;" title="Auto-calculated: copies minus borrowed. Server will enforce the correct value.">
                          </div>
                        </div>
                        <small style="font-size:11px; color:var(--muted);">Available = copies − borrowed (server auto-corrects on save).</small>
                        <button class="btn-sm" type="submit">Update</button>
                      </form>
                      <form action="<?php echo e(route('admin.books.delete', $book->id)); ?>" method="POST" style="margin-top:6px;" class="confirmable" data-confirm="Archive this book?">
                        <?php echo csrf_field(); ?>
                        <button class="btn-sm danger" type="submit">Archive</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr><td colspan="8" style="text-align:center;">No active books yet. Use Add Book to start the catalog.</td></tr>
                <?php endif; ?>
              <?php else: ?>
                <tr><td colspan="8" style="text-align:center;">Run migrations to enable the catalog.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  <!-- BARCODE REQUESTS PANEL -->
  <section id="panel-barcode-requests" class="panel">
    <?php $__env->startComponent('components.card', ['title' => 'Barcode Replacement Requests', 'subtitle' => 'Users requesting new barcodes (lost, damaged, etc.)']); ?>
      <table>
        <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Old Barcode</th><th>New Barcode</th><th>Reason</th><th>Status</th><th>Requested At</th><th>Action</th></tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $pendingBarcodeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><strong><?php echo e($request->user_name ?? 'Unknown'); ?></strong></td>
              <td><?php echo e($request->user_email ?? 'N/A'); ?></td>
              <td><?php echo e(ucfirst($request->user_role ?? 'student')); ?></td>
              <td><?php echo e($request->old_barcode_id ?? '—'); ?></td>
              <td><strong><?php echo e($request->new_barcode_id ?? '—'); ?></strong></td>
              <td>
                <div><?php echo e(ucfirst($request->reason ?? 'unspecified')); ?></div>
                <?php if(!empty($request->reason_details)): ?>
                  <div style="font-size: 12px; color: #6b7280; margin-top: 4px;"><?php echo e($request->reason_details); ?></div>
                <?php endif; ?>
                <?php if(!empty($request->proof_path)): ?>
                  <div style="margin-top: 6px;"><a href="<?php echo e(asset('storage/' . $request->proof_path)); ?>" target="_blank" rel="noopener">View proof</a></div>
                <?php endif; ?>
                <?php if(!empty($request->staff_notes)): ?>
                  <div style="margin-top:6px; font-size:13px; color:#374151;">Staff note: <span style="color:#6b7280; white-space:pre-wrap;"><?php echo e($request->staff_notes); ?></span></div>
                <?php endif; ?>
              </td>
              <td><span style="display:inline-flex; padding:4px 10px; border-radius:999px; background: <?php echo e(strtolower($request->status ?? '') === 'approved' ? 'rgba(34,197,94,0.12); color:#16a34a' : (strtolower($request->status ?? '') === 'rejected' ? 'rgba(239,68,68,0.12); color:#dc2626' : 'rgba(249,115,22,0.15); color:#c2410c')); ?>; font-size:11px; font-weight:600;"><?php echo e(ucfirst($request->status ?? 'pending')); ?></span></td>
              <td><?php echo e(\Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A')); ?></td>
              <td>
                <div style="display:flex; flex-wrap:wrap; gap:6px; align-items:flex-start;">
                  <form method="POST" action="<?php echo e(route('staff.barcode-request.approve', $request->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <button class="btn-sm" type="submit">Approve</button>
                  </form>

                  <form method="POST" action="<?php echo e(route('staff.barcode-request.reject', $request->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <textarea name="rejection_reason" placeholder="Reject reason" required style="width:200px; min-height:60px; padding:8px; border:1px solid #d1d5db; border-radius:6px; font-size:12px;"></textarea>
                    <button class="btn-sm" type="submit" style="background:#dc2626; margin-top:4px;">Reject</button>
                  </form>

                  <form method="POST" action="<?php echo e(route('staff.barcode-request.archive', $request->id)); ?>" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <button class="btn-sm" type="submit">Archive</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="9" style="text-align:center;">No barcode requests yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    <?php echo $__env->renderComponent(); ?>

    <?php $__env->startComponent('components.card', ['title' => 'Approved Barcode Requests', 'subtitle' => 'Requests that have been approved and assigned new barcodes']); ?>
      <table>
        <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Old Barcode</th><th>New Barcode</th><th>Approved Note</th><th>Approved At</th></tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $approvedBarcodeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><strong><?php echo e($request->user_name ?? 'Unknown'); ?></strong></td>
              <td><?php echo e($request->user_email ?? 'N/A'); ?></td>
              <td><?php echo e(ucfirst($request->user_role ?? 'student')); ?></td>
              <td><?php echo e($request->old_barcode_id ?? '—'); ?></td>
              <td><strong><?php echo e($request->new_barcode_id ?? '—'); ?></strong></td>
              <td style="max-width:220px; white-space:pre-wrap;"><?php echo e($request->staff_notes ?? 'Approved'); ?></td>
              <td><?php echo e(\Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A')); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" style="text-align:center;">No approved barcode requests yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    <?php echo $__env->renderComponent(); ?>

    <?php $__env->startComponent('components.card', ['title' => 'Rejected Barcode Requests', 'subtitle' => 'Requests declined for insufficient reason or invalid proof']); ?>
      <table>
        <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Old Barcode</th><th>Request Reason</th><th>Rejection Note</th><th>Rejected At</th></tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $rejectedBarcodeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><strong><?php echo e($request->user_name ?? 'Unknown'); ?></strong></td>
              <td><?php echo e($request->user_email ?? 'N/A'); ?></td>
              <td><?php echo e(ucfirst($request->user_role ?? 'student')); ?></td>
              <td><?php echo e($request->old_barcode_id ?? '—'); ?></td>
              <td>
                <div><?php echo e(ucfirst($request->reason ?? 'unspecified')); ?></div>
                <?php if(!empty($request->reason_details)): ?>
                  <div style="font-size:12px; color:#6b7280; margin-top:4px;"><?php echo e($request->reason_details); ?></div>
                <?php endif; ?>
              </td>
              <td style="max-width:220px; white-space:pre-wrap;"><?php echo e($request->staff_notes ?? 'No rejection note provided.'); ?></td>
              <td><?php echo e(\Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A')); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" style="text-align:center;">No rejected barcode requests yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    <?php echo $__env->renderComponent(); ?>
  </section>

  <section id="panel-borrow-requests" class="panel">
    <div class="borrow-request-panel-shell" style="padding:24px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:24px; min-height:420px; box-shadow:0 18px 46px rgba(15,23,42,0.06);">
      <div class="borrow-request-topbar" style="margin-bottom:18px;">
        <div style="padding:18px 20px; border-radius:20px; background:#ffffff; border:1px solid #d1d5db; color:#0f172a; font-size:14px; line-height:1.6; box-shadow:0 8px 22px rgba(15,23,42,0.04);">
          This admin panel shows all borrow request queues. Use the tabs above to switch between pending, approved, rejected, and archived requests.
        </div>
      </div>

      <div class="borrow-request-tabs" style="display:flex; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
        <button type="button" class="borrow-request-tab-button active" data-borrow-tab="pending">Pending Book Requests</button>
        <button type="button" class="borrow-request-tab-button" data-borrow-tab="approved">Approved Book Requests</button>
        <button type="button" class="borrow-request-tab-button" data-borrow-tab="rejected">Rejected Book Requests</button>
        <button type="button" class="borrow-request-tab-button" data-borrow-tab="archived">Archived Book Requests</button>
      </div>

      <div id="borrow-tab-pending" class="borrow-request-tab-panel" style="display:block;">
        <?php $__env->startComponent('components.card', ['title' => 'Pending Book Requests', 'subtitle' => 'Review and manage borrow requests from verified accounts']); ?>
        <?php echo $__env->make('components.borrow-requests-table', [
          'requests' => $pendingBorrowRequests,
          'showActions' => true,
          'statusColumn' => false,
          'emptyMessage' => 'No pending borrow requests yet.'
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <?php echo $__env->renderComponent(); ?>
    </div>

    <div id="borrow-tab-approved" class="borrow-request-tab-panel" style="display:none;">
      <?php $__env->startComponent('components.card', ['title' => 'Approved Book Requests', 'subtitle' => 'Borrow requests that were approved for pickup']); ?>
        <?php echo $__env->make('components.borrow-requests-table', [
          'requests' => $approvedBorrowRequests,
          'showActions' => false,
          'statusColumn' => true,
          'showArchiveAction' => true,
          'emptyMessage' => 'No approved borrow requests yet.'
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <?php echo $__env->renderComponent(); ?>
    </div>

    <div id="borrow-tab-rejected" class="borrow-request-tab-panel" style="display:none;">
      <?php $__env->startComponent('components.card', ['title' => 'Rejected Book Requests', 'subtitle' => 'Borrow requests that were rejected and may require review']); ?>
        <?php echo $__env->make('components.borrow-requests-table', [
          'requests' => $rejectedBorrowRequests,
          'showActions' => false,
          'statusColumn' => true,
          'showArchiveAction' => true,
          'showRejectionReason' => true,
          'emptyMessage' => 'No rejected borrow requests yet.'
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <?php echo $__env->renderComponent(); ?>
    </div>

    <div id="borrow-tab-archived" class="borrow-request-tab-panel" style="display:none;">
      <?php $__env->startComponent('components.card', ['title' => 'Archived Request History', 'subtitle' => 'Review borrow requests that were archived after approval or rejection']); ?>
        <table>
          <thead><tr><th>User</th><th>Book</th><th>Status</th><th>Archived At</th></tr></thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $archivedBorrowRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td><?php echo e($request->user_name ?? $request->user_id ?? 'Unknown'); ?></td>
                <td><?php echo e($request->title ?? 'Unknown book'); ?></td>
                <td><span class="status-chip returned"><?php echo e(ucfirst($request->status ?? 'Archived')); ?></span></td>
                <td><?php echo e(\Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A')); ?></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="4" style="text-align:center;">No archived borrow requests yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      <?php echo $__env->renderComponent(); ?>
    </div>
  </div>

    <script>
      (function () {
        const buttons = document.querySelectorAll('[data-borrow-tab]');
        const panels = document.querySelectorAll('.borrow-request-tab-panel');

        function setActiveTab(tabName) {
          buttons.forEach(button => {
            if (button.dataset.borrowTab === tabName) {
              button.classList.add('active');
            } else {
              button.classList.remove('active');
            }
          });

          panels.forEach(panel => {
            panel.style.display = panel.id === 'borrow-tab-' + tabName ? '' : 'none';
          });
        }

        const initialBorrowTab = '<?php echo e(session('borrow_tab', 'pending')); ?>';

        buttons.forEach(button => {
          button.addEventListener('click', () => setActiveTab(button.dataset.borrowTab));
        });

        if (initialBorrowTab) {
          setActiveTab(initialBorrowTab);
        }
      })();
    </script>
  </section>

  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* WiFi voucher panel removed as feature was deprecated from the staff/admin workflow */
    .sidebar-bottom {
      margin-top: auto;
      padding: 14px 10px;
      border-top: 1px solid var(--border);
    }

    .user-card {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 12px;
      border-radius: 10px;
      background: var(--card);
      color: var(--text);
    }

    .avatar {
      width: 34px; height: 34px;
      background: linear-gradient(135deg, var(--accent), var(--gold-light));
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-weight: 600;
      font-size: 14px;
      flex-shrink: 0;
      color: #ffffff;
    }

    .user-info { flex: 1; min-width: 0; }
    .user-name { font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .user-role { font-size: 11px; color: var(--muted); }

    .logout-item { margin-top:8px }
    .logout-item:hover { background:rgba(239,68,68,.08)!important;color:var(--error)!important }

    .nav-section + .nav-section {
      margin-top: 2px;
    }

    /* MAIN CONTAINER INTERFACES */
    .main {
      margin-left: var(--sidebar-w);
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .topbar {
      background: var(--panel);
      border-bottom: 1px solid var(--border);
      padding: 16px 32px;
      display: flex;
      align-items: center;
      gap: 16px;
      position: sticky;
      top: 0;
      z-index: 50;
      box-shadow: var(--shadow-sm);
    }

    .topbar-title {
      font-family: 'Inter', sans-serif;
      font-size: 20px;
      font-weight: 700;
      color: var(--text);
    }

    .content { padding: 32px; flex: 1; }
    .panel { display: none; }
    .panel.active { display: block; }

    /* DASHBOARD CARD LAYOUTS */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 28px; }

    .stat-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 22px;
      position: relative;
      overflow: hidden;
      color: var(--text);
      box-shadow: var(--shadow-sm);
    }

    .stat-card::before {
      content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
    }
    .stat-card.gold::before { background: var(--gold); }
    .stat-card.blue::before { background: var(--accent); }
    .stat-card.green::before { background: var(--success); }
    .stat-card.red::before { background: var(--error); }

    .dashboard-stats-section .stat-card.gray {
      background: #f3f4f6;
      border-color: #d1d5db;
    }
    .dashboard-stats-section .stat-card.gray::before {
      background: #9ca3af;
    }

    .dashboard-stats-section .stat-card.blue {
      background: #e0f2fe;
      border-color: #93c5fd;
    }

    .dashboard-stats-section .stat-card.orange {
      background: #fff7ed;
      border-color: #fdba74;
    }
    .dashboard-stats-section .stat-card.orange::before {
      background: #f59e0b;
    }

    .dashboard-stats-section .stat-card.red {
      background: #fee2e2;
      border-color: #fca5a5;
    }

    .dashboard-stats-section .stat-card.yellow {
      background: #fef3c7;
      border-color: #fcd34d;
    }
    .dashboard-stats-section .stat-card.yellow::before {
      background: #eab308;
    }

    .dashboard-stats-section .stat-card.brown {
      background: #f5e7dc;
      border-color: #d2b48c;
    }
    .dashboard-stats-section .stat-card.brown::before {
      background: #a16207;
    }

    .stat-label { font-size: 11px; color: var(--muted); font-weight: 600; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-size: 30px; font-weight: 700; line-height: 1; margin-bottom: 6px; }
    .stat-change { font-size: 12px; color: var(--muted); opacity: 0.9; }
    .stat-icon { position: absolute; top: 20px; right: 20px; font-size: 22px; opacity: 0.4; }

    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
    .admin-overview-row {
      display: grid;
      grid-template-columns: 1fr 1.4fr;
      gap: 20px;
      margin-bottom: 24px;
      align-items: stretch;
    }
    .admin-books-overview,
    .admin-activity-log {
      min-width: 0;
      height: 100%;
    }
    .gender-table-card {
      display: grid;
      gap: 16px;
      margin-top: 4px;
    }
    .gender-table-row {
      display: grid;
      grid-template-columns: 90px 1fr 90px;
      align-items: center;
      gap: 14px;
      padding: 10px 12px;
      border: 1px solid rgba(148, 163, 184, 0.2);
      border-radius: 12px;
      background: #f8fafc;
    }
    .gender-table-label {
      display: flex;
      align-items: center;
      justify-content: flex-start;
    }
    .gender-pill {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 70px;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.04em;
      color: #ffffff;
      text-transform: uppercase;
    }
    .gender-pill.female { background: #ec4899; }
    .gender-pill.male { background: #3b82f6; }
    .gender-table-bar {
      position: relative;
      width: 100%;
      height: 10px;
      background: #e2e8f0;
      border-radius: 999px;
      overflow: hidden;
    }
    .gender-table-bar span {
      display: block;
      height: 100%;
      border-radius: inherit;
    }
    .gender-table-metric {
      display: flex;
      align-items: baseline;
      justify-content: flex-end;
      gap: 4px;
      font-size: 13px;
      color: var(--text);
      white-space: nowrap;
    }
    .gender-table-metric strong {
      font-size: 15px;
      font-weight: 800;
    }
    .gender-table-metric small {
      color: var(--muted);
      font-size: 11px;
    }
    .dashboard-stats-section .admin-split-cards {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 24px;
      align-items: start;
    }
    .grid-3 { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px; }

    @media (max-width: 1100px) {
      .grid-2,
      .dashboard-stats-section .admin-split-cards,
      .admin-overview-row {
        grid-template-columns: 1fr;
      }
      .operations-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
      .grid-3 { grid-template-columns: 1fr; }
      .collection-stats {
        flex-direction: column;
      }
      .collection-total-card {
        flex-direction: row;
        gap: 12px;
        align-items: baseline;
        min-width: auto;
      }
      .borrowing-activity-chart {
        height: 100px;
      }
    }

    @media (max-width: 860px) {
      .sidebar { position: static; width: 100%; height: auto; }
      .main { margin-left: 0; }
      .topbar { position: static; }
      .content { padding: 22px; }
      .operations-header {
        align-items: flex-start;
        flex-direction: column;
      }
      .operations-command-bar {
        width: 100%;
      }
      .admin-overview-row {
        grid-template-columns: 1fr;
      }
      .overdue-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
      }
    }

    @media (max-width: 600px) {
      .stats-grid {
        grid-template-columns: 1fr 1fr;
        gap: 10px;
      }
      .operations-grid {
        grid-template-columns: 1fr;
      }
      .operations-command {
        flex: 1 1 calc(50% - 5px);
        justify-content: center;
      }
    }

    /* COMPONENT CARDS ARRAYS */
    .card { background: var(--panel); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; color: var(--text); box-shadow: var(--shadow-sm); }
    .card-header { padding: 18px 22px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; background: var(--card); }
    .card-title { font-weight: 700; font-size: 14px; }
    .card-sub { font-size: 12px; color: var(--muted); margin-top: 2px; }
    .card-body { padding: 20px 22px; background: #ffffff; color: var(--text); }

    /* ACTION UTILITIES BUTTONS */
    .btn-sm {
      padding: 7px 14px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 500;
      border: 1px solid var(--border, #d1d5db);
      background: #f8fafc;
      color: var(--text, #111827);
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-sm:hover {
      border-color: var(--primary, #2f9e8f);
      color: var(--primary, #2f9e8f);
      background: #ecfdf5;
    }

    .btn-sm.gold {
      background: var(--primary, #2f9e8f);
      border-color: var(--primary, #2f9e8f);
      color: #ffffff;
      font-weight: 600;
    }

    .btn-sm.gold:hover {
      background: var(--primary-hover, #26867a);
      color: #ffffff;
    }

    .btn-sm.danger {
      background: rgba(239, 68, 68, 0.15);
      border-color: var(--error, #ef4444);
      color: var(--error, #ef4444);
    }

    .btn-sm.danger:hover {
      background: var(--error, #ef4444);
      color: white;
    }

    .btn-sm.success {
      background: rgba(34, 197, 94, 0.15);
      border-color: var(--success, #22c55e);
      color: var(--success, #22c55e);
    }

    .btn-sm.success:hover {
      background: var(--success, #22c55e);
      color: white;
    }

    /* TABLES MATRIX DESIGN */
    table { width: 100%; border-collapse: collapse; }
    th {
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: var(--text);
      font-weight: 700;
      padding: 12px 14px;
      text-align: left;
      border-bottom: 1px solid var(--border);
      background: var(--panel);
    }

    td {
      padding: 14px 14px;
      font-size: 13px;
      border-bottom: 1px solid var(--border);
      color: var(--text);
      background: #ffffff;
    }

    tr:last-child td { border-bottom: none; }
    tr:hover td { background: var(--bg); }

    .status-chip {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
    }

    .status-chip.borrowed { background: rgba(47, 158, 143, 0.12); color: var(--gold); }
    .status-chip.returned { background: rgba(34, 197, 94, 0.12); color: var(--success); }
    .status-chip.overdue { background: rgba(239, 68, 68, 0.12); color: var(--error); }
    .status-chip.pending { background: rgba(243, 156, 18, 0.15); color: var(--warning); }
    .status-chip.approved { background: rgba(34, 197, 94, 0.12); color: var(--success); }
    .status-chip.rejected,
    .status-chip.suspended { background: rgba(239, 68, 68, 0.12); color: var(--error); }
    .status-chip.neutral { background: rgba(100, 116, 139, 0.12); color: #475569; }

    /* User records table */
    .user-records-card {
      margin-bottom: 24px;
    }
    .user-records-header {
      align-items: flex-start;
      gap: 12px;
    }
    .user-records-body {
      padding: 0;
    }
    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    .user-records-table-wrap {
      width: 100%;
    }
    .user-records-table {
      min-width: 980px;
    }
    .user-records-table th {
      background: #f8fafc;
      color: #475569;
    }
    .user-records-table th,
    .user-records-table td {
      padding: 14px 16px;
      vertical-align: middle;
    }
    .user-records-table tbody tr {
      min-height: 58px;
    }
    .user-record-id {
      color: var(--muted);
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
      font-size: 12px;
      white-space: nowrap;
    }
    .user-record-name strong {
      color: var(--text);
      font-weight: 700;
    }
    .user-record-muted {
      color: var(--muted);
      max-width: 220px;
    }
    .actions-col,
    .user-record-actions-cell {
      min-width: 190px;
      width: 190px;
    }
    .table-actions {
      align-items: center;
      display: flex;
      gap: 8px;
      justify-content: flex-start;
      white-space: nowrap;
    }
    .table-actions form {
      margin: 0;
    }
    .table-action-btn {
      align-items: center;
      border: 1px solid var(--border);
      border-radius: 8px;
      cursor: pointer;
      display: inline-flex;
      font-family: 'Inter', sans-serif;
      font-size: 12px;
      font-weight: 700;
      gap: 6px;
      height: 36px;
      justify-content: center;
      min-width: 78px;
      padding: 0 12px;
      text-decoration: none;
      transition: all 0.18s ease;
    }
    .table-action-btn svg {
      height: 15px;
      width: 15px;
    }
    .table-action-btn.edit {
      background: #f8fafc;
      color: var(--text);
    }
    .table-action-btn.edit:hover,
    .table-action-btn.edit:focus-visible {
      background: #ecfdf5;
      border-color: var(--primary);
      color: var(--primary);
    }
    .table-action-btn.delete {
      background: rgba(239, 68, 68, 0.1);
      border-color: rgba(239, 68, 68, 0.35);
      color: var(--error);
    }
    .table-action-btn.delete:hover,
    .table-action-btn.delete:focus-visible {
      background: var(--error);
      border-color: var(--error);
      color: #ffffff;
    }
    .table-action-btn:focus-visible {
      outline: 3px solid rgba(47, 158, 143, 0.25);
      outline-offset: 2px;
    }
    .user-record-empty {
      color: var(--muted);
      padding: 28px 16px;
      text-align: center;
    }

    @media (max-width: 720px) {
      .table-actions {
        align-items: stretch;
        flex-direction: column;
      }
      .table-action-btn {
        width: 100%;
      }
    }

    .btn-sm.report-export {
      background: linear-gradient(90deg, #40E0D0 0%, #7C3AED 50%, #F59E0B 100%);
      border: none;
      color: #ffffff;
      box-shadow: 0 10px 22px rgba(64, 224, 208, 0.18);
    }
    .btn-sm.report-export:hover {
      filter: brightness(1.05);
    }

    .borrow-request-tabs {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 20px;
    }

    .borrow-request-tab-button {
      padding: 10px 16px;
      border-radius: 999px;
      border: 1px solid var(--border, #d1d5db);
      background: var(--bg, #ffffff);
      color: var(--text, #111827);
      font-weight: 600;
      cursor: pointer;
      transition: all 0.15s ease;
      min-width: 180px;
      text-align: center;
    }

    .borrow-request-tab-button:hover {
      background: var(--panel, #f8fafc);
    }

    .borrow-request-tab-button.active {
      background: var(--primary, #2f9e8f);
      color: #ffffff;
      border-color: var(--primary, #2f9e8f);
      box-shadow: 0 8px 20px rgba(47, 158, 143, 0.12);
    }

    .borrow-request-tab-panel {
      display: none;
    }

    .borrow-request-tab-panel:first-of-type {
      display: block;
    }

    .bar-fill.gender-female { background: #EC4899; }
    .bar-fill.gender-male { background: #2563EB; }
    .bar-fill.age-underage { background: #FACC15; }
    .bar-fill.age-youth { background: #FB923C; }
    .bar-fill.age-other { background: #111827; }
    .bar-fill.school-dnsc { background: #16A34A; }
    .bar-fill.school-um { background: #7C2D12; }
    .bar-fill.school-pnhs { background: #1D4ED8; }
    .bar-fill.school-maryknoll { background: #60A5FA; }
    .bar-fill.school-southerndavao { background: #EC4899; }
    .bar-fill.school-northdavao { background: #FCA5A5; }
    .bar-fill.school-aces { background: #F8FAFC; border: 1px solid #D1D5DB; }
    .bar-fill.school-sanvicente { background: #6B7280; }

    .report-badge { display: inline-flex; align-items: center; gap: 8px; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; }
    .report-badge.gender-female { background: #FCE7F3; color: #BE185D; }
    .report-badge.gender-male { background: #DBEAFE; color: #1D4ED8; }
    .report-badge.school-dnsc { background: #DCFCE7; color: #166534; }
    .report-badge.school-um { background: #FEE2E2; color: #7C2D12; }
    .report-badge.school-pnhs { background: #DBEAFE; color: #1D4ED8; }
    .report-badge.school-maryknoll { background: #DBEAFE; color: #2563EB; }
    .report-badge.school-southerndavao { background: #FBCFE8; color: #BE185D; }
    .report-badge.school-northdavao { background: #FECACA; color: #B91C1C; }
    .report-badge.school-aces { background: #F8FAFC; color: #111827; border: 1px solid #D1D5DB; }
    .report-badge.school-sanvicente { background: #E5E7EB; color: #374151; }

    .rank-num.turquoise-rank { background: #40E0D0; color: #111827; }
    .rank-num.violet-rank { background: #7C3AED; color: #ffffff; }
    .rank-num.brown-rank { background: #A16207; color: #ffffff; }

    /* PROGRESS BARS STYLING RULES */
    .bar-chart { display: flex; flex-direction: column; gap: 14px; }
    .bar-row { display: flex; align-items: center; gap: 12px; }
    .bar-label { font-size: 12px; color: var(--text); width: 90px; text-align: right; flex-shrink: 0; font-weight: 500; }
    .bar-track { flex: 1; background: var(--bg); border-radius: 4px; height: 10px; overflow: hidden; border: 1px solid var(--border); }
    .bar-fill { height: 100%; border-radius: 4px; transition: width 0.8s ease; }
    .bar-val { font-size: 12px; font-weight: 600; color: var(--text); width: 65px; text-align: right; flex-shrink: 0; }

    .legend { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 16px; }
    .legend-item { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--muted); }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

    /* RANK CODES ELEMENTS */
    .rank-list { display: flex; flex-direction: column; gap: 10px; }
    .rank-item { display: flex; align-items: center; gap: 12px; padding: 12px 14px; background: #ffffff; border-radius: 10px; border: 1px solid var(--border); }
    .rank-num { width: 24px; height: 24px; border-radius: 50%; background: var(--card2); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; color: var(--text); }
    .rank-num.gold-rank { background: var(--gold); color: white; }
    .rank-num.silver-rank { background: var(--card2); color: var(--text); }
    .rank-num.bronze-rank { background: #cd7f32; color: white; }
    .rank-name { flex: 1; font-size: 13px; color: var(--text); font-weight: 500; }
    .rank-val { font-size: 13px; color: var(--gold); font-weight: 600; }

    /* TIMELINE SYSTEM ACTIVITIES */
    .activity-list { display: flex; flex-direction: column; gap: 14px; }
    .activity-item { display: flex; align-items: flex-start; gap: 12px; }
    .activity-dot { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; color: white; }
    .activity-dot.blue { background: var(--accent); }
    .activity-dot.green { background: var(--success); }
    .activity-dot.gold { background: var(--gold); }
    .activity-text { font-size: 13px; line-height: 1.5; color: var(--text); }
    .activity-time { font-size: 11px; color: var(--muted); margin-top: 2px; }

    .recent-borrowings-list { display: flex; flex-direction: column; gap: 12px; }
    .recent-borrowing-item { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; padding: 16px 18px; border-radius: 14px; background: #f8fafc; border: 1px solid rgba(226, 232, 240, 0.8); }
    .recent-borrowing-book { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
    .recent-borrowing-meta { font-size: 12px; color: var(--muted); }
    .recent-borrowing-status { font-size: 13px; font-weight: 700; color: #111827; text-align: right; flex-shrink: 0; white-space: nowrap; }
    .empty-state { padding: 18px 12px; text-align: center; color: #64748b; border-radius: 12px; background: #f8fafc; }

    /* SEARCH CONTROLS WRAPPERS */
    .search-bar { display: flex; align-items: center; gap: 10px; background: #ffffff; border: 1px solid var(--border); border-radius: 8px; padding: 6px 12px; width: 280px; }
    .search-bar input { background: none; border: none; color: var(--text); font-family: 'Inter', sans-serif; font-size: 13px; outline: none; flex: 1; }

    .card input[type="text"], .card input[type="number"], .card input[type="date"], .card input[type="datetime-local"], .card select {
      width: 100%;
      background: #ffffff;
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 9px 12px;
      color: var(--text);
      font-family: 'Inter', sans-serif;
      font-size: 13px;
      outline: none;
    }

    /* ── CENTERED MODAL DESIGN LAYOUTS SHEET (CORRECTED FROM IMAGE_613A66) ── */
    .modal-overlay {
      display: none;
      position: fixed; inset: 0;
      background: rgba(31, 41, 55, 0.35);
      backdrop-filter: blur(2px);
      z-index: 200;
      align-items: center; justify-content: center;
    }
    .modal-overlay.open { display: flex !important; }

    .modal {
      background: var(--panel);
      border: 1px solid var(--border);
      border-radius: 16px;
      width: 620px;
      max-width: 95vw;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: var(--shadow-md);
      display: flex;
      flex-direction: column;
    }

    .modal-header {
      padding: 20px 24px;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
      background: var(--bg);
    }

    .modal-title { font-size: 16px; font-weight: 700; color: var(--text); }
    .modal-close { cursor: pointer; color: var(--muted); font-size: 22px; font-weight: bold; }
    .modal-body { padding: 24px; display: flex; flex-direction: column; gap: 18px; overflow-y: auto; }

    .form-group { display: flex; flex-direction: column; gap: 6px; width: 100%; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; width: 100%; }
    .form-group.full-width { grid-column: span 2; }

    .modal label { display: block; font-size: 11px; font-weight: 700; color: var(--text); text-transform: uppercase; letter-spacing: 0.5px; }

    .modal input[type="text"], .modal input[type="number"], .modal input[type="date"], .modal select, .modal textarea {
      width: 100% !important; background: #ffffff !important; border: 1px solid var(--border) !important; border-radius: 8px !important; padding: 10px 14px !important; color: var(--text) !important; font-size: 13.5px !important; outline: none; transition: all 0.2s;
    }
    .modal input:focus, .modal select:focus, .modal textarea:focus { border-color: var(--gold) !important; box-shadow: 0 0 0 3px rgba(179,143,41,0.1); }
    .modal textarea { resize: vertical; min-height: 80px; }

    /* FIXED DEDICATED CONTAINER FOR MODAL BUTTON PLACEMENTS */
    .modal-footer {
      padding: 16px 24px;
      border-top: 1px solid var(--border);
      display: flex; gap: 12px; justify-content: flex-end;
      background: var(--bg);
    }
    .modal .btn { padding: 10px 22px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: 1px solid var(--border); background: #ffffff; color: var(--text); transition: all 0.2s; }

    /* MODAL BUTTON COLOR CUSTOMIZATIONS MECHANICS */
    .modal .btn.cancel { background: #ffffff; border-color: var(--border); color: var(--text); }
    .modal .btn.cancel:hover { background: var(--card2); }
    .modal .btn.primary { background: #b38f29; border-color: #b38f29; color: white; }
    .modal .btn.primary:hover { background: #a67d22; border-color: #a67d22; }

    .report-selection-matrix { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px; }
    .matrix-item { display: flex; align-items: center; gap: 10px; background: var(--panel); padding: 12px; border-radius: 8px; border: 1px solid var(--border); cursor: pointer; }
    .matrix-item input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--gold); cursor: pointer; }
    .matrix-item span { font-weight: 500; font-size: 13px; color: var(--text); }

    .report-category-group { display: none; }
    .report-category-group.visible { display: block; }

    /* Operations summary */
    .operations-summary {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 12px;
      box-shadow: var(--shadow-sm);
      margin-bottom: 24px;
      padding: 18px;
    }
    .operations-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 16px;
    }
    .operations-title {
      color: var(--text);
      font-size: 17px;
      font-weight: 800;
      line-height: 1.2;
    }
    .operations-subtitle {
      color: var(--muted);
      font-size: 12px;
      margin-top: 4px;
    }
    .operations-command-bar {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: flex-end;
    }
    .operations-command {
      align-items: center;
      background: #f8fafc;
      border: 1px solid var(--border);
      border-radius: 8px;
      color: var(--text);
      cursor: pointer;
      display: inline-flex;
      font-family: 'Inter', sans-serif;
      font-size: 12px;
      font-weight: 700;
      min-height: 36px;
      padding: 0 12px;
      transition: all 0.18s ease;
      white-space: nowrap;
    }
    .operations-command:hover {
      background: #ecfdf5;
      border-color: var(--primary);
      color: var(--primary);
    }
    .operations-grid {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 12px;
    }
    .operations-item {
      background: #f8fafc;
      border: 1px solid var(--border);
      border-radius: 10px;
      color: inherit;
      display: flex;
      flex-direction: column;
      min-height: 118px;
      padding: 14px;
      text-decoration: none;
      transition: all 0.18s ease;
    }
    .operations-item:hover {
      border-color: var(--primary);
      box-shadow: 0 8px 22px rgba(15, 23, 42, 0.08);
      transform: translateY(-1px);
    }
    .operations-item strong {
      color: var(--text);
      font-size: 30px;
      font-weight: 800;
      line-height: 1;
      margin: 12px 0 8px;
    }
    .operations-item span:last-child {
      color: var(--muted);
      font-size: 12px;
      margin-top: auto;
    }
    .operations-item-label {
      color: var(--text);
      font-size: 12px;
      font-weight: 700;
      line-height: 1.35;
    }
    .operations-item.urgent { border-top: 3px solid #f59e0b; }
    .operations-item.danger { border-top: 3px solid #ef4444; }
    .operations-item.success { border-top: 3px solid #22c55e; }
    .operations-item.neutral { border-top: 3px solid #64748b; }

    /* ── COLLECTION OVERVIEW ── */
    .collection-stats {
      display: flex;
      gap: 24px;
      align-items: stretch;
    }
    .collection-total-card {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-width: 100px;
      padding: 16px 20px;
      background: #f8fafc;
      border-radius: 14px;
      border: 1px solid rgba(148,163,184,0.2);
    }
    .collection-total-num {
      font-size: 36px;
      font-weight: 800;
      color: var(--text);
      line-height: 1;
    }
    .collection-total-label {
      font-size: 11px;
      color: var(--muted);
      margin-top: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 600;
    }
    .collection-bars {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 14px;
      min-width: 0;
    }
    .collection-bar-item {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    .collection-bar-header {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .collection-bar-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      flex-shrink: 0;
    }
    .collection-bar-label {
      font-size: 12px;
      font-weight: 600;
      color: var(--text);
      flex: 1;
    }
    .collection-bar-val {
      font-size: 13px;
      font-weight: 700;
      color: var(--text);
      min-width: 30px;
      text-align: right;
    }
    .collection-bar-track {
      width: 100%;
      height: 6px;
      background: #e2e8f0;
      border-radius: 999px;
      overflow: hidden;
    }
    .collection-bar-fill {
      height: 100%;
      border-radius: inherit;
      transition: width 0.6s ease;
    }

    /* ── OVERDUE BOOKS ── */
    .overdue-count-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 24px;
      height: 24px;
      padding: 0 8px;
      border-radius: 999px;
      background: rgba(239,68,68,0.12);
      color: #dc2626;
      font-size: 12px;
      font-weight: 700;
    }
    .overdue-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 12px 0;
      border-bottom: 1px solid #f1f5f9;
    }
    .overdue-item:last-child { border-bottom: none; }
    .overdue-item-left { flex: 1; min-width: 0; }
    .overdue-borrower {
      font-size: 13px;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 2px;
    }
    .overdue-book-title {
      font-size: 12px;
      color: var(--muted);
      margin-bottom: 4px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .overdue-meta {
      display: flex;
      gap: 12px;
      align-items: center;
    }
    .overdue-days {
      font-size: 11px;
      font-weight: 700;
      color: #f59e0b;
    }
    .overdue-days.warning { color: #f97316; }
    .overdue-days.critical { color: #dc2626; }
    .overdue-due {
      font-size: 11px;
      color: var(--muted);
    }
    .overdue-view-btn {
      font-size: 11px;
      font-weight: 600;
      color: var(--primary);
      text-decoration: none;
      padding: 4px 10px;
      border-radius: 6px;
      border: 1px solid var(--primary);
      transition: all 0.15s ease;
      flex-shrink: 0;
    }
    .overdue-view-btn:hover {
      background: var(--primary);
      color: #fff;
    }
    .empty-state-success {
      text-align: center;
      padding: 24px 16px;
    }
    .empty-state-success .empty-state-icon {
      font-size: 32px;
      margin-bottom: 8px;
    }
    .empty-state-success .empty-state-title {
      font-size: 14px;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 4px;
    }
    .empty-state-success .empty-state-sub {
      font-size: 12px;
      color: var(--muted);
    }

    /* ── REDESIGNED ACTIVITY LIST ── */
    .activity-list-redesigned {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .activity-item-redesigned {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 10px 12px;
      border-radius: 10px;
      border: 1px solid #f1f5f9;
      transition: background 0.15s ease;
    }
    .activity-item-redesigned:hover {
      background: #f8fafc;
    }
    .activity-icon-circle {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      flex-shrink: 0;
    }
    .activity-body-redesigned {
      flex: 1;
      min-width: 0;
    }
    .activity-text-redesigned {
      font-size: 12.5px;
      line-height: 1.45;
      color: var(--text);
    }
    .activity-time-redesigned {
      font-size: 11px;
      color: var(--muted);
      margin-top: 3px;
    }

    /* ── BORROWING ACTIVITY CHART ── */
    .borrowing-activity-chart {
      display: flex;
      align-items: flex-end;
      gap: 6px;
      height: 140px;
      padding: 8px 0;
    }
    .ba-bar-col {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-end;
      gap: 4px;
      height: 100%;
    }
    .ba-bar-val {
      font-size: 10px;
      font-weight: 600;
      color: var(--muted);
      line-height: 1;
    }
    .ba-bar {
      width: 100%;
      max-width: 32px;
      border-radius: 4px 4px 0 0;
      background: #cbd5e1;
      transition: height 0.5s ease;
    }
    .ba-bar.current {
      background: var(--primary);
    }
    .ba-bar-label {
      font-size: 10px;
      font-weight: 600;
      color: var(--muted);
      text-transform: uppercase;
    }

    /* ── SYSTEM UTILITIES ── */
    .utilities-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
      margin-bottom: 24px;
    }
    .utilities-grid-wide {
      grid-template-columns: 1fr 1fr;
    }
    .utilities-grid > .card {
      min-width: 0;
    }
    .system-section-label {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: var(--muted);
      padding: 6px 0 12px 2px;
    }
    .system-form-heading {
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: var(--muted);
      margin-bottom: 16px;
      padding-bottom: 10px;
      border-bottom: 1px solid #f1f5f9;
    }

    /* ── FORM FIELDS ── */
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    .form-group label {
      font-size: 12px;
      font-weight: 600;
      color: var(--text);
      margin-bottom: 0;
    }
    .form-group input,
    .form-group select {
      height: 42px;
      padding: 10px 12px;
      border: 1px solid var(--border);
      border-radius: 10px;
      background: #fff;
      color: var(--text);
      font: 400 13.5px/1 'Inter', sans-serif;
      outline: none;
      transition: border-color 0.18s ease, box-shadow 0.18s ease;
    }
    .form-group input:focus,
    .form-group select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(47,158,143,0.1);
    }

    /* ── PASSWORD TOGGLE ── */
    .password-input-wrap {
      position: relative;
      display: flex;
      align-items: center;
    }
    .password-input-wrap input {
      flex: 1;
      padding-right: 44px;
    }
    .password-toggle {
      position: absolute;
      right: 0;
      top: 0;
      bottom: 0;
      width: 42px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: transparent;
      border: none;
      cursor: pointer;
      font-size: 18px;
      color: var(--muted);
      transition: color 0.15s ease;
      border-radius: 0 10px 10px 0;
    }
    .password-toggle:hover {
      color: var(--text);
    }
    .password-toggle:focus {
      outline: 2px solid var(--primary);
      outline-offset: -2px;
    }

    /* ── INLINE VALIDATION HINTS ── */
    .field-hint {
      font-size: 11px;
      font-weight: 600;
      margin-top: 4px;
      line-height: 1.3;
    }
    .field-hint.valid {
      color: #16a34a;
    }
    .field-hint.error {
      color: #dc2626;
    }
    .input-valid {
      border-color: #16a34a !important;
    }
    .input-error {
      border-color: #dc2626 !important;
    }

    /* ── CREATE USER BUTTON ── */
    .create-user-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 10px 22px;
      border-radius: 10px;
      border: 1px solid var(--primary);
      background: var(--primary);
      color: #fff;
      font-family: 'Inter', sans-serif;
      font-size: 13.5px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.18s ease;
    }
    .create-user-btn:hover {
      background: var(--primary-hover);
      border-color: var(--primary-hover);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(47,158,143,0.2);
    }
    .create-user-btn:active {
      transform: translateY(0);
    }
    .create-user-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }
    .btn-icon-left {
      font-size: 16px;
      font-weight: 700;
    }

    @media (max-width: 860px) {
      .utilities-grid,
      .utilities-grid-wide {
        grid-template-columns: 1fr;
      }
    }

    @media print {
      * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
      body { background: #ffffff !important; color: #1f2937 !important; }
      body * { visibility: hidden !important; }
      #panel-reports, #panel-reports * { visibility: visible !important; }
      #panel-reports { display: block !important; position: absolute !important; top: 0 !important; left: 0 !important; width: 100% !important; padding: 0 !important; margin: 0 !important; }
      .report-selector-box { display: none !important; }
      .report-category-group { display: none !important; }
      .report-category-group.visible { display: block !important; }
      .card { border: 1px solid #d1d5db !important; background: #ffffff !important; box-shadow: none !important; }
      .card, .card * { color: #1f2937 !important; }
    }
  </style>

  <div class="content">
    <?php if(session('success')): ?>
      <div style="background:#e4f4eb; color:var(--success); border:1px solid #b8dec9; padding:12px 16px; border-radius:8px; margin-bottom:18px; font-weight:700;">
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
      <div style="background:#f8e5e1; color:var(--error); border:1px solid #efb8ae; padding:12px 16px; border-radius:8px; margin-bottom:18px; font-weight:700;">
        <?php echo e($errors->first()); ?>

      </div>
    <?php endif; ?>

    <div id="panel-verify" class="panel">
      <div class="card">
        <div class="card-header"><div class="card-title">Pending Account Verifications</div><div class="card-sub">Student, researcher, and visitor accounts waiting for approval</div></div>
        <div class="card-body" style="padding:0;">
          <table>
            <thead><tr><th>Name</th><th>Barcode ID</th><th>School</th><th>Role</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $pendingUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pendingUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($pendingUser->name); ?><br><span class="text-muted"><?php echo e($pendingUser->email); ?></span></td>
                  <td><?php echo e($pendingUser->barcode_id ?? 'Unassigned'); ?></td>
                  <td><?php echo e(strtolower($pendingUser->role) === 'visitor' ? '' : ($pendingUser->school ?: 'N/A')); ?></td>
                  <td><?php echo e(ucfirst($pendingUser->role)); ?></td>
                  <td><span class="status-chip pending">Pending</span></td>
                  <td>
                    <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                      <form method="POST" action="<?php echo e(route('admin.verify-researcher')); ?>" class="verify-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="lookup" value="<?php echo e($pendingUser->user_id); ?>">
                        <button class="btn-sm gold verify-btn" type="submit" data-original-text="Verify">Verify</button>
                      </form>
                      <form method="POST" action="<?php echo e(route('admin.reject-researcher')); ?>" class="reject-form" style="display:flex; flex-direction:column; gap:8px; max-width:280px; width:100%;">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="lookup" value="<?php echo e($pendingUser->user_id); ?>">
                        <textarea name="rejection_reason" placeholder="Reason for rejection" required style="width:100%; min-height:72px; padding:10px; border:1px solid #d1d5db; border-radius:10px; font-size:0.95rem; resize:vertical;"></textarea>
                        <button class="btn-sm danger reject-btn" type="submit">Reject</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" style="text-align:center;">No pending accounts to verify.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card" style="margin-top:20px;">
        <div class="card-header"><div class="card-title">Rejected Account Verifications</div><div class="card-sub">Rejected accounts and their rejection reasons</div></div>
        <div class="card-body" style="padding:0;">
          <table>
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Rejection Reason</th><th>Rejected At</th></tr></thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $rejectedUsers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rejectedUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($rejectedUser->name); ?></td>
                  <td><?php echo e($rejectedUser->email); ?></td>
                  <td><?php echo e(ucfirst($rejectedUser->role)); ?></td>
                  <td><?php echo e($rejectedUser->rejection_reason ?? 'No reason provided.'); ?></td>
                  <td><?php echo e($rejectedUser->updated_at ? \Carbon\Carbon::parse($rejectedUser->updated_at)->format('M d, Y h:i A') : 'N/A'); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" style="text-align:center;">No rejected accounts yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div id="panel-verified" class="panel">
      <div class="card">
        <div class="card-header"><div class="card-title">Verified Accounts</div><div class="card-sub">Approved accounts with active barcodes</div></div>
        <div class="card-body" style="padding:0;">
          <table class="verified-accounts-table">
            <thead><tr><th>User ID</th><th>Name</th><th>Barcode</th><th>Role</th><th>Verified At</th><th>Action</th></tr></thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $verifiedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $verifiedUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($verifiedUser->user_id); ?></td>
                  <td><?php echo e($verifiedUser->name); ?><br><span class="text-muted"><?php echo e($verifiedUser->email); ?></span></td>
                  <td>
                    <?php if($verifiedUser->barcode_id): ?>
                      <div style="display:flex; flex-direction:column; align-items:center; gap:6px; max-width:170px;">
                        <svg class="barcode-svg" data-code="<?php echo e($verifiedUser->barcode_id); ?>" aria-hidden="true" style="width:160px; height:45px;"></svg>
                        <div style="font-size:0.78rem; color:#334155; text-align:center; word-break:break-all;"><?php echo e($verifiedUser->barcode_id); ?></div>
                      </div>
                    <?php else: ?>
                      <span class="text-muted">Unassigned</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo e(ucfirst($verifiedUser->role)); ?> </
                  <td><?php echo e($verifiedUser->updated_at ? \Carbon\Carbon::parse($verifiedUser->updated_at)->format('M d, Y h:i A') : 'N/A'); ?></td>
                  <td>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                      <?php if(empty($verifiedUser->barcode_id)): ?>
                        <form method="POST" action="<?php echo e(route('admin.generate-barcode')); ?>">
                          <?php echo csrf_field(); ?>
                          <input type="hidden" name="user_id" value="<?php echo e($verifiedUser->user_id); ?>">
                          <button class="btn-sm" type="submit">Generate Barcode</button>
                        </form>
                      <?php else: ?>
                        <form method="POST" action="<?php echo e(route('admin.assign-barcode')); ?>" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                          <?php echo csrf_field(); ?>
                          <input type="hidden" name="user_id" value="<?php echo e($verifiedUser->user_id); ?>">
                          <select name="barcode_id" style="min-width:160px;">
                            <option value="">Reassign barcode</option>
                            <?php $__currentLoopData = $availableBarcodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $barcode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <option value="<?php echo e($barcode->barcode_id); ?>"><?php echo e($barcode->barcode_id); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                          </select>
                          <button class="btn-sm" type="submit">Assign</button>
                        </form>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" style="text-align:center;">No verified accounts yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div id="panel-barcodes" class="panel">
      <div class="section-header">
        <div><div class="section-title">Barcode Pool</div><div class="section-sub"></div></div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:0;gap:8px;">
          <table>
            <thead><tr><th>Barcode ID</th><th>Assigned User</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $availableBarcodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $barcode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><strong><?php echo e($barcode->barcode_id); ?></strong></td>
                  <td><?php echo e($barcode->assigned_user_id ?? 'Unassigned'); ?></td>
                  <td><span class="status-chip returned">Available</span></td>
                  <td>
                    <form method="POST" action="<?php echo e(route('admin.assign-barcode')); ?>" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                      <?php echo csrf_field(); ?>
                      <input type="hidden" name="barcode_id" value="<?php echo e($barcode->barcode_id); ?>">
                      <input type="text" name="user_id" placeholder="User ID" required style="width:160px;">
                      <button class="btn-sm gold" type="submit">Assign</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" style="text-align:center;">No available barcodes yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <?php
      $userRows = \Illuminate\Support\Facades\Schema::hasTable('users')
        ? \Illuminate\Support\Facades\DB::table('users')->latest('created_at')->get()
        : collect();
      $fineSummary = \Illuminate\Support\Facades\Schema::hasTable('financial_transactions')
        ? \Illuminate\Support\Facades\DB::table('financial_transactions')
            ->select('user_id',
                \Illuminate\Support\Facades\DB::raw("SUM(CASE WHEN status = 'Unpaid' THEN amount ELSE 0 END) as unpaid_total"),
                \Illuminate\Support\Facades\DB::raw("SUM(CASE WHEN status = 'Paid' THEN amount ELSE 0 END) as paid_total")
            )
            ->where('transaction_type', 'Fine')
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id')
        : collect();
      $entryLogs = \Illuminate\Support\Facades\Schema::hasTable('entry_logs')
        ? \Illuminate\Support\Facades\DB::table('entry_logs')
            ->leftJoin('members', 'entry_logs.member_id', '=', 'members.id')
            ->select('entry_logs.*', 'members.full_name', 'members.barcode_id')
            ->latest('entry_logs.entry_time')
            ->limit(25)
            ->get()
        : collect();
      $displayValue = function ($value, string $fallback = 'Not provided') {
        $clean = trim((string) ($value ?? ''));
        $lower = strtolower($clean);
        return $clean === '' || $lower === 'null' || str_starts_with($lower, 'null@') ? $fallback : $clean;
      };
    ?>
    <div id="panel-users" class="panel">
      <div class="section-header">
        <div>
          <div class="section-title">User Records</div>
          <div class="section-sub">Manage registered patrons and account information.</div>
        </div>
      </div>
      <div class="card user-records-card">
        <div class="card-header user-records-header">
          <div>
            <div class="card-title">Registered Patrons</div>
            <div class="card-sub">View and manage registered student and researcher accounts.</div>
          </div>
        </div>
        <div class="card-body user-records-body">
          <div class="table-responsive user-records-table-wrap">
          <table class="user-records-table">
            <thead><tr><th>ID</th><th>Name</th><th>Role</th><th>Email</th><th>Status</th><th>Rejection Reason</th><th>Fines</th><th class="actions-col">Action</th></tr></thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $userRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                  $fineRow = $fineSummary->get($userRow->user_id);
                  $unpaid = $fineRow ? (float) $fineRow->unpaid_total : 0;
                  $status = strtolower($displayValue($userRow->status ?? null, 'approved'));
                  $statusLabel = ucfirst($status);
                  $statusClass = match($status) {
                    'approved' => 'approved',
                    'pending' => 'pending',
                    'rejected' => 'rejected',
                    'suspended' => 'suspended',
                    default => 'neutral',
                  };
                  $roleLabel = ucfirst($displayValue($userRow->role ?? null, 'student'));
                ?>
                <tr>
                  <td class="user-record-id"><?php echo e($displayValue($userRow->user_id ?? null, 'N/A')); ?></td>
                  <td class="user-record-name"><strong><?php echo e($displayValue($userRow->name ?? null, 'Unnamed User')); ?></strong></td>
                  <td><span class="status-chip borrowed"><?php echo e($roleLabel); ?></span></td>
                  <td><?php echo e($displayValue($userRow->email ?? null, 'Not provided')); ?></td>
                  <td><span class="status-chip <?php echo e($statusClass); ?>"><?php echo e($statusLabel); ?></span></td>
                  <td class="user-record-muted"><?php echo e($status === 'rejected' ? $displayValue($userRow->rejection_reason ?? null, 'No reason provided.') : '-'); ?></td>
                  <td>
                    <?php if($unpaid > 0): ?>
                      <span class="status-chip overdue">PHP <?php echo e(number_format($unpaid, 2)); ?> Due</span>
                    <?php else: ?>
                      <span class="status-chip returned">Clear</span>
                    <?php endif; ?>
                  </td>
                  <td class="user-record-actions-cell">
                    <div class="table-actions">
                    <a class="table-action-btn edit" href="<?php echo e(route('admin.users.edit', $userRow->id)); ?>"><?php if (isset($component)) { $__componentOriginal32022bdceaa704d305484041fc21cb4a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32022bdceaa704d305484041fc21cb4a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.edit','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.edit'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal32022bdceaa704d305484041fc21cb4a)): ?>
<?php $attributes = $__attributesOriginal32022bdceaa704d305484041fc21cb4a; ?>
<?php unset($__attributesOriginal32022bdceaa704d305484041fc21cb4a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal32022bdceaa704d305484041fc21cb4a)): ?>
<?php $component = $__componentOriginal32022bdceaa704d305484041fc21cb4a; ?>
<?php unset($__componentOriginal32022bdceaa704d305484041fc21cb4a); ?>
<?php endif; ?> <span>Edit</span></a>
                    <form method="POST" action="<?php echo e(route('admin.users.delete', $userRow->id)); ?>">
                      <?php echo csrf_field(); ?>
                      <button class="table-action-btn delete" type="submit" onclick="return confirm('Are you sure you want to delete this user?')"><?php if (isset($component)) { $__componentOriginal54951bafeaab9df77e16fbbcb64bac40 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal54951bafeaab9df77e16fbbcb64bac40 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.trash','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.trash'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal54951bafeaab9df77e16fbbcb64bac40)): ?>
<?php $attributes = $__attributesOriginal54951bafeaab9df77e16fbbcb64bac40; ?>
<?php unset($__attributesOriginal54951bafeaab9df77e16fbbcb64bac40); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal54951bafeaab9df77e16fbbcb64bac40)): ?>
<?php $component = $__componentOriginal54951bafeaab9df77e16fbbcb64bac40; ?>
<?php unset($__componentOriginal54951bafeaab9df77e16fbbcb64bac40); ?>
<?php endif; ?> <span>Delete</span></button>
                    </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="user-record-empty">No registered patrons yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title">Entry Log History</div></div>
        <div class="card-body" style="padding:0;">
          <table>
            <thead><tr><th>User Profile</th><th>Account ID</th><th>Role Type</th><th>Time In</th><th>Time Out</th><th>Date</th></tr></thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $entryLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($log->full_name ?? 'Unknown Member'); ?></td>
                  <td><?php echo e($log->barcode_id ?? 'N/A'); ?></td>
                  <td>Member</td>
                  <td><?php echo e(\Carbon\Carbon::parse($log->entry_time)->format('h:i A')); ?></td>
                  <td><?php echo e($log->exit_time ? \Carbon\Carbon::parse($log->exit_time)->format('h:i A') : 'Active'); ?></td>
                  <td><?php echo e(\Carbon\Carbon::parse($log->entry_time)->format('M d, Y')); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" style="text-align:center;">No entry logs recorded yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card" style="margin-top:24px;">
        <div class="card-header"><div class="card-title">Library Clearance Certificate</div></div>
        <div class="card-body">
          <form action="<?php echo e(url('/admin/certificates/generate')); ?>" method="POST" style="display:flex; gap:12px; align-items:end;">
            <?php echo csrf_field(); ?>
            <div class="form-group" style="margin:0; flex:1;"><label>Patron / Student ID</label><input type="text" name="user_id" placeholder="STU-0000 or barcode" required></div>
            <button class="btn-sm gold" type="submit">Check & Generate</button>
          </form>
        </div>
      </div>
    </div>

    <div id="panel-borrowing" class="panel">
      <div class="section-header">
        <div><div class="section-title">Borrowing & Returns</div><div class="section-sub"></div></div>
      </div>
      <div class="card" style="margin-bottom:18px;gap:8px;">
        <div class="card-header"><div class="card-title">Barcode Borrowing Console</div></div>
        <div class="card-body">
          <form action="<?php echo e(route('admin.borrow-by-barcode')); ?>" method="POST" style="display:grid; gap:16px;">
            <?php echo csrf_field(); ?>
            <div style="padding:18px; border:1px dashed #0f766e; border-radius:16px; background:#f3faf7;">
              <label style="display:block; margin-bottom:8px; font-size:0.85rem; letter-spacing:0.08em; text-transform:uppercase; color:#0f766e;">Scan Borrower Library ID Barcode</label>
              <input type="text" name="barcode_id" placeholder="202600123" autofocus required style="width:100%; padding:18px 16px; font-size:18px; border:2px solid #0f766e; border-radius:14px; background:#ffffff;" />
              <p style="margin:10px 0 0; color:#475569; font-size:0.95rem;">This barcode field is for the borrower only. Enter the book reference below.</p>
            </div>
            <div style="padding:18px; border:1px dashed #0f766e; border-radius:16px; background:#f3faf7;">
              <label style="display:block; margin-bottom:8px; font-size:0.85rem; letter-spacing:0.08em; text-transform:uppercase; color:#0f766e;">ISBN / Accession / Title / Barcode</label>
              <input type="text" name="book_reference" placeholder="ISBN / Accession / Title / Barcode" required style="width:100%; padding:18px 16px; font-size:18px; border:2px solid #0f766e; border-radius:14px; background:#ffffff;" />
              <p style="margin:10px 0 0; color:#475569; font-size:0.95rem;">Enter the book identifier here after scanning the borrower barcode.</p>
            </div>
            <button class="btn-sm gold" type="submit" style="height:40px; width:160px;">Record Borrow</button>
          </form>
        </div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:0;">
          <table id="borrowingLifecycleTable">
            <thead><tr><th>Transaction ID</th><th>Student / Patron</th><th>Book Title</th><th>Borrow Date</th><th>Due Date</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
              <?php if(\Illuminate\Support\Facades\Schema::hasTable('borrowing_transactions')): ?>
                <?php $__empty_1 = true; $__currentLoopData = \Illuminate\Support\Facades\DB::table('borrowing_transactions')->leftJoin('books', 'borrowing_transactions.book_id', '=', 'books.id')->select('borrowing_transactions.*', 'books.title')->latest('borrowing_transactions.created_at')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr>
                    <td>TXN-<?php echo e(str_pad($borrow->id, 4, '0', STR_PAD_LEFT)); ?></td>
                    <td><?php echo e($borrow->barcode_id); ?></td>
                    <td><?php echo e($borrow->title ?? $borrow->book_barcode); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($borrow->borrow_date)->format('M d, Y')); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($borrow->due_date)->format('M d, Y')); ?></td>
                    <td><span class="status-chip <?php echo e($borrow->status === 'Returned' ? 'returned' : 'borrowed'); ?>"><?php echo e($borrow->status); ?></span></td>
                    <td>
                      <?php if($borrow->status !== 'Returned'): ?>
                        <div style="display:grid; gap:8px;">
                          <form action="<?php echo e(route('admin.borrowings.return', $borrow->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button class="btn-sm success" type="submit">Return</button>
                          </form>
                          <?php if(\Carbon\Carbon::parse($borrow->due_date)->isPast()): ?>
                            <form action="<?php echo e(route('admin.borrowings.overdue', $borrow->id)); ?>" method="POST">
                              <?php echo csrf_field(); ?>
                              <button class="btn-sm danger" type="submit">Mark Overdue</button>
                            </form>
                          <?php endif; ?>
                        </div>
                      <?php else: ?>
                        <span class="status-chip returned">Closed</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr><td colspan="7" style="text-align:center;">No barcode borrowing transactions yet.</td></tr>
                <?php endif; ?>
              <?php else: ?>
                <tr><td colspan="7" style="text-align:center;">Run migrations to enable barcode borrowing.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div id="panel-fines" class="panel">
     <?php
    $financialRows = \Illuminate\Support\Facades\Schema::hasTable('financial_transactions')
      ? collect(\Illuminate\Support\Facades\DB::table('financial_transactions')->latest('transaction_date')->get())
      : collect();

        // Resolve patron display names and sanitize description/amount for admin view (display-only)

    $financialRows = $financialRows->map(function ($row) {
      $resolved = $row->patron_name ?? null;

      if (empty($resolved) && ! empty($row->user_id)) {
        $user = \App\Models\User::where('user_id', $row->user_id)
          ->orWhere('email', $row->user_id)
          ->orWhere('barcode_id', $row->user_id)
          ->first();
             if ($user) {
          $resolved = $user->name;
        } else {
          $member = \Illuminate\Support\Facades\DB::table('members')
            ->where('barcode_id', $row->user_id)
            ->orWhere('assigned_user_id', $row->user_id)
            ->first();

          if ($member) {
            $resolved = $member->full_name;
          }
        }
      }
           $row->patron_name = $resolved;

      if (! empty($row->description)) {
        $row->description = preg_replace('/\([^)]*day\/s\)/i', '', $row->description);
        $row->description = trim(preg_replace('/\s+/', ' ', $row->description));
      }

      if (isset($row->amount)) {
        $row->amount = (isset($row->transaction_type) && $row->transaction_type === 'Fine')
          ? max(20, round((float) $row->amount, 2))
          : round((float) $row->amount, 2);
      } else {
        $row->amount = 0;
      }

      return $row;
    });
        $fineRows = $financialRows->where('transaction_type', 'Fine');
    $collectedFines = $fineRows->where('status', 'Paid')->sum('amount');
    $unpaidFines = $fineRows->where('status', 'Unpaid')->sum('amount');

        $fundRows = $financialRows->filter(function ($r) {
          return in_array($r->transaction_type, ['Fund', 'Fund Income', 'Fund Expense']);
        });
        $fundIncome = $fundRows->filter(function ($r) {
          return in_array($r->transaction_type, ['Fund', 'Fund Income']) && in_array($r->status, ['Paid', 'Available']);
        })->sum('amount');
        $fundExpense = $fundRows->filter(function ($r) {
          return $r->transaction_type === 'Fund Expense' || ($r->transaction_type === 'Fund' && $r->status === 'Expenses');
        })->sum('amount');
        $availableFunds = $fundIncome -$fundExpense;
        $fundUtilization = $fundIncome > 0 ? round(($fundExpense / $fundIncome) * 100, 1) : 0;
      ?>

      <div class="section-header">
    <div>
      <div class="section-title" style="gap:10px;">Fines</div>
      <div class="section-sub"></div>
    </div>
  </div>
      <div class="stats-grid" style="margin-top: 5px">
    <div class="stat-card green">
      <div class="stat-label">Collected Fines</div>
      <div class="stat-value">PHP <?php echo e(number_format($collectedFines, 2)); ?></div>
      <div class="stat-change">Paid fine transactions</div>
    </div>
    <div class="stat-card red">
      <div class="stat-label">Unpaid Fines</div>
      <div class="stat-value">PHP <?php echo e(number_format($unpaidFines, 2)); ?></div>
      <div class="stat-change">Outstanding balance</div>
    </div>
  </div>
 <div class="card" style="margin-bottom:5px; margin-top:10px">
    <div class="card-header">
      <div class="card-title">Manage Fines</div>
    </div>
    <div class="card-body">
      <form id="fines-financial-form" action="<?php echo e(route('admin.financial-transactions.store')); ?>" method="POST" style="display:grid; grid-template-columns: repeat(4, 1fr); gap:8px;">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="transaction_id" id="financial-transaction-id-fines" value="">
        <input type="hidden" name="change_amount" id="fines-change-amount-hidden" value="0">

            <div class="form-group">
          <label>Transaction Type</label>
          <select name="transaction_type" required>
            <option value="Fine">Fine</option>
          </select>
        </div>

        <div class="form-group">
          <label>Patron Name / User ID</label>
          <input type="text" name="user_id" placeholder="Enter patron name, barcode, or ID">
        </div>
            <!-- Change display and cashier confirmation -->
            <div class="form-group">
          <label>Status</label>
          <select name="status" required>
            <option value="Unpaid">Select</option>
            <option value="Paid">Paid</option>
          </select>
        </div>

        <div class="form-group">
          <label>Fine Amount</label>
          <input type="number" name="amount" step="0.01" min="0" required>
        </div>

        <div class="form-group">
          <label>Cash Received</label>
          <input type="number" name="received_amount" step="0.01" min="0" placeholder="Optional for payment">
        </div>
            <div class="form-group" id="admin-fines-change-confirm" style="display:none; grid-column: span 2;">
          <label style="display:auto; align-items:center; gap:5px;">
            <input type="checkbox" id="admin-fines-change-confirm-checkbox" name="change_confirmed" value="1" required>
            I returned PHP <strong><span id="admin-fines-change-amount-confirm">0.00</span></strong> to the patron
          </label>
        </div>
<div class="form-group" style="grid-column: span 2;">
          <label>Notes / Reason</label>
          <input type="text" name="description" placeholder="Enter fine reason" required>
        </div>

        <div class="form-group">
          <label>Notify Patron</label>
          <label class="checkbox-inline">
            <input type="checkbox" name="send_notification" value="1"> Send notification
          </label>
        </div>
 <div class="form-group">
          <label>Date</label>
          <input type="date" name="transaction_date" value="<?php echo e(now()->toDateString()); ?>" required>
        </div>

        <div style="grid-column:1 / -1; text-align:right;">
          <button class="btn-sm gold" type="submit">Save Fine</button>
        </div>
      </form>


          <script>
             (function () {
          const form = document.getElementById('fines-financial-form');
          if (!form) return;

          const amountInput = form.querySelector('[name="amount"]');
          const receivedInput = form.querySelector('[name="received_amount"]');
          const statusInput = form.querySelector('[name="status"]');
          const changeHidden = document.getElementById('fines-change-amount-hidden');
          const changeAmountSpan = document.getElementById('admin-fines-change-amount');
          const changeConfirmWrap = document.getElementById('admin-fines-change-confirm');
          const changeConfirmCheckbox = document.getElementById('admin-fines-change-confirm-checkbox');
          const changeConfirmAmountSpan = document.getElementById('admin-fines-change-amount-confirm');


                function fmt(n) {
            return Number(n).toFixed(2);
          }

               function syncFinePaymentState() {
            const amount = parseFloat(amountInput?.value || 0) || 0;
            const received = parseFloat(receivedInput?.value || 0) || 0;
            const change = Math.max(received - amount, 0);
            const computedStatus = (received >= amount && amount > 0) ? 'Paid' : 'Unpaid';

  if (statusInput) {
              statusInput.value = computedStatus;
            }

            if (changeHidden) {
              changeHidden.value = fmt(change);
            }

            if (changeAmountSpan) {
              changeAmountSpan.textContent = fmt(change);
            }

            if (changeConfirmAmountSpan) {
              changeConfirmAmountSpan.textContent = fmt(change);
            }
 if (change > 0) {
              if (changeConfirmWrap) {
                changeConfirmWrap.style.display = '';
              }
              if (changeConfirmCheckbox) {
                changeConfirmCheckbox.required = true;
              }
            } else {
              if (changeConfirmWrap) {
                changeConfirmWrap.style.display = 'none';
              }
              if (changeConfirmCheckbox) {
                changeConfirmCheckbox.required = false;
                changeConfirmCheckbox.checked = false;
              }
            }
          }
 amountInput?.addEventListener('input', syncFinePaymentState);
          receivedInput?.addEventListener('input', syncFinePaymentState);
          syncFinePaymentState();
        })();
      </script>
    </div>
  </div>

      <div class="card">
        <div class="card-header"><div class="card-title">Fine Transaction History</div><button class="btn-sm" onclick="window.print()">Print Report</button></div>
        <div class="card-body" style="padding:0;">
          <table>
            <thead><tr><th>Type</th><th>Patron</th><th>Description</th><th>Amount</th><th>Status</th><th>Date</th><!--<th>Action</th></tr></thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $fineRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($row->transaction_type); ?></td>
                  <td>
                    <?php $adminDisplay = $row->patron_name ?? null; ?>
                    <?php if(! empty($adminDisplay)): ?>
                      <strong><?php echo e($adminDisplay); ?></strong>
                      <?php if(! empty($row->user_id)): ?><br><small class="text-muted"><?php echo e($row->user_id); ?></small><?php endif; ?>
                    <?php else: ?>
                      <?php echo e($row->user_id ?? 'Library Fund'); ?>

                    <?php endif; ?>
                  </td>
                  <td><?php echo e($row->description); ?></td>
                  <td style="font-weight:700;">PHP <?php echo e(number_format($row->amount, 2)); ?></td>
                  <td>
                    <?php if($row->transaction_type === 'Fine'): ?>
                      <div>Received: PHP <?php echo e(number_format((float) ($row->received_amount ?? 0), 2)); ?></div>
                      <div>Change: PHP <?php echo e(number_format((float) ($row->change_amount ?? 0), 2)); ?></div>
                    <?php else: ?>
                      <span class="status-chip <?php echo e($row->status === 'Paid' ? 'returned' : 'pending'); ?>"><?php echo e($row->status); ?></span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo e(\Carbon\Carbon::parse($row->transaction_date)->format('M d, Y')); ?></td>
                  <td style="white-space:nowrap;">
                    <!--<button class="btn-sm" type="button" onclick="populateFinancialForm('fines', <?php echo e($row->id); ?>, '<?php echo e(addslashes($row->transaction_type)); ?>', '<?php echo e(addslashes($row->user_id ?? '')); ?>', '<?php echo e(addslashes($row->patron_name ?? '')); ?>', '<?php echo e(addslashes($row->description ?? '')); ?>', '<?php echo e($row->amount); ?>', '<?php echo e($row->status); ?>', '<?php echo e(\Carbon\Carbon::parse($row->transaction_date)->toDateString()); ?>')">Edit</button>
                    <form method="POST" action="<?php echo e(route('admin.financial-transactions.archive', $row->id)); ?>" style="display:inline-block; margin-left:6px;">
                      <?php echo csrf_field(); ?>
                      <button class="btn-sm danger" type="submit" onclick="return confirm('Archive this transaction?')">Archive</button>
                    </form>-->
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" style="text-align:center;">No fine transactions yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div id="panel-funds" class="panel">
      <div class="section-header"><div><div class="section-title" style="gap:8px;">Funds</div><div class="section-sub"></div></div></div>
      <div class="stats-grid">
      <!-- <div class="stat-card gold"><div class="stat-label">Fund Income</div><div class="stat-value">PHP <?php echo e(number_format($fundIncome, 2)); ?></div><div class="stat-change">Total fund income</div></div> -->
        <div class="stat-card blue"><div class="stat-label">Fund Expenses</div><div class="stat-value">PHP <?php echo e(number_format($fundExpense, 2)); ?></div><div class="stat-change">Total fund expenses</div></div>
    <div class="stat-card green"><div class="stat-label"> Available Funds </div><div class="stat-value">PHP <?php echo e(number_format($availableFunds, 2)); ?></div><div class="stat-change">Income minus expenses</div></div>
        <!--<div class="stat-card purple"><div class="stat-label">Fund Utilization</div><div class="stat-value"><?php echo e($fundUtilization); ?>%</div><div class="stat-change">Expense vs income</div></div>-->
      </div>
      <div class="card" style="margin-bottom:18px;">
        <div class="card-header"><div class="card-title">Manage Funds</div></div>
        <div class="card-body">
          <form id="funds-financial-form" action="<?php echo e(route('admin.financial-transactions.store')); ?>" method="POST" style="display:grid; grid-template-columns: repeat(4, 1fr); gap:12px;">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="transaction_id" id="financial-transaction-id-funds" value="">
            <div class="form-group"><label>Transaction Type</label><select name="transaction_type" required><option value="Fund">Research Fund</option><option value="Fund Expense">Fund Expense</option></select></div>
            <div class="form-group"><label>Patron Name / User ID</label><input type="text" name="user_id" placeholder="Enter patron name, barcode, or ID"></div>
            <div class="form-group"><label>Status</label><select name="status" required><option>Select</option><option>Paid</option><option>Expenses</option></select></div>
            <div class="form-group"><label>Amount</label><input type="number" name="amount" step="0.01" min="0" required></div>
            <div class="form-group" style="grid-column: span 2;"><label>Notes / Reason</label><input type="text" name="description" placeholder="Enter fund details" required></div>
            <div class="form-group"><label>Notify Patron</label><label class="checkbox-inline"><input type="checkbox" name="send_notification" value="1"> Send notification</label></div>
            <div class="form-group"><label>Date</label><input type="date" name="transaction_date" value="<?php echo e(now()->toDateString()); ?>" required></div>
            <div style="grid-column:1 / -1; text-align:right;"><button class="btn-sm gold" type="submit">Save Fund</button></div>
          </form>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title">Fund Transaction History</div><button class="btn-sm" onclick="window.print()">Print Report</button></div>
        <div class="card-body" style="padding:0;">
          <table>
            <thead><tr><th>Type</th><th>Patron</th><th>Description</th><th>Amount</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $fundRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($row->transaction_type); ?></td>
                  <td>
                    <?php $adminDisplay = $row->patron_name ?? null; ?>
                    <?php if(! empty($adminDisplay)): ?>
                      <strong><?php echo e($adminDisplay); ?></strong>
                      <?php if(! empty($row->user_id)): ?><br><small class="text-muted"><?php echo e($row->user_id); ?></small><?php endif; ?>
                    <?php else: ?>
                      <?php echo e($row->user_id ?? 'Library Fund'); ?>

                    <?php endif; ?>
                  </td>
                  <td><?php echo e($row->description); ?></td>
                  <td style="font-weight:700;">PHP <?php echo e(number_format($row->amount, 2)); ?></td>
                  <td><span class="status-chip <?php echo e(in_array($row->status, ['Paid','Available']) ? 'returned' : 'pending'); ?>"><?php echo e($row->status); ?></span></td>
                  <td><?php echo e(\Carbon\Carbon::parse($row->transaction_date)->format('M d, Y')); ?></td>
                  <td style="white-space:nowrap;">
                    <button class="btn-sm" type="button" onclick="populateFinancialForm('funds', <?php echo e($row->id); ?>, '<?php echo e(addslashes($row->transaction_type)); ?>', '<?php echo e(addslashes($row->user_id ?? '')); ?>', '<?php echo e(addslashes($row->patron_name ?? '')); ?>', '<?php echo e(addslashes($row->description ?? '')); ?>', '<?php echo e($row->amount); ?>', '<?php echo e($row->status); ?>', '<?php echo e(\Carbon\Carbon::parse($row->transaction_date)->toDateString()); ?>')">Edit</button>
                    <form method="POST" action="<?php echo e(route('admin.financial-transactions.archive', $row->id)); ?>" style="display:inline-block; margin-left:6px;">
                      <?php echo csrf_field(); ?>
                      <button class="btn-sm danger" type="submit" onclick="return confirm('Archive this transaction?')">Archive</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" style="text-align:center;">No fund transactions yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <script>
      // Populate the financial form for fines or funds and navigate to the appropriate panel
      window.populateFinancialForm = function () {
        const args = Array.from(arguments);
        let type = null, id = null, transaction_type = null, userId = '', patronName = '', description = '', amount = '', status = '', date = '';

        if (typeof args[0] === 'string' && (args[0] === 'funds' || args[0] === 'fines')) {
          // new signature: (type, id, transaction_type, userId, patronName, description, amount, status, date)
          type = args[0]; id = args[1]; transaction_type = args[2]; userId = args[3]; patronName = args[4]; description = args[5]; amount = args[6]; status = args[7]; date = args[8];
        } else {
          // legacy signature: (id, transaction_type, userId, patronName, description, amount, status, date)
          id = args[0]; transaction_type = args[1]; userId = args[2]; patronName = args[3]; description = args[4]; amount = args[5]; status = args[6]; date = args[7];
          // infer type from transaction_type when possible
          if (transaction_type && String(transaction_type).toLowerCase().includes('fund')) type = 'funds'; else type = 'fines';
        }

        const formId = type === 'funds' ? 'funds-financial-form' : 'fines-financial-form';
        const hiddenId = type === 'funds' ? 'financial-transaction-id-funds' : 'financial-transaction-id-fines';
        const form = document.getElementById(formId);

        // If form not present, still try to switch panel so user can see Manage Funds
        if (window.showPanel) window.showPanel(type || 'funds');
        if (!form) return;

        // set hidden transaction id
        const hid = document.getElementById(hiddenId);
        if (hid) hid.value = id || '';

        // transaction type mapping for UX
        const txTypeField = form.querySelector('[name="transaction_type"]');
        if (txTypeField) {
          const mapped = (transaction_type && String(transaction_type).toLowerCase().includes('fund')) ? 'Fund' : (transaction_type || txTypeField.value || txTypeField.options?.[0]?.value);
          try { txTypeField.value = mapped; } catch (e) { /* ignore */ }
        }

        const userField = form.querySelector('[name="user_id"]');
        if (userField) userField.value = patronName || userId || '';

        const descField = form.querySelector('[name="description"]');
        if (descField) descField.value = description || '';

        const amountField = form.querySelector('[name="amount"]');
        if (amountField) amountField.value = (amount !== undefined && amount !== null) ? amount : '';

        const statusField = form.querySelector('[name="status"]');
        if (statusField) statusField.value = status || '';

        const dateField = form.querySelector('[name="transaction_date"]');
        if (dateField) dateField.value = date || new Date().toISOString().slice(0,10);

        // bring form into view and focus first input
        try { form.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch (e) { window.scrollTo({ top: 0, behavior: 'smooth' }); }
        setTimeout(function () { const first = form.querySelector('input, select, textarea'); if (first) first.focus(); }, 300);
      };
    </script>

    <div id="panel-fines-legacy" class="panel">
      <div class="section-header"><div><div class="section-title" style="gap:8px;">Fines Management</div><div class="section-sub"></div></div></div>
      <div class="card">
        <div class="card-body" style="padding:0;">
          <table>
            <thead><tr><th>Patron Name</th><th>Reason</th><th>Overdue Days</th><th>Fine Amount</th><th>Status</th></tr></thead>
            <tbody>
              <tr><td>Juan Dela Cruz (STU-2202)</td><td>Overdue Book: <em>Physics Vol. 2</em></td><td>2 days</td><td style="color:var(--error); font-weight:700">₱20.00</td><td><span class="status-chip pending">Unpaid</span></td></tr>
              <tr><td>Dr. Ana Reyes (RES-0101)</td><td>Overdue Researcher Item: <em>Lab Terminal</em></td><td>5 days</td><td style="color:var(--error); font-weight:700">₱50.00</td><td><span class="status-chip pending">Unpaid</span></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div id="panel-fines-legacy" class="panel">
      <div class="section-header"><div><div class="section-title" style="gap:8px;">Fines Management</div><div class="section-sub"></div></div></div>
      <div class="card">
        <div class="card-body" style="padding:0;">
          <table>
            <thead><tr><th>Patron Name</th><th>Reason</th><th>Overdue Days</th><th>Fine Amount</th><th>Status</th></tr></thead>
            <tbody>
              <tr><td>Juan Dela Cruz (STU-2202)</td><td>Overdue Book: <em>Physics Vol. 2</em></td><td>2 days</td><td style="color:var(--error); font-weight:700">₱20.00</td><td><span class="status-chip pending">Unpaid</span></td></tr>
              <tr><td>Dr. Ana Reyes (RES-0101)</td><td>Overdue Researcher Item: <em>Lab Terminal</em></td><td>5 days</td><td style="color:var(--error); font-weight:700">₱50.00</td><td><span class="status-chip pending">Unpaid</span></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div id="panel-wifi" class="panel">
      <?php
        $wifiRows = \Illuminate\Support\Facades\Schema::hasTable('wifi_vouchers')
          ? \Illuminate\Support\Facades\DB::table('wifi_vouchers')
              ->when(\Illuminate\Support\Facades\Schema::hasColumn('wifi_vouchers', 'is_active'), function ($query) {
                  $query->orderBy('is_active', 'desc');
              })
              ->orderByRaw("CASE status WHEN 'Available' THEN 0 WHEN 'Used' THEN 1 WHEN 'Expired' THEN 2 ELSE 3 END")
              ->latest('created_at')
              ->get()
          : collect();
        $inactiveCount = \Illuminate\Support\Facades\Schema::hasColumn('wifi_vouchers', 'is_active')
          ? $wifiRows->where('is_active', false)->count()
          : 0;
      ?>
      <div class="section-header"><div><div class="section-title" style="gap:8px;">WiFi Voucher Management</div><div class="section-sub"></div></div></div>
      <div class="card" style="margin-bottom:18px;">
        <div class="card-header"><div class="card-title">Create WiFi Voucher</div></div>
        <div class="card-body">
          <form action="<?php echo e(route('admin.wifi-vouchers.store')); ?>" method="POST" style="display:grid; grid-template-columns:1fr 180px 220px auto; gap:12px; align-items:end;">
            <?php echo csrf_field(); ?>
            <div class="form-group"><label>Voucher Code</label><input type="text" name="voucher_code" placeholder="PCL-WIFI-001" required></div>
            <div class="form-group"><label>Duration (minutes)</label><input type="number" name="duration_minutes" value="120" min="15" required></div>
            <div class="form-group"><label>Expires At</label><input type="datetime-local" name="expires_at"></div>
            <button class="btn-sm gold" type="submit" style="height:40px;">Create Voucher</button>
          </form>
        </div>
      </div>
      <div class="stats-grid">
        <div class="stat-card green"><div class="stat-label">Available</div><div class="stat-value"><?php echo e($wifiRows->where('status', 'Available')->count()); ?></div></div>
        <div class="stat-card blue"><div class="stat-label">Used</div><div class="stat-value"><?php echo e($wifiRows->where('status', 'Used')->count()); ?></div></div>
        <div class="stat-card red"><div class="stat-label">Expired</div><div class="stat-value"><?php echo e($wifiRows->where('status', 'Expired')->count()); ?></div></div>
        <div class="stat-card gold"><div class="stat-label">Total Vouchers</div><div class="stat-value"><?php echo e($wifiRows->count()); ?></div><div class="stat-change"><?php echo e($inactiveCount); ?> inactive</div></div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:0;">
          <table>
            <thead><tr><th>Voucher Code</th><th>Assigned User</th><th>Status</th><th>Active</th><th>Duration</th><th>Expiry / Used</th><th>Manage</th></tr></thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $wifiRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $voucher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><code><?php echo e($voucher->voucher_code); ?></code></td>
                  <td><?php echo e($voucher->name ?? 'Unassigned'); ?><br><small><?php echo e($voucher->user_id); ?></small></td>
                  <td><span class="status-chip <?php echo e($voucher->status === 'Available' ? 'returned' : ($voucher->status === 'Expired' ? 'overdue' : 'borrowed')); ?>"><?php echo e($voucher->status); ?></span></td>
                  <td>
                    <?php if(\Illuminate\Support\Facades\Schema::hasColumn('wifi_vouchers', 'is_active')): ?>
                      <span class="status-chip <?php echo e($voucher->is_active ? 'returned' : 'pending'); ?>"><?php echo e($voucher->is_active ? 'Active' : 'Inactive'); ?></span>
                    <?php else: ?>
                      <span class="status-chip returned">Active</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo e($voucher->duration_minutes); ?> minutes</td>
                  <td>
                    <?php if($voucher->expires_at): ?> Expires <?php echo e(\Carbon\Carbon::parse($voucher->expires_at)->format('M d, Y h:i A')); ?><br><?php endif; ?>
                    <?php if($voucher->used_at): ?> Used <?php echo e(\Carbon\Carbon::parse($voucher->used_at)->format('M d, Y h:i A')); ?> <?php endif; ?>
                  </td>
                  <td>
                    <form action="<?php echo e(route('admin.wifi-vouchers.update', $voucher->id)); ?>" method="POST" style="display:grid; gap:8px; min-width:220px;">
                      <?php echo csrf_field(); ?>
                      <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:6px;">
                        <select name="status">
                          <option <?php echo e($voucher->status === 'Available' ? 'selected' : ''); ?>>Available</option>
                          <option <?php echo e($voucher->status === 'Used' ? 'selected' : ''); ?>>Used</option>
                          <option <?php echo e($voucher->status === 'Expired' ? 'selected' : ''); ?>>Expired</option>
                        </select>
                        <select name="is_active">
                          <option value="1" <?php echo e(($voucher->is_active ?? true) ? 'selected' : ''); ?>>Active</option>
                          <option value="0" <?php echo e(($voucher->is_active ?? true) ? '' : 'selected'); ?>>Inactive</option>
                        </select>
                        <input type="number" name="duration_minutes" min="15" value="<?php echo e($voucher->duration_minutes); ?>" placeholder="Minutes">
                        <input type="datetime-local" name="expires_at" value="<?php echo e($voucher->expires_at ? \Carbon\Carbon::parse($voucher->expires_at)->format('Y-m-d\TH:i') : ''); ?>">
                      </div>
                      <div style="display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:6px;">
                        <input type="text" name="assign_user_id" placeholder="Assign ID or email">
                        <input type="text" name="assign_name" placeholder="Assign name (optional)">
                        <select name="assign_role">
                          <option value="">Role (optional)</option>
                          <option value="student">Student</option>
                          <option value="researcher">Researcher</option>
                          <option value="staff">Staff</option>
                          <option value="admin">Admin</option>
                          <option value="member">Member</option>
                        </select>
                        <label style="display:flex; align-items:center; gap:6px; font-size:11px;">
                          <input type="checkbox" name="clear_assignment" value="1"> Clear assignment
                        </label>
                      </div>
                      <button class="btn-sm" type="submit">Update</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" style="text-align:center;">No WiFi vouchers yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div id="panel-wifi-legacy" class="panel">
      <div class="section-header"><div><div class="section-title " style="gap:8px;">WiFi Vouchers</div><div class="section-sub"></div></div></div>
      <div class="card">
        <div class="card-body" style="padding:0;">
          <table>
            <thead><tr><th>User / Student</th><th>Voucher Code</th><th>Location Redeemed</th><th>Duration</th><th>Date</th></tr></thead>
            <tbody>
              <tr><td>Carlos Mendez (STU-2204)</td><td><code>WIFI-8821A</code></td><td>PC Grid Area</td><td>2 Hours</td><td>May 26, 2026</td></tr>
              <tr><td>Sofia Torres (STU-2205)</td><td><code>WIFI-8822B</code></td><td>Reading Room</td><td>1 Hour</td><td>May 26, 2026</td></tr>
              <tr><td>Maria Santos (STU-2201)</td><td><code>WIFI-8823C</code></td><td>PC Grid Area</td><td>2 Hours</td><td>May 25, 2026</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div id="panel-proposals" class="panel">
      <div class="section-header">
        <div><div class="section-title " style="gap:8px;">Research Proposals</div><div class="section-sub"></div></div>
      </div>

      <?php
        $allProposals = \Illuminate\Support\Facades\Schema::hasTable('proposals')
          ? \Illuminate\Support\Facades\DB::table('proposals')
              ->when(\Illuminate\Support\Facades\Schema::hasColumn('proposals', 'archived_at'), function ($query) {
                  $query->whereNull('archived_at');
              })
              ->get()
          : collect();
        $pendingProposals = $allProposals->filter(function ($prop) {
            return strtolower($prop->status ?? '') === 'pending';
        });
        $approvedProposals = $allProposals->filter(function ($prop) {
            return strtolower($prop->status ?? '') === 'approved';
        });
        $rejectedProposals = $allProposals->filter(function ($prop) {
            return strtolower($prop->status ?? '') === 'rejected';
        });
        $archivedProposals = \Illuminate\Support\Facades\Schema::hasTable('proposals')
          ? \Illuminate\Support\Facades\DB::table('proposals')
              ->when(\Illuminate\Support\Facades\Schema::hasColumn('proposals', 'archived_at'), function ($query) {
                  $query->whereNotNull('archived_at');
              })
              ->get()
          : collect();
      ?>

      <div style="display:grid; gap:18px;">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Pending Proposals</div>
            <div class="card-sub">Review submissions before approval</div>
          </div>
          <div class="card-body" style="padding:0;">
            <table>
              <thead>
                <tr><th>Researcher</th><th>Proposal Title</th><th>Deadline</th><th>Budget</th><th>Status</th><th style="text-align: right;">Review Decisions</th></tr>
              </thead>
              <tbody>
                <?php if(\Illuminate\Support\Facades\Schema::hasTable('proposals')): ?>
                  <?php $__empty_1 = true; $__currentLoopData = $pendingProposals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                      $proposalDocs = \Illuminate\Support\Facades\Schema::hasTable('proposal_documents')
                        ? \Illuminate\Support\Facades\DB::table('proposal_documents')->where('proposal_id', $prop->id)->get()
                        : collect();
                      $researcherName = \Illuminate\Support\Facades\DB::table('users')
                        ->where('user_id', $prop->user_id)
                        ->orWhere('email', $prop->user_id)
                        ->value('name');
                      $researcherName = trim((string) ($researcherName ?: $prop->user_id));
                    ?>
                    <tr id="proposal-row-<?php echo e($prop->id); ?>">
                      <td><b><?php echo e($researcherName); ?></b></td>
                      <td>
                        <?php echo e($prop->title); ?>

                        <?php if($proposalDocs->isNotEmpty()): ?>
                          <br>
                          <?php $__currentLoopData = $proposalDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('proposal.documents.view', $doc->id)); ?>" target="_blank" rel="noopener" style="font-size:11px; margin-right:6px;"><?php echo e(strtoupper(str_replace('_', ' ', $doc->document_type))); ?></a>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <?php if(!empty($prop->feedback)): ?>
                          <br><small style="color:var(--muted);">Feedback: <?php echo nl2br(e($prop->feedback)); ?></small>
                        <?php endif; ?>
                      </td>
                      <td><?php echo e(\Carbon\Carbon::parse($prop->deadline)->format('M d, Y')); ?></td>
                      <td>₱<?php echo e(number_format($prop->budget, 2)); ?></td>
                      <td><span class="status-chip pending" id="prop-badge-<?php echo e($prop->id); ?>"><?php echo e(ucfirst($prop->status ?? 'Pending')); ?></span></td>
                      <td style="text-align: right;">
                        <div style="display:inline-flex; gap:6px; align-items:start;">
                          <button type="button" class="btn-sm" data-inspector-id="<?php echo e($prop->id); ?>" data-inspector-researcher="<?php echo e($researcherName); ?>" data-inspector-title="<?php echo e($prop->title); ?>" data-inspector-budget="₱<?php echo e(number_format($prop->budget, 2)); ?>" data-inspector-deadline="<?php echo e(\Carbon\Carbon::parse($prop->deadline)->format('M d, Y')); ?>" data-inspector-requirements="<?php echo e($prop->requirements ?? ''); ?>" data-inspector-status="<?php echo e(ucfirst($prop->status ?? 'Pending')); ?>" data-inspector-feedback="<?php echo e($prop->feedback ?? ''); ?>" data-inspector-docs-url="<?php echo e(route('admin.proposals.pdf', $prop->id)); ?>" onclick="evaluateProposalInspector(this)">View</button>
                          <form action="<?php echo e(route('admin.proposals.archive', $prop->id)); ?>" method="POST" class="confirmable" data-confirm="Archive this proposal?" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <button class="btn-sm" type="submit">Archive</button>
                          </form>
                          <form action="<?php echo e(route('admin.proposals.status', $prop->id)); ?>" method="POST" class="confirmable" data-confirm="Approve this proposal?" style="display:grid; gap:6px;">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="status" value="Approved">
                            <input type="number" name="approved_grant" value="<?php echo e($prop->budget); ?>" step="0.01" min="0" style="width:110px;">
                            <button class="btn-sm success" type="submit">Approve</button>
                          </form>
                          <form action="<?php echo e(route('admin.proposals.status', $prop->id)); ?>" method="POST" style="display:grid; gap:6px;" data-proposal-id="<?php echo e($prop->id); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="status" value="Rejected">
                            <input type="hidden" name="feedback" value="">
                            <button class="btn-sm danger" type="button" onclick="handleReject(this.form)">Reject</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" style="text-align:center;">No pending proposals at the moment.</td></tr>
                  <?php endif; ?>
                <?php else: ?>
                  <tr><td colspan="6" style="text-align:center;">Run migrations to enable proposal tracking.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div id="reject-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.42); align-items:center; justify-content:center; z-index:9999; padding:24px;">
          <div style="background:#fff; width:100%; max-width:460px; border-radius:16px; padding:24px; box-shadow:0 24px 50px rgba(15,23,42,0.16); border:1px solid rgba(15,23,42,0.08);">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:16px;">
              <div>
                <h3 style="margin:0; font-size:1.15rem; letter-spacing:0.02em;">Reject Proposal</h3>
                <p style="margin:6px 0 0; color:#475569; line-height:1.5;">Provide a clear reason so the researcher can see why the proposal was rejected.</p>
              </div>
              <button type="button" class="btn-sm" style="background:#f8fafc; border-color:#cbd5e1; color:#0f172a;" onclick="closeRejectModal()">Close</button>
            </div>
            <textarea id="reject-reason" placeholder="Type the rejection reason here..." style="width:100%; min-height:140px; padding:14px; border:1px solid #cbd5e1; border-radius:12px; resize:vertical; font-size:0.95rem; color:#0f172a;"></textarea>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:18px;">
              <button type="button" class="btn-sm" style="background:#f8fafc; border-color:#cbd5e1; color:#0f172a;" onclick="closeRejectModal()">Cancel</button>
              <button type="button" class="btn-sm danger" onclick="submitReject()" style="min-width:120px;">Submit Reject</button>
            </div>
          </div>
        </div>

        <script>
          let activeRejectForm = null;
          function handleReject(form) {
            activeRejectForm = form;
            document.getElementById('reject-reason').value = '';
            document.getElementById('reject-modal').style.display = 'flex';
          }
          function closeRejectModal() {
            document.getElementById('reject-modal').style.display = 'none';
            activeRejectForm = null;
          }
          async function submitReject() {
            const reason = document.getElementById('reject-reason').value.trim();
            if (!reason) {
              alert('Rejection reason is required.');
              return;
            }
            if (!activeRejectForm) return;
            if (window.showConfirm) {
              const confirmed = await window.showConfirm('Reject this proposal and send the feedback to the researcher?');
              if (!confirmed) return;
            }
            const input = activeRejectForm.querySelector('[name="feedback"]');
            if (input) input.value = reason;
            activeRejectForm.submit();
          }
        </script>

        <div class="card">
          <div class="card-header">
            <div class="card-title">Approved Proposals</div>
            <div class="card-sub">Completed research proposals</div>
          </div>
          <div class="card-body" style="padding:0;">
            <table>
              <thead>
                <tr><th>Researcher</th><th>Proposal Title</th><th>Deadline</th><th>Budget</th><th>Status</th><th style="text-align: right;">Actions</th></tr>
              </thead>
              <tbody>
                <?php if(\Illuminate\Support\Facades\Schema::hasTable('proposals')): ?>
                  <?php $__empty_1 = true; $__currentLoopData = $approvedProposals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                      $proposalDocs = \Illuminate\Support\Facades\Schema::hasTable('proposal_documents')
                        ? \Illuminate\Support\Facades\DB::table('proposal_documents')->where('proposal_id', $prop->id)->get()
                        : collect();
                      $researcherName = \Illuminate\Support\Facades\DB::table('users')
                        ->where('user_id', $prop->user_id)
                        ->orWhere('email', $prop->user_id)
                        ->value('name');
                      $researcherName = trim((string) ($researcherName ?: $prop->user_id));
                    ?>
                    <tr id="proposal-row-<?php echo e($prop->id); ?>">
                      <td><b><?php echo e($researcherName); ?></b></td>
                      <td>
                        <?php echo e($prop->title); ?>

                        <?php if($proposalDocs->isNotEmpty()): ?>
                          <br>
                          <?php $__currentLoopData = $proposalDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('proposal.documents.view', $doc->id)); ?>" target="_blank" rel="noopener" style="font-size:11px; margin-right:6px;"><?php echo e(strtoupper(str_replace('_', ' ', $doc->document_type))); ?></a>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <?php if(!empty($prop->feedback)): ?>
                          <br><small style="color:var(--muted);">Feedback: <?php echo nl2br(e($prop->feedback)); ?></small>
                        <?php endif; ?>
                      </td>
                      <td><?php echo e(\Carbon\Carbon::parse($prop->deadline)->format('M d, Y')); ?></td>
                      <td>₱<?php echo e(number_format($prop->budget, 2)); ?></td>
                      <td><span class="status-chip approved"><?php echo e(ucfirst($prop->status ?? 'Approved')); ?></span></td>
                      <td style="text-align: right;">
                        <div style="display:inline-flex; gap:6px; align-items:start;">
                          <button type="button" class="btn-sm" data-inspector-id="<?php echo e($prop->id); ?>" data-inspector-researcher="<?php echo e($researcherName); ?>" data-inspector-title="<?php echo e($prop->title); ?>" data-inspector-budget="₱<?php echo e(number_format($prop->budget, 2)); ?>" data-inspector-deadline="<?php echo e(\Carbon\Carbon::parse($prop->deadline)->format('M d, Y')); ?>" data-inspector-requirements="<?php echo e($prop->requirements ?? ''); ?>" data-inspector-status="<?php echo e(ucfirst($prop->status ?? 'Pending')); ?>" data-inspector-feedback="<?php echo e($prop->feedback ?? ''); ?>" data-inspector-docs-url="<?php echo e(route('admin.proposals.pdf', $prop->id)); ?>" onclick="evaluateProposalInspector(this)">View</button>
                          <form action="<?php echo e(route('admin.proposals.archive', $prop->id)); ?>" method="POST" class="confirmable" data-confirm="Archive this proposal?" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <button class="btn-sm" type="submit">Archive</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" style="text-align:center;">No approved proposals yet.</td></tr>
                  <?php endif; ?>
                <?php else: ?>
                  <tr><td colspan="6" style="text-align:center;">Run migrations to enable proposal tracking.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <div class="card-title">Rejected Proposals</div>
            <div class="card-sub">Declined and reviewed submissions</div>
          </div>
          <div class="card-body" style="padding:0;">
            <table>
              <thead>
                <tr><th>Researcher</th><th>Proposal Title</th><th>Deadline</th><th>Budget</th><th>Status</th><th style="text-align: right;">Actions</th></tr>
              </thead>
              <tbody>
                <?php if(\Illuminate\Support\Facades\Schema::hasTable('proposals')): ?>
                  <?php $__empty_1 = true; $__currentLoopData = $rejectedProposals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                      $proposalDocs = \Illuminate\Support\Facades\Schema::hasTable('proposal_documents')
                        ? \Illuminate\Support\Facades\DB::table('proposal_documents')->where('proposal_id', $prop->id)->get()
                        : collect();
                      $researcherName = \Illuminate\Support\Facades\DB::table('users')
                        ->where('user_id', $prop->user_id)
                        ->orWhere('email', $prop->user_id)
                        ->value('name');
                      $researcherName = trim((string) ($researcherName ?: $prop->user_id));
                    ?>
                    <tr id="proposal-row-<?php echo e($prop->id); ?>-rejected">
                      <td><b><?php echo e($researcherName); ?></b></td>
                      <td>
                        <?php echo e($prop->title); ?>

                        <?php if($proposalDocs->isNotEmpty()): ?>
                          <br>
                          <?php $__currentLoopData = $proposalDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('proposal.documents.view', $doc->id)); ?>" target="_blank" rel="noopener" style="font-size:11px; margin-right:6px;"><?php echo e(strtoupper(str_replace('_', ' ', $doc->document_type))); ?></a>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <?php if(!empty($prop->feedback)): ?>
                          <br><small style="color:var(--muted);">Feedback: <?php echo nl2br(e($prop->feedback)); ?></small>
                        <?php endif; ?>
                      </td>
                      <td><?php echo e(\Carbon\Carbon::parse($prop->deadline)->format('M d, Y')); ?></td>
                      <td>₱<?php echo e(number_format($prop->budget, 2)); ?></td>
                      <td><span class="status-chip overdue">Rejected</span></td>
                      <td style="text-align: right;">
                        <div style="display:inline-flex; gap:6px; align-items:start;">
                          <button type="button" class="btn-sm" data-inspector-id="<?php echo e($prop->id); ?>" data-inspector-researcher="<?php echo e($researcherName); ?>" data-inspector-title="<?php echo e($prop->title); ?>" data-inspector-budget="₱<?php echo e(number_format($prop->budget, 2)); ?>" data-inspector-deadline="<?php echo e(\Carbon\Carbon::parse($prop->deadline)->format('M d, Y')); ?>" data-inspector-requirements="<?php echo e($prop->requirements ?? ''); ?>" data-inspector-status="<?php echo e(ucfirst($prop->status ?? 'Pending')); ?>" data-inspector-feedback="<?php echo e($prop->feedback ?? ''); ?>" data-inspector-docs-url="<?php echo e(route('admin.proposals.pdf', $prop->id)); ?>" onclick="evaluateProposalInspector(this)">View</button>
                          <form action="<?php echo e(route('admin.proposals.archive', $prop->id)); ?>" method="POST" class="confirmable" data-confirm="Archive this proposal?" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <button class="btn-sm" type="submit">Archive</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" style="text-align:center;">No rejected proposals yet.</td></tr>
                  <?php endif; ?>
                <?php else: ?>
                  <tr><td colspan="6" style="text-align:center;">Run migrations to enable proposal tracking.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <div class="card-title">Archived Proposals</div>
            <div class="card-sub">Restorable proposal records</div>
          </div>
          <div class="card-body" style="padding:0;">
            <table>
              <thead>
                <tr><th>Researcher</th><th>Proposal Title</th><th>Deadline</th><th>Budget</th><th>Status</th><th style="text-align: right;">Actions</th></tr>
              </thead>
              <tbody>
                <?php if(\Illuminate\Support\Facades\Schema::hasTable('proposals')): ?>
                  <?php $__empty_1 = true; $__currentLoopData = $archivedProposals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                      $proposalDocs = \Illuminate\Support\Facades\Schema::hasTable('proposal_documents')
                        ? \Illuminate\Support\Facades\DB::table('proposal_documents')->where('proposal_id', $prop->id)->get()
                        : collect();
                      $researcherName = \Illuminate\Support\Facades\DB::table('users')
                        ->where('user_id', $prop->user_id)
                        ->orWhere('email', $prop->user_id)
                        ->value('name');
                      $researcherName = trim((string) ($researcherName ?: $prop->user_id));
                    ?>
                    <tr id="proposal-row-<?php echo e($prop->id); ?>-archived">
                      <td><b><?php echo e($researcherName); ?></b></td>
                      <td>
                        <?php echo e($prop->title); ?>

                        <?php if($proposalDocs->isNotEmpty()): ?>
                          <br>
                          <?php $__currentLoopData = $proposalDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('proposal.documents.view', $doc->id)); ?>" target="_blank" rel="noopener" style="font-size:11px; margin-right:6px;"><?php echo e(strtoupper(str_replace('_', ' ', $doc->document_type))); ?></a>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                      </td>
                      <td><?php echo e(\Carbon\Carbon::parse($prop->deadline)->format('M d, Y')); ?></td>
                      <td>₱<?php echo e(number_format($prop->budget, 2)); ?></td>
                      <td><span class="status-chip returned">Archived</span></td>
                      <td style="text-align: right;">
                        <div style="display:inline-flex; gap:6px; align-items:start;">
                          <button type="button" class="btn-sm" data-inspector-id="<?php echo e($prop->id); ?>" data-inspector-researcher="<?php echo e($researcherName); ?>" data-inspector-title="<?php echo e($prop->title); ?>" data-inspector-budget="₱<?php echo e(number_format($prop->budget, 2)); ?>" data-inspector-deadline="<?php echo e(\Carbon\Carbon::parse($prop->deadline)->format('M d, Y')); ?>" data-inspector-requirements="<?php echo e($prop->requirements ?? ''); ?>" data-inspector-status="<?php echo e(ucfirst($prop->status ?? 'Pending')); ?>" data-inspector-feedback="<?php echo e($prop->feedback ?? ''); ?>" data-inspector-docs-url="<?php echo e(route('admin.proposals.pdf', $prop->id)); ?>" onclick="evaluateProposalInspector(this)">View</button>
                          <form action="<?php echo e(route('admin.proposals.restore', $prop->id)); ?>" method="POST" class="confirmable" data-confirm="Restore this proposal?" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <button class="btn-sm success" type="submit">Restore</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" style="text-align:center;">No archived proposals yet.</td></tr>
                  <?php endif; ?>
                <?php else: ?>
                  <tr><td colspan="6" style="text-align:center;">Run migrations to enable proposal tracking.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div id="panel-announcements" class="panel">
      <div class="section-header">
        <div><div class="section-title" style="gap:8px;">Announcements</div><div class="section-sub"></div></div>
        <button class="btn-sm gold" onclick="openModal('announcementModal')">+ New Announcement</button>
      </div>
      <div class="card">
        <div class="card-body" style="padding:0; gap:2px;">
          <table>
            <thead><tr><th>Title Context Header</th><th>Target Recipients Group</th><th>Dispatched Date Log</th><th>Server Status</th></tr></thead>
            <tbody>
              <?php if(\Illuminate\Support\Facades\Schema::hasTable('announcements')): ?>
                <?php $__empty_1 = true; $__currentLoopData = \Illuminate\Support\Facades\DB::table('announcements')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr>
                    <td><strong><?php echo e($announcement->title); ?></strong></td>
                    <td><?php echo e($announcement->recipients ?? 'All Users'); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($announcement->created_at)->format('M d, Y')); ?></td>
                    <td><span class="status-chip returned">Sent</span></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr><td>Library Hours Update</td><td>All Users</td><td>Apr 20, 2026</td><td><span class="status-chip returned">Sent</span></td></tr>
                  <tr><td>New Reference Books Arrived</td><td>Students</td><td>Apr 15, 2026</td><td><span class="status-chip returned">Sent</span></td></tr>
                <?php endif; ?>
              <?php else: ?>
                <tr><td>Library Hours Update</td><td>All Users</td><td>Apr 20, 2026</td><td><span class="status-chip returned">Sent</span></td></tr>
                <tr><td>New Reference Books Arrived</td><td>Students</td><td>Apr 15, 2026</td><td><span class="status-chip returned">Sent</span></td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div id="panel-reports" class="panel">
      <div class="section-header">
        <div><div class="section-title">Reports Generator</div><div class="section-sub"></div></div>
      </div>

      <div class="card report-selector-box" style="margin-bottom: 20px; border-color: var(--border);">
        <div class="card-body">
          <div style="margin-bottom: 12px;"><b style="font-size: 13px; color: var(--gold);">1. Select Target Category Segments:</b></div>
          <div class="report-selection-matrix">
            <label class="matrix-item"><input type="checkbox" class="report-checkbox-target" value="schools"><span>Schools</span></label>
            <label class="matrix-item"><input type="checkbox" class="report-checkbox-target" value="ages"><span>Ages</span></label>
            <label class="matrix-item"><input type="checkbox" class="report-checkbox-target" value="proposals"><span>Proposals</span></label>
            <label class="matrix-item"><input type="checkbox" class="report-checkbox-target" value="topusers"><span>Top Users</span></label>
            <label class="matrix-item"><input type="checkbox" class="report-checkbox-target" value="topwifi"><span>Top WiFi Users</span></label>
            <label class="matrix-item"><input type="checkbox" class="report-checkbox-target" value="borrowing" checked><span>Borrowing</span></label>
            <label class="matrix-item"><input type="checkbox" class="report-checkbox-target" value="genders" checked><span>Gender Demographics</span></label>
            <label class="matrix-item"><input type="checkbox" class="report-checkbox-target" value="gatecheckins"><span>Gate Check-ins</span></label>
          </div>

          <div style="display: flex; align-items: center; gap: 14px; border-top: 1px solid var(--border); padding-top: 16px; margin-top: 10px; flex-wrap: wrap;">
            <b style="font-size: 13px; color: var(--gold);">2. Timeframe Frequency:</b>
            <select id="reportTimeframeSelector" style="width: auto; padding: 6px 14px; background:var(--bg);"><option value="weekly">Weekly</option><option value="monthly" selected>Monthly</option><option value="annual">Annual</option></select>
            <button class="btn-sm report-export" onclick="exportFilteredReportsPDF()" style="margin-left: auto;">📥 Export PDF</button>
          </div>
        </div>
      </div>

      <?php
        $borrowedMonthTotal = array_sum($monthlyBorrowing ?? []);
        $returnedTotal = $returnedToday ?? 0;
        $borrowReportTotal = max(1, $borrowedMonthTotal + $returnedTotal);
        $borrowPct = round(($borrowedMonthTotal / $borrowReportTotal) * 100);
        $returnPct = round(($returnedTotal / $borrowReportTotal) * 100);
        $proposalTotal = max(1, $proposalStats['total'] ?? 0);

        // Map school names to CSS color classes
        $schoolColorClass = function ($name) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name ?? ''));
            $map = [
                'dnsc' => 'school-dnsc', 'um' => 'school-um', 'pnhs' => 'school-pnhs',
                'maryknoll' => 'school-maryknoll', 'southerndavao' => 'school-southerndavao',
                'northdavao' => 'school-northdavao', 'aces' => 'school-aces',
                'sanvicente' => 'school-sanvicente',
            ];
            foreach ($map as $key => $class) {
                if (str_contains($slug, $key)) return $class;
            }
            return 'age-other';
        };

        // Map age bracket to CSS class
        $ageColorClass = function ($label) {
            return match ($label) {
                '18-22' => 'age-underage',
                '23-27' => 'age-youth',
                '28+' => 'age-other',
                default => 'age-other',
            };
        };
      ?>

      <div id="rep-group-borrowing" class="card report-category-group visible" style="margin-bottom: 20px;">
        <div class="card-header"><div class="card-title">Book Borrowing Report</div></div>
        <div class="card-body" style="background:#ffffff;">
          <div class="bar-chart">
            <div class="bar-row"><div class="bar-label">Borrowed</div><div class="bar-track"><div class="bar-fill" style="width: <?php echo e($borrowPct); ?>%; background-color:#2563EB;"></div></div><div class="bar-val"><?php echo e($borrowedMonthTotal); ?> <span>(<?php echo e($borrowPct); ?>%)</span></div></div>
            <div class="bar-row"><div class="bar-label">Returned</div><div class="bar-track"><div class="bar-fill" style="width: <?php echo e($returnPct); ?>%; background-color:#16A34A;"></div></div><div class="bar-val"><?php echo e($returnedTotal); ?> <span>(<?php echo e($returnPct); ?>%)</span></div></div>
          </div>
          <table style="margin-top:14px; font-size:12px;"><thead><tr><th>Name of Borrower</th><th>Category</th><th>Title of Books</th><th>Author</th><th>Date of Request</th><th>Date of Deadline</th><th>Return</th><th>Fines</th></tr></thead><tbody><tr><td colspan="8" style="text-align:center; color:var(--muted);">Full table available in PDF export. Select Borrowing and click Export PDF.</td></tr></tbody></table>
        </div>
      </div>

      <div id="rep-group-genders" class="card report-category-group visible" style="margin-bottom: 20px;">
        <div class="card-header"><div class="card-title">Gender Demographics</div></div>
        <div class="card-body" style="background:#ffffff;">
          <div class="bar-chart">
            <div class="bar-row"><div class="bar-label">Female</div><div class="bar-track"><div class="bar-fill gender-female" style="width: <?php echo e($femalePct ?? 0); ?>%;"></div></div><div class="bar-val"><?php echo e($femaleCount ?? 0); ?> <span>(<?php echo e($femalePct ?? 0); ?>%)</span></div></div>
            <div class="bar-row"><div class="bar-label">Male</div><div class="bar-track"><div class="bar-fill gender-male" style="width: <?php echo e($malePct ?? 0); ?>%;"></div></div><div class="bar-val"><?php echo e($maleCount ?? 0); ?> <span>(<?php echo e($malePct ?? 0); ?>%)</span></div></div>
          </div>
          <table style="margin-top:14px; font-size:12px;"><thead><tr><th>Gender</th><th class="text-center">Count</th><th class="text-center">Percentage</th></tr></thead><tbody><tr><td>Female</td><td class="text-center"><?php echo e($femaleCount ?? 0); ?></td><td class="text-center"><?php echo e($femalePct ?? 0); ?>%</td></tr><tr><td>Male</td><td class="text-center"><?php echo e($maleCount ?? 0); ?></td><td class="text-center"><?php echo e($malePct ?? 0); ?>%</td></tr></tbody></table>
        </div>
      </div>

      <div id="rep-group-schools" class="card report-category-group" style="margin-bottom: 20px;">
        <div class="card-header"><div class="card-title">Schools</div></div>
        <div class="card-body" style="background:#ffffff;">
          <div class="bar-chart">
            <?php $__empty_1 = true; $__currentLoopData = ($schoolDistribution ?? collect())->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <?php $schoolPct = ($totalSchoolUsers ?? 0) > 0 ? round(($school->count / $totalSchoolUsers) * 100) : 0; ?>
              <div class="bar-row"><div class="bar-label"><?php echo e($school->school ?: 'Unspecified'); ?></div><div class="bar-track"><div class="bar-fill <?php echo e($schoolColorClass($school->school)); ?>" style="width: <?php echo e($schoolPct); ?>%;"></div></div><div class="bar-val"><?php echo e($school->count); ?> <span>(<?php echo e($schoolPct); ?>%)</span></div></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <div class="empty-state">No school distribution data available.</div>
            <?php endif; ?>
          </div>
          <table style="margin-top:14px; font-size:12px;"><thead><tr><th>Name</th><th>School</th><th class="text-center">Count</th></tr></thead><tbody>
            <?php $__empty_1 = true; $__currentLoopData = ($schoolDistribution ?? collect())->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr><td>—</td><td><?php echo e($school->school ?: 'Unspecified'); ?></td><td class="text-center"><?php echo e($school->count); ?></td></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="3" style="text-align:center; color:var(--muted);">No data available.</td></tr>
            <?php endif; ?>
          </tbody></table>
        </div>
      </div>

      <div id="rep-group-ages" class="card report-category-group" style="margin-bottom: 20px;">
        <div class="card-header"><div class="card-title">Ages</div></div>
        <div class="card-body" style="background:#ffffff;">
          <div class="bar-chart">
            <?php $__currentLoopData = ($ageBrackets ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php $agePct = ($totalAgeUsers ?? 0) > 0 ? round(($count / $totalAgeUsers) * 100) : 0; ?>
              <div class="bar-row"><div class="bar-label"><?php echo e($label); ?></div><div class="bar-track"><div class="bar-fill <?php echo e($ageColorClass($label)); ?>" style="width: <?php echo e($agePct); ?>%;"></div></div><div class="bar-val"><?php echo e($count); ?> <span>(<?php echo e($agePct); ?>%)</span></div></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
          <table style="margin-top:14px; font-size:12px;"><thead><tr><th>Age Bracket</th><th class="text-center">Count</th><th class="text-center">Percentage</th></tr></thead><tbody>
            <?php $ageTotal = max(1, array_sum($ageBrackets ?? [])); ?>
            <?php $__currentLoopData = ($ageBrackets ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr><td><?php echo e($label); ?></td><td class="text-center"><?php echo e($count); ?></td><td class="text-center"><?php echo e(round(($count / $ageTotal) * 100)); ?>%</td></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody></table>
        </div>
      </div>

      <div id="rep-group-proposals" class="card report-category-group" style="margin-bottom: 20px;">
        <div class="card-header"><div class="card-title">Research Proposals</div></div>
        <div class="card-body" style="background:#ffffff;">
          <div class="bar-chart">
            <?php $__currentLoopData = ['pending' => ['Pending', '#F59E0B'], 'approved' => ['Approved', '#16A34A'], 'rejected' => ['Rejected', '#EF4444']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => [$label, $color]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php $proposalPct = round((($proposalStats[$key] ?? 0) / $proposalTotal) * 100); ?>
              <div class="bar-row"><div class="bar-label"><?php echo e($label); ?></div><div class="bar-track"><div class="bar-fill" style="width: <?php echo e($proposalPct); ?>%; background-color: <?php echo e($color); ?>;"></div></div><div class="bar-val"><?php echo e($proposalStats[$key] ?? 0); ?> <span>(<?php echo e($proposalPct); ?>%)</span></div></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
          <table style="margin-top:14px; font-size:12px;"><thead><tr><th>Name</th><th>Titles of Proposal</th><th>Date of Submitted</th><th>Deadline</th><th>Funds</th></tr></thead><tbody><tr><td colspan="5" style="text-align:center; color:var(--muted);">Full table available in PDF export. Select Proposals and click Export PDF.</td></tr></tbody></table>
        </div>
      </div>

      <div id="rep-group-topusers" class="card report-category-group" style="margin-bottom: 20px;">
        <div class="card-header"><div class="card-title">Top Users</div></div>
        <div class="card-body" style="background:#ffffff; padding:0;">
          <table><thead><tr><th>Rank</th><th>Visitor Name</th><th>Barcode</th><th>Visit Count</th></tr></thead><tbody>
            <?php $__empty_1 = true; $__currentLoopData = ($topVisitors ?? collect())->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $visitor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr><td><span class="rank-num turquoise-rank"><?php echo e($index + 1); ?></span></td><td><?php echo e($visitor->full_name ?? 'Unknown'); ?></td><td><?php echo e($visitor->barcode_id ?? 'N/A'); ?></td><td><?php echo e($visitor->visit_count ?? 0); ?></td></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="4" style="text-align:center;">No visitor ranking data available.</td></tr>
            <?php endif; ?>
          </tbody></table>
        </div>
      </div>

      <div id="rep-group-topwifi" class="card report-category-group" style="margin-bottom: 20px;">
        <div class="card-header"><div class="card-title">Top WiFi Users</div></div>
        <div class="card-body" style="background:#ffffff; padding:0;">
          <table><thead><tr><th>Rank</th><th>User</th><th>User ID</th><th>Bandwidth</th><th>Voucher</th></tr></thead><tbody>
            <?php $__empty_1 = true; $__currentLoopData = ($topWifiUsers ?? collect())->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $wifiUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr><td><span class="rank-num turquoise-rank"><?php echo e($index + 1); ?></span></td><td><?php echo e($wifiUser->name ?? 'Unknown'); ?></td><td><?php echo e($wifiUser->user_id ?? 'N/A'); ?></td><td><?php echo e($wifiUser->bandwidth_gb ?? 0); ?> GB</td><td><?php echo e($wifiUser->voucher_code ?? 'N/A'); ?></td></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="5" style="text-align:center;">No WiFi usage data available.</td></tr>
            <?php endif; ?>
          </tbody></table>
        </div>
      </div>

      <div id="rep-group-gatecheckins" class="card report-category-group" style="margin-bottom: 20px;">
        <div class="card-header"><div class="card-title">Gate Check-ins</div></div>
        <div class="card-body" style="background:#ffffff; padding:0;">
          <table>
            <thead><tr><th>Patron</th><th>Barcode</th><th>Check-In</th><th>Check-Out</th></tr></thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = ($gateEntries ?? collect())->take(20); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr><td><?php echo e($entry->full_name ?? 'Unknown Member'); ?></td><td><?php echo e($entry->barcode_id ?? 'N/A'); ?></td><td><?php echo e($entry->entry_time ? \Carbon\Carbon::parse($entry->entry_time)->format('M d, Y h:i A') : 'N/A'); ?></td><td><?php echo e($entry->exit_time ? \Carbon\Carbon::parse($entry->exit_time)->format('M d, Y h:i A') : 'Active'); ?></td></tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" style="text-align:center;">No gate check-in data available.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
   <div id="panel-system" class="panel">
  <div class="section-header">
    <div>
      <div class="section-title">System Utilities</div>
      <div class="section-sub">Manage users, schools, backup snapshots, and system-wide account settings</div>
    </div>
  </div>

  
  <div class="system-section-label">Account Management</div>
  <div class="utilities-grid">

    
    <div class="card">
      <div class="card-header">
        <div class="card-title">Create Account</div>
        <div class="card-sub">Add a new user to the system</div>
      </div>
      <div class="card-body" style="padding:24px;">
        <form action="<?php echo e(route('admin.users.store')); ?>" method="POST" id="create-user-form" novalidate>
          <?php echo csrf_field(); ?>
          <div class="system-form-heading">Account Information</div>
          <div class="form-row">
            <div class="form-group">
              <label for="sys-name">Full Name</label>
              <input type="text" name="name" id="sys-name" placeholder="e.g. Juan Dela Cruz" required>
            </div>
            <div class="form-group">
              <label for="sys-email">Email Address</label>
              <input type="email" name="email" id="sys-email" placeholder="e.g. juan.delacruz@example.com" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="sys-user-id">Username / ID</label>
              <input type="text" name="user_id" id="sys-user-id" placeholder="e.g. STU-2024-0001" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="sys-role">Role</label>
              <select name="role" id="sys-role" required>
                <option value="" disabled selected>Select a role</option>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option value="student">Student</option>
                <option value="researcher">Researcher</option>
                <option value="visitor">Visitor</option>
              </select>
            </div>
            <div class="form-group">
              <label for="sys-status">Account Status</label>
              <select name="status" id="sys-status">
                <option value="approved">Approved</option>
                <option value="pending">Pending</option>
                <option value="retired">Retired</option>
                <option value="transferred">Transferred</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="sys-password">Password</label>
              <div class="password-input-wrap">
                <input type="password" name="password" id="sys-password" placeholder="At least 8 characters" required minlength="8">
                <button type="button" class="password-toggle" data-target="sys-password" aria-label="Toggle password visibility">👁</button>
              </div>
              <div class="field-hint" id="sys-password-hint" style="display:none;"></div>
            </div>
            <div class="form-group">
              <label for="sys-password-confirm">Confirm Password</label>
              <div class="password-input-wrap">
                <input type="password" name="password_confirmation" id="sys-password-confirm" placeholder="Re-enter password" required minlength="8">
                <button type="button" class="password-toggle" data-target="sys-password-confirm" aria-label="Toggle password visibility">👁</button>
              </div>
              <div class="field-hint" id="sys-password-confirm-hint" style="display:none;"></div>
            </div>
          </div>
          <div style="margin-top:4px;">
            <button type="submit" class="btn-primary create-user-btn" data-confirm="Create this user account?">
              <span class="btn-icon-left">+</span> Create User
            </button>
          </div>
        </form>
      </div>
    </div>

    
    <div class="card">
      <div class="card-header">
        <div class="card-title">Update User Status</div>
        <div class="card-sub">Change an account status by lookup</div>
      </div>
      <div class="card-body" style="padding:24px;">
        <form action="<?php echo e(route('admin.users.status')); ?>" method="POST" id="update-status-form">
          <?php echo csrf_field(); ?>
          <div class="form-group">
            <label for="sys-lookup">User ID, Barcode, or Email</label>
            <input type="text" name="lookup" id="sys-lookup" placeholder="e.g. STU-2024-0001" required>
          </div>
          <div class="form-group">
            <label for="sys-update-status">New Status</label>
            <select name="status" id="sys-update-status">
              <option value="approved">Approved</option>
              <option value="pending">Pending</option>
              <option value="retired">Retired</option>
              <option value="transferred">Transferred</option>
            </select>
          </div>
          <button type="submit" class="btn-sm gold" data-confirm="Update this user status?">Update Status</button>
        </form>
        <?php $__errorArgs = ['user_status_error'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
          <div style="color:#dc2626; font-size:12px; margin-top:10px;"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    
    <?php if(\Illuminate\Support\Facades\Schema::hasTable('users')): ?>
      <div class="card" style="grid-column: 1 / -1;">
        <div class="card-header">
          <div class="card-title">Reset Username / Password</div>
          <div class="card-sub">Update account credentials for an existing user</div>
        </div>
        <div class="card-body" style="padding:24px;">
          <form method="POST" id="admin-user-reset-form" style="display:grid; gap:16px;">
            <?php echo csrf_field(); ?>
            <div class="form-row">
              <div class="form-group">
                <label for="admin-user-reset-select">Select User</label>
                <select name="target_user_id" id="admin-user-reset-select" onchange="populateResetUserForm()">
                  <option value="">Choose a user</option>
                  <?php $__currentLoopData = App\Models\User::orderBy('role')->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($account->id); ?>" data-user-id="<?php echo e($account->user_id); ?>" data-name="<?php echo e($account->name); ?>">
                      <?php echo e($account->name); ?> (<?php echo e(ucfirst($account->role)); ?>)
                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              <div class="form-group">
                <label for="admin-reset-user-id">New Username / Employee ID</label>
                <input type="text" name="user_id" id="admin-reset-user-id" placeholder="Enter new username" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="admin-reset-password">New Password</label>
                <div class="password-input-wrap">
                  <input type="password" name="password" id="admin-reset-password" placeholder="At least 8 characters" required minlength="8">
                  <button type="button" class="password-toggle" data-target="admin-reset-password" aria-label="Toggle password visibility">👁</button>
                </div>
              </div>
              <div class="form-group">
                <label for="admin-reset-password-confirm">Confirm New Password</label>
                <div class="password-input-wrap">
                  <input type="password" name="password_confirmation" id="admin-reset-password-confirm" placeholder="Re-enter password" required minlength="8">
                  <button type="button" class="password-toggle" data-target="admin-reset-password-confirm" aria-label="Toggle password visibility">👁</button>
                </div>
              </div>
            </div>
            <button type="submit" class="btn-sm gold" data-confirm="Reset this user's credentials?">Save Credentials</button>
          </form>
        </div>
      </div>
    <?php endif; ?>
  </div>

  
  <div class="system-section-label">Library Configuration</div>
  <div class="utilities-grid utilities-grid-wide">

    
    <div class="card">
      <div class="card-header">
        <div class="card-title">School List</div>
        <div class="card-sub">Add or remove schools from registration options</div>
      </div>
      <div class="card-body" style="padding:24px;">
        <form action="<?php echo e(route('admin.schools.store')); ?>" method="POST" style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px;">
          <?php echo csrf_field(); ?>
          <input type="text" name="name" placeholder="e.g. Davao del Norte State College" required style="flex:1; min-width:220px;">
          <button type="submit" class="btn-sm gold" data-confirm="Add this school to the registration options?">Add School</button>
        </form>

        <?php if(\Illuminate\Support\Facades\Schema::hasTable('school_options')): ?>
          <?php $managedSchools = \Illuminate\Support\Facades\DB::table('school_options')->orderBy('name')->get(); ?>
          <?php if($managedSchools->isNotEmpty()): ?>
            <div style="display:flex; flex-wrap:wrap; gap:8px;">
              <?php $__currentLoopData = $managedSchools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex; align-items:center; gap:8px; padding:8px 12px; border:1px solid #d1d5db; border-radius:999px; background:#f8fafc; font-size:13px;">
                  <span><?php echo e($school->name); ?></span>
                  <form action="<?php echo e(route('admin.schools.delete', $school->id)); ?>" method="POST" style="margin:0; display:inline;">
                    <?php echo csrf_field(); ?>
                    <button class="btn-sm danger" type="submit" data-confirm="Remove this school from the registration list?">Remove</button>
                  </form>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          <?php else: ?>
            <div style="color:#64748b; font-size:13px;">No custom schools added yet.</div>
          <?php endif; ?>
        <?php else: ?>
          <div style="color:#64748b; font-size:13px;">School list is not available. Run migrations to enable.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  
  <div class="system-section-label">System Maintenance</div>
  <div class="utilities-grid utilities-grid-wide">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Backup Snapshot</div>
        <div class="card-sub">Create and store a local JSON backup</div>
      </div>
      <div class="card-body" style="padding:24px;">
        <form action="<?php echo e(route('admin.backups.store')); ?>" method="POST" style="display:grid; gap:12px;">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn-sm gold" style="width:100%;" data-confirm="Generate a new JSON backup snapshot?">Generate JSON Backup</button>
        </form>

        <?php if(\Illuminate\Support\Facades\Schema::hasTable('backup_logs')): ?>
          <?php $backups = \Illuminate\Support\Facades\DB::table('backup_logs')->latest()->limit(5)->get(); ?>
          <div style="margin-top:14px; display:grid; gap:8px;">
            <?php $__empty_1 = true; $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div style="display:flex; justify-content:space-between; gap:8px; border:1px solid #e2e8f0; border-radius:10px; padding:10px 12px; background:#f8fafc;">
                <div>
                  <div style="font-weight:600; color:#0f172a; font-size:13px;"><?php echo e($backup->filename); ?></div>
                  <div style="font-size:11px; color:#64748b;"><?php echo e(\Carbon\Carbon::parse($backup->created_at)->format('M d, Y h:i A')); ?></div>
                </div>
                <div style="font-size:12px; color:#0f172a; font-weight:700;"><?php echo e(number_format($backup->size / 1024, 2)); ?> KB</div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <div style="color:#64748b; font-size:13px; text-align:center; padding:12px;">No backups generated yet.</div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>






    <div id="panel-backup" class="panel">
      <div class="section-header"><div><div class="section-title" style="gap:8px;">System Backup</div></div></div>
      <div class="card" style="margin-bottom:18px;">
        <div class="card-header"><div class="card-title">Create Backup Snapshot</div></div>
        <div class="card-body">
          <form action="<?php echo e(route('admin.backups.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button class="btn-sm gold" type="submit">Generate JSON Backup</button>
          </form>
        </div>
      </div>
      <div class="card">
        <div class="card-body" style="padding:0;">
          <table>
            <thead><tr><th>Filename</th><th>Size</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
              <?php if(\Illuminate\Support\Facades\Schema::hasTable('backup_logs')): ?>
                <?php $__empty_1 = true; $__currentLoopData = \Illuminate\Support\Facades\DB::table('backup_logs')->latest()->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $backup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr><td><?php echo e($backup->filename); ?></td><td><?php echo e(number_format($backup->size / 1024, 2)); ?> KB</td><td><?php echo e(\Carbon\Carbon::parse($backup->created_at)->format('M d, Y h:i A')); ?></td><td><span class="status-chip returned">Stored local</span></td></tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr><td colspan="4" style="text-align:center;">No backups generated yet.</td></tr>
                <?php endif; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div id="panel-inventory" class="panel"></div>
    <div id="panel-cataloging" class="panel"></div>
    <div id="panel-logrecords" class="panel"></div>

  </div>

<div class="modal-overlay" id="addBookModal">
  <div class="modal">
    <form action="<?php echo e(route('admin.books.add')); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <div class="modal-header">
        <div class="modal-title">Catalog New Book</div>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group"><label>Book Title</label><input type="text" name="title" placeholder="Enter book title" required/></div>
          <div class="form-group"><label>Author</label><input type="text" name="author" placeholder="Author name" required/></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>ISBN </label><input type="text" name="isbn" placeholder="e.g. LIB-006" required/></div>
          <div class="form-group"><label>Call Number</label><input type="text" name="call_number" placeholder="e.g. CS-804" required/></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label> Accession Number</label><input type="text" name="accession_number" placeholder="8-9 letters/numbers only" pattern="[A-Za-z0-9]{8,9}" title="8-9 letters or numbers only" required/></div>
          <div class="form-group">
            <label>Category Genre Class Classification Selection</label>
            <select name="category" required>
              <option value="Math (Mathematics)">Math (Mathematics)</option>
              <option value="Science (Science)">Science (Science)</option>
              <option value="History (Historical)">History (Historical)</option>
              <option value="Lit (Literature)">Lit (Literature)</option>
              <option value="Fil (Filipiniana)">Fil (Filipiniana)</option>
              <option value="Cir (Circulation)">Cir (Circulation)</option>
              <option value="Gen. Ref.">Gen. Ref. (General References)</option>
              <option value="F (Fiction)">F (Fiction)</option>
              <option value="D (Dissertation)">D (Dissertation)</option>
              <option value="T (Thesis)">T (Thesis)</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Book Summary</label><textarea name="summary" placeholder="Add a short summary" rows="3"></textarea></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Cover Photo / Book Image</label><input type="file" name="cover_image" accept="image/*"/></div>
          <div class="form-group"><label>Total Inventory Copies Quantity</label><input type="number" name="copies" value="1" min="1" required/></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Library Shelf Room Section Placement</label><input type="text" name="section_location" placeholder="e.g. Shelf D-2" required/></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn cancel" onclick="closeModal('addBookModal')">Cancel</button>
        <button type="submit" class="btn primary">Save Book</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="proposalInspectorViewModal">
  <div class="modal" style="max-width:640px;">
    <div class="modal-header">
      <div class="modal-title">View Proposal Content</div>
      <span class="modal-close" onclick="closeModal('proposalInspectorViewModal')">Close</span>
    </div>
    <div class="modal-body" style="background:#ffffff">
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; border-bottom:1px solid var(--border); padding-bottom:14px; margin-bottom:16px;">
        <div>
          <label>Lead Researcher</label>
          <div id="inspector-prop-researcher" style="font-size:15px; font-weight:700; color:var(--text)">--</div>
        </div>
        <div>
          <label>Status</label>
          <div id="inspector-prop-status" style="font-size:14px; font-weight:600;">--</div>
        </div>
      </div>
      <div style="border-bottom:1px solid var(--border); padding-bottom:14px; margin-bottom:16px;">
        <label>Proposal Title</label>
        <div id="inspector-prop-title" style="font-size:14px; font-weight:500; line-height:1.5; color:var(--text); font-style:italic;">--</div>
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; border-bottom:1px solid var(--border); padding-bottom:14px; margin-bottom:16px;">
        <div>
          <label>Budget</label>
          <div id="inspector-prop-budget" style="font-size:16px; font-weight:700; color:var(--gold)">--</div>
        </div>
        <div>
          <label>Deadline</label>
          <div id="inspector-prop-deadline" style="font-size:14px; font-weight:600; color:var(--text)">--</div>
        </div>
      </div>
      <div style="border-bottom:1px solid var(--border); padding-bottom:14px; margin-bottom:16px;">
        <label>Requirements / General Specifications</label>
        <div id="inspector-prop-requirements" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:14px; min-height:120px; max-height:300px; overflow-y:auto; font-size:13px; line-height:1.7; color:var(--text); white-space:pre-wrap; word-break:break-word;">--</div>
      </div>
      <div style="margin-bottom:16px;">
        <label>Admin Feedback</label>
        <div id="inspector-prop-feedback" style="background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:14px; min-height:60px; font-size:13px; line-height:1.7; color:#92400e; white-space:pre-wrap; word-break:break-word;">--</div>
      </div>

      
      <div style="border-top:1px solid var(--border); padding-top:14px; margin-top:4px;">
        <label style="margin-bottom:8px; display:block;">💬 Review Comments</label>
        <div id="inspector-comments-list" style="max-height:200px; overflow-y:auto; margin-bottom:12px;">
          <div style="color:var(--muted); font-size:12px; text-align:center; padding:10px;">Loading comments...</div>
        </div>
        <div style="display:flex; gap:8px;">
          <input type="text" id="inspector-comment-input" placeholder="Add a comment..." style="flex:1; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:13px;" maxlength="2000">
          <button type="button" id="inspector-comment-submit" class="btn-sm" onclick="submitInspectorComment()" style="white-space:nowrap;">Send</button>
        </div>
      </div>
    </div>
    <div class="modal-footer" style="display:flex; justify-content:space-between; align-items:center;">
      <a id="inspector-prop-docs-link" href="#" target="_blank" rel="noopener" class="btn-sm" style="text-decoration:none;">📄 Open Documents (PDF viewer)</a>
      <button type="button" class="btn cancel" onclick="closeModal('proposalInspectorViewModal')">Close View</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="addUserModal"><div class="modal"><div class="modal-footer"><button class="btn cancel" onclick="closeModal('addUserModal')">Cancel</button></div></div></div>
<div class="modal-overlay" id="announcementModal">
  <div class="modal">
    <form action="<?php echo e(route('admin.announcements.store')); ?>" method="POST">
      <?php echo csrf_field(); ?>
      <div class="modal-header">
        <div class="modal-title">New Announcement</div>
        <span class="modal-close" onclick="closeModal('announcementModal')">Close</span>
      </div>
      <div class="modal-body">
        <div class="form-group"><label>Title</label><input type="text" name="title" required></div>
        <div class="form-group"><label>Message</label><textarea name="body" required></textarea></div>
        <div class="form-row">
          <div class="form-group"><label>Recipients</label><select name="recipients"><option value="all">All Users</option><option value="student">Students</option><option value="researcher">Researchers</option><option value="staff">Staff</option></select></div>
          <div class="form-group"><label>Expires At</label><input type="datetime-local" name="expires_at"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn cancel" onclick="closeModal('announcementModal')">Cancel</button>
        <button type="submit" class="btn primary">Publish</button>
      </div>
    </form>
  </div>
</div>
<div class="modal-overlay" id="borrowModal"><div class="modal"><div class="modal-footer"><button class="btn cancel" onclick="closeModal('borrowModal')">Exit</button></div></div></div>
<div class="modal-overlay" id="editBookModal"><div class="modal"><div class="modal-footer"><button class="btn cancel" onclick="closeModal('editBookModal')">Exit</button></div></div></div>

<script>
  function openModal(id) { document.getElementById(id).classList.add('open'); }
  function closeModal(id) { document.getElementById(id).classList.remove('open'); }

  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) { if (e.target === overlay) overlay.classList.remove('open'); });
  });

  /* ── LIVE BOOK SEARCH ROW CALCULATIONS FOR FILTERS ── */
  function filterBookTable() {
    const filter = document.getElementById("bookSearchInput").value.toLowerCase();
    const tr = document.getElementById("adminBooksTable").getElementsByTagName("tr");
    for (let i = 1; i < tr.length; i++) {
      if (tr[i].getElementsByTagName("td").length < 2) continue;
      const title = tr[i].getElementsByTagName("td")[2].textContent.toLowerCase();
      const author = tr[i].getElementsByTagName("td")[3].textContent.toLowerCase();
      tr[i].style.display = (title.includes(filter) || author.includes(filter)) ? "" : "none";
    }
  }

  /* ── VERIFIED ACCOUNTS SEARCH ── */
  function filterVerifiedTable() {
    const filter = document.getElementById("verifiedSearchInput").value.toLowerCase();
    document.querySelectorAll(".verified-accounts-table tbody tr").forEach(function (tr) {
      if (tr.getElementsByTagName("td").length < 2) return;
      const rowText = tr.textContent.toLowerCase();
      tr.style.display = rowText.includes(filter) ? "" : "none";
    });
  }

  /* ── REPORT CATEGORY SELECTION HANDLING ── */
  function showSelectedReportGroup() {
    document.querySelectorAll('.report-category-group').forEach(group => { group.classList.remove('visible'); });
    const selectedCategories = document.querySelectorAll('.report-checkbox-target:checked');
    if (!selectedCategories.length) return;
    selectedCategories.forEach(checkbox => {
      const targetGroupBlock = document.getElementById('rep-group-' + checkbox.value);
      if (targetGroupBlock) targetGroupBlock.classList.add('visible');
    });
  }

  function triggerCategorizedPrintPipeline() {
    showSelectedReportGroup();
    window.print();
  }

  function exportFilteredReportsPDF() {
    const checked = document.querySelectorAll('.report-checkbox-target:checked');
    if (!checked.length) {
      alert('Please select at least one report category to export.');
      return;
    }
    const categories = Array.from(checked).map(c => c.value).join(',');
    const url = '<?php echo e(route("admin.reports.export")); ?>?categories=' + encodeURIComponent(categories) + '&view=1';
    window.open(url, '_blank');
  }

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.report-checkbox-target').forEach(input => {
      input.addEventListener('change', showSelectedReportGroup);
    });
    showSelectedReportGroup();
  });

  /* ── RESEARCH INSPECTOR EVALUATION STATE HANDLERS ── */
  let currentInspectorProposalId = null;

  function evaluateProposalInspector(button) {
    const ds = button.dataset;
    currentInspectorProposalId = ds.inspectorId || null;
    document.getElementById('inspector-prop-researcher').textContent = ds.inspectorResearcher || '--';
    document.getElementById('inspector-prop-title').textContent = ds.inspectorTitle || '--';
    document.getElementById('inspector-prop-budget').textContent = ds.inspectorBudget || '--';
    document.getElementById('inspector-prop-deadline').textContent = ds.inspectorDeadline || '--';
    document.getElementById('inspector-prop-status').textContent = ds.inspectorStatus || '--';
    document.getElementById('inspector-prop-requirements').textContent = ds.inspectorRequirements || '--';
    document.getElementById('inspector-prop-feedback').textContent = ds.inspectorFeedback || '--';

    // Style status as a chip
    const statusEl = document.getElementById('inspector-prop-status');
    statusEl.className = 'status-chip';
    const st = (ds.inspectorStatus || '').toLowerCase();
    if (st === 'approved') statusEl.classList.add('approved');
    else if (st === 'rejected') statusEl.classList.add('overdue');
    else statusEl.classList.add('pending');

    // Set the document link
    const docsLink = document.getElementById('inspector-prop-docs-link');
    if (docsLink && ds.inspectorDocsUrl) {
      docsLink.href = ds.inspectorDocsUrl;
      docsLink.style.display = '';
    } else if (docsLink) {
      docsLink.style.display = 'none';
    }

    // Reset comment input
    const commentInput = document.getElementById('inspector-comment-input');
    if (commentInput) commentInput.value = '';

    openModal('proposalInspectorViewModal');

    // Load existing comments
    loadInspectorComments();
  }

  async function loadInspectorComments() {
    const list = document.getElementById('inspector-comments-list');
    if (!list || !currentInspectorProposalId) return;
    list.innerHTML = '<div style="color:var(--muted); font-size:12px; text-align:center; padding:10px;">Loading comments...</div>';

    try {
      const resp = await fetch('/admin/proposals/' + currentInspectorProposalId + '/comments');
      const data = await resp.json();
      const comments = data.comments || [];
      if (!comments.length) {
        list.innerHTML = '<div style="color:var(--muted); font-size:12px; text-align:center; padding:10px;">No comments yet.</div>';
        return;
      }
      list.innerHTML = comments.map(c => {
        const date = c.created_at ? new Date(c.created_at).toLocaleString() : '';
        const role = c.commenter_role ? ' (' + c.commenter_role + ')' : '';
        return '<div style="padding:8px 10px; border-bottom:1px solid #f1f5f9; font-size:12px;">' +
               '<div style="color:var(--muted); margin-bottom:2px;"><b>' + (c.user_id || 'System') + '</b>' + role + ' · ' + date + '</div>' +
               '<div style="color:var(--text); line-height:1.5; white-space:pre-wrap;">' + (c.comment_text || '') + '</div>' +
               '</div>';
      }).join('');
    } catch (e) {
      list.innerHTML = '<div style="color:#dc2626; font-size:12px; text-align:center; padding:10px;">Failed to load comments.</div>';
    }
  }

  async function submitInspectorComment() {
    const input = document.getElementById('inspector-comment-input');
    const btn = document.getElementById('inspector-comment-submit');
    const text = (input?.value || '').trim();
    if (!text || !currentInspectorProposalId) return;

    btn.disabled = true;
    btn.textContent = 'Sending...';

    try {
      const formData = new FormData();
      formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
      formData.append('comment_text', text);

      const resp = await fetch('/admin/proposals/' + currentInspectorProposalId + '/comments', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      });

      if (!resp.ok) throw new Error('Server error');

      input.value = '';
      loadInspectorComments();
    } catch (e) {
      alert('Failed to save comment: ' + e.message);
    } finally {
      btn.disabled = false;
      btn.textContent = 'Send';
    }
  }

  function executeReturnProcess(transactionRowToken, targetedAssetBarcode) {
    const targetStatusSpan = document.getElementById('txn-status-' + transactionRowToken);
    const targetActionButton = document.getElementById('txn-btn-' + transactionRowToken);
    if (targetStatusSpan && targetActionButton) {
      targetStatusSpan.className = 'status-chip returned';
      targetStatusSpan.textContent = 'Returned';
      targetActionButton.style.display = 'none';
    }
  }

  function populateResetUserForm() {
    const select = document.getElementById('admin-user-reset-select');
    const form = document.getElementById('admin-user-reset-form');
    const usernameInput = document.getElementById('admin-reset-user-id');
    const selected = select.options[select.selectedIndex];

    if (!selected || !selected.value) {
      usernameInput.value = '';
      form.action = '';
      return;
    }

    const userId = selected.dataset.userId || '';
    form.action = '/admin/users/' + selected.value + '/reset-password';
    usernameInput.value = userId || '';
  }

  function renderBarcodePreviews() {
    if (typeof JsBarcode !== 'function') return;
    document.querySelectorAll('.barcode-svg').forEach((svg) => {
      const code = svg.dataset.code || '';
      if (!code) return;
      try {
        JsBarcode(svg, code, {
          format: 'CODE128',
          displayValue: false,
          width: 1.2,
          height: 42,
          margin: 0,
          background: '#ffffff',
          lineColor: '#111827'
        });
      } catch (error) {
        console.warn('Barcode render failed', error);
      }
    });
  }

  function printBarcodeCards() {
    const grid = document.getElementById('barcodeCardsGrid');
    if (!grid) return;

    const printWindow = window.open('', '_blank');
    if (!printWindow) return;

    const styles = `
      <style>
        body { margin: 0; font-family: Inter, sans-serif; background:#f9fafb; }
        .print-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:16px; padding:18px; }
        .print-card { border:1px solid #0f172a; border-radius:18px; background:#ffffff; padding:18px; box-sizing:border-box; min-height:230px; display:flex; flex-direction:column; justify-content:space-between; }
        .print-card h1 { margin:0; font-size:18px; }
        .print-card p { margin:4px 0; color:#475569; font-size:13px; }
        .barcode-svg { width:100%; height:60px; }
        .barcode-number { margin-top:10px; font-size:14px; font-weight:700; text-align:center; word-break:break-all; }
      </style>
    `;

    printWindow.document.write('<html><head><title>Barcode Cards</title>' + styles + '</head><body>');
    printWindow.document.write('<div class="print-grid">' + grid.innerHTML + '</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();

    printWindow.onload = function() {
      printWindow.focus();
      printWindow.print();
    };
  }

  // Add library script for barcode previews
</script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
  // Add loading state to verify and reject buttons once DOM is ready
  document.addEventListener('DOMContentLoaded', function() {
    showPanel('<?php echo e(session('active_panel', 'dashboard')); ?>');
    renderBarcodePreviews();
    document.querySelectorAll('.verify-form').forEach(form => {
      form.addEventListener('submit', function(e) {
        const btn = this.querySelector('.verify-btn');
        if (btn) { btn.disabled = true; btn.textContent = '⏳ Processing...'; }
      });
    });
    document.querySelectorAll('.reject-form').forEach(form => {
      form.addEventListener('submit', function(e) {
        const btn = this.querySelector('.reject-btn');
        if (btn) { btn.disabled = true; btn.textContent = '⏳ Processing...'; }
      });
    });
  });
</script>
<!-- AJAX for Book Actions (Update & Archive) -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('adminBooksTable');
    if (!table) return;

    table.querySelectorAll('tbody tr').forEach(row => {
      const actionsCell = row.querySelector('td:last-child');
      if (!actionsCell) return;

      // ─── UPDATE FORM HANDLER ───
      const updateForm = actionsCell.querySelector('form[style*="grid"]');
      if (updateForm) {
        updateForm.addEventListener('submit', async function (e) {
          e.preventDefault();
          const submitBtn = updateForm.querySelector('button[type="submit"]');
          const originalText = submitBtn ? submitBtn.textContent : 'Update';

          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = '💾 Saving...';
          }

          try {
            // Get the actual input values (not hidden fields)
            const copiesInput = updateForm.querySelector('input[name="copies"]');

            // Hidden field references
            const titleHidden = updateForm.querySelector('input[name="title"][type="hidden"]');
            const authorHidden = updateForm.querySelector('input[name="author"][type="hidden"]');
            const barcodeHidden = updateForm.querySelector('input[name="barcode"][type="hidden"]');
            const callNumberHidden = updateForm.querySelector('input[name="call_number"][type="hidden"]');
            const accessionHidden = updateForm.querySelector('input[name="accession_number"][type="hidden"]');
            const categoryHidden = updateForm.querySelector('input[name="category"][type="hidden"]');
            const locationHidden = updateForm.querySelector('input[name="section_location"][type="hidden"]');
            const isbnHidden = updateForm.querySelector('input[name="isbn"][type="hidden"]');
            const summaryHidden = updateForm.querySelector('input[name="summary"][type="hidden"]');

            // Create FormData with all current values
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
            formData.append('title', titleHidden?.value || '');
            formData.append('author', authorHidden?.value || '');
            formData.append('barcode', barcodeHidden?.value || '');
            formData.append('call_number', callNumberHidden?.value || '');
            formData.append('accession_number', accessionHidden?.value || '');
            formData.append('category', categoryHidden?.value || '');
            formData.append('section_location', locationHidden?.value || '');
            formData.append('isbn', isbnHidden?.value || '');
            formData.append('summary', summaryHidden?.value || '');
            formData.append('copies', copiesInput?.value || '1');

            console.log('Sending Update Data:');
            for (let [key, value] of formData.entries()) {
              console.log(`${key}: ${value}`);
            }

            const response = await fetch(updateForm.action, {
              method: 'POST',
              body: formData,
              credentials: 'same-origin'
            });

            console.log('Response Status:', response.status);
            const responseText = await response.text();
            console.log('Response Body:', responseText);

            if (!response.ok) {
              let errorMsg = `Server error: ${response.status}`;
              try {
                const json = JSON.parse(responseText);
                if (json.message) errorMsg = json.message;
                if (json.errors) errorMsg = Object.values(json.errors)[0];
              } catch (e) {}
              throw new Error(errorMsg);
            }

            // Success: flash green
            row.style.transition = 'background-color 0.3s ease';
            row.style.backgroundColor = '#ecfdf5';
            setTimeout(() => { row.style.backgroundColor = ''; }, 700);
            showToast('✅ Book updated successfully (copies: ' + (copiesInput?.value || '1') + ')', '#0f766e');

          } catch (error) {
            console.error('Update Error:', error);
            showToast(`❌ ${error.message}`, '#dc2626');
          } finally {
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = originalText;
            }
          }
        });
      }

      // ─── ARCHIVE FORM HANDLER ───
      const archiveForm = actionsCell.querySelector('form[style*="margin-top"]');
      if (archiveForm) {
        archiveForm.addEventListener('submit', async function (e) {
          e.preventDefault();
          const submitBtn = archiveForm.querySelector('button[type="submit"]');
          const originalText = submitBtn ? submitBtn.textContent : 'Archive';

          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = '🗂️ Archiving...';
          }

          try {
            const formData = new FormData(archiveForm);

            const response = await fetch(archiveForm.action, {
              method: 'POST',
              body: formData,
              credentials: 'same-origin'
            });

            console.log('Archive Response Status:', response.status);

            if (!response.ok) {
              let errorMsg = `Server error: ${response.status}`;
              try {
                const json = JSON.parse(responseText);
                if (json.message) errorMsg = json.message;
              } catch (e) {}
              throw new Error(errorMsg);
            }

            // Fade out and remove row
            row.style.transition = 'opacity 0.4s ease';
            row.style.opacity = '0';
            setTimeout(() => { row.remove(); }, 400);
            showToast('✅ Book archived successfully', '#0f766e');

          } catch (error) {
            console.error('Archive Error:', error);
            showToast(`❌ ${error.message}`, '#dc2626');
          } finally {
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = originalText;
            }
          }
        });
      }
    });

    function showToast(message, color) {
      const toast = document.createElement('div');
      toast.textContent = message;
      toast.style.cssText = `
        position: fixed;
        right: 18px;
        bottom: 18px;
        background: ${color};
        color: #fff;
        padding: 12px 16px;
        border-radius: 8px;
        z-index: 9999;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
        font-weight: 600;
        font-size: 13px;
      `;
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), 2000);
    }
  });

  /* ── SYSTEM UTILITIES: PASSWORD TOGGLE ── */
  document.querySelectorAll('.password-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const targetId = this.getAttribute('data-target');
      const input = document.getElementById(targetId);
      if (!input) return;
      const isPassword = input.type === 'password';
      input.type = isPassword ? 'text' : 'password';
      this.textContent = isPassword ? '🙈' : '👁';
    });
  });

  /* ── SYSTEM UTILITIES: INLINE VALIDATION ── */
  (function () {
    const emailInput = document.getElementById('sys-email');
    const passwordInput = document.getElementById('sys-password');
    const confirmInput = document.getElementById('sys-password-confirm');
    const passwordHint = document.getElementById('sys-password-hint');
    const confirmHint = document.getElementById('sys-password-confirm-hint');

    function showHint(el, msg, type) {
      if (!el) return;
      el.textContent = msg;
      el.className = 'field-hint ' + type;
      el.style.display = '';
    }
    function hideHint(el) {
      if (!el) return;
      el.style.display = 'none';
      el.className = 'field-hint';
    }
    function markField(input, valid) {
      if (!input) return;
      input.classList.remove('input-valid', 'input-error');
      if (valid === true) input.classList.add('input-valid');
      else if (valid === false) input.classList.add('input-error');
    }

    if (emailInput) {
      emailInput.addEventListener('blur', function () {
        const val = this.value.trim();
        if (val === '') { hideHint(passwordHint); markField(this, null); return; }
        if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
          showHint(passwordHint, '✓ Valid email', 'valid');
          markField(this, true);
        } else {
          markField(this, false);
        }
      });
      emailInput.addEventListener('input', function () {
        if (this.value.trim() === '') { hideHint(passwordHint); markField(this, null); }
      });
    }

    function checkPasswords() {
      if (!passwordInput || !confirmInput) return;
      const pw = passwordInput.value;
      const cp = confirmInput.value;
      if (pw === '' && cp === '') { hideHint(passwordHint); hideHint(confirmHint); markField(passwordInput, null); markField(confirmInput, null); return; }
      if (pw.length > 0 && pw.length < 8) {
        showHint(passwordHint, '⚠ Password must be at least 8 characters.', 'error');
        markField(passwordInput, false);
      } else if (pw.length >= 8) {
        showHint(passwordHint, '✓ Password length OK', 'valid');
        markField(passwordInput, true);
      }
      if (cp !== '') {
        if (cp !== pw) {
          showHint(confirmHint, '⚠ Passwords do not match.', 'error');
          markField(confirmInput, false);
        } else {
          showHint(confirmHint, '✓ Passwords match', 'valid');
          markField(confirmInput, true);
        }
      } else {
        hideHint(confirmHint);
        markField(confirmInput, null);
      }
    }

    if (passwordInput) passwordInput.addEventListener('input', checkPasswords);
    if (confirmInput) confirmInput.addEventListener('input', checkPasswords);

    /* ── DOUBLE-SUBMISSION PREVENTION ── */
    const createUserBtn = document.querySelector('.create-user-btn');
    const createUserForm = document.getElementById('create-user-form');
    if (createUserBtn && createUserForm) {
      createUserForm.addEventListener('submit', function () {
        createUserBtn.disabled = true;
        createUserBtn.textContent = 'Creating...';
        setTimeout(function () {
          if (createUserBtn.disabled) {
            createUserBtn.disabled = false;
            createUserBtn.innerHTML = '<span class="btn-icon-left">+</span> Create User';
          }
        }, 5000);
      });
    }
  })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\LIBRARY-MANAGEMENT-SYSTEM\resources\views/admin-dashboard.blade.php ENDPATH**/ ?>