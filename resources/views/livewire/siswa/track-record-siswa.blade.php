{{--
    View: livewire/siswa/track-record-siswa.blade.php
    Track Record & Rekam Jejak Terpadu Siswa (Setoran, Kehadiran & Journey Timeline)
--}}

<div class="space-y-6" style="font-family:'Inter',sans-serif;">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- PAGE HEADER & KPI STATS --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl bg-white shadow-sm p-6 border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold mb-2" style="background-color: #E8F5F1; color: #1B5E4F;">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Rekam Jejak & Portofolio Siswa
                </div>
                <h1 class="text-2xl font-bold text-gray-900" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Track Record & Perjalanan Tahfidz
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Histori lengkap perjalanan halaqah, setoran ayat, dan kehadiran siswa dari awal program hingga saat ini.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('siswa.dashboard') }}" class="px-3.5 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    ← Kembali ke Dashboard
                </a>
            </div>
        </div>

        {{-- 4 KPI Stats Grid --}}
        @php $kpi = $this->kpiStats; @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-100">
            {{-- KPI 1 --}}
            <div class="p-3.5 rounded-xl bg-emerald-50/50 border border-emerald-100">
                <p class="text-xs text-gray-500 font-medium">Hafalan Tuntas</p>
                <p class="text-xl font-bold text-emerald-900 mt-0.5" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    {{ number_format($kpi['total_juz'], 1) }} <span class="text-xs text-gray-500 font-normal">/ {{ $kpi['target_juz'] }} Juz</span>
                </p>
                <div class="mt-2 w-full bg-emerald-200/50 rounded-full h-1.5 overflow-hidden">
                    <div class="h-1.5 rounded-full bg-emerald-800" style="width: {{ $kpi['pct_juz'] }}%;"></div>
                </div>
            </div>

            {{-- KPI 2 --}}
            <div class="p-3.5 rounded-xl bg-blue-50/50 border border-blue-100">
                <p class="text-xs text-gray-500 font-medium">Kehadiran Halaqah</p>
                <p class="text-xl font-bold text-blue-900 mt-0.5" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    {{ $kpi['persentase_hadir'] }}%
                </p>
                <p class="text-xs text-gray-500 mt-1">{{ $kpi['total_hadir'] }} dari {{ $kpi['total_pertemuan'] }} pertemuan</p>
            </div>

            {{-- KPI 3 --}}
            <div class="p-3.5 rounded-xl bg-purple-50/50 border border-purple-100">
                <p class="text-xs text-gray-500 font-medium">Total Setoran</p>
                <p class="text-xl font-bold text-purple-900 mt-0.5" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    {{ $kpi['total_setoran'] }} <span class="text-xs text-gray-500 font-normal">Sesi</span>
                </p>
                <p class="text-xs text-gray-500 mt-1">{{ $kpi['setoran_lanjut'] }} Lanjut • {{ $kpi['setoran_ulang'] }} Ulang</p>
            </div>

            {{-- KPI 4 --}}
            <div class="p-3.5 rounded-xl bg-amber-50/50 border border-amber-100">
                <p class="text-xs text-gray-500 font-medium">Kualitas Rata-Rata</p>
                <p class="text-base font-bold text-amber-900 mt-0.5 truncate" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    {{ $kpi['rata_predikat'] }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Tajwid & Kelancaran</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TAB CONTROLLER --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-2 flex flex-wrap gap-2">
        <button
            type="button"
            wire:click="setTab('perjalanan')"
            class="flex-1 min-w-[160px] py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-2 {{ $activeTab === 'perjalanan' ? 'bg-emerald-800 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            Rekam Jejak Perjalanan (Journey)
        </button>

        <button
            type="button"
            wire:click="setTab('setoran')"
            class="flex-1 min-w-[160px] py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-2 {{ $activeTab === 'setoran' ? 'bg-emerald-800 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Riwayat Setoran Ayat
        </button>

        <button
            type="button"
            wire:click="setTab('kehadiran')"
            class="flex-1 min-w-[160px] py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-2 {{ $activeTab === 'kehadiran' ? 'bg-emerald-800 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            Riwayat Kehadiran Halaqah
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: PERJALANAN (JOURNEY TIMELINE) --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'perjalanan')
        <div class="rounded-2xl bg-white shadow-sm p-6 border border-gray-200 space-y-6">
            <div>
                <h3 class="text-base font-bold text-gray-900" style="font-family:'Plus Jakarta Sans',sans-serif;">
                    Perjalanan Tingkat Tahfidz Siswa
                </h3>
                <p class="text-xs text-gray-500">
                    Rekam jejak perpindahan tingkat dari jenjang Dasar, Menengah, hingga jenjang Atas.
                </p>
            </div>

            @if(empty($journeyStages))
                <div class="py-12 text-center rounded-xl bg-gray-50 border border-dashed border-gray-200">
                    <p class="text-sm font-semibold text-gray-700">Belum Ada Rekam Jejak Perjalanan</p>
                    <p class="text-xs text-gray-400 mt-1">Data perpindahan tingkat akan otomatis tercatat saat siswa ditempatkan atau naik tingkat.</p>
                </div>
            @else
                <div class="relative pl-6 space-y-6 before:absolute before:left-3.5 before:top-3 before:bottom-3 before:w-0.5 before:bg-gray-200">
                    @foreach($journeyStages as $idx => $stage)
                        @php
                            $tingkat = $stage['tingkat'];
                            $badge = match($tingkat) {
                                'Dasar' => ['bg' => '#E8F5F1', 'text' => '#1B5E4F', 'dot' => '#1B5E4F', 'icon' => '📗'],
                                'Menengah' => ['bg' => '#EFF6FF', 'text' => '#1D4ED8', 'dot' => '#1D4ED8', 'icon' => '📘'],
                                'Atas' => ['bg' => '#FEF9E7', 'text' => '#A16207', 'dot' => '#A16207', 'icon' => '📙'],
                                default => ['bg' => '#F3F4F6', 'text' => '#374151', 'dot' => '#6B7280', 'icon' => '🏷️']
                            };
                        @endphp
                        <div class="relative">
                            {{-- Dot Indicator --}}
                            <div class="absolute -left-6 top-1.5 w-7 h-7 rounded-full bg-white border-2 flex items-center justify-center text-xs font-bold shadow-xs"
                                 style="border-color: {{ $badge['dot'] }}; color: {{ $badge['dot'] }};">
                                {{ $idx + 1 }}
                            </div>

                            {{-- Card Detail --}}
                            <div class="ml-4 p-5 rounded-2xl border border-gray-200 bg-white hover:border-gray-300 shadow-xs space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-gray-100">
                                    <div class="flex items-center gap-2.5">
                                        <span class="px-3 py-1 rounded-lg text-xs font-bold" style="background-color: {{ $badge['bg'] }}; color: {{ $badge['text'] }};">
                                            {{ $badge['icon'] }} Tingkat {{ $tingkat }}
                                        </span>
                                        <h4 class="text-base font-bold text-gray-900" style="font-family:'Plus Jakarta Sans',sans-serif;">
                                            {{ $stage['kelompok_nama'] }}
                                        </h4>
                                        @if(!empty($stage['is_current']))
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                ● Tingkat Saat Ini
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-xs font-medium text-gray-500">
                                        Durasi: <strong class="text-gray-800">{{ $stage['durasi_text'] }}</strong>
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                                    <div class="p-3 rounded-xl bg-gray-50">
                                        <span class="text-gray-400 block mb-0.5">Ustadz/ah Pembimbing:</span>
                                        <span class="font-bold text-gray-800">{{ $stage['guru_nama'] }}</span>
                                    </div>
                                    <div class="p-3 rounded-xl bg-gray-50">
                                        <span class="text-gray-400 block mb-0.5">Periode Waktu:</span>
                                        <span class="font-bold text-gray-800">{{ $stage['tanggal_masuk'] }} &mdash; {{ $stage['tanggal_keluar'] }}</span>
                                    </div>
                                    <div class="p-3 rounded-xl bg-gray-50">
                                        <span class="text-gray-400 block mb-0.5">Target & Status Ketuntasan:</span>
                                        <span class="font-bold {{ $stage['is_tuntas'] ? 'text-emerald-700' : 'text-amber-700' }}">
                                            Target: {{ $stage['target_juz'] }} {{ $stage['is_tuntas'] ? '(✓ Tuntas)' : '(Dalam Proses)' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="text-xs text-gray-500 pt-1">
                                    <span class="font-semibold text-gray-700">Rincian Capaian:</span> {{ $stage['capaian_text'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: RIWAYAT SETORAN AYAT --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'setoran')
        <div class="space-y-4">
            {{-- Toolbar Filter Setoran --}}
            <div class="rounded-2xl bg-white shadow-sm p-4 border border-gray-200">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2.5">
                        {{-- Filter Jenis --}}
                        <select wire:model.live="filterJenis" class="px-3 py-2 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                            <option value="">Semua Jenis Setoran</option>
                            <option value="Ziyadah">Ziyadah (Baru)</option>
                            <option value="Muroja'ah">Muroja'ah (Ulang)</option>
                        </select>

                        {{-- Filter Keputusan --}}
                        <select wire:model.live="filterKeputusan" class="px-3 py-2 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                            <option value="">Semua Keputusan</option>
                            <option value="Lanjut">Lanjut</option>
                            <option value="Ulang">Ulang</option>
                        </select>

                        {{-- Filter Surah --}}
                        <select wire:model.live="filterSurah" class="px-3 py-2 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white max-w-[200px]">
                            <option value="">Semua Surah</option>
                            @foreach($surahs as $s)
                                <option value="{{ $s->id }}">{{ $s->nomor }}. {{ $s->nama_latin }}</option>
                            @endforeach
                        </select>

                        {{-- Sorting --}}
                        <select wire:model.live="sortBy" class="px-3 py-2 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                            <option value="tanggal_desc">Terbaru ke Terlama</option>
                            <option value="tanggal_asc">Terlama ke Terbaru</option>
                        </select>

                        @if($filterJenis || $filterKeputusan || $filterSurah)
                            <button type="button" wire:click="resetSetoranFilters" class="px-3 py-2 text-xs font-semibold text-red-600 hover:text-red-800">
                                Reset Filter
                            </button>
                        @endif
                    </div>

                    <span class="text-xs text-gray-500">
                        Menampilkan total <strong class="text-gray-800">{{ $setorans->total() }}</strong> riwayat
                    </span>
                </div>
            </div>

            {{-- Table Setoran --}}
            <div class="rounded-2xl bg-white shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 font-semibold border-b border-gray-200">
                                <th class="p-3.5">Tanggal</th>
                                <th class="p-3.5">Surah & Ayat</th>
                                <th class="p-3.5">Juz</th>
                                <th class="p-3.5">Jenis</th>
                                <th class="p-3.5 text-center">Tajwid</th>
                                <th class="p-3.5 text-center">Kelancaran</th>
                                <th class="p-3.5 text-center">Makhraj</th>
                                <th class="p-3.5 text-center">Fashahah</th>
                                <th class="p-3.5 text-center">Predikat</th>
                                <th class="p-3.5 text-center">Keputusan</th>
                                <th class="p-3.5">Catatan Ustadz/ah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($setorans as $item)
                                @php
                                    $isLanjut = ($item->keputusan instanceof \BackedEnum ? $item->keputusan->value : (string)$item->keputusan) === 'Lanjut';
                                    $tgl = $item->absensi?->tanggal ? Carbon::parse($item->absensi->tanggal)->translatedFormat('d M Y') : Carbon::parse($item->created_at)->translatedFormat('d M Y');
                                    $predikatLabel = $item->predikat instanceof \BackedEnum ? $item->predikat->value : (string)($item->predikat ?? '-');
                                    $jenisLabel = $item->jenis_setoran instanceof \BackedEnum ? $item->jenis_setoran->value : (string)$item->jenis_setoran;
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-3.5 font-medium text-gray-800 whitespace-nowrap">{{ $tgl }}</td>
                                    <td class="p-3.5 font-bold text-gray-900 whitespace-nowrap">
                                        {{ $item->surah?->nama_latin ?? '-' }} : {{ $item->ayat_mulai }}-{{ $item->ayat_selesai }}
                                    </td>
                                    <td class="p-3.5 font-semibold text-gray-700">Juz {{ $item->juz?->nomor_juz ?? ($item->juz_id ?? '-') }}</td>
                                    <td class="p-3.5">
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-medium {{ $jenisLabel === 'Ziyadah' ? 'bg-purple-50 text-purple-700' : 'bg-blue-50 text-blue-700' }}">
                                            {{ $jenisLabel }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-center font-semibold">{{ $item->skor_tajwid instanceof \BackedEnum ? $item->skor_tajwid->value : (string)($item->skor_tajwid ?? '-') }}</td>
                                    <td class="p-3.5 text-center font-semibold">{{ $item->skor_kelancaran instanceof \BackedEnum ? $item->skor_kelancaran->value : (string)($item->skor_kelancaran ?? '-') }}</td>
                                    <td class="p-3.5 text-center font-semibold">{{ $item->skor_makhraj instanceof \BackedEnum ? $item->skor_makhraj->value : (string)($item->skor_makhraj ?? '-') }}</td>
                                    <td class="p-3.5 text-center font-semibold">{{ $item->skor_fashahah instanceof \BackedEnum ? $item->skor_fashahah->value : (string)($item->skor_fashahah ?? '-') }}</td>
                                    <td class="p-3.5 text-center">
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-gray-100 text-gray-800">
                                            {{ $predikatLabel }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $isLanjut ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                            {{ $isLanjut ? '✓ Lanjut' : '↺ Ulang' }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-gray-600 italic max-w-xs truncate" title="{{ $item->catatan }}">
                                        {{ $item->catatan ? '"'.$item->catatan.'"' : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="p-12 text-center text-gray-400">
                                        Tidak ada data setoran yang cocok dengan filter yang dipilih.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($setorans->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50">
                        {{ $setorans->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 3: RIWAYAT KEHADIRAN HALAQAH --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'kehadiran')
        <div class="space-y-4">
            {{-- Toolbar Filter Kehadiran --}}
            <div class="rounded-2xl bg-white shadow-sm p-4 border border-gray-200">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2.5">
                        {{-- Filter Status --}}
                        <select wire:model.live="filterStatusKehadiran" class="px-3 py-2 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white">
                            <option value="">Semua Status Kehadiran</option>
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Alfa">Alfa</option>
                        </select>

                        {{-- Filter Bulan --}}
                        <input
                            type="month"
                            wire:model.live="filterBulanKehadiran"
                            class="px-3 py-1.5 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white"
                        />

                        @if($filterStatusKehadiran || $filterBulanKehadiran)
                            <button type="button" wire:click="resetKehadiranFilters" class="px-3 py-2 text-xs font-semibold text-red-600 hover:text-red-800">
                                Reset Filter
                            </button>
                        @endif
                    </div>

                    <span class="text-xs text-gray-500">
                        Total <strong class="text-gray-800">{{ $absensis->total() }}</strong> sesi pertemuan halaqah
                    </span>
                </div>
            </div>

            {{-- Table Kehadiran --}}
            <div class="rounded-2xl bg-white shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 text-gray-700 font-semibold border-b border-gray-200">
                                <th class="p-3.5">Tanggal Sesi</th>
                                <th class="p-3.5">Halaqah</th>
                                <th class="p-3.5">Ustadz/ah Pembimbing</th>
                                <th class="p-3.5 text-center">Status Kehadiran</th>
                                <th class="p-3.5">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($absensis as $abs)
                                @php
                                    $st = $abs->status_kehadiran instanceof \BackedEnum ? $abs->status_kehadiran->value : (string)$abs->status_kehadiran;
                                    $badge = match($st) {
                                        'Hadir' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                        'Izin'  => 'bg-blue-100 text-blue-800 border-blue-300',
                                        'Sakit' => 'bg-amber-100 text-amber-800 border-amber-300',
                                        'Alfa'  => 'bg-red-100 text-red-800 border-red-300',
                                        default => 'bg-gray-100 text-gray-800 border-gray-300',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="p-3.5 font-bold text-gray-900 whitespace-nowrap">
                                        {{ Carbon::parse($abs->tanggal)->translatedFormat('l, d F Y') }}
                                    </td>
                                    <td class="p-3.5 font-semibold text-gray-800">{{ $abs->kelompok?->nama ?? '-' }}</td>
                                    <td class="p-3.5 text-gray-700">{{ $abs->kelompok?->guruPembimbing?->nama ?? '-' }}</td>
                                    <td class="p-3.5 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $badge }}">
                                            {{ $st }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-gray-500 italic">
                                        {{ $abs->keterangan ? $abs->keterangan : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-12 text-center text-gray-400">
                                        Tidak ada catatan kehadiran yang cocok dengan filter yang dipilih.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($absensis->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50">
                        {{ $absensis->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
