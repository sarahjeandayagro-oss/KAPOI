<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'brand' => 'Panabo City Library',
    'subtitle' => 'Library Portal',
    'avatar' => 'P',
    'userName' => 'User',
    'userRole' => 'Role',
    'activePanel' => 'dashboard',
]));

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

foreach (array_filter(([
    'brand' => 'Panabo City Library',
    'subtitle' => 'Library Portal',
    'avatar' => 'P',
    'userName' => 'User',
    'userRole' => 'Role',
    'activePanel' => 'dashboard',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<aside class="app-sidebar">
  <div class="sidebar-brand">
    <img src="<?php echo e(asset('images/library logos.jpg')); ?>" class="sidebar-brand-logo" alt="Library Logo">
    <div>
      <div class="brand-name"><?php echo e($brand); ?></div>
      <div class="brand-sub"><?php echo e($subtitle); ?></div>
    </div>
  </div>

  
  <?php if($userRole === 'Admin'): ?>
    <div class="nav-section">
      <div class="nav-label">Overview</div>
      <button class="nav-item <?php echo e($activePanel === 'dashboard' ? 'active' : ''); ?>" type="button" data-panel-button="dashboard" data-title="Admin Dashboard" onclick="showPanel('dashboard')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.dashboard','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.dashboard'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf)): ?>
<?php $attributes = $__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf; ?>
<?php unset($__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf)): ?>
<?php $component = $__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf; ?>
<?php unset($__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf); ?>
<?php endif; ?></span> Dashboard
      </button>
      <button class="nav-item <?php echo e($activePanel === 'announcements' ? 'active' : ''); ?>" type="button" data-panel-button="announcements" data-title="Announcements" onclick="showPanel('announcements')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginal436f68001dda59ed2b89e86e554ada1a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal436f68001dda59ed2b89e86e554ada1a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.announcement','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.announcement'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal436f68001dda59ed2b89e86e554ada1a)): ?>
<?php $attributes = $__attributesOriginal436f68001dda59ed2b89e86e554ada1a; ?>
<?php unset($__attributesOriginal436f68001dda59ed2b89e86e554ada1a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal436f68001dda59ed2b89e86e554ada1a)): ?>
<?php $component = $__componentOriginal436f68001dda59ed2b89e86e554ada1a; ?>
<?php unset($__componentOriginal436f68001dda59ed2b89e86e554ada1a); ?>
<?php endif; ?></span> Announcements
      </button>
    </div>

    <div class="nav-section">
      <div class="nav-label">User Management</div>
      <button class="nav-item <?php echo e($activePanel === 'verified' ? 'active' : ''); ?>" type="button" data-panel-button="verified" data-title="Verified Accounts" onclick="showPanel('verified')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginala5c49b669d49607064644fe7784c40e5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala5c49b669d49607064644fe7784c40e5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.verify','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.verify'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala5c49b669d49607064644fe7784c40e5)): ?>
<?php $attributes = $__attributesOriginala5c49b669d49607064644fe7784c40e5; ?>
<?php unset($__attributesOriginala5c49b669d49607064644fe7784c40e5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala5c49b669d49607064644fe7784c40e5)): ?>
<?php $component = $__componentOriginala5c49b669d49607064644fe7784c40e5; ?>
<?php unset($__componentOriginala5c49b669d49607064644fe7784c40e5); ?>
<?php endif; ?></span> Verified Accounts
      </button>
      <button class="nav-item <?php echo e($activePanel === 'barcode-cards' ? 'active' : ''); ?>" type="button" data-panel-button="barcode-cards" data-title="Barcode Cards" onclick="showPanel('barcode-cards')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginalbabcb5a601c22b877bd5d302b6d05191 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbabcb5a601c22b877bd5d302b6d05191 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.barcode','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.barcode'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbabcb5a601c22b877bd5d302b6d05191)): ?>
<?php $attributes = $__attributesOriginalbabcb5a601c22b877bd5d302b6d05191; ?>
<?php unset($__attributesOriginalbabcb5a601c22b877bd5d302b6d05191); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbabcb5a601c22b877bd5d302b6d05191)): ?>
<?php $component = $__componentOriginalbabcb5a601c22b877bd5d302b6d05191; ?>
<?php unset($__componentOriginalbabcb5a601c22b877bd5d302b6d05191); ?>
<?php endif; ?></span> Barcode Cards
      </button>
      <button class="nav-item <?php echo e($activePanel === 'barcode-requests' ? 'active' : ''); ?>" type="button" data-panel-button="barcode-requests" data-title="Barcode Requests" onclick="showPanel('barcode-requests')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginalbabcb5a601c22b877bd5d302b6d05191 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbabcb5a601c22b877bd5d302b6d05191 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.barcode','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.barcode'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbabcb5a601c22b877bd5d302b6d05191)): ?>
