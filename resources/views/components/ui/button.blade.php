@props([
    'variant' => 'primary',
    'type' => 'button',
    'disabled' => false,
    'class' => '',
])

@php
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
@endphp

<button
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => $buttonClasses]) }}
>
    {{ $slot }}
</button>
