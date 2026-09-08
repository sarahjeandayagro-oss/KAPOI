<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $book->title }} — OPAC</title>
  <style>
    :root {
      --bg: #f7fafc;
      --card: #ffffff;
      --text: #1f2937;
      --muted: #6b7280;
      --border: #e5e7eb;
      --radius: 20px;
      --shadow: 0 10px 30px rgba(15,23,42,0.08);
      --success: #22c55e;
      --error: #ef4444;
    }
    * { box-sizing: border-box; }
    body { margin: 0; font-family: Inter, system-ui, sans-serif; background: var(--bg); color: var(--text); }
    .page { max-width: 1100px; margin: 0 auto; padding: 24px; }
    .breadcrumbs { margin-bottom: 18px; font-size: 13px; color: var(--muted); }
    .breadcrumbs a { color: var(--text); text-decoration: none; }
    .breadcrumbs a:hover { text-decoration: underline; }
    .detail-grid { display: grid; grid-template-columns: 1.2fr .8fr; gap: 24px; }
    .book-panel { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); }
    .book-cover { min-height: 420px; background: #f3f4f6; display: flex; align-items: center; justify-content: center; position: relative; }
    .book-cover img { width: 100%; height: 100%; object-fit: cover; }
    .book-cover-fallback { font-size: 96px; font-weight: 900; color: white; width: 100%; height: 100%; display: grid; place-items: center; text-transform: uppercase; }
    .book-info { padding: 28px; display: flex; flex-direction: column; gap: 18px; }
    .book-title { font-size: 28px; font-weight: 800; line-height: 1.1; }
    .book-author { font-size: 16px; color: var(--muted); }
    .status-pill { display: inline-flex; align-items: center; padding: 8px 14px; border-radius: 999px; font-size: 12px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; }
    .status-available { background: rgba(34,197,94,.12); color: var(--success); }
    .status-borrowed { background: rgba(239,68,68,.12); color: var(--error); }
    .summary { font-size: 15px; line-height: 1.7; color: #374151; }
    .meta-block { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-top: 18px; }
    .meta-card { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 18px; padding: 18px; }
    .meta-label { font-size: 12px; font-weight: 700; color: #374151; margin-bottom: 6px; }
    .meta-value { font-size: 14px; color: #111827; }
    .meta-text { font-size: 13px; color: var(--muted); }
    .actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 24px; }
    .btn { display: inline-flex; align-items: center; justify-content: center; padding: 12px 18px; border-radius: 12px; text-decoration: none; color: white; font-weight: 700; }
    .btn-secondary { background: #e5e7eb; color: #111827; }
    .btn-primary { background: #2f9e8f; }
    .modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(15,23,42,0.55);
      display: none;
      align-items: center;
      justify-content: center;
      padding: 24px;
      z-index: 1000;
    }
    .modal-backdrop.open { display: flex; }
    .modal {
      width: min(520px, 100%);
      background: #fff;
      border-radius: 22px;
      overflow: hidden;
      box-shadow: 0 30px 90px rgba(15,23,42,0.18);
    }
    .modal-head {
      padding: 22px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: linear-gradient(135deg, #2f9e8f, #1f766e);
      color: white;
      gap: 12px;
    }
    .modal-head h2 { margin: 0; font-size: 20px; }
    .modal-close {
      border: none;
      background: rgba(255,255,255,0.16);
      color: white;
      width: 40px;
      height: 40px;
      border-radius: 12px;
      cursor: pointer;
      font-size: 18px;
    }
    .modal-body {
      padding: 24px;
      display: grid;
      gap: 18px;
    }
    .login-card {
      background: #f8fafc;
      border-radius: 18px;
      padding: 20px;
      display: grid;
      gap: 16px;
    }
    .login-card h3 { margin: 0; font-size: 20px; }
    .login-field { display: grid; gap: 8px; }
    .login-field label { font-size: 13px; font-weight: 700; color: #334155; }
    .login-field input { width: 100%; border: 1px solid #cbd5e1; border-radius: 12px; padding: 14px 16px; font: inherit; color: #1f2937; background: white; }
    .login-field input:focus { outline: none; border-color: #2f9e8f; box-shadow: 0 0 0 4px rgba(47,158,143,.12); }
    .login-actions { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
    .login-actions button, .login-actions a { padding: 12px 18px; border-radius: 12px; font: inherit; cursor: pointer; text-decoration: none; border: none; }
    .login-actions button { background: #2f9e8f; color: #fff; }
    .login-actions a { color: #2f9e8f; background: transparent; }
    .login-error { color: #b91c1c; background: rgba(239,68,68,.1); padding: 12px 14px; border-radius: 12px; font-size: 13px; }
    @media (max-width: 900px) { .detail-grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
  <div class="page">
    <div class="breadcrumbs">
      <a href="{{ route('opac.index') }}">OPAC</a> / {{ $book->title }}
    </div>
    <div class="detail-grid">
      <div class="book-panel book-cover">
        @if($book->cover_url)
          <img src="{{ $book->cover_url }}" alt="{{ $book->title }}">
        @else
          @php
            $catColors = [
              'Fil (Filipiniana)' => ['#2F9E8F','#1a7a6e'],
              'Cir (Circulation)' => ['#3B82F6','#2563EB'],
              'Gen. Ref.' => ['#8B5CF6','#7C3AED'],
              'F (Fiction)' => ['#F59E0B','#D97706'],
              'D (Dissertation)' => ['#EF4444','#DC2626'],
              'T (Thesis)' => ['#10B981','#059669'],
              'Journal' => ['#14B8A6','#0F766E'],
            ];
            $colors = $catColors[$book->category] ?? ['#6B7280','#4B5563'];
          @endphp
          <div class="book-cover-fallback" style="background: linear-gradient(135deg, {{ $colors[0] }}, {{ $colors[1] }});">
            {{ strtoupper(substr($book->title, 0, 1)) }}
          </div>
        @endif
      </div>
      <div class="book-info">
        <div>
          <div class="book-title">{{ $book->title }}</div>
          <div class="book-author">by {{ $book->author }}</div>
          <div class="status-pill {{ ($book->available ?? 0) > 0 ? 'status-available' : 'status-borrowed' }}">
            {{ ($book->available ?? 0) > 0 ? 'Available' : 'Unavailable' }}
          </div>
        </div>
        <div class="summary">
          {{ $book->summary ?? 'No summary description is available for this title.' }}
        </div>
        <div class="meta-block">
          <div class="meta-card">
            <div class="meta-label">Accession</div>
            <div class="meta-value">{{ $book->accession_number }}</div>
            <div class="meta-text">Neat catalog identifier for shelving and lookup.</div>
          </div>
          <div class="meta-card">
            <div class="meta-label">Book Barcode</div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
              <img src="{{ route('barcode.book.png', ['barcode' => $book->barcode]) }}" alt="Barcode: {{ $book->barcode }}" style="height: 50px; width: 100%; object-fit: contain;">
              <div class="meta-value" style="font-family: monospace; font-size: 12px;">{{ $book->barcode }}</div>
            </div>
          </div>
          <div class="meta-card">
            <div class="meta-label">Location</div>
            <div class="meta-value">{{ $book->section_location ?? 'General collection' }}</div>
          </div>
          <div class="meta-card">
            <div class="meta-label">Inventory</div>
            <div class="meta-value">{{ $book->available ?? 0 }} of {{ $book->copies ?? 0 }} available</div>
          </div>
        </div>
        <div class="actions">
          <a class="btn btn-secondary" href="{{ route('opac.index') }}">Back to Catalog</a>
        <!--  <a class="btn btn-primary" href="javascript:window.scrollTo(0,document.body.scrollHeight);">Read Details</a> -->
        </div>
      </div>
      @auth
        @if(in_array(auth()->user()->role, ['student', 'visitor']))
          <div class="actions">
              <button class="btn btn-primary" onclick="requestBook({{ $book->id }})">Request Book</button>
          </div>
        @endif
      @else
        <div class="actions">
          <button class="btn btn-primary" onclick="openLoginModal()">Request Book</button>
        </div>
      @endauth

      <div class="modal-backdrop" id="loginModal" role="dialog" aria-modal="true" aria-labelledby="loginModalTitle">
        <div class="modal">
          <div class="modal-head">
            <div>
              <h2 id="loginModalTitle">Sign in to request</h2>
              <p style="margin: 8px 0 0; font-size: 13px; opacity: .82;">Use your library account to request this title.</p>
            </div>
            <button class="modal-close" type="button" onclick="closeLoginModal()" aria-label="Close login modal">×</button>
          </div>
          <div class="modal-body">
            <div class="login-card">
              <h3>Library Account Login</h3>
              <div class="login-field">
                <label for="modal_user_id">Library ID or Email</label>
                <input id="modal_user_id" name="user_id" type="text" autocomplete="username" placeholder="Enter Library ID or email" />
              </div>
              <div class="login-field">
                <label for="modal_password">Password</label>
                <input id="modal_password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" />
              </div>
              <div class="login-error" id="loginModalError" style="display:none;"></div>
              <div class="login-actions">
                <button type="button" onclick="submitModalLogin()">Login & Continue</button>
                <a href="{{ route('login') }}">Full login page</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      @auth
        @if(in_array(auth()->user()->role, ['student', 'visitor']))
          <script>
          async function requestBook(bookId) {
              const ok = await showConfirm('Are you sure you want to request this book for borrowing?');
              if (!ok) return;

              fetch('{{ route("opac.request-borrow") }}', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                  },
                  body: JSON.stringify({ book_id: bookId })
              })
              .then(r => r.json())
              .then(data => {
                  if (data.success) {
                      showResponseModal('Request sent successfully.', true);
                  } else {
                      showResponseModal(data.error || 'Unable to submit request.', false);
                  }
              })
              .catch(() => {
                  showResponseModal('Unable to submit request. Please try again.', false);
              });
          }
          </script>
        @endif
      @endauth
    </div>
  </div>

  <div class="modal-backdrop" id="responseModal" role="dialog" aria-modal="true" aria-labelledby="responseModalTitle">
    <div class="modal">
      <div class="modal-head">
        <div>
          <h2 id="responseModalTitle">Request Status</h2>
          <p id="responseModalSubtitle" style="margin: 8px 0 0; font-size: 13px; opacity: .82;">Your book request status will appear here.</p>
        </div>
        <button class="modal-close" type="button" onclick="closeResponseModal()" aria-label="Close response modal">×</button>
      </div>
      <div class="modal-body" style="padding-top: 16px; gap: 12px;">
        <div id="responseMessage" style="font-size: 15px; line-height: 1.6; color: #111827;"></div>
        <div style="display: flex; justify-content: flex-end;">
          <button class="btn btn-primary" type="button" onclick="closeResponseModal()">OK</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Confirm modal -->
  <div class="modal-backdrop" id="confirmModal" role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle">
    <div class="modal">
      <div class="modal-head">
        <div>
          <h2 id="confirmModalTitle">Confirm Request</h2>
          <p style="margin: 8px 0 0; font-size: 13px; opacity: .82;">Please confirm before continuing.</p>
        </div>
        <button class="modal-close" type="button" onclick="closeConfirmModal()" aria-label="Close confirmation">×</button>
      </div>
      <div class="modal-body" style="padding-top: 16px;">
        <div id="confirmModalMessage" style="font-size: 15px; line-height: 1.6; color: #111827;"></div>
        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 16px;">
          <button class="btn btn-secondary" type="button" onclick="closeConfirmModal()">Cancel</button>
          <button class="btn btn-primary" type="button" id="confirmModalOk">OK</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function openLoginModal() {
      document.getElementById('loginModal').classList.add('open');
      document.getElementById('loginModalError').style.display = 'none';
      document.getElementById('modal_user_id').value = '';
      document.getElementById('modal_password').value = '';
    }

    function closeLoginModal() {
      document.getElementById('loginModal').classList.remove('open');
    }

    function submitModalLogin() {
      const userId = document.getElementById('modal_user_id').value.trim();
      const password = document.getElementById('modal_password').value;
      const errorBox = document.getElementById('loginModalError');

      errorBox.style.display = 'none';
      errorBox.textContent = '';

      if (!userId || !password) {
        errorBox.textContent = 'Please enter both Library ID/email and password.';
        errorBox.style.display = 'block';
        return;
      }

      fetch('{{ route('login.ajax') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ user_id: userId, password })
      })
      .then(async response => {
        const data = await response.json();
        if (!response.ok) {
          throw new Error(data.message || 'Login failed.');
        }
        return data;
      })
      .then(() => {
        window.location.reload();
      })
      .catch(error => {
        errorBox.textContent = error.message;
        errorBox.style.display = 'block';
      });
    }

    function showResponseModal(message, success) {
      const responseModal = document.getElementById('responseModal');
      const responseMessage = document.getElementById('responseMessage');
      const responseSubtitle = document.getElementById('responseModalSubtitle');

      responseMessage.textContent = message;
      responseSubtitle.textContent = success ? 'Your request was processed by the library system.' : 'There was a problem with your request.';
      responseModal.classList.add('open');
    }

    function showConfirm(message) {
      return new Promise(function (resolve) {
        const modal = document.getElementById('confirmModal');
        const msg = document.getElementById('confirmModalMessage');
        const ok = document.getElementById('confirmModalOk');
        if (!modal || !msg || !ok) return resolve(false);
        function done(val) {
          modal.classList.remove('open');
          ok.removeEventListener('click', onOk);
          document.removeEventListener('click', onDoc);
          resolve(val);
        }
        function onOk() { done(true); }
        function onDoc(e) { if (e.target === modal) done(false); }
        msg.textContent = message;
        modal.classList.add('open');
        ok.addEventListener('click', onOk);
        document.addEventListener('click', onDoc);
      });
    }

    function closeConfirmModal() {
      const modal = document.getElementById('confirmModal');
      if (modal) modal.classList.remove('open');
    }

    function closeResponseModal() {
      document.getElementById('responseModal').classList.remove('open');
    }

    document.addEventListener('click', function(event) {
      const loginModal = document.getElementById('loginModal');
      if (loginModal && loginModal.classList.contains('open') && event.target === loginModal) {
        closeLoginModal();
      }
      const responseModal = document.getElementById('responseModal');
      if (responseModal && responseModal.classList.contains('open') && event.target === responseModal) {
        closeResponseModal();
      }
      const confirmModal = document.getElementById('confirmModal');
      if (confirmModal && confirmModal.classList.contains('open') && event.target === confirmModal) {
        closeConfirmModal();
      }
    });
  </script>