<?php $attributes = $__attributesOriginalbabcb5a601c22b877bd5d302b6d05191; ?>
<?php unset($__attributesOriginalbabcb5a601c22b877bd5d302b6d05191); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbabcb5a601c22b877bd5d302b6d05191)): ?>
<?php $component = $__componentOriginalbabcb5a601c22b877bd5d302b6d05191; ?>
<?php unset($__componentOriginalbabcb5a601c22b877bd5d302b6d05191); ?>
<?php endif; ?></span> Barcode Requests
      </button>
      <button class="nav-item <?php echo e($activePanel === 'users' ? 'active' : ''); ?>" type="button" data-panel-button="users" data-title="User Records" onclick="showPanel('users')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginal46848001facf1cdb1a84c118cea2e25d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal46848001facf1cdb1a84c118cea2e25d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.users','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.users'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal46848001facf1cdb1a84c118cea2e25d)): ?>
<?php $attributes = $__attributesOriginal46848001facf1cdb1a84c118cea2e25d; ?>
<?php unset($__attributesOriginal46848001facf1cdb1a84c118cea2e25d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal46848001facf1cdb1a84c118cea2e25d)): ?>
<?php $component = $__componentOriginal46848001facf1cdb1a84c118cea2e25d; ?>
<?php unset($__componentOriginal46848001facf1cdb1a84c118cea2e25d); ?>
<?php endif; ?></span> User Records
      </button>
      <button class="nav-item <?php echo e($activePanel === 'system' ? 'active' : ''); ?>" type="button" data-panel-button="system" data-title="System Utilities" onclick="showPanel('system')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginal675d5ec13ccf645c64542fb04e9f331e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal675d5ec13ccf645c64542fb04e9f331e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.settings','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.settings'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal675d5ec13ccf645c64542fb04e9f331e)): ?>
<?php $attributes = $__attributesOriginal675d5ec13ccf645c64542fb04e9f331e; ?>
<?php unset($__attributesOriginal675d5ec13ccf645c64542fb04e9f331e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal675d5ec13ccf645c64542fb04e9f331e)): ?>
<?php $component = $__componentOriginal675d5ec13ccf645c64542fb04e9f331e; ?>
<?php unset($__componentOriginal675d5ec13ccf645c64542fb04e9f331e); ?>
<?php endif; ?></span> Utilities
      </button>
    </div>

    <div class="nav-section">
      <div class="nav-label">Library Management</div>
      <button class="nav-item <?php echo e($activePanel === 'books' ? 'active' : ''); ?>" type="button" data-panel-button="books" data-title="Book Collection" onclick="showPanel('books')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginal151195c365bba4c5d09f3f795d6ad750 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal151195c365bba4c5d09f3f795d6ad750 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.books','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.books'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal151195c365bba4c5d09f3f795d6ad750)): ?>
<?php $attributes = $__attributesOriginal151195c365bba4c5d09f3f795d6ad750; ?>
<?php unset($__attributesOriginal151195c365bba4c5d09f3f795d6ad750); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal151195c365bba4c5d09f3f795d6ad750)): ?>
<?php $component = $__componentOriginal151195c365bba4c5d09f3f795d6ad750; ?>
<?php unset($__componentOriginal151195c365bba4c5d09f3f795d6ad750); ?>
<?php endif; ?></span> Book Collection
      </button>
      <button class="nav-item <?php echo e($activePanel === 'fines' ? 'active' : ''); ?>" type="button" data-panel-button="fines" data-title="Fines" onclick="showPanel('fines')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginal650dfa99c516960286d5fe50bcd1e7c5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.fine','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.fine'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5)): ?>
