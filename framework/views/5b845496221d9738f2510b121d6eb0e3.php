<?php $__env->startSection('title', 'Panabo City Library - Visitor Portal'); ?>

<?php
  $borrowings = $borrowings ?? collect();
  $announcements = $announcements ?? collect();
  $fines = $fines ?? collect();

  $sidebar = view('components.sidebar', [
    'brand' => 'Panabo City Library',
    'subtitle' => 'Visitor Portal',
    'avatar' => auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'V',
    'userName' => auth()->user()?->name ?? 'Visitor',
    'userRole' => 'Visitor',
    'activePanel' => session('active_panel', 'dashboard'),
  ]);

  $topbar = view('components.topbar', [
    'title' => 'Visitor Dashboard',
    'subtitle' => 'Welcome back, ' . (auth()->user()?->name ?? 'Visitor'),
  ]);
?>

<?php $__env->startSection('content'); ?>
  <section id="panel-dashboard" class="panel active">
    <div class="grid-3">
      <?php echo $__env->make('components.stat-card', ['label' => 'Borrowed Books', 'value' => $borrowings->count(), 'change' => 'Current loans'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <?php echo $__env->make('components.stat-card', ['label' => 'Announcements', 'value' => $announcements->count(), 'change' => 'Latest updates'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <?php echo $__env->make('components.stat-card', ['label' => 'Outstanding Fines', 'value' => 'PHP ' . number_format($fines->where('status', 'Unpaid')->sum('amount') ?? 0, 2), 'change' => 'Please settle soon'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <div class="grid-2">

      <?php if(!empty($dueSoonBorrowings) && count($dueSoonBorrowings) > 0): ?>
        <div style="margin-bottom:16px;">
          <?php $__env->startComponent('components.card', ['title' => 'Upcoming Due Reminder', 'subtitle' => "Books due within next 3 days"]); ?> 
            <ul style="margin:0;padding-left:18px;">
              <?php $__currentLoopData = $dueSoonBorrowings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $daysLeft = \Carbon\Carbon::parse($b->due_date)->diffInDays(\Carbon\Carbon::now()); ?>
                <li style="margin-bottom:8px;"> <strong><?php echo e($b->title ?? $b->book_barcode); ?></strong> — due in <?php echo e($daysLeft); ?> day<?php echo e($daysLeft > 1 ? 's' : ''); ?> (<?php echo e(\Carbon\Carbon::parse($b->due_date)->format('M d, Y')); ?>)</li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          <?php echo $__env->renderComponent(); ?>
        </div>
      <?php endif; ?>

      <?php $__env->startComponent('components.card', ['title' => 'My Borrowings']); ?>
        <table>
          <thead><tr><th>Book</th><th>Due Date</th><th>Status</th></tr></thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $borrowings->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td><?php echo e($borrow->title ?? $borrow->book_barcode); ?></td>
                <td><?php echo e(\Carbon\Carbon::parse($borrow->due_date)->format('M d, Y')); ?></td>
                <td><span class="status-chip pending"><?php echo e($borrow->status); ?></span></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="3">No borrowed books yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      <?php echo $__env->renderComponent(); ?>

      <?php $__env->startComponent('components.card', ['title' => 'Latest Announcements']); ?>
        <?php $__empty_1 = true; $__currentLoopData = $announcements->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="ann-item">
            <div class="ann-title"><?php echo e($announcement->title); ?></div>
            <div class="ann-body"><?php echo e($announcement->body); ?></div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="ann-item"><div class="ann-body">No announcements available.</div></div>
        <?php endif; ?>
      <?php echo $__env->renderComponent(); ?>
    </div>
  </section>

  <section id="panel-borrowings" class="panel">
    <?php $__env->startComponent('components.card', ['title' => 'My Borrowings']); ?>
      <table>
        <thead><tr><th>Book</th><th>Due Date</th><th>Status</th></tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $borrowings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($borrow->title ?? $borrow->book_barcode); ?></td>
              <td><?php echo e(\Carbon\Carbon::parse($borrow->due_date)->format('M d, Y')); ?></td>
              <td><span class="status-chip pending"><?php echo e($borrow->status); ?></span></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="3">No borrowed books yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    <?php echo $__env->renderComponent(); ?>

    <?php $__env->startComponent('components.card', ['title' => 'Request History', 'subtitle' => 'Track your request status from submission to staff action']); ?>
      <table>
        <thead><tr><th>Book</th><th>Requested At</th><th>Status</th><th>Rejection Reason</th></tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $requestHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($request->title ?? $request->accession_number ?? 'Unknown book'); ?></td>
              <td><?php echo e(\Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A')); ?></td>
              <td><span class="status-chip <?php echo e(strtolower($request->status) === 'approved' || strtolower($request->status) === 'borrowed' ? 'approved' : (strtolower($request->status) === 'rejected' ? 'danger' : 'pending')); ?>"><?php echo e(ucfirst($request->status ?? 'Pending')); ?></span></td>
              <td>
                <?php if(strtolower($request->status ?? '') === 'rejected'): ?>
                  <?php echo e($request->rejection_reason ?? 'No reason provided.'); ?>

                <?php else: ?>
                  <span class="text-muted">—</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4">No request history yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    <?php echo $__env->renderComponent(); ?>
  </section>

  <section id="panel-fines" class="panel">
    <?php $__env->startComponent('components.card', ['title' => 'Fines History']); ?>
      <table>
        <thead><tr><th>Description</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $fines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($fine->description); ?></td>
              <td>PHP <?php echo e(number_format($fine->amount, 2)); ?></td>
              <td><span class="status-chip <?php echo e($fine->status === 'Paid' ? 'returned' : 'pending'); ?>"><?php echo e($fine->status); ?></span></td>
              <td><?php echo e(\Carbon\Carbon::parse($fine->transaction_date)->format('M d, Y')); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4">No fines recorded.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    <?php echo $__env->renderComponent(); ?>
  </section>

  <section id="panel-opac" class="panel">
    <?php $__env->startComponent('components.card', ['title' => 'Browse & Request Books', 'subtitle' => 'Search the library catalog and request books to borrow']); ?>
      <div style="margin-bottom: 20px;">
        <div style="display: flex; gap: 12px; margin-bottom: 16px; align-items: stretch; flex-wrap: wrap;">
          <input type="text" id="opac-search-dashboard" placeholder=" Search by title, author..." style="min-width: 250px; flex-grow: 1; padding: 14px 16px; border: 2px solid #2f9e8f; border-radius: 8px; font-size: 15px; font-family: 'Inter', sans-serif; background: #ffffff; color: #1f2937; outline: none;">
          <select id="opac-filter-type-dashboard" style="min-width: 200px; padding: 14px 16px; border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 15px; background: #ffffff; color: #1f2937; cursor: pointer; outline: none;">
            <option value=""> All Material Types</option>
            <option value="Math (Mathematics)">Math (Mathematics)</option>
            <option value="Science (Science)">Science (Science)</option>
            <option value="History (Historical)">History (Historical)</option>
            <option value="Lit (Literature)">Lit (Literature)</option>
            <option value="Fil (Filipiniana)">Filipiniana</option>
            <option value="Cir (Circulation)">Circulation</option>
            <option value="Gen. Ref.">General References</option>
            <option value="F (Fiction)">Fiction</option>
            <option value="D (Dissertation)">Dissertation</option>
            <option value="T (Thesis)">Thesis</option>
            <option value="Journal">Journal</option>
          </select>
        </div>
        <div id="opac-results-dashboard" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px; max-height: 600px; overflow-y: auto;">
          <!-- Results will be loaded here -->
          <div style="grid-column: 1 / -1; text-align: center; padding: 24px; color: #6b7280;">
            <p>Type above to search for books...</p>
          </div>
        </div>
      </div>
    <?php echo $__env->renderComponent(); ?>
  </section>

  <?php
    $isRegularUser = in_array(auth()->user()?->role, ['student', 'visitor'], true);
  ?>

  <?php if($isRegularUser): ?>
    <section id="panel-barcode-request" class="panel">
      <?php $__env->startComponent('components.card', ['title' => 'Request New Barcode', 'subtitle' => 'Request a replacement barcode if yours is lost or damaged']); ?>
        <div style="margin-bottom: 20px;">
          <form method="POST" action="<?php echo e(route('visitor.barcode-request.store')); ?>" enctype="multipart/form-data" style="display: grid; grid-template-columns: 1fr; gap: 12px; margin-bottom: 20px;">
            <?php echo csrf_field(); ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div class="form-group">
              <label style="display: block; font-size: 11px; font-weight: 700; color: #1f2937; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Current Barcode (if available)</label>
              <input type="text" name="old_barcode_id" placeholder="Your current barcode ID (optional)" style="width: 100%; padding: 9px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif;">
            </div>
            <div class="form-group">
              <label style="display: block; font-size: 11px; font-weight: 700; color: #1f2937; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Reason</label>
              <select name="reason" required style="width: 100%; padding: 9px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif;">
                <option value="">Select reason</option>
                <option value="lost">Lost</option>
                <option value="damaged">Damaged</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label style="display: block; font-size: 11px; font-weight: 700; color: #1f2937; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Additional Notes</label>
            <textarea name="reason_details" placeholder="Provide any additional details about your request..." style="width: 100%; padding: 9px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; resize: vertical; min-height: 80px;"></textarea>
          </div>
            <div class="form-group">
              <label style="display: block; font-size: 11px; font-weight: 700; color: #1f2937; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Proof Upload (optional)</label>
              <input type="file" name="proof" accept="image/*,.pdf,.doc,.docx,.txt" style="width: 100%; padding: 9px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; background: white;">
            </div>
            <div style="text-align: right;">
              <button type="submit" style="padding: 10px 22px; background: #2f9e8f; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif;">Submit Request</button>
            </div>
          </form>

          <div>
            <div style="font-size: 12px; font-weight: 600; color: #1f2937; margin-bottom: 12px; text-transform: uppercase;">Your Barcode Requests</div>
            <?php if($barcodeRequests && count($barcodeRequests) > 0): ?>
              <table style="width: 100%; border-collapse: collapse;">
                <thead>
                  <tr style="border-bottom: 1px solid #e5e7eb;">
                    <th style="padding: 12px 14px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; color: #1f2937; font-weight: 700; text-align: left;">Old Barcode</th>
                    <th style="padding: 12px 14px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; color: #1f2937; font-weight: 700; text-align: left;">Reason</th>
                    <th style="padding: 12px 14px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; color: #1f2937; font-weight: 700; text-align: left;">Status</th>
                    <th style="padding: 12px 14px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; color: #1f2937; font-weight: 700; text-align: left;">New Barcode</th>
                    <th style="padding: 12px 14px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; color: #1f2937; font-weight: 700; text-align: left;">Requested At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $__empty_1 = true; $__currentLoopData = $barcodeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                      <td style="padding: 14px 14px; font-size: 13px; color: #1f2937;"><?php echo e($request->old_barcode_id ?? '—'); ?></td>
                      <td style="padding: 14px 14px; font-size: 13px; color: #1f2937;">
                        <div><?php echo e(ucfirst($request->reason)); ?></div>
                        <?php if(!empty($request->reason_details)): ?>
                          <div style="font-size: 12px; color: #6b7280; margin-top: 4px;"><?php echo e($request->reason_details); ?></div>
                        <?php endif; ?>
                        <?php if(!empty($request->proof_path)): ?>
                          <div style="margin-top: 6px;"><a href="<?php echo e(asset('storage/' . $request->proof_path)); ?>" target="_blank" rel="noopener">View proof</a></div>
                        <?php endif; ?>
                      </td>
                      <td style="padding: 14px 14px; font-size: 13px; color: #1f2937;">
                        <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: <?php echo e($request->status === 'approved' ? 'rgba(34, 197, 94, 0.12); color: #22c55e' : ($request->status === 'pending' ? 'rgba(243, 156, 18, 0.15); color: #f59e0b' : 'rgba(239, 68, 68, 0.12); color: #ef4444')); ?>;">
                          <?php echo e(ucfirst($request->status)); ?>

                        </span>
                      </td>
                      <td style="padding: 14px 14px; font-size: 13px; color: #1f2937; font-weight: 600;"><?php echo e($request->new_barcode_id ?? '—'); ?></td>
                      <td style="padding: 14px 14px; font-size: 13px; color: #6b7280;"><?php echo e(\Carbon\Carbon::parse($request->created_at)->format('M d, Y')); ?></td>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                      <td colspan="5" style="text-align: center; padding: 20px; color: #6b7280;">No barcode requests yet.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            <?php else: ?>
              <div style="text-align: center; padding: 20px; color: #6b7280; border: 1px solid #e5e7eb; border-radius: 8px;">
                You haven't requested a new barcode yet.
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php echo $__env->renderComponent(); ?>
    </section>
  <?php endif; ?>
<?php $__env->stopSection(); ?>

<style>
  .opac-book-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .opac-book-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .opac-card-cover {
    height: 140px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: 700;
    overflow: hidden;
    position: relative;
  }

  .opac-card-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .opac-availability-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
  }

  .opac-available {
    background: #22c55e;
    color: white;
  }

  .opac-borrowed {
    background: #ef4444;
    color: white;
  }

  .opac-card-body {
    padding: 12px;
  }

  .opac-card-title {
    font-size: 13px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .opac-card-author {
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 8px;
  }

  .opac-card-footer {
    display: flex;
    gap: 8px;
    margin-top: 8px;
  }

  .opac-borrow-btn {
    flex: 1;
    padding: 6px 12px;
    background: #2f9e8f;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }

  .opac-borrow-btn:hover {
    background: #26867a;
  }

  .opac-borrow-btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
  }

  .search-input {
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    color: #1f2937;
  }

  .search-input::placeholder {
    color: #9ca3af;
  }

  .filter-select {
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    color: #1f2937;
    background: white;
    cursor: pointer;
  }

  @keyframes spin {
    to { transform: rotate(360deg); }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const searchInputDashboard = document.getElementById('opac-search-dashboard');
    const filterTypeDashboard = document.getElementById('opac-filter-type-dashboard');
    const resultsContainerDashboard = document.getElementById('opac-results-dashboard');
    let debounceTimer;

    if (!searchInputDashboard || !filterTypeDashboard) {
      console.error('Search elements not found');
      return;
    }

    const catColors = {
      'Fil (Filipiniana)': ['#2F9E8F','#1a7a6e'],
      'Cir (Circulation)': ['#3B82F6','#2563EB'],
      'Gen. Ref.': ['#8B5CF6','#7C3AED'],
      'F (Fiction)': ['#F59E0B','#D97706'],
      'D (Dissertation)': ['#EF4444','#DC2626'],
      'T (Thesis)': ['#10B981','#059669'],
      'Journal': ['#14B8A6','#0F766E'],
    };

    function doSearchDashboard() {
      const query = searchInputDashboard.value.trim();
      if (query.length === 0) {
        resultsContainerDashboard.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 24px; color: #6b7280;"><p>Type above to search for books...</p></div>';
        return;
      }

      const params = new URLSearchParams();
      params.set('q', query);
      if (filterTypeDashboard.value) {
        params.set('type', filterTypeDashboard.value);
      }

      resultsContainerDashboard.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 24px;"><div style="display: inline-block; width: 24px; height: 24px; border: 3px solid #e5e7eb; border-top-color: #2f9e8f; border-radius: 50%; animation: spin 0.6s linear infinite;"></div></div>';

      fetch('/opac/search?' + params.toString())
        .then(r => r.json())
        .then(data => {
          if (data.results.length === 0) {
            resultsContainerDashboard.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 24px; color: #6b7280;"><p>No books found matching your search.</p></div>';
            return;
          }
          resultsContainerDashboard.innerHTML = data.results.map(book => renderBookCardDashboard(book)).join('');
        })
        .catch(err => {
          console.error(err);
          resultsContainerDashboard.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; padding: 24px; color: #ef4444;"><p>Search error. Please try again.</p></div>';
        });
    }

    function renderBookCardDashboard(book) {
      const available = (book.available || 0) > 0;
      let coverHtml;
      if (book.cover_url) {
        coverHtml = '<img src="' + book.cover_url + '" alt="' + escapeHtml(book.title) + '">';
      } else {
        const colors = catColors[book.category] || ['#6B7280','#4B5563'];
        coverHtml = '<div style="background: linear-gradient(135deg, ' + colors[0] + ', ' + colors[1] + '); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: 700; color: white;">' + (book.title ? book.title.charAt(0).toUpperCase() : '?') + '</div>';
      }
      return `
        <div class="opac-book-card">
          <div class="opac-card-cover">
            ${coverHtml}
            <span class="opac-availability-badge ${available ? 'opac-available' : 'opac-borrowed'}">${available ? 'Available' : 'Borrowed'}</span>
          </div>
          <div class="opac-card-body">
            <div class="opac-card-title">${escapeHtml(book.title || 'Untitled')}</div>
            <div class="opac-card-author">${escapeHtml(book.author || 'Unknown')}</div>
            <div class="opac-card-footer">
              ${available ? '<button class="opac-borrow-btn" onclick="borrowBook(' + book.id + ')">Request Book</button>' : '<button class="opac-borrow-btn" disabled>Not Available</button>'}
            </div>
          </div>
        </div>`;
    }

    window.borrowBook = async function(bookId) {
      const ok = await showConfirm('Are you sure you want to request this book for borrowing?');
      if (!ok) return;

      fetch('/opac/request-borrow', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        },
        body: JSON.stringify({ book_id: bookId })
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          alert('✓ ' + data.message);
          doSearchDashboard();
        } else {
          alert('✗ ' + (data.error || 'Error requesting book'));
        }
      })
      .catch(err => {
        console.error(err);
        alert('Error requesting book. Please try again.');
      });
    };

    function escapeHtml(str) {
      const div = document.createElement('div');
      div.textContent = str;
      return div.innerHTML;
    }

    searchInputDashboard.addEventListener('input', function() {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(doSearchDashboard, 300);
    });

    filterTypeDashboard.addEventListener('change', doSearchDashboard);
  });

  @keyframes spin {
    to { transform: rotate(360deg); }
  }
</script>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\LIBRARY-MANAGEMENT-SYSTEM\resources\views/visitor-dashboard.blade.php ENDPATH**/ ?>