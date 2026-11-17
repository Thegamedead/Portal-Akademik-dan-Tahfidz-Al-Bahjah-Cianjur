{{--
    View: livewire/admin/master-data/kelas/index-kelas.blade.php
    Komponen Livewire: App\Livewire\Admin\MasterData\Kelas\IndexKelas

    Redesigned UI dengan layout yang sama seperti Siswa
--}}
<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- PAGE HEADER --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Data Kelas Akademik
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Kelola data kelas akademik dan wali kelas
        </p>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- SINGLE ROW HEADER: Action Buttons LEFT + Search/Filter RIGHT --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        {{-- Single Row: Flex Layout --}}
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

            {{-- LEFT: Action Buttons in Horizontal Row --}}
            <div class="flex flex-wrap items-center gap-3">
                {{-- Tambah Kelas --}}
                <button
                    wire:click="openModal"
                    type="button"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2"
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
                        wire:click="openModal({{ $selectedIds[0] }})"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
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
                        title="Pilih tepat 1 kelas untuk edit"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                @endif

                {{-- Hapus Button (enabled when items selected, shows count) --}}
                @if(count($selectedIds) > 0)
                    <button
                        wire:click="openBulkDeleteConfirm"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                        style="background-color:#9B3A3A;color:#FFFFFF;border:1.5px solid #9B3A3A;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus ({{ count($selectedIds) }})
                    </button>
                @else
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih minimal 1 kelas untuk hapus"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                @endif

                {{-- Detail Button (enabled only when 1 item selected) --}}
                @if(count($selectedIds) === 1)
                    <button
                        wire:click="bukaAnggotaModal({{ $selectedIds[0] }})"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                        style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Anggota Kelas
                    </button>
                @else
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#FFFFFF;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih tepat 1 kelas untuk lihat anggota"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Anggota Kelas
                    </button>
                @endif

                {{-- Luluskan Kelas 9 Button --}}
                <button
                    wire:click="bukaModalLuluskanKelas9"
                    type="button"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                    style="background-color:#4F46E5;color:#FFFFFF;border:1.5px solid #4F46E5;"
                    title="Luluskan semua siswa aktif yang berada di kelas 9"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v7"></path>
                    </svg>
                    Luluskan Kelas 9
                </button>
            </div>

            {{-- RIGHT: Search Field + Filter Dropdown Button --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-[400px]">
                {{-- Search Input --}}
                <div class="flex-1">
                    <x-ui.search-field
                        wire-model="search"
                        placeholder="Cari nama kelas atau wali kelas..."
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
                        @if($filterTingkat)
                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                1
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
                                    Filter Kelas
                                </h3>
                                @if($filterTingkat)
                                    <button
                                        wire:click="$set('filterTingkat', '')"
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
                                    <option value="7">Tingkat 7</option>
                                    <option value="8">Tingkat 8</option>
                                    <option value="9">Tingkat 9</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TABLE SECTION --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="background-color:#1B5E4F;" class="text-white">
                        {{-- Checkbox Column --}}
                        <th class="px-6 py-4 text-center" style="width:60px;">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                class="w-4 h-4 rounded transition-colors cursor-pointer"
                                style="color:#1B5E4F;border-color:#D1D5DB;"
                            >
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold">Tingkat</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold">Nama Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold">Ruang Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold">Jumlah Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold">Wali Kelas</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    @forelse($kelas as $k)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Checkbox --}}
                            <td class="px-6 py-4 text-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedIds"
                                    value="{{ $k->id }}"
                                    class="w-4 h-4 rounded transition-colors cursor-pointer"
                                    style="color:#1B5E4F;border-color:#D1D5DB;"
                                >
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold" style="background-color:#E8F5F1;color:#1B5E4F;">
                                    {{ $k->tingkat }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold" style="color:#1A2422;">
                                {{ $k->nama_kelas }}
                            </td>
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                {{ $k->ruang_kelas ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold" style="background-color:#E8F5F1;color:#1B5E4F;border:1px solid #C4E5DC;" title="Siswa Aktif">
                                        {{ $k->siswas_aktif_count ?? 0 }} Aktif
                                    </span>
                                    @if(($k->siswas_non_aktif_count ?? 0) > 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium" style="background-color:#F3F4F6;color:#6B7280;border:1px solid #E5E7EB;" title="Siswa Lulus / Non-Aktif">
                                            {{ $k->siswas_non_aktif_count }} Non-Aktif
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                {{ $k->nama_wali_kelas }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        @if($search || $filterTingkat)
                                            Tidak ada kelas yang sesuai dengan filter
                                        @else
                                            Belum ada data kelas
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
        @if($kelas->hasPages())
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                {{ $kelas->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL BULK DELETE CONFIRMATION --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showBulkDeleteConfirm)
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif; pointer-events: none;">
            <div class="flex min-h-full items-center justify-center p-4" style="pointer-events: auto;">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6" style="border:1px solid #E5E7EB;">
                    {{-- Icon --}}
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full" style="background-color:#FEE2E2;">
                        <svg class="w-6 h-6" style="color:#9B3A3A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>

                    {{-- Title --}}
                    <h3 class="text-lg font-semibold text-center mb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                        Konfirmasi Hapus Massal
                    </h3>

                    {{-- Message --}}
                    <div class="text-sm mb-6" style="color:#6B7280;">
                        <p class="text-center mb-3">
                            Apakah Anda yakin ingin menghapus <strong style="color:#9B3A3A;">{{ count($selectedIds) }} kelas</strong> yang dipilih?
                        </p>

                        <div class="mt-3 p-3 rounded-lg" style="background-color:#FEF3C7;border-left:3px solid #C9A227;">
                            <p class="font-medium mb-2" style="color:#1A2422;font-size:0.8125rem;">
                                Peringatan:
                            </p>
                            <ul class="space-y-1 ml-4 text-xs" style="color:#4B5563;list-style-type:disc;">
                                <li>Semua data siswa di kelas ini akan kehilangan referensi kelas</li>
                                <li>Data tidak dapat dikembalikan setelah dihapus</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3">
                        <button
                            wire:click="closeBulkDeleteConfirm"
                            type="button"
                            class="btn-modal-cancel flex-1"
                        >
                            Batal
                        </button>
                        <button
                            wire:click="bulkDelete"
                            type="button"
                            class="btn-modal-confirm flex-1"
                        >
                            Hapus Semua
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL ANGGOTA KELAS --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showAnggotaModal && $kelasAnggota)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none" role="dialog" aria-modal="true" style="pointer-events: none;">
            <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-xl flex flex-col" style="font-family:'Inter',sans-serif; max-height:85vh; pointer-events: auto;">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                    <div>
                        <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Anggota Kelas
                        </h3>
                        <p class="text-sm mt-0.5" style="color:#A7D8CC;">
                            Kelas {{ $kelasAnggota->nama_kelas }} &bull; Tingkat {{ $kelasAnggota->tingkat }}
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
                        <span class="text-xs font-medium" style="color:#065F46;">Wali Kelas: {{ $kelasAnggota->nama_wali_kelas ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" style="color:#1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-xs font-medium" style="color:#065F46;">Jumlah Siswa: <strong>{{ $kelasAnggota->siswas->count() }} siswa</strong></span>
                    </div>
                </div>

                {{-- Search --}}
                <div class="px-6 pt-4 pb-2 flex-shrink-0">
                    <div class="relative">
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
                </div>

                {{-- Daftar Anggota --}}
                <div class="overflow-y-auto flex-1 px-6 pb-6">
                    @php
                        $anggotaList = $kelasAnggota->siswas;
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
                                @if($searchAnggota) Tidak ada siswa yang cocok @else Belum ada siswa di kelas ini @endif
                            </p>
                        </div>
                    @else
                        <table class="min-w-full mt-2">
                            <thead>
                                <tr style="border-bottom:2px solid #E5E7EB;">
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">No</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">Nama Siswa</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">NIS</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">JK</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="border-color:#F3F4F6;">
                                @foreach($anggotaList->values() as $i => $siswa)
                                    <tr class="hover:bg-gray-50 transition-colors">
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
                                        <td class="py-3 pr-4 text-sm" style="color:#6B7280;">{{ $siswa->jenis_kelamin === 'Laki-laki' ? 'L' : ($siswa->jenis_kelamin === 'Perempuan' ? 'P' : '-') }}</td>
                                        <td class="py-3 pr-4 text-xs">
                                            @if($siswa->status_aktif)
                                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-700">
                                                    {{ $siswa->status_akademik ?? 'Non-Aktif' }}
                                                </span>
                                            @endif
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
    {{-- MODAL KONFIRMASI LULUSKAN KELAS 9 --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showModalLuluskanKelas9)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none" role="dialog" aria-modal="true" style="pointer-events: none;">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 pointer-events-auto" style="font-family:'Inter',sans-serif; border:1px solid #E5E7EB; pointer-events: auto; max-height: 90vh; overflow-y: auto;">
                {{-- Header --}}
                <div class="flex items-center gap-3 pb-4 mb-4" style="border-bottom: 1px solid #E5E7EB;">
                    <div class="flex items-center justify-center w-11 h-11 rounded-xl flex-shrink-0" style="background-color:#EEF2FF;">
                        <svg class="w-6 h-6" style="color:#4F46E5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v7"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                            Kelulusan Siswa Kelas 9 (Lulus SMP)
                        </h3>
                        <p class="text-xs text-gray-500">
                            Proses kelulusan akhir jenjang untuk seluruh siswa aktif kelas 9
                        </p>
                    </div>
                </div>

                {{-- Body Form --}}
                <div class="space-y-4 text-sm mb-6">
                    {{-- Info Alert --}}
                    <div class="p-3.5 rounded-xl text-xs space-y-1.5" style="background-color:#F8FAFC;border:1px solid #E2E8F0;">
                        <div class="flex items-center justify-between font-semibold text-gray-800">
                            <span>Siswa Kelas 9 Ditemukan:</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold" style="background-color:#EEF2FF;color:#4F46E5;">
                                {{ $totalSiswaKelas9 }} Siswa Aktif
                            </span>
                        </div>
                        <p class="text-gray-500">
                            Seluruh siswa yang terdaftar di kelas tingkat 9 akan diubah status akademiknya menjadi <strong>Lulus SMP</strong> dan dipindahkan ke daftar <strong>Siswa Non-Aktif</strong>.
                        </p>
                    </div>

                    {{-- Form: Tanggal Kelulusan --}}
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color:#1A2422;">
                            Tanggal Kelulusan <span style="color:#DC2626;">*</span>
                        </label>
                        <input
                            type="date"
                            wire:model="tanggalLulus"
                            class="w-full px-3.5 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all block"
                            style="border:1.5px solid #D1D5DB !important;color:#1A2422;background-color:#FFFFFF;"
                        >
                        @error('tanggalLulus')
                            <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Form: Keterangan / Catatan Kelulusan --}}
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color:#1A2422;">
                            Keterangan / Catatan Keluar
                        </label>
                        <input
                            type="text"
                            wire:model="keteranganLulus"
                            placeholder="Contoh: Lulusan Kelas 9 (Lulus SMP) Tahun Ajaran 2025/2026"
                            class="w-full px-3.5 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all block"
                            style="border:1.5px solid #D1D5DB !important;color:#1A2422;background-color:#FFFFFF;"
                        >
                    </div>

                    {{-- Daftar Siswa Preview --}}
                    @if(!empty($previewSiswaKelas9))
                        <div>
                            <label class="block text-xs font-semibold mb-1 text-gray-700">
                                Daftar Siswa yang Akan Diluluskan:
                            </label>
                            <div class="max-h-40 overflow-y-auto rounded-lg divide-y divide-gray-100" style="border: 1px solid #E5E7EB;">
                                @foreach($previewSiswaKelas9 as $item)
                                    <div class="px-3 py-2 flex items-center justify-between text-xs hover:bg-gray-50">
                                        <div>
                                            <span class="font-semibold text-gray-800">{{ $item['nama'] }}</span>
                                            <span class="text-gray-400 ml-1">({{ $item['nis'] }})</span>
                                            <span class="ml-2 px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-700">
                                                Kelas {{ $item['kelas'] }}
                                            </span>
                                        </div>
                                        <div>
                                            @if($item['status_tahfidz'] === 'Lulus Program Tahfidz')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                                    🎓 Tahfidz Lulus
                                                </span>
                                            @else
                                                <span class="text-[10px] text-gray-500">
                                                    {{ $item['total_juz'] }} Juz
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 pt-2" style="border-top:1px solid #F3F4F6;">
                    <button
                        wire:click="tutupModalLuluskanKelas9"
                        type="button"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none flex-1"
                        style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                    >
                        Batal
                    </button>
                    <button
                        wire:click="prosesLuluskanKelas9"
                        type="button"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none flex-1 {{ $totalSiswaKelas9 === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                        style="background-color:#4F46E5;border:1.5px solid #4F46E5;"
                        @if($totalSiswaKelas9 === 0) disabled @endif
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="prosesLuluskanKelas9">
                            Luluskan
                        </span>
                        <span wire:loading wire:target="prosesLuluskanKelas9">
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal CRUD -->
    @if($showModal)
        @include('livewire.admin.master-data.kelas.partials.modal-kelas')
    @endif
</div>
