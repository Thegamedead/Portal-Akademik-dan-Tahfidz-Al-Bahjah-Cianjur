<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'placeholder' => 'Cari...',
    'wireModel' => null,
    'name' => 'search',
    'value' => '',
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
    'placeholder' => 'Cari...',
    'wireModel' => null,
    'name' => 'search',
    'value' => '',
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
    // Base classes for the search input following design system
    $baseClasses = 'w-full rounded-lg py-2.5 pl-10 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-[#1B5E4F] transition-all';

    // Border color from design system
    $borderStyle = 'border: 1.5px solid #D1D5DB;';

    // Text and background colors
    $colorStyle = 'color: #1A2422; background-color: #FFFFFF;';

    // Typography from design system - Inter for body/data
    $fontStyle = "font-family: 'Inter', sans-serif;";

    // Combine all inline styles
    $inlineStyles = $borderStyle . $colorStyle . $fontStyle;

    // Combine classes
    $inputClasses = trim($baseClasses . ' ' . $class);
?>

<div class="relative rounded-md">
    
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>

    
    <input
        type="search"
        name="<?php echo e($name); ?>"
        <?php if($wireModel): ?>
            wire:model.live.debounce.300ms="<?php echo e($wireModel); ?>"
        <?php endif; ?>
        placeholder="<?php echo e($placeholder); ?>"
        value="<?php echo e($value); ?>"
        <?php echo e($attributes->merge(['class' => $inputClasses])->merge(['style' => $inlineStyles])); ?>

    />
</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/components/ui/search-field.blade.php ENDPATH**/ ?>