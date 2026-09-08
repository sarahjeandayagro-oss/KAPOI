<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label', 'value', 'change' => null, 'tone' => 'primary', 'icon' => null]));

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

foreach (array_filter((['label', 'value', 'change' => null, 'tone' => 'primary', 'icon' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<div class="stat-card <?php echo e($tone); ?>">
  <?php if($icon): ?>
    <div class="stat-icon"><?php echo e($icon); ?></div>
  <?php endif; ?>
  <div class="stat-label"><?php echo e($label); ?></div>
  <div class="stat-value"><?php echo e($value); ?></div>
  <?php if($change): ?>
    <div class="stat-change"><?php echo e($change); ?></div>
  <?php endif; ?>

</div>
<?php /**PATH C:\laragon\www\LIBRARY-MANAGEMENT-SYSTEM\resources\views/components/stat-card.blade.php ENDPATH**/ ?>