@props([
    'color' => 'zamrud', // zamrud, blue, purple, green
    'icon' => null,
    'title' => null,
    'subtitle' => null,
])

@php
    $gradients = [
        'zamrud' => [
            'from' => 'from-zamrud-50',
            'to' => 'to-zamrud-100',
            'border' => 'border-zamrud-200',
            'iconBg' => 'bg-zamrud-500',
            'titleColor' => 'text-zamrud-900',
            'subtitleColor' => 'text-zamrud-700',
        ],
        'blue' => [
            'from' => 'from-blue-50',
            'to' => 'to-blue-100',
            'border' => 'border-blue-200',
            'iconBg' => 'bg-blue-500',
            'titleColor' => 'text-blue-900',
            'subtitleColor' => 'text-blue-700',
        ],
        'purple' => [
            'from' => 'from-purple-50',
            'to' => 'to-purple-100',
            'border' => 'border-purple-200',
            'iconBg' => 'bg-purple-500',
            'titleColor' => 'text-purple-900',
            'subtitleColor' => 'text-purple-700',
        ],
        'green' => [
            'from' => 'from-green-50',
            'to' => 'to-green-100',
            'border' => 'border-green-200',
            'iconBg' => 'bg-green-500',
            'titleColor' => 'text-green-900',
            'subtitleColor' => 'text-green-700',
        ],
    ];

    $config = $gradients[$color] ?? $gradients['zamrud'];
@endphp

<div {{ $attributes->merge(['class' => "bg-linear-to-br {$config['from']} {$config['to']} rounded-lg shadow-sm border {$config['border']} p-6"]) }}>
    @if($icon || $title)
        <div class="flex items-center gap-3 mb-4">
            @if($icon)
                <div class="p-2 {{ $config['iconBg'] }} rounded-lg shrink-0">
                    {!! $icon !!}
                </div>
            @endif

            @if($title || $subtitle)
                <div>
                    @if($title)
                        <h2 class="text-lg font-semibold {{ $config['titleColor'] }}">{{ $title }}</h2>
                    @endif
                    @if($subtitle)
                        <p class="text-sm {{ $config['subtitleColor'] }}">{{ $subtitle }}</p>
                    @endif
                </div>
            @endif
        </div>
    @endif

    {{ $slot }}
</div>
