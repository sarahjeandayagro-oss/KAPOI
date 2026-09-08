<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title' => null, 'subtitle' => null]));

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

foreach (array_filter((['title' => null, 'subtitle' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<section <?php echo e($attributes->merge(['class' => 'card'])); ?>>
  <?php if($title || $subtitle || isset($actions)): ?>
    <div class="card-header">
      <div>
        <?php if($title): ?><div class="card-title"><?php echo e($title); ?></div><?php endif; ?>
        <?php if($subtitle): ?><div class="card-sub"><?php echo e($subtitle); ?></div><?php endif; ?>
      </div>
      <?php if(isset($actions)): ?><?php echo e($actions); ?><?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="card-body"><?php echo e($slot); ?></div>
</section>
<?php /**PATH C:\laragon\www\LIBRARY-MANAGEMENT-SYSTEM\resources\views/components/card.blade.php ENDPATH**/ ?>