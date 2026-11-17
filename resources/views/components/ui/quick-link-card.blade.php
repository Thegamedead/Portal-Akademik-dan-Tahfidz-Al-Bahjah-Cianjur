@props([
    'href' => '#',
    'icon' => null,
    'iconColor' => 'blue', // blue, green, purple, yellow, amber, red, zamrud
    'title' => '',
    'description' => null,
])

@php
    $iconColors = [
        'blue' => 'bg-blue-100 text-blue-600 group-hover:bg-blue-200',
        'green' => 'bg-green-100 text-green-600 group-hover:bg-green-200',
        'purple' => 'bg-purple-100 text-purple-600 group-hover:bg-purple-200',
        'yellow' => 'bg-yellow-100 text-yellow-600 group-hover:bg-yellow-200',
        'amber' => 'bg-amber-100 text-amber-600 group-hover:bg-amber-200',
        'red' => 'bg-red-100 text-red-600 group-hover:bg-red-200',
        'zamrud' => 'bg-zamrud-100 text-zamrud-600 group-hover:bg-zamrud-200',
    ];

    $iconColorClass = $iconColors[$iconColor] ?? $iconColors['blue'];
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition group block']) }}>
    <div class="flex items-center gap-3">
        @if($icon)
            <div class="p-2 rounded-lg transition {{ $iconColorClass }} shrink-0">
                {!! $icon !!}
            </div>
        @endif

        <div class="flex-1 min-w-0">
            <h3 class="text-sm font-medium text-gray-900">{{ $title }}</h3>
            @if($description)
                <p class="text-xs text-gray-500 mt-0.5">{{ $description }}</p>
            @endif
        </div>
    </div>

    {{ $slot }}
</a>
