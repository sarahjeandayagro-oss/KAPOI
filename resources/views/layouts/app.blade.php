<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Panabo City Library')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @stack('head')
</head>
<body class="app-shell">
  <div class="app-layout">
    @isset($sidebar)
      {!! $sidebar !!}
    @endisset
    <main class="app-main">
      @isset($topbar)
        {!! $topbar !!}
      @endisset
      <div class="app-content">
        @yield('content')
      </div>
    </main>
  </div>
  <script>
    function showPanel(name) {
      document.querySelectorAll('.panel').forEach((panel) => panel.classList.remove('active'));
      const target = document.getElementById('panel-' + name);
      if (target) target.classList.add('active');

      document.querySelectorAll('[data-panel-button]').forEach((button) => button.classList.remove('active'));
      const activeButton = document.querySelector('[data-panel-button="' + name + '"]');
      if (activeButton) {
        activeButton.classList.add('active');
        const title = document.getElementById('topbarTitle');
        if (title && activeButton.dataset.title) title.textContent = activeButton.dataset.title;
      }
    }
  </script>

  <!-- Confirmation modal (global) -->
  <div id="confirm-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:1200; align-items:center; justify-content:center;">
    <div id="confirm-modal" style="width:520px; max-width:95vw; background:#fff; border-radius:12px; padding:18px; box-shadow:0 24px 56px rgba(15,23,42,0.2);">
      <div style="font-weight:700; margin-bottom:8px;">Confirm action</div>
      <div id="confirm-modal-message" style="color:#374151; margin-bottom:18px;">Are you sure you want to perform this action?</div>
      <div style="display:flex; justify-content:flex-end; gap:8px;">
        <button id="confirm-modal-cancel" class="btn" style="background:#f3f4f6;">Cancel</button>
        <button id="confirm-modal-confirm" class="btn primary" style="background:#2f9e8f; color:#fff;">Confirm</button>
      </div>
    </div>
  </div>

  <script>
    (function () {
      // Show confirm modal returns a Promise resolved with true/false
      function showConfirm(message) {
        return new Promise((resolve) => {
          const overlay = document.getElementById('confirm-modal-overlay');
          const msg = document.getElementById('confirm-modal-message');
          const btnOk = document.getElementById('confirm-modal-confirm');
          const btnCancel = document.getElementById('confirm-modal-cancel');
          if (!overlay || !msg || !btnOk || !btnCancel) return resolve(false);
          msg.textContent = message || 'Are you sure you want to continue?';
          overlay.style.display = 'flex';

          function cleanup() {
            overlay.style.display = 'none';
            btnOk.removeEventListener('click', onOk);
            btnCancel.removeEventListener('click', onCancel);
            overlay.removeEventListener('click', onOverlayClick);
          }
          function onOk(e) { e && e.preventDefault(); cleanup(); resolve(true); }
          function onCancel(e) { e && e.preventDefault(); cleanup(); resolve(false); }
          function onOverlayClick(e) { if (e.target === overlay) { cleanup(); resolve(false); } }

          btnOk.addEventListener('click', onOk);
          btnCancel.addEventListener('click', onCancel);
          overlay.addEventListener('click', onOverlayClick);
        });
      }

      // Handler for elements with data-confirm attribute and forms with class 'confirmable'
      function attachConfirmHandlers() {
        // Links and buttons (data-confirm)
        document.querySelectorAll('[data-confirm]').forEach(function (el) {
          // avoid attaching twice
          if (el.__confirmAttached) return; el.__confirmAttached = true;
          el.addEventListener('click', function (evt) {
            // Let modifier keys (Ctrl/Cmd/Shift/Alt) pass through for user convenience (open in new tab etc.)
            if (evt.ctrlKey || evt.metaKey || evt.shiftKey || evt.altKey) return;
            evt.preventDefault();
            const message = el.getAttribute('data-confirm') || 'Are you sure?';
            showConfirm(message).then(function (ok) {
              if (!ok) return;
              // If button inside a form (type=submit), submit the parent form
              if (el.tagName.toLowerCase() === 'button') {
                const type = (el.getAttribute('type') || '').toLowerCase();
                if (type === 'submit') {
                  const form = el.closest('form'); if (form) {
                    form.__confirmed = true;
                    return form.submit();
                  }
                }
              }
              // If anchor, navigate
              if (el.tagName.toLowerCase() === 'a') {
                const href = el.getAttribute('href');
                if (href && href !== '#') window.location.href = href;
                return;
              }
              // Otherwise call click programmatically as fallback
              try { el.click(); } catch (e) { /* ignore */ }
            });
          });
        });

        // Forms with .confirmable or data-confirm attribute on form
        document.querySelectorAll('form.confirmable, form[data-confirm]').forEach(function (form) {
          if (form.__confirmSubmitAttached) return; form.__confirmSubmitAttached = true;
          form.addEventListener('submit', function (evt) {
            // only intercept when not already confirmed
            if (form.__confirmed) return; evt.preventDefault();
            const message = form.getAttribute('data-confirm') || form.querySelector('[type=submit]')?.getAttribute('data-confirm') || 'Are you sure you want to submit?';
            showConfirm(message).then(function (ok) {
              if (!ok) return; form.__confirmed = true; form.submit();
            });
          });
        });
      }

      // Attach on DOM ready and after AJAX navigation if needed
      if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', attachConfirmHandlers); else attachConfirmHandlers();
      // Expose helpers for dynamically rendered buttons.
      window.attachConfirmHandlers = attachConfirmHandlers;
      window.showConfirm = showConfirm;
    })();
  </script>
  @stack('scripts')
</body>
</html>
