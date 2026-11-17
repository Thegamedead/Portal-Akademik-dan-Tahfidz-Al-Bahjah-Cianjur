<div class="space-y-6">

    {{-- ══════════════════ HEADER ELEGANT & COMPACT ══════════════════ --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_2px_15px_-3px_rgba(6,81,237,0.05)] p-4 sm:p-5 relative overflow-hidden">
        {{-- Subtle decorative background --}}
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-full blur-2xl opacity-70 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-24 h-24 bg-gradient-to-tr from-blue-50 to-indigo-50 rounded-full blur-2xl opacity-60 pointer-events-none"></div>

        <div class="relative flex flex-col xl:flex-row xl:items-center justify-between gap-4 z-10">
            {{-- Kiri: Info Kelompok --}}
            <div class="flex-1">
                <div class="flex items-center gap-2.5 mb-1.5 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 font-jakarta leading-none tracking-tight">Detail {{ $kelompok->nama }}</h1>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wide bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/10">{{ $kelompok->tingkat }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wide bg-amber-50 text-amber-600 ring-1 ring-inset ring-amber-600/10">{{ $kelompok->jenis }}</span>
                </div>
                <p class="text-xs sm:text-sm text-gray-500 font-inter mb-2">
                    Manajemen absensi, setoran, dan progres capaian hafalan kelompok
                </p>

                <div class="flex items-center gap-3 text-xs sm:text-sm text-gray-500 font-medium">
                    <span class="flex items-center gap-1.5 text-gray-600">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $kelompok->guruPembimbing->nama ?? '—' }}
                    </span>
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                    <span class="flex items-center gap-1.5 text-gray-600">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <strong class="text-blue-700">{{ $kelompok->siswas->count() }}</strong>
                        <span class="text-gray-600">Siswa Terdaftar</span>
                    </span>
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                    <span class="flex items-center gap-1.5 text-gray-600">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <strong class="text-green-700">{{ count($alumniSiswaIds) }}</strong>
                        <span class="text-gray-600">Siswa Lulus</span>
                    </span>
                </div>
            </div>

            {{-- Kanan: Statistik & Action --}}
            <div class="flex items-center gap-3 sm:gap-4 shrink-0 overflow-x-auto pb-1 sm:pb-0">

                {{-- Target Card --}}
                <div class="flex flex-col justify-center px-4 py-2.5 rounded-xl min-w-[140px] shadow-md border-2" style="background-color: #ffffff; border-color: #d1d5db;">
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider mb-0.5" style="color: #6b7280;">Target Capaian</span>
                    <span class="text-sm sm:text-base font-extrabold leading-none" style="color: #1f2937;">{{ $targetJuzLabel }}</span>
                    <span class="text-[11px] sm:text-xs leading-tight mt-1" style="color: #4b5563;">{{ number_format($targetAyatTingkat, 0, ',', '.') }} ayat</span>
                </div>

                {{-- Action Kembali --}}
                <a href="{{ route('admin.master-data.kelompok.index') }}"
                   class="shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-full bg-[#1B5E4F] hover:bg-[#155441] text-white transition-all shadow-md group ml-1 sm:ml-2"
                   title="Kembali ke Daftar Kelompok">
                    <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════════ TABS ══════════════════ --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm" style="overflow: visible;">

        {{-- Tab Navigation --}}
        <div class="flex border-b-2 border-gray-300 bg-gray-100 px-2 pt-2 gap-1">
            <button wire:click="setTab('absensi')"
                    class="px-6 py-3 text-sm font-bold transition-all rounded-t-lg {{ $activeTab === 'absensi' ? 'bg-[#1B5E4F] text-white shadow-[0_-3px_0_0_#155441] translate-y-[2px] -mb-[2px]' : 'bg-white border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    Absensi & Hafalan
                </span>
            </button>
            <button wire:click="setTab('progres')"
                    class="px-6 py-3 text-sm font-bold transition-all rounded-t-lg {{ $activeTab === 'progres' ? 'bg-[#1B5E4F] text-white shadow-[0_-3px_0_0_#155441] translate-y-[2px] -mb-[2px]' : 'bg-white border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    Progres Hafalan
                </span>
            </button>
            <button wire:click="setTab('alumni')"
                    class="px-6 py-3 text-sm font-bold transition-all rounded-t-lg {{ $activeTab === 'alumni' ? 'bg-[#1B5E4F] text-white shadow-[0_-3px_0_0_#155441] translate-y-[2px] -mb-[2px]' : 'bg-white border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Riwayat Kelulusan
                </span>
            </button>
        </div>

        {{-- ───── TAB: ABSENSI MATRIX ───── --}}
        @if($activeTab === 'absensi')
            <div class="p-5 space-y-4 overflow-visible">

                {{-- Toolbar: bulan picker + tombol input + keterangan + info --}}
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-4">
                    {{-- Kiri: Bulan picker + Input Absensi --}}
                    <div class="flex items-end gap-3">
                        <div class="flex items-end gap-2">
                            <div>
                                <label class="text-xs font-medium text-gray-400 uppercase tracking-wider block mb-1">Bulan & Tahun</label>
                                <input type="month" wire:model.live="bulanTahun"
                                       class="text-sm font-semibold text-[#1A2422] border border-gray-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F]/30 focus:border-[#1B5E4F] bg-white h-[36px] cursor-pointer" />
                            </div>
                        </div>
                        <button wire:click="$dispatch('openInputAbsensi', { kelompokId: {{ $kelompok->id }} })"
                           class="inline-flex items-center gap-2 px-4 py-1.5 h-[36px] text-sm font-semibold rounded-lg text-white bg-[#1B5E4F] hover:bg-[#155441] transition-colors shadow-sm whitespace-nowrap cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Input Absensi
                        </button>
                    </div>

                    {{-- Kanan: Keterangan + Tombol Info --}}
                    <div class="flex items-start gap-3 flex-wrap">
                        <div class="flex items-start gap-2">
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest pt-1">Keterangan:</span>
                            <div class="flex flex-col items-center gap-0.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold border" style="background:#16a34a;color:#fff;border-color:#15803d;">
                                    <span class="font-black">H</span> Hadir + Lanjut
                                </span>
                                <span class="text-[11px] font-bold text-green-700">{{ $countHadirLanjut }}</span>
                            </div>
                            <div class="flex flex-col items-center gap-0.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold border" style="background:#ea580c;color:#fff;border-color:#c2410c;">
                                    <span class="font-black">H</span> Hadir + Mengulang
                                </span>
                                <span class="text-[11px] font-bold text-orange-700">{{ $countHadirMengulang }}</span>
                            </div>
                            <div class="flex flex-col items-center gap-0.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold border" style="background:#e5e7eb;color:#6b7280;border-color:#d1d5db;">
                                    <span class="font-black">H</span> Hadir (Kosong)
                                </span>
                                <span class="text-[11px] font-bold text-gray-600">{{ $countHadirKosong }}</span>
                            </div>
                        </div>
                        <button x-data="{ open: false }" @click="open = !open" @click.away="open = false"
                                class="relative inline-flex items-center justify-center w-8 h-8 rounded-lg border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 transition-colors"
                                title="Tips Pengisian">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>

                            {{-- Dropdown Tips --}}
                            <div x-show="open" x-transition.opacity.duration.200ms
                                 style="display: none;"
                                 class="absolute z-50 right-0 top-full mt-2 w-[420px] bg-gray-100 rounded-lg shadow-2xl border-2 border-gray-300 overflow-hidden text-left">
                                <div class="p-3">
                                    <div class="flex gap-3">
                                        <!-- Input Massal -->
                                        <div class="flex-1 bg-white rounded-lg p-2.5 shadow-md border-2 border-blue-200">
                                            <div class="flex items-center gap-2 mb-1">
                                                <div class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-500 text-white font-bold text-[9px] shrink-0">1</div>
                                                <h5 class="text-[10px] font-bold text-gray-900">Input Massal</h5>
                                            </div>
                                            <p class="text-[9px] text-gray-700 leading-snug">Klik header tanggal untuk mengisi seluruh siswa</p>
                                        </div>

                                        <!-- Input Individu -->
                                        <div class="flex-1 bg-white rounded-lg p-2.5 shadow-md border-2 border-green-200">
                                            <div class="flex items-center gap-2 mb-1">
                                                <div class="flex items-center justify-center w-5 h-5 rounded-full bg-green-500 text-white font-bold text-[9px] shrink-0">2</div>
                                                <h5 class="text-[10px] font-bold text-gray-900">Input Individu</h5>
                                            </div>
                                            <p class="text-[9px] text-gray-700 leading-snug">Klik cell siswa untuk mengisi per siswa</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                {{-- Judul Tabel + Rekap & Navigasi --}}
                @php
                    $bulanTahunObj = \Carbon\Carbon::parse($bulanTahun);
                    $namaBulan = $bulanTahunObj->locale('id')->translatedFormat('F');
                    $tahun = $bulanTahunObj->format('Y');
                @endphp
                <div class="mb-4 space-y-3">
                    {{-- Baris 1: Judul Tabel (Tengah) --}}
                    <div class="text-center">
                        <h3 class="text-sm font-bold text-gray-900 leading-tight">Absensi dan Hafalan</h3>
                        <p class="text-sm font-semibold text-gray-700 mt-0.5">Bulan {{ $namaBulan }} Tahun {{ $tahun }}</p>
                    </div>

                    {{-- Baris 2: Rekap (Kiri) + Search & Filter (Kanan) --}}
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        {{-- Rekap Bulan Ini (Kiri) --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Rekap Bulan Ini:</span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-md bg-green-50 text-green-700 border border-green-200">
                                Hadir: {{ $attendanceStats['Hadir'] }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-200">
                                Izin: {{ $attendanceStats['Izin'] }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                                Sakit: {{ $attendanceStats['Sakit'] }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-md bg-red-50 text-red-700 border border-red-200">
                                Alfa: {{ $attendanceStats['Alfa'] }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-md bg-purple-50 text-purple-700 border border-purple-200">
                                Libur: {{ $attendanceStats['Libur'] ?? 0 }}
                            </span>
                        </div>

                        {{-- Search & Filter + Navigasi (Kanan) --}}
                        <div class="flex items-center gap-3 shrink-0">
                            {{-- Search Input --}}
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.300ms="searchSiswa"
                                       placeholder="Cari siswa (NIS, NISN, Nama)..."
                                       class="pl-10 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1B5E4F]/30 focus:border-[#1B5E4F] bg-white w-64">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>

                            {{-- Filter Dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button
                                    @click="open = !open"
                                    type="button"
                                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-colors focus:outline-none whitespace-nowrap cursor-pointer"
                                    style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                    </svg>
                                    Filter
                                    @if($filterKehadiran || $filterSetoran)
                                        <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                            {{ collect([$filterKehadiran, $filterSetoran])->filter()->count() }}
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
                                        <div class="flex items-center justify-between pb-2" style="border-bottom: 1px solid #E5E7EB;">
                                            <h3 class="text-sm font-semibold" style="color:#1A2422;font-family:'Plus Jakarta Sans',sans-serif;">
                                                Filter Siswa
                                            </h3>
                                            @if($filterKehadiran || $filterSetoran)
                                                <button
                                                    wire:click="resetFilters"
                                                    type="button"
                                                    class="text-xs font-medium text-gray-500 hover:text-[#1B5E4F] transition-colors">
                                                    Reset
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Filter Status Kehadiran --}}
                                        <div>
                                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                                Status Kehadiran
                                            </label>
                                            <select
                                                wire:model.live="filterKehadiran"
                                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                                style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                                <option value="">Semua</option>
                                                <option value="Hadir">Hadir</option>
                                                <option value="Izin">Izin</option>
                                                <option value="Sakit">Sakit</option>
                                                <option value="Alfa">Alfa</option>
                                                <option value="Libur">Libur</option>
                                            </select>
                                        </div>

                                        {{-- Filter Setoran --}}
                                        <div>
                                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                                Status Setoran
                                            </label>
                                            <select
                                                wire:model.live="filterSetoran"
                                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                                style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                                <option value="">Semua</option>
                                                <option value="ada_setoran">Ada Setoran</option>
                                                <option value="tidak_ada_setoran">Tidak Ada Setoran</option>
                                                <option value="lanjut">Lanjut (Hafalan Baru)</option>
                                                <option value="mengulang">Mengulang</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Navigasi Bulan --}}
                            <button wire:click="previousMonth"
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 hover:text-gray-900 transition-colors"
                                    title="Bulan Sebelumnya">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button wire:click="nextMonth"
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 hover:text-gray-900 transition-colors"
                                    title="Bulan Berikutnya">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Matrix Table --}}
                @if(empty($matrixDates))
                    <div class="py-12 text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-sm text-gray-400 italic">Belum ada sesi absensi pada bulan ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-xl border border-gray-200 pb-32">
                        <style>
                            .matrix-table th, .matrix-table td {
                                border: 1px solid #e5e7eb; /* gray-200 */
                            }
                            .matrix-table thead th {
                                border: 1px solid #134e40; /* slightly darker green than header */
                            }
                            .matrix-table tfoot td {
                                border-top: 2px solid #bbf7d0;
                            }
                        </style>
                        <table class="min-w-full text-xs matrix-table" style="font-family:'Inter',sans-serif; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color:#1B5E4F;">
                                    <th class="px-3 py-2.5 text-left text-[10px] font-semibold text-white w-8 sticky left-0 z-30" style="background:#1B5E4F;">No</th>
                                    <th class="px-2 py-2.5 text-left text-[10px] font-semibold text-white min-w-[100px] sticky left-8 z-30" style="background:#1B5E4F;">Nama Siswa</th>
                                    @foreach($matrixDates as $col)
                                        @php
                                            $d = \Carbon\Carbon::parse($col['date']);
                                            $isLiburDate = isset($liburDates[$col['date']]);

                                            // Singkatan hari custom
                                            $dayMap = [
                                                'Sun' => 'Mg',
                                                'Mon' => 'Sn',
                                                'Tue' => 'Sl',
                                                'Wed' => 'Rb',
                                                'Thu' => 'Km',
                                                'Fri' => 'Jm',
                                                'Sat' => 'Sb',
                                            ];
                                            $dayShort = $dayMap[$d->format('D')] ?? $d->format('D');
                                        @endphp
                                        @if($col['isSunday'])
                                            <th class="px-1 py-1.5 text-center text-[10px] font-semibold min-w-[52px] relative z-10" style="background:#dc2626;color:#fca5a5;">
                                                <div class="flex flex-col items-center justify-center w-full py-0.5">
                                                    <div>{{ $dayShort }}</div>
                                                    <div class="text-[11px] font-bold">{{ $d->format('d') }}</div>
                                                    <div class="h-3 mt-0.5"></div>
                                                </div>
                                            </th>
                                        @elseif($isLiburDate)
                                            <th class="px-1 py-1.5 text-center text-[10px] font-semibold min-w-[52px] relative" :class="open ? 'z-50' : 'z-10'" style="background:#dc2626;color:#fca5a5;" x-data="{ open: false }" @click.away="open = false">
                                                <button type="button" @click="open = !open" class="flex flex-col items-center justify-center w-full hover:bg-white/10 rounded transition-colors py-0.5 focus:outline-none">
                                                    <div>{{ $dayShort }}</div>
                                                    <div class="text-[11px] font-bold">{{ $d->format('d') }}</div>
                                                    <div class="mt-0.5 h-3">
                                                        <span class="text-[9px] font-bold text-red-200">LIBUR</span>
                                                    </div>
                                                </button>
                                                {{-- Dropdown Menu --}}
                                                <div x-show="open" x-transition.opacity.duration.200ms
                                                     style="display: none;"
                                                     class="absolute z-[70] mt-1 w-28 bg-white rounded shadow-lg border border-gray-100 left-1/2 -translate-x-1/2 top-full text-left overflow-hidden font-normal text-gray-800">
                                                    <button type="button" @click="$dispatch('openInputAbsensi', { kelompokId: {{ $kelompok->id }}, tanggal: '{{ $col['date'] }}' }); open = false" class="w-full text-left px-3 py-2 text-[10px] font-semibold text-blue-600 hover:bg-blue-50 transition-colors border-b border-gray-50 flex items-center gap-1.5">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                        Edit
                                                    </button>
                                                    <button type="button" @click="$dispatch('dialog-open', { type: 'confirm', title: 'Hapus Sesi Absensi', message: 'Hapus semua data libur tanggal {{ $d->tgl() }}?', confirmText: 'Ya, Hapus', cancelText: 'Batal', confirmColor: 'red', noBackdrop: true, payload: { action: 'doHapusSesiAbsensi', data: '{{ $col['date'] }}' } }); open = false" class="w-full text-left px-3 py-2 text-[10px] font-semibold text-red-600 hover:bg-red-50 transition-colors flex items-center gap-1.5">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        Hapus
                                                    </button>
                                                </div>
                                            </th>
                                        @else
                                            <th class="px-1 py-1.5 text-center text-[10px] font-semibold text-white min-w-[52px] relative" :class="open ? 'z-50' : 'z-10'" x-data="{ open: false }" @click.away="open = false">
                                                <button type="button" @click="open = !open" class="flex flex-col items-center justify-center w-full hover:bg-white/10 rounded transition-colors py-0.5 focus:outline-none">
                                                    <div>{{ $dayShort }}</div>
                                                    <div class="text-[11px] font-bold">{{ $d->format('d') }}</div>
                                                    @if(isset($matrixPivot) && collect($matrixPivot)->contains(fn($dates) => isset($dates[$col['date']])))
                                                        <div class="flex items-center justify-center mt-0.5 h-3">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                        </div>
                                                    @else
                                                        <div class="h-3 mt-0.5"></div> {{-- spacer to keep height consistent --}}
                                                    @endif
                                                </button>

                                                {{-- Dropdown Menu --}}
                                                <div x-show="open" x-transition.opacity.duration.200ms
                                                     style="display: none;"
                                                     class="absolute z-[70] mt-1 w-28 bg-white rounded shadow-lg border border-gray-100 left-1/2 -translate-x-1/2 top-full text-left overflow-hidden font-normal text-gray-800">
                                                    @if(isset($matrixPivot) && collect($matrixPivot)->contains(fn($dates) => isset($dates[$col['date']])))
                                                        <button type="button" @click="$dispatch('openInputAbsensi', { kelompokId: {{ $kelompok->id }}, tanggal: '{{ $col['date'] }}' }); open = false" class="w-full text-left px-3 py-2 text-[10px] font-semibold text-blue-600 hover:bg-blue-50 transition-colors border-b border-gray-50 flex items-center gap-1.5">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                            Edit
                                                        </button>
                                                        <button type="button" @click="$dispatch('dialog-open', { type: 'confirm', title: 'Hapus Sesi Absensi', message: 'Hapus semua absensi & setoran tanggal {{ $d->tgl() }}? Data tidak bisa dikembalikan.', confirmText: 'Ya, Hapus', cancelText: 'Batal', confirmColor: 'red', noBackdrop: true, payload: { action: 'doHapusSesiAbsensi', data: '{{ $col['date'] }}' } }); open = false" class="w-full text-left px-3 py-2 text-[10px] font-semibold text-red-600 hover:bg-red-50 transition-colors flex items-center gap-1.5">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                            Hapus
                                                        </button>
                                                    @else
                                                        <button type="button" @click="$dispatch('openInputAbsensi', { kelompokId: {{ $kelompok->id }}, tanggal: '{{ $col['date'] }}' }); open = false" class="w-full text-left px-3 py-2 text-[10px] font-semibold text-emerald-600 hover:bg-emerald-50 transition-colors flex items-center gap-1.5">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                            Isi Absensi
                                                        </button>
                                                    @endif
                                                </div>
                                            </th>
                                        @endif
                                    @endforeach
                                    <th class="px-3 py-2.5 text-center text-[10px] font-semibold text-white min-w-[54px] sticky right-[120px] z-30" style="background:#0f4a3a;">Total</th>
                                    <th class="px-3 py-2.5 text-center text-[10px] font-semibold text-white min-w-[120px] sticky right-0 z-30" style="background:#0d3d30;">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @php
                                    $statusMap = [
                                        'Hadir' => ['label' => 'H', 'bg' => '#16a34a', 'text' => '#fff'],
                                        'Izin'  => ['label' => 'I', 'bg' => '#2563eb', 'text' => '#fff'],
                                        'Sakit' => ['label' => 'S', 'bg' => '#d97706', 'text' => '#fff'],
                                        'Alfa'  => ['label' => 'A', 'bg' => '#dc2626', 'text' => '#fff'],
                                        'Libur' => ['label' => 'L', 'bg' => '#7c3aed', 'text' => '#fff'],
                                    ];
                                    $totalPerDate = []; $totalSetoranPerDate = []; $totalAyatPerDate = []; $totalPerSiswa = []; $liburDates = [];
                                    // $alumniSiswaIds sudah di-load dari component DashboardKelompok
                                    foreach ($matrixDates as $col) {
                                        if ($col['isSunday']) continue;
                                        $cnt = 0; $stot = 0; $ayatTot = 0; $liburCnt = 0; $otherCnt = 0; $totalSiswaCount = $kelompok->siswas->count();
                                        foreach ($kelompok->siswas as $s) {
                                            $c = $matrixPivot[$s->id][$col['date']] ?? null;
                                            if ($c) {
                                                if (($c['status'] ?? '') === 'Hadir') {
                                                    $cnt++;
                                                }
                                                if (($c['status'] ?? '') === 'Libur') {
                                                    $liburCnt++;
                                                } else {
                                                    $otherCnt++;
                                                    $stot += count($c['setorans'] ?? []);
                                                    foreach ($c['setorans'] ?? [] as $st) {
                                                        if (($st['keputusan'] ?? '') !== 'Ulang') {
                                                            $ayatTot += max(0, (int)($st['selesai'] ?? 0) - (int)($st['mulai'] ?? 0) + 1);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $totalPerDate[$col['date']] = $cnt;
                                        $totalSetoranPerDate[$col['date']] = $stot;
                                        $totalAyatPerDate[$col['date']] = $ayatTot;
                                        // Mark as libur date if there are Libur records and no other statuses
                                        if ($liburCnt > 0 && $otherCnt === 0) {
                                            $liburDates[$col['date']] = true;
                                        }
                                    }
                                    foreach ($kelompok->siswas as $s) {
                                        $hadirCnt = 0;
                                        foreach ($matrixDates as $col) {
                                            if (!$col['isSunday'] && isset($matrixPivot[$s->id][$col['date']])) {
                                                if (($matrixPivot[$s->id][$col['date']]['status'] ?? '') === 'Hadir') {
                                                    $hadirCnt++;
                                                }
                                            }
                                        }
                                        $totalPerSiswa[$s->id] = $hadirCnt;
                                    }
                                @endphp
                                @foreach($kelompok->siswas as $i => $siswa)
                                    @php
                                        $rowBg = $i % 2 === 0 ? '#ffffff' : '#f9fafb';
                                        $tanggalTuntas = $tanggalTuntasPerSiswa[$siswa->id] ?? null;
                                        $isTuntas = $tanggalTuntas !== null;

                                        // Cek apakah siswa tuntas di bulan yang sedang dilihat
                                        $tuntasDiBulanIni = false;
                                        if ($tanggalTuntas) {
                                            [$yearView, $monthView] = explode('-', $bulanTahun);
                                            $tuntasDate = \Carbon\Carbon::parse($tanggalTuntas);
                                            $tuntasDiBulanIni = ($tuntasDate->year == $yearView && $tuntasDate->month == $monthView);
                                        }
                                    @endphp
                                    <tr class="hover:bg-[#F0FAF7] transition-colors">
                                        <td class="px-3 py-2.5 text-gray-400 text-center sticky left-0 z-10" style="background:{{ $rowBg }}">{{ $i + 1 }}</td>
                                        <td class="px-2 py-2.5 font-semibold text-[#1A2422] sticky left-8 z-10 text-[9px]" style="background:{{ $rowBg }}">{{ $siswa->nama }}</td>

                                        @foreach($matrixDates as $col)
                                            @if($col['isSunday'])
                                                <td class="px-1 py-2 text-center" style="background:#fca5a5; height: 42px;">
                                                    <span class="text-[9px] text-red-600 font-bold">—</span>
                                                </td>
                                            @elseif(isset($liburDates[$col['date']]))
                                                <td class="px-1 py-2 text-center" style="background:#fca5a5; height: 42px;">
                                                    <span class="text-[9px] font-bold text-red-600">L</span>
                                                </td>
                                            @else
                                                @php
                                                    $isValidDate = isset($validSiswaDates[$siswa->id][$col['date']]);
                                                    $cell = $matrixPivot[$siswa->id][$col['date']] ?? null;
                                                    $sc = $cell ? ($statusMap[$cell['status']] ?? ['label'=>'?','bg'=>'#f3f4f6','text'=>'#6b7280']) : null;
                                                    $hasSetoran = $cell && !empty($cell['setorans']);

                                                    // Cek apakah siswa sudah menyelesaikan target pada tanggal ini
                                                    $isTargetComplete = isset($targetCompletionMap[$siswa->id][$col['date']]) && $targetCompletionMap[$siswa->id][$col['date']];

                                                    // Cek apakah tanggal ini setelah tanggal tuntas
                                                    $isAfterTuntas = $tanggalTuntas && \Carbon\Carbon::parse($col['date'])->gt(\Carbon\Carbon::parse($tanggalTuntas));

                                                    // Dynamic styling for Hadir
                                                    if ($cell && $cell['status'] === 'Hadir') {
                                                        if (!$hasSetoran) {
                                                            $sc['bg'] = '#e5e7eb';
                                                            $sc['text'] = '#6b7280'; // gray — no setoran
                                                        } else {
                                                            // Check keputusan priority: Lanjut > Ulang
                                                            $hasLanjut = collect($cell['setorans'])->contains(fn($st) => ($st['keputusan'] ?? '') === 'Lanjut');
                                                            $hasUlang = collect($cell['setorans'])->contains(fn($st) => ($st['keputusan'] ?? '') === 'Ulang');

                                                            if ($hasLanjut) {
                                                                $sc['bg'] = '#16a34a';
                                                                $sc['text'] = '#fff'; // bold green — Lanjut (priority)
                                                            } elseif ($hasUlang) {
                                                                $sc['bg'] = '#ea580c';
                                                                $sc['text'] = '#fff'; // bold orange — Ulang
                                                            } else {
                                                                $sc['bg'] = '#16a34a';
                                                                $sc['text'] = '#fff'; // bold green — default for Hadir with setoran
                                                            }
                                                        }
                                                    }

                                                    // Build tooltip with surah name, count, and total ayat
                                                    $tip = '';
                                                    if ($hasSetoran) {
                                                        $jumlahSetoran = count($cell['setorans']);
                                                        $totalAyat = collect($cell['setorans'])->sum('jumlah_ayat');
                                                        $setoranText = collect($cell['setorans'])->map(fn($s) => $s['surah_nama'] . ' ' . $s['mulai'] . '-' . $s['selesai'])->implode(' | ');
                                                        $tip = $setoranText . ' (' . $jumlahSetoran . ' setoran, ' . $totalAyat . ' ayat)';
                                                    }
                                                @endphp
                                                <td class="px-0.5 py-1 text-center align-top relative" style="background:{{ $rowBg }}; height: 42px;">
                                                    @if(!$isValidDate)
                                                        {{-- Cell tidak valid untuk siswa ini pada tanggal ini (belum join atau sudah pindah) --}}
                                                        <div class="w-full h-full flex items-center justify-center" style="background:#f3f4f6;" title="Siswa tidak berada di kelompok ini pada tanggal ini">
                                                            <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                        </div>
                                                    @elseif($isAfterTuntas)
                                                        {{-- Cell setelah tuntas: disabled dengan tanda centang --}}
                                                        <div class="w-full h-full flex items-center justify-center" style="background:#f0fdf4;">
                                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        </div>
                                                    @elseif($cell)
                                                        <div x-data="{ open: false }" @click.away="open = false" :class="open ? 'z-50' : 'z-10'" class="relative inline-flex flex-col items-center gap-0 w-full h-full">
                                                            @php
                                                                // Update tooltip untuk cell yang sudah terisi
                                                                $cellTooltip = $tip ?: $cell['status'];
                                                                if ($isTargetComplete) {
                                                                    $cellTooltip = '✓ Target selesai - ' . $cellTooltip;
                                                                }
                                                            @endphp
                                                            <button @click="open = !open" title="{{ $cellTooltip }}" class="flex flex-col items-center hover:opacity-75 transition-opacity cursor-pointer focus:outline-none w-full h-full">
                                                                {{-- Status Badge --}}
                                                                <span class="inline-flex items-center justify-center w-full h-4 text-[9px] font-bold border-b flex-shrink-0"
                                                                      style="background-color:{{ $sc['bg'] }};color:{{ $sc['text'] }};border-color:rgba(0,0,0,0.1);">{{ $sc['label'] }}</span>

                                                                {{-- Setoran Info --}}
                                                                @if($hasSetoran)
                                                                    <div class="w-full px-0.5 py-1 bg-white flex-1 flex flex-col justify-center gap-0.5 overflow-hidden">
                                                                        @foreach($cell['setorans'] as $st)
                                                                            <div class="text-[3.5px] leading-tight text-gray-700 font-normal truncate" style="font-size: 5.5px; line-height: 1.2;">{{ $st['surah'] }} {{ $st['mulai'] }}-{{ $st['selesai'] }}</div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <div class="w-full px-0.5 py-0.5 bg-white flex-1 flex items-center justify-center">
                                                                        <div class="text-[6px] leading-tight text-gray-400 italic">-</div>
                                                                    </div>
                                                                @endif
                                                            </button>

                                                            {{-- Dropdown Menu --}}
                                                            <div x-show="open" x-transition.opacity.duration.200ms
                                                                 style="display: none;"
                                                                 class="absolute z-[60] mt-1 w-28 bg-white rounded shadow-lg border border-gray-100 left-1/2 -translate-x-1/2 top-full text-left overflow-hidden">
                                                                @if($isTargetComplete)
                                                                    <div class="px-3 py-2 text-[9px] text-green-600 text-center font-medium leading-tight border-b border-green-100 bg-green-50">
                                                                        ✓ Target Selesai
                                                                    </div>
                                                                    <div class="px-3 py-2 text-[8px] text-gray-500 text-center leading-tight">
                                                                        Proses kelulusan di menu Progres Siswa
                                                                    </div>
                                                                @elseif(!in_array($siswa->id, $alumniSiswaIds))
                                                                    <button type="button" @click="$dispatch('openInputAbsensi', { kelompokId: {{ $kelompok->id }}, tanggal: '{{ $col['date'] }}', siswaId: {{ $siswa->id }} }); open = false" class="w-full text-left px-3 py-2 text-[10px] font-semibold text-blue-600 hover:bg-blue-50 transition-colors border-b border-gray-50 flex items-center gap-1.5">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                                        Edit
                                                                    </button>
                                                                    <button type="button" @click="$dispatch('dialog-open', { type: 'confirm', title: 'Hapus Absensi Siswa', message: 'Yakin ingin menghapus data absensi ini?', confirmText: 'Ya, Hapus', cancelText: 'Batal', confirmColor: 'red', noBackdrop: true, payload: { action: 'doHapusAbsensiSiswa', data: { tanggal: '{{ $col['date'] }}', siswaId: {{ $siswa->id }} } } }); open = false" class="w-full text-left px-3 py-2 text-[10px] font-semibold text-red-600 hover:bg-red-50 transition-colors flex items-center gap-1.5">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                        Hapus
                                                                    </button>
                                                                @else
                                                                    <div class="px-3 py-2 text-[9px] text-gray-500 text-center font-medium leading-tight">
                                                                        Data terkunci karena status lulus
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @else
                                                        @if(!in_array($siswa->id, $alumniSiswaIds))
                                                            @if($isTargetComplete)
                                                                {{-- Siswa sudah menyelesaikan target pada tanggal ini atau sebelumnya --}}
                                                                <div class="w-full flex items-center justify-center cursor-not-allowed"
                                                                     style="height: 42px; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);"
                                                                     title="Hafalan Tuntas">
                                                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                </div>
                                                            @else
                                                                <button
                                                                    type="button"
                                                                    @click="$dispatch('openInputAbsensi', { kelompokId: {{ $kelompok->id }}, tanggal: '{{ $col['date'] }}', siswaId: {{ $siswa->id }} })"
                                                                    class="w-full flex items-center justify-center text-gray-200 hover:text-emerald-500 hover:bg-emerald-50 rounded transition-colors focus:outline-none cursor-pointer"
                                                                    style="height: 42px;"
                                                                    title="Isi absensi {{ $siswa->nama }} ({{ \Carbon\Carbon::parse($col['date'])->locale('id')->translatedFormat('d F Y') }})"
                                                                >
                                                                    <svg class="w-3.5 h-3.5 opacity-0 hover:opacity-100 transition-opacity absolute" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                                    <span class="text-base leading-none hover:opacity-0 transition-opacity">·</span>
                                                                </button>
                                                            @endif
                                                        @else
                                                            <div class="w-full flex items-center justify-center text-gray-100 cursor-not-allowed" style="height: 42px;" title="Siswa sudah lulus, tidak bisa input absensi">
                                                                <span class="text-base leading-none">·</span>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </td>
                                            @endif
                                        @endforeach
                                        <td class="px-2 py-1 text-center sticky right-[120px] z-10 bg-gradient-to-b from-green-50 to-emerald-100 border-l border-green-200" style="height: 42px;">
                                            <div class="flex flex-col items-center justify-start h-full pt-0.5">
                                                <div class="text-[9px] font-bold text-[#1B5E4F] leading-tight">{{ $totalPerSiswa[$siswa->id] ?? 0 }}</div>
                                                <div class="w-full border-t border-emerald-300 my-0.5"></div>
                                                <div class="text-[9px] font-bold text-[#0d3d30] leading-tight">{{ $totalAyatMap[$siswa->id] ?? 0 }}</div>
                                            </div>
                                        </td>
                                        <td class="px-2 py-2.5 text-center text-[7px] sticky right-0 z-10 bg-white border-l border-gray-200" style="height: 42px; vertical-align: middle;">
                                            @if($tuntasDiBulanIni)
                                                <div class="flex flex-col items-center justify-center gap-0.5">
                                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[7px] font-bold bg-green-100 text-green-700">
                                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        Tuntas
                                                    </span>
                                                    <span class="text-[6px] text-gray-500">{{ \Carbon\Carbon::parse($tanggalTuntas)->format('d/m/Y') }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background:#f0fdf4;border-top:2px solid #bbf7d0;">
                                    <td colspan="2" class="px-4 py-2 sticky left-0 z-10 font-bold text-[11px] text-[#1B5E4F] uppercase tracking-wide" style="background:#f0fdf4;">Total</td>
                                    @php $grandTotalAyat = 0; @endphp
                                    @foreach($matrixDates as $col)
                                        @if($col['isSunday'])
                                            <td class="px-1 py-2 text-center" style="background:#fca5a5;"><span class="text-red-700 font-bold">—</span></td>
                                        @elseif(isset($liburDates[$col['date']]))
                                            <td class="px-1 py-2 text-center" style="background:#fca5a5;"><span class="text-red-700 font-bold">L</span></td>
                                        @else
                                            @php
                                                $tot = $totalPerDate[$col['date']] ?? 0;
                                                $ayatOnDate = 0;
                                                foreach ($kelompok->siswas as $s) {
                                                    $c2 = $matrixPivot[$s->id][$col['date']] ?? null;
                                                    if ($c2 && ($c2['status'] ?? '') !== 'Libur') {
                                                        foreach ($c2['setorans'] ?? [] as $st2) {
                                                            if (($st2['keputusan'] ?? '') !== 'Ulang') {
                                                                $ayatOnDate += max(0, (int)($st2['selesai'] ?? 0) - (int)($st2['mulai'] ?? 0) + 1);
                                                            }
                                                        }
                                                    }
                                                }
                                                $grandTotalAyat += $ayatOnDate;
                                            @endphp
                                            <td class="px-1 py-1.5 text-center">
                                                <div class="flex flex-col items-center justify-center gap-0.5">
                                                    @if($tot > 0)
                                                        <span class="inline-flex items-center justify-center w-5 h-4 rounded text-[8px] font-bold bg-[#1B5E4F] text-white">{{ $tot }}</span>
                                                    @else
                                                        <span class="text-gray-300 text-[8px]">·</span>
                                                    @endif
                                                    <div class="w-full border-t border-gray-200"></div>
                                                    @if($ayatOnDate > 0)
                                                        <span class="text-[7px] font-bold text-gray-800">{{ $ayatOnDate }}</span>
                                                    @else
                                                        <span class="text-gray-300 text-[7px]">·</span>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    @endforeach
                                    <td class="px-2 py-1 text-center sticky right-[120px] bg-gradient-to-b from-[#1B5E4F] to-[#0d3d30]">
                                        <div class="flex flex-col items-center justify-start gap-0.5 pt-0.5">
                                            <div class="text-[9px] font-bold text-white leading-tight">{{ array_sum($totalPerDate) }}</div>
                                            <div class="w-full border-t border-white/30"></div>
                                            <div class="text-[9px] font-bold text-white leading-tight">{{ $grandTotalAyat }}</div>
                                        </div>
                                    </td>
                                    <td class="px-2 py-2 text-center sticky right-0 bg-gray-50">
                                        <span class="text-[8px] text-gray-400">—</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        @endif

        {{-- ───── TAB: PROGRES PER SISWA ───── --}}
        @if($activeTab === 'progres')
            <div class="p-5 space-y-4">
                <div>
                    <h2 class="text-base font-semibold text-[#1A2422]">Progres & Setoran Per Siswa</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Rekap keseluruhan hafalan masing-masing siswa di kelompok ini (all-time)</p>
                </div>

                <div class="rounded-xl border border-gray-200 overflow-hidden">
                    <style>.progres-table th,.progres-table td{border:1px solid #e5e7eb;}.progres-table thead th{border-color:#134e40;}</style>
                    <table class="min-w-full text-xs progres-table" style="border-collapse:collapse;">
                        <thead>
                            <tr style="background-color:#1B5E4F;">
                                <th class="px-4 py-3 text-center text-[11px] font-semibold text-white w-10">No</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-white">Nama Siswa</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold text-white">Juz</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold text-white">Target Ayat</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold text-white">Ayat Disetorkan</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold text-white">Sisa Ayat</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-white w-52">Progress Capaian</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold text-white">Status</th>
                                <th class="px-4 py-3 text-center text-[11px] font-semibold text-white">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($progresPerSiswa as $i => $item)
                                @php $rowBg = $i % 2 === 0 ? '#ffffff' : '#f9fafb'; @endphp
                                <tr class="hover:bg-[#F0FAF7] transition-colors" style="background:{{ $rowBg }}">
                                    <td class="px-4 py-2.5 text-gray-400 text-center">{{ $i + 1 }}</td>
                                    <td class="px-4 py-2.5 font-semibold text-[#1A2422]">{{ $item['siswa']->nama }}</td>
                                    <td class="px-4 py-2.5 text-center font-semibold text-blue-600">{{ $targetJuzLabel }}</td>
                                    <td class="px-4 py-2.5 text-center font-bold text-gray-500">{{ number_format($item['target_ayat'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        @if($item['ayat_setorkan'] > 0)
                                            <span class="font-bold text-[#1B5E4F]">{{ number_format($item['ayat_setorkan'], 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-center font-bold {{ $item['sisa_ayat'] > 0 ? 'text-amber-600' : 'text-green-600' }}">
                                        {{ $item['sisa_ayat'] > 0 ? number_format($item['sisa_ayat'], 0, ',', '.') : '✓ Tuntas' }}
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                                <div class="h-2.5 rounded-full transition-all duration-700"
                                                    style="width:{{ $item['persentase'] }}%; background: {{ $item['persentase'] >= 100 ? '#16a34a' : ($item['persentase'] >= 50 ? '#1B5E4F' : '#64748b') }};"></div>
                                            </div>
                                            <span class="text-[11px] font-extrabold w-12 text-right {{ $item['persentase'] >= 100 ? 'text-green-600' : 'text-gray-600' }}">{{ $item['persentase'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        @if(!empty($item['is_alumni']))
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 ring-1 ring-amber-300">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                Lulus (Direkomendasikan)
                                            </span>
                                        @elseif($item['persentase'] >= 100)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 ring-1 ring-green-300">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                Lulus Tingkat
                                            </span>
                                        @elseif($item['persentase'] > 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 ring-1 ring-blue-200">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                                Sedang Berlangsung
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 ring-1 ring-gray-200">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                                Belum Mulai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <div class="flex flex-col md:flex-row items-center justify-center gap-1.5">
                                            @php
                                                $trackRecordUrl = auth()->guard('guru')->check()
                                                    ? route('guru.setoran.riwayat-hafalan', $item['siswa']->id) . '?kelompok_id=' . $kelompok->id
                                                    : route('admin.setoran.riwayat-hafalan', $item['siswa']->id) . '?kelompok_id=' . $kelompok->id;
                                            @endphp
                                            <a href="{{ $trackRecordUrl }}"
                                               class="inline-flex items-center justify-center w-full md:w-auto gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white shadow-sm transition-all hover:opacity-90 hover:shadow-md"
                                               style="background:linear-gradient(135deg,#1B5E4F,#2d7a67);" title="Lihat track record hafalan">
                                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                                </svg>
                                                Track Record
                                            </a>

                                            @if($item['persentase'] >= 100 && empty($item['is_alumni']))
                                                @php
                                                    $tingkatKelompok = is_string($kelompok->tingkat) ? $kelompok->tingkat : $kelompok->tingkat->value;
                                                    $isTingkatAtas = $tingkatKelompok === 'Atas';
                                                @endphp
                                                <button type="button" wire:click="$dispatch('openRekomendasiModal', { siswaId: {{ $item['siswa']->id }} })"
                                                        class="inline-flex items-center justify-center w-full md:w-auto gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md animate-pulse-slow"
                                                        style="background: linear-gradient(135deg, {{ $isTingkatAtas ? '#16a34a, #15803d' : '#f59e0b, #d97706' }});"
                                                        title="{{ $isTingkatAtas ? 'Luluskan dari Program Tahfidz' : 'Beri Rekomendasi Naik Tingkat' }}">
                                                    @if($isTingkatAtas)
                                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Lulus Program
                                                    @else
                                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                        </svg>
                                                        Naik Tingkat
                                                    @endif
                                                </button>
                                            @elseif(!empty($item['is_alumni']))
                                                <button type="button" wire:click="batalkanKelulusan({{ $item['siswa']->id }})"
                                                        wire:confirm="Yakin ingin membatalkan kelulusan siswa ini dan mengembalikannya ke kelompok aktif?"
                                                        class="inline-flex items-center justify-center w-full md:w-auto gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md"
                                                        style="background: linear-gradient(135deg, #ef4444, #dc2626);"
                                                        title="Batalkan kelulusan siswa ini">
                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Batal Lulus
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-10 text-center text-sm text-gray-400 italic">Belum ada data setoran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>

    {{-- Modal Input Absensi --}}
@if($isRekomendasiModalOpen)
        @php
            $tingkatKelompok = is_string($kelompok->tingkat) ? $kelompok->tingkat : $kelompok->tingkat->value;
            $isTingkatAtas = $tingkatKelompok === 'Atas';
        @endphp

        {{-- Modal Rekomendasi Naik Tingkat / Lulus Program --}}
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" @click="$wire.closeRekomendasiModal()">

            {{-- Modal content --}}
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl" @click.stop>

                {{-- Modal inner padding --}}
                <div class="p-6">
                    {{-- Header --}}
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full shrink-0" style="background: {{ $isTingkatAtas ? '#dcfce7' : '#fef3c7' }}; color: {{ $isTingkatAtas ? '#16a34a' : '#f59e0b' }};">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($isTingkatAtas)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                @endif
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold leading-tight" style="color:#1A2422;">
                                {{ $isTingkatAtas ? 'Lulus Program Tahfidz' : 'Naik Tingkat' }}
                            </h3>
                            <p class="text-xs" style="color:#6B7280;">
                                {{ $isTingkatAtas ? 'Konfirmasi kelulusan dari program' : 'Konfirmasi rekomendasi kelulusan tingkat' }}
                            </p>
                        </div>
                        <button wire:click="closeRekomendasiModal" class="text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="mb-6 space-y-4 text-sm">
                        <p style="color:#6B7280;">
                            Siswa <strong style="color:#1A2422;">{{ $rekomendasiNamaSiswa }}</strong> telah mencapai 100% target hafalan pada kelompok <span class="font-bold text-emerald-600">{{ $kelompok->nama }} ({{ $kelompok->tingkat }})</span>.
                        </p>

                        {{-- Field Tanggal Naik Tingkat / Lulus --}}
                        <div>
                            <label for="tanggalNaikTingkat" class="block text-sm font-semibold mb-2" style="color:#1A2422;">
                                {{ $isTingkatAtas ? 'Tanggal Lulus Program' : 'Tanggal Naik Tingkat' }}
                                <span style="color:#9B3A3A;">*</span>
                            </label>
                            <input
                                type="date"
                                id="tanggalNaikTingkat"
                                wire:model="tanggalNaikTingkat"
                                data-no-flatpickr="true"
                                class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all no-flatpickr"
                                style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                required
                            >
                            @error('tanggalNaikTingkat')
                                <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs" style="color:#6B7280;">
                                Pilih tanggal ketika siswa dinyatakan lulus (tidak boleh lebih dari hari ini)
                            </p>
                        </div>

                        {{-- Info Box --}}
                        <div class="p-3 border rounded-lg flex gap-3 items-start" style="background:#dbeafe;border-color:#93c5fd;">
                            <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-xs text-blue-800 leading-relaxed">
                                @if($isTingkatAtas)
                                    Dengan mengklik "<strong>Luluskan dari Program</strong>", siswa ini akan <strong>LULUS dari Program Tahfidz</strong> karena telah menyelesaikan seluruh target hafalan tingkat tertinggi (Atas). Admin akan memproses kelulusan final di halaman Usulan Kenaikan Tingkat.
                                @else
                                    Dengan mengklik "<strong>Luluskan & Naik Tingkat</strong>", siswa ini akan dikeluarkan dari kelompok saat ini dan statusnya dikembalikan menjadi <strong>Belum Ditempatkan</strong> agar Admin dapat menempatkannya di kelompok dengan tingkat yang lebih tinggi.
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t" style="border-color:#E5E7EB;">
                        <button wire:click="closeRekomendasiModal" class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors focus:outline-none hover:bg-gray-50" style="color:#6B7280;">
                            Batal
                        </button>
                        <button wire:click="simpanRekomendasi" class="px-5 py-2 text-sm font-bold text-white rounded-lg shadow-sm hover:shadow transition-all focus:outline-none focus:ring-2 focus:ring-offset-2" style="background:linear-gradient(135deg,{{ $isTingkatAtas ? '#16a34a,#15803d' : '#f59e0b,#d97706' }});--tw-ring-color:{{ $isTingkatAtas ? '#16a34a' : '#f59e0b' }};">
                            {{ $isTingkatAtas ? 'Luluskan dari Program' : 'Luluskan & Naik Tingkat' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ───── TAB: RIWAYAT KELULUSAN (ALUMNI) ───── --}}
    @if($activeTab === 'alumni')
        <livewire:admin.kelompok.tab-alumni :kelompok="$kelompok" />
    @endif

    {{-- Modal Input Absensi & Setoran --}}
    @if(auth()->guard('admin')->check())
        <livewire:admin.absensi.input-absensi />
    @elseif(auth()->guard('guru')->check())
        <livewire:admin.absensi.input-absensi />
    @endif
</div>
