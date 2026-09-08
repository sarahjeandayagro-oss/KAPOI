<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Library Profile</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg:#F5F7FA;
      --panel:#FFFFFF;
      --card:#FFFFFF;
      --card-soft:#F8FAFC;
      --border:#E5E7EB;
      --text:#1F2937;
      --muted:#6B7280;
      --primary:#2F9E8F;
      --primary-hover:#26867A;
      --success:#22C55E;
      --warning:#F59E0B;
      --danger:#EF4444;
      --shadow:0 10px 30px rgba(15,23,42,.08);
      --radius:14px;
    }

    * { box-sizing:border-box; }
    body { margin:0; font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); }
    .topbar {
      background:rgba(255,255,255,.9);
      border-bottom:1px solid var(--border);
      padding:18px 28px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:16px;
      position:sticky;
      top:0;
      z-index:20;
      box-shadow:var(--shadow);
      backdrop-filter:blur(6px);
    }
    .topbar-title { font-size:18px; font-weight:700; }
    .topbar-sub { font-size:12px; color:var(--muted); margin-top:2px; }
    .wrap { max-width:1200px; margin:0 auto; padding:28px; }
    .header-row { display:flex; justify-content:space-between; align-items:end; gap:16px; margin-bottom:20px; }
    .title { font-size:28px; font-weight:800; margin:0; }
    .subtitle { color:var(--muted); margin-top:6px; }
    .btn, .btn-secondary {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      border-radius:10px;
      padding:10px 14px;
      text-decoration:none;
      font-weight:700;
      border:1px solid var(--primary);
      cursor:pointer;
      box-shadow:var(--shadow);
    }
    .btn { background:linear-gradient(135deg,var(--primary),var(--primary-hover)); color:#fff; }
    .btn-secondary { background:#fff; color:var(--text); border-color:var(--border); }
    .profile-grid {
      display:grid;
      grid-template-columns: 320px 1fr;
      gap:20px;
      align-items:start;
    }
    .card {
      background:var(--panel);
      border:1px solid var(--border);
      border-radius:var(--radius);
      overflow:hidden;
      box-shadow:var(--shadow);
    }
    .card-header {
      padding:16px 18px;
      background:var(--card-soft);
      border-bottom:1px solid var(--border);
    }
    .card-title { font-size:15px; font-weight:700; }
    .card-sub { font-size:12px; color:var(--muted); margin-top:2px; }
    .card-body { padding:18px; }
    .avatar {
      width:164px;
      height:164px;
      border-radius:18px;
      background:linear-gradient(135deg, rgba(47,158,143,.12), rgba(47,158,143,.05));
      border:1px solid var(--border);
      overflow:hidden;
      display:grid;
      place-items:center;
      font-size:56px;
      font-weight:800;
      color:var(--primary);
      margin-bottom:18px;
    }
    .avatar img { width:100%; height:100%; object-fit:cover; }
    .meta { display:grid; gap:14px; }
    .field-label { font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:.7px; }
    .field-value { font-size:15px; font-weight:600; margin-top:4px; }
    .pill {
      display:inline-flex;
      align-items:center;
      padding:4px 10px;
      border-radius:999px;
      font-size:11px;
      font-weight:700;
      background:rgba(47,158,143,.12);
      color:var(--primary);
    }
    .stats {
      display:grid;
      grid-template-columns:repeat(2, minmax(0, 1fr));
      gap:16px;
      margin-top:16px;
    }
    .mini-card {
      background:#fff;
      border:1px solid var(--border);
      border-radius:12px;
      padding:14px;
    }
    .mini-label { font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:.6px; }
    .mini-value { font-size:14px; font-weight:700; margin-top:6px; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:12px 14px; border-bottom:1px solid var(--border); text-align:left; font-size:14px; }
    th { background:var(--card-soft); color:var(--muted); text-transform:uppercase; font-size:11px; letter-spacing:.7px; }
    .empty { text-align:center; color:var(--muted); padding:20px 14px; }
    .section { margin-top:20px; }
    .section .card { margin-bottom:20px; }
    .success {
      background:rgba(34,197,94,.12);
      color:#15803d;
      border:1px solid rgba(34,197,94,.24);
      border-radius:12px;
      padding:12px 14px;
      margin-bottom:18px;
      font-weight:600;
    }
    @media (max-width: 920px) {
      .profile-grid { grid-template-columns:1fr; }
      .header-row { flex-direction:column; align-items:flex-start; }
      .stats { grid-template-columns:1fr; }
    }
    @media (max-width: 640px) {
      .wrap { padding:18px; }
      .topbar { padding:14px 18px; }
      .title { font-size:22px; }
    }
  </style>
</head>
<body>
  <div class="topbar">
    <div>
      <div class="topbar-title">Panabo City Library</div>
      <div class="topbar-sub">Profile overview</div>
    </div>
    <a class="btn-secondary" href="{{ match(auth()->user()->role) {
      'admin' => route('admin.dashboard'),
      'researcher' => route('researcher.dashboard'),
      'staff' => route('staff.dashboard'),
      'visitor' => route('visitor.dashboard'),
      default => route('student.dashboard'),
    } }}">Back to Dashboard</a>
  </div>

  <div class="wrap">
    @if(session('success'))
      <div class="success">{{ session('success') }}</div>
    @endif

    <div class="header-row">
      <div>
        <h1 class="title">My Profile</h1>
        <div class="subtitle">Read-only profile details and library activity summary.</div>
      </div>
      <a class="btn" href="{{ route('profile.edit') }}">Edit Profile</a>
    </div>

    <div class="profile-grid">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Profile Snapshot</div>
          <div class="card-sub">Your account information</div>
        </div>
        <div class="card-body">
          @php
            $profilePictureUrl = null;
            if (!empty($user->profile_picture)) {
                if (str_starts_with($user->profile_picture, 'http')) {
                    $profilePictureUrl = $user->profile_picture;
                } else {
                    $profilePictureUrl = asset('storage/' . ltrim($user->profile_picture, '/'));
                }
            }
          @endphp

          <div class="avatar">
            @if($profilePictureUrl)
              <img src="{{ $profilePictureUrl }}" alt="{{ $user->name }}">
            @else
              {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
          </div>

          <div class="meta">
            <div>
              <div class="field-label">Full Name</div>
              <div class="field-value">{{ $user->name }}</div>
            </div>
            <div>
              <div class="field-label">Library ID</div>
              <div class="field-value">{{ $user->barcode_id ?? $user->user_id }}</div>
            </div>
            <div>
              <div class="field-label">Barcode</div>
              <div class="field-value" style="display:grid; gap:12px;">
                <img src="{{ route('barcode.book.png', ['barcode' => $user->barcode_id ?? $user->user_id]) }}" alt="Barcode for {{ $user->name }}" style="width:100%; max-width:260px; height:auto;" />
                <div style="font-size:13px; color: var(--muted); word-break:break-all;">{{ $user->barcode_id ?? $user->user_id }}</div>
              </div>
            </div>
            <div>
              <div class="field-label">Email</div>
              <div class="field-value">{{ $user->email }}</div>
            </div>
            <div>
              <div class="field-label">School</div>
              <div class="field-value">{{ $user->school ?? 'Not listed' }}</div>
            </div>
            <div>
              <div class="field-label">Contact Number</div>
              <div class="field-value">{{ $user->contact_no ?? 'Not provided' }}</div>
            </div>
            <div>
              <div class="field-label">Gender</div>
              <div class="field-value">{{ $user->gender ?? 'Not provided' }}</div>
            </div>
            <div>
              <div class="field-label">Birthdate</div>
              <div class="field-value">{{ $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->format('M d, Y') : 'Not provided' }}</div>
            </div>
            <div>
              <div class="field-label">Account Type</div>
              <div class="field-value">{{ ucfirst($user->role) }}</div>
            </div>
            <div>
              <div class="field-label">Status</div>
              <div class="field-value"><span class="pill">{{ ucfirst($user->status ?? 'approved') }}</span></div>
            </div>
          </div>
        </div>
      </div>

      <div>
        <div class="stats">
          <div class="mini-card">
            <div class="mini-label">Borrowings</div>
            <div class="mini-value">{{ $borrowings->count() }}</div>
          </div>
          <div class="mini-card">
            <div class="mini-label">Fines</div>
            <div class="mini-value">{{ $fines->count() }}</div>
          </div>
        </div>

        <div class="section">
          <div class="card">
            <div class="card-header">
              <div class="card-title">Borrowing History</div>
              <div class="card-sub">Recent transactions</div>
            </div>
            <div class="card-body" style="padding:0;">
              <table>
                <thead><tr><th>Book</th><th>Borrowed</th><th>Due</th><th>Status</th></tr></thead>
                <tbody>
                  @forelse($borrowings as $borrow)
                    <tr>
                      <td><strong>{{ $borrow->title ?? $borrow->book_barcode }}</strong><br><small>{{ $borrow->author }}</small></td>
                      <td>{{ \Carbon\Carbon::parse($borrow->borrow_date)->format('M d, Y') }}</td>
                      <td>{{ \Carbon\Carbon::parse($borrow->due_date)->format('M d, Y') }}</td>
                      <td>{{ $borrow->status }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="4" class="empty">No borrowing history yet.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <div class="card-title">Account Fines</div>
              <div class="card-sub">Current and past balances</div>
            </div>
            <div class="card-body" style="padding:0;">
              <table>
                <thead><tr><th>Description</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                  @forelse($fines as $fine)
                    <tr>
                      <td>{{ $fine->description }}</td>
                      <td>PHP {{ number_format($fine->amount, 2) }}</td>
                      <td>{{ $fine->status }}</td>
                      <td>{{ \Carbon\Carbon::parse($fine->transaction_date)->format('M d, Y') }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="4" class="empty">No fines recorded.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <div class="card-title">Attendance History</div>
              <div class="card-sub">Entry logs</div>
            </div>
            <div class="card-body" style="padding:0;">
              <table>
                <thead><tr><th>Date</th><th>Time</th><th>Remarks</th></tr></thead>
                <tbody>
                  @forelse($attendance as $log)
                    <tr>
                      <td>{{ \Carbon\Carbon::parse($log->entry_time)->format('M d, Y') }}</td>
                      <td>{{ \Carbon\Carbon::parse($log->entry_time)->format('h:i A') }}</td>
                      <td>{{ $log->remarks ?? 'Entry logged' }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="3" class="empty">No attendance records yet.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          @if($user->role === 'researcher')
            <div class="card">
              <div class="card-header">
                <div class="card-title">Proposal History</div>
                <div class="card-sub">Research submissions</div>
              </div>
              <div class="card-body" style="padding:0;">
                <table>
                  <thead><tr><th>Title</th><th>Status</th><th>Budget</th><th>Date</th></tr></thead>
                  <tbody>
                    @forelse($proposals as $proposal)
                      <tr>
                        <td>{{ $proposal->title }}</td>
                        <td>{{ $proposal->status }}</td>
                        <td>PHP {{ number_format($proposal->budget, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($proposal->created_at)->format('M d, Y') }}</td>
                      </tr>
                    @empty
                      <tr><td colspan="4" class="empty">No proposals submitted yet.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</body>
</html>