<?php $attributes = $__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5; ?>
<?php unset($__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal650dfa99c516960286d5fe50bcd1e7c5)): ?>
<?php $component = $__componentOriginal650dfa99c516960286d5fe50bcd1e7c5; ?>
<?php unset($__componentOriginal650dfa99c516960286d5fe50bcd1e7c5); ?>
<?php endif; ?></span> Fines
      </button>
      <button class="nav-item <?php echo e($activePanel === 'funds' ? 'active' : ''); ?>" type="button" data-panel-button="funds" data-title="Funds" onclick="showPanel('funds')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginal650dfa99c516960286d5fe50bcd1e7c5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.fine','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.fine'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5)): ?>
<?php $attributes = $__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5; ?>
<?php unset($__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal650dfa99c516960286d5fe50bcd1e7c5)): ?>
<?php $component = $__componentOriginal650dfa99c516960286d5fe50bcd1e7c5; ?>
<?php unset($__componentOriginal650dfa99c516960286d5fe50bcd1e7c5); ?>
<?php endif; ?></span> Funds
      </button>
    </div>

    <div class="nav-section">
      <div class="nav-label">Research</div>
      <button class="nav-item <?php echo e($activePanel === 'proposals' ? 'active' : ''); ?>" type="button" data-panel-button="proposals" data-title="Proposals" onclick="showPanel('proposals')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginalc193ef63664f4fbf943eccdba55870ec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc193ef63664f4fbf943eccdba55870ec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.proposal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.proposal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc193ef63664f4fbf943eccdba55870ec)): ?>
<?php $attributes = $__attributesOriginalc193ef63664f4fbf943eccdba55870ec; ?>
<?php unset($__attributesOriginalc193ef63664f4fbf943eccdba55870ec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc193ef63664f4fbf943eccdba55870ec)): ?>
<?php $component = $__componentOriginalc193ef63664f4fbf943eccdba55870ec; ?>
<?php unset($__componentOriginalc193ef63664f4fbf943eccdba55870ec); ?>
<?php endif; ?></span> Proposals
      </button>
    </div>

    <div class="nav-section">
      <div class="nav-label">Reports</div>
      <button class="nav-item <?php echo e($activePanel === 'reports' ? 'active' : ''); ?>" type="button" data-panel-button="reports" data-title="Reports" onclick="showPanel('reports')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginald16cd3052473197eb075cf69039f9224 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald16cd3052473197eb075cf69039f9224 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.report','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.report'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald16cd3052473197eb075cf69039f9224)): ?>
<?php $attributes = $__attributesOriginald16cd3052473197eb075cf69039f9224; ?>
<?php unset($__attributesOriginald16cd3052473197eb075cf69039f9224); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald16cd3052473197eb075cf69039f9224)): ?>
<?php $component = $__componentOriginald16cd3052473197eb075cf69039f9224; ?>
<?php unset($__componentOriginald16cd3052473197eb075cf69039f9224); ?>
<?php endif; ?></span> Reports
      </button>
    </div>

  
  <?php elseif($userRole === 'Staff'): ?>
    <div class="nav-section">
      <div class="nav-label">Overview</div>
      <button class="nav-item <?php echo e($activePanel === 'dashboard' ? 'active' : ''); ?>" type="button" data-panel-button="dashboard" data-title="Staff Dashboard" onclick="showPanel('dashboard')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.dashboard','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.dashboard'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf)): ?>
<?php $attributes = $__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf; ?>
<?php unset($__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf)): ?>
<?php $component = $__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf; ?>
<?php unset($__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf); ?>
<?php endif; ?></span> Dashboard
      </button>
    </div>

    <div class="nav-section">
      <div class="nav-label">Library Operations</div>
     <!-- <button class="nav-item <?php echo e($activePanel === 'verify' ? 'active' : ''); ?>" type="button" data-panel-button="verify" data-title="Verify Accounts" onclick="showPanel('verify')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginala5c49b669d49607064644fe7784c40e5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala5c49b669d49607064644fe7784c40e5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.verify','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.verify'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala5c49b669d49607064644fe7784c40e5)): ?>
