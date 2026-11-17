@props([
    'percentage' => 0,
    'color' => 'primary', // primary, blue, green, purple, yellow, amber, red, custom
    'size' => 'md', // sm, md, lg
    'showLabel' => false,
    'animate' => true,
    'customColor' => null,
])

@php
    // Size configurations
    $heights = [
        'sm' => 'h-1.5',
        'md' => 'h-2',
        'lg' => 'h-3',
    ];

    // Color configurations
    $colors = [
        'primary' => '#1B5E4F',
        'blue' => '#2563EB',
        'green' => '#16A34A',
        'purple' => '#9333EA',
        'yellow' => '#EAB308',
        'amber' => '#D97706',
        'red' => '#DC2626',
    ];

    $barColor = $customColor ?? ($colors[$color] ?? $colors['primary']);
    $heightClass = $heights[$size] ?? $heights['md'];
    $percentage = min(max($percentage, 0), 100); // Clamp between 0-100
@endphp

<div {{ $attributes->class(['w-full']) }}>
    @if($showLabel)
        <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
            <span>{{ $slot }}</span>
            <span class="font-semibold">{{ number_format($percentage, 1) }}%</span>
        </div>
    @endif

    <div class="bg-gray-200 rounded-full {{ $heightClass }} overflow-hidden">
        <div
            class="rounded-full {{ $heightClass }} {{ $animate ? 'transition-all duration-500' : '' }}"
            style="width: {{ $percentage }}%; background-color: {{ $barColor }};"
            role="progressbar"
            aria-valuenow="{{ $percentage }}"
            aria-valuemin="0"
            aria-valuemax="100"
        ></div>
    </div>
</div>
