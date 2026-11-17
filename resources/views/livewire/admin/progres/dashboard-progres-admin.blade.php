{{--
    View: livewire/admin/progres/dashboard-progres-admin.blade.php
    Component: App\Livewire\Admin\Progres\DashboardProgresAdmin

    Dashboard progres hafalan untuk Admin — menampilkan seluruh data
    dengan filter lanjutan, statistik agregat, dan penanda (flags)
    untuk siswa yang mendekati target atau tertinggal.

    Requirements: 6.1, 6.2, 6.3, 6.4, 6.5
--}}
<div class="space-y-6" style="font-family:'Inter',sans-serif;">

    {{-- =====================================================================
         HEADER
    ===================================================================== --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    📊 Pemantauan Progres Tahfidz
                </h2>
                <p class="mt-1 text-sm" style="color:#6B7280;">
                    Tinjauan komprehensif capaian hafalan seluruh tingkat dan kelompok
                </p>
            </div>
            <div class="flex items-center gap-3">
                <select
                    wire:model.live="filterTahunAjaran"
                    class="rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-zamrud font-medium"
                    style="background-color:#F4F5F4;border:1px solid #E5E7EB;color:#1A2422;"
                >
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach ($allTahunAjaran as $ta)
                        <option value="{{ $ta['id'] }}">
                            {{ $ta['label'] ?? 'TA ' . $ta['id'] }} {{ $ta['status'] ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>

                {{-- Export Buttons --}}
                <div class="flex items-center gap-2">
                    <button
                        wire:click="exportPdf"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors hover:bg-gray-100"
                        style="color:#1B5E4F; border:1px solid #1B5E4F;"
                        title="Cetak transkrip/rapor siswa ke PDF"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        PDF
                    </button>
                    <button
                        wire:click="exportExcel"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors hover:opacity-90"
                        style="background-color:#1B5E4F;color:#FFFFFF;"
                        title="Unduh rekap agregat ke Excel"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================================
         STATISTIK AGREGAT (Cards)
    ===================================================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Card 1: Rata-rata Juz Tuntas per Tingkat --}}
        <div class="rounded-2xl bg-white shadow-sm p-5" style="border:1px solid #E5E7EB;">
            <h3 class="text-sm font-medium" style="color:#6B7280;">Rata-rata Capaian per Tingkat</h3>
            <div class="mt-4 space-y-3">
                @foreach(['Dasar' => '#E8F5F1', 'Menengah' => '#FEF3C7', 'Atas' => '#E0E7FF'] as $t => $bg)
                    @php
                        $avg = $aggregateStats['avg_juz_per_tingkat'][$t] ?? 0;
                        $textColor = match($t) { 'Dasar' => '#1B5E4F', 'Menengah' => '#92400E', 'Atas' => '#3730A3', default => '#475569' };
                    @endphp
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium"
                              style="background-color:{{ $bg }};color:{{ $textColor }};">
                            {{ $t }}
                        </span>
                        <span class="text-sm font-semibold" style="color:#1A2422;font-variant-numeric:tabular-nums;">
                            {{ number_format($avg, 1) }} Juz
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Card 2: Distribusi Progres --}}
        <div class="rounded-2xl bg-white shadow-sm p-5" style="border:1px solid #E5E7EB;">
            <h3 class="text-sm font-medium" style="color:#6B7280;">Distribusi Progres ke Target</h3>
            <div class="mt-4 space-y-3">
                @php
                    $ranges = ['0-25%' => '#9B3A3A', '26-50%' => '#C9A227', '51-75%' => '#5B7A8C', '76-100%' => '#1B5E4F'];
                    $total = array_sum($aggregateStats['distribution_by_progress']);
                @endphp
                @foreach($ranges as $range => $color)
                    @php
                        $count = $aggregateStats['distribution_by_progress'][$range] ?? 0;
                        $pct = $total > 0 ? ($count / $total) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span style="color:#6B7280;">{{ $range }}</span>
                            <span class="font-semibold" style="color:#1A2422;">{{ $count }} siswa</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full" style="background-color:#F3F4F6;">
                            <div class="h-1.5 rounded-full" style="width:{{ $pct }}%;background-color:{{ $color }};"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Card 3: Flags / Perhatian Khusus --}}
        <div class="rounded-2xl bg-white shadow-sm p-5 flex flex-col justify-between" style="border:1px solid #E5E7EB;">
            <div>
                <h3 class="text-sm font-medium" style="color:#6B7280;">Insight Perhatian Khusus</h3>
                <div class="mt-4 space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0" style="background-color:#FEF9E7;color:#C9A227;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-bold leading-none" style="color:#1A2422;">{{ $approaching->count() }}</p>
                            <p class="text-xs mt-1" style="color:#6B7280;">Siswa mendekati target kelulusan (≤1 juz lagi)</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0" style="background-color:#FEF2F2;color:#9B3A3A;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-bold leading-none" style="color:#1A2422;">{{ $belowProgress->count() }}</p>
                            <p class="text-xs mt-1" style="color:#6B7280;">Siswa tertinggal (<40% dari target)</p>
                        </div>
                    </div>
                </div>
            </div>
            <button wire:click="toggleFlags" class="mt-4 text-xs font-medium w-full py-1.5 rounded bg-gray-50 hover:bg-gray-100 transition-colors" style="color:#5B7A8C;">
                {{ $showFlags ? 'Sembunyikan' : 'Tampilkan' }} Detail Flagging
            </button>
        </div>
    </div>

    {{-- =====================================================================
         PANEL FILTER & TABEL DATA
    ===================================================================== --}}
    <div class="rounded-2xl bg-white shadow-sm" style="border:1px solid #E5E7EB;">

        {{-- ADVANCED FILTER PANEL --}}
        <div class="p-5 border-b" style="border-color:#E5E7EB;">
            <div class="flex flex-col md:flex-row items-center gap-4">

                {{-- Filter Kelompok --}}
                <div class="w-full md:w-1/4">
                    <select wire:model.live="filterKelompok" class="w-full rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-zamrud" style="background-color:#FFFFFF;border:1.5px solid #E5E7EB;color:#1A2422;">
                        <option value="">Semua Kelompok</option>
                        @foreach ($allKelompok as $k)
                            <option value="{{ $k['id'] }}">{{ $k['nama'] ?? 'Kelompok '.$k['id'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Tingkat --}}
                <div class="w-full md:w-1/4">
                    <select wire:model.live="filterTingkat" class="w-full rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-zamrud" style="background-color:#FFFFFF;border:1.5px solid #E5E7EB;color:#1A2422;">
                        <option value="">Semua Tingkat</option>
                        <option value="Dasar">Dasar</option>
                        <option value="Menengah">Menengah</option>
                        <option value="Atas">Atas</option>
                    </select>
                </div>

                {{-- Filter Jenis Kelamin --}}
                <div class="w-full md:w-1/4">
                    <select wire:model.live="filterJenisKelamin" class="w-full rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-zamrud" style="background-color:#FFFFFF;border:1.5px solid #E5E7EB;color:#1A2422;">
                        <option value="">Semua Gender</option>
                        <option value="Laki-laki">Putra</option>
                        <option value="Perempuan">Putri</option>
                    </select>
                </div>

                {{-- Sort By --}}
                <div class="w-full md:w-1/4 flex gap-2">
                    <select wire:model.live="sortBy" class="w-full rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-zamrud" style="background-color:#FFFFFF;border:1.5px solid #E5E7EB;color:#1A2422;">
                        <option value="juz_count">Juz Terbanyak</option>
                        <option value="name">Nama Siswa</option>
                        <option value="progress_percentage">Progress</option>
                    </select>
                    <button wire:click="$set('sortDirection', '{{ $sortDirection === 'asc' ? 'desc' : 'asc' }}')" class="px-3 rounded-lg border flex items-center justify-center hover:bg-gray-50 transition-colors" style="border-color:#E5E7EB;color:#6B7280;" title="Balik urutan">
                        @if($sortDirection === 'asc')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4"/></svg>
                        @endif
                    </button>
                </div>
            </div>

            {{-- Reset Button --}}
            @if($filterKelompok || $filterTingkat || $filterJenisKelamin)
                <div class="mt-3">
                    <button wire:click="resetFilters" class="text-xs font-medium hover:underline transition-colors" style="color:#5B7A8C;">Reset Filter</button>
                </div>
            @endif
        </div>

        {{-- TABEL --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr style="background-color:#1B5E4F;" class="text-white">
                        <th class="py-3 px-5 font-semibold text-xs uppercase tracking-wider" style="color:white;">Nama & NIS</th>
                        <th class="py-3 px-5 font-semibold text-xs uppercase tracking-wider" style="color:white;">Kelompok</th>
                        <th class="py-3 px-5 font-semibold text-xs uppercase tracking-wider" style="color:white;">Tingkat</th>
                        <th class="py-3 px-5 font-semibold text-xs uppercase tracking-wider" style="color:white;">Juz Tuntas</th>
                        <th class="py-3 px-5 font-semibold text-xs uppercase tracking-wider" style="color:white;">Progress Target</th>
                        <th class="py-3 px-5 font-semibold text-xs uppercase tracking-wider text-center" style="color:white;">Indikator</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="divide-color:#E5E7EB;">
                    @forelse ($progresData as $progres)
                        @php
                            $siswa = $progres->siswa;
                            $kelompok = $siswa?->kelompok;

                            // Check Flags
                            $isApproaching = $approaching->contains('siswa_id', $siswa?->id);
                            $isBelow = $belowProgress->contains('siswa_id', $siswa?->id);
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-5">
                                <p class="text-sm font-semibold" style="color:#1A2422;">{{ $siswa?->nama ?? '-' }}</p>
                                <p class="text-xs mt-0.5" style="color:#6B7280;font-variant-numeric:tabular-nums;">{{ $siswa?->nis ?? '-' }}</p>
                            </td>
                            <td class="py-3 px-5">
                                <p class="text-sm" style="color:#1A2422;">{{ $kelompok?->nama ?? '-' }}</p>
                            </td>
                            <td class="py-3 px-5">
                                @if($kelompok?->tingkat)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                          style="background-color:{{ match($kelompok->tingkat){'Dasar'=>'#E8F5F1','Menengah'=>'#FEF3C7','Atas'=>'#E0E7FF',default=>'#F3F4F6'} }};
                                                 color:{{ match($kelompok->tingkat){'Dasar'=>'#1B5E4F','Menengah'=>'#92400E','Atas'=>'#3730A3',default=>'#4B5563'} }};">
                                        {{ $kelompok->tingkat }}
                                    </span>
                                @else
                                    <span class="text-sm" style="color:#6B7280;">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-sm font-semibold"
                                      style="background-color:#E8F5F1;color:#1B5E4F;font-variant-numeric:tabular-nums;">
                                    {{ $progres->total_juz_tuntas ?? 0 }}
                                </span>
                            </td>
                            <td class="py-3 px-5">
                                @php $pct = $progres->progress_toward_target ?? 0; @endphp
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-medium w-8 text-right" style="color:#1A2422;font-variant-numeric:tabular-nums;">{{ number_format($pct, 0) }}%</span>
                                    <div class="w-24 h-1.5 rounded-full" style="background-color:#F3F4F6;">
                                        <div class="h-1.5 rounded-full" style="width:{{ min($pct, 100) }}%;background-color:{{ $pct>=100 ? '#1B5E4F' : ($pct>=60 ? '#C9A227' : '#9B3A3A') }};"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-5 text-center">
                                @if ($isApproaching)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                          style="background-color:#FEF9E7;color:#C9A227;border:1px solid #FDE68A;" title="Mendekati Target (≤1 Juz)">
                                        ⭐ Mendekati Target
                                    </span>
                                @elseif ($isBelow)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                          style="background-color:#FEF2F2;color:#9B3A3A;border:1px solid #FECACA;" title="Tertinggal (<40% Target)">
                                        ⚠️ Tertinggal
                                    </span>
                                @else
                                    <span class="text-xs" style="color:#9CA3AF;">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center">
                                <p class="text-sm" style="color:#6B7280;">Tidak ada data progres ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($progresData->hasPages())
            <div class="p-4 border-t" style="border-color:#E5E7EB;">
                {{ $progresData->links() }}
            </div>
        @endif
    </div>
</div>