<?php $attributes = $__attributesOriginala5c49b669d49607064644fe7784c40e5; ?>
<?php unset($__attributesOriginala5c49b669d49607064644fe7784c40e5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala5c49b669d49607064644fe7784c40e5)): ?>
<?php $component = $__componentOriginala5c49b669d49607064644fe7784c40e5; ?>
<?php unset($__componentOriginala5c49b669d49607064644fe7784c40e5); ?>
<?php endif; ?></span> Verify Accounts
      </button> -->
      <button class="nav-item <?php echo e($activePanel === 'verified' ? 'active' : ''); ?>" type="button" data-panel-button="verified" data-title="Verified Accounts" onclick="showPanel('verified')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginala5c49b669d49607064644fe7784c40e5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala5c49b669d49607064644fe7784c40e5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.verify','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.verify'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala5c49b669d49607064644fe7784c40e5)): ?>
<?php $attributes = $__attributesOriginala5c49b669d49607064644fe7784c40e5; ?>
<?php unset($__attributesOriginala5c49b669d49607064644fe7784c40e5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala5c49b669d49607064644fe7784c40e5)): ?>
<?php $component = $__componentOriginala5c49b669d49607064644fe7784c40e5; ?>
<?php unset($__componentOriginala5c49b669d49607064644fe7784c40e5); ?>
<?php endif; ?></span> Verified Accounts
      </button>
      <button class="nav-item <?php echo e($activePanel === 'barcode-cards' ? 'active' : ''); ?>" type="button" data-panel-button="barcode-cards" data-title="Barcode Cards" onclick="showPanel('barcode-cards')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginalbabcb5a601c22b877bd5d302b6d05191 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbabcb5a601c22b877bd5d302b6d05191 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.barcode','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.barcode'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbabcb5a601c22b877bd5d302b6d05191)): ?>
<?php $attributes = $__attributesOriginalbabcb5a601c22b877bd5d302b6d05191; ?>
<?php unset($__attributesOriginalbabcb5a601c22b877bd5d302b6d05191); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbabcb5a601c22b877bd5d302b6d05191)): ?>
<?php $component = $__componentOriginalbabcb5a601c22b877bd5d302b6d05191; ?>
<?php unset($__componentOriginalbabcb5a601c22b877bd5d302b6d05191); ?>
<?php endif; ?></span> Barcode Cards
      </button>
      <button class="nav-item <?php echo e($activePanel === 'barcode-requests' ? 'active' : ''); ?>" type="button" data-panel-button="barcode-requests" data-title="Barcode Requests" onclick="showPanel('barcode-requests')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginala5c49b669d49607064644fe7784c40e5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala5c49b669d49607064644fe7784c40e5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.verify','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.verify'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala5c49b669d49607064644fe7784c40e5)): ?>
<?php $attributes = $__attributesOriginala5c49b669d49607064644fe7784c40e5; ?>
<?php unset($__attributesOriginala5c49b669d49607064644fe7784c40e5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala5c49b669d49607064644fe7784c40e5)): ?>
<?php $component = $__componentOriginala5c49b669d49607064644fe7784c40e5; ?>
<?php unset($__componentOriginala5c49b669d49607064644fe7784c40e5); ?>
<?php endif; ?></span> Barcode Requests
      </button>

      <button class="nav-item <?php echo e($activePanel === 'books' ? 'active' : ''); ?>" type="button" data-panel-button="books" data-title="Book Collection" onclick="showPanel('books')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginal151195c365bba4c5d09f3f795d6ad750 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal151195c365bba4c5d09f3f795d6ad750 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.books','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.books'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal151195c365bba4c5d09f3f795d6ad750)): ?>
