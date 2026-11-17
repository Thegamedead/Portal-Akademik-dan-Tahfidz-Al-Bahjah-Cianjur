<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- PAGE HEADER --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Riwayat Setoran Hafalan
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Histori setoran dan penilaian hafalan siswa
        </p>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- SEARCH AND FILTER ROW --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            {{-- Search Field --}}
            @if(in_array($role, ['admin', 'guru']))
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4" style="color:#9CA3AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text"
                               wire:model.live.debounce.300ms="searchSiswa"
                               placeholder="Cari siswa (NIS, Nama)..."
                               class="w-full pl-10 px-4 py-2.5 text-sm rounded-lg transition-colors focus:outline-none focus:ring-2"
                               style="border:1.5px solid #D1D5DB;color:#1A2422;background-color:#FFFFFF;"
                               onfocus="this.style.borderColor='#1B5E4F';this.style.boxShadow='0 0 0 3px rgba(27,94,79,0.1)'"
                               onblur="this.style.borderColor='#D1D5DB';this.style.boxShadow='none'">
                    </div>
                </div>
            @endif

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
                    @php
                        $activeFilters = collect([
                            $tanggalMulai,
                            $tanggalSelesai,
                            $filterJenisSetoran,
                            $filterPredikat,
                            $filterKeputusan,
                            $filterKelompokId
                        ])->filter()->count();
                    @endphp
                    @if($activeFilters > 0)
                        <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                            {{ $activeFilters }}
                        </span>
                    @endif
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                {{-- Backdrop untuk close filter --}}
                <div x-show="open"
                     @click="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-40"
                     style="display: none;">
                </div>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     @click.stop
                     class="absolute z-50 right-0 mt-2 w-80 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                     style="display: none; border: 1px solid #E5E7EB;">

                    <div class="p-4 space-y-3">
                        {{-- Filter Header with Close Button --}}
                        <div class="flex items-center justify-between pb-2" style="border-bottom: 1px solid #E5E7EB;">
                            <h3 class="text-sm font-semibold" style="color:#1A2422;font-family:'Plus Jakarta Sans',sans-serif;">
                                Filter Setoran
                            </h3>
                            <div class="flex items-center gap-2">
                                @if($tanggalMulai || $tanggalSelesai || $filterJenisSetoran || $filterPredikat || $filterKeputusan || $filterKelompokId)
                                    <button
                                        wire:click="resetFilters"
                                        type="button"
                                        class="text-xs font-medium text-gray-500 hover:text-[#1B5E4F] transition-colors"
                                    >
                                        Reset
                                    </button>
                                @endif
                                <button
                                    @click="open = false"
                                    type="button"
                                    class="text-gray-400 hover:text-gray-600 transition-colors"
                                    title="Tutup Filter"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Quick Date Range Shortcuts --}}
                        <div>
                            <label class="block text-xs font-medium mb-2" style="color:#6B7280;">
                                Rentang Cepat
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <button wire:click="setDateRange('today')"
                                        type="button"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors hover:bg-emerald-50 hover:text-emerald-700"
                                        style="border:1px solid #D1D5DB;color:#6B7280;">
                                    Hari Ini
                                </button>
                                <button wire:click="setDateRange('yesterday')"
                                        type="button"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors hover:bg-emerald-50 hover:text-emerald-700"
                                        style="border:1px solid #D1D5DB;color:#6B7280;">
                                    Kemarin
                                </button>
                                <button wire:click="setDateRange('last7days')"
                                        type="button"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors hover:bg-emerald-50 hover:text-emerald-700"
                                        style="border:1px solid #D1D5DB;color:#6B7280;">
                                    7 Hari
                                </button>
                                <button wire:click="setDateRange('last30days')"
                                        type="button"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors hover:bg-emerald-50 hover:text-emerald-700"
                                        style="border:1px solid #D1D5DB;color:#6B7280;">
                                    30 Hari
                                </button>
                                <button wire:click="setDateRange('thisMonth')"
                                        type="button"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors hover:bg-emerald-50 hover:text-emerald-700"
                                        style="border:1px solid #D1D5DB;color:#6B7280;">
                                    Bulan Ini
                                </button>
                                <button wire:click="setDateRange('lastMonth')"
                                        type="button"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors hover:bg-emerald-50 hover:text-emerald-700"
                                        style="border:1px solid #D1D5DB;color:#6B7280;">
                                    Bulan Lalu
                                </button>
                            </div>
                        </div>

                        {{-- Tanggal Mulai --}}
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Tanggal Mulai
                            </label>
                            <input type="date"
                                   wire:model.live="tanggalMulai"
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                   style="border:1.5px solid #D1D5DB;color:#1A2422;">
                        </div>

                        {{-- Tanggal Selesai --}}
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Tanggal Selesai
                            </label>
                            <input type="date"
                                   wire:model.live="tanggalSelesai"
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                   style="border:1.5px solid #D1D5DB;color:#1A2422;">
                        </div>

                        {{-- Jenis Setoran --}}
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Jenis Setoran
                            </label>
                            <select wire:model.live="filterJenisSetoran"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                <option value="">Semua</option>
                                @foreach($jenisSetoranOptions as $jenis)
                                    <option value="{{ $jenis->value }}">{{ $jenis->value }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Predikat --}}
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Predikat
                            </label>
                            <select wire:model.live="filterPredikat"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                <option value="">Semua</option>
                                @foreach($predikatOptions as $predikat)
                                    <option value="{{ $predikat->value }}">{{ $predikat->value }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Keputusan --}}
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Keputusan
                            </label>
                            <select wire:model.live="filterKeputusan"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                <option value="">Semua</option>
                                @foreach($keputusanOptions as $keputusan)
                                    <option value="{{ $keputusan->value }}">{{ $keputusan->value }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kelompok Filter (Admin & Guru only) --}}
                        @if(in_array($role, ['admin', 'guru']))
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Kelompok
                                </label>
                                <select wire:model.live="filterKelompokId"
                                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                        style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                    <option value="">Semua</option>
                                    @foreach($kelompoks as $kelompok)
                                        <option value="{{ $kelompok->id }}">{{ $kelompok->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TABLE --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="background-color:#1B5E4F;color:white;">
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tanggal</th>
                        @if($role !== 'siswa')
                            <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Nama Siswa</th>
                        @endif
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Juz - Surah - Ayat</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold" style="color:white;">Jenis</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold" style="color:white;">Predikat</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold" style="color:white;">Keputusan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold" style="color:white;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    @forelse($setorans as $setoran)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                {{ \Carbon\Carbon::parse($setoran->absensi->tanggal)->tgl() }}
                            </td>
                            @if($role !== 'siswa')
                                <td class="px-6 py-4 text-sm font-medium" style="color:#1A2422;">
                                    {{ $setoran->absensi->siswa->nama_lengkap }}
                                </td>
                            @endif
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <div class="flex flex-col">
                                    <span class="font-medium">Juz {{ $setoran->juz->juz_ke }} - {{ $setoran->surah->nama_latin }}</span>
                                    <span class="text-xs" style="color:#6B7280;">Ayat {{ $setoran->ayat_mulai }} - {{ $setoran->ayat_selesai }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $jenisColors = [
                                        'Hafalan Baru' => ['bg' => '#FEF3C7', 'text' => '#C9A227'],
                                        'Ulang' => ['bg' => '#E0F2FE', 'text' => '#0369A1'],
                                    ];
                                    $jenisColor = $jenisColors[$setoran->jenis_setoran->value] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                      style="background-color:{{ $jenisColor['bg'] }};color:{{ $jenisColor['text'] }};">
                                    {{ $setoran->jenis_setoran->value }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $predikatColors = [
                                        'Lancar' => ['bg' => '#1B5E4F', 'text' => '#FFFFFF'],
                                        'Kurang Lancar' => ['bg' => '#C9A227', 'text' => '#FFFFFF'],
                                        'Tidak Lancar' => ['bg' => '#9B3A3A', 'text' => '#FFFFFF'],
                                    ];
                                    $predikatColor = $predikatColors[$setoran->predikat->value] ?? ['bg' => '#6B7280', 'text' => '#FFFFFF'];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                                      style="background-color:{{ $predikatColor['bg'] }};color:{{ $predikatColor['text'] }};">
                                    {{ $setoran->predikat->value }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $keputusanColors = [
                                        'Lanjut' => ['bg' => '#E8F5F1', 'text' => '#1B5E4F'],
                                        'Ulang' => ['bg' => '#FEE2E2', 'text' => '#9B3A3A'],
                                    ];
                                    $keputusanColor = $keputusanColors[$setoran->keputusan->value] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                      style="background-color:{{ $keputusanColor['bg'] }};color:{{ $keputusanColor['text'] }};">
                                    {{ $setoran->keputusan->value }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="showDetail({{ $setoran->id }})"
                                        class="text-sm font-medium transition-colors"
                                        style="color:#1B5E4F;"
                                        onmouseover="this.style.color='#155441'"
                                        onmouseout="this.style.color='#1B5E4F'">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $role !== 'siswa' ? '7' : '6' }}" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        @if($tanggalMulai || $tanggalSelesai || $filterJenisSetoran || $filterPredikat || $filterKeputusan || $filterKelompokId || $searchSiswa)
                                            Tidak ada data setoran yang sesuai dengan filter
                                        @else
                                            Belum ada data setoran
                                        @endif
                                    </p>
                                    <p class="mt-1 text-xs" style="color:#9CA3AF;">Coba ubah filter atau rentang tanggal</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($setorans->hasPages())
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                {{ $setorans->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- DETAIL MODAL --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    @if($showDetailModal && $detailSetoran)
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif;">
            {{-- Backdrop --}}
            <div class="fixed inset-0 pointer-events-none" wire:click="closeDetail"></div>

            {{-- Modal --}}
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-2xl w-full" style="border:1px solid #E5E7EB;">
                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl" style="background-color:#1B5E4F;">
                        <div>
                            <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">
                                Detail Setoran Hafalan
                            </h3>
                            <p class="mt-1 text-sm" style="color:#E8F5F1;">
                                {{ $detailSetoran->absensi->siswa->nama_lengkap }} - {{ \Carbon\Carbon::parse($detailSetoran->absensi->tanggal)->tgl() }}
                            </p>
                        </div>
                        <button wire:click="closeDetail" class="text-white hover:text-gray-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Content --}}
                    <div class="px-6 py-5 space-y-4">
                        {{-- Ayat Range --}}
                        <div class="rounded-lg p-4" style="background-color:#F9FAFB;">
                            <div class="text-sm mb-1" style="color:#6B7280;">Juz & Surah</div>
                            <div class="text-base font-semibold" style="color:#1A2422;">
                                Juz {{ $detailSetoran->juz->juz_ke }} - {{ $detailSetoran->surah->nama_latin }} ({{ $detailSetoran->surah->nama_arab }})
                            </div>
                            <div class="text-sm mt-2" style="color:#6B7280;">Ayat {{ $detailSetoran->ayat_mulai }} - {{ $detailSetoran->ayat_selesai }}</div>
                        </div>

                        {{-- Jenis & Keputusan --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm mb-2" style="color:#6B7280;">Jenis Setoran</div>
                                @php
                                    $jenisColors = [
                                        'Hafalan Baru' => ['bg' => '#FEF3C7', 'text' => '#C9A227'],
                                        'Ulang' => ['bg' => '#E0F2FE', 'text' => '#0369A1'],
                                    ];
                                    $jenisColor = $jenisColors[$detailSetoran->jenis_setoran->value] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                      style="background-color:{{ $jenisColor['bg'] }};color:{{ $jenisColor['text'] }};">
                                    {{ $detailSetoran->jenis_setoran->value }}
                                </span>
                            </div>
                            <div>
                                <div class="text-sm mb-2" style="color:#6B7280;">Keputusan</div>
                                @php
                                    $keputusanColors = [
                                        'Lanjut' => ['bg' => '#E8F5F1', 'text' => '#1B5E4F'],
                                        'Ulang' => ['bg' => '#FEE2E2', 'text' => '#9B3A3A'],
                                    ];
                                    $keputusanColor = $keputusanColors[$detailSetoran->keputusan->value] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                      style="background-color:{{ $keputusanColor['bg'] }};color:{{ $keputusanColor['text'] }};">
                                    {{ $detailSetoran->keputusan->value }}
                                </span>
                            </div>
                        </div>

                        {{-- 4 Komponen Penilaian --}}
                        <div>
                            <div class="text-sm font-medium mb-3" style="color:#1A2422;">Komponen Penilaian</div>
                            <div class="grid grid-cols-2 gap-3">
                                @php
                                    $skorColors = [
                                        'Lancar' => ['bg' => '#1B5E4F', 'text' => '#FFFFFF'],
                                        'Kurang Lancar' => ['bg' => '#C9A227', 'text' => '#FFFFFF'],
                                        'Tidak Lancar' => ['bg' => '#9B3A3A', 'text' => '#FFFFFF'],
                                    ];
                                @endphp

                                {{-- Tajwid --}}
                                <div class="rounded-lg p-3" style="border:1px solid #E5E7EB;">
                                    <div class="text-xs mb-1" style="color:#6B7280;">Tajwid</div>
                                    @php
                                        $skorColor = $skorColors[$detailSetoran->skor_tajwid->value] ?? ['bg' => '#6B7280', 'text' => '#FFFFFF'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color:{{ $skorColor['bg'] }};color:{{ $skorColor['text'] }};">
                                        {{ $detailSetoran->skor_tajwid->value }}
                                    </span>
                                </div>

                                {{-- Kelancaran --}}
                                <div class="rounded-lg p-3" style="border:1px solid #E5E7EB;">
                                    <div class="text-xs mb-1" style="color:#6B7280;">Kelancaran</div>
                                    @php
                                        $skorColor = $skorColors[$detailSetoran->skor_kelancaran->value] ?? ['bg' => '#6B7280', 'text' => '#FFFFFF'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color:{{ $skorColor['bg'] }};color:{{ $skorColor['text'] }};">
                                        {{ $detailSetoran->skor_kelancaran->value }}
                                    </span>
                                </div>

                                {{-- Makhraj --}}
                                <div class="rounded-lg p-3" style="border:1px solid #E5E7EB;">
                                    <div class="text-xs mb-1" style="color:#6B7280;">Makhraj</div>
                                    @php
                                        $skorColor = $skorColors[$detailSetoran->skor_makhraj->value] ?? ['bg' => '#6B7280', 'text' => '#FFFFFF'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color:{{ $skorColor['bg'] }};color:{{ $skorColor['text'] }};">
                                        {{ $detailSetoran->skor_makhraj->value }}
                                    </span>
                                </div>

                                {{-- Fashahah --}}
                                <div class="rounded-lg p-3" style="border:1px solid #E5E7EB;">
                                    <div class="text-xs mb-1" style="color:#6B7280;">Fashahah</div>
                                    @php
                                        $skorColor = $skorColors[$detailSetoran->skor_fashahah->value] ?? ['bg' => '#6B7280', 'text' => '#FFFFFF'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color:{{ $skorColor['bg'] }};color:{{ $skorColor['text'] }};">
                                        {{ $detailSetoran->skor_fashahah->value }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Predikat Akhir --}}
                        <div class="rounded-lg p-4" style="background-color:#F9FAFB;">
                            <div class="text-sm mb-2" style="color:#6B7280;">Predikat Akhir</div>
                            @php
                                $predikatColors = [
                                    'Lancar' => ['bg' => '#1B5E4F', 'text' => '#FFFFFF'],
                                    'Kurang Lancar' => ['bg' => '#C9A227', 'text' => '#FFFFFF'],
                                    'Tidak Lancar' => ['bg' => '#9B3A3A', 'text' => '#FFFFFF'],
                                ];
                                $predikatColor = $predikatColors[$detailSetoran->predikat->value] ?? ['bg' => '#6B7280', 'text' => '#FFFFFF'];
                            @endphp
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-bold"
                                  style="background-color:{{ $predikatColor['bg'] }};color:{{ $predikatColor['text'] }};">
                                {{ $detailSetoran->predikat->value }}
                            </span>
                            <p class="text-xs mt-2" style="color:#9CA3AF;">Predikat dihitung dari skor terendah komponen penilaian</p>
                        </div>

                        {{-- Catatan Guru --}}
                        @if($detailSetoran->catatan)
                            <div>
                                <div class="text-sm font-medium mb-2" style="color:#1A2422;">Catatan Guru</div>
                                <div class="rounded-lg p-3 text-sm" style="background-color:#F9FAFB;color:#1A2422;">
                                    {{ $detailSetoran->catatan }}
                                </div>
                            </div>
                        @endif

                        {{-- Saran Ayat Berikutnya --}}
                        @if($detailSetoran->saran_juz_id)
                            <div class="rounded-lg p-3" style="background-color:#E0F2FE;">
                                <div class="text-xs mb-1" style="color:#0C4A6E;">Saran Ayat Berikutnya</div>
                                <div class="text-sm font-medium" style="color:#0C4A6E;">
                                    Juz {{ $detailSetoran->saranJuz->juz_ke }} - {{ $detailSetoran->saranSurah->nama_latin }}, Ayat {{ $detailSetoran->saran_ayat_mulai }}
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="flex justify-end px-6 py-4 rounded-b-2xl" style="background-color:#F9FAFB;border-top:1px solid #E5E7EB;">
                        <button
                            wire:click="closeDetail"
                            type="button"
                            class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                            style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
