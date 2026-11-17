<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'primary',
    'type' => 'button',
    'disabled' => false,
    'class' => '',
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
    'variant' => 'primary',
    'type' => 'button',
    'disabled' => false,
    'class' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Map variants to design system colors
    $variantClasses = [
        'primary'   => 'btn-modal-confirm',
        'danger'    => 'btn-modal-cancel',
        'secondary' => 'btn-modal-cancel',
        'outline'   => 'bg-white border-2 border-[#1B5E4F] text-[#1B5E4F] hover:bg-[#E8F5F1] focus:ring-[#1B5E4F] focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-md px-4 py-2 font-medium text-sm cursor-pointer transition-colors',
    ];

    $useCustomClass = in_array($variant, ['primary', 'danger', 'secondary']);
    $baseClasses    = $useCustomClass ? '' : '';
    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '';

    $buttonClasses = trim(implode(' ', [
        $variantClasses[$variant] ?? $variantClasses['primary'],
        $disabledClasses,
        $class
    ]));
?>

<button
    type="<?php echo e($type); ?>"
    <?php echo e($disabled ? 'disabled' : ''); ?>

    <?php echo e($attributes->merge(['class' => $buttonClasses])); ?>

>
    <?php echo e($slot); ?>

</button>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/components/ui/button.blade.php ENDPATH**/ ?>