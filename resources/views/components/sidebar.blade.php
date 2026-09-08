@props([
    'brand' => 'Panabo City Library',
    'subtitle' => 'Library Portal',
    'avatar' => 'P',
    'userName' => 'User',
    'userRole' => 'Role',
    'activePanel' => 'dashboard',
])
<aside class="app-sidebar">
  <div class="sidebar-brand">
    <img src="{{ asset('images/library logos.jpg') }}" class="sidebar-brand-logo" alt="Library Logo">
    <div>
      <div class="brand-name">{{ $brand }}</div>
      <div class="brand-sub">{{ $subtitle }}</div>
    </div>
  </div>

  {{-- ========== ADMIN ========== --}}
  @if($userRole === 'Admin')
    <div class="nav-section">
      <div class="nav-label">Overview</div>
      <button class="nav-item {{ $activePanel === 'dashboard' ? 'active' : '' }}" type="button" data-panel-button="dashboard" data-title="Admin Dashboard" onclick="showPanel('dashboard')">
        <span class="icon"><x-icons.dashboard /></span> Dashboard
      </button>
      <button class="nav-item {{ $activePanel === 'announcements' ? 'active' : '' }}" type="button" data-panel-button="announcements" data-title="Announcements" onclick="showPanel('announcements')">
        <span class="icon"><x-icons.announcement /></span> Announcements
      </button>
    </div>

    <div class="nav-section">
      <div class="nav-label">User Management</div>
      <button class="nav-item {{ $activePanel === 'verified' ? 'active' : '' }}" type="button" data-panel-button="verified" data-title="Verified Accounts" onclick="showPanel('verified')">
        <span class="icon"><x-icons.verify /></span> Verified Accounts
      </button>
      <button class="nav-item {{ $activePanel === 'barcode-cards' ? 'active' : '' }}" type="button" data-panel-button="barcode-cards" data-title="Barcode Cards" onclick="showPanel('barcode-cards')">
        <span class="icon"><x-icons.barcode /></span> Barcode Cards
      </button>
      <button class="nav-item {{ $activePanel === 'barcode-requests' ? 'active' : '' }}" type="button" data-panel-button="barcode-requests" data-title="Barcode Requests" onclick="showPanel('barcode-requests')">
        <span class="icon"><x-icons.barcode /></span> Barcode Requests
      </button>
      <button class="nav-item {{ $activePanel === 'users' ? 'active' : '' }}" type="button" data-panel-button="users" data-title="User Records" onclick="showPanel('users')">
        <span class="icon"><x-icons.users /></span> User Records
      </button>
      <button class="nav-item {{ $activePanel === 'system' ? 'active' : '' }}" type="button" data-panel-button="system" data-title="System Utilities" onclick="showPanel('system')">
        <span class="icon"><x-icons.settings /></span> Utilities
      </button>
    </div>

    <div class="nav-section">
      <div class="nav-label">Library Management</div>
      <button class="nav-item {{ $activePanel === 'books' ? 'active' : '' }}" type="button" data-panel-button="books" data-title="Book Collection" onclick="showPanel('books')">
        <span class="icon"><x-icons.books /></span> Book Collection
      </button>
      <button class="nav-item {{ $activePanel === 'fines' ? 'active' : '' }}" type="button" data-panel-button="fines" data-title="Fines" onclick="showPanel('fines')">
        <span class="icon"><x-icons.fine /></span> Fines
      </button>
      <button class="nav-item {{ $activePanel === 'funds' ? 'active' : '' }}" type="button" data-panel-button="funds" data-title="Funds" onclick="showPanel('funds')">
        <span class="icon"><x-icons.fine /></span> Funds
      </button>
    </div>

    <div class="nav-section">
      <div class="nav-label">Research</div>
      <button class="nav-item {{ $activePanel === 'proposals' ? 'active' : '' }}" type="button" data-panel-button="proposals" data-title="Proposals" onclick="showPanel('proposals')">
        <span class="icon"><x-icons.proposal /></span> Proposals
      </button>
    </div>

    <div class="nav-section">
      <div class="nav-label">Reports</div>
      <button class="nav-item {{ $activePanel === 'reports' ? 'active' : '' }}" type="button" data-panel-button="reports" data-title="Reports" onclick="showPanel('reports')">
        <span class="icon"><x-icons.report /></span> Reports
      </button>
    </div>

  {{-- ========== STAFF ========== --}}
  @elseif($userRole === 'Staff')
    <div class="nav-section">
      <div class="nav-label">Overview</div>
      <button class="nav-item {{ $activePanel === 'dashboard' ? 'active' : '' }}" type="button" data-panel-button="dashboard" data-title="Staff Dashboard" onclick="showPanel('dashboard')">
        <span class="icon"><x-icons.dashboard /></span> Dashboard
      </button>
    </div>

    <div class="nav-section">
      <div class="nav-label">Library Operations</div>
     <!-- <button class="nav-item {{ $activePanel === 'verify' ? 'active' : '' }}" type="button" data-panel-button="verify" data-title="Verify Accounts" onclick="showPanel('verify')">
        <span class="icon"><x-icons.verify /></span> Verify Accounts
      </button> -->
      <button class="nav-item {{ $activePanel === 'verified' ? 'active' : '' }}" type="button" data-panel-button="verified" data-title="Verified Accounts" onclick="showPanel('verified')">
        <span class="icon"><x-icons.verify /></span> Verified Accounts
      </button>
      <button class="nav-item {{ $activePanel === 'barcode-cards' ? 'active' : '' }}" type="button" data-panel-button="barcode-cards" data-title="Barcode Cards" onclick="showPanel('barcode-cards')">
        <span class="icon"><x-icons.barcode /></span> Barcode Cards
      </button>
      <button class="nav-item {{ $activePanel === 'barcode-requests' ? 'active' : '' }}" type="button" data-panel-button="barcode-requests" data-title="Barcode Requests" onclick="showPanel('barcode-requests')">
        <span class="icon"><x-icons.verify /></span> Barcode Requests
      </button>

      <button class="nav-item {{ $activePanel === 'books' ? 'active' : '' }}" type="button" data-panel-button="books" data-title="Book Collection" onclick="showPanel('books')">
        <span class="icon"><x-icons.books /></span> Book Collection
      </button>
      <button class="nav-item {{ $activePanel === 'book-management' ? 'active' : '' }}" type="button" data-panel-button="book-management" data-title="Book Management" onclick="showPanel('book-management')">
        <span class="icon"><x-icons.books /></span> Book Management
      </button>
      <button class="nav-item {{ $activePanel === 'borrow-requests' ? 'active' : '' }}" type="button" data-panel-button="borrow-requests" data-title="Pending Book Requests" onclick="showPanel('borrow-requests')">
        <span class="icon"><x-icons.borrow /></span> Pending Requests
      </button>
      <button class="nav-item {{ $activePanel === 'borrowing' ? 'active' : '' }}" type="button" data-panel-button="borrowing" data-title="Borrowing / Returns" onclick="showPanel('borrowing')">
        <span class="icon"><x-icons.borrow /></span> Borrowing / Returns
      </button>
      <button class="nav-item {{ $activePanel === 'fines' ? 'active' : '' }}" type="button" data-panel-button="fines" data-title="Fines" onclick="showPanel('fines')">
        <span class="icon"><x-icons.fine /></span> Fines
      </button>
    </div>
<!--
    <div class="nav-section">
      <div class="nav-label">Attendance & Access</div>
      <button class="nav-item {{ $activePanel === 'attendance' ? 'active' : '' }}" type="button" data-panel-button="attendance" data-title="Attendance" onclick="showPanel('attendance')">
       <!-- <span class="icon"><x-icons.attendance /></span> Attendance
      </button>
      <button class="nav-item {{ $activePanel === 'wifi' ? 'active' : '' }}" type="button" data-panel-button="wifi" data-title="Wi-Fi Vouchers" onclick="showPanel('wifi')">
      </button>
      <button class="nav-item {{ $activePanel === 'visitors' ? 'active' : '' }}" type="button" data-panel-button="visitors" data-title="Visitor Barcodes" onclick="showPanel('visitors')">
      </button>
    </div>
-->

  {{-- ========== STUDENT / VISITOR ========== --}}
  @elseif($userRole === 'Student' || $userRole === 'Visitor')
    <div class="nav-section">
      <div class="nav-label">Overview</div>
      <button class="nav-item {{ $activePanel === 'dashboard' ? 'active' : '' }}" type="button" data-panel-button="dashboard" data-title="Student Dashboard" onclick="showPanel('dashboard')">
        <span class="icon"><x-icons.dashboard /></span> Dashboard
      </button>
    </div>

    <div class="nav-section">
      <div class="nav-label">Account</div>
      <a class="nav-item" href="{{ $userRole === 'Visitor' ? (Route::has('visitor.profile') ? route('visitor.profile') : '#') : (Route::has('student.profile') ? route('student.profile') : '#') }}"><span class="icon"><x-icons.profile /></span> My Profile</a>
    </div>

    <div class="nav-section">
      <div class="nav-label">Borrowing</div>
      <button class="nav-item {{ $activePanel === 'borrowings' ? 'active' : '' }}" type="button" data-panel-button="borrowings" data-title="My Borrowings" onclick="showPanel('borrowings')">
        <span class="icon"><x-icons.borrow /></span> My Borrowings
      </button>
      <button class="nav-item {{ $activePanel === 'fines' ? 'active' : '' }}" type="button" data-panel-button="fines" data-title="My Fines" onclick="showPanel('fines')">
        <span class="icon"><x-icons.fine /></span> My Fines
      </button>
      <button class="nav-item {{ $activePanel === 'barcode-request' ? 'active' : '' }}" type="button" data-panel-button="barcode-request" data-title="Request Barcode" onclick="showPanel('barcode-request')">
        <span class="icon"><x-icons.barcode /></span> Request Barcode
      </button>

    </div>

    <div class="nav-section">
      <div class="nav-label">Tools</div>
      <a class="nav-item" href="{{ Route::has('opac.index') ? route('opac.index') : '#' }}" style="color:#2F9E8F;font-weight:600;"><span class="icon"><x-icons.search /></span> Search Catalog</a>
    </div>

  {{-- ========== RESEARCHER ========== --}}
  @elseif($userRole === 'Researcher')
    <div class="nav-section">
      <div class="nav-label">Overview</div>
      <button class="nav-item {{ $activePanel === 'dashboard' ? 'active' : '' }}" type="button" data-panel-button="dashboard" data-title="Researcher Dashboard" onclick="showPanel('dashboard')">
        <span class="icon"><x-icons.dashboard /></span> Dashboard
      </button>
    </div>

    <div class="nav-section">
      <div class="nav-label">Account</div>
      <a class="nav-item" href="{{ Route::has('researcher.profile') ? route('researcher.profile') : '#' }}"><span class="icon"><x-icons.profile /></span> My Profile</a>
    </div>

    <div class="nav-section">
      <div class="nav-label">Research</div>
      <button class="nav-item {{ $activePanel === 'proposals' ? 'active' : '' }}" type="button" data-panel-button="proposals" data-title="My Proposals" onclick="showPanel('proposals')">
        <span class="icon"><x-icons.proposal /></span> My Proposals
      </button>
      <button class="nav-item {{ $activePanel === 'submit' ? 'active' : '' }}" type="button" data-panel-button="submit" data-title="Submit Proposal" onclick="showPanel('submit')">
        <span class="icon"><x-icons.plus /></span> Submit Proposal
      </button>
    </div>

  {{-- ========== DEFAULT / FALLBACK ========== --}}
  @else
    <div class="nav-section">
      <button class="nav-item active" type="button"><span class="icon"><x-icons.dashboard /></span> Dashboard</button>
    </div>
  @endif

  {{-- Bottom: User chip + Logout --}}
  <div class="sidebar-bottom">
    <div class="user-chip">
      <div class="avatar">{{ $avatar }}</div>
      <div>
        <div class="user-name">{{ $userName }}</div>
        <div class="user-role">{{ $userRole }}</div>
      </div>
    </div>
    <form id="logout-form" action="{{ Route::has('logout') ? route('logout') : url('/login') }}" method="POST" style="display:none;">
      @csrf
    </form>
    <button class="nav-item logout-item" type="button" onclick="if(window.showConfirm){window.showConfirm('Are you sure you want to log out?').then(function(ok){if(ok)document.getElementById('logout-form').submit()})}else{if(confirm('Are you sure you want to log out?'))document.getElementById('logout-form').submit()}">
      <span class="icon"><x-icons.logout /></span> Logout
    </button>
  </div>
</aside>