<?php $attributes = $__attributesOriginal151195c365bba4c5d09f3f795d6ad750; ?>
<?php unset($__attributesOriginal151195c365bba4c5d09f3f795d6ad750); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal151195c365bba4c5d09f3f795d6ad750)): ?>
<?php $component = $__componentOriginal151195c365bba4c5d09f3f795d6ad750; ?>
<?php unset($__componentOriginal151195c365bba4c5d09f3f795d6ad750); ?>
<?php endif; ?></span> Book Collection
      </button>
      <button class="nav-item <?php echo e($activePanel === 'book-management' ? 'active' : ''); ?>" type="button" data-panel-button="book-management" data-title="Book Management" onclick="showPanel('book-management')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginal151195c365bba4c5d09f3f795d6ad750 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal151195c365bba4c5d09f3f795d6ad750 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.books','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.books'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal151195c365bba4c5d09f3f795d6ad750)): ?>
<?php $attributes = $__attributesOriginal151195c365bba4c5d09f3f795d6ad750; ?>
<?php unset($__attributesOriginal151195c365bba4c5d09f3f795d6ad750); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal151195c365bba4c5d09f3f795d6ad750)): ?>
<?php $component = $__componentOriginal151195c365bba4c5d09f3f795d6ad750; ?>
<?php unset($__componentOriginal151195c365bba4c5d09f3f795d6ad750); ?>
<?php endif; ?></span> Book Management
      </button>
      <button class="nav-item <?php echo e($activePanel === 'borrow-requests' ? 'active' : ''); ?>" type="button" data-panel-button="borrow-requests" data-title="Pending Book Requests" onclick="showPanel('borrow-requests')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginal124339efe8fed8b121246464561d0eb5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal124339efe8fed8b121246464561d0eb5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.borrow','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.borrow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal124339efe8fed8b121246464561d0eb5)): ?>
<?php $attributes = $__attributesOriginal124339efe8fed8b121246464561d0eb5; ?>
<?php unset($__attributesOriginal124339efe8fed8b121246464561d0eb5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal124339efe8fed8b121246464561d0eb5)): ?>
<?php $component = $__componentOriginal124339efe8fed8b121246464561d0eb5; ?>
<?php unset($__componentOriginal124339efe8fed8b121246464561d0eb5); ?>
<?php endif; ?></span> Pending Requests
      </button>
      <button class="nav-item <?php echo e($activePanel === 'borrowing' ? 'active' : ''); ?>" type="button" data-panel-button="borrowing" data-title="Borrowing / Returns" onclick="showPanel('borrowing')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginal124339efe8fed8b121246464561d0eb5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal124339efe8fed8b121246464561d0eb5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.borrow','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.borrow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal124339efe8fed8b121246464561d0eb5)): ?>
<?php $attributes = $__attributesOriginal124339efe8fed8b121246464561d0eb5; ?>
<?php unset($__attributesOriginal124339efe8fed8b121246464561d0eb5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal124339efe8fed8b121246464561d0eb5)): ?>
<?php $component = $__componentOriginal124339efe8fed8b121246464561d0eb5; ?>
<?php unset($__componentOriginal124339efe8fed8b121246464561d0eb5); ?>
<?php endif; ?></span> Borrowing / Returns
      </button>
      <button class="nav-item <?php echo e($activePanel === 'fines' ? 'active' : ''); ?>" type="button" data-panel-button="fines" data-title="Fines" onclick="showPanel('fines')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginal650dfa99c516960286d5fe50bcd1e7c5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.fine','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.fine'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5)): ?>
<?php $attributes = $__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5; ?>
<?php unset($__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal650dfa99c516960286d5fe50bcd1e7c5)): ?>
<?php $component = $__componentOriginal650dfa99c516960286d5fe50bcd1e7c5; ?>
<?php unset($__componentOriginal650dfa99c516960286d5fe50bcd1e7c5); ?>
<?php endif; ?></span> Fines
      </button>
    </div>
