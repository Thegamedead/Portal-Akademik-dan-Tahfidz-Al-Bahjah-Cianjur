{{--
    View: livewire/admin/master-data/guru/index-guru.blade.php
    Komponen Livewire: App\Livewire\Admin\MasterData\Guru\IndexGuru

    Daftar guru dengan fitur pencarian dan filter (Requirements 4.8, 4.9)
--}}
<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- PAGE HEADER --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Data Guru
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Kelola data guru, guru pembimbing tahfidz, dan tugas tambahan
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
                {{-- Tambah Guru --}}
                <button
                    wire:click="openCreateModal"
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
                        wire:click="openEditModal({{ $selectedIds[0] }})"
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
                        title="Pilih tepat 1 guru untuk edit"
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
                        Hapus
                    </button>
                @else
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih minimal 1 guru untuk hapus"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                @endif

                @auth('admin')
                    @if(count($selectedIds) === 1)
                        <button
                            type="button"
                            wire:click="bukaModalRegistrasiKeluar"
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                            style="background-color:#9B3A3A;color:#FFFFFF;border:1.5px solid #9B3A3A;"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Registrasi Keluar
                        </button>
                    @else
                        <button
                            disabled
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                            style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                            title="Pilih tepat 1 guru untuk registrasi keluar"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Registrasi Keluar
                        </button>
                    @endif
                @endauth

                {{-- Info Label --}}
                <div class="flex items-center sm:ml-2 sm:pl-4 sm:border-l border-gray-200">
                    <span class="text-sm text-gray-500">
                        Total <strong style="color:#1A2422;">{{ $gurus->total() }}</strong> guru
                        @if(count($selectedIds) > 0)
                            <span class="mx-1.5">&bull;</span>
                            <strong style="color:#1B5E4F;">{{ count($selectedIds) }}</strong> terpilih
                        @endif
                    </span>
                </div>

            </div>

            {{-- RIGHT: Search Field + Filter Dropdown Button --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-100">
                {{-- Search Input --}}
                <div class="flex-1">
                    <x-ui.search-field
                        wireModel="search"
                        placeholder="Cari guru (NIY, Nama)..."
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
                        @if($filterJabatan || $filterJenisKelamin)
                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                {{ collect([$filterJabatan, $filterJenisKelamin])->filter()->count() }}
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
                            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl shrink-0" style="background-color:#1B5E4F;">
                                <h3 class="text-sm font-semibold" style="color:white;font-family:'Plus Jakarta Sans',sans-serif;">
                                    Filter Guru
                                </h3>
                                @if($filterJabatan || $filterJenisKelamin)
                                    <button
                                        wire:click="resetFilters"
                                        type="button"
                                        class="text-xs font-medium text-gray-500 hover:text-[#1B5E4F] transition-colors"
                                    >
                                        Reset
                                    </button>
                                @endif
                            </div>

                            {{-- Filter Jabatan --}}
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Jabatan
                                </label>
                                <select
                                    wire:model.live="filterJabatan"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    <option value="Guru Pembimbing Tahfidz">Pembimbing Tahfidz</option>
                                    <option value="Guru Mapel">Guru Mapel</option>
                                    <option value="Kepala Sekolah">Kepala Sekolah</option>
                                    <option value="Tendik">Tendik</option>
                                </select>
                            </div>

                            {{-- Filter Jenis Kelamin --}}
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Jenis Kelamin
                                </label>
                                <select
                                    wire:model.live="filterJenisKelamin"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    <option value="Laki-laki">L</option>
                                    <option value="Perempuan">P</option>
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
                    <tr style="background-color:#1B5E4F;color:white;">
                        {{-- Checkbox Column --}}
                        <th class="px-6 py-4 text-center" style="width:60px;">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                class="w-4 h-4 rounded transition-colors cursor-pointer"
                                style="color:#1B5E4F;border-color:#D1D5DB;"
                            >
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">NIY</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">JK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tempat Lahir</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tanggal Lahir</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Jabatan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">TMT</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Nama Kelompok</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tingkat Kelompok</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tanggal Penempatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    @forelse($gurus as $guru)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Checkbox --}}
                            <td class="px-6 py-4 text-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedIds"
                                    value="{{ $guru->id }}"
                                    class="w-4 h-4 rounded transition-colors cursor-pointer"
                                    style="color:#1B5E4F;border-color:#D1D5DB;"
                                >
                            </td>

                            {{-- NIY --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                {{ $guru->niy }}
                            </td>

                            {{-- Nama --}}
                            <td class="px-6 py-4 text-sm font-medium" style="color:#1A2422;">
                                {{ $guru->nama }}
                            </td>

                            {{-- JK (Circular Badge) --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold"
                                    style="background-color:{{ $guru->jenis_kelamin === 'Laki-laki' ? '#E0F2FE' : '#FCE7F3' }};color:{{ $guru->jenis_kelamin === 'Laki-laki' ? '#0C4A6E' : '#831843' }};"
                                >
                                    {{ $guru->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}
                                </span>
                            </td>

                            {{-- Tempat Lahir --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                {{ $guru->tempat_lahir ?? '-' }}
                            </td>

                            {{-- Tanggal Lahir --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                {{ $guru->tanggal_lahir ? \Carbon\Carbon::parse($guru->tanggal_lahir)->tgl() : '-' }}
                            </td>

                            {{-- Jabatan --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                @php
                                    $jabatanColor = match($guru->jabatan) {
                                        'Guru Pembimbing Tahfidz' => ['bg' => '#E8F5F1', 'text' => '#1B5E4F'],
                                        'Kepala Sekolah' => ['bg' => '#FEF3C7', 'text' => '#C9A227'],
                                        default => ['bg' => '#E8EEF2', 'text' => '#5B7A8C'],
                                    };
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                    style="background-color:{{ $jabatanColor['bg'] }};color:{{ $jabatanColor['text'] }};"
                                >
                                    {{ $guru->jabatan }}
                                </span>
                            </td>

                            {{-- TMT (Tanda Mulai Tugas) --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                {{ $guru->tanggal_mulai_kerja ? \Carbon\Carbon::parse($guru->tanggal_mulai_kerja)->tgl() : '-' }}
                            </td>

                            {{-- Nama Kelompok --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                @if($guru->jabatan === 'Guru Pembimbing Tahfidz')
                                    @if($guru->kelompoksBimbingan->count() > 0)
                                        <div class="flex flex-col gap-1">
                                            @foreach($guru->kelompoksBimbingan as $kel)
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" style="background-color:#E8F5F1;color:#1B5E4F;border:1px solid #D1EAE3;width:max-content;">
                                                    {{ $kel->nama }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs italic" style="color:#9CA3AF;">Belum ditempatkan</span>
                                    @endif
                                @else
                                    <span class="text-xs" style="color:#D1D5DB;">-</span>
                                @endif
                            </td>

                            {{-- Tingkat Kelompok --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                @if($guru->jabatan === 'Guru Pembimbing Tahfidz')
                                    @if($guru->kelompoksBimbingan->count() > 0)
                                        <div class="flex flex-col gap-1">
                                            @foreach($guru->kelompoksBimbingan as $kel)
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" style="background-color:#F4F5F4;color:#5B7A8C;border:1px solid #E5E7EB;width:max-content;">
                                                    {{ $kel->tingkat }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs italic" style="color:#9CA3AF;">-</span>
                                    @endif
                                @else
                                    <span class="text-xs" style="color:#D1D5DB;">-</span>
                                @endif
                            </td>

                            {{-- Tanggal Penempatan --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                @if($guru->jabatan === 'Guru Pembimbing Tahfidz')
                                    @if($guru->kelompoksBimbingan->count() > 0)
                                        <div class="flex flex-col gap-1">
                                            @foreach($guru->kelompoksBimbingan as $kel)
                                                @if($kel->tanggal_penempatan_guru)
                                                    <span class="text-xs" style="color:#1A2422;">
                                                        {{ $kel->tanggal_penempatan_guru->tgl() }}
                                                    </span>
                                                @else
                                                    <span class="text-xs italic" style="color:#9CA3AF;">-</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs italic" style="color:#9CA3AF;">-</span>
                                    @endif
                                @else
                                    <span class="text-xs" style="color:#D1D5DB;">-</span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        @if($search || $filterJabatan || $filterJenisKelamin)
                                            Tidak ada guru yang sesuai dengan filter
                                        @else
                                            Belum ada data guru
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
        @if($gurus->hasPages())
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                {{ $gurus->links() }}
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
                            Apakah Anda yakin ingin menghapus <strong style="color:#9B3A3A;">{{ count($selectedIds) }} guru</strong> yang dipilih?
                        </p>

                        <div class="mt-3 p-3 rounded-lg" style="background-color:#FEF3C7;border-left:3px solid #C9A227;">
                            <p class="font-medium mb-2" style="color:#1A2422;font-size:0.8125rem;">
                                Peringatan:
                            </p>
                            <ul class="space-y-1 ml-4 text-xs" style="color:#4B5563;list-style-type:disc;">
                                <li>Guru yang membimbing kelompok akan kehilangan pembimbingnya</li>
                                <li>Semua tugas tambahan akan ikut terhapus</li>
                                <li>Semua riwayat penilaian setoran dan absensi akan terhapus permanen</li>
                                <li>Data tidak dapat dikembalikan setelah dihapus</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3">
                        <button
                            wire:click="closeBulkDeleteConfirm"
                            type="button"
                            class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                            style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                        >
                            Batal
                        </button>
                        <button
                            wire:click="bulkDelete"
                            type="button"
                            class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                            style="background-color:#9B3A3A;color:#FFFFFF;border:1.5px solid #9B3A3A;"
                        >
                            Hapus Semua
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL REGISTRASI KELUAR --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showRegistrasiKeluarModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none" role="dialog" aria-modal="true" style="pointer-events: none;">
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl" style="font-family:'Inter',sans-serif; pointer-events: auto;">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl" style="background-color:#9B3A3A;">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Registrasi Keluar Guru
                        </h3>
                    </div>
                    <button wire:click="tutupModalRegistrasiKeluar" class="text-white hover:text-red-200 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6">

                    @if(!empty($kelompokAktif))
                        {{-- HARD BLOCK: Guru masih punya kelompok aktif --}}
                        <div class="flex gap-3 p-4 mb-5 rounded-xl" style="background-color:#FEF2F2; border:1.5px solid #FCA5A5;">
                            <svg class="w-5 h-5 shrink-0 mt-0.5" style="color:#DC2626;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold mb-2" style="color:#991B1B;">
                                    Guru ini tidak dapat diregistrasi keluar!
                                </p>
                                <p class="text-sm mb-3" style="color:#991B1B;">
                                    Guru ini masih terdaftar sebagai pembimbing pada kelompok berikut. Silakan ganti pembimbing pada kelompok-kelompok tersebut terlebih dahulu melalui menu <strong>Kelompok Tahfidz</strong>.
                                </p>
                                <ul class="space-y-1">
                                    @foreach($kelompokAktif as $namaKelompok)
                                        <li class="flex items-center gap-2 text-sm" style="color:#7F1D1D;">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full shrink-0" style="background-color:#DC2626;"></span>
                                            <strong>{{ $namaKelompok }}</strong>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        @php $guruTerpilih = $gurus->firstWhere('id', $selectedIds[0] ?? null); @endphp
                        @if($guruTerpilih)
                            <div class="flex items-center gap-4 p-4 rounded-xl" style="background-color:#F8FAFC; border:1px solid #E2E8F0;">
                                <div class="shrink-0 w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg" style="background-color:#E0F2FE;color:#0369A1;">
                                    {{ substr($guruTerpilih->nama, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $guruTerpilih->nama }}</h4>
                                    <p class="text-xs text-gray-500">NIY: {{ $guruTerpilih->niy }} &bull; {{ $guruTerpilih->jabatan }}</p>
                                </div>
                            </div>
                        @endif

                    @else
                        {{-- Normal flow: Guru tidak punya kelompok aktif --}}
                        <div class="flex gap-3 p-4 mb-6 rounded-lg" style="background-color:#FEF2F2; border:1px solid #FEE2E2;">
                            <svg class="w-5 h-5 shrink-0 mt-0.5" style="color:#DC2626;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-sm" style="color:#991B1B;">
                                Perhatian: Guru yang didaftarkan keluar akan dipindahkan ke tab <strong>Guru Non-Aktif</strong>. Aksi ini tidak akan menghapus data riwayat di masa lalu, namun guru tersebut tidak lagi aktif di sistem.
                            </p>
                        </div>

                        @php $guruTerpilih = $gurus->firstWhere('id', $selectedIds[0] ?? null); @endphp
                        @if($guruTerpilih)
                            <div class="flex items-center gap-4 p-4 mb-6 rounded-xl" style="background-color:#F8FAFC; border:1px solid #E2E8F0;">
                                <div class="shrink-0 w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg" style="background-color:#E0F2FE;color:#0369A1;">
                                    {{ substr($guruTerpilih->nama, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $guruTerpilih->nama }}</h4>
                                    <p class="text-xs text-gray-500">NIY: {{ $guruTerpilih->niy }} &bull; Jabatan: {{ $guruTerpilih->jabatan }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-4">
                            {{-- Jenis Keluar --}}
                            <div>
                                <label class="block text-sm font-medium mb-1" style="color:#374151;">
                                    Jenis Keluar <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="jenis_keluar" class="w-full rounded-lg px-3 py-2 text-sm focus:ring-2 focus:outline-none" style="border:1px solid #D1D5DB; color:#1A2422;">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Pensiun">Pensiun</option>
                                    <option value="Mutasi">Mutasi / Pindah Tugas</option>
                                    <option value="Berhenti">Berhenti</option>
                                    <option value="Meninggal">Meninggal Dunia</option>
                                </select>
                                @error('jenis_keluar') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Tanggal Keluar --}}
                            <div>
                                <label class="block text-sm font-medium mb-1" style="color:#374151;">
                                    Tanggal Efektif Keluar <span class="text-red-500">*</span>
                                </label>
                                <input type="date" wire:model="tanggal_keluar" class="w-full rounded-lg px-3 py-2 text-sm focus:ring-2 focus:outline-none" style="border:1px solid #D1D5DB; color:#1A2422;">
                                @error('tanggal_keluar') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Keterangan / Catatan --}}
                            <div>
                                <label class="block text-sm font-medium mb-1" style="color:#374151;">
                                    Keterangan / Catatan
                                </label>
                                <textarea wire:model="keterangan_keluar" rows="3" class="w-full rounded-lg px-3 py-2 text-sm focus:ring-2 focus:outline-none" style="border:1px solid #D1D5DB; color:#1A2422;" placeholder="Tuliskan keterangan detail jika ada..."></textarea>
                                @error('keterangan_keluar') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="flex gap-3 px-6 py-4 rounded-b-2xl" style="background-color:#F9FAFB; border-top:1px solid #E5E7EB;">
                    <button wire:click="tutupModalRegistrasiKeluar" type="button" class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors" style="background-color:#FFFFFF; color:#374151; border:1px solid #D1D5DB;">
                        {{ !empty($kelompokAktif) ? 'Tutup' : 'Batal' }}
                    </button>
                    @if(empty($kelompokAktif))
                        <button wire:click="simpanRegistrasiKeluar" type="button" class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors" style="background-color:#9B3A3A; color:#FFFFFF; border:1px solid #9B3A3A;">
                            <span wire:loading.remove wire:target="simpanRegistrasiKeluar">Konfirmasi &amp; Keluarkan Guru</span>
                            <span wire:loading wire:target="simpanRegistrasiKeluar">Memproses...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL FORM (TAMBAH / EDIT GURU) --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showFormModal)
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif; pointer-events: none;">
            <div class="flex min-h-full items-center justify-center p-4" style="pointer-events: auto;">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-4xl w-full overflow-visible flex flex-col" style="border:1px solid #E5E7EB; max-height: 90vh;">

                    {{-- Header Modal --}}
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl shrink-0" style="background-color:#1B5E4F;">
                        <h3 class="text-xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                            {{ $isEdit ? 'Edit Data Guru' : 'Registrasi Guru Baru' }}
                        </h3>
                        <button wire:click="closeFormModal" class="text-white hover:text-gray-200 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Form Content (Scrollable) --}}
                    <div class="overflow-y-auto overflow-x-visible flex-1 p-6 space-y-6">

                        {{-- IDENTITAS --}}
                        <div>
                            <h4 class="text-lg font-semibold text-[#1A2422] mb-4 border-b pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">Identitas</h4>
                            <div class="space-y-3">
                                {{-- NIY --}}
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">NIY <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <input
                                            type="text"
                                            wire:model.live="niy"
                                            maxlength="16"
                                            inputmode="numeric"
                                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 16)"
                                            @class([
                                                'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                'border-red-400 bg-red-50' => $errors->has('niy'),
                                                'border-gray-300' => !$errors->has('niy'),
                                            ])
                                            placeholder="16 digit angka"
                                        >
                                        <div class="flex items-center justify-between mt-1">
                                            @error('niy')
                                                <p class="text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                                            @else
                                                <span></span>
                                            @enderror
                                            <span class="text-xs text-gray-400">{{ strlen($niy ?? '') }}/16</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Nama Lengkap --}}
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Nama Lengkap <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <input
                                            type="text"
                                            wire:model.live="nama"
                                            @class([
                                                'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                'border-red-400 bg-red-50' => $errors->has('nama'),
                                                'border-gray-300' => !$errors->has('nama'),
                                            ])
                                            placeholder="Nama lengkap guru"
                                        >
                                        @error('nama') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Jenis Kelamin --}}
                                <div class="grid grid-cols-12 gap-3 items-center">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Jenis Kelamin <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                    <div class="col-span-8">
                                        <div class="flex gap-6">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" wire:model.live="jenis_kelamin" value="Laki-laki" class="accent-[#1B5E4F]">
                                                <span class="text-sm text-gray-700">L</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" wire:model.live="jenis_kelamin" value="Perempuan" class="accent-[#1B5E4F]">
                                                <span class="text-sm text-gray-700">P</span>
                                            </label>
                                        </div>
                                        @error('jenis_kelamin') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Tempat Lahir --}}
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Tempat Lahir <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <input
                                            type="text"
                                            wire:model.live="tempat_lahir"
                                            @class([
                                                'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                'border-red-400 bg-red-50' => $errors->has('tempat_lahir'),
                                                'border-gray-300' => !$errors->has('tempat_lahir'),
                                            ])
                                            placeholder="Kota/Kabupaten"
                                        >
                                        @error('tempat_lahir') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Tanggal Lahir --}}
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Tanggal Lahir <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8 relative">
                                        <div wire:ignore>
                                            <input
                                                type="text"
                                                id="tanggal_lahir"
                                                x-data="{
                                                    init() {
                                                        const fp = flatpickr($el, {
                                                            dateFormat: 'Y-m-d',
                                                            maxDate: 'today',
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
                                                                @this.set('tanggal_lahir', dateStr);
                                                            },
                                                            onOpen: function(selectedDates, dateStr, instance) {
                                                                // Pastikan calendar ter-render dengan benar
                                                                setTimeout(() => {
                                                                    instance.calendarContainer.style.width = 'auto';
                                                                }, 10);
                                                            }
                                                        });
                                                    }
                                                }"
                                                @class([
                                                    'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                    'border-red-400 bg-red-50' => $errors->has('tanggal_lahir'),
                                                    'border-gray-300' => !$errors->has('tanggal_lahir'),
                                                ])
                                                placeholder="Pilih tanggal"
                                                value="{{ $tanggal_lahir }}"
                                            >
                                        </div>
                                        @error('tanggal_lahir') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Jabatan --}}
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Jabatan <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <select
                                            wire:model.live="jabatan"
                                            @class([
                                                'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                'border-red-400 bg-red-50' => $errors->has('jabatan'),
                                                'border-gray-300' => !$errors->has('jabatan'),
                                            ])
                                        >
                                            <option value="">Pilih</option>
                                            @foreach($jabatanOptions as $jab)
                                                <option value="{{ $jab }}">{{ $jab }}</option>
                                            @endforeach
                                        </select>
                                        @error('jabatan') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Tanggal Mulai Kerja --}}
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Tanggal Mulai Kerja <span class="text-gray-400 font-normal">(Opsional)</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8 relative">
                                        <div wire:ignore>
                                            <input
                                                type="text"
                                                id="tanggal_mulai_kerja"
                                                x-data="{
                                                    init() {
                                                        const fp = flatpickr($el, {
                                                            dateFormat: 'Y-m-d',
                                                            maxDate: 'today',
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
                                                                @this.set('tanggal_mulai_kerja', dateStr);
                                                            },
                                                            onOpen: function(selectedDates, dateStr, instance) {
                                                                // Pastikan calendar ter-render dengan benar
                                                                setTimeout(() => {
                                                                    instance.calendarContainer.style.width = 'auto';
                                                                }, 10);
                                                            }
                                                        });
                                                    }
                                                }"
                                                @class([
                                                    'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                    'border-red-400 bg-red-50' => $errors->has('tanggal_mulai_kerja'),
                                                    'border-gray-300' => !$errors->has('tanggal_mulai_kerja'),
                                                ])
                                                placeholder="Pilih tanggal"
                                                value="{{ $tanggal_mulai_kerja }}"
                                            >
                                        </div>
                                        @error('tanggal_mulai_kerja') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KONTAK --}}
                        <div>
                            <h4 class="text-lg font-semibold text-[#1A2422] mb-4 border-b pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">Kontak</h4>
                            <div class="space-y-3">
                                {{-- No Telepon --}}
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">No. Telepon <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <input
                                            type="text"
                                            wire:model.live="no_telepon"
                                            maxlength="13"
                                            inputmode="numeric"
                                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 13)"
                                            @class([
                                                'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                'border-red-400 bg-red-50' => $errors->has('no_telepon'),
                                                'border-gray-300' => !$errors->has('no_telepon'),
                                            ])
                                            placeholder="08xxxxxxxxxx"
                                        >
                                        <div class="flex items-center justify-between mt-1">
                                            @error('no_telepon')
                                                <p class="text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                                            @else
                                                <span></span>
                                            @enderror
                                            <span class="text-xs text-gray-400">{{ strlen($no_telepon ?? '') }}/13</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Email <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <input
                                            type="email"
                                            wire:model.live="email"
                                            @class([
                                                'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                'border-red-400 bg-red-50' => $errors->has('email'),
                                                'border-gray-300' => !$errors->has('email'),
                                            ])
                                            placeholder="email@example.com"
                                        >
                                        @error('email') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Footer Actions --}}
                    <div class="mt-6 pt-6 pb-2 px-6 flex justify-end gap-3 shrink-0" style="border-top:1px solid #E5E7EB;">
                        <button wire:click="closeFormModal" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #9B3A3A; border: 1.5px solid #9B3A3A;">
                            Batal
                        </button>
                        <button wire:click="saveForm" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #1B5E4F; border: 1.5px solid #1B5E4F;">
                            <span wire:loading.remove wire:target="saveForm">Simpan Data</span>
                            <span wire:loading wire:target="saveForm">Menyimpan...</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif


</div>
