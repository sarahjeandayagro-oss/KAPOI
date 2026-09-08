@extends('layouts.app')

@section('title', 'Panabo City Library - Staff Portal')

@php
  $pendingResearchers = $pendingResearchers ?? collect();
  $pendingStudents = $pendingStudents ?? collect();
  $repeatVisitors = $repeatVisitors ?? collect();
  $wifiConsumers = $wifiConsumers ?? collect();
  $realtimeAttendance = $realtimeAttendance ?? collect();
  $staffAttendanceSummary = $staffAttendanceSummary ?? collect();

  $sidebar = view('components.sidebar', [
    'brand' => 'Panabo City Library',
    'subtitle' => 'Staff Portal',
    'avatar' => auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'S',
    'userName' => auth()->user()?->name ?? 'Staff',
    'userRole' => 'Staff',
    'activePanel' => session('active_panel', 'dashboard'),
  ]);

  $topbar = view('components.topbar', [
    'title' => 'Staff Dashboard',
    'subtitle' => 'Library Management Console',
  ]);
@endphp

@section('content')
  @push('head')
    <style>
      .borrow-modal-backdrop {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, 0.55);
        z-index: 9999;
      }
      .borrow-modal-backdrop.open {
        display: flex;
      }
      .borrow-modal {
        width: min(100%, 680px);
        border-radius: 20px;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 24px 80px rgba(15, 23, 42, 0.24);
      }
      .borrow-modal .modal-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        padding: 22px 24px;
        background: #0f766e;
        color: #ffffff;
      }
      .borrow-modal .modal-head h2 {
        margin: 0;
        font-size: 1.05rem;
        letter-spacing: 0.01em;
        font-weight: 700;
      }
      .borrow-modal .modal-close {
        background: transparent;
        border: none;
        color: #ffffff;
        font-size: 28px;
        line-height: 1;
        cursor: pointer;
      }
      .borrow-modal .modal-body {
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 24px;
        padding: 24px;
        background: #ffffff;
      }
      .borrow-modal .photo {
        min-height: 240px;
        border-radius: 18px;
        background: #ecfdf5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0f766e;
        font-size: 0.95rem;
        font-weight: 700;
        overflow: hidden;
        text-align: center;
      }
      .borrow-modal .photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      .borrow-modal .details {
        display: grid;
        gap: 18px;
      }
      .borrow-modal .detail-row {
        display: grid;
        grid-template-columns: 120px 1fr;
        gap: 10px;
      }
      .borrow-modal .detail-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.08em;
      }
      .borrow-modal .detail-value {
        font-size: 0.98rem;
        color: #0f172a;
        font-weight: 700;
        line-height: 1.4;
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
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #111827;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        min-width: 180px;
        text-align: center;
      }

      .borrow-request-tab-button:hover {
        background: #f8fafc;
      }

      .borrow-request-tab-button.active {
        background: #2f9e8f;
        color: #ffffff;
        border-color: #2f9e8f;
        box-shadow: 0 8px 20px rgba(47, 158, 143, 0.12);
      }

      .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 28px; }
      .stat-card {
        background: #ffffff;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 18px;
        padding: 22px;
        position: relative;
        overflow: hidden;
        color: #0f172a;
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.06);
        transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
      }
      .stat-card:hover { transform: translateY(-2px); border-color: rgba(59, 130, 246, 0.2); box-shadow: 0 20px 45px rgba(15, 23, 42, 0.1); }
      .stat-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; }
      .stat-card.gold::before { background: #f59e0b; }
      .stat-card.blue::before { background: #3b82f6; }
      .stat-card.green::before { background: #22c55e; }
      .stat-card.red::before { background: #ef4444; }
      .stat-card.primary::before { background: #6366f1; }
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
      .stat-label { font-size: 11px; color: #64748b; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 10px; }
      .stat-value { font-size: 32px; font-weight: 800; line-height: 1; margin-bottom: 8px; }
      .stat-change { font-size: 13px; color: #475569; opacity: 0.95; }
      .stat-icon { position: absolute; top: 18px; right: 18px; font-size: 20px; opacity: 0.16; }
      .stat-card .stat-icon { display:flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:10px; background: rgba(15,23,42,0.02); font-size:18px; }

      .grid-2, .grid-3 { display: grid; gap: 20px; margin-bottom: 24px; }
      .grid-2 { grid-template-columns: 1fr 1fr; }
      .grid-3 { grid-template-columns: 2fr 1fr; }
      .dashboard-stacked-layout { display: grid; gap: 20px; margin-bottom: 24px; }
      .staff-overview-row { display: grid; grid-template-columns: 1fr 1.4fr; gap: 20px; margin-bottom: 20px; align-items: stretch; }
      .staff-activity-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; align-items: stretch; }
      .staff-books-overview { min-width: 30%; }
      .staff-activity-log { min-width: 0; }
      .card { border-radius: 18px; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06); overflow: hidden; }
      .card-header { padding: 20px 24px; border-bottom: 1px solid rgba(229, 231, 235, 0.95); background: #f8fafc; display: flex; justify-content: space-between; align-items: center; }
      .card-title { font-size: 15px; font-weight: 800; color: #111827; }
      .card-sub { font-size: 13px; color: #6b7280; margin-top: 4px; }
      .card-body { padding: 22px; background: #ffffff; color: #0f172a; }
      .mini-chart { width: 100%; height: 120px; display: block; border-radius: 16px; overflow: hidden; background: #ecfdf5; }
      .activity-list { display: flex; flex-direction: column; gap: 14px; }
      .activity-item { display: flex; align-items: flex-start; gap: 14px; padding: 14px; border-radius: 14px; background: #f8fafc; }
      .activity-dot { width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; font-size: 12px; color: white; font-weight: 700; flex-shrink: 0; }
      .activity-dot.blue { background: #3b82f6; }
      .activity-dot.green { background: #22c55e; }
      .activity-dot.gold { background: #f59e0b; }
      .activity-text { font-size: 14px; color: #0f172a; line-height: 1.6; }
      .activity-time { font-size: 12px; color: #6b7280; margin-top: 4px; }

      .recent-borrowings-list { display: flex; flex-direction: column; gap: 12px; }
      .recent-borrowing-item { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; padding: 16px 18px; border-radius: 14px; background: #f8fafc; border: 1px solid rgba(226, 232, 240, 0.8); }
      .recent-borrowing-book { font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 6px; }
      .recent-borrowing-meta { font-size: 12px; color: #6b7280; }
      .recent-borrowing-status { font-size: 13px; font-weight: 700; color: #111827; text-align: right; flex-shrink: 0; white-space: nowrap; }
      .empty-state { padding: 18px 12px; text-align: center; color: #64748b; border-radius: 12px; background: #f8fafc; }

      @media (max-width: 740px) {
        .borrow-modal .modal-body {
          grid-template-columns: 1fr;
        }
      }
    </style>
  @endpush

  @if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="alert error">{{ $errors->first() }}</div>
  @endif

  @if(session('visitor_barcode'))
    @php
      $visitorPass = session('visitor_barcode');
    @endphp
    @component('components.card', ['title' => 'Visitor Barcode Generated', 'subtitle' => 'Give this pass to the visitor'])
      <div class="pass-card">
        <div>
          <div class="pass-name">{{ $visitorPass['full_name'] }}</div>
          <div class="text-muted">{{ $visitorPass['school'] }} · {{ $visitorPass['created_at'] }}</div>
        </div>
        <div class="pass-code">{{ $visitorPass['barcode_id'] }}</div>
      </div>
    @endcomponent
  @endif


   <section id="panel-dashboard" class="panel active dashboard-stats-section">
    <div class="stats-grid">
      <!--@include('components.stat-card', ['label' => 'Pending Verifications', 'value' => count($pendingUsers), 'change' => 'Awaiting approval', 'tone' => 'gray', 'icon' => '🕒'])-->
      @include('components.stat-card', ['label' => 'Total Books', 'value' => $totalBooks ?? 0, 'change' => 'In collection', 'tone' => 'blue', 'icon' => '📚'])
      @include('components.stat-card', ['label' => 'Active Borrowers', 'value' => $activeBorrowers ?? 0, 'change' => 'Currently borrowed', 'tone' => 'orange', 'icon' => '👥'])
      @include('components.stat-card', ['label' => 'Overdue Books', 'value' => $overdueBooks ?? 0, 'change' => 'Past due date', 'tone' => 'red', 'icon' => '⚠️'])
      @include('components.stat-card', ['label' => 'Returned Today', 'value' => $returnedToday ?? 0, 'change' => 'Books returned', 'tone' => 'yellow', 'icon' => '🔁'])
      @include('components.stat-card', ['label' => 'Total Visitors (June)', 'value' => $totalVisitorsThisMonth ?? 0, 'change' => 'This month', 'tone' => 'brown', 'icon' => '👣'])
    </div>

    <div class="staff-overview-row">
      <div class="card card-compact staff-books-overview" style="width:auto; min-width: 30%;">
        <div class="card-header">
          <div><div class="card-title">Books Overview</div><div class="card-sub">Current collection status</div></div>
        </div>
        <div class="card-body" style="width: fit-content; min-width: 0;">
          <x-books-overview-card :books="$books" />
        </div>
      </div>

      @component('components.card', ['title' => 'Gender Distribution'])
        <div style="display: flex; gap: 20px; align-items: center;">
          <div>
            <div style="font-size: 24px; font-weight: 700; color: var(--text);">{{ $femalePct ?? 0 }}%</div>
            <div style="font-size: 12px; color: var(--muted);">Female ({{ $femaleCount ?? 0 }})</div>
          </div>
          <div style="flex: 1; height: 8px; background: var(--border); border-radius: 4px; overflow: hidden;">
            <div style="height: 100%; width: {{ $femalePct ?? 0 }}%; background: #ec4899;"></div>
          </div>
        </div>
        <div style="display: flex; gap: 20px; align-items: center; margin-top: 16px;">
          <div>
            <div style="font-size: 24px; font-weight: 700; color: var(--text);">{{ $malePct ?? 0 }}%</div>
            <div style="font-size: 12px; color: var(--muted);">Male ({{ $maleCount ?? 0 }})</div>
          </div>
          <div style="flex: 1; height: 8px; background: var(--border); border-radius: 4px; overflow: hidden;">
            <div style="height: 100%; width: {{ $malePct ?? 0 }}%; background: #3b82f6;"></div>
          </div>
        </div>
      @endcomponent
    </div>

    <div class="staff-activity-row">
      <div class="card staff-activity-log">
        <div class="card-header"><div class="card-title">Recent Activity Log</div></div>
        <div class="card-body">
          <div class="activity-list" style="gap:8px;">
            @forelse($recentActivity as $activity)
              <div class="activity-item">
                <div class="activity-dot {{ $activity->type === 'borrow' ? 'blue' : ($activity->type === 'entry' ? 'green' : 'gold') }}">
                  {{ ucfirst($activity->type) }}
                </div>
                <div>
                  <div class="activity-text">{!! $activity->text !!}</div>
                  <div class="activity-time">{{ $activity->time }}</div>
                </div>
              </div>
            @empty
              <div class="activity-item">
                <div class="activity-dot gold">Info</div>
                <div>
                  <div class="activity-text">No recent activity available yet.</div>
                  <div class="activity-time">—</div>
                </div>
              </div>
            @endforelse
          </div>
        </div>
      </div>

      @component('components.card', ['title' => 'Recent Borrowings', 'subtitle' => 'Latest book issues on file'])
        <div class="recent-borrowings-list">
          @forelse($recentBorrowings ?? collect() as $borrow)
            <div class="recent-borrowing-item" style="display:flex; align-items:center; gap:12px;">
              <img
                src="{{ $borrow->cover_image ? asset('storage/' . ltrim($borrow->cover_image, '/')) : asset('images/library logos.jpg') }}"
                alt="{{ $borrow->title ?? 'Book cover' }}"
                style="width:48px; height:64px; object-fit:cover; border-radius:8px; border:1px solid rgba(148,163,184,0.35); background:#f8fafc;"
              >
              <div style="flex:1; min-width:0;">
                <div class="recent-borrowing-book" style="font-weight:700; color:#0f172a;">{{ $borrow->title ?? 'Unknown' }}</div>
                <div class="recent-borrowing-meta" style="margin-top:4px; color:#6b7280;">{{ $borrow->borrower_name ?? $borrow->member_name ?? 'Member' }}</div>
              </div>
              <div class="recent-borrowing-status" style="white-space:nowrap; text-align:right;">
                {{ $borrow->borrower_name ?? $borrow->member_name ?? $borrow->barcode_id ?? '—' }}
              </div>
            </div>
          @empty
            <div class="empty-state">No recent borrowings.</div>
          @endforelse
        </div>
      @endcomponent
    </div>
  </section>


  <section id="panel-users" class="panel">
    @component('components.card', ['title' => 'User Management', 'subtitle' => 'All users, verification and barcode actions'])
      <table>
        <thead><tr><th>User ID</th><th>Name</th><th>Barcode</th><th>Role</th><th>Status</th><th>Rejection Reason</th><th>Verified At</th></tr></thead>
        <tbody>
          @forelse($allUsers as $userRow)
            <tr>
              <td>{{ $userRow->user_id }}</td>
              <td>{{ $userRow->name }}<br><span class="text-muted">{{ $userRow->email }}</span></td>
              <td>{{ $userRow->barcode_id ?? '—' }}</td>
              <td>{{ ucfirst($userRow->role) }}</td>
              <td><span class="status-chip {{ $userRow->status === 'approved' ? 'approved' : ($userRow->status === 'rejected' ? 'danger' : 'pending') }}">{{ ucfirst($userRow->status ?? 'pending') }}</span></td>
              <td>{{ $userRow->status === 'rejected' ? ($userRow->rejection_reason ?? 'No reason provided.') : '—' }}</td>
              <td>{{ $userRow->updated_at ? \Carbon\Carbon::parse($userRow->updated_at)->format('M d, Y h:i A') : 'N/A' }}</td>
            </tr>
          @empty
            <tr><td colspan="6">No users found.</td></tr>
          @endforelse
        </tbody>
      </table>
    @endcomponent
  </section>

  <section id="panel-verify" class="panel">
    @component('components.card', ['title' => 'Pending Account Verifications', 'subtitle' => 'Student, researcher, and visitor accounts waiting for approval'])
      <table>
        <thead><tr><th>Name</th><th>Barcode ID</th><th>School</th><th>Role</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
          @forelse($pendingUsers as $pendingUser)
            <tr>
              <td>{{ $pendingUser->name }}<br><span class="text-muted">{{ $pendingUser->email }}</span></td>
              <td>{{ $pendingUser->barcode_id ?? 'Unassigned' }}</td>
              <td>{{ $pendingUser->school ?? 'N/A' }}</td>
              <td>{{ ucfirst($pendingUser->role) }}</td>
              <td><span class="status-chip pending">Pending</span></td>
              <td>
                <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                  <form method="POST" action="{{ route('admin.verify-researcher') }}" class="verify-form">
                    @csrf
                    <input type="hidden" name="lookup" value="{{ $pendingUser->user_id ?? $pendingUser->email ?? $pendingUser->id }}">
                    <button class="btn-primary verify-btn" type="submit" data-original-text="Verify">Verify</button>
                  </form>
                  <form method="POST" action="{{ route('admin.reject-researcher') }}" class="reject-form" style="display:flex; flex-direction:column; gap:8px; max-width:280px; width:100%;">
                    @csrf
                    <input type="hidden" name="lookup" value="{{ $pendingUser->user_id ?? $pendingUser->email ?? $pendingUser->id }}">
                    <textarea name="rejection_reason" placeholder="Reason for rejection" required style="width:100%; min-height:72px; padding:10px; border:1px solid #d1d5db; border-radius:10px; font-size:0.95rem; resize:vertical;"></textarea>
                    <button class="btn-sm danger reject-btn" type="submit">Reject</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6">No pending accounts to verify.</td></tr>
          @endforelse
        </tbody>
      </table>
    @endcomponent

    @component('components.card', ['title' => 'Rejected Account Verifications', 'subtitle' => 'Rejected accounts and their rejection reasons'])
      <table>
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Reason for Rejection</th><th>Rejected At</th></tr></thead>
        <tbody>
          @forelse($rejectedUsers ?? [] as $rejectedUser)
            <tr>
              <td>{{ $rejectedUser->name }}</td>
              <td>{{ $rejectedUser->email }}</td>
              <td>{{ ucfirst($rejectedUser->role) }}</td>
              <td>{{ $rejectedUser->rejection_reason ?? 'No reason provided.' }}</td>
              <td>{{ $rejectedUser->updated_at ? \Carbon\Carbon::parse($rejectedUser->updated_at)->format('M d, Y h:i A') : 'N/A' }}</td>
            </tr>
          @empty
            <tr><td colspan="5">No rejected accounts yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    @endcomponent
  </section>

  <section id="panel-verified" class="panel">
    @component('components.card', ['title' => 'Recently Verified Accounts', 'subtitle' => 'Approved accounts now visible immediately on staff dashboard'])
      <table>
        <thead><tr><th>Name</th><th>Barcode</th><th>Role</th><th>Verified At</th></tr></thead>
        <tbody>
          @forelse($verifiedUsers as $verifiedUser)
            <tr>
             <!-- <td>{{ $verifiedUser->user_id }}</td> -->
              <td>{{ $verifiedUser->name }}<br><span class="text-muted">{{ $verifiedUser->email }}</span></td>
              <td>
                @if($verifiedUser->barcode_id)
                  <div style="display:flex; flex-direction:column; align-items:center; gap:6px; max-width:170px;">
                    <svg class="barcode-svg" data-code="{{ $verifiedUser->barcode_id }}" aria-hidden="true" style="width:160px; height:45px;"></svg>
                    <div style="font-size:0.78rem; color:#334155; text-align:center; word-break:break-all;">{{ $verifiedUser->barcode_id }}</div>
                  </div>
                @else
                  <span class="text-muted">Unassigned</span>
                @endif
              </td>
              <td>{{ ucfirst($verifiedUser->role) }}</td>
              <td>{{ $verifiedUser->updated_at ? \Carbon\Carbon::parse($verifiedUser->updated_at)->format('M d, Y h:i A') : 'N/A' }}</td>
            </tr>
          @empty
            <tr><td colspan="5">No verified accounts yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    @endcomponent
  </section>

  <section id="panel-barcode-cards" class="panel">
    <div class="card">
      <div class="card-header"><div class="card-title">Barcode Cards</div><div class="card-sub">Printable barcode ID cards for verified accounts</div></div>
      <div class="card-body" style="padding:24px;">
        <div style="display:flex; justify-content:space-between; flex-wrap:wrap; align-items:center; gap:12px;">
          <div style="max-width:640px; color:#475569;">Generate barcode cards for recently approved users. Each card includes the user's name, role, barcode image, and barcode ID.</div>
          <button class="btn-primary" type="button" onclick="printBarcodeCards()">Print Barcode Cards</button>
        </div>
        <div id="barcodeCardsGrid" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:16px; margin-top:20px;">
          @forelse($verifiedUsers as $verifiedUser)
            <div style="padding:18px; border:1px solid #e2e8f0; border-radius:18px; background:#ffffff; display:flex; flex-direction:column; justify-content:space-between; min-height:240px;">
              <div>
                <div style="font-size:0.78rem; font-weight:700; color:#0f766e; text-transform:uppercase; letter-spacing:0.08em;">{{ ucfirst($verifiedUser->role) }}</div>
                <div style="font-size:1.05rem; font-weight:700; margin-top:10px; line-height:1.2;">{{ $verifiedUser->name }}</div>
                <div style="font-size:0.85rem; color:#64748b; margin-top:6px; word-break:break-word;">{{ $verifiedUser->email }}</div>
              </div>
              <div style="margin-top:18px;">
                <svg class="barcode-svg" data-code="{{ $verifiedUser->barcode_id ?? $verifiedUser->user_id }}" aria-hidden="true" style="width:100%; height:50px;"></svg>
                <div style="margin-top:12px; font-size:0.92rem; font-weight:700; text-align:center; color:#0f172a; word-break:break-all;">{{ $verifiedUser->barcode_id ?? $verifiedUser->user_id }}</div>
              </div>
              <div style="margin-top:16px; display:flex; justify-content:space-between; align-items:center; color:#475569; font-size:0.82rem;">
                <div>Verified {{ $verifiedUser->updated_at ? \Carbon\Carbon::parse($verifiedUser->updated_at)->format('M d, Y') : 'N/A' }}</div>
                @if($verifiedUser->barcode_id)
                  <div style="padding:4px 10px; background:#f8fafc; border-radius:999px; font-size:0.72rem; font-weight:700;">Barcode Ready</div>
                @endif
              </div>
            </div>
          @empty
            <div style="grid-column:1/-1; padding:18px; color:#64748b;">No verified accounts available. Once users are approved, this page will show barcode cards ready to print.</div>
          @endforelse
        </div>
      </div>
    </div>
  </section>

  @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
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

      function showBorrowModal() {
        const modal = document.getElementById('borrowModal');
        if (!modal) return;
        modal.classList.add('open');
      }

      function closeBorrowModal() {
        const modal = document.getElementById('borrowModal');
        if (!modal) return;
        modal.classList.remove('open');
      }

      document.addEventListener('click', function(event) {
        if (event.target.id === 'borrowModal') {
          closeBorrowModal();
        }
      });

      document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
          closeBorrowModal();
        }
      });

      document.addEventListener('DOMContentLoaded', function() {
        showPanel('{{ session('active_panel', 'dashboard') }}');
        renderBarcodePreviews();

        if ({{ session('borrow_popup') || session('return_popup') ? 'true' : 'false' }}) {
          showBorrowModal();
        }

        // Add loading state to verify buttons
        document.querySelectorAll('.verify-form').forEach(form => {
          form.addEventListener('submit', function(e) {
            const btn = this.querySelector('.verify-btn');
            btn.disabled = true;
            btn.textContent = '⏳ Processing...';
          });
        });
        // Add loading state to reject buttons
        document.querySelectorAll('.reject-form').forEach(form => {
          form.addEventListener('submit', function(e) {
            const btn = this.querySelector('.reject-btn');
            btn.disabled = true;
            btn.textContent = '⏳ Processing...';
          });
        });
      });
    </script>
  @endpush

  <section id="panel-barcodes" class="panel">
    <div class="grid-2">
      @component('components.card', ['title' => 'Create Available Barcode', 'subtitle' => 'Add a new unused barcode to the pool'])
        <form method="POST" action="{{ route('admin.available-barcodes.store') }}" style="display:flex; gap:10px; flex-wrap:wrap; align-items:end;">
          @csrf
          <div class="form-group" style="margin:0; flex:1; min-width:220px;">
            <label>Barcode ID</label>
            <input type="text" name="barcode_id" placeholder="202600300" required>
          </div>
          <button class="btn-primary" type="submit">Create Barcode</button>
        </form>
      @endcomponent

      @component('components.card', ['title' => 'Available Barcode Pool', 'subtitle' => 'Pre-generated barcodes waiting for assignment'])
        <table>
          <thead><tr><th>Barcode ID</th><th>Assigned User</th><th>Status</th><th>Action</th></tr></thead>
          <tbody>
            @forelse($availableBarcodes as $barcode)
              <tr>
                <td><strong>{{ $barcode->barcode_id }}</strong></td>
                <td>{{ $barcode->assigned_user_id ?? 'Unassigned' }}</td>
                <td><span class="status-chip pending">Available</span></td>
                <td>
                  <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <form method="POST" action="{{ route('admin.available-barcodes.update', $barcode->id) }}" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                      @csrf
                      <input type="text" name="barcode_id" value="{{ $barcode->barcode_id }}" style="width:140px;" required>
                      <button class="btn-secondary" type="submit">Update</button>
                    </form>
                    <form method="POST" action="{{ route('admin.available-barcodes.delete', $barcode->id) }}">
                      @csrf
                      <button class="btn-sm danger" type="submit" onclick="return confirm('Delete this available barcode?')">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="4">No available barcodes yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      @endcomponent
    </div>
  </section>

  <section id="panel-books" class="panel">
    @component('components.card', ['title' => 'Book Collection (OPAC)', 'subtitle' => 'Search, add, edit and archive books'])
      <div style="display:flex; gap:12px; margin-bottom:12px; align-items:flex-start;">
        <form id="book-add-form" method="POST" action="{{ route('admin.books.add') }}" enctype="multipart/form-data" style="flex:1; display:grid; grid-template-columns:repeat(2,1fr); gap:8px;">
          @csrf
          <input type="text" name="title" placeholder="Title" required>
          <input type="text" name="author" placeholder="Author" required>
          <input type="text" name="isbn" placeholder="ISBN" required>
          <input type="text" name="call_number" placeholder="Call Number" required>
          <input type="text" name="accession_number" placeholder="Accession No." required>
          <select name="category" required>
            <option value="">Select Category</option>
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
          <textarea name="summary" placeholder="Book Summary" rows="3" style="resize:vertical;"></textarea>
          <input type="file" name="cover_image" accept="image/*">
          <input type="number" name="copies" placeholder="Copies" min="1" required>
          <select name="section_location" required style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px; background:#fff; color:#111827;">
            <option value="">Select Shelf Location</option>
            <option value="Shelf 1">Shelf 1</option>
            <option value="Shelf 2">Shelf 2</option>
            <option value="Shelf 3">Shelf 3</option>
            <option value="Shelf 4">Shelf 4</option>
            <option value="Shelf 5">Shelf 5</option>
            </select>
          <div style="grid-column:1/-1; text-align:right;"><button class="btn-primary" type="submit">Add Book</button></div>
        </form>

        <div style="width:320px;">
          <label>Search OPAC</label>
          <input id="opac-search" type="search" placeholder="Search title, author, ISBN..." style="width:100%; margin-bottom:8px;">
          <div id="opac-results" style="max-height:280px; overflow:auto; border:1px solid #eee; padding:8px; background:#fff;"></div>
          <a href="{{ route('barcode.printer') }}" class="btn-primary" style="display:block; margin-top:8px; text-align:center; text-decoration:none; padding:10px; border-radius:6px; font-size:13px;">🖨️ Print Barcodes</a>
        </div>
      </div>

      <table>
        <thead><tr><th>Barcode</th><th>Accession</th><th>Title</th><th>Author</th><th>Available</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
          @forelse($books as $book)
            <tr>
              <td><code>{{ $book->barcode ?? 'N/A' }}</code></td>
              <td>{{ $book->accession_number }}</td>
              <td>{{ $book->title }}</td>
              <td>{{ $book->author }}</td>
              <td>{{ $book->available ?? 0 }}</td>
              <td><span class="status-chip">{{ $book->status ?? 'Available' }}</span></td>
              <td style="display:flex; gap:8px;">
                <form method="POST" action="{{ route('admin.books.update', $book->id) }}" enctype="multipart/form-data" style="display:inline-block;">
                  @csrf
                  <input type="hidden" name="title" value="{{ $book->title }}">
                  <input type="hidden" name="author" value="{{ $book->author }}">
                  <input type="hidden" name="isbn" value="{{ $book->isbn }}">
                  <input type="hidden" name="accession_number" value="{{ $book->accession_number }}">
                  <input type="hidden" name="category" value="{{ $book->category }}">
                  <input type="hidden" name="copies" value="{{ $book->copies ?? 1 }}">
                  <input type="hidden" name="available" value="{{ $book->available ?? 0 }}">
                  <button class="btn-sm" type="submit">Quick Edit</button>
                </form>
                <form method="POST" action="{{ route('admin.books.delete', $book->id) }}">
                  @csrf
                  <button class="btn-sm danger" type="submit" onclick="return confirm('Archive this book from active catalog?')">Archive</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6">No books found in the active catalog.</td></tr>
          @endforelse
        </tbody>
      </table>

    @endcomponent
  </section>

  <section id="panel-book-management" class="panel">
    @component('components.card', ['title' => 'Book Management', 'subtitle' => 'All books currently in the system'])
      <div style="display:grid; gap:16px;">
        @php
          $transactionSummary = [
            'Borrowed' => collect($bookTransactionFeed)->where('event', 'Borrowed')->count(),
            'Returned' => collect($bookTransactionFeed)->where('event', 'Returned')->count(),
            'Rejected' => collect($bookTransactionFeed)->where('event', 'Rejected')->count(),
            'Approved' => collect($bookTransactionFeed)->where('event', 'Approved')->count(),
            'New Book Added' => collect($bookTransactionFeed)->where('event', 'New Book Added')->count(),
            'Archived' => collect($bookTransactionFeed)->where('event', 'Archived')->count(),
          ];
        @endphp
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:10px;">
          @foreach($transactionSummary as $label => $count)
            <div style="padding:12px; border:1px solid #e2e8f0; border-radius:12px; background:#f8fafc;">
              <div style="font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700;">{{ $label }}</div>
              <div style="font-size:22px; font-weight:800; color:#0f172a; margin-top:6px;">{{ $count }}</div>
            </div>
          @endforeach
        </div>

        <div style="overflow-x:auto;">
          <table>
            <thead><tr><th>Date</th><th>Event</th><th>User</th><th>Book</th><th>Details</th></tr></thead>
            <tbody>
              @forelse($bookTransactionFeed as $entry)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($entry['created_at'])->format('M d, Y h:i A') }}</td>
                  <td><span style="display:inline-flex; padding:4px 10px; border-radius:999px; background:#e0f2fe; color:#0f766e; font-size:11px; font-weight:700;">{{ $entry['event'] }}</span></td>
                  <td>{{ $entry['user_name'] }}</td>
                  <td>{{ $entry['book_title'] }}</td>
                  <td>{{ $entry['details'] }}</td>
                </tr>
              @empty
                <tr><td colspan="6" style="text-align:center;">No book transactions yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div style="margin-top:24px;">
        <table>
          <thead><tr><th>Barcode</th><th>Accession</th><th>Title</th><th>Author</th><th>Available</th><th>Status</th></tr></thead>
          <tbody>
            @forelse($books as $book)
              <tr>
                <td><code>{{ $book->barcode ?? 'N/A' }}</code></td>
                <td>{{ $book->accession_number ?? 'N/A' }}</td>
                <td>{{ $book->title ?? 'Unknown Title' }}</td>
                <td>{{ $book->author ?? 'Unknown' }}</td>
                <td>{{ $book->available ?? 0 }}</td>
                <td><span class="status-chip">{{ $book->status ?? 'Available' }}</span></td>
              </tr>
            @empty
              <tr><td colspan="6" style="text-align:center;">No books have been added yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    @endcomponent
  </section>

  <section id="panel-borrowing" class="panel">
    @component('components.card', ['title' => 'Borrow / Return', 'subtitle' => 'Process borrow and returns by barcode'])
      <div style="display:grid; gap:16px; margin-bottom:12px;">
        @if($errors->has('borrow_error'))
          <div class="alert danger">{{ $errors->first('borrow_error') }}</div>
        @endif
        <form method="POST" action="{{ route('staff.borrow-by-barcode') }}" style="display:grid; gap:12px;">
          @csrf
          <div style="padding:18px; border:1px dashed #0f766e; border-radius:16px; background:#f3faf7;">
            <label style="display:block; margin-bottom:8px; font-size:0.85rem; letter-spacing:0.08em; text-transform:uppercase; color:#0f766e;">Scan Borrower Library ID Barcode</label>
            <input type="text" name="barcode_id" placeholder="202600123" required style="width:100%; padding:18px 16px; font-size:18px; border:2px solid #0f766e; border-radius:14px; background:#ffffff;" />
            <p style="margin:10px 0 0; color:#475569; font-size:0.95rem;">Use this barcode box for the borrower only, then enter the book reference below.</p>
          </div>
          <div style="padding:18px; border:1px dashed #0f766e; border-radius:16px; background:#f3faf7;">
            <label style="display:block; margin-bottom:8px; font-size:0.85rem; letter-spacing:0.08em; text-transform:uppercase; color:#0f766e;">ISBN / Accession / Title / Barcode</label>
            <input type="text" name="book_reference" placeholder="ISBN / Accession / Title / Barcode" required style="width:100%; padding:18px 16px; font-size:18px; border:2px solid #0f766e; border-radius:14px; background:#ffffff;" />
            <p style="margin:10px 0 0; color:#475569; font-size:0.95rem;">Enter the book identifier here after scanning the borrower barcode.</p>
          </div>
          <div style="text-align:right;"><button class="btn-primary" type="submit">Record Borrow</button></div>
        </form>

        <div>
          <div class="section-sub">Recent Borrowed / Returned Books</div>
          <table>
            <thead>
              <tr>
                <th>Title</th>
                <th>Borrower</th>
                <th>Borrowed</th>
                <th>Due</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($borrowings->take(10) as $b)
                <tr>
                  <td>{{ $b->title ?? 'Unknown title' }}</td>
                  <td>{{ $b->borrower_name ?? $b->barcode_id }}</td>
                  <td>{{ \Carbon\Carbon::parse($b->borrow_date)->format('M d, Y') }}</td>
                  <td>
                    @if(!empty($b->due_date))
                      {{ \Carbon\Carbon::parse($b->due_date)->format('M d, Y') }}
                    @else
                      —
                    @endif
                  </td>
                  <td>
                    @if($b->status === 'Returned')
                      <span class="status-chip returned">Returned</span>
                    @elseif($b->status === 'Due')
                      <span class="status-chip overdue">Due</span>
                    @else
                      <span class="status-chip pending">{{ ucfirst($b->status ?? 'Pending') }}</span>
                    @endif
                  </td>
                  <td>
                    @if(empty($b->is_dummy) && $b->status !== 'Returned')
                      <div style="display:grid; gap:8px;">
                        <form method="POST" action="{{ route('admin.borrowings.return', $b->id) }}">
                          @csrf
                          <button class="btn-sm" type="submit">Record Return</button>
                        </form>
                      @php
                        $due = !empty($b->due_date) ? \Carbon\Carbon::parse($b->due_date) : null;
                      @endphp
                      @if($b->status !== 'Due' && (($due && ($due->isPast() || $due->isToday())) || $b->status === 'Borrowed'))
                        <div style="display:flex; gap:8px;">
                          <form method="POST" action="{{ route('admin.borrowings.overdue', $b->id) }}">
                            @csrf
                           <!-- <button class="btn-sm danger" type="submit">Mark Overdue</button>-->
                          </form>

                          <!-- Mark Due: record the borrower's overdue condition and create the fine entry in Fines -->
                          <form method="POST" action="{{ route('admin.borrowings.mark_due', $b->id) }}" class="mark-due-form">
                            @csrf
                            <input type="hidden" name="waive_note" value="">
                            <button class="btn-sm" type="submit" style="background:#10b981; color:#fff;">Mark Due</button>
                          </form>
                        </div>
                      @endif
                      </div>
                    @elseif(empty($b->is_dummy) && $b->status === 'Returned')
                      <span class="status-chip returned">Returned</span>
                    @else
                      <span class="status-chip pending">Sample request</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" style="text-align:center;">No recent borrowings.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    @endcomponent
  </section>

  <div class="borrow-modal-backdrop {{ session('borrow_popup') || session('return_popup') ? 'open' : '' }}" id="borrowModal" role="dialog" aria-modal="true" aria-labelledby="borrowModalTitle">
    <div class="borrow-modal">
      <div class="modal-head">
        <h2 id="borrowModalTitle">{{ session('return_popup') ? 'Book Return Recorded' : 'Welcome to Panabo City Library' }}</h2>
        <button class="modal-close" type="button" onclick="closeBorrowModal()" aria-label="Close">&times;</button>
      </div>
      <div class="modal-body">
        <div class="photo" id="borrowPhoto">
          @if(session('book_cover'))
            <img src="{{ session('book_cover') }}" alt="{{ session('book_title') }} cover">
          @else
            <span>BOOK IMAGE</span>
          @endif
        </div>
        <div class="details">
          <div class="detail-row">
            <div class="detail-label">Full Name</div>
            <div class="detail-value">{{ session('borrower_full_name', 'Unknown') }}</div>
          </div>
          @if(session('borrower_school'))
            <div class="detail-row">
              <div class="detail-label">School</div>
              <div class="detail-value">{{ session('borrower_school') }}</div>
            </div>
          @endif
          <div class="detail-row">
            <div class="detail-label">Book Title</div>
            <div class="detail-value">{{ session('book_title', 'Unknown Book') }}</div>
          </div>
          <div class="detail-row">
            <div class="detail-label">Library ID</div>
            <div class="detail-value">{{ session('borrower_id', '-') }}</div>
          </div>
          <div class="detail-row">
            <div class="detail-label">Date & Time</div>
            <div class="detail-value">{{ session('transaction_time', now()->format('F j, Y \a\t h:i A')) }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <section id="panel-borrow-requests" class="panel">
    <div class="borrow-request-tabs" style="display:flex; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
      <button type="button" class="borrow-request-tab-button active" data-borrow-tab="pending">Pending Book Requests</button>
      <button type="button" class="borrow-request-tab-button" data-borrow-tab="approved">Approved Book Requests</button>
      <button type="button" class="borrow-request-tab-button" data-borrow-tab="rejected">Rejected Book Requests</button>
      <button type="button" class="borrow-request-tab-button" data-borrow-tab="archived">Archived Book Requests</button>
    </div>

    <div id="borrow-tab-pending" class="borrow-request-tab-panel">
      @component('components.card', ['title' => 'Pending Book Requests', 'subtitle' => 'Review and manage borrow requests from verified accounts'])
        @include('components.borrow-requests-table', [
          'requests' => $pendingBorrowRequests,
          'showActions' => true,
          'statusColumn' => false,
          'emptyMessage' => 'No pending borrow requests yet.'
        ])
      @endcomponent
    </div>

    <div id="borrow-tab-approved" class="borrow-request-tab-panel" style="display:none;">
      @component('components.card', ['title' => 'Approved Book Requests', 'subtitle' => 'Borrow requests that were approved for pickup'])
        @include('components.borrow-requests-table', [
          'requests' => $approvedBorrowRequests,
          'showActions' => false,
          'statusColumn' => true,
          'showArchiveAction' => true,
          'emptyMessage' => 'No approved borrow requests yet.'
        ])
      @endcomponent
    </div>

    <div id="borrow-tab-rejected" class="borrow-request-tab-panel" style="display:none;">
      @component('components.card', ['title' => 'Rejected Book Requests', 'subtitle' => 'Borrow requests that were rejected and may require review'])
        @include('components.borrow-requests-table', [
          'requests' => $rejectedBorrowRequests,
          'showActions' => false,
          'statusColumn' => true,
          'showArchiveAction' => true,
          'showRejectionReason' => true,
          'emptyMessage' => 'No rejected borrow requests yet.'
        ])
      @endcomponent
    </div>

    <div id="borrow-tab-archived" class="borrow-request-tab-panel" style="display:none;">
      @component('components.card', ['title' => 'Archived Request History', 'subtitle' => 'Review borrow requests that were archived after approval or rejection'])
        <table>
          <thead><tr><th>User</th><th>Book</th><th>Status</th><th>Archived At</th></tr></thead>
          <tbody>
            @forelse($archivedBorrowRequests as $request)
              <tr>
                <td>{{ $request->user_name ?? $request->user_id ?? 'Unknown' }}</td>
                <td>{{ $request->title ?? 'Unknown book' }}</td>
                <td><span class="status-chip returned">{{ ucfirst($request->status ?? 'Archived') }}</span></td>
                <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A') }}</td>
              </tr>
            @empty
              <tr><td colspan="4" style="text-align:center;">No archived borrow requests yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      @endcomponent
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

        const initialBorrowTab = '{{ session('borrow_tab', 'pending') }}';

        buttons.forEach(button => {
          button.addEventListener('click', () => setActiveTab(button.dataset.borrowTab));
        });

        if (initialBorrowTab) {
          setActiveTab(initialBorrowTab);
        }
      })();
    </script>
  </section>

 <!-- <section id="panel-attendance" class="panel">
    <div class="grid-1">
      @component('components.card', ['title' => 'Attendance Summary'])
        <table>
          <thead><tr><th>Name</th><th>ID</th><th>Total Logs</th></tr></thead>
          <tbody>
            @forelse($staffAttendanceSummary as $staff)
              <tr>
                <td>{{ $staff->name }}</td>
                <td>{{ $staff->user_id }}</td>
                <td>{{ $staff->total_logs }}</td>
              </tr>
            @empty
              <tr><td colspan="3">No attendance summary yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      @endcomponent
    </div>
  </section>
-->
  <!-- Wi-Fi voucher panel removed per staff workflow simplification -->

 <section id="panel-fines" class="panel">
  @php
    $financialRows = \Illuminate\Support\Facades\Schema::hasTable('financial_transactions')
      ? \Illuminate\Support\Facades\DB::table('financial_transactions')
          ->leftJoin('users', 'financial_transactions.user_id', '=', 'users.user_id')
          ->leftJoin('members', function ($join) {
              $join->on('financial_transactions.user_id', '=', 'members.barcode_id')
                   ->orOn('financial_transactions.user_id', '=', 'members.assigned_user_id');
          })
          ->where('financial_transactions.transaction_type', 'Fine')
          ->whereIn('financial_transactions.status', ['Unpaid', 'Paid'])
          ->select(
              'financial_transactions.*',
          \Illuminate\Support\Facades\DB::raw("COALESCE(CASE WHEN financial_transactions.patron_name REGEXP '^[0-9]+$' THEN NULL ELSE financial_transactions.patron_name END, users.name, members.full_name, financial_transactions.user_id, 'N/A') as user_name")
          )
          ->latest('transaction_date')
          ->get()
      : collect();

    $unpaidFineRows = $financialRows->where('status', 'Unpaid');
    $paidFineRows = $financialRows->where('status', 'Paid');
    $collectedFines = $paidFineRows->sum('amount');
    $unpaidFines = $unpaidFineRows->sum('amount');
  @endphp

  <div class="section-header">
    <div>
      <div class="section-title">Fines</div>
      <div class="section-sub">Monitor and update library fine records</div>
    </div>
  </div>

  <div class="stats-grid" style="margin-bottom:18px;">
    <div class="stat-card green">
      <div class="stat-label">Collected Fines</div>
      <div class="stat-value">PHP {{ number_format($collectedFines, 2) }}</div>
    </div>
    <div class="stat-card red">
      <div class="stat-label">Unpaid Fines</div>
      <div class="stat-value">PHP {{ number_format($unpaidFines, 2) }}</div>
    </div>
  </div>


    <div class="card" style="margin-bottom:18px;">
    <div class="card-header">
      <div class="card-title">Add or Update Fine</div>
    </div>
    <div class="card-body">
      <form action="{{ route('admin.financial-transactions.store') }}" method="POST" style="display:grid; grid-template-columns: repeat(3, 1fr); gap:12px;">
        @csrf
          <input type="hidden" name="transaction_type" value="Fine">
        <div class="form-group" style="margin:0;">
          <label>Patron / User ID</label>
          <input type="text" name="user_id" placeholder="Barcode, user ID, or email" required>
        </div>
         <!-- <div class="form-group" style="margin:0;"><label>Patron Display Name</label><input type="text" name="patron_name" placeholder="Optional display name"></div> -->
            <div class="form-group" style="margin:0;">
          <label>Fine Amount</label>
          <input type="number" name="amount" step="0.01" min="0" required>
        </div>
            <div class="form-group" style="margin:0;">
          <label>Cash Received</label>
          <input type="number" name="received_amount" step="0.01" min="0" placeholder="Optional for payment">
        </div>
    <div class="form-group" style="margin:0; grid-column: span 2;">
            <label>Description</label>
            <input type="text" name="description" placeholder="Reason for fine" required>
            </div>
 <div class="form-group" style="margin:0;">
          <label>Status</label>
          <select name="status" required>
            <option>Select</option>
            <option>Paid</option>
          </select>
        </div>
            <div class="form-group" style="margin:0; display:flex; align-items:center; gap:8px;">
          <label><input type="checkbox" name="send_notification" value="1"> Notify patron</label>
        </div>
       <input type="hidden" name="transaction_date" value="{{ now()->toDateString() }}">

        <div style="grid-column:1 / -1; text-align:right;">
          <button class="btn-primary" type="submit">Save Transaction</button>
        </div>
      </form>
    </div>
  </div>

    <div class="card" style="margin-bottom:18px;">
   <div class="card-header">
     <div class="card-title">Unpaid Fine Records</div>
   </div>
   <div class="card-body" style="padding:0;">
     <table>
       <thead>
         <tr>
           <th>User Name</th>
           <th>Description</th>
           <th>Amount</th>
           <th>Status</th>
           <th>Date</th>
           <th>Update</th>
         </tr>
       </thead>
       <tbody>
          @forelse($unpaidFineRows as $row)
           <tr>
             <td>
               <strong>{{ $row->user_name ?? $row->user_id ?? 'N/A' }}</strong>
             </td>
             <td>{{ preg_replace('/\s*\([^)]*day\/s\)/i', '', $row->description) }}</td>
             <td style="font-weight:700;">PHP {{ number_format($row->amount, 2) }}</td>
             <td>{{ $row->status }}</td>
             <td>{{ \Carbon\Carbon::parse($row->transaction_date)->format('M d, Y') }}</td>
              <td>
                 <form action="{{ route('admin.financial-transactions.store') }}" method="POST" style="display:grid; gap:6px; min-width:220px;">
                  @csrf
                  <input type="hidden" name="transaction_type" value="Fine">
                  <input type="hidden" name="user_id" value="{{ $row->user_id }}">
                  <input type="hidden" name="description" value="{{ $row->description }}">
                  <input type="hidden" name="amount" value="{{ $row->amount }}">
                  <input type="hidden" name="received_amount" value="{{ $row->received_amount ?? 0 }}">
                  <input type="hidden" name="transaction_date" value="{{ \Carbon\Carbon::parse($row->transaction_date)->format('Y-m-d') }}">
                  <select name="status" required>
                    <option value="Unpaid" selected>Select</option>
                    <option value="Paid">Paid</option>
                  </select>
                  <button class="btn-sm" type="submit">Save Status</button>
                </form>
              </td>
            </tr>
          @empty
               <tr>
              <td colspan="6" style="text-align:center;">No unpaid fine records yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
      </div>
    </div>
 <div class="card">    <div class="card-header" style="background:#f0fdf4;">
      <div class="card-title">Paid Fine Records</div>
    </div>
    <div class="card-body" style="padding:0;">
      <table>
        <thead>
          <tr>
            <th>User Name</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Received</th>
            <th>Change</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
             @forelse($paidFineRows as $row)
            <tr>
              <td>
                @php
                  $display = $row->patron_name ?? null;
                  if (empty($display) && ! empty($row->user_id)) {
                    $lookupUser = \App\Models\User::where('user_id', $row->user_id)
                      ->orWhere('email', $row->user_id)
                      ->orWhere('barcode_id', $row->user_id)
                      ->first();
                    if ($lookupUser) {
                      $display = $lookupUser->name;
                    } else {
                      $lookupMember = \Illuminate\Support\Facades\DB::table('members')
                        ->where('barcode_id', $row->user_id)
                        ->orWhere('assigned_user_id', $row->user_id)
                        ->first();
                      if ($lookupMember) $display = $lookupMember->full_name;
                    }
                  }
                @endphp
                   @if(! empty($display))
                  <strong>{{ $display }}</strong>
                @else
                  {{ $row->user_id ?? 'N/A' }}
                @endif
              </td>
<td>{{ preg_replace('/\s*\([^)]*day\/s\)/i', '', $row->description) }}</td>              <td style="font-weight:700;">PHP {{ number_format($row->amount, 2) }}</td>
              <td>PHP {{ number_format((float) ($row->received_amount ?? 0), 2) }}</td>
              <td>PHP {{ number_format((float) ($row->change_amount ?? 0), 2) }}</td>
              <td>{{ \Carbon\Carbon::parse($row->transaction_date)->format('M d, Y') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align:center;">No paid fine records yet.</td>
            </tr>
          @endforelse
           </tbody>
      </table>
    </div>
  </div>
</section>

  <section id="panel-barcode-requests" class="panel">
    @component('components.card', ['title' => 'Barcode Replacement Requests', 'subtitle' => 'Users requesting new barcodes (lost, damaged, etc.)'])
      <table>
        <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Old Barcode</th><th>New Barcode</th><th>Reason</th><th>Status</th><th>Requested At</th><th>Action</th></tr></thead>
        <tbody>
          @forelse($pendingBarcodeRequests as $request)
            <tr>
              <td><strong>{{ $request->user_name ?? 'Unknown' }}</strong></td>
              <td>{{ $request->user_email ?? 'N/A' }}</td>
              <td>{{ ucfirst($request->user_role ?? 'student') }}</td>
              <td>{{ $request->old_barcode_id ?? '—' }}</td>
              <td><strong>{{ $request->new_barcode_id ?? '—' }}</strong></td>
              <td>
                <div>{{ ucfirst($request->reason ?? 'unspecified') }}</div>
                @if(!empty($request->reason_details))
                  <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">{{ $request->reason_details }}</div>
                @endif
                @if(!empty($request->proof_path))
                  <div style="margin-top: 6px;"><a href="{{ asset('storage/' . $request->proof_path) }}" target="_blank" rel="noopener">View proof</a></div>
                @endif
                @if(!empty($request->staff_notes))
                  <div style="margin-top:6px; font-size:13px; color:#374151;">Staff note: <span style="color:#6b7280; white-space:pre-wrap;">{{ $request->staff_notes }}</span></div>
                @endif
              </td>
              <td><span style="display:inline-flex; padding:4px 10px; border-radius:999px; background: {{ strtolower($request->status ?? '') === 'approved' ? 'rgba(34,197,94,0.12); color:#16a34a' : (strtolower($request->status ?? '') === 'rejected' ? 'rgba(239,68,68,0.12); color:#dc2626' : 'rgba(249,115,22,0.15); color:#c2410c') }}; font-size:11px; font-weight:600;">{{ ucfirst($request->status ?? 'pending') }}</span></td>
              <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A') }}</td>
              <td>
                <div style="display:flex; flex-direction:column; gap:8px;">
                  <form method="POST" action="{{ route('staff.barcode-request.approve', $request->id) }}">
                    @csrf
                    <button class="btn-sm" type="submit">Approve</button>
                  </form>
                  <form method="POST" action="{{ route('staff.barcode-request.reject', $request->id) }}">
                    @csrf
                    <textarea name="rejection_reason" placeholder="Reject reason" required style="width:220px; min-height:60px; padding:8px; border:1px solid #d1d5db; border-radius:6px; font-size:12px;"></textarea>
                    <button class="btn-sm" type="submit" style="background:#dc2626; margin-top:4px;">Reject</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="9" style="text-align:center;">No barcode requests yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    @endcomponent

    @component('components.card', ['title' => 'Approved Barcode Requests', 'subtitle' => 'Requests that have been approved and assigned new barcodes'])
      <table>
        <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Old Barcode</th><th>New Barcode</th><th>Approved At</th></tr></thead>
        <tbody>
          @forelse($approvedBarcodeRequests as $request)
            <tr>
              <td><strong>{{ $request->user_name ?? 'Unknown' }}</strong></td>
              <td>{{ $request->user_email ?? 'N/A' }}</td>
              <td>{{ ucfirst($request->user_role ?? 'student') }}</td>
              <td>{{ $request->old_barcode_id ?? '—' }}</td>
              <td><strong>{{ $request->new_barcode_id ?? '—' }}</strong></td>
              <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A') }}</td>
            </tr>
          @empty
            <tr><td colspan="7" style="text-align:center;">No approved barcode requests yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    @endcomponent

    @component('components.card', ['title' => 'Rejected Barcode Requests', 'subtitle' => 'Requests declined for insufficient reason or invalid proof'])
      <table>
        <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Old Barcode</th><th>Request Reason</th><th>Rejection Note</th><th>Rejected At</th></tr></thead>
        <tbody>
          @forelse($rejectedBarcodeRequests as $request)
            <tr>
              <td><strong>{{ $request->user_name ?? 'Unknown' }}</strong></td>
              <td>{{ $request->user_email ?? 'N/A' }}</td>
              <td>{{ ucfirst($request->user_role ?? 'student') }}</td>
              <td>{{ $request->old_barcode_id ?? '—' }}</td>
              <td>
                <div>{{ ucfirst($request->reason ?? 'unspecified') }}</div>
                @if(!empty($request->reason_details))
                  <div style="font-size:12px; color:#6b7280; margin-top:4px;">{{ $request->reason_details }}</div>
                @endif
              </td>
              <td style="max-width:220px; white-space:pre-wrap;">{{ $request->staff_notes ?? 'No rejection note provided.' }}</td>
              <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y h:i A') }}</td>
            </tr>
          @empty
            <tr><td colspan="7" style="text-align:center;">No rejected barcode requests yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    @endcomponent
  </section>

  <!-- Visitor barcode functionality removed from staff dashboard (handled via auto-generate) -->

  <div id="mark-due-modal" style="display:none; position:fixed; inset:0; z-index:2000; align-items:center; justify-content:center; background:rgba(0,0,0,0.45);">
    <div style="background:#fff; border-radius:18px; max-width:520px; width:90%; margin:0 auto; box-shadow:0 25px 60px rgba(0,0,0,0.25); overflow:hidden;">
      <div style="padding:24px; position:relative;">
        <button id="mark-due-close" type="button" style="position:absolute; right:24px; top:24px; background:transparent; border:none; color:#6b7280; font-size:20px; cursor:pointer;">×</button>
        <h2 style="margin:0 0 12px; font-size:1.15rem;">Mark Borrowing as Due</h2>
        <p style="margin:0 0 16px; color:#374151; line-height:1.5;">Enter a short staff note explaining why this borrowing is being marked as Due. The system will automatically record the corresponding fine entry in the Fines module.</p>
        <textarea id="mark-due-note" rows="5" style="width:100%; min-height:120px; padding:12px; border:1px solid #d1d5db; border-radius:12px; font-size:0.95rem; resize:vertical;" placeholder="Staff note / reason (required)"></textarea>
        <div id="mark-due-notification" style="display:none; margin-top:16px; padding:14px; border-radius:12px; background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46;"></div>
        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:18px;">
          <button id="mark-due-cancel" type="button" style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:10px 18px; color:#0f172a; cursor:pointer;">Cancel</button>
          <button id="mark-due-submit" type="button" style="background:#10b981; border:none; border-radius:10px; padding:10px 18px; color:#ffffff; cursor:pointer;">Save Note</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const staffPanels = [
        'dashboard',
        'verify',
        'verified',
        'barcode-cards',
        'barcode-requests',
        'books',
        'book-management',
        'borrow-requests',
        'borrowing',
        'fines'
      ];

      function staffShowPanel(name) {
        const cleanedName = String(name || 'dashboard').trim();

        document.querySelectorAll('.panel').forEach((panel) => {
          panel.classList.remove('active');
        });

        document.querySelectorAll('[data-panel-button]').forEach((button) => {
          button.classList.remove('active');
        });

        if (!staffPanels.includes(cleanedName)) {
          const topbarTitle = document.getElementById('topbarTitle');
          if (topbarTitle) {
            topbarTitle.textContent = 'Staff Dashboard';
          }
          return;
        }

        const target = document.getElementById('panel-' + cleanedName);
        if (!target) {
          return;
        }

        target.classList.add('active');

        const activeButton = document.querySelector('[data-panel-button="' + cleanedName + '"]');
        if (activeButton) {
          activeButton.classList.add('active');
        }

        const topbarTitle = document.getElementById('topbarTitle');
        if (topbarTitle) {
          topbarTitle.textContent = activeButton ? (activeButton.dataset.title || cleanedName) : 'Staff Dashboard';
        }
      }

      window.staffShowPanel = staffShowPanel;
      window.showPanel = function (name) {
        staffShowPanel(name);
      };

      document.querySelectorAll('[data-panel-button]').forEach((button) => {
        const original = button.getAttribute('onclick');
        if (original && original.includes('showPanel')) {
          button.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            const nextPanel = button.dataset.panelButton;
            staffShowPanel(nextPanel);
          }, true);
        }
      });

      const initialPanel = '{{ session('active_panel', 'dashboard') }}';
      if (initialPanel && staffPanels.includes(initialPanel)) {
        staffShowPanel(initialPanel);
      } else {
        staffShowPanel('dashboard');
      }
    })();

    (function(){
      const input = document.getElementById('opac-search');
      const out = document.getElementById('opac-results');
      if (!input || !out) return;
      let timer = null;
      input.addEventListener('input', function(){
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 2) { out.innerHTML = ''; return; }
        timer = setTimeout(() => {
          fetch(`/opac/search?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => {
              out.innerHTML = '';
              if (!data.results || data.results.length === 0) {
                out.innerHTML = '<div class="text-muted">No matches</div>';
                return;
              }
              data.results.forEach(b => {
                const row = document.createElement('div');
                row.style.padding = '6px 0';
                row.style.borderBottom = '1px solid #f4f4f4';
                row.innerHTML = `<div style="font-weight:700">${b.title}</div><div class="text-muted">${b.author} · ${b.accession_number || b.isbn}</div>`;
                out.appendChild(row);
              });
            }).catch(()=> out.innerHTML = '<div class="text-muted">Search failed</div>');
        }, 250);
      });
    })();

    // Staff note modal for marking due
    (function(){
      const modal = document.getElementById('mark-due-modal');
      const noteField = document.getElementById('mark-due-note');
      const cancelButton = document.getElementById('mark-due-cancel');
      const closeButton = document.getElementById('mark-due-close');
      const submitButton = document.getElementById('mark-due-submit');
      const notificationBox = document.getElementById('mark-due-notification');
      let activeForm = null;

      function showNotification(message, isError) {
        if (!notificationBox) return;
        notificationBox.textContent = message;
        notificationBox.style.display = 'block';
        notificationBox.style.background = isError ? '#fee2e2' : '#ecfdf5';
        notificationBox.style.borderColor = isError ? '#fecaca' : '#a7f3d0';
        notificationBox.style.color = isError ? '#b91c1c' : '#065f46';
      }

      function clearNotification() {
        if (!notificationBox) return;
        notificationBox.style.display = 'none';
        notificationBox.textContent = '';
      }

      function openModal(form) {
        activeForm = form;
        noteField.value = '';
        clearNotification();
        modal.style.display = 'flex';
        noteField.focus();
      }

      function closeModal() {
        modal.style.display = 'none';
        activeForm = null;
      }

      async function submitMarkDue() {
        if (!activeForm) {
          showNotification('Unable to find the borrowing form. Please refresh and try again.', true);
          return;
        }

        const note = noteField.value.trim();
        if (!note) {
          showNotification('Staff note is required to mark this borrowing as Due.', true);
          noteField.focus();
          return;
        }

        submitButton.disabled = true;
        submitButton.textContent = 'Saving...';
        clearNotification();

        const formData = new FormData(activeForm);
        formData.set('waive_note', note);

        try {
          const response = await fetch(activeForm.action, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          if (!response.ok) {
            throw new Error('Failed to save the note and create the fine record.');
          }

          showNotification('The borrowing was marked as Due and the fine has been recorded in the Fines module.', false);
          submitButton.textContent = 'Saved';

          setTimeout(function() {
            window.location.reload();
          }, 1400);
        } catch (error) {
          showNotification(error.message || 'Unable to record the fine. Please try again.', true);
          submitButton.disabled = false;
          submitButton.textContent = 'Save Note';
        }
      }

      document.querySelectorAll('.mark-due-form').forEach(function(form){
        form.addEventListener('submit', function(e){
          e.preventDefault();
          openModal(form);
        });
      });

      cancelButton.addEventListener('click', function(){
        closeModal();
      });

      if (closeButton) {
        closeButton.addEventListener('click', function(){
          closeModal();
        });
      }

      submitButton.addEventListener('click', function(){
        submitMarkDue();
      });

      modal.addEventListener('click', function(e){
        if (e.target === modal) {
          closeModal();
        }
      });
    })();
  </script>
@endsection