<!--
    <div class="nav-section">
      <div class="nav-label">Attendance & Access</div>
      <button class="nav-item <?php echo e($activePanel === 'attendance' ? 'active' : ''); ?>" type="button" data-panel-button="attendance" data-title="Attendance" onclick="showPanel('attendance')">
       <!-- <span class="icon"><?php if (isset($component)) { $__componentOriginalc9feecf6a73dc441a006bfd283cd2a0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc9feecf6a73dc441a006bfd283cd2a0d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.attendance','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.attendance'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9feecf6a73dc441a006bfd283cd2a0d)): ?>
<?php $attributes = $__attributesOriginalc9feecf6a73dc441a006bfd283cd2a0d; ?>
<?php unset($__attributesOriginalc9feecf6a73dc441a006bfd283cd2a0d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9feecf6a73dc441a006bfd283cd2a0d)): ?>
<?php $component = $__componentOriginalc9feecf6a73dc441a006bfd283cd2a0d; ?>
<?php unset($__componentOriginalc9feecf6a73dc441a006bfd283cd2a0d); ?>
<?php endif; ?></span> Attendance
      </button>
      <button class="nav-item <?php echo e($activePanel === 'wifi' ? 'active' : ''); ?>" type="button" data-panel-button="wifi" data-title="Wi-Fi Vouchers" onclick="showPanel('wifi')">
      </button>
      <button class="nav-item <?php echo e($activePanel === 'visitors' ? 'active' : ''); ?>" type="button" data-panel-button="visitors" data-title="Visitor Barcodes" onclick="showPanel('visitors')">
      </button>
    </div>
-->

  
  <?php elseif($userRole === 'Student' || $userRole === 'Visitor'): ?>
    <div class="nav-section">
      <div class="nav-label">Overview</div>
      <button class="nav-item <?php echo e($activePanel === 'dashboard' ? 'active' : ''); ?>" type="button" data-panel-button="dashboard" data-title="Student Dashboard" onclick="showPanel('dashboard')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.dashboard','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.dashboard'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf)): ?>
<?php $attributes = $__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf; ?>
<?php unset($__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf)): ?>
<?php $component = $__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf; ?>
<?php unset($__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf); ?>
<?php endif; ?></span> Dashboard
      </button>
    </div>

    <div class="nav-section">
      <div class="nav-label">Account</div>
      <a class="nav-item" href="<?php echo e($userRole === 'Visitor' ? (Route::has('visitor.profile') ? route('visitor.profile') : '#') : (Route::has('student.profile') ? route('student.profile') : '#')); ?>"><span class="icon"><?php if (isset($component)) { $__componentOriginald5bf95aa182035908f29e85961a938de = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald5bf95aa182035908f29e85961a938de = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.profile','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.profile'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald5bf95aa182035908f29e85961a938de)): ?>
<?php $attributes = $__attributesOriginald5bf95aa182035908f29e85961a938de; ?>
<?php unset($__attributesOriginald5bf95aa182035908f29e85961a938de); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald5bf95aa182035908f29e85961a938de)): ?>
<?php $component = $__componentOriginald5bf95aa182035908f29e85961a938de; ?>
<?php unset($__componentOriginald5bf95aa182035908f29e85961a938de); ?>
<?php endif; ?></span> My Profile</a>
    </div>

    <div class="nav-section">
      <div class="nav-label">Borrowing</div>
      <button class="nav-item <?php echo e($activePanel === 'borrowings' ? 'active' : ''); ?>" type="button" data-panel-button="borrowings" data-title="My Borrowings" onclick="showPanel('borrowings')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginal124339efe8fed8b121246464561d0eb5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal124339efe8fed8b121246464561d0eb5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.borrow','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.borrow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal124339efe8fed8b121246464561d0eb5)): ?>
<?php $attributes = $__attributesOriginal124339efe8fed8b121246464561d0eb5; ?>
<?php unset($__attributesOriginal124339efe8fed8b121246464561d0eb5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal124339efe8fed8b121246464561d0eb5)): ?>
<?php $component = $__componentOriginal124339efe8fed8b121246464561d0eb5; ?>
<?php unset($__componentOriginal124339efe8fed8b121246464561d0eb5); ?>
<?php endif; ?></span> My Borrowings
      </button>
      <button class="nav-item <?php echo e($activePanel === 'fines' ? 'active' : ''); ?>" type="button" data-panel-button="fines" data-title="My Fines" onclick="showPanel('fines')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginal650dfa99c516960286d5fe50bcd1e7c5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.fine','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.fine'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5)): ?>
