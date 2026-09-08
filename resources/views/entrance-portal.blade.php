  <!DOCTYPE html>
  <html lang="en">

  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Library Entrance Portal</title>
      <link
          href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap"
          rel="stylesheet">
      <style>
          *,
          *::before,
          *::after {
              box-sizing: border-box;
          }

          :root {
              --bg: #f6f7fb;
              --surface: #ffffff;
              --surface-2: #e8efec;
              --ink: #1d2b2a;
              --muted: #60716f;
              --line: #bfd0cb;
              --brand: #24786f;
              --brand-dark: #18564f;
              --gold: #b58a2a;
              --danger: #b7412e;
              --success: #247a4f;
              --shadow: 0 22px 60px rgba(20, 38, 35, 0.12);
              --shadow-sm: 0 10px 24px rgba(20, 38, 35, 0.08);
          }

          body {
              margin: 0;
              min-height: 100vh;
              font-family: "DM Sans", Arial, sans-serif;

              /* LIBRARY BACKGROUND IMAGE */
              background:
                  linear-gradient(180deg,
                      rgba(245, 247, 250, 0.72) 0%,
                      rgba(245, 247, 250, 0.82) 100%),
                  color: var(--ink);
          }

          .shell {
              min-height: 100vh;
              display: grid;
              grid-template-rows: auto 1fr;
          }

          /* ===== HEADER ===== */

          .topbar {
              min-height: 40px;
              padding: 6px 16px;
              box-sizing: border-box;
              background: rgba(255, 255, 255, 0.10);
              backdrop-filter: blur(2px);
              -webkit-backdrop-filter: blur(2px);
              border-bottom: 1px solid rgba(255, 255, 255, 0.45);
              display: flex;
              align-items: center;
              gap: 9px;
              position: relative;
              z-index: 50;
          }

          /* ===== LOGO ===== */

          .logo {
              width: 40px;
              height: 40px;
              border-radius: 50%;
              border: 1px solid var(--line);
              background: var(--surface-2);
              display: grid;
              place-items: center;
              overflow: hidden;
              font-weight: 800;
              color: var(--brand);
              flex-shrink: 0;
              font-size: 0;
          }

          .logo img {
              width: 50px ;
              height: 40px ;

              object-fit: contain ;

              border-radius: 50%;
          }

          /* ===== HEADER TITLE ===== */

          h1 {
              margin: 0;
              font-family: "Playfair Display", Georgia, serif;
              font-size: 30px;
              font-weight: 600;
              line-height: 1.1;
              letter-spacing: -0.2px;
          }

          .subtitle {
              margin-top: 3px;
              color: var(--muted);
              font-size: 15px;
              line-height: 1.2;
          }

          /* ===== RIGHT SIDE ===== */

          .top-actions {
              margin-left: auto;
              display: flex;
              align-items: center;
              gap: 5px;
              flex-wrap: nowrap;
              justify-content: flex-end;
          }

          .clock {
              min-width: 100px;
              height: 40px;
              text-align: center;
              padding: 0 12px;
              border: 1px solid var(--line);
              border-radius: 8px;
              background: var(--surface-2);
              font-weight: 700;
              font-size: 13px;
              font-variant-numeric: tabular-nums;
              display:flex;
              align-items: center;
              justify-content: center;
          }

          /* ===== SEARCH ===== */

          .search-panel {
              position: relative;
              min-width: 260px;
              max-width: 360px;
              width: 50%;
              z-index: 30;
          }

          .top-search-input {
              width: 100%;

              height: 40px;
              min-height: 40px;

              border: 1px solid var(--line);
              border-radius: 8px;

              padding: 0 14px;

              font: inherit;
              font-weight: 700;
              font-size: 13px;

              color: var(--ink);

              background: #fff;

              transition: border-radius 0.2s ease;

              outline: none;

              box-shadow: none;
          }

          .search-panel.open .top-search-input {
              border-bottom-left-radius: 0;
              border-bottom-right-radius: 0;
              opacity: 1;
          }

          .top-search-input:focus {
              outline: none;
              border-color: var(--brand);
              box-shadow: 0 0 0 3px rgba(36, 120, 111, 0.14);
          }

          .search-results {
              position: absolute;
              top: calc(100% + 1px);
              left: 0;
              right: 0;
              z-index: 30;
              display: none;
              background: #fff;
              border: 1px solid rgba(27, 45, 42, 0.52);
              border-radius: 0 0 14px 14px;
              border-top: none;
              box-shadow: 0 22px 38px rgba(18, 31, 30, 0.2);
              max-height: 560px;
              min-height: 72px;
              overflow-y: auto;
              overflow-x: hidden;
              overscroll-behavior: contain;
              scrollbar-width: thick;
              scrollbar-color: rgba(20, 32, 31, 0.82) rgba(214, 221, 219, 0.86);
              scrollbar-gutter: stable;
              padding-right: 8px;
          }

          .search-results::-webkit-scrollbar {
              width: 14px;
          }

          .search-results::-webkit-scrollbar-thumb {
              background: linear-gradient(180deg, rgba(36, 120, 111, 0.95), rgba(16, 74, 67, 0.9));
              border-radius: 999px;
              border: 3px solid rgba(214, 221, 219, 0.86);
          }

          .search-results::-webkit-scrollbar-track {
              background: rgba(214, 221, 219, 0.86);
              border-radius: 999px;
          }

          .search-results {
              scrollbar-color: rgba(36, 120, 111, 0.9) rgba(214, 221, 219, 0.86);
          }

          .search-results::after {
              content: "Scroll for more results";
              position: sticky;
              bottom: 0;
              display: block;
              padding: 8px 12px;
              font-size: 10px;
              letter-spacing: 0.08em;
              text-transform: uppercase;
              color: rgba(29, 43, 42, 0.7);
              background: linear-gradient(180deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.92) 32%, rgba(255, 255, 255, 1) 100%);
              text-align: center;
              border-top: 1px solid rgba(27, 45, 42, 0.12);
          }

          .search-item {
              display: flex;
              align-items: center;
              justify-content: space-between;
              gap: 12px;
              padding: 14px 16px;
              border-bottom: 1px solid var(--line);
              cursor: pointer;
              transition: background 0.15s ease;
          }

          .search-item:last-child {
              border-bottom: 0;
          }

          .search-item:hover {
              background: #f4fbf8;
          }

          .search-item-label {
              display: grid;
              gap: 4px;
              text-align: left;
          }

          .search-item-title {
              font-weight: 800;
          }

          .search-item-subtitle {
              color: var(--muted);
              font-size: 12px;
          }

          .search-results-empty {
              padding: 14px 16px;
              color: var(--muted);
              font-size: 13px;
          }

          .btn {
              border: 1px solid var(--line);
              background: var(--surface);
              color: var(--ink);
              border-radius: 8px;
              padding: 10px 14px;
              font: inherit;
              font-weight: 700;
              font-size: 13px;
              cursor: pointer;
              text-decoration: none;
              display: inline-flex;
              align-items: center;
              justify-content: center;
              min-height: 40px;
              height: 40px;
              white-space: nowrap;
          }

          .btn.primary {
              background: var(--brand);
              color: #fff;
              border-color: var(--brand);
          }

          .btn.gold {
              background: var(--gold);
              color: #fff;
              border-color: var(--gold);
          }

          .btn:hover {
              border-color: var(--brand);
          }

          .content {
              width: 100%;
              box-sizing: border-box;
              padding: 26px 24px 32px;
              display: grid;
              grid-template-columns: minmax(0, 0.42fr) minmax(0, 0.58fr);
              gap: 22px;
              align-items: start;
          }

          .panel {
              width: 100%;
              box-sizing: border-box;
              background: rgba(255, 255, 255, 0.94);
              border: 1px solid var(--line);
              border-radius: 12px;
              box-shadow: var(--shadow);
              overflow: hidden;
          }

          .panel-header {
              padding: 18px 20px;

              border-bottom: 1px solid var(--line);

              display: flex;
              align-items: center;
              justify-content: space-between;

              gap: 14px;
          }

          .panel-title {
              font-weight: 800;
              font-size: 16px;
          }

          .panel-note {
              color: var(--muted);
              font-size: 12px;
              margin-top: 3px;
          }

          .panel-body {
              padding: 20px;
          }

          .scanner-card {
              min-height: 420px;

              display: flex;
              align-items: center;
              justify-content: center;

              text-align: center;
          }

          .scanner-card .panel-body {
              width: 100%;
              box-sizing: border-box;

              padding: 18px;
          }

          .scanner-frame {
              width: 100%;
              max-width: none;
              min-height: 340px;
              box-sizing: border-box;
              padding: 26px 24px;
              border: 2px dashed var(--brand);
              border-radius: 12px;
              background: rgba(248, 251, 250, 0.94);
              box-shadow: var(--shadow-sm);
              display: flex;
              flex-direction: column;
              align-items: center;
              justify-content: center;
              text-align: center;
          }

          .scan-icon {
              width: 88px;
              height: 96px;
              margin: 0 auto 20px;
              border: 2px solid var(--line);
              border-radius: 8px;
              background:
                  repeating-linear-gradient(90deg,
                      #243331 0 4px,
                      transparent 4px 12px);

              position: relative;
              overflow: hidden;
          }


          .scan-icon::after {
              content: "";
              position: absolute;
              left: 8px;
              right: 8px;
              top: 50%;
              height: 3px;
              background: var(--gold);
              animation: scanline 1.6s ease-in-out infinite;
          }

          @keyframes scanline {

              0%,
              100% {
                  transform: translateY(-34px);
              }

              50% {
                  transform: translateY(34px);
              }
          }

          label {
              display: block;
              font-weight: 800;
              margin-bottom: 10px;
          }

          .barcode-input {
              width: 100%;
              height: 72px;
              border: 2px solid var(--brand);
              border-radius: 8px;
              padding: 0 22px;
              font-size: 30px;
              font-weight: 800;
              text-align: center;
              letter-spacing: 0;
              color: var(--ink);
              outline: none;
              background: #fff;
          }

          .barcode-input:focus {
              box-shadow: 0 0 0 4px rgba(36, 120, 111, 0.14);
          }

          .search-results {
              margin-top: 14px;
              border: 1px solid var(--line);
              border-radius: 12px;
              background: #fff;
              box-shadow: var(--shadow-sm);
              overflow: hidden;
          }

          .search-item {
              display: flex;
              align-items: center;
              justify-content: space-between;
              gap: 12px;
              padding: 14px 16px;
              border-bottom: 1px solid var(--line);
              cursor: pointer;
              transition: background 0.15s ease;
          }

          .search-item:last-child {
              border-bottom: 0;
          }

          .search-item:hover {
              background: #f4fbf8;
          }

          .search-item-label {
              display: grid;
              gap: 4px;
              text-align: left;
          }

          .search-item-title {
              font-weight: 800;
          }

          .search-item-subtitle {
              color: var(--muted);
              font-size: 12px;
          }

          .search-results.empty {
              padding: 14px 16px;
              color: var(--muted);
              font-size: 13px;
          }

          .hint {
              color: var(--muted);
              margin: 13px 0 0;
              line-height: 1.5;
          }

          .status-strip {
              margin-top: 18px;
              min-height: 50px;
              border-radius: 8px;
              padding: 14px 16px;
              display: flex;
              align-items: center;
              justify-content: center;
              font-weight: 800;
              background: var(--surface-2);
              color: var(--muted);
          }

          .status-strip.success {
              background: #e4f4eb;
              color: var(--success);
          }

          .status-strip.error {
              background: #f8e5e1;
              color: var(--danger);
          }

          .status-strip.warning {
              background: #fff3d7;
              color: #795300;
          }

          .visitors {
              width: 100%;
              border-collapse: collapse;
          }

          .visitors th {
              text-align: left;
              color: var(--muted);
              font-size: 11px;
              text-transform: uppercase;
              letter-spacing: 0.08em;
              border-bottom: 1px solid var(--line);
              padding: 12px 14px;
          }

          .visitors td {
              border-bottom: 1px solid var(--line);
              padding: 13px 14px;
              vertical-align: middle;
              font-size: 13px;
          }

          .visitors tr:last-child td {
              border-bottom: 0;
          }

          .name-cell {
              font-weight: 800;
          }

          .muted {
              color: var(--muted);
          }

          .voucher {
              display: inline-flex;
              border: 1px solid #d3b45d;
              background: #fff8df;
              color: #5b4100;
              border-radius: 8px;
              padding: 8px 12px;
              font-weight: 900;
              font-size: 18px;
              letter-spacing: 0;
          }

          .modal-backdrop {
              position: fixed;
              inset: 0;
              background: rgba(10, 24, 22, 0.55);
              display: none;
              align-items: center;
              justify-content: center;
              padding: 24px;
              z-index: 1000;
              opacity: 0;
              transition: opacity 0.2s ease;
          }

          .modal-backdrop.open {
              display: flex !important;
              opacity: 1;
              pointer-events: auto;
          }

          .modal {
              width: min(760px, 100%);
              background: #fff;
              border-radius: 14px;
              box-shadow: 0 30px 90px rgba(0, 0, 0, 0.28);
              overflow: hidden;
          }

          .modal-head {
              padding: 20px 24px;
              background: var(--brand);
              color: #fff;
              display: flex;
              align-items: center;
              justify-content: space-between;
              gap: 14px;
          }

          .modal-head h2 {
              margin: 0;
              font-family: "Playfair Display", Georgia, serif;
              font-size: 27px;
              letter-spacing: 0;
          }

          .close {
              width: 38px;
              height: 38px;
              border: 1px solid rgba(255, 255, 255, 0.45);
              background: transparent;
              color: #fff;
              border-radius: 8px;
              font-size: 24px;
              cursor: pointer;
          }

          .modal-body {
              padding: 24px;
              display: grid;
              grid-template-columns: 210px 1fr;
              gap: 24px;
          }

          .photo {
              width: 210px;
              height: 250px;
              border-radius: 8px;
              border: 1px solid var(--line);
              background: var(--surface-2);
              object-fit: cover;
              display: grid;
              place-items: center;
              font-size: 54px;
              font-weight: 900;
              color: var(--brand);
          }

          .details {
              display: grid;
              gap: 13px;
              align-content: start;
          }

          .detail-row {
              display: grid;
              grid-template-columns: 150px 1fr;
              gap: 12px;
              padding-bottom: 12px;
              border-bottom: 1px solid var(--line);
          }

          .detail-label {
              color: var(--muted);
              font-weight: 700;
          }

          .detail-value {
              font-weight: 800;
          }

          .toast {
              position: fixed;
              right: 24px;
              bottom: 24px;
              min-width: 280px;
              max-width: calc(100vw - 48px);
              padding: 14px 18px;
              border-radius: 8px;
              color: #fff;
              background: var(--brand);
              font-weight: 800;
              transform: translateY(80px);
              opacity: 0;
              transition: 0.25s ease;
              z-index: 1100;
          }

          .toast.show {
              transform: translateY(0);
              opacity: 1;
          }

          .empty-row {
              text-align: center;
              color: var(--muted);
              padding: 28px 14px;
          }

          @media (max-width: 900px) {
              .content {
                  grid-template-columns: 1fr;
                  padding: 18px;
              }

              .topbar {
                  padding: 12px 18px;
                  flex-wrap: wrap;
              }

              .top-actions {
                  width: 100%;
                  margin-left: 0;
                  justify-content: space-between;
              }

              .modal-body {
                  grid-template-columns: 1fr;
              }

              .photo {
                  width: 100%;
                  height: 230px;
              }

              .detail-row {
                  grid-template-columns: 1fr;
                  gap: 4px;
              }

              .barcode-input {
                  font-size: 24px;
              }
          }
      </style>
  </head>

  <body>
      @php
          $dashboardRoute = 'login';
          $dashboardLabel = 'Back to Login';

          if (auth()->check()) {
              $dashboardRoute = match (auth()->user()->role) {
                  'admin' => 'admin.dashboard',
                  'staff' => 'staff.dashboard',
                  'researcher' => 'researcher.dashboard',
                  'visitor' => 'visitor.dashboard',
                  default => 'student.dashboard',
              };
              $dashboardLabel = 'Back to Dashboard';
          }
      @endphp
      <div class="shell">
          <header class="topbar">

              <div class="logo">
                  <img src="{{ asset('images/library logos.jpg') }}" alt="Panabo City Library Logo"
                      style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
              </div>
              <div>
                  <h1>Library Entrance Portal</h1>
                  <div class="subtitle">Panabo City Library barcode ID scanner</div>
              </div>
              <div class="top-actions">
                  <div class="search-panel">
                      <input id="searchInput" class="top-search-input" name="query"
                          placeholder="Search verified account" autocomplete="off" aria-label="Search verified account">
                      <div class="search-results" id="searchResults" hidden aria-live="polite"></div>
                  </div>
                  <div class="clock" id="clock">--:--:--</div>
                  <a href="{{ route('login') }}" class="btn primary">Back to Login</a>
              </div>
          </header>

          <main class="content">
              <section class="panel scanner-card">
                  <div class="panel-body" style="width: 100%;">
                      <div class="scanner-frame">
                          <div class="scan-icon" aria-hidden="true"></div>

                          <form id="scanForm" autocomplete="off">
                              <label for="barcodeInput">Scan Library ID Barcode</label>
                              <input id="barcodeInput" class="barcode-input" name="barcode_id" inputmode="numeric"
                                  placeholder="202600123" autofocus>
                          </form>
                          <p class="hint">The scanner cursor stays here automatically. Scan the ID or type the barcode
                              and press Enter.</p>
                          <div class="status-strip" id="scanStatus">Ready for next scan</div>
                      </div>
              </section>

              <section class="panel">
                  <div class="panel-header">
                      <div>
                          <div class="panel-title">Recent Visitors</div>
                          <div class="panel-note">Latest successful entrance logs</div>
                      </div>
                  </div>
                  <div style="overflow-x:auto;">
                      <table class="visitors">
                          <thead>
                              <tr>
                                  <th>Name</th>
                                  <th>ID</th>
                                  <th>Time In</th>
                              </tr>
                          </thead>
                          <tbody id="recentVisitorsBody">
                              @forelse($recentVisitors as $visitor)
                                  <tr>
                                      <td>
                                          <div class="name-cell">{{ $visitor['name'] }}</div>
                                          <div class="muted">{{ $visitor['school'] }}</div>
                                      </td>
                                      <td>{{ $visitor['barcode_id'] }}</td>
                                      <td data-entry-timestamp="{{ $visitor['entry_timestamp'] }}">
                                          {{ $visitor['entry_time'] }}</td>
                                  </tr>
                              @empty
                                  <tr id="emptyVisitorsRow">
                                      <td colspan="3" class="empty-row">No scanned visitors yet.</td>
                                  </tr>
                              @endforelse
                          </tbody>
                      </table>
                  </div>
              </section>
          </main>
      </div>

      <div class="modal-backdrop" id="memberModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
          <div class="modal">
              <div class="modal-head">
                  <h2 id="modalTitle">Welcome to Panabo City Library</h2>
                  <button class="close" type="button" onclick="closeModal()" aria-label="Close">&times;</button>
              </div>
              <div class="modal-body">
                  <div id="memberPhoto" class="photo">ID</div>
                  <div class="details">
                      <div class="detail-row">
                          <div class="detail-label">Full Name</div>
                          <div class="detail-value" id="memberName">-</div>
                      </div>
                      <div class="detail-row">
                          <div class="detail-label">School</div>
                          <div class="detail-value" id="memberSchool">-</div>
                      </div>
                      <div class="detail-row">
                          <div class="detail-label">Library ID</div>
                          <div class="detail-value" id="memberLibraryId">-</div>
                      </div>
                      <div class="detail-row">
                          <div class="detail-label">Date & Time</div>
                          <div class="detail-value" id="memberEntryTime">-</div>
                      </div>
                      <!-- WiFi voucher removed from UI -->
                  </div>
              </div>
          </div>
      </div>

      <div class="toast" id="toast"></div>

      <script>
          const barcodeInput = document.getElementById('barcodeInput');
          const scanForm = document.getElementById('scanForm');
          const scanStatus = document.getElementById('scanStatus');
          const toast = document.getElementById('toast');
          let scanTimer = null;
          let isScanning = false;

          function focusScanner() {
              barcodeInput.focus();
              barcodeInput.select();
          }

          function updateClock() {
              document.getElementById('clock').textContent = new Date().toLocaleTimeString('en-PH', {
                  hour: '2-digit',
                  minute: '2-digit',
                  second: '2-digit'
              });
          }

          function playTone(type) {
              const AudioContext = window.AudioContext || window.webkitAudioContext;
              if (!AudioContext) return;

              const context = new AudioContext();
              const oscillator = context.createOscillator();
              const gain = context.createGain();
              oscillator.connect(gain);
              gain.connect(context.destination);
              oscillator.frequency.value = type === 'success' ? 880 : 180;
              oscillator.type = 'sine';
              gain.gain.setValueAtTime(0.0001, context.currentTime);
              gain.gain.exponentialRampToValueAtTime(0.18, context.currentTime + 0.02);
              gain.gain.exponentialRampToValueAtTime(0.0001, context.currentTime + 0.18);
              oscillator.start();
              oscillator.stop(context.currentTime + 0.2);
          }

          function showToast(message, type = 'success') {
              toast.textContent = message;
              toast.style.background = type === 'error' ? 'var(--danger)' : type === 'warning' ? '#8a6100' : 'var(--brand)';
              toast.classList.add('show');
              setTimeout(() => toast.classList.remove('show'), 2600);
          }

          function setStatus(message, type = '') {
              scanStatus.className = 'status-strip';
              if (type) scanStatus.classList.add(type);
              scanStatus.textContent = message;
          }

          function initials(name) {
              return String(name || 'ID')
                  .trim()
                  .split(/\s+/)
                  .slice(0, 2)
                  .map(part => part.charAt(0).toUpperCase())
                  .join('') || 'ID';
          }

          let modalCloseTimer = null;
          const MODAL_AUTO_CLOSE_MS = 3000;

          function showMemberModal(member) {
              document.getElementById('memberName').textContent = member.full_name;
              document.getElementById('memberSchool').textContent = member.school || 'Not listed';
              document.getElementById('memberLibraryId').textContent = member.library_id_number;
              document.getElementById('memberEntryTime').textContent = `${member.entry_date} at ${member.entry_time}`;

              const photo = document.getElementById('memberPhoto');
              if (member.profile_picture) {
                  photo.innerHTML =
                      `<img src="${member.profile_picture}" alt="${member.full_name}" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">`;
              } else {
                  photo.textContent = initials(member.full_name);
              }

              const modal = document.getElementById('memberModal');
              modal.style.display = 'flex';
              modal.style.opacity = '1';
              modal.style.pointerEvents = 'auto';
              modal.classList.add('open');

              if (modalCloseTimer) {
                  clearTimeout(modalCloseTimer);
              }
              modalCloseTimer = setTimeout(() => {
                  closeModal();
              }, MODAL_AUTO_CLOSE_MS);
          }

          function closeModal() {
              const modal = document.getElementById('memberModal');
              modal.classList.remove('open');
              modal.style.opacity = '0';
              modal.style.pointerEvents = 'none';
              modal.style.display = 'none';
              if (modalCloseTimer) {
                  clearTimeout(modalCloseTimer);
                  modalCloseTimer = null;
              }
              focusScanner();
          }

          function renderRecentVisitors(visitors) {
              const body = document.getElementById('recentVisitorsBody');
              if (!visitors || visitors.length === 0) {
                  body.innerHTML =
                      '<tr id="emptyVisitorsRow"><td colspan="3" class="empty-row">No scanned visitors yet.</td></tr>';
                  return;
              }

              body.innerHTML = visitors.map(visitor => `
        <tr>
          <td>
            <div class="name-cell">${escapeHtml(visitor.name)}</div>
            <div class="muted">${escapeHtml(visitor.school)}</div>
          </td>
          <td>${escapeHtml(visitor.barcode_id)}</td>
          <td data-entry-timestamp="${escapeHtml(visitor.entry_timestamp)}">${escapeHtml(visitor.entry_time)}</td>
        </tr>
      `).join('');
              updateRecentVisitorTimes();
          }

          function formatTimeFromTimestamp(timestamp) {
              const value = Date.parse(timestamp);
              if (Number.isNaN(value)) return timestamp || '';
              const date = new Date(value);
              let hours = date.getHours();
              const minutes = date.getMinutes();
              const seconds = date.getSeconds();
              const suffix = hours >= 12 ? 'PM' : 'AM';
              hours = hours % 12 || 12;
              const minuteText = String(minutes).padStart(2, '0');
              const secondText = String(seconds).padStart(2, '0');
              return `${hours}:${minuteText}:${secondText} ${suffix}`;
          }

          function updateRecentVisitorTimes() {
              document.querySelectorAll('#recentVisitorsBody td[data-entry-timestamp]').forEach(td => {
                  const timestamp = td.dataset.entryTimestamp;
                  if (!timestamp) return;
                  td.textContent = formatTimeFromTimestamp(timestamp);
              });
          }

          function escapeHtml(value) {
              return String(value ?? '').replace(/[&<>"']/g, char => ({
                  '&': '&amp;',
                  '<': '&lt;',
                  '>': '&gt;',
                  '"': '&quot;',
                  "'": '&#039;'
              } [char]));
          }

          async function submitScan() {
              const barcode = barcodeInput.value.trim();
              if (!barcode || isScanning) return;

              isScanning = true;
              setStatus('Searching member record...', 'warning');

              try {
                  const response = await fetch('{{ route('entrance.portal.scan') }}', {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                          'Accept': 'application/json',
                          'X-CSRF-TOKEN': '{{ csrf_token() }}'
                      },
                      body: JSON.stringify({
                          barcode_id: barcode
                      })
                  });

                  const data = await response.json();

                  if (!response.ok || !data.success) {
                      playTone('error');
                      setStatus(data.message || 'Barcode ID not found.', 'error');
                      showToast(data.message || 'Barcode ID not found.', 'error');
                      return;
                  }

                  playTone('success');
                  showMemberModal(data.member);
                  renderRecentVisitors(data.recent_visitors);

                  if (data.duplicate) {
                      setStatus(data.message, 'warning');
                      showToast(data.message, 'warning');
                  } else {
                      setStatus(data.message, 'success');
                      showToast('Welcome to Panabo City Library', 'success');
                  }
              } catch (error) {
                  playTone('error');
                  setStatus('Unable to connect to the scanner endpoint.', 'error');
                  showToast('Unable to connect to the scanner endpoint.', 'error');
              } finally {
                  barcodeInput.value = '';
                  isScanning = false;
                  setTimeout(focusScanner, 150);
              }
          }

          scanForm.addEventListener('submit', event => {
              event.preventDefault();
              submitScan();
          });

          barcodeInput.addEventListener('input', () => {
              clearTimeout(scanTimer);
              scanTimer = setTimeout(() => {
                  if (barcodeInput.value.trim().length >= 5) submitScan();
              }, 240);
          });

          document.addEventListener('click', event => {
              if (event.target.id === 'memberModal') return closeModal();
              if (event.target.closest('.search-panel') || event.target.closest('#searchResults')) return;
              if (event.target.closest('button') || event.target.closest('a')) return;
              if (!event.target.closest('#barcodeInput')) focusScanner();
          });

          document.addEventListener('keydown', event => {
              if (event.key === 'Escape') closeModal();
          });

          updateClock();
          setInterval(updateClock, 1000);
          setInterval(updateRecentVisitorTimes, 1000);
          window.addEventListener('load', () => {
              focusScanner();
              searchInputFocus();
          });

          const searchInput = document.getElementById('searchInput');
          const searchResults = document.getElementById('searchResults');
          let searchDebounce = null;
          let searchItems = [];

          function searchInputFocus() {
              searchInput.addEventListener('focus', () => {
                  if (searchInput.value.trim()) {
                      performSearch();
                  }
              });
          }

          function buildSearchItem(item, index) {
              return `
        <div class="search-item" role="button" data-index="${index}" onclick="chooseSearchResult(${index})">
          <div class="search-item-label">
            <div class="search-item-title">${escapeHtml(item.full_name)}</div>
            <div class="search-item-subtitle">${escapeHtml(item.barcode_id)} · ${escapeHtml(item.school)}</div>
          </div>
          <span class="btn primary">Enter</span>
        </div>
      `;
          }

          function renderSearchResults(items) {
              if (!items || items.length === 0) {
                  searchResults.innerHTML = '<div class="search-results-empty">No verified account found.</div>';
                  searchResults.hidden = false;
                  searchResults.style.display = 'block';
                  document.querySelector('.search-panel').classList.add('open');
                  return;
              }

              searchResults.innerHTML = items.map(buildSearchItem).join('');
              searchResults.hidden = false;
              searchResults.style.display = 'block';
              document.querySelector('.search-panel').classList.add('open');
          }

          function clearSearchResults() {
              searchResults.hidden = true;
              searchResults.style.display = 'none';
              searchResults.innerHTML = '';
              searchItems = [];
              document.querySelector('.search-panel').classList.remove('open');
          }

          async function performSearch() {
              const query = searchInput.value.trim();
              if (query.length < 2) {
                  return clearSearchResults();
              }

              try {
                  const response = await fetch('{{ route('entrance.portal.search') }}?query=' + encodeURIComponent(
                      query));
                  if (!response.ok) {
                      return clearSearchResults();
                  }

                  const results = await response.json();
                  searchItems = results;
                  renderSearchResults(results);
              } catch (error) {
                  clearSearchResults();
              }
          }

          function chooseSearchResult(index) {
              const item = searchItems[index];
              if (!item) return;
              barcodeInput.value = item.barcode_id;
              clearSearchResults();
              submitScan();
          }

          searchInput.addEventListener('input', () => {
              clearTimeout(searchDebounce);
              searchDebounce = setTimeout(performSearch, 240);
          });

          searchInput.addEventListener('keydown', event => {
              if (event.key === 'Enter') {
                  event.preventDefault();
                  if (searchItems.length === 1) {
                      chooseSearchResult(0);
                  }
              }
          });

          document.addEventListener('click', event => {
              if (!event.target.closest('.search-panel') && !event.target.closest('#searchResults')) {
                  clearSearchResults();
              }
          });
      </script>
  </body>

  </html>
