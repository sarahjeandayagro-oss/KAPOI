<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'subtitle' => '', 'actions' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['title', 'subtitle' => '', 'actions' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
  if (! isset($notifications)) {
    $notifications = auth()->check() ? auth()->user()->notifications()->latest()->limit(12)->get() : collect();
  }
  if (! isset($unreadNotificationCount)) {
    $unreadNotificationCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
  }
?>
<header class="app-topbar">
  <div>
    <div class="topbar-title" id="topbarTitle"><?php echo e($title); ?></div>
    <?php if($subtitle): ?>
      <div class="topbar-sub"><?php echo e($subtitle); ?></div>
    <?php endif; ?>
  </div>
  <?php if($actions || isset($slot)): ?>
    <div class="topbar-right">
      <?php echo e($actions ?? $slot); ?>

    </div>
  <?php else: ?>
    <div class="topbar-right">
      <button id="notifBtn" class="btn-icon notif-toggle" type="button" aria-label="Notifications" title="Notifications" onclick="toggleNotifDropdown(event)">
        <span class="notif-icon" aria-hidden="true" style="display:inline-flex;align-items:center;justify-content:center;width:auto;height:auto;font-size:18px;line-height:1;font-family:'Segoe UI Emoji','Apple Color Emoji','Segoe UI Symbol',sans-serif;">🔔</span>
        <?php if($unreadNotificationCount > 0): ?>
          <span class="notif-badge"><?php echo e($unreadNotificationCount); ?></span>
        <?php endif; ?>
      </button>

      <div id="notificationDropdown" class="notif-dropdown" aria-hidden="true">
        <div class="notif-dropdown-card" role="dialog" aria-modal="false">
          <div class="notif-dropdown-head">
            <div class="notif-head-left">
              <div class="notif-head-title">Notifications</div>
              <div class="notif-head-sub">Stay updated with your latest notifications</div>
            </div>
            <div class="notif-head-right">
              <div class="notif-tabs" role="tablist">
                <button type="button" class="notif-tab active" data-filter="all">All</button>
                <button type="button" class="notif-tab" data-filter="unread">Unread (<?php echo e($unreadNotificationCount); ?>)</button>
              </div>
              <button id="markAllReadBtn" class="btn-sm notif-action">Mark all read</button>
              <button id="notifClose" class="btn-icon notif-close" aria-label="Close">✕</button>
            </div>
          </div>
          <div class="notif-dropdown-body">
            <div class="notif-list">
              <?php if($notifications->count()): ?>
                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php
                    $data = is_array($note->data) ? (object) $note->data : (object) ($note->data ?? []);
                    $titleText = $data->title ?? ($data->message ?? 'Notification');
                    $bodyText = $data->body ?? $data->message ?? ($data->description ?? '');
                    $timeText = \Carbon\Carbon::parse($note->created_at)->diffForHumans();
                    $fullTime = \Carbon\Carbon::parse($note->created_at)->format('M d, Y h:i A');
                  ?>
                  <div class="notif-item <?php echo e($note->read_at ? 'notif-read' : 'notif-unread'); ?>" data-id="<?php echo e($note->id); ?>" data-status="<?php echo e($note->read_at ? 'read' : 'unread'); ?>" data-title="<?php echo e(e($titleText)); ?>" data-body="<?php echo e(e($bodyText)); ?>" data-time="<?php echo e(e($timeText)); ?>" data-full-time="<?php echo e(e($fullTime)); ?>">
                    <div class="notif-item-left"><div class="notif-dot">🔔</div></div>
                    <div class="notif-item-body">
                      <div class="notif-item-title"><?php echo e($titleText); ?></div>
                      <div class="notif-item-time"><?php echo e($timeText); ?></div>
                    </div>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php else: ?>
                <div class="notif-empty">No notifications.</div>
              <?php endif; ?>
            </div>
            <div class="notif-detail" id="notifDetail">
              <div class="notif-detail-title">Select a notification to read the full message</div>
              <div class="notif-detail-meta"></div>
              <div class="notif-detail-body"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</header>

<?php $__env->startPush('head'); ?>
  <style>
    .btn-icon { background:transparent; border:0; cursor:pointer; position:relative; padding:8px 10px; font-size:18px; color:inherit; min-width:40px; min-height:40px; }
    .btn-icon:not(.notif-close):hover { background:rgba(15,23,42,.03); border-radius:12px; }
    .btn-icon .icon, .app-topbar .icon, .btn-icon .notif-icon { display:inline-flex; align-items:center; justify-content:center; width:auto; height:auto; font-size:18px; line-height:1; }
    .notif-badge { position:absolute; top:4px; right:4px; background:#ef4444; color:#fff; font-size:11px; padding:2px 6px; border-radius:999px; min-width:18px; text-align:center; }
    .notif-dropdown { position:relative; }
    .notif-dropdown-card { position:absolute; right:16px; top:calc(100% + 8px); width:min(920px,calc(100vw - 32px)); max-height:88vh; background:#ffffff; border-radius:24px; overflow:hidden; display:none; box-shadow:0 28px 70px rgba(15,23,42,.18); z-index:9999; }
    .notif-dropdown.open .notif-dropdown-card { display:block; }
    .notif-dropdown-head { padding:22px 24px 20px; display:flex; justify-content:space-between; align-items:flex-start; gap:16px; border-bottom:1px solid #eff2f7; }
    .notif-head-left { min-width:240px; }
    .notif-head-title { font-size:18px; font-weight:800; color:#0f172a; margin-bottom:6px; }
    .notif-head-sub { font-size:13px; color:#64748b; line-height:1.5; }
    .notif-head-right { display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
    .notif-tabs { display:flex; gap:10px; border:1px solid #E5E7EB; background:#ffffff; border-radius:999px; padding:6px; }
    .notif-tab { background:transparent; border:none; padding:10px 14px; border-radius:999px; font-weight:700; color:#475569; cursor:pointer; transition:background .18s ease,color .18s ease; }
    .notif-tab.active { color:#0f172a; background:#f8fafc; }
    .notif-action { border-radius:999px; padding:10px 16px; background:#eef2ff; color:#0f172a; border:1px solid transparent; }
    .notif-action:hover { background:#e0f2fe; }
    .notif-close { font-size:20px; width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center; border-radius:12px; }
    .notif-dropdown-body { display:flex; gap:16px; padding:18px; max-height:84vh; overflow:hidden; }
    .notif-list { width:360px; max-height:82vh; overflow:auto; border-right:1px solid #f1f5f9; padding-right:10px; }
    .notif-item { display:flex; gap:16px; align-items:flex-start; padding:16px 14px; border-radius:18px; cursor:pointer; transition:background .2s ease, transform .2s ease; }
    .notif-item + .notif-item { margin-top:8px; }
    .notif-item:hover { background:#f8fbff; transform:translateX(1px); }
    .notif-item.selected { background:#e8f3ff; }
    .notif-item.notif-read { opacity:0.75; }
    .notif-item-left { flex-shrink:0; }
    .notif-dot { width:44px; height:44px; border-radius:14px; display:flex; align-items:center; justify-content:center; background:#eff6ff; font-size:18px; }
    .notif-item-body { min-width:0; }
    .notif-item-title { font-weight:700; color:#0f172a; font-size:14px; line-height:1.3; }
    .notif-item-time { font-size:12px; color:#64748b; margin-top:6px; }
    .notif-empty { padding:20px; color:#64748b; font-size:14px; }
    .notif-detail { flex:1; padding:24px; border-radius:18px; background:#f8fafc; min-height:1000px; overflow:auto; }
    .notif-detail-title { font-weight:800; font-size:18px; color:#0f172a; margin-bottom:10px; }
    .notif-detail-meta { color:#64748b; font-size:13px; margin-bottom:18px; }
    .notif-detail-body { font-size:14px; color:#334155; line-height:1.75; white-space:pre-line; }
    @media (max-width:900px) { .notif-dropdown-card { width:96vw; right:2vw; } .notif-dropdown-body { flex-direction:column; } .notif-list { width:100%; border-right:0; border-bottom:1px solid #f1f5f9; padding-right:0; } .notif-detail { min-height:180px; width:100%; } .notif-dropdown-head { flex-direction:column; align-items:flex-start; } .notif-head-right { width:100%; justify-content:space-between; } }
  </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
  <script>
    (function(){
      const btn = document.getElementById('notifBtn');
      const dropdownWrap = document.getElementById('notificationDropdown');
      const dropdownCard = dropdownWrap ? dropdownWrap.querySelector('.notif-dropdown-card') : null;
      const close = document.getElementById('notifClose');

      function openDropdown(){ if(dropdownWrap) dropdownWrap.classList.add('open'); }
      function closeDropdown(){ if(dropdownWrap) dropdownWrap.classList.remove('open'); }

      if(btn) btn.addEventListener('click', function(e){ e.stopPropagation(); dropdownWrap.classList.toggle('open'); if(dropdownWrap.classList.contains('open')) activateTab('all'); });
      if(close) close.addEventListener('click', function(e){ e.stopPropagation(); closeDropdown(); });
      window.toggleNotifDropdown = function(e) { e.stopPropagation(); if(dropdownWrap) { dropdownWrap.classList.toggle('open'); if(dropdownWrap.classList.contains('open')) activateTab('all'); } };

      // close when clicking outside
      document.addEventListener('click', function(e){ if(!e.target.closest('.notif-dropdown') && !e.target.closest('#notifBtn')) closeDropdown(); });

      const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : null;
      const markReadUrl = '/notifications';
      const markAllReadUrl = '<?php echo e(route('notifications.read-all')); ?>';
      const detailPanel = document.getElementById('notifDetail');
      const tabs = Array.from(document.querySelectorAll('.notif-tab'));
      const items = Array.from(document.querySelectorAll('.notif-item'));

      function activateTab(filter){
        tabs.forEach((tab) => tab.classList.toggle('active', tab.dataset.filter === filter));
        items.forEach((item) => {
          item.style.display = filter === 'all' || item.dataset.status === filter ? 'flex' : 'none';
        });
      }

      tabs.forEach((tab) => {
        tab.addEventListener('click', function(e){
          e.stopPropagation();
          activateTab(this.dataset.filter);
        });
      });

      function updateBadge(count){
        const badge = document.querySelector('.notif-badge');
        if(!badge) return;
        if(count > 0) badge.textContent = count;
        else badge.remove();
      }

      function markAsRead(id, element){
        if(!id || !csrfToken) return;
        fetch(markReadUrl + '/' + encodeURIComponent(id) + '/read', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          credentials: 'same-origin',
          body: JSON.stringify({})
        }).then(resp => {
          if(resp.ok || resp.status === 302 || resp.status === 204){
            element.classList.add('notif-read');
            element.classList.add('selected');
            const badge = document.querySelector('.notif-badge');
            if(badge){
              let n = parseInt(badge.textContent || '0', 10) - 1;
              updateBadge(n);
            }
          }
        }).catch(() => {});
      }

      function renderNotificationDetail(element){
        if(!detailPanel || !element) return;
        const title = element.dataset.title || 'Notification';
        const body = element.dataset.body || 'No additional details.';
        const fullTime = element.dataset.fullTime || '';
        const meta = element.dataset.time ? 'Received ' + element.dataset.time : '';

        detailPanel.querySelector('.notif-detail-title').textContent = title;
        detailPanel.querySelector('.notif-detail-meta').textContent = fullTime ? `${meta} · ${fullTime}` : meta;
        detailPanel.querySelector('.notif-detail-body').textContent = body;
      }

      function markAllRead(){
        if(!csrfToken) return;
        fetch(markAllReadUrl, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          credentials: 'same-origin',
          body: JSON.stringify({})
        }).then(resp => {
          if(resp.ok || resp.status === 302 || resp.status === 204){
            document.querySelectorAll('.notif-item').forEach(item => item.classList.add('notif-read'));
            updateBadge(0);
          }
        }).catch(() => {});
      }

      document.addEventListener('click', function(e){
        const v = e.target.closest('.notif-item');
        if(!v) return;
        document.querySelectorAll('.notif-item').forEach(item => item.classList.remove('selected'));
        v.classList.add('selected');
        renderNotificationDetail(v);
        const id = v.dataset.id || null;
        if(id){ markAsRead(id, v); }
      });

      const markAllBtn = document.getElementById('markAllReadBtn');
      if(markAllBtn){
        markAllBtn.addEventListener('click', function(e){
          e.stopPropagation();
          markAllRead();
        });
      }

      const openFull = document.getElementById('openFullList');
      if(openFull){
        openFull.addEventListener('click', function(e){ e.stopPropagation(); if(typeof showPanel === 'function') showPanel('notifications'); closeDropdown(); });
      }
    })();
  </script>
<?php $__env->stopPush(); ?>

<?php /**PATH C:\laragon\www\LIBRARY-MANAGEMENT-SYSTEM\resources\views/components/topbar.blade.php ENDPATH**/ ?>