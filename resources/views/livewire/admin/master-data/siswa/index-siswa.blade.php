{{--
    View: livewire/admin/master-data/siswa/index-siswa.blade.php
    Komponen Livewire: App\Livewire\Admin\MasterData\Siswa\IndexSiswa

    Redesigned UI dengan bulk actions dan 2-column header layout
--}}
<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- PAGE HEADER --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Data Siswa
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Kelola data siswa, registrasi siswa baru, dan data orang tua/wali
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
                {{-- Tambah Siswa --}}
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
                        title="Pilih tepat 1 siswa untuk edit"
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
                        wire:click="bukaModalBulkDelete"
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
                        title="Pilih minimal 1 siswa untuk hapus"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                @endif



                @auth('admin')
                    {{-- Registrasi Keluar - Support Bulk (1+) --}}
                    @if(count($selectedIds) > 0)
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

                        {{-- Tempatkan Kelompok --}}
                        @if($this->canPlaceKelompok)
                            <button
                                type="button"
                                wire:click="bukaModalBulkPenempatan"
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none shadow-sm"
                                style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Tempatkan Kelompok
                            </button>
                        @else
                            <button
                                disabled
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                                style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                                title="Siswa yang sudah ditempatkan atau telah lulus tidak dapat ditempatkan ke kelompok lagi"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Tempatkan Kelompok
                            </button>
                        @endif

                        {{-- Track Record Hafalan (Single Selection) --}}
                        @if(count($selectedIds) === 1)
                            <a
                                href="{{ route('admin.setoran.riwayat-hafalan', ['siswa' => $selectedIds[0], 'kelompok_id' => 'all']) }}"
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none shadow-sm"
                                style="background-color:#0D9488;color:#FFFFFF;border:1.5px solid #0D9488;"
                                title="Lihat rekam jejak riwayat hafalan siswa"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Track Record Hafalan
                            </a>
                        @endif
                    @else
                        {{-- Disabled state when no selection --}}
                        <button
                            disabled
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                            style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                            title="Pilih minimal 1 siswa untuk registrasi keluar"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Registrasi Keluar
                        </button>

                        <button
                            disabled
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                            style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                            title="Pilih minimal 1 siswa untuk tempatkan ke kelompok"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Tempatkan Kelompok
                        </button>
                    @endif
                @endauth

                {{-- Info Label --}}
                <div class="flex items-center sm:ml-2 sm:pl-4 sm:border-l border-gray-200">
                    <span class="text-sm text-gray-500">
                        Total <strong style="color:#1A2422;">{{ $siswas->total() }}</strong> siswa
                        @if(count($selectedIds) > 0)
                            <span class="mx-1.5">&bull;</span>
                            <strong style="color:#1B5E4F;">{{ count($selectedIds) }}</strong> terpilih
                        @endif
                    </span>
                </div>

            </div>

            {{-- RIGHT: Search Field + Filter Dropdown Button --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-[400px]">
                {{-- Search Input --}}
                <div class="flex-1">
                    <x-ui.search-field
                        wireModel="search"
                        placeholder="Cari siswa (NIS, NISN, Nama)..."
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
                        @if($filterStatusPenempatan || $filterJenisKelamin || $filterKelas)
                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                {{ collect([$filterStatusPenempatan, $filterJenisKelamin, $filterKelas])->filter()->count() }}
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
                         class="absolute z-50 right-0 mt-2 w-72 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                         style="display: none; border: 1px solid #E5E7EB;">

                        <div class="p-4 space-y-3">
                            {{-- Filter Header --}}
                            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                                <h3 class="text-sm font-semibold" style="color:white;font-family:'Plus Jakarta Sans',sans-serif;">
                                    Filter Siswa
                                </h3>
                                @if($filterStatusAkademik || $filterStatusTahfidz || $filterStatusPenempatan || $filterJenisKelamin || $filterKelas)
                                    <button
                                        wire:click="resetFilters"
                                        type="button"
                                        class="text-xs font-medium text-gray-500 hover:text-[#1B5E4F] transition-colors"
                                    >
                                        Reset
                                    </button>
                                @endif
                            </div>

                            {{-- Filter Status Akademik --}}
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Status Akademik
                                </label>
                                <select
                                    wire:model.live="filterStatusAkademik"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Lulus SMP">Lulus SMP</option>
                                    <option value="Pindah Sekolah">Pindah Sekolah</option>
                                    <option value="Keluar">Keluar</option>
                                </select>
                            </div>

                            {{-- Filter Status Tahfidz --}}
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Status Tahfidz
                                </label>
                                <select
                                    wire:model.live="filterStatusTahfidz"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    <option value="Sedang Berlangsung">Sedang Berlangsung</option>
                                    <option value="Lulus Program Tahfidz">Lulus</option>
                                </select>
                            </div>

                            {{-- Filter Status Penempatan --}}
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Status Penempatan
                                </label>
                                <select
                                    wire:model.live="filterStatusPenempatan"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    <option value="Belum Ditempatkan">Belum Ditempatkan</option>
                                    <option value="Ditempatkan">Ditempatkan</option>
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

                            {{-- Filter Kelas Akademik --}}
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Kelas Akademik
                                </label>
                                <select
                                    wire:model.live="filterKelas"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}">{{ $kelas->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TABLE WITH BULK SELECTION --}}
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
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">NIS/NISN</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">JK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tempat, Tanggal Lahir</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Status Akademik</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Status Tahfidz</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Terdaftar Sebagai</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Hafalan Sebelumnya</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Kelompok</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tahun Ajaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    @forelse($siswas as $siswa)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Checkbox --}}
                            <td class="px-6 py-4 text-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedIds"
                                    value="{{ $siswa->id }}"
                                    class="w-4 h-4 rounded transition-colors cursor-pointer"
                                    style="color:#1B5E4F;border-color:#D1D5DB;"
                                >
                            </td>

                            {{-- NIS / NISN --}}
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium" style="color:#1A2422;">{{ $siswa->nis }}</div>
                                <div class="text-xs" style="color:#6B7280;">{{ $siswa->nisn ?? '-' }}</div>
                            </td>

                            {{-- Nama --}}
                            <td class="px-6 py-4 text-sm font-medium">
                                <button
                                    wire:click="openDetailModal({{ $siswa->id }})"
                                    class="hover:underline transition-colors text-left"
                                    style="color:#1B5E4F;"
                                    title="Lihat detail siswa">
                                    {{ $siswa->nama }}
                                </button>
                            </td>

                            {{-- Jenis Kelamin (abbreviated) --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                {{ $siswa->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}
                            </td>

                            {{-- Tempat, Tanggal Lahir --}}
                            <td class="px-6 py-4 text-sm whitespace-nowrap" style="color:#1A2422;">
                                @php
                                    $tempat = $siswa->tempat_lahir;
                                    $tglLahir = $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d/m/Y') : null;
                                    $ttl = $tempat && $tglLahir
                                        ? "{$tempat}, {$tglLahir}"
                                        : ($tempat ?? ($tglLahir ?? '-'));
                                @endphp
                                {{ $ttl }}
                            </td>

                            {{-- Kelas --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                @if($siswa->kelas)
                                    <span class="font-medium">{{ $siswa->kelas->nama_lengkap }}</span>
                                @else
                                    <span style="color:#6B7280;">-</span>
                                @endif
                            </td>

                            {{-- Status Akademik --}}
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $statusAkademikColors = [
                                        'Aktif' => ['bg' => '#D1FAE5', 'text' => '#065F46'],
                                        'Lulus SMP' => ['bg' => '#DBEAFE', 'text' => '#1E40AF'],
                                        'Pindah Sekolah' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
                                        'Keluar' => ['bg' => '#FEE2E2', 'text' => '#991B1B'],
                                    ];
                                    $color = $statusAkademikColors[$siswa->status_akademik] ?? ['bg' => '#F3F4F6', 'text' => '#374151'];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap" style="background-color:{{ $color['bg'] }};color:{{ $color['text'] }};">
                                    {{ $siswa->status_akademik }}
                                </span>
                            </td>

                            {{-- Status Tahfidz --}}
                            <td class="px-6 py-4 text-sm">
                                @php
                                    $isLulusTahfidz = $siswa->status_tahfidz === 'Lulus Program Tahfidz' || $siswa->status_tahfidz === 'Lulus';
                                    $isSedangBerlangsung = !$isLulusTahfidz && ($siswa->kelompok_id !== null && $siswa->status_penempatan === 'Ditempatkan');
                                @endphp
                                @if($isLulusTahfidz)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap" style="background-color:#D1FAE5;color:#065F46;">
                                        Lulus
                                    </span>
                                @elseif($isSedangBerlangsung)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap" style="background-color:#E0E7FF;color:#3730A3;">
                                        Sedang Berlangsung
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap" style="background-color:#F3F4F6;color:#6B7280;">
                                        -
                                    </span>
                                @endif
                            </td>

                            {{-- Terdaftar Sebagai --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                @if($siswa->status_masuk)
                                    <div class="flex flex-col items-start gap-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium" style="background-color:{{ $siswa->status_masuk === 'Baru' ? '#E8F5F1' : '#E8EEF2' }};color:{{ $siswa->status_masuk === 'Baru' ? '#1B5E4F' : '#5B7A8C' }};">
                                            {{ $siswa->status_masuk === 'Baru' ? 'Siswa Baru' : $siswa->status_masuk }}
                                        </span>
                                        <span class="text-xs" style="color:#6B7280;">{{ $siswa->tanggal_masuk ? $siswa->tanggal_masuk->format('d/m/Y') : '-' }}</span>
                                    </div>
                                @else
                                    <span style="color:#6B7280;">-</span>
                                @endif
                            </td>

                            {{-- Hafalan Sebelumnya --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                @if($siswa->punya_hafalan_sebelumnya)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" style="background-color:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE;">
                                        Ya
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" style="background-color:#F0FDF4;color:#15803D;border:1px solid #BBF7D0;">
                                        Tidak
                                    </span>
                                @endif
                            </td>

                            {{-- Kelompok --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                @if($siswa->kelompok)
                                    <div class="flex flex-col gap-0.5">
                                        <span class="font-medium text-sm" style="color:#1A2422;">{{ $siswa->kelompok->nama }}</span>
                                        <span class="text-xs" style="color:#6B7280;">{{ $siswa->kelompok->tingkat }} · {{ $siswa->kelompok->jenis }}</span>
                                    </div>
                                @elseif($siswa->status_akademik === 'Aktif')
                                    <span style="color:#9CA3AF;">Belum Ditempatkan</span>
                                @else
                                    <span style="color:#9CA3AF;">-</span>
                                @endif
                            </td>

                            {{-- Tahun Ajaran --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                @if($siswa->tahunAjaran)
                                    <span class="font-medium">{{ $siswa->tahunAjaran->label }}</span>
                                @else
                                    <span style="color:#6B7280;">-</span>
                                @endif
                            </td>

                            {{-- Keterangan (status penempatan + tanggal penempatan / kelulusan) --}}
                            <td class="px-6 py-4">
                                @php
                                    $isLulus = $siswa->status_tahfidz === 'Lulus Program Tahfidz' || $siswa->status_tahfidz === 'Lulus';
                                    $tglLulus = null;
                                    if ($siswa->kelulusanProgram?->tanggal_tuntas) {
                                        $tglLulus = $siswa->kelulusanProgram->tanggal_tuntas->format('d/m/Y');
                                    } elseif ($siswa->progresHafalan?->tanggal_lulus_tahfidz) {
                                        $tglLulus = \Carbon\Carbon::parse($siswa->progresHafalan->tanggal_lulus_tahfidz)->format('d/m/Y');
                                    } elseif ($siswa->tanggal_lulus_akademik) {
                                        $tglLulus = $siswa->tanggal_lulus_akademik->format('d/m/Y');
                                    }
                                @endphp
                                @if($isLulus)
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-xs font-semibold" style="color:#065F46;">Lulus</span>
                                        @if($tglLulus)
                                            <span class="text-xs" style="color:#6B7280;">{{ $tglLulus }}</span>
                                        @endif
                                    </div>
                                @elseif($siswa->kelompok && $siswa->status_penempatan === 'Ditempatkan')
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-xs font-medium" style="color:#15803D;">Sudah ditempatkan</span>
                                        @if($siswa->tanggal_penempatan)
                                            <span class="text-xs" style="color:#6B7280;">{{ $siswa->tanggal_penempatan->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                @else
                                    @php
                                        $usulan = $siswa->usulanKenaikanTingkats->first();
                                        if ($usulan && $usulan->status_usulan->value === 'Disetujui') {
                                            $keterangan = "→ Siap ke Kelompok " . $usulan->tingkat_tujuan->value;
                                            $color = '059669'; // Emerald 600
                                        } elseif ($usulan && $usulan->status_usulan->value === 'Diajukan') {
                                            $keterangan = "→ Menunggu keputusan ke " . $usulan->tingkat_tujuan->value;
                                            $color = 'F59E0B'; // Amber 500
                                        } else {
                                            $keterangan = $siswa->punya_hafalan_sebelumnya
                                                ? '→ Perlu Tes Penempatan'
                                                : '→ Siap ke Kelompok Dasar';
                                            $color = '6B7280'; // Gray 500
                                        }
                                    @endphp
                                    <span class="text-xs" style="color:#{{ $color }};">
                                        {{ $keterangan }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        @if($search || $filterStatusAkademik || $filterStatusTahfidz || $filterStatusPenempatan || $filterJenisKelamin || $filterKelas)
                                            Tidak ada siswa yang sesuai dengan filter
                                        @else
                                            Belum ada data siswa
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
        @if($siswas->hasPages())
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                {{ $siswas->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL BULK DELETE CONFIRMATION --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showModalBulkDelete)
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif; pointer-events: none;">
            <div class="flex min-h-full items-center justify-center p-4" style="pointer-events: auto;">

            {{-- Modal --}}
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6" style="border:1px solid #E5E7EB;">
                    {{-- Icon --}}
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full" style="background-color:#FEE2E2;">
                        <svg class="w-6 h-6" style="color:#9B3A3A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>

                    {{-- Title --}}
                    {{-- Title --}}
                    <h3 class="text-lg font-semibold text-center mb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                        Konfirmasi Hapus {{ count($selectedIds) === 1 ? 'Siswa' : 'Massal' }}
                    </h3>

                    {{-- Message --}}
                    <div class="text-sm mb-6" style="color:#6B7280;">
                        <p class="text-center mb-3">
                            Apakah Anda yakin ingin menghapus
                            <strong style="color:#9B3A3A;">
                                {{ count($selectedIds) === 1 ? 'siswa ini' : count($selectedIds) . ' siswa yang dipilih' }}
                            </strong>?
                        </p>

                        <div class="mt-3 p-3 rounded-lg" style="background-color:#FEF3C7;border-left:3px solid #C9A227;">
                            <p class="font-medium mb-2" style="color:#1A2422;font-size:0.8125rem;">
                                Peringatan:
                            </p>
                            <ul class="space-y-1 ml-4 text-xs" style="color:#4B5563;list-style-type:disc;">
                                <li>Semua data orang tua/wali akan ikut terhapus</li>
                                <li>Semua riwayat absensi, setoran, dan progres akan terhapus permanen</li>
                                <li>Data tidak dapat dikembalikan setelah dihapus</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3">
                        <button
                            wire:click="tutupModalBulkDelete"
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
                            {{ count($selectedIds) === 1 ? 'Hapus' : 'Hapus Semua' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL PARTIALS --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    {{-- Registrasi Masuk Modal --}}
    @include('livewire.admin.master-data.siswa.partials.modal-registrasi-masuk')

    {{-- Registrasi Keluar Modal --}}
    @include('livewire.admin.master-data.siswa.partials.modal-registrasi-keluar')

    {{-- Konfirmasi Keluar Modal --}}
    @include('livewire.admin.master-data.siswa.partials.modal-konfirmasi-keluar')

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL FORM (TAMBAH / EDIT SISWA) --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showFormModal)
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif; pointer-events: none;">
            <div class="flex min-h-full items-center justify-center p-4" style="pointer-events: auto;">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-4xl w-full overflow-visible flex flex-col" style="border:1px solid #E5E7EB; max-height:90vh;">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                        <h3 class="text-xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                            {{ $isEdit ? 'Edit Data Siswa' : 'Registrasi Siswa Baru' }}
                        </h3>
                        <button wire:click="closeFormModal" class="text-white hover:text-gray-200 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Scrollable Content --}}
                    <div class="overflow-y-auto overflow-x-visible flex-1 p-6 space-y-6">

                        {{-- IDENTITAS --}}
                        <div>
                            <h4 class="text-lg font-semibold text-[#1A2422] mb-4 border-b pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">Data Identitas</h4>
                            <div class="space-y-3">
                                {{-- NIS --}}
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">NIS <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <input
                                            type="text"
                                            wire:model.live="nis"
                                            maxlength="9"
                                            inputmode="numeric"
                                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 9)"
                                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm"
                                            placeholder="9 digit angka">
                                        <div class="flex items-center justify-between mt-1">
                                            @error('nis') <p class="text-xs" style="color:#9B3A3A;">{{ $message }}</p> @else <span></span> @enderror
                                            <span class="text-xs text-gray-400">{{ strlen($nis ?? '') }}/9</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- NISN --}}
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">NISN</label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <input
                                            type="text"
                                            wire:model.live="nisn"
                                            maxlength="9"
                                            inputmode="numeric"
                                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 9)"
                                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm"
                                            placeholder="9 digit angka">
                                        <div class="flex items-center justify-between mt-1">
                                            @error('nisn') <p class="text-xs" style="color:#9B3A3A;">{{ $message }}</p> @else <span></span> @enderror
                                            <span class="text-xs text-gray-400">{{ strlen($nisn ?? '') }}/9</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Nama Lengkap --}}
                                <div class="grid grid-cols-12 gap-3 items-center">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Nama Lengkap <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                    <div class="col-span-8">
                                        <input type="text" wire:model="nama" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm" placeholder="Nama lengkap siswa">
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
                                                <span class="text-sm" style="color:#1A2422;">L</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" wire:model.live="jenis_kelamin" value="Perempuan" class="accent-[#1B5E4F]">
                                                <span class="text-sm" style="color:#1A2422;">P</span>
                                            </label>
                                        </div>
                                        @error('jenis_kelamin') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Tempat Lahir --}}
                                <div class="grid grid-cols-12 gap-3 items-center">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Tempat Lahir <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                    <div class="col-span-8">
                                        <input type="text" wire:model="tempat_lahir" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm" placeholder="Kota/Kabupaten">
                                        @error('tempat_lahir') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Tanggal Lahir --}}
                                <div class="grid grid-cols-12 gap-3 items-center">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Tanggal Lahir <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                    <div class="col-span-8 relative">
                                        <div wire:ignore>
                                            <input
                                                type="text"
                                                id="tanggal_lahir_siswa"
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
                                                                setTimeout(() => {
                                                                    instance.calendarContainer.style.width = 'auto';
                                                                }, 10);
                                                            }
                                                        });
                                                    }
                                                }"
                                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm"
                                                placeholder="Pilih tanggal"
                                                value="{{ $tanggal_lahir }}"
                                            >
                                        </div>
                                        @error('tanggal_lahir') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ORANG TUA --}}
                        <div>
                            <h4 class="text-lg font-semibold text-[#1A2422] mb-4 border-b pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">Data Orang Tua</h4>
                            <div class="space-y-3">
                                {{-- Nama Ayah --}}
                                <div class="grid grid-cols-12 gap-3 items-center">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Nama Ayah</label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                    <div class="col-span-8">
                                        <input type="text" wire:model="nama_ayah" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm" placeholder="Nama ayah kandung">
                                        @error('nama_ayah') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Nama Ibu --}}
                                <div class="grid grid-cols-12 gap-3 items-center">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Nama Ibu</label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                    <div class="col-span-8">
                                        <input type="text" wire:model="nama_ibu" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm" placeholder="Nama ibu kandung">
                                        @error('nama_ibu') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Registrasi Masuk --}}
                            <div class="mt-4 pt-4">
                                <h4 class="text-lg font-semibold text-[#1A2422] mb-4 border-b pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">Informasi Masuk</h4>
                                <div class="space-y-3">
                                    {{-- Kelas --}}
                                    <div class="grid grid-cols-12 gap-3 items-center">
                                        <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Kelas</label>
                                        <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                        <div class="col-span-8">
                                            <select wire:model="kelas_id" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm">
                                                <option value="">Belum ditentukan</option>
                                                @foreach($kelasList as $kls)
                                                    <option value="{{ $kls->id }}">
                                                        {{ $kls->nama_lengkap }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('kelas_id') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    {{-- Terdaftar Sebagai --}}
                                    <div class="grid grid-cols-12 gap-3 items-center">
                                        <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Terdaftar Sebagai</label>
                                        <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                        <div class="col-span-8">
                                            <div class="flex items-center gap-4">
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="radio" wire:model="status_masuk" value="Baru" class="w-4 h-4" style="accent-color:#1B5E4F;">
                                                    <span class="text-sm font-medium" style="color:#1A2422;">Siswa Baru</span>
                                                </label>
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="radio" wire:model="status_masuk" value="Pindahan" class="w-4 h-4" style="accent-color:#1B5E4F;">
                                                    <span class="text-sm font-medium" style="color:#1A2422;">Pindahan</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Tanggal Masuk --}}
                                    <div class="grid grid-cols-12 gap-3 items-center">
                                        <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Tanggal Masuk</label>
                                        <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                        <div class="col-span-8 relative">
                                            <div wire:ignore>
                                                <input
                                                    type="text"
                                                    id="tanggal_masuk_siswa"
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
                                                                    @this.set('tanggal_masuk', dateStr);
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
                                                    value="{{ $tanggal_masuk }}"
                                                >
                                            </div>
                                            @error('tanggal_masuk') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    {{-- Asal Sekolah --}}
                                    <div class="grid grid-cols-12 gap-3 items-center">
                                        <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Asal Sekolah</label>
                                        <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                        <div class="col-span-8">
                                            <input type="text" wire:model="asal_sekolah" placeholder="Nama sekolah/pesantren asal (kosongkan jika tidak ada)" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm">
                                            @error('asal_sekolah') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Informasi Hafalan --}}
                            <div class="mt-4 pt-4" style="border-top:1px solid #E5E7EB;">
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <div class="col-span-3 text-right pt-1">
                                        <span class="text-sm font-medium" style="color:#6B7280;">Hafalan Sebelumnya</span>
                                    </div>
                                    <span class="col-span-1 text-sm font-medium text-center pt-1" style="color:#6B7280;">:</span>
                                    <div class="col-span-8">
                                        <div class="flex items-center gap-4">
                                            <label class="flex items-center gap-2 cursor-pointer group">
                                                <input type="radio" wire:model="punya_hafalan_sebelumnya" value="0" class="w-4 h-4 cursor-pointer" style="accent-color:#1B5E4F;">
                                                <span class="text-sm font-medium" style="color:#1A2422;">Tidak</span>
                                                <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background-color:#F0FDF4;color:#15803D;border:1px solid #BBF7D0;">Kelompok Dasar</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer group">
                                                <input type="radio" wire:model="punya_hafalan_sebelumnya" value="1" class="w-4 h-4 cursor-pointer" style="accent-color:#1B5E4F;">
                                                <span class="text-sm font-medium" style="color:#1A2422;">Ya</span>
                                                <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background-color:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE;">Perlu Tes Penempatan</span>
                                            </label>
                                        </div>
                                        <p class="mt-1.5 text-xs" style="color:#9CA3AF;">Jika siswa memiliki hafalan sebelumnya, akan dijadwalkan tes penempatan untuk menentukan kelompok yang tepat.</p>
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
    {{-- MODAL DETAIL SISWA --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showDetailModal && $selectedSiswa)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="font-family:'Inter',sans-serif;">
            <div class="relative bg-white rounded-2xl shadow-xl max-w-2xl w-full flex flex-col" style="border:1px solid #E5E7EB; max-height:90vh;">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                    <div>
                        <h3 class="text-xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">Detail Data Siswa</h3>
                        <p class="mt-1 text-sm" style="color:#D1FAE5;">{{ $selectedSiswa->nama }}</p>
                    </div>
                    <button wire:click="closeDetailModal" class="text-white hover:text-gray-200 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Scrollable Content --}}
                <div class="overflow-y-auto flex-1 p-6 space-y-6">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">NIS</span>
                                <span class="font-medium" style="color:#1A2422;">{{ $selectedSiswa->nis }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">NISN</span>
                                <span style="color:#1A2422;">{{ $selectedSiswa->nisn ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Jenis Kelamin</span>
                                <span style="color:#1A2422;">{{ $selectedSiswa->jenis_kelamin }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Tempat, Tanggal Lahir</span>
                                <span style="color:#1A2422;">{{ $selectedSiswa->tempat_lahir }}, {{ $selectedSiswa->tanggal_lahir?->tgl() ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Kelas</span>
                                <span style="color:#1A2422;">{{ $selectedSiswa->kelas?->nama_lengkap ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Status Akademik</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    style="background-color:{{ match($selectedSiswa->status_akademik) { 'Aktif' => '#1B5E4F', 'Lulus SMP' => '#C9A227', 'Pindah Sekolah' => '#5B7A8C', default => '#9B3A3A' } }};color:#FFFFFF;">
                                    {{ $selectedSiswa->status_akademik }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Status Penempatan</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    style="background-color:{{ $selectedSiswa->status_penempatan === 'Ditempatkan' ? '#1B5E4F' : '#5B7A8C' }};color:#FFFFFF;">
                                    {{ $selectedSiswa->status_penempatan }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Terdaftar Sebagai</span>
                                <span style="color:#1A2422;">{{ $selectedSiswa->status_masuk ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Tahun Pelajaran</span>
                                <span style="color:#1A2422;">{{ $selectedSiswa->tahunAjaran?->label ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Tanggal Masuk</span>
                                <span style="color:#1A2422;">{{ $selectedSiswa->tanggal_masuk?->tgl() ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- Orang Tua --}}
                        <div class="pt-4" style="border-top:1px solid #E5E7EB;">
                            <h4 class="text-sm font-semibold uppercase tracking-wider mb-3" style="color:#1B5E4F;">Data Orang Tua</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Nama Ayah</span>
                                    <span style="color:#1A2422;">{{ $selectedSiswa->nama_ayah ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Nama Ibu</span>
                                    <span style="color:#1A2422;">{{ $selectedSiswa->nama_ibu ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Kelompok --}}
                        @if($selectedSiswa->kelompok)
                            <div class="pt-4" style="border-top:1px solid #E5E7EB;">
                                <h4 class="text-sm font-semibold uppercase tracking-wider mb-3" style="color:#1B5E4F;">Kelompok Tahfidz</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Nama Kelompok</span>
                                        <span style="color:#1A2422;">{{ $selectedSiswa->kelompok->nama_kelompok }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Guru Pembimbing</span>
                                        <span style="color:#1A2422;">{{ $selectedSiswa->kelompok->guruPembimbing?->nama ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                    {{-- Footer --}}
                    <div class="mt-6 pt-6 pb-2 px-6 flex justify-end shrink-0" style="border-top:1px solid #E5E7EB;">
                        <button wire:click="closeDetailModal" type="button" class="px-5 py-2.5 text-sm font-medium rounded-lg" style="background-color:#F3F4F6;color:#374151;border:1.5px solid #D1D5DB;">Tutup</button>
                    </div>

                </div>
        </div>
    @endif

    {{-- Livewire component untuk popup Jadwalkan Tes --}}
    @livewire('admin.tes-penempatan.schedule-test')

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL BULK PENEMPATAN KELOMPOK --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showModalBulkPenempatan)
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif; pointer-events: none;">
            <div class="flex min-h-full items-center justify-center p-4" style="pointer-events: auto;">
                <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-y-auto" style="border:1px solid #E5E7EB; overflow-x: visible;">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                        <h3 class="text-lg font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                            Tempatkan {{ count($selectedIds) }} Siswa ke Kelompok
                        </h3>
                        <button wire:click="tutupModalBulkPenempatan" class="p-2 rounded-lg transition-colors" style="color:white;">
                            <svg class="h-5 w-5 hover:opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Form Content --}}
                    <div class="p-6">

                    {{-- Info Box --}}
                    @php
                        // Deteksi tingkat siswa (target_tingkat)
                        $tingkatUnik = array_values(array_unique(array_map(fn($item) => $item['target_tingkat'], $tingkatSiswa)));
                        $samaTingkat = count($tingkatUnik) === 1;
                        $tingkatTujuan = $samaTingkat ? $tingkatUnik[0] : null;

                        // Ambil label status secara keseluruhan (jika sama, atau sebut campuran)
                        $labelStatusUnik = array_values(array_unique(array_map(fn($item) => $item['status_label'], $tingkatSiswa)));
                        $labelTingkat = count($labelStatusUnik) === 1 ? $labelStatusUnik[0] : 'Berbagai Status / Tingkat';
                    @endphp

                    @if($samaTingkat && $tingkatTujuan)
                        {{-- Smart Detection: Semua siswa sama tingkatnya --}}
                        <div class="mb-5 p-4 rounded-xl" style="background-color:#F0FDF4;border:1px solid #BBF7D0;">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 shrink-0" style="color:#15803D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm" style="color:#15803D;">
                                    <p class="font-semibold mb-1">✨ Smart Detection</p>
                                    <p class="text-xs">{{ count($selectedIds) }} siswa dengan status <strong>{{ $labelTingkat }}</strong> akan ditempatkan ke tingkat <strong>{{ $tingkatTujuan }}</strong>. Kelompok yang ditampilkan sudah difilter otomatis.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Mixed Tingkat: Siswa punya tingkat berbeda-beda --}}
                        <div class="mb-5 p-4 rounded-xl" style="background-color:#FFF7ED;border:1px solid #FFEDD5;">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 shrink-0" style="color:#C2410C;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div class="text-sm" style="color:#C2410C;">
                                    <p class="font-semibold mb-1">⚠️ Tingkat Siswa Berbeda</p>
                                    <p class="text-xs">Siswa yang dipilih berasal dari tingkat yang berbeda-beda. Sebaiknya tempatkan siswa dengan tingkat yang sama secara terpisah untuk akurasi penempatan.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Preview Siswa yang Dipilih (Optional - scrollable if many) --}}
                    <div class="mb-5">
                        <p class="text-sm font-medium mb-2" style="color:#1A2422;">Siswa yang akan ditempatkan:</p>
                        <div class="max-h-40 overflow-y-auto p-3 rounded-lg" style="background-color:#F9FAFB;border:1px solid #E5E7EB;">
                            <div class="grid grid-cols-1 gap-2">
                                @php
                                    $selectedSiswasList = App\Models\Siswa::whereIn('id', $selectedIds)->get();
                                @endphp
                                @foreach($selectedSiswasList as $s)
                                    @php
                                        $tingkatSiswaIni = $tingkatSiswa[$s->id] ?? ['target_tingkat' => 'Dasar', 'status_label' => 'Siswa Baru'];
                                        $targetTingkat = $tingkatSiswaIni['target_tingkat'];
                                        $labelStatus = $tingkatSiswaIni['status_label'];

                                        $bgColor = $targetTingkat ? '#DCFCE7' : '#DBEAFE';
                                        $textColor = $targetTingkat ? '#15803D' : '#1E40AF';
                                        $borderColor = $targetTingkat ? '#BBF7D0' : '#BFDBFE';
                                    @endphp
                                    <div class="flex items-center justify-between gap-2 text-xs p-2 rounded" style="background-color:#FFFFFF;border:1px solid #E5E7EB;">
                                        <div class="flex items-center gap-2 min-w-0 flex-1">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0" style="background-color:#1B5E4F;color:#FFFFFF;">
                                                {{ strtoupper(substr($s->nama, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="font-medium truncate" style="color:#1A2422;">{{ $s->nama }}</p>
                                                <p class="text-xs" style="color:#6B7280;">{{ $s->jenis_kelamin === 'Laki-laki' ? 'Laki-laki' : 'Perempuan' }} • NIS: {{ $s->nis }}</p>
                                            </div>
                                        </div>
                                        <span class="shrink-0 px-2 py-1 text-xs font-medium rounded"
                                              style="background-color:{{ $bgColor }};
                                                     color:{{ $textColor }};
                                                     border:1px solid {{ $borderColor }};">
                                            {{ $labelStatus }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Kelompok Dropdown --}}
                    <div class="mb-5">
                        <label class="block text-sm font-medium mb-2" style="color:#1A2422;">
                            Pilih Kelompok Tujuan <span style="color:#9B3A3A;">*</span>
                        </label>

                        @if($kelompokListBulk->isEmpty())
                            <div class="p-4 rounded-lg text-sm text-center" style="background-color:#FFF7ED;color:#C2410C;border:1px solid #FFEDD5;">
                                <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                Belum ada kelompok yang tersedia.<br>
                                <a href="{{ route('admin.master-data.kelompok.index') }}" class="underline font-medium" style="color:#C2410C;">Buat kelompok dulu →</a>
                            </div>
                        @else
                            {{-- PRE-CHECK: Validasi Gender Sebelum Pilih Kelompok --}}
                            @php
                                $selectedSiswasList = App\Models\Siswa::whereIn('id', $selectedIds)->get();
                                $allLakiLaki = $selectedSiswasList->every(fn($s) => $s->jenis_kelamin === 'Laki-laki');
                                $allPerempuan = $selectedSiswasList->every(fn($s) => $s->jenis_kelamin === 'Perempuan');
                                $mixedGender = !$allLakiLaki && !$allPerempuan;

                                // Get mismatch students
                                $lakiLakiCount = $selectedSiswasList->where('jenis_kelamin', 'Laki-laki')->count();
                                $perempuanCount = $selectedSiswasList->where('jenis_kelamin', 'Perempuan')->count();
                            @endphp

                            {{-- WARNING: Mixed Gender --}}
                            @if($mixedGender)
                                <div class="mb-3 p-3 rounded-lg" style="background-color:#FEE2E2;border:1px solid #FCA5A5;">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 mt-0.5 shrink-0" style="color:#DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <div class="text-sm" style="color:#991B1B;">
                                            <p class="font-semibold mb-1">⚠️ Jenis Kelamin Siswa Campur!</p>
                                            <p class="text-xs">Anda memilih <strong>{{ $lakiLakiCount }} siswa Laki-laki</strong> dan <strong>{{ $perempuanCount }} siswa Perempuan</strong>.</p>
                                            <p class="text-xs mt-1">Satu kelompok hanya bisa untuk satu jenis kelamin saja (Banin = Laki-laki, Banat = Perempuan).</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <select
                                wire:model.live="bulkKelompokId"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all"
                            >
                                <option value="">-- Pilih Kelompok --</option>
                                @foreach($kelompokListBulk as $klp)
                                    @php
                                        $sisaKapasitas = $klp->kapasitas_maks - $klp->siswas_count;
                                        $cukupKapasitas = $sisaKapasitas >= count($selectedIds);

                                        // Check gender compatibility
                                        $jenisKelompok = $klp->jenis; // 'Banin' or 'Banat'
                                        $genderCompatible = false;
                                        if ($jenisKelompok === 'Banin' && $allLakiLaki) {
                                            $genderCompatible = true;
                                        } elseif ($jenisKelompok === 'Banat' && $allPerempuan) {
                                            $genderCompatible = true;
                                        }

                                        $canSelect = $cukupKapasitas && $genderCompatible;
                                    @endphp
                                    <option value="{{ $klp->id }}" @disabled(!$canSelect)>
                                        {{ $klp->nama }} ({{ $klp->tingkat }} - {{ $klp->jenis }})
                                        @if($klp->guruPembimbing) — {{ $klp->guruPembimbing->nama }} @endif
                                        — {{ $klp->siswas_count }}/{{ $klp->kapasitas_maks }}
                                        @if(!$cukupKapasitas)
                                            [Kapasitas kurang! Butuh {{ count($selectedIds) }}, tersisa {{ $sisaKapasitas }}]
                                        @elseif(!$genderCompatible)
                                            @if($jenisKelompok === 'Banin')
                                                [❌ Banin hanya untuk Laki-laki]
                                            @else
                                                [❌ Banat hanya untuk Perempuan]
                                            @endif
                                        @else
                                            [Tersisa {{ $sisaKapasitas }} slot]
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            @error('bulkKelompokId')
                                <p class="mt-2 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    {{-- Tanggal Penempatan --}}
                    <div class="mb-5">
                        <label for="bulkTanggalPenempatan" class="block text-sm font-medium mb-2" style="color:#1A2422;">
                            Tanggal Penempatan <span style="color:#9B3A3A;">*</span>
                        </label>
                        <div wire:ignore>
                            <input
                                type="text"
                                id="bulkTanggalPenempatan"
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
                                                @this.set('bulkTanggalPenempatan', dateStr);
                                            },
                                            onOpen: function(selectedDates, dateStr, instance) {
                                                setTimeout(() => {
                                                    instance.calendarContainer.style.width = 'auto';
                                                }, 10);
                                            }
                                        });
                                    }
                                }"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all"
                                placeholder="Pilih tanggal penempatan"
                                value="{{ $bulkTanggalPenempatan }}"
                            >
                        </div>
                        @error('bulkTanggalPenempatan')
                            <p class="mt-1.5 text-xs flex items-center gap-1" style="color:#DC2626;">
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                        <p class="mt-1.5 text-xs" style="color:#6B7280;">
                            <svg class="w-3.5 h-3.5 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            Pilih tanggal penempatan siswa ke kelompok
                        </p>
                    </div>

                    </div>{{-- End Form Content --}}

                    {{-- Footer --}}
                    <div class="flex justify-end gap-3 pt-4 pb-2 px-6" style="border-top:1px solid #E5E7EB;">
                        <button wire:click="tutupModalBulkPenempatan" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #9B3A3A; border: 1.5px solid #9B3A3A;">
                            Batal
                        </button>
                        @if(!$kelompokListBulk->isEmpty())
                            <button wire:click="prosesBulkPenempatan" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #1B5E4F; border: 1.5px solid #1B5E4F;">
                                <span wire:loading.remove wire:target="prosesBulkPenempatan">
                                    Tempatkan {{ count($selectedIds) }} Siswa
                                </span>
                                <span wire:loading wire:target="prosesBulkPenempatan">
                                    Memproses...
                                </span>
                            </button>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>

