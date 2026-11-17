{{--
    View: livewire/siswa/progres/dashboard-progres-siswa.blade.php
    Component: App\Livewire\Siswa\Progres\DashboardProgresSiswa

    Dashboard progres hafalan untuk Siswa/Orang Tua.
    Menampilkan visualisasi progres yang besar, statistik utama,
    dan riwayat setoran terbaru.

    Requirements: 7.1, 7.2, 7.5
--}}
<div class="space-y-6 max-w-5xl mx-auto" style="font-family:'Inter',sans-serif;">

    @if (empty($dashboardData['progres']))
        <div class="rounded-2xl bg-white shadow-sm p-16 text-center" style="border:1px solid #E5E7EB;">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color:#F3F4F6;">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <p class="text-base font-semibold text-gray-700 mb-1">Data Progres Belum Tersedia</p>
            <p class="text-sm text-gray-500">Menunggu tahun ajaran aktif atau data setoran pertama.</p>
        </div>
    @else
        @php
            $progres          = $dashboardData['progres'];
            $juzTuntas        = (int) ($progres['total_juz_tuntas'] ?? 0);
            $juzBerjalan      = $progres['juz_berjalan'] ?? null;
            $juzPct           = (float) ($progres['juz_berjalan_percentage'] ?? 0);
            $target           = (int) ($progres['target_kelulusan'] ?? 3);
            $progressToTarget = (float) ($progres['progress_toward_target'] ?? 0);
            $sisaTarget       = max(0, $target - $juzTuntas);
            $tercapai         = $progressToTarget >= 100;

            // Kelompok siswa saat ini
            $siswa        = auth()->guard('siswa')->user();
            $kelompok     = $siswa->kelompok;
            $tingkat      = $kelompok?->tingkat ?? null;
            $tingkatValue = $tingkat instanceof \BackedEnum ? $tingkat->value : (string)($tingkat ?? '');
            $tingkatBadge = match($tingkatValue) {
                'Dasar'    => ['bg' => '#E8F5F1', 'color' => '#1B5E4F', 'icon' => '📗'],
                'Menengah' => ['bg' => '#EFF6FF', 'color' => '#1D4ED8', 'icon' => '📘'],
                'Atas'     => ['bg' => '#FEF9E7', 'color' => '#A16207', 'icon' => '📙'],
                default    => null,
            };

            // Ring color
            $ringColor = $tercapai ? '#4ADE80' : ($progressToTarget >= 60 ? '#FCD34D' : '#60A5FA');
        @endphp

        {{-- =====================================================================
             HERO SECTION
        ===================================================================== --}}
        <div class="rounded-2xl overflow-hidden shadow-sm" style="border:1px solid #E5E7EB;">
            {{-- Header bar --}}
            <div class="p-6 md:p-8" style="background: linear-gradient(135deg, #1B5E4F 0%, #164A3C 60%, #0F3328 100%); color: #FFFFFF;">
                <div class="flex flex-col md:flex-row gap-6 md:items-center md:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Progres Tahfidz
                        </h2>
                        <p class="mt-2 text-sm leading-relaxed" style="color:rgba(255,255,255,0.75); max-width:400px;">
                            Pantau perjalanan hafalan Al-Qur'an secara visual. Semangat menyelesaikan target!
                        </p>
                        @if($kelompok && $tingkatBadge)
                            <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold" style="background:rgba(255,255,255,0.15); color:white; backdrop-filter:blur(4px);">
                                <span>{{ $tingkatBadge['icon'] }}</span>
                                <span>Tingkat {{ $tingkatValue }}</span>
                                <span style="color:rgba(255,255,255,0.5);">•</span>
                                <span>{{ $kelompok->nama }}</span>
                            </div>
                        @elseif(!$kelompok)
                            <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium" style="background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.6);">
                                ℹ️ Belum ditempatkan di kelompok
                            </div>
                        @endif
                    </div>

                    {{-- Progress Ring Widget --}}
                    <div class="shrink-0 flex items-center gap-4 rounded-xl px-5 py-4" style="background:rgba(0,0,0,0.25); backdrop-filter:blur(6px);">
                        {{-- SVG Ring --}}
                        <div class="relative w-16 h-16 flex items-center justify-center">
                            <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 64 64">
                                <circle cx="32" cy="32" r="28" stroke="rgba(255,255,255,0.2)" stroke-width="6" fill="transparent"/>
                                <circle cx="32" cy="32" r="28"
                                    stroke="{{ $ringColor }}"
                                    stroke-width="6"
                                    fill="transparent"
                                    stroke-dasharray="{{ 2 * M_PI * 28 }}"
                                    stroke-dashoffset="{{ 2 * M_PI * 28 * (1 - min($progressToTarget, 100) / 100) }}"
                                    stroke-linecap="round"
                                    style="transition: stroke-dashoffset 1s ease;"
                                />
                            </svg>
                            <span class="absolute text-xs font-bold" style="color:white;">{{ number_format($progressToTarget, 0) }}%</span>
                        </div>
                        <div>
                            <p class="text-xs font-medium mb-1" style="color:rgba(255,255,255,0.7);">Menuju Target</p>
                            <p class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $target }} <span class="text-base font-medium" style="color:rgba(255,255,255,0.7);">Juz</span></p>
                            @if($tercapai)
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full mt-1 inline-block" style="background:#4ADE80;color:#14532D;">⭐ Tercapai!</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- =====================================================================
             ROADMAP TAHFIDZ
        ===================================================================== --}}
        <x-roadmap-tahfidz />

        {{-- =====================================================================
             SUMMARY CARDS
        ===================================================================== --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

            {{-- Card: Juz Tuntas --}}
            <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
                <div class="w-11 h-11 rounded-full flex items-center justify-center mb-4" style="background-color:#E8F5F1;">
                    <svg class="w-5 h-5" style="color:#1B5E4F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Total Juz Tuntas</p>
                <p class="text-3xl font-bold" style="color:#1A2422; font-family:'Plus Jakarta Sans',sans-serif;">
                    {{ $juzTuntas }} <span class="text-sm font-medium text-gray-400">Juz</span>
                </p>
                <div class="mt-3 text-xs" style="color:#1B5E4F;">
                    dari {{ $target }} Juz target kelulusan
                </div>
            </div>

            {{-- Card: Juz Berjalan --}}
            <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
                <div class="w-11 h-11 rounded-full flex items-center justify-center mb-4" style="background-color:#FEF9E7;">
                    <svg class="w-5 h-5" style="color:#C9A227;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Hafalan Berjalan</p>
                @if($juzBerjalan)
                    <p class="text-3xl font-bold" style="color:#1A2422; font-family:'Plus Jakarta Sans',sans-serif;">
                        Juz <span style="color:#C9A227;">{{ $juzBerjalan }}</span>
                    </p>
                    <div class="mt-3 flex items-center gap-2">
                        <div class="flex-1 h-2 rounded-full" style="background-color:#F3F4F6;">
                            <div class="h-2 rounded-full transition-all duration-700" style="width:{{ min($juzPct,100) }}%; background-color:#C9A227;"></div>
                        </div>
                        <span class="text-sm font-bold" style="color:#1A2422;">{{ number_format($juzPct, 0) }}%</span>
                    </div>
                @else
                    <p class="text-sm text-gray-400 mt-2">Tidak ada progres parsial</p>
                @endif
            </div>

            {{-- Card: Sisa Target --}}
            <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
                <div class="w-11 h-11 rounded-full flex items-center justify-center mb-4" style="background-color:#EFF6FF;">
                    <svg class="w-5 h-5" style="color:#3B82F6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Sisa ke Target ({{ $target }} Juz)</p>
                @if($tercapai)
                    <p class="text-3xl font-bold" style="color:#1B5E4F; font-family:'Plus Jakarta Sans',sans-serif;">Selesai!</p>
                    <span class="inline-flex mt-2 text-xs font-semibold px-2 py-1 rounded-full" style="background-color:#DCFCE7; color:#15803D;">⭐ Target Tercapai</span>
                @else
                    <p class="text-3xl font-bold" style="color:#1A2422; font-family:'Plus Jakarta Sans',sans-serif;">
                        {{ $sisaTarget }} <span class="text-sm font-medium text-gray-400">Juz lagi</span>
                    </p>
                    <div class="mt-3 text-xs text-gray-500">
                        Sudah {{ $juzTuntas }} dari {{ $target }} Juz
                    </div>
                @endif
            </div>

        </div>

        {{-- =====================================================================
             RECENT SETORAN
        ===================================================================== --}}
        <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;">
            <div class="p-5 border-b flex items-center justify-between" style="border-color:#F3F4F6;">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color:#E8F5F1;">
                        <svg class="w-4 h-4" style="color:#1B5E4F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif; color:#1A2422;">
                        Riwayat Setoran Terbaru
                    </h3>
                </div>
                <span class="text-xs text-gray-400">10 entri terakhir</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr style="background-color:#F9FAFB; border-bottom:2px solid #E5E7EB;">
                            <th class="py-3 px-5 text-xs font-semibold uppercase tracking-wider" style="color:#6B7280;">Tanggal</th>
                            <th class="py-3 px-5 text-xs font-semibold uppercase tracking-wider" style="color:#6B7280;">Jenis</th>
                            <th class="py-3 px-5 text-xs font-semibold uppercase tracking-wider" style="color:#6B7280;">Materi (Juz & Surah)</th>
                            <th class="py-3 px-5 text-xs font-semibold uppercase tracking-wider" style="color:#6B7280;">Nilai/Predikat</th>
                            <th class="py-3 px-5 text-xs font-semibold uppercase tracking-wider" style="color:#6B7280;">Keputusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dashboardData['recent_setoran'] as $idx => $setoran)
                            <tr class="border-b hover:bg-gray-50 transition-colors duration-150" style="border-color:#F3F4F6;">
                                <td class="py-3.5 px-5 text-sm font-medium whitespace-nowrap" style="color:#374151;">
                                    {{ \Carbon\Carbon::parse($setoran['tanggal'])->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3.5 px-5">
                                    @php
                                        $jenisBg = $setoran['jenis_setoran'] === 'Hafalan Baru' ? 'background:#E8F5F1;color:#1B5E4F;' : 'background:#FEF9E7;color:#A16207;';
                                    @endphp
                                    <span class="inline-flex px-2.5 py-1 rounded-md text-xs font-semibold" style="{{ $jenisBg }}">
                                        {{ $setoran['jenis_setoran'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5">
                                    <p class="text-sm font-semibold" style="color:#1A2422;">
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-bold mr-1.5" style="background-color:#1B5E4F;color:white;">Juz {{ $setoran['juz'] ?? '-' }}</span>
                                        {{ $setoran['surah'] ?? '-' }}
                                    </p>
                                    <p class="text-xs mt-0.5" style="color:#6B7280;">({{ $setoran['ayat_range'] }})</p>
                                </td>
                                <td class="py-3.5 px-5">
                                    @php
                                        $predStyle = match($setoran['predikat'] ?? '') {
                                            'Mumtaz'       => 'color:#15803D; font-weight:700;',
                                            'Jayyid Jiddan'=> 'color:#1D4ED8; font-weight:700;',
                                            'Jayyid'       => 'color:#D97706; font-weight:700;',
                                            'Maqbul'       => 'color:#EA580C; font-weight:700;',
                                            'Lancar'       => 'color:#1B5E4F; font-weight:700;',
                                            default        => 'color:#6B7280;',
                                        };
                                    @endphp
                                    <span class="text-sm" style="{{ $predStyle }}">{{ $setoran['predikat'] ?? '-' }}</span>
                                </td>
                                <td class="py-3.5 px-5">
                                    @if ($setoran['keputusan'] === 'Lanjut')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold" style="background-color:#DCFCE7; color:#15803D;">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            Lanjut
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold" style="background-color:#FEE2E2; color:#B91C1C;">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/></svg>
                                            Ulang
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-sm font-medium text-gray-500">Belum ada riwayat setoran</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
