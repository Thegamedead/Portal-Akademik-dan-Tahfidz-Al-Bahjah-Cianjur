<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- PAGE HEADER --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Riwayat Tahfidz Terpadu
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Histori lengkap kehadiran dan hasil setoran siswa (Satu Kesatuan)
        </p>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- SEARCH AND FILTER ROW --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        {{-- Quick Rentang Filters --}}
        <div class="flex items-center gap-2 mb-4">
            <button wire:click="setFilterRentang('hari_ini')" class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors {{ $tanggalMulai == now()->format('Y-m-d') && $tanggalSelesai == now()->format('Y-m-d') ? 'bg-[#1B5E4F] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Per Hari Ini</button>
            <button wire:click="setFilterRentang('minggu_ini')" class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors {{ $tanggalMulai == now()->startOfWeek()->format('Y-m-d') && $tanggalSelesai == now()->endOfWeek()->format('Y-m-d') ? 'bg-[#1B5E4F] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Per Minggu Ini</button>
            <button wire:click="setFilterRentang('bulan_ini')" class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors {{ $tanggalMulai == now()->startOfMonth()->format('Y-m-d') && $tanggalSelesai == now()->endOfMonth()->format('Y-m-d') ? 'bg-[#1B5E4F] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Per Bulan Ini</button>
        </div>

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
                        $activeFilters = collect([$tanggalMulai, $tanggalSelesai, $filterKelompokId, $filterStatus])->filter()->count();
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

                <div x-show="open"
                     @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute z-50 right-0 mt-2 w-80 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                     style="display: none; border: 1px solid #E5E7EB;">

                    <div class="p-4 space-y-3">
                        {{-- Filter Header --}}
                        <div class="flex items-center justify-between pb-2" style="border-bottom: 1px solid #E5E7EB;">
                            <h3 class="text-sm font-semibold" style="color:#1A2422;font-family:'Plus Jakarta Sans',sans-serif;">
                                Filter Riwayat Absensi
                            </h3>
                            @if($tanggalMulai || $tanggalSelesai || $filterKelompokId || $filterStatus)
                                <button
                                    wire:click="resetFilters"
                                    type="button"
                                    class="text-xs font-medium text-gray-500 hover:text-[#1B5E4F] transition-colors"
                                >
                                    Reset
                                </button>
                            @endif
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

                        {{-- Status Filter --}}
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Status Kehadiran
                            </label>
                            <select wire:model.live="filterStatus"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                <option value="">Semua</option>
                                @foreach($statusOptions as $status)
                                    <option value="{{ $status->value }}">{{ $status->value }}</option>
                                @endforeach
                            </select>
                        </div>
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
                        <th class="px-4 py-4 text-left text-xs font-semibold" style="color:white;">Jam Sesi</th>
                        @if($role !== 'siswa')
                            <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">NIS</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Nama Siswa</th>
                        @endif
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Kelompok</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Hasil Setoran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Guru Pengisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    @php
                        $currentDate = null;
                    @endphp
                    @forelse($absensis as $absensi)
                        @php
                            $absensiDate = \Carbon\Carbon::parse($absensi->tanggal)->format('Y-m-d');
                        @endphp
                        
                        @if($currentDate !== $absensiDate)
                            <tr style="background-color: #F8FAFC;">
                                <td colspan="{{ $role !== 'siswa' ? '7' : '5' }}" class="px-6 py-3 border-b border-gray-200">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#1B5E4F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="font-bold text-[#1A2422] text-sm">
                                            {{ \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->translatedFormat('l, d F Y') }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            @php
                                $currentDate = $absensiDate;
                            @endphp
                        @endif

                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 text-xs" style="color:#6B7280;">
                                @if($absensi->waktu_mulai && $absensi->waktu_selesai)
                                    <span class="font-mono">{{ $absensi->waktu_mulai->format('H:i') }} - {{ $absensi->waktu_selesai->format('H:i') }}</span>
                                @elseif($absensi->waktu_mulai)
                                    <span class="font-mono">{{ $absensi->waktu_mulai->format('H:i') }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            @if($role !== 'siswa')
                                <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                    {{ $absensi->siswa->nis }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium" style="color:#1A2422;">
                                    {{ $absensi->siswa->nama }}
                                </td>
                            @endif
                            <td class="px-6 py-4 text-sm" style="color:#6B7280;">
                                {{ $absensi->kelompok->nama }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusColors = [
                                        'Hadir' => ['bg' => '#E8F5F1', 'text' => '#1B5E4F'],
                                        'Izin' => ['bg' => '#E8EEF2', 'text' => '#5B7A8C'],
                                        'Sakit' => ['bg' => '#E8EEF2', 'text' => '#5B7A8C'],
                                        'Alfa' => ['bg' => '#FEE2E2', 'text' => '#9B3A3A'],
                                    ];
                                    $colors = $statusColors[$absensi->status_kehadiran->value] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                      style="background-color:{{ $colors['bg'] }};color:{{ $colors['text'] }};">
                                    {{ $absensi->status_kehadiran->value }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($absensi->setorans->isNotEmpty())
                                    <div class="space-y-1.5">
                                        @foreach($absensi->setorans as $setoran)
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-800">{{ $setoran->surah->nama_latin ?? 'Surat' }} : {{ $setoran->ayat_mulai }} - {{ $setoran->ayat_selesai }}</span>
                                                <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $setoran->keputusan === 'Lanjut' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $setoran->predikat }} ({{ $setoran->keputusan }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Belum ada setoran</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm" style="color:#6B7280;">
                                @if($absensi->override_admin)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800" title="Diisi/Diubah oleh Admin: {{ $absensi->admin->nama_lengkap ?? 'Admin' }}">
                                        Admin (Override)
                                    </span>
                                @else
                                    {{ $absensi->guruPengisi->nama_lengkap ?? '-' }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $role !== 'siswa' ? '7' : '5' }}" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        @if($tanggalMulai || $tanggalSelesai || $filterKelompokId || $filterStatus || $searchSiswa)
                                            Tidak ada data absensi yang sesuai dengan filter
                                        @else
                                            Belum ada data absensi
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
        @if($absensis->hasPages())
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                {{ $absensis->links() }}
            </div>
        @endif
    </div>
</div>
