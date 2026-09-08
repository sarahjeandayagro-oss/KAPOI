<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Verify your email</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    body { font-family: Inter, sans-serif; background: #f5f7fa; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:24px; }
    .card { background:#fff; border:1px solid #e5e7eb; border-radius:18px; padding:28px; max-width:480px; width:100%; box-shadow:0 16px 40px rgba(15,23,42,.08); }
    h1 { font-size:24px; margin-bottom:10px; }
    p { color:#6b7280; line-height:1.6; margin-bottom:16px; }
    input { width:100%; padding:12px 14px; border-radius:10px; border:1px solid #d1d5db; margin-bottom:12px; }
    button { width:100%; padding:12px 16px; background:#2f9e8f; border:none; color:#fff; border-radius:10px; font-weight:700; cursor:pointer; }
    .error { color:#dc2626; margin-bottom:12px; }
    .success { color:#15803d; margin-bottom:12px; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Verify your email</h1>
    <p>We sent a 6-digit verification code to your registered email address. Please enter it below to confirm that this email belongs to you.</p>
    @if(auth()->check() && auth()->user()->email)
      <p style="margin-top:-8px; margin-bottom:18px; font-weight:600; color:#1f2937;">Code sent to: <span style="word-break:break-word;">{{ auth()->user()->email }}</span></p>
    @endif
    @if($errors->any())
      <div class="error">{{ $errors->first() }}</div>
    @endif
    @if(session('success'))
      <div class="success">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('email.verify') }}">
      @csrf
      <input type="text" name="code" maxlength="6" placeholder="Enter 6-digit code" required>
      <button type="submit">Verify Email</button>
    </form>
  </div>
</body>
</html>
