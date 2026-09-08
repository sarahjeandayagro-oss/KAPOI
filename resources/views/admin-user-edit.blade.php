<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User Profile</title>
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
    .title { font-size:18px; font-weight:700; }
    .sub { font-size:12px; color:var(--muted); margin-top:2px; }
    .wrap { max-width:1100px; margin:0 auto; padding:28px; }
    .card { background:var(--panel); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow); }
    .card-header { padding:16px 18px; background:var(--card-soft); border-bottom:1px solid var(--border); }
    .card-title { font-size:15px; font-weight:700; }
    .card-sub { font-size:12px; color:var(--muted); margin-top:2px; }
    .card-body { padding:18px; }
    .grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .field label { display:block; font-size:12px; color:var(--muted); margin-bottom:6px; }
    .field input, .field select {
      width:100%;
      padding:11px 12px;
      border:1px solid var(--border);
      border-radius:10px;
      background:#fff;
      color:var(--text);
      outline:none;
    }
    .field input:focus, .field select:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(47,158,143,.12); }
    .actions { display:flex; gap:10px; flex-wrap:wrap; margin-top:20px; }
    .btn, .btn-secondary {
      display:inline-flex;
      align-items:center;
      justify-content:center;
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
    .danger {
      background:rgba(239,68,68,.10);
      color:var(--text);
      border-color:rgba(239,68,68,.3);
    }
    .success {
      background:rgba(34,197,94,.12);
      color:#15803d;
      border:1px solid rgba(34,197,94,.24);
      border-radius:12px;
      padding:12px 14px;
      margin-bottom:18px;
      font-weight:600;
    }
    @media (max-width: 760px) {
      .wrap { padding:18px; }
      .topbar { padding:14px 18px; }
      .grid { grid-template-columns:1fr; }
    }
  </style>
</head>
<body>
  <div class="topbar">
    <div>
      <div class="title">Edit User Profile</div>
      <div class="sub">Manage account details from the admin dashboard</div>
    </div>
    <a class="btn-secondary" href="{{ route('admin.dashboard') }}">Back to Dashboard</a>
  </div>

  <div class="wrap">
    @if(session('success'))
      <div class="success">{{ session('success') }}</div>
    @endif

    <div class="card">
      <div class="card-header">
        <div class="card-title">{{ $user->name }}</div>
        <div class="card-sub">{{ ucfirst($user->role) }} account</div>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
          @csrf
          <div class="grid">
            <div class="field">
              <label>User ID</label>
              <input type="text" name="user_id" value="{{ $user->user_id }}" required>
            </div>
            <div class="field">
              <label>Full Name</label>
              <input type="text" name="name" value="{{ $user->name }}" required>
            </div>
            <div class="field">
              <label>Email</label>
              <input type="email" name="email" value="{{ $user->email }}" required>
            </div>
            <div class="field">
              <label>Role</label>
              <select name="role" required>
                @foreach(['admin', 'staff', 'student', 'researcher'] as $roleOption)
                  <option value="{{ $roleOption }}" {{ ($user->role ?? 'student') === $roleOption ? 'selected' : '' }}>{{ ucfirst($roleOption) }}</option>
                @endforeach
              </select>
            </div>
            <div class="field">
              <label>Status</label>
              <select name="status" required>
                @foreach(['pending', 'approved'] as $statusOption)
                  <option value="{{ $statusOption }}" {{ ($user->status ?? 'approved') === $statusOption ? 'selected' : '' }}>{{ ucfirst($statusOption) }}</option>
                @endforeach
              </select>
            </div>
            <div class="field">
              <label>School</label>
              <input type="text" name="school" value="{{ $user->school }}">
            </div>
            <div class="field">
              <label>Contact Number</label>
              <input type="text" name="contact_no" value="{{ $user->contact_no }}">
            </div>
            <div class="field">
              <label>Gender</label>
              <select name="gender">
                <option value="">Select gender</option>
                @foreach(['Male', 'Female', 'Other', 'Prefer not to say'] as $genderOption)
                  <option value="{{ $genderOption }}" {{ ($user->gender ?? '') === $genderOption ? 'selected' : '' }}>{{ $genderOption }}</option>
                @endforeach
              </select>
            </div>
            <div class="field">
              <label>Birthdate</label>
              <input type="date" name="birthdate" value="{{ $user->birthdate ? \Carbon\Carbon::parse($user->birthdate)->format('Y-m-d') : '' }}">
            </div>
          </div>
          <div class="actions">
            <button class="btn" type="submit">Save Changes</button>
            <a class="btn-secondary" href="{{ route('admin.dashboard') }}">Cancel</a>
          </div>
        </form>
        <form method="POST" action="{{ route('admin.users.delete', $user->id) }}" style="margin-top:12px;">
          @csrf
          <button class="btn-secondary danger" type="submit" onclick="return confirm('Delete this user profile?')">Delete User</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
