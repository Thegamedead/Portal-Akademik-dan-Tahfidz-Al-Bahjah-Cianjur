@props([
    'title' => '',
    'confirmText' => 'Konfirmasi',
    'cancelText' => 'Batal',
    'confirmVariant' => 'primary',
    'show' => false,
    'wireModel' => null,
])

<div
    @if($wireModel)
        x-data="{ show: @entangle($wireModel) }"
    @else
        x-data="{ show: {{ $show ? 'true' : 'false' }} }"
    @endif
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto pointer-events-none"
    style="display: none;"
>
    {{-- Backdrop Overlay - Tinta dengan 60% opacity --}}
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 pointer-events-none"
        @click="show = false"
    ></div>

    {{-- Modal Container --}}
    <div class="flex items-center justify-center min-h-screen px-4 py-6">
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl"
            style="border:1px solid #E5E7EB;"
            @click.away="show = false"
        >
            {{-- Modal Header --}}
            @if($title)
            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                <h3 class="text-lg font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                    {{ $title }}
                </h3>
                <button
                    type="button"
                    @click="show = false"
                    class="rounded-lg p-1 transition-colors focus:outline-none hover:bg-gray-100"
                    style="color:#6B7280;"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            @endif

            {{-- Modal Body --}}
            <div class="px-6 py-5">
                {{ $slot }}
            </div>

            {{-- Modal Footer --}}
            @if(isset($footer))
                <div class="flex items-center justify-end gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB; background-color: #F9FAFB;">
                    {{ $footer }}
                </div>
            @else
                <div class="flex items-center justify-end gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB; background-color: #F9FAFB;">
                    <x-ui.button
                        variant="secondary"
                        @click="show = false"
                    >
                        {{ $cancelText }}
                    </x-ui.button>

                    <x-ui.button
                        :variant="$confirmVariant"
                        {{ $attributes->whereStartsWith('wire:click') }}
                    >
                        {{ $confirmText }}
                    </x-ui.button>
                </div>
            @endif
        </div>
    </div>
</div>
