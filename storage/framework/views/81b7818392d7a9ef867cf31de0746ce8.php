

<div class="space-y-6" style="font-family:'Inter',sans-serif;">

    
    <?php
        $motivationalQuotes = [
            [
                'text' => 'خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ',
                'translation' => 'Sebaik-baik kalian adalah orang yang mempelajari Al-Qur\'an dan mengajarkannya.',
                'source' => 'HR. Bukhari'
            ],
            [
                'text' => 'مَثَلُ الَّذِي يَقْرَأُ الْقُرْآنَ وَهُوَ حَافِظٌ لَهُ مَعَ السَّفَرَةِ الْكِرَامِ الْبَرَرَةِ',
                'translation' => 'Perumpamaan orang yang membaca Al-Qur\'an dan ia menghafalkannya, maka ia bersama para malaikat yang mulia lagi berbakti.',
                'source' => 'HR. Bukhari & Muslim'
            ],
            [
                'text' => 'اقْرَءُوا الْقُرْآنَ فَإِنَّهُ يَأْتِي يَوْمَ الْقِيَامَةِ شَفِيعًا لِأَصْحَابِهِ',
                'translation' => 'Bacalah Al-Qur\'an, karena sesungguhnya Al-Qur\'an akan datang pada hari kiamat sebagai pemberi syafa\'at bagi para pembacanya.',
                'source' => 'HR. Muslim'
            ],
            [
                'text' => 'إِنَّ الَّذِي لَيْسَ فِي جَوْفِهِ شَيْءٌ مِنَ الْقُرْآنِ كَالْبَيْتِ الْخَرِبِ',
                'translation' => 'Sesungguhnya orang yang tidak memiliki sedikitpun Al-Qur\'an di dalam dadanya (hafalannya) bagaikan rumah yang roboh.',
                'source' => 'HR. Tirmidzi'
            ],
            [
                'text' => 'مَنْ قَرَأَ حَرْفًا مِنْ كِتَابِ اللَّهِ فَلَهُ بِهِ حَسَنَةٌ وَالْحَسَنَةُ بِعَشْرِ أَمْثَالِهَا',
                'translation' => 'Barangsiapa membaca satu huruf dari Kitabullah (Al-Qur\'an), maka baginya satu kebaikan, dan satu kebaikan dibalas sepuluh kali lipat.',
                'source' => 'HR. Tirmidzi'
            ]
        ];
        $dayOfYear = now()->dayOfYear;
        $selectedQuote = $motivationalQuotes[$dayOfYear % count($motivationalQuotes)];
    ?>

    <div class="relative overflow-hidden rounded-2xl shadow-sm border border-emerald-200" style="background: linear-gradient(135deg, #E8F5F1 0%, #D1F2E9 100%);">
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%231B5E4F\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        <div class="relative p-5 flex items-start gap-4">
            <div class="shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-2xl" style="background-color: #1B5E4F; color: white; box-shadow: 0 4px 12px rgba(27,94,79,0.3);">
                📖
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider" style="background-color: #1B5E4F; color: white;">
                        Hadits Hari Ini
                    </span>
                    <span class="text-xs text-emerald-700 font-medium"><?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></span>
                </div>
                <p class="text-right text-lg sm:text-xl font-arabic leading-loose mb-2" style="color: #0F3328; font-family: 'Amiri', 'Traditional Arabic', serif; direction: rtl;">
                    <?php echo e($selectedQuote['text']); ?>

                </p>
                <p class="text-sm text-gray-700 italic leading-relaxed mb-1">
                    "<?php echo e($selectedQuote['translation']); ?>"
                </p>
                <p class="text-xs font-semibold text-emerald-800">
                    — <?php echo e($selectedQuote['source']); ?>

                </p>
            </div>
        </div>
    </div>

    
    <div class="relative overflow-hidden rounded-2xl shadow-sm border border-gray-200 bg-white">
        
        <div class="h-2 w-full" style="background: linear-gradient(90deg, #1B5E4F 0%, #2A856E 50%, #34D399 100%);"></div>

        <div class="p-6 md:p-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                
                <div class="space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold" style="background-color: #E8F5F1; color: #1B5E4F;">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Portal Siswa Tahfidz
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($this->progresHafalan['status_tahfidz'])): ?>
                            <?php
                                $isLulus = $this->progresHafalan['status_tahfidz'] === 'Lulus';
                            ?>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold <?php echo e($isLulus ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200'); ?>" title="Status Kelulusan Program Tahfidz">
                                <span><?php echo e($isLulus ? '🎓' : '📖'); ?></span>
                                <span>Status: <?php echo e($this->progresHafalan['status_tahfidz']); ?></span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight" style="color: #1A2422; font-family: 'Plus Jakarta Sans', sans-serif;">
                        Assalamu'alaikum, <?php echo e(auth('siswa')->user()->nama); ?> 👋
                    </h1>
                    <p class="text-sm text-gray-500">
                        NIS: <span class="font-semibold text-gray-700"><?php echo e(auth('siswa')->user()->nis); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth('siswa')->user()->kelas): ?>
                            • Kelas: <span class="font-semibold text-gray-700"><?php echo e(auth('siswa')->user()->kelas->nama_kelas); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </p>
                </div>

                
                <div class="flex items-center gap-3 self-start md:self-auto px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-right">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-emerald-800 bg-emerald-100/60 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium"><?php echo e(now()->locale('id')->isoFormat('dddd')); ?></p>
                        <p class="text-sm font-bold text-gray-800" style="font-family: 'Plus Jakarta Sans', sans-serif;"><?php echo e(now()->locale('id')->isoFormat('D MMMM YYYY')); ?></p>
                    </div>
                </div>
            </div>

            
            <div class="mt-6 pt-6 border-t border-gray-100">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->kelompokInfo): ?>
                    <?php
                        $tingkat = $this->kelompokInfo['tingkat'];
                        $tingkatBadge = match($tingkat) {
                            'Dasar' => ['bg' => '#E8F5F1', 'text' => '#1B5E4F', 'icon' => '📗'],
                            'Menengah' => ['bg' => '#EFF6FF', 'text' => '#1D4ED8', 'icon' => '📘'],
                            'Atas' => ['bg' => '#FEF9E7', 'text' => '#A16207', 'icon' => '📙'],
                            default => ['bg' => '#F3F4F6', 'text' => '#374151', 'icon' => '🏷️']
                        };
                    ?>
                    <div class="flex flex-wrap items-center justify-between gap-4 p-4 rounded-xl" style="background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-bold" style="background-color: <?php echo e($tingkatBadge['bg']); ?>; color: <?php echo e($tingkatBadge['text']); ?>;">
                                <span><?php echo e($tingkatBadge['icon']); ?></span>
                                <span>Tingkat <?php echo e($tingkat); ?></span>
                            </div>
                            <div class="h-4 w-px bg-gray-300 hidden sm:block"></div>
                            <span class="text-sm font-bold text-gray-900" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                                Halaqah: <?php echo e($this->kelompokInfo['nama']); ?>

                            </span>
                            <span class="text-xs text-gray-500">• Pembimbing: <strong class="text-gray-700"><?php echo e($this->kelompokInfo['guru_pembimbing']); ?></strong></span>
                        </div>
                        <div class="text-xs font-medium text-gray-500">
                            Anggota: <span class="font-bold text-gray-800"><?php echo e($this->kelompokInfo['jumlah_siswa']); ?></span> / <?php echo e($this->kelompokInfo['kapasitas_maks']); ?> Siswa
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900">
                        <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs sm:text-sm">
                            <strong>Status Penempatan:</strong> Kamu saat ini belum ditempatkan di kelompok halaqah tahfidz. Silakan berkoordinasi dengan Ustadz/ah atau Admin sekolah.
                        </p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        
        <?php
            $juzTuntas = (float)($this->progresHafalan['total_juz_tuntas'] ?? 0);
            $targetJuz = (float)($this->progresHafalan['target_juz'] ?? 5);
            $pctJuz = $targetJuz > 0 ? min(100, round(($juzTuntas / $targetJuz) * 100)) : 0;
        ?>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200 hover:border-emerald-300 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Hafalan Tuntas</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white" style="background-color: #1B5E4F;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl font-bold text-gray-900" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    <?php echo e(number_format($juzTuntas, 1)); ?>

                </span>
                <span class="text-xs text-gray-500">/ <?php echo e(number_format($targetJuz, 0)); ?> Juz</span>
            </div>
            
            <div class="mt-3 w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                <div class="h-2 rounded-full transition-all duration-500" style="width: <?php echo e($pctJuz); ?>%; background-color: #1B5E4F;"></div>
            </div>
            <p class="mt-1.5 text-[11px] text-gray-500 flex justify-between">
                <span>Pencapaian:</span>
                <span class="font-bold text-emerald-700"><?php echo e($pctJuz); ?>% Target</span>
            </p>
        </div>

        
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200 hover:border-emerald-300 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kehadiran</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-700 bg-blue-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl font-bold text-gray-900" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    <?php echo e($this->statistikKehadiran['persentase_hadir']); ?>%
                </span>
                <span class="text-xs text-gray-500">Hadir</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs text-gray-500 pt-1 border-t border-gray-100">
                <span>Pertemuan:</span>
                <span class="font-semibold text-gray-800"><?php echo e($this->statistikKehadiran['hadir']); ?> / <?php echo e($this->statistikKehadiran['total']); ?> Kali</span>
            </div>
            <p class="mt-1 text-[11px] text-gray-400">Kumulatif seluruh absensi</p>
        </div>

        
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200 hover:border-emerald-300 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Sesi Setoran</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-purple-700 bg-purple-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl font-bold text-gray-900" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    <?php echo e($this->statistikSetoran['total']); ?>

                </span>
                <span class="text-xs text-gray-500">Sesi</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs text-gray-500 pt-1 border-t border-gray-100">
                <span>Hasil:</span>
                <span class="font-semibold text-emerald-700"><?php echo e($this->statistikSetoran['lanjut']); ?> Lanjut</span>
                <span class="text-gray-300">|</span>
                <span class="font-semibold text-amber-600"><?php echo e($this->statistikSetoran['ulang']); ?> Ulang</span>
            </div>
            <p class="mt-1 text-[11px] text-gray-400">Keseluruhan interaksi talaqqi</p>
        </div>

        
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200 hover:border-emerald-300 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kualitas Hafalan</span>
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-amber-600 bg-amber-50">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-xl font-bold text-gray-900 truncate" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    <?php echo e($this->statistikSetoran['rata_rata_predikat']); ?>

                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs text-gray-500 pt-1 border-t border-gray-100">
                <span>Status:</span>
                <span class="font-semibold text-emerald-700">Rata-rata Terkini</span>
            </div>
            <p class="mt-1 text-[11px] text-gray-400">Tajwid, Makhraj & Kelancaran</p>
        </div>

    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        
        <div class="lg:col-span-5 bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-gray-900" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                            Aktivitas Setoran 7 Hari Terakhir
                        </h2>
                        <p class="text-xs text-gray-500">Frekuensi setoran harian yang kamu lakukan</p>
                    </div>
                </div>

                <div
                    x-data="{
                        chartData: <?php echo \Illuminate\Support\Js::from($this->chartProgresMingguan)->toHtml() ?>,
                        chart: null
                    }"
                    x-init="
                        $nextTick(() => {
                            const options = {
                                chart: {
                                    type: 'bar',
                                    height: 240,
                                    toolbar: { show: false },
                                    fontFamily: 'Inter, sans-serif'
                                },
                                series: [{ name: 'Setoran', data: chartData.data }],
                                xaxis: {
                                    categories: chartData.labels,
                                    axisBorder: { show: false },
                                    axisTicks: { show: false },
                                    labels: { style: { colors: '#6B7280', fontSize: '11px' } }
                                },
                                yaxis: {
                                    labels: { style: { colors: '#6B7280', fontSize: '11px' } }
                                },
                                grid: {
                                    strokeDashArray: 4,
                                    borderColor: '#F3F4F6'
                                },
                                colors: ['#1B5E4F'],
                                dataLabels: { enabled: false },
                                plotOptions: {
                                    bar: {
                                        borderRadius: 6,
                                        columnWidth: '45%',
                                        distributed: false
                                    }
                                },
                                tooltip: {
                                    y: { formatter: (val) => val + ' kali setoran' }
                                }
                            };
                            chart = new ApexCharts($el, options);
                            chart.render();
                        });
                    "
                    wire:ignore
                ></div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                <a href="<?php echo e(route('siswa.progres.dashboard')); ?>" class="text-xs font-semibold text-emerald-800 hover:text-emerald-900 inline-flex items-center gap-1">
                    Buka Detail Peta Progres Hafalan
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        
        <div class="lg:col-span-7 bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-gray-900" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                            Riwayat Setoran Terakhir
                        </h2>
                        <p class="text-xs text-gray-500">5 riwayat setoran ayat dan penilaian terbaru</p>
                    </div>
                    <a href="<?php echo e(route('siswa.hasil-hafalan')); ?>" class="text-xs font-semibold text-emerald-800 hover:text-emerald-900">
                        Lihat Semua →
                    </a>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($this->setoranTerbaru)): ?>
                    <div class="py-12 text-center rounded-xl bg-gray-50 border border-dashed border-gray-200">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400 bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-700">Belum ada riwayat setoran</p>
                        <p class="text-xs text-gray-400 mt-0.5">Setoran kamu akan muncul di sini setelah dinilai oleh Ustadz/ah.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->setoranTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setoran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $isLanjut = ($setoran['keputusan'] ?? '') === 'Lanjut' || ($setoran['keputusan']['value'] ?? '') === 'Lanjut';
                                $surahNama = $setoran['surah']['nama_latin'] ?? ('Surah ID: ' . ($setoran['surah_id'] ?? '-'));
                                $juzNomor = $setoran['juz']['nomor_juz'] ?? ($setoran['juz_id'] ?? '-');
                                $tgl = isset($setoran['absensi']['tanggal']) ? \Carbon\Carbon::parse($setoran['absensi']['tanggal'])->translatedFormat('d M Y') : \Carbon\Carbon::parse($setoran['created_at'])->translatedFormat('d M Y');
                                $predikatVal = is_array($setoran['predikat']) ? ($setoran['predikat']['value'] ?? '-') : ($setoran['predikat'] ?? '-');
                            ?>
                            <div class="p-3.5 rounded-xl border border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50/70 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-start gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 <?php echo e($isLanjut ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'); ?>">
                                        Juz <?php echo e($juzNomor); ?>

                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-900 truncate" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                                            <?php echo e($surahNama); ?> : <?php echo e($setoran['ayat_mulai'] ?? 1); ?>-<?php echo e($setoran['ayat_selesai'] ?? 1); ?>

                                        </p>
                                        <div class="flex flex-wrap items-center gap-2 mt-0.5 text-xs text-gray-500">
                                            <span><?php echo e($tgl); ?></span>
                                            <span>•</span>
                                            <span class="font-medium text-gray-700"><?php echo e($setoran['jenis_setoran']['value'] ?? $setoran['jenis_setoran']); ?></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($setoran['catatan'])): ?>
                                                <span>•</span>
                                                <span class="text-gray-500 italic truncate max-w-[200px]" title="<?php echo e($setoran['catatan']); ?>">"<?php echo e($setoran['catatan']); ?>"</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold <?php echo e($isLanjut ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200'); ?>">
                                        <?php echo e($isLanjut ? '✓ Lanjut' : '↺ Ulang'); ?>

                                    </span>
                                    <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700">
                                        <?php echo e($predikatVal); ?>

                                    </span>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Catatan: Terus istiqomah menambah ziyadah dan menjaga muroja'ah hafalan.</span>
            </div>
        </div>

    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
        <a href="<?php echo e(route('siswa.progres.dashboard')); ?>" class="group p-4 rounded-2xl bg-white border border-gray-200 hover:border-emerald-500 hover:shadow-md transition-all flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-emerald-800 bg-emerald-50 group-hover:bg-emerald-800 group-hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900 group-hover:text-emerald-800 transition-colors" style="font-family: 'Plus Jakarta Sans', sans-serif;">Peta Progres Hafalan</h3>
                <p class="text-xs text-gray-500">Lihat rincian capaian per juz & target</p>
            </div>
        </a>

        <a href="<?php echo e(route('siswa.hasil-hafalan')); ?>" class="group p-4 rounded-2xl bg-white border border-gray-200 hover:border-emerald-500 hover:shadow-md transition-all flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-blue-700 bg-blue-50 group-hover:bg-blue-700 group-hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition-colors" style="font-family: 'Plus Jakarta Sans', sans-serif;">Rekam Jejak Lengkap</h3>
                <p class="text-xs text-gray-500">Perjalanan, setoran & kehadiran</p>
            </div>
        </a>

        <a href="<?php echo e(route('siswa.referensi-juz')); ?>" class="group p-4 rounded-2xl bg-white border border-gray-200 hover:border-emerald-500 hover:shadow-md transition-all flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-purple-700 bg-purple-50 group-hover:bg-purple-700 group-hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900 group-hover:text-purple-700 transition-colors" style="font-family: 'Plus Jakarta Sans', sans-serif;">Referensi Al-Qur'an</h3>
                <p class="text-xs text-gray-500">Daftar 30 juz dan ayat untuk tilawah</p>
            </div>
        </a>

        <a href="<?php echo e(route('siswa.profil')); ?>" class="group p-4 rounded-2xl bg-white border border-gray-200 hover:border-emerald-500 hover:shadow-md transition-all flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-amber-700 bg-amber-50 group-hover:bg-amber-700 group-hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900 group-hover:text-amber-700 transition-colors" style="font-family: 'Plus Jakarta Sans', sans-serif;">Profil Saya</h3>
                <p class="text-xs text-gray-500">Data pribadi & riwayat akademik</p>
            </div>
        </a>
    </div>

</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/siswa/dashboard-siswa.blade.php ENDPATH**/ ?>