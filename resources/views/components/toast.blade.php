@props(['type' => null, 'variant' => null, 'message'])

@php
    $type = $variant ?? $type ?? 'success';
    $variants = [
        'success' => [
            'style' => 'background-color:#E8F5F1; border-color:#1B5E4F;',
            'text_style' => 'color:#1B5E4F;',
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
        ],
        'error' => [
            'style' => 'background-color:#FDEAEA; border-color:#9B3A3A;',
            'text_style' => 'color:#9B3A3A;',
            'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
        ],
        'info' => [
            'style' => 'background-color:#EFF6FF; border-color:#3B82F6;',
            'text_style' => 'color:#3B82F6;',
            'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
        ],
        'warning' => [
            'style' => 'background-color:#FFFBEB; border-color:#F59E0B;',
            'text_style' => 'color:#D97706;',
            'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
        ],
    ];

    // support 'sukses' mapping since some places use it
    if ($type === 'sukses') $type = 'success';

    $variant = $variants[$type] ?? $variants['success'];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(() => show = false, 5000)"
    x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="w-80 sm:w-96 pointer-events-auto"
    role="alert"
>
    <div class="rounded-lg shadow-lg overflow-hidden border" style="{{ $variant['style'] }}">
        <div class="p-4">
            <div class="flex items-start">
                <div class="shrink-0">
                    <svg class="h-6 w-6" style="{{ $variant['text_style'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $variant['icon'] }}"></path>
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium" style="{{ $variant['text_style'] }}">
                        {{ $message }}
                    </p>
                </div>
                <div class="ml-4 shrink-0 flex">
                    <button
                        @click="show = false"
                        class="inline-flex rounded-md hover:opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2"
                        style="{{ $variant['text_style'] }}"
                    >
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
