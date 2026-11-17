@props([
    'items' => [], // [['label' => '', 'value' => ''], ...]
    'columns' => 2, // 1, 2, 3, 4
    'transparent' => false,
])

@php
    $gridCols = [
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-3',
        4 => 'grid-cols-2 md:grid-cols-4',
    ];

    $colClass = $gridCols[$columns] ?? $gridCols[2];
    $bgClass = $transparent ? 'bg-white bg-opacity-70' : 'bg-white';
@endphp

<div {{ $attributes->merge(['class' => "grid {$colClass} gap-4"]) }}>
    @if($items)
        @foreach($items as $item)
            <div class="{{ $bgClass }} rounded-lg p-3 border border-gray-100">
                <p class="text-xs text-gray-600">{{ $item['label'] }}</p>
                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $item['value'] }}</p>
            </div>
        @endforeach
    @else
        {{ $slot }}
    @endif
</div>
