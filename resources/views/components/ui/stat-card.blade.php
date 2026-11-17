@props([
    'title' => '',
    'value' => '',
    'subtitle' => null,
    'icon' => null,
    'iconColor' => 'blue', // blue, green, purple, yellow, amber, red, zamrud
    'trend' => null, // up, down, neutral
    'trendValue' => null,
])

@php
    $iconColors = [
        'blue' => 'bg-blue-100 text-blue-600',
        'green' => 'bg-green-100 text-green-600',
        'purple' => 'bg-purple-100 text-purple-600',
        'yellow' => 'bg-yellow-100 text-yellow-600',
        'amber' => 'bg-amber-100 text-amber-600',
        'red' => 'bg-red-100 text-red-600',
        'zamrud' => 'bg-zamrud-100 text-zamrud-600',
    ];

    $iconColorClass = $iconColors[$iconColor] ?? $iconColors['blue'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border border-gray-200 p-5']) }}>
    <div class="flex items-center justify-between mb-3">
        @if($icon)
            <div class="p-2 rounded-lg {{ $iconColorClass }}">
                {!! $icon !!}
            </div>
        @endif

        <span class="text-2xl font-bold text-gray-900">{{ $value }}</span>
    </div>

    <h3 class="text-sm font-medium text-gray-700">{{ $title }}</h3>

    @if($subtitle)
        <p class="text-xs text-gray-500 mt-1">{{ $subtitle }}</p>
    @endif

    @if($trend)
        <div class="mt-2 flex items-center gap-1 text-xs">
            @if($trend === 'up')
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <span class="text-green-600 font-medium">{{ $trendValue }}</span>
            @elseif($trend === 'down')
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                </svg>
                <span class="text-red-600 font-medium">{{ $trendValue }}</span>
            @endif
        </div>
    @endif

    {{ $slot }}
</div>