<?php $attributes = $__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5; ?>
<?php unset($__attributesOriginal650dfa99c516960286d5fe50bcd1e7c5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal650dfa99c516960286d5fe50bcd1e7c5)): ?>
<?php $component = $__componentOriginal650dfa99c516960286d5fe50bcd1e7c5; ?>
<?php unset($__componentOriginal650dfa99c516960286d5fe50bcd1e7c5); ?>
<?php endif; ?></span> My Fines
      </button>
      <button class="nav-item <?php echo e($activePanel === 'barcode-request' ? 'active' : ''); ?>" type="button" data-panel-button="barcode-request" data-title="Request Barcode" onclick="showPanel('barcode-request')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginalbabcb5a601c22b877bd5d302b6d05191 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbabcb5a601c22b877bd5d302b6d05191 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.barcode','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.barcode'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbabcb5a601c22b877bd5d302b6d05191)): ?>
<?php $attributes = $__attributesOriginalbabcb5a601c22b877bd5d302b6d05191; ?>
<?php unset($__attributesOriginalbabcb5a601c22b877bd5d302b6d05191); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbabcb5a601c22b877bd5d302b6d05191)): ?>
<?php $component = $__componentOriginalbabcb5a601c22b877bd5d302b6d05191; ?>
<?php unset($__componentOriginalbabcb5a601c22b877bd5d302b6d05191); ?>
<?php endif; ?></span> Request Barcode
      </button>

    </div>

    <div class="nav-section">
      <div class="nav-label">Tools</div>
      <a class="nav-item" href="<?php echo e(Route::has('opac.index') ? route('opac.index') : '#'); ?>" style="color:#2F9E8F;font-weight:600;"><span class="icon"><?php if (isset($component)) { $__componentOriginala0c73ad9511ae1934ff7056d4fc38e8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala0c73ad9511ae1934ff7056d4fc38e8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.search','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala0c73ad9511ae1934ff7056d4fc38e8a)): ?>
<?php $attributes = $__attributesOriginala0c73ad9511ae1934ff7056d4fc38e8a; ?>
<?php unset($__attributesOriginala0c73ad9511ae1934ff7056d4fc38e8a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala0c73ad9511ae1934ff7056d4fc38e8a)): ?>
<?php $component = $__componentOriginala0c73ad9511ae1934ff7056d4fc38e8a; ?>
<?php unset($__componentOriginala0c73ad9511ae1934ff7056d4fc38e8a); ?>
<?php endif; ?></span> Search Catalog</a>
    </div>

  
  <?php elseif($userRole === 'Researcher'): ?>
    <div class="nav-section">
      <div class="nav-label">Overview</div>
      <button class="nav-item <?php echo e($activePanel === 'dashboard' ? 'active' : ''); ?>" type="button" data-panel-button="dashboard" data-title="Researcher Dashboard" onclick="showPanel('dashboard')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.dashboard','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.dashboard'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf)): ?>
<?php $attributes = $__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf; ?>
<?php unset($__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf)): ?>
<?php $component = $__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf; ?>
<?php unset($__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf); ?>
<?php endif; ?></span> Dashboard
      </button>
    </div>

    <div class="nav-section">
      <div class="nav-label">Account</div>
      <a class="nav-item" href="<?php echo e(Route::has('researcher.profile') ? route('researcher.profile') : '#'); ?>"><span class="icon"><?php if (isset($component)) { $__componentOriginald5bf95aa182035908f29e85961a938de = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald5bf95aa182035908f29e85961a938de = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.profile','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.profile'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald5bf95aa182035908f29e85961a938de)): ?>
<?php $attributes = $__attributesOriginald5bf95aa182035908f29e85961a938de; ?>
<?php unset($__attributesOriginald5bf95aa182035908f29e85961a938de); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald5bf95aa182035908f29e85961a938de)): ?>
<?php $component = $__componentOriginald5bf95aa182035908f29e85961a938de; ?>
<?php unset($__componentOriginald5bf95aa182035908f29e85961a938de); ?>
<?php endif; ?></span> My Profile</a>
    </div>

    <div class="nav-section">
      <div class="nav-label">Research</div>
      <button class="nav-item <?php echo e($activePanel === 'proposals' ? 'active' : ''); ?>" type="button" data-panel-button="proposals" data-title="My Proposals" onclick="showPanel('proposals')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginalc193ef63664f4fbf943eccdba55870ec = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc193ef63664f4fbf943eccdba55870ec = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.proposal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.proposal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc193ef63664f4fbf943eccdba55870ec)): ?>
