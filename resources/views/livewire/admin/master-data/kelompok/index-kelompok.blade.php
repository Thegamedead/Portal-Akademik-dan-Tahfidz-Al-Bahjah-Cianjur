{{--
    View: livewire/admin/master-data/kelompok/index-kelompok.blade.php
    Komponen Livewire: App\Livewire\Admin\MasterData\Kelompok\IndexKelompok

    Daftar kelompok dengan fitur filter dan status kapasitas color-coded
    Requirements: 5.6, 5.9, 10.6, 10.7, 10.10
--}}
<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- HEADER --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Kelompok Tahfidz
                </h2>
                <p class="mt-1 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
                    Kelola data kelompok tahfidz dan anggota kelompok
                </p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ACTION BUTTONS & SEARCH --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        {{-- Single Row: Flex Layout --}}
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

            {{-- LEFT: Action Buttons in Horizontal Row --}}
            <div class="flex flex-wrap items-center gap-3">
                {{-- Tambah Kelompok --}}
                <button
                    wire:click="openCreateModal"
                    type="button"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 cursor-pointer"
                    style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah
                </button>

                {{-- Edit Button (enabled only when 1 item selected) --}}
                @if(count($selectedIds) === 1)
                    <button
                        wire:click="openEditModal({{ $selectedIds[0] }})"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
                        style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                @else
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#FFFFFF;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih tepat 1 kelompok untuk edit"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                @endif

                {{-- Hapus Button (enabled when items selected) --}}
                @if(count($selectedIds) > 0)
                    <button
                        wire:click="openBulkDeleteConfirm"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
                        style="background-color:#9B3A3A;color:#FFFFFF;border:1.5px solid #9B3A3A;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                @else
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih minimal 1 kelompok untuk hapus"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                @endif

                {{-- Separator --}}
                <span class="text-gray-300 text-xl font-light select-none">|</span>

                {{-- Action Buttons (enabled only when 1 item selected) --}}
                @if(count($selectedIds) === 1)
                    <button
                        wire:click="bukaAnggotaModal({{ $selectedIds[0] }})"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
                        style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Anggota Kelompok
                    </button>
                    <button
                        wire:click="bukaTempatkanSiswaModal({{ $selectedIds[0] }})"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
                        style="background-color:#16A34A;color:#FFFFFF;border:1.5px solid #16A34A;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Tempatkan Siswa
                    </button>
                    <a
                        href="{{ route('admin.kelompok.absensisetoran', $selectedIds[0]) }}"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
                        style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Setoran Hafalan
                    </a>

                {{-- Dropdown Pengaturan --}}
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        @click.away="open = false"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
                        style="background-color:#FFFFFF;color:#374151;border:1.5px solid #D1D5DB;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Pengaturan
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute z-50 left-0 mt-2 w-56 rounded-xl shadow-xl bg-white focus:outline-none overflow-hidden"
                         style="display: none; border: 1.5px solid #D1D5DB; box-shadow: 0 8px 24px rgba(0,0,0,0.12);">
                        <div class="p-1.5 space-y-0.5">
                            <button type="button" wire:click="bukaPengaturanModal" @click="open = false"
                                class="group flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg transition-all"
                                style=""
                                onmouseover="this.style.backgroundColor='#F0FDF4';this.style.color='#166534';this.querySelector('svg').style.color='#166534'"
                                onmouseout="this.style.backgroundColor='';this.style.color='';this.querySelector('svg').style.color='#9CA3AF'">
                                <svg class="mr-3 h-4 w-4 transition-colors" style="color:#9CA3AF" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Kapasitas Kelompok
                            </button>
                            <button type="button" wire:click="bukaTargetHafalanModal" @click="open = false"
                                class="group flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg transition-all"
                                onmouseover="this.style.backgroundColor='#F0FDF4';this.style.color='#166534';this.querySelector('svg').style.color='#166534'"
                                onmouseout="this.style.backgroundColor='';this.style.color='';this.querySelector('svg').style.color='#9CA3AF'">
                                <svg class="mr-3 h-4 w-4 transition-colors" style="color:#9CA3AF" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Target Hafalan
                            </button>
                            <button type="button" wire:click="bukaKomponenPenilaianModal" @click="open = false"
                                class="group flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg transition-all"
                                onmouseover="this.style.backgroundColor='#F0FDF4';this.style.color='#166534';this.querySelector('svg').style.color='#166534'"
                                onmouseout="this.style.backgroundColor='';this.style.color='';this.querySelector('svg').style.color='#9CA3AF'">
                                <svg class="mr-3 h-4 w-4 transition-colors" style="color:#9CA3AF" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                Komponen Penilaian
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Info Label --}}
                <div class="flex items-center sm:ml-2 sm:pl-4 sm:border-l border-gray-200">
                    <span class="text-sm text-gray-500">
                        Total <strong style="color:#1A2422;">{{ $kelompoks->total() }}</strong> kelompok
                        @if(count($selectedIds) > 0)
                            <span class="mx-1.5">&bull;</span>
                            <strong style="color:#1B5E4F;">{{ count($selectedIds) }}</strong> terpilih
                        @endif
                    </span>
                </div>
                @else
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih tepat 1 kelompok untuk lihat anggota"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Anggota Kelompok
                    </button>
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih tepat 1 kelompok untuk tempatkan siswa"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Tempatkan Siswa
                    </button>
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih tepat 1 kelompok untuk lihat setoran hafalan"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Setoran Hafalan
                    </button>

                {{-- Dropdown Pengaturan --}}
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        @click.away="open = false"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
                        style="background-color:#FFFFFF;color:#374151;border:1.5px solid #D1D5DB;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Pengaturan
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute z-50 left-0 mt-2 w-56 rounded-xl bg-white focus:outline-none overflow-hidden"
                         style="display: none; border: 1.5px solid #D1D5DB; box-shadow: 0 8px 24px rgba(0,0,0,0.12);">
                        <div class="p-1.5 space-y-0.5">
                            <button type="button" wire:click="bukaPengaturanModal" @click="open = false"
                                class="group flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg transition-all"
                                onmouseover="this.style.backgroundColor='#F0FDF4';this.style.color='#166534';this.querySelector('svg').style.color='#166534'"
                                onmouseout="this.style.backgroundColor='';this.style.color='';this.querySelector('svg').style.color='#9CA3AF'">
                                <svg class="mr-3 h-4 w-4 transition-colors" style="color:#9CA3AF" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Kapasitas Kelompok
                            </button>
                            <button type="button" wire:click="bukaTargetHafalanModal" @click="open = false"
                                class="group flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg transition-all"
                                onmouseover="this.style.backgroundColor='#F0FDF4';this.style.color='#166534';this.querySelector('svg').style.color='#166534'"
                                onmouseout="this.style.backgroundColor='';this.style.color='';this.querySelector('svg').style.color='#9CA3AF'">
                                <svg class="mr-3 h-4 w-4 transition-colors" style="color:#9CA3AF" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Target Hafalan
                            </button>
                            <button type="button" wire:click="bukaKomponenPenilaianModal" @click="open = false"
                                class="group flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg transition-all"
                                onmouseover="this.style.backgroundColor='#F0FDF4';this.style.color='#166534';this.querySelector('svg').style.color='#166534'"
                                onmouseout="this.style.backgroundColor='';this.style.color='';this.querySelector('svg').style.color='#9CA3AF'">
                                <svg class="mr-3 h-4 w-4 transition-colors" style="color:#9CA3AF" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                Komponen Penilaian
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- RIGHT: Search Field + Filter Dropdown Button --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-[400px]">
                {{-- Search Input --}}
                <div class="flex-1">
                    <x-ui.search-field
                        wireModel="search"
                        placeholder="Cari nama kelompok atau guru..."
                    />
                </div>

                {{-- Filter Dropdown Button --}}
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        type="button"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none whitespace-nowrap cursor-pointer w-full sm:w-auto"
                        style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                        @if($filterTingkat || $filterJenis || $filterGuruPembimbing)
                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                {{ collect([$filterTingkat, $filterJenis, $filterGuruPembimbing])->filter()->count() }}
                            </span>
                        @endif
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open"
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute z-50 right-0 mt-2 w-64 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                         style="display: none; border: 1px solid #E5E7EB;">

                        <div class="p-4 space-y-3">
                            {{-- Filter Header --}}
                            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                                <h3 class="text-sm font-semibold" style="color:white;font-family:'Plus Jakarta Sans',sans-serif;">
                                    Filter Kelompok
                                </h3>
                                @if($filterTingkat || $filterJenis || $filterGuruPembimbing)
                                    <button
                                        wire:click="resetFilters"
                                        type="button"
                                        class="text-xs font-medium text-gray-500 hover:text-[#1B5E4F] transition-colors"
                                    >
                                        Reset
                                    </button>
                                @endif
                            </div>

                            {{-- Filter Tingkat --}}
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Tingkat
                                </label>
                                <select
                                    wire:model.live="filterTingkat"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    <option value="Dasar">Dasar</option>
                                    <option value="Menengah">Menengah</option>
                                    <option value="Atas">Atas</option>
                                </select>
                            </div>

                            {{-- Filter Jenis --}}
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Jenis
                                </label>
                                <select
                                    wire:model.live="filterJenis"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    <option value="Banin">Banin</option>
                                    <option value="Banat">Banat</option>
                                </select>
                            </div>

                            {{-- Filter Guru Pembimbing --}}
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Guru Pembimbing
                                </label>
                                <select
                                    wire:model.live="filterGuruPembimbing"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    @foreach($guruPembimbings as $guru)
                                        <option value="{{ $guru->id }}">
                                            {{ $guru->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Removed old collapsible filter panel --}}
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TABLE --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="background-color:#1B5E4F;">
                        {{-- Checkbox Column --}}
                        <th class="px-6 py-4 text-center" style="width:60px;">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                class="w-4 h-4 rounded transition-colors cursor-pointer"
                                style="color:#1B5E4F;border-color:#D1D5DB;"
                            >
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tingkat</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Jenis</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Nama Kelompok</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Guru Pembimbing</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Jumlah Juz</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Juz Ke</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Ayat</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Jumlah Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Status Kapasitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    @forelse($kelompoks as $kelompok)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Checkbox --}}
                            <td class="px-6 py-4 text-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedIds"
                                    value="{{ $kelompok->id }}"
                                    class="w-4 h-4 rounded transition-colors cursor-pointer"
                                    style="color:#1B5E4F;border-color:#D1D5DB;"
                                >
                            </td>

                            {{-- Tingkat --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                @php
                                    $tingkatColor = $this->getTingkatBadgeColor($kelompok->tingkat);
                                    $tingkatBg = match($tingkatColor) {
                                        'zamrud' => '#E8F5F1',
                                        'emas' => '#FEF3C7',
                                        default => '#F1F5F9'
                                    };
                                    $tingkatText = match($tingkatColor) {
                                        'zamrud' => '#1B5E4F',
                                        'emas' => '#92400E',
                                        default => '#475569'
                                    };
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                    style="background-color:{{ $tingkatBg }};color:{{ $tingkatText }};"
                                >
                                    {{ $kelompok->tingkat }}
                                </span>
                            </td>

                            {{-- Jenis --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                @php
                                    $jenisColor = $this->getJenisBadgeColor($kelompok->jenis);
                                    $jenisBg = match($jenisColor) {
                                        'zamrud' => '#E8F5F1',
                                        'emas' => '#FEF3C7',
                                        default => '#F1F5F9'
                                    };
                                    $jenisText = match($jenisColor) {
                                        'zamrud' => '#1B5E4F',
                                        'emas' => '#92400E',
                                        default => '#475569'
                                    };
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                    style="background-color:{{ $jenisBg }};color:{{ $jenisText }};"
                                >
                                    {{ $kelompok->jenis }}
                                </span>
                            </td>

                            {{-- Nama Kelompok --}}
                            <td class="px-6 py-4 text-sm font-medium" style="color:#1A2422;">
                                {{ $kelompok->nama }}
                            </td>

                            {{-- Guru Pembimbing --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                {{ $kelompok->guruPembimbing->nama }}
                            </td>

                            {{-- Jumlah Juz --}}
                            <td class="px-6 py-4 text-sm text-center" style="color:#1A2422;">
                                @php
                                    $config = \App\Models\ParameterKonfigurasi::first();
                                    $batasTingkat = $config ? ($config->batas_tingkat ?? []) : [];
                                    $juzList = $batasTingkat[$kelompok->tingkat] ?? [];
                                    $jumlahJuz = count($juzList);
                                @endphp
                                <span class="font-medium" style="font-variant-numeric: tabular-nums;">{{ $jumlahJuz }}</span> Juz
                            </td>

                            {{-- Juz Ke --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                @if($jumlahJuz > 0)
                                    @php
                                        // Group consecutive juz into ranges
                                        $ranges = [];
                                        $sortedJuz = $juzList;
                                        sort($sortedJuz);

                                        if (count($sortedJuz) > 0) {
                                            $start = $sortedJuz[0];
                                            $end = $sortedJuz[0];

                                            for ($i = 1; $i < count($sortedJuz); $i++) {
                                                if ($sortedJuz[$i] == $end + 1) {
                                                    $end = $sortedJuz[$i];
                                                } else {
                                                    $ranges[] = $start == $end ? $start : "$start-$end";
                                                    $start = $sortedJuz[$i];
                                                    $end = $sortedJuz[$i];
                                                }
                                            }
                                            $ranges[] = $start == $end ? $start : "$start-$end";
                                        }
                                    @endphp
                                    <span class="text-xs" style="color:#1A2422;">
                                        Juz {{ implode(', ', $ranges) }}
                                    </span>
                                @else
                                    <span class="text-xs" style="color:#9CA3AF;">-</span>
                                @endif
                            </td>

                            {{-- Ayat --}}
                            <td class="px-6 py-4 text-sm text-center" style="color:#1A2422;">
                                @php
                                    $totalAyat = 0;
                                    if ($jumlahJuz > 0) {
                                        $totalAyat = \App\Models\ReferensiQuran::whereIn('juz_ke', $juzList)
                                            ->sum('jumlah_ayat');
                                    }
                                @endphp
                                <span class="font-medium" style="font-variant-numeric: tabular-nums;">{{ number_format($totalAyat, 0, ',', '.') }}</span> Ayat
                            </td>

                            {{-- Jumlah Siswa --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <span class="font-medium">{{ $kelompok->jumlah_siswa }}</span> / {{ $kelompok->kapasitas_maks }}
                            </td>

                            {{-- Status Kapasitas --}}
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = $this->getStatusKapasitasBadgeColor($kelompok->status_kapasitas);
                                    $statusBg = $statusColor === 'zamrud' ? '#1B5E4F' : '#9B3A3A';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                    style="background-color:{{ $statusBg }};color:#FFFFFF;"
                                >
                                    {{ $kelompok->status_kapasitas }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        @if($filterTingkat || $filterJenis || $filterTahunAjaran || $filterGuruPembimbing)
                                            Tidak ada kelompok yang sesuai dengan filter
                                        @else
                                            Belum ada data kelompok
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($kelompoks->hasPages())
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                {{ $kelompoks->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL KONFIRMASI HAPUS --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showBulkDeleteConfirm)
        {{-- ... (existing bulk delete modal omitted for brevity if unchanged, but I must provide the FULL original block I am replacing plus the new ones) --}}
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif;">
            <div class="fixed inset-0 pointer-events-none" wire:click="closeBulkDeleteConfirm"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6" style="border:2px solid #D1D5DB;">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full" style="background-color:#FEE2E2;">
                        <svg class="w-6 h-6" style="color:#9B3A3A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-center mb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                        Konfirmasi Penghapusan Massal
                    </h3>
                    <div class="text-sm mb-6" style="color:#6B7280;">
                        <p class="text-center mb-3">
                            Apakah Anda yakin ingin menghapus <strong style="color:#9B3A3A;">{{ count($selectedIds) }} kelompok</strong> yang dipilih?
                        </p>
                        <div class="mt-3 p-3 rounded-lg" style="background-color:#FEF3C7;border-left:3px solid #C9A227;">
                            <p class="font-medium mb-2" style="color:#1A2422;font-size:0.8125rem;">
                                Konsekuensi penghapusan:
                            </p>
                            <ul class="space-y-1 ml-4 text-xs" style="color:#4B5563;list-style-type:disc;">
                                <li>Siswa di kelompok ini akan kehilangan kelompoknya</li>
                                <li>Siswa akan berstatus "Belum Ditempatkan"</li>
                                <li>Semua riwayat sesi, absensi, dan setoran kelompok ini akan terhapus permanen</li>
                                <li>Data tidak dapat dikembalikan setelah dihapus</li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <x-ui.button variant="secondary" wire:click="closeBulkDeleteConfirm" class="flex-1">Batal</x-ui.button>
                        <x-ui.button variant="danger" wire:click="bulkDelete" class="flex-1">Hapus Semua</x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL FORM KELOMPOK --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showFormModal)
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif;">
            <div class="fixed inset-0 pointer-events-none" wire:click="closeFormModal"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-4xl w-full overflow-visible flex flex-col" style="border:1px solid #E5E7EB; max-height:90vh;">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                        <h3 class="text-xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                            {{ $isEdit ? 'Edit Kelompok Tahfidz' : 'Tambah Kelompok Tahfidz' }}
                        </h3>
                        <button wire:click="closeFormModal" class="text-white hover:text-gray-200 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Form Content (Scrollable) --}}
                    <div class="overflow-y-auto flex-1 p-6 space-y-6" style="overflow-x: visible;">
                        <div>
                            <h4 class="text-lg font-semibold text-[#1A2422] mb-4 border-b pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">Informasi Kelompok</h4>
                            <div class="space-y-3">
                                {{-- Nama --}}
                                <div class="grid grid-cols-12 gap-3 items-center">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Nama Kelompok <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                    <div class="col-span-8">
                                        <input type="text" wire:model="nama" maxlength="100" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] transition-all text-sm" placeholder="Contoh: Kelompok A Dasar Banin">
                                        @error('nama') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Jenis --}}
                                <div class="grid grid-cols-12 gap-3 items-center">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Jenis Kelompok <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                    <div class="col-span-8">
                                        <select wire:model.live="jenis" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] transition-all text-sm">
                                            <option value="">Pilih Jenis</option>
                                            <option value="Banin">Banin (Putra)</option>
                                            <option value="Banat">Banat (Putri)</option>
                                        </select>
                                        @error('jenis') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Tingkat --}}
                                <div class="grid grid-cols-12 gap-3 items-center">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Tingkat <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                    <div class="col-span-8">
                                        <select wire:model="tingkat" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] transition-all text-sm">
                                            <option value="">Pilih Tingkat</option>
                                            <option value="Dasar">Dasar</option>
                                            <option value="Menengah">Menengah</option>
                                            <option value="Atas">Atas</option>
                                        </select>
                                        @error('tingkat') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Guru Pembimbing --}}
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Guru Pembimbing <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <select wire:model="guru_pembimbing_id" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] transition-all text-sm">
                                            <option value="">Pilih Guru Pembimbing</option>
                                            @foreach($this->formGuruPembimbings as $guru)
                                                <option value="{{ $guru->id }}">
                                                    {{ $guru->nama }}
                                                    @if($guru->kelompoksBimbingan->count() > 0)
                                                        (Membimbing: {{ $guru->kelompoksBimbingan->pluck('nama')->join(', ') }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('guru_pembimbing_id') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror

                                        {{-- Info guru yang sudah ditugaskan --}}
                                        @if($guru_pembimbing_id)
                                            @php
                                                $selectedGuru = $this->formGuruPembimbings->firstWhere('id', $guru_pembimbing_id);
                                            @endphp
                                            @if($selectedGuru && $selectedGuru->kelompoksBimbingan->count() > 0)
                                                <div class="mt-2 p-3 rounded-lg" style="background-color:#E0F2FE;border:1px solid #7DD3FC;">
                                                    <div class="flex items-start gap-2">
                                                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:#0369A1;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <div class="flex-1">
                                                            <p class="text-xs font-semibold mb-1" style="color:#0C4A6E;">
                                                                Guru ini sudah membimbing kelompok lain:
                                                            </p>
                                                            <ul class="space-y-1">
                                                                @foreach($selectedGuru->kelompoksBimbingan as $kel)
                                                                    <li class="flex items-center gap-1.5">
                                                                        <span class="inline-block w-1 h-1 rounded-full flex-shrink-0" style="background-color:#0369A1;"></span>
                                                                        <span class="text-xs" style="color:#075985;">
                                                                            <strong>{{ $kel->nama }}</strong> ({{ $kel->tingkat }} - {{ $kel->jenis }})
                                                                        </span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif

                                        @if($isEdit && $guru_pembimbing_id != $originalGuruPembimbingId)
                                            <div class="mt-2 p-2.5 rounded-lg" style="background-color:#FEF3C7;border:1px solid #F59E0B;">
                                                <p class="text-xs" style="color:#92400E;">
                                                    <strong>Perhatian:</strong> Anda mengubah guru pembimbing. Perubahan ini akan dicatat dalam riwayat.
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Tanggal Penempatan Guru --}}
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Tanggal Penempatan <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8 relative">
                                        <div wire:ignore>
                                            <input
                                                type="text"
                                                id="tanggal_penempatan_guru"
                                                x-data="{
                                                    init() {
                                                        const fp = flatpickr($el, {
                                                            dateFormat: 'Y-m-d',
                                                            static: false,
                                                            appendTo: document.body,
                                                            position: 'auto center',
                                                            locale: {
                                                                firstDayOfWeek: 1,
                                                                weekdays: {
                                                                    shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                                                                    longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
                                                                },
                                                                months: {
                                                                    shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                                                                    longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                                                                }
                                                            },
                                                            onChange: function(selectedDates, dateStr) {
                                                                @this.set('tanggal_penempatan_guru', dateStr);
                                                            },
                                                            onOpen: function(selectedDates, dateStr, instance) {
                                                                setTimeout(() => {
                                                                    instance.calendarContainer.style.width = 'auto';
                                                                }, 10);
                                                            }
                                                        });
                                                    }
                                                }"
                                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm"
                                                placeholder="Pilih tanggal"
                                                value="{{ $tanggal_penempatan_guru }}"
                                            >
                                        </div>
                                        @error('tanggal_penempatan_guru') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-6 pt-6 pb-2 px-6 flex justify-end gap-3 shrink-0" style="border-top:1px solid #E5E7EB;">
                        <button wire:click="closeFormModal" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #9B3A3A; border: 1.5px solid #9B3A3A;">Batal</button>
                        <button wire:click="saveForm" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #1B5E4F; border: 1.5px solid #1B5E4F;">
                            <span wire:loading.remove wire:target="saveForm">Simpan Data</span>
                            <span wire:loading wire:target="saveForm">Menyimpan...</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL KONFIRMASI UBAH GURU PEMBIMBING --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showConfirmationDialog)
        <div class="fixed inset-0 z-[60] flex items-center justify-center overflow-y-auto pointer-events-none" style="background-color:rgba(26,36,34,0.6);">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">Konfirmasi Perubahan Guru</h3>
                    <p class="mt-2 text-sm" style="color:#6B7280;">Anda akan mengubah guru pembimbing kelompok ini. Silakan berikan alasan perubahan.</p>
                </div>
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">Alasan Perubahan <span style="color:#9B3A3A;">*</span></label>
                    <textarea wire:model="alasanPerubahanGuru" rows="3" class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2" style="background-color:#FFFFFF;border:1.5px solid #D1D5DB;color:#1A2422;focus:ring-color:#1B5E4F;" placeholder="Contoh: Guru pindah tugas"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" wire:click="cancelConfirmation" class="btn-modal-cancel flex-1">Batal</button>
                    <button type="button" wire:click="confirmUpdate" class="btn-modal-confirm flex-1">Konfirmasi</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL DETAIL KELOMPOK --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showDetailModal && $selectedKelompok)
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif;">
            <div class="fixed inset-0 pointer-events-none" wire:click="closeDetailModal"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-4xl w-full p-6 flex flex-col" style="border:1px solid #E5E7EB; max-height:90vh;">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                        <div>
                            <h3 class="text-xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">Detail Kelompok</h3>
                            <p class="mt-1 text-sm" style="color:#6B7280;">{{ $selectedKelompok->nama }}</p>
                        </div>
                        <button wire:click="closeDetailModal" class="p-2 rounded-lg hover:bg-gray-100" style="color:#6B7280;">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Scrollable Content --}}
                    <div class="overflow-y-auto flex-1 pr-1 space-y-6">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Nama Kelompok</span>
                                <span class="font-medium" style="color:#1A2422;">{{ $selectedKelompok->nama }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Jenis</span>
                                <span style="color:#1A2422;">{{ $selectedKelompok->jenis }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Tingkat</span>
                                <span style="color:#1A2422;">{{ $selectedKelompok->tingkat }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Guru Pembimbing</span>
                                <span style="color:#1A2422;">{{ $selectedKelompok->guruPembimbing?->nama ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Tahun Ajaran</span>
                                <span style="color:#1A2422;">{{ $selectedKelompok->tahunAjaran?->label ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Kapasitas</span>
                                <span style="color:#1A2422;">{{ $jumlahSiswaDetail }} / {{ $selectedKelompok->kapasitas_maks }} Siswa</span>
                            </div>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="mt-6 pt-4 flex justify-end shrink-0" style="border-top:1px solid #E5E7EB;">
                        <button wire:click="closeDetailModal" type="button" class="btn-modal-cancel">Tutup</button>
                    </div>

                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL ANGGOTA KELOMPOK --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showAnggotaModal && $kelompokAnggota)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-xl flex flex-col" style="font-family:'Inter',sans-serif; max-height:85vh;">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                    <div>
                        <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Anggota Kelompok
                        </h3>
                        <p class="text-sm mt-0.5" style="color:#A7D8CC;">
                            {{ $kelompokAnggota->nama }} &bull; {{ $kelompokAnggota->tingkat }} &bull; {{ $kelompokAnggota->jenis }}
                        </p>
                    </div>
                    <button wire:click="tutupAnggotaModal" class="text-white hover:text-green-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Info Bar --}}
                <div class="flex items-center gap-6 px-6 py-3 flex-shrink-0" style="background-color:#F0FDF9; border-bottom:1px solid #D1FAE5;">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" style="color:#1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-xs font-medium" style="color:#065F46;">Guru: {{ $kelompokAnggota->guruPembimbing?->nama ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" style="color:#1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-xs font-medium" style="color:#065F46;">Jumlah Anggota: <strong>{{ $kelompokAnggota->siswas->count() }} siswa</strong></span>
                    </div>
                </div>



                {{-- Search & Bulk Actions --}}
                <div class="px-6 pt-4 pb-2 flex-shrink-0 flex items-center justify-between gap-4">
                    <div class="relative flex-1">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9CA3AF;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"></path>
                        </svg>
                        <input
                            type="text"
                            wire:model.live="searchAnggota"
                            placeholder="Cari nama atau NIS siswa..."
                            class="w-full pl-9 pr-4 py-2 text-sm rounded-lg focus:outline-none focus:ring-2"
                            style="border:1px solid #D1D5DB;color:#1A2422;"
                        >
                    </div>

                    @if(count($selectedAnggotaIds) > 0)
                        <button
                            wire:click="konfirmasiKeluarkanBulk"
                            type="button"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                            style="background-color:#FEF2F2;color:#DC2626;border:1px solid #FECACA;"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Keluarkan ({{ count($selectedAnggotaIds) }})
                        </button>
                    @endif
                </div>

                {{-- Daftar Anggota --}}
                <div class="overflow-y-auto flex-1 px-6 pb-6">
                    @php
                        $anggotaList = $kelompokAnggota->siswas;
                        if ($searchAnggota) {
                            $s = strtolower($searchAnggota);
                            $anggotaList = $anggotaList->filter(fn($sw) =>
                                str_contains(strtolower($sw->nama), $s) ||
                                str_contains(strtolower($sw->nis ?? ''), $s)
                            );
                        }
                    @endphp

                    @if($anggotaList->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12">
                            <svg class="w-14 h-14 mb-3" style="color:#D1D5DB;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <p class="text-sm font-medium" style="color:#9CA3AF;">
                                @if($searchAnggota) Tidak ada siswa yang cocok @else Belum ada anggota di kelompok ini @endif
                            </p>
                        </div>
                    @else
                        <table class="min-w-full mt-2">
                            <thead>
                                <tr style="border-bottom:2px solid #E5E7EB;">
                                    <th class="py-2 pr-4 text-left" style="width: 40px;">
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectAllAnggota"
                                            class="w-4 h-4 rounded transition-colors cursor-pointer"
                                            style="color:#1B5E4F;border-color:#D1D5DB;"
                                        >
                                    </th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">No</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">Nama Siswa</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">NIS</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">Kelas</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">Tgl. Masuk</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="border-color:#F3F4F6;">
                                @foreach($anggotaList->values() as $i => $siswa)
                                    @php
                                        $riwayatSiswa    = $riwayatAnggota[$siswa->id] ?? ['tanggal_masuk'=>null];
                                        $tglMasuk        = $riwayatSiswa['tanggal_masuk']  ?? null;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-3 pr-4">
                                            <input
                                                type="checkbox"
                                                wire:model.live="selectedAnggotaIds"
                                                value="{{ $siswa->id }}"
                                                class="w-4 h-4 rounded transition-colors cursor-pointer"
                                                style="color:#1B5E4F;border-color:#D1D5DB;"
                                            >
                                        </td>
                                        <td class="py-3 pr-4 text-sm" style="color:#9CA3AF;">{{ $i + 1 }}</td>
                                        <td class="py-3 pr-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0" style="background-color:#E8F5F1;color:#1B5E4F;">
                                                    {{ substr($siswa->nama, 0, 1) }}
                                                </div>
                                                <span class="text-sm font-medium" style="color:#1A2422;">{{ $siswa->nama }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 pr-4 text-sm" style="color:#6B7280;">{{ $siswa->nis ?? '-' }}</td>
                                        <td class="py-3 pr-4 text-sm" style="color:#6B7280;">{{ $siswa->kelas?->nama_lengkap ?? '-' }}</td>
                                        <td class="py-3 pr-4">
                                            @if($tglMasuk)
                                                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-full"
                                                      style="background-color:#E8F5F1; color:#1B5E4F;">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ $tglMasuk }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Tidak tercatat</span>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full"
                                                  style="background-color:#DCFCE7; color:#15803D;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block animate-pulse"></span>
                                                Aktif
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="flex justify-end px-6 py-4 flex-shrink-0" style="border-top:1px solid #E5E7EB;background-color:#F9FAFB;border-radius:0 0 1rem 1rem;">
                    <button wire:click="tutupAnggotaModal" type="button" class="btn-modal-cancel">Tutup</button>
                </div>

            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL KONFIRMASI KELUARKAN (POPUP) --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showKonfirmasiKeluarkan && $kelompokAnggota)
        <div class="fixed inset-0 flex items-center justify-center p-4 z-[60] pointer-events-none" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-transparent" wire:click="batalKeluarkan"></div>

            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden" style="font-family:'Inter',sans-serif;">
                {{-- Header Modal --}}
                <div class="flex items-center justify-between px-6 py-4 bg-red-50 border-b border-red-100">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-red-900" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Konfirmasi Keluarkan
                        </h3>
                    </div>
                    <button wire:click="batalKeluarkan" type="button" class="text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Body Modal --}}
                <div class="px-6 py-5">
                    <p class="text-gray-800 text-base mb-2">
                        Keluarkan <strong>{{ count($selectedAnggotaIds) }} siswa</strong> yang dipilih dari kelompok ini?
                    </p>
                    <p class="text-sm text-gray-500">
                        Siswa akan dikembalikan ke status "Belum Ditempatkan" dan bisa ditempatkan ulang ke kelompok lain.
                    </p>
                </div>

                {{-- Footer Modal --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                    <button wire:click="batalKeluarkan" type="button" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="keluarkanAnggota" type="button" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 transition-colors shadow-sm">
                        <span wire:loading.remove wire:target="keluarkanAnggota">Ya, Keluarkan</span>
                        <span wire:loading wire:target="keluarkanAnggota">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL PENGATURAN KAPASITAS --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showPengaturanModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="absolute inset-0" wire:click="tutupPengaturanModal"></div>

            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl flex flex-col" style="font-family:'Inter',sans-serif;">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F; border-bottom:1px solid #047857;">
                    <div>
                        <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Pengaturan Kapasitas
                        </h3>
                        <p class="text-sm mt-0.5" style="color:#A7D8CC;">
                            Atur batas minimal dan maksimal jumlah siswa per kelompok
                        </p>
                    </div>
                    <button wire:click="tutupPengaturanModal" class="text-white hover:text-green-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6">
                    <form wire:submit.prevent="savePengaturanKelompok" class="space-y-5">

                        {{-- Kapasitas --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Min. Siswa</label>
                                <input type="number" wire:model="kapasitas_kelompok_min" class="block w-full rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:border-transparent" style="border:1.5px solid #D1D5DB; color:#1A2422;" min="1" required>
                                @error('kapasitas_kelompok_min') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Maks. Siswa</label>
                                <input type="number" wire:model="kapasitas_kelompok_max" class="block w-full rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:border-transparent" style="border:1.5px solid #D1D5DB; color:#1A2422;" min="1" required>
                                @error('kapasitas_kelompok_max') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex justify-end gap-3 pt-6 mt-2" style="border-top:1px solid #E5E7EB;">
                            <button wire:click="tutupPengaturanModal" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #9B3A3A; border: 1.5px solid #9B3A3A;">
                                Batal
                            </button>
                            <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #1B5E4F; border: 1.5px solid #1B5E4F;">
                                <span wire:loading.remove wire:target="savePengaturanKelompok">Simpan Pengaturan</span>
                                <span wire:loading wire:target="savePengaturanKelompok">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL TARGET HAFALAN --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showTargetHafalanModal)

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="absolute inset-0" wire:click="tutupTargetHafalanModal"></div>

            <div class="relative w-full max-w-5xl rounded-2xl bg-white shadow-xl flex flex-col" style="font-family:'Inter',sans-serif; max-height:90vh;">


                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                    <div>
                        <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Target Hafalan Per Tingkat
                        </h3>
                    </div>
                    <button wire:click="tutupTargetHafalanModal" class="text-white hover:text-green-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 overflow-y-auto flex-1">
                    @if($semuaJuz->isEmpty())
                        <div class="p-6 rounded-xl text-center" style="background-color:#FEF3C7;border:1.5px solid #F59E0B;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3" style="color:#F59E0B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <h4 class="font-semibold mb-2" style="color:#92400E;font-family:'Plus Jakarta Sans',sans-serif;">Data Referensi Al-Quran Kosong!</h4>
                            <p class="text-sm mb-4" style="color:#92400E;">Belum ada data referensi 30 Juz. Klik tombol di bawah untuk mengisi data secara otomatis.</p>
                            <button
                                wire:click="runJuzSeeder"
                                type="button"
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors"
                                style="background-color:#C9A227;color:#FFFFFF;border:1.5px solid #C9A227;"
                                onmouseover="this.style.backgroundColor='#B8911F'"
                                onmouseout="this.style.backgroundColor='#C9A227'">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Isi Data 30 Juz Sekarang
                            </button>
                        </div>
                    @else
                        <form wire:submit.prevent="saveTargetHafalan">
                            @php
                                // Hitung total ayat per tingkat berdasarkan juz yang dipilih
                                $juzMap = $semuaJuz->keyBy('juz_ke');
                                $totalAyatDasar   = collect($target_dasar)->sum(fn($j) => $juzMap[$j]?->jumlah_ayat ?? 0);
                                $totalAyatMenengah = collect($target_menengah)->sum(fn($j) => $juzMap[$j]?->jumlah_ayat ?? 0);
                                $totalAyatAtas    = collect($target_atas)->sum(fn($j) => $juzMap[$j]?->jumlah_ayat ?? 0);
                                $totalJuzSemua    = count($target_dasar) + count($target_menengah) + count($target_atas);
                                $totalAyatSemua   = $totalAyatDasar + $totalAyatMenengah + $totalAyatAtas;
                            @endphp

                            {{-- Summary Bar --}}
                            <div class="flex items-center gap-6 px-5 py-4 rounded-xl mb-6" style="background-color:#F4F5F4; border:1.5px solid #D1D5DB;">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" style="color:#1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    <span class="text-sm font-bold" style="color:#1A2422;">Total Target: <span style="color:#1B5E4F;">{{ $totalJuzSemua }} Juz</span></span>
                                </div>
                                <div class="w-px h-5" style="background-color:#D1D5DB;"></div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" style="color:#1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <span class="text-sm font-bold" style="color:#1A2422;">Total Ayat: <span style="color:#1B5E4F;">{{ number_format($totalAyatSemua) }} ayat</span></span>
                                </div>
                                <div class="w-px h-5" style="background-color:#D1D5DB;"></div>
                                <span class="text-sm font-bold" style="color:#6B7280;">dari 30 Juz (6.236 ayat)</span>
                                <div class="w-px h-5" style="background-color:#D1D5DB;"></div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" style="color:#1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-sm font-bold" style="color:#1A2422;"><span style="color:#1B5E4F;">{{ $totalJuzSemua }} Juz</span> dalam 3 Tahun</span>
                                </div>
                            </div>



                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

                                {{-- Tingkat Dasar --}}
                                <div class="rounded-xl border-2 overflow-hidden flex flex-col shadow-sm" style="background-color:#FFFFFF; border-color:#1B5E4F;">
                                    <div class="px-5 py-4" style="background: linear-gradient(135deg, #1B5E4F 0%, #2D7A66 100%);">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-bold text-base text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">📘 Tingkat Dasar</h4>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            @if(count($target_dasar) > 0)
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background-color:rgba(255,255,255,0.25); color:#FFFFFF;">{{ count($target_dasar) }} Juz</span>
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background-color:#FFFFFF; color:#1B5E4F;">{{ number_format($totalAyatDasar) }} ayat</span>
                                                </div>
                                            @else
                                                <span class="text-xs inline-block py-0.5" style="color:rgba(255,255,255,0.7);">Belum diatur</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="p-5 flex flex-col gap-3 flex-1" style="background-color:#F4F5F4;">
                                        <select wire:model.live="selected_juz_dasar" class="block w-full rounded-lg border-2 bg-white py-2.5 px-3 text-sm focus:outline-none focus:ring-2 transition-all" style="border-color:#1B5E4F; focus:ring-color:#1B5E4F;">
                                            <option value="">— Pilih Juz —</option>
                                            @foreach($semuaJuz as $juz)
                                                @php $isDisabled = in_array((int)$juz->juz_ke, array_merge($target_dasar, $target_menengah, $target_atas)); @endphp
                                                <option value="{{ $juz->juz_ke }}" @if($isDisabled) disabled @endif>Juz {{ $juz->juz_ke }}{{ $isDisabled ? ' ✓' : '' }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" wire:click="addJuz('Dasar')" @if(empty($selected_juz_dasar)) disabled @endif class="w-full py-2.5 text-sm font-bold rounded-lg text-white transition-all @if(empty($selected_juz_dasar)) opacity-50 cursor-not-allowed @else hover:opacity-90 @endif" style="background-color:#1B5E4F;">
                                            + Tambah Juz
                                        </button>
                                        <div class="bg-white rounded-lg border-2 p-3 min-h-[80px]" style="border-color:#D1D5DB;">
                                            @if(count($target_dasar) > 0)
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($target_dasar as $juzItem)
                                                        <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold" style="background-color:#1B5E4F; color:#FFFFFF;">
                                                            Juz {{ $juzItem }}
                                                            <button type="button" wire:click="removeJuz('Dasar', {{ $juzItem }})" class="ml-0.5 hover:text-red-400 font-bold text-base leading-none transition-colors">&times;</button>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-center text-xs py-4" style="color:#9CA3AF;">Belum ada Juz dipilih</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Tingkat Menengah --}}
                                <div class="rounded-xl border-2 overflow-hidden flex flex-col shadow-sm" style="background-color:#FFFFFF; border-color:#5B7A8C;">
                                    <div class="px-5 py-4" style="background: linear-gradient(135deg, #5B7A8C 0%, #6B8A9C 100%);">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-bold text-base text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">📗 Tingkat Menengah</h4>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            @if(count($target_menengah) > 0)
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background-color:rgba(255,255,255,0.25); color:#FFFFFF;">{{ count($target_menengah) }} Juz</span>
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background-color:#FFFFFF; color:#5B7A8C;">{{ number_format($totalAyatMenengah) }} ayat</span>
                                                </div>
                                            @else
                                                <span class="text-xs inline-block py-0.5" style="color:rgba(255,255,255,0.7);">Belum diatur</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="p-5 flex flex-col gap-3 flex-1" style="background-color:#F4F5F4;">
                                        <select wire:model.live="selected_juz_menengah" class="block w-full rounded-lg border-2 bg-white py-2.5 px-3 text-sm focus:outline-none focus:ring-2 transition-all" style="border-color:#5B7A8C; focus:ring-color:#5B7A8C;">
                                            <option value="">— Pilih Juz —</option>
                                            @foreach($semuaJuz as $juz)
                                                @php $isDisabled = in_array((int)$juz->juz_ke, array_merge($target_dasar, $target_menengah, $target_atas)); @endphp
                                                <option value="{{ $juz->juz_ke }}" @if($isDisabled) disabled @endif>Juz {{ $juz->juz_ke }}{{ $isDisabled ? ' ✓' : '' }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" wire:click="addJuz('Menengah')" @if(empty($selected_juz_menengah)) disabled @endif class="w-full py-2.5 text-sm font-bold rounded-lg text-white transition-all @if(empty($selected_juz_menengah)) opacity-50 cursor-not-allowed @else hover:opacity-90 @endif" style="background-color:#5B7A8C;">
                                            + Tambah Juz
                                        </button>
                                        <div class="bg-white rounded-lg border-2 p-3 min-h-[80px]" style="border-color:#D1D5DB;">
                                            @if(count($target_menengah) > 0)
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($target_menengah as $juzItem)
                                                        <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold" style="background-color:#5B7A8C; color:#FFFFFF;">
                                                            Juz {{ $juzItem }}
                                                            <button type="button" wire:click="removeJuz('Menengah', {{ $juzItem }})" class="ml-0.5 hover:text-red-400 font-bold text-base leading-none transition-colors">&times;</button>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-center text-xs py-4" style="color:#9CA3AF;">Belum ada Juz dipilih</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Tingkat Atas --}}
                                <div class="rounded-xl border-2 overflow-hidden flex flex-col shadow-sm" style="background-color:#FFFFFF; border-color:#C9A227;">
                                    <div class="px-5 py-4" style="background: linear-gradient(135deg, #C9A227 0%, #D4B03C 100%);">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-bold text-base text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">📙 Tingkat Atas</h4>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            @if(count($target_atas) > 0)
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background-color:rgba(255,255,255,0.25); color:#FFFFFF;">{{ count($target_atas) }} Juz</span>
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background-color:#FFFFFF; color:#C9A227;">{{ number_format($totalAyatAtas) }} ayat</span>
                                                </div>
                                            @else
                                                <span class="text-xs inline-block py-0.5" style="color:rgba(255,255,255,0.7);">Belum diatur</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="p-5 flex flex-col gap-3 flex-1" style="background-color:#F4F5F4;">
                                        <select wire:model.live="selected_juz_atas" class="block w-full rounded-lg border-2 bg-white py-2.5 px-3 text-sm focus:outline-none focus:ring-2 transition-all" style="border-color:#C9A227; focus:ring-color:#C9A227;">
                                            <option value="">— Pilih Juz —</option>
                                            @foreach($semuaJuz as $juz)
                                                @php $isDisabled = in_array((int)$juz->juz_ke, array_merge($target_dasar, $target_menengah, $target_atas)); @endphp
                                                <option value="{{ $juz->juz_ke }}" @if($isDisabled) disabled @endif>Juz {{ $juz->juz_ke }}{{ $isDisabled ? ' ✓' : '' }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" wire:click="addJuz('Atas')" @if(empty($selected_juz_atas)) disabled @endif class="w-full py-2.5 text-sm font-bold rounded-lg text-white transition-all @if(empty($selected_juz_atas)) opacity-50 cursor-not-allowed @else hover:opacity-90 @endif" style="background-color:#C9A227;">
                                            + Tambah Juz
                                        </button>
                                        <div class="bg-white rounded-lg border-2 p-3 min-h-[80px]" style="border-color:#D1D5DB;">
                                            @if(count($target_atas) > 0)
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($target_atas as $juzItem)
                                                        <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold" style="background-color:#C9A227; color:#FFFFFF;">
                                                            Juz {{ $juzItem }}
                                                            <button type="button" wire:click="removeJuz('Atas', {{ $juzItem }})" class="ml-0.5 hover:text-red-400 font-bold text-base leading-none transition-colors">&times;</button>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-center text-xs py-4" style="color:#9CA3AF;">Belum ada Juz dipilih</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>

                             {{-- Footer --}}
                            <div class="flex justify-between items-center gap-3 pt-4" style="border-top:1px solid #E5E7EB;">
                                <button wire:click="runJuzSeeder" type="button" class="px-4 py-2 text-xs font-medium rounded-lg text-white hover:opacity-90" style="background-color:#6B7280;">
                                    <span wire:loading.remove wire:target="runJuzSeeder">Sinkronisasi Database Al-Quran</span>
                                    <span wire:loading wire:target="runJuzSeeder">Memproses...</span>
                                </button>
                                <div class="flex gap-3">
                                    <button wire:click="tutupTargetHafalanModal" type="button" class="px-5 py-2.5 text-sm font-medium rounded-lg" style="background-color:#FFFFFF;color:#374151;border:1.5px solid #D1D5DB;">Batal</button>
                                    <button type="submit" class="px-5 py-2.5 text-sm font-medium rounded-lg text-white" style="background-color:#1B5E4F;">
                                        <span wire:loading.remove wire:target="saveTargetHafalan">Simpan Target Hafalan</span>
                                        <span wire:loading wire:target="saveTargetHafalan">Menyimpan...</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>

            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL KOMPONEN PENILAIAN --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showKomponenPenilaianModal)
        @php
            $komponenList = [
                'skor_tajwid'     => ['id' => 'cb_tajwid',     'label' => 'Tajwid',     'desc' => 'Ketepatan hukum bacaan & panjang-pendek mad'],
                'skor_kelancaran' => ['id' => 'cb_kelancaran', 'label' => 'Kelancaran', 'desc' => 'Hafalan tanpa terbata-bata atau berhenti lama'],
                'skor_makhraj'    => ['id' => 'cb_makhraj',    'label' => 'Makhraj',    'desc' => 'Ketepatan pelafalan Makharijul Huruf'],
                'skor_fashahah'   => ['id' => 'cb_fashahah',   'label' => 'Fashahah',   'desc' => 'Keindahan nada & kesempurnaan harakat'],
            ];
            $savedSelected = is_array($komponen_penilaian) ? $komponen_penilaian : [];
        @endphp

        <style>
            .kp-label { display:flex; align-items:center; gap:1rem; padding:1rem; border-radius:0.75rem; cursor:pointer; user-select:none; transition:all .15s; border:1.5px solid #D1D5DB; background:#fff; }
            .kp-label:has(.kp-cb:checked) { border-color:#1B5E4F; background:#F0FDF4; }
            .kp-track { flex-shrink:0; width:2.75rem; height:1.5rem; border-radius:9999px; background:#D1D5DB; display:flex; align-items:center; padding:0 2px; transition:background .15s; }
            .kp-label:has(.kp-cb:checked) .kp-track { background:#1B5E4F; }
            .kp-thumb { width:1.25rem; height:1.25rem; border-radius:9999px; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,0.2); transform:translateX(0); transition:transform .15s; }
            .kp-label:has(.kp-cb:checked) .kp-thumb { transform:translateX(1.25rem); }
            .kp-name { font-size:.875rem; font-weight:600; color:#4B5563; }
            .kp-label:has(.kp-cb:checked) .kp-name { color:#111827; }
            .kp-badge { display:none; font-size:.75rem; font-weight:600; padding:.125rem .5rem; border-radius:.375rem; background:#D1FAE5; color:#065F46; flex-shrink:0; }
            .kp-label:has(.kp-cb:checked) .kp-badge { display:inline-flex; }
        </style>

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="absolute inset-0" wire:click="tutupKomponenPenilaianModal"></div>

            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl" style="font-family:'Inter',sans-serif;">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl" style="background-color:#1B5E4F; border-bottom:1px solid #047857;">
                    <div>
                        <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">Komponen Penilaian</h3>
                        <p class="text-sm mt-0.5" style="color:#A7D8CC;">Pilih aspek penilaian untuk setoran santri</p>
                    </div>
                    <button wire:click="tutupKomponenPenilaianModal" type="button" class="text-white hover:text-green-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Body --}}
                <form wire:submit.prevent="saveKomponenPenilaian" class="p-6">
                    <div class="space-y-3">
                        @foreach($komponenList as $key => $item)
                            <label class="kp-label" for="{{ $item['id'] }}">
                                <input class="kp-cb sr-only"
                                       type="checkbox"
                                       id="{{ $item['id'] }}"
                                       value="{{ $key }}"
                                       @checked(in_array($key, $savedSelected))>
                                <div class="kp-track"><div class="kp-thumb"></div></div>
                                <div class="flex-1">
                                    <span class="kp-name block">{{ $item['label'] }}</span>
                                    <span class="block text-xs mt-0.5" style="color:#9CA3AF;">{{ $item['desc'] }}</span>
                                </div>
                                <span class="kp-badge">Aktif</span>
                            </label>
                        @endforeach
                    </div>

                    @error('komponen_penilaian')
                        <p class="text-xs text-red-500 mt-3">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end gap-3 mt-6 pt-4" style="border-top:1px solid #E5E7EB;">
                        <button wire:click="tutupKomponenPenilaianModal" type="button"
                                class="px-5 py-2.5 text-sm font-medium rounded-lg"
                                style="background:#fff;color:#374151;border:1.5px solid #D1D5DB;">
                            Batal
                        </button>
                        <button type="button"
                                @click="
                                    const checked = [...document.querySelectorAll('.kp-cb:checked')].map(cb => cb.value);
                                    if (checked.length < 1) { alert('Pilih minimal satu komponen penilaian.'); return; }
                                    $wire.saveKomponenPenilaian(checked);
                                "
                                class="px-5 py-2.5 text-sm font-medium rounded-lg text-white"
                                style="background-color:#1B5E4F;">
                            <span wire:loading.remove wire:target="saveKomponenPenilaian">Simpan Komponen</span>
                            <span wire:loading wire:target="saveKomponenPenilaian">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @include('livewire.admin.master-data.kelompok.modal-tempatkan-siswa')

</div>



