@props([
    'icon' => 'inbox', // inbox, calendar, book, users, document, search, chart
    'title' => 'Belum Ada Data',
    'description' => null,
    'action' => null,
    'actionText' => null,
    'actionRoute' => null,
    'size' => 'md', // sm, md, lg
])

@php
    $icons = [
        'inbox' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>',
        'book' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>',
        'document' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>',
        'search' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>',
        'chart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>',
    ];

    $sizes = [
        'sm' => ['icon' => 'w-10 h-10', 'title' => 'text-base', 'desc' => 'text-xs', 'py' => 'py-6'],
        'md' => ['icon' => 'w-12 h-12', 'title' => 'text-lg', 'desc' => 'text-sm', 'py' => 'py-8'],
        'lg' => ['icon' => 'w-16 h-16', 'title' => 'text-xl', 'desc' => 'text-base', 'py' => 'py-12'],
    ];

    $sizeConfig = $sizes[$size] ?? $sizes['md'];
    $iconPath = $icons[$icon] ?? $icons['inbox'];
@endphp

<div {{ $attributes->merge(['class' => "text-center {$sizeConfig['py']}"]) }}>
    <div class="flex justify-center mb-4">
        <svg class="{{ $sizeConfig['icon'] }} text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $iconPath !!}
        </svg>
    </div>

    <h3 class="font-semibold text-gray-900 {{ $sizeConfig['title'] }} mb-2">{{ $title }}</h3>

    @if($description)
        <p class="text-gray-500 {{ $sizeConfig['desc'] }} max-w-md mx-auto">{{ $description }}</p>
    @endif

    @if($slot->isNotEmpty())
        <div class="mt-4">
            {{ $slot }}
        </div>
    @elseif($action || $actionRoute)
        <div class="mt-4">
            @if($actionRoute)
                <a href="{{ $actionRoute }}" class="inline-flex items-center gap-2 text-sm font-medium text-zamrud-600 hover:text-zamrud-700 transition">
                    {{ $actionText ?? 'Tambah Data' }} →
                </a>
            @elseif($action)
                <button type="button" wire:click="{{ $action }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-zamrud-600 hover:bg-zamrud-700 rounded-lg transition">
                    {{ $actionText ?? 'Tambah Data' }}
                </button>
            @endif
        </div>
    @endif
</div>
