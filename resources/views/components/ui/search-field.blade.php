@props([
    'placeholder' => 'Cari...',
    'wireModel' => null,
    'name' => 'search',
    'value' => '',
    'class' => '',
])

@php
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
@endphp

<div class="relative rounded-md">
    {{-- SVG magnifying glass icon (20x20, gray-400 color) on the left side --}}
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>

    {{-- Search input field with proper padding-left to accommodate icon --}}
    <input
        type="search"
        name="{{ $name }}"
        @if($wireModel)
            wire:model.live.debounce.300ms="{{ $wireModel }}"
        @endif
        placeholder="{{ $placeholder }}"
        value="{{ $value }}"
        {{ $attributes->merge(['class' => $inputClasses])->merge(['style' => $inlineStyles]) }}
    />
</div>