<?php $attributes = $__attributesOriginalc193ef63664f4fbf943eccdba55870ec; ?>
<?php unset($__attributesOriginalc193ef63664f4fbf943eccdba55870ec); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc193ef63664f4fbf943eccdba55870ec)): ?>
<?php $component = $__componentOriginalc193ef63664f4fbf943eccdba55870ec; ?>
<?php unset($__componentOriginalc193ef63664f4fbf943eccdba55870ec); ?>
<?php endif; ?></span> My Proposals
      </button>
      <button class="nav-item <?php echo e($activePanel === 'submit' ? 'active' : ''); ?>" type="button" data-panel-button="submit" data-title="Submit Proposal" onclick="showPanel('submit')">
        <span class="icon"><?php if (isset($component)) { $__componentOriginal52632fe7b137108a4c1d9fb6383ade19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal52632fe7b137108a4c1d9fb6383ade19 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.plus','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.plus'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal52632fe7b137108a4c1d9fb6383ade19)): ?>
<?php $attributes = $__attributesOriginal52632fe7b137108a4c1d9fb6383ade19; ?>
<?php unset($__attributesOriginal52632fe7b137108a4c1d9fb6383ade19); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal52632fe7b137108a4c1d9fb6383ade19)): ?>
<?php $component = $__componentOriginal52632fe7b137108a4c1d9fb6383ade19; ?>
<?php unset($__componentOriginal52632fe7b137108a4c1d9fb6383ade19); ?>
<?php endif; ?></span> Submit Proposal
      </button>
    </div>

  
  <?php else: ?>
    <div class="nav-section">
      <button class="nav-item active" type="button"><span class="icon"><?php if (isset($component)) { $__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.dashboard','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.dashboard'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf)): ?>
<?php $attributes = $__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf; ?>
<?php unset($__attributesOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf)): ?>
<?php $component = $__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf; ?>
<?php unset($__componentOriginaldd7efffb9c9f6e09cb77b3f1b8d38adf); ?>
<?php endif; ?></span> Dashboard</button>
    </div>
  <?php endif; ?>

  
  <div class="sidebar-bottom">
    <div class="user-chip">
      <div class="avatar"><?php echo e($avatar); ?></div>
      <div>
        <div class="user-name"><?php echo e($userName); ?></div>
        <div class="user-role"><?php echo e($userRole); ?></div>
      </div>
    </div>
    <form id="logout-form" action="<?php echo e(Route::has('logout') ? route('logout') : url('/login')); ?>" method="POST" style="display:none;">
      <?php echo csrf_field(); ?>
    </form>
    <button class="nav-item logout-item" type="button" onclick="if(window.showConfirm){window.showConfirm('Are you sure you want to log out?').then(function(ok){if(ok)document.getElementById('logout-form').submit()})}else{if(confirm('Are you sure you want to log out?'))document.getElementById('logout-form').submit()}">
      <span class="icon"><?php if (isset($component)) { $__componentOriginal88de01fd0a2dfb43f9ff296f6277e232 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal88de01fd0a2dfb43f9ff296f6277e232 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icons.logout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icons.logout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal88de01fd0a2dfb43f9ff296f6277e232)): ?>
<?php $attributes = $__attributesOriginal88de01fd0a2dfb43f9ff296f6277e232; ?>
<?php unset($__attributesOriginal88de01fd0a2dfb43f9ff296f6277e232); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal88de01fd0a2dfb43f9ff296f6277e232)): ?>
<?php $component = $__componentOriginal88de01fd0a2dfb43f9ff296f6277e232; ?>
<?php unset($__componentOriginal88de01fd0a2dfb43f9ff296f6277e232); ?>
<?php endif; ?></span> Logout
    </button>
  </div>
</aside>
<?php /**PATH C:\laragon\www\LIBRARY-MANAGEMENT-SYSTEM\resources\views/components/sidebar.blade.php ENDPATH**/ ?>