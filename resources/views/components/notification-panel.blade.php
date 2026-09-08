@php
  $notifications = $notifications ?? collect();
  $unreadNotificationCount = $unreadNotificationCount ?? 0;
@endphp

<section id="panel-notifications" class="panel">
  <div class="notification-panel-card">
    <div class="notification-panel-header">
      <div>
        <h3>Notifications</h3>
        <p>Stay updated with your latest notifications.</p>
      </div>
      <div class="notification-panel-actions">
        <div class="notification-tabs" role="tablist">
          <button type="button" class="notification-tab active" data-filter="all">All</button>
          <button type="button" class="notification-tab" data-filter="unread">Unread ({{ $unreadNotificationCount }})</button>
        </div>
        <form method="POST" action="{{ route('notifications.read-all') }}">
          @csrf
          <button class="btn-sm success" type="submit">Mark all read</button>
        </form>
      </div>
    </div>

    <div class="notification-list">
      @if($notifications->count())
        @foreach($notifications as $notification)
          <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}" data-status="{{ $notification->read_at ? 'read' : 'unread' }}">
            <div class="notification-badge">!</div>
            <div class="notification-body">
              <div class="notification-title">{{ $notification->data['title'] ?? $notification->data['message'] ?? 'Notification' }}</div>
              <div class="notification-text">{{ $notification->data['body'] ?? $notification->data['message'] ?? '' }}</div>
              <div class="notification-meta">{{ \Carbon\Carbon::parse($notification->created_at)->format('M d, Y | h:i A') }}</div>
            </div>
            @if(! $notification->read_at)
              <div class="notification-unread-dot"></div>
            @endif
          </div>
        @endforeach
      @else
        <div class="notification-empty">No notifications yet.</div>
      @endif
    </div>
  </div>
</section>

@once
  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const notificationTabs = document.querySelectorAll('.notification-tab');
        notificationTabs.forEach((tab) => {
          tab.addEventListener('click', function () {
            const filter = this.dataset.filter;
            notificationTabs.forEach((t) => t.classList.remove('active'));
            this.classList.add('active');

            document.querySelectorAll('.notification-item').forEach((item) => {
              item.style.display = filter === 'all' || item.dataset.status === filter ? 'grid' : 'none';
            });
          });
        });
      });
    </script>
  @endpush
@endonce
