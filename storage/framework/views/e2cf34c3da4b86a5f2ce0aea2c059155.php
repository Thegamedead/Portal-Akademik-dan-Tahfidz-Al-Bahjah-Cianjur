
<div class="space-y-4">

    
    <div class="bg-white rounded-xl px-4 py-3 shadow-sm border border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center text-white shrink-0 shadow-sm" style="background-color: #1B5E4F;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-sm sm:text-base font-bold text-gray-900 leading-none" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        Dashboard Administrasi Tahfidz
                    </h1>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTahunAjaran): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            T.A. <?php echo e($activeTahunAjaran->label); ?>

                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <p class="text-[11px] text-gray-500 mt-1"><?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
            </div>
        </div>

        
        <div class="flex flex-wrap items-center gap-1.5">
            <a href="<?php echo e(route('admin.master-data.siswa.index')); ?>"
               class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg shadow-sm transition-colors">
                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Siswa
            </a>
            <a href="<?php echo e(route('admin.master-data.kelompok.index')); ?>"
               class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg shadow-sm transition-colors">
                <svg class="w-3.5 h-3.5 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Kelompok
            </a>
            <a href="<?php echo e(route('admin.usulan-kenaikan-tingkat.index')); ?>"
               class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-300 rounded-lg shadow-sm transition-colors">
                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                Usulan
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($usulanStats['diajukan'] > 0): ?>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black bg-amber-600 text-white">
                        <?php echo e($usulanStats['diajukan']); ?>

                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>
            <a href="<?php echo e(route('admin.kelulusan-program.index')); ?>"
               class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-emerald-900 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-lg shadow-sm transition-colors">
                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
                Kelulusan
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($masterData['kelulusan_pending'] > 0): ?>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-black bg-emerald-600 text-white animate-pulse">
                        <?php echo e($masterData['kelulusan_pending']); ?>

                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </a>
            <button type="button"
                    @click="$dispatch('open-integrated-rollover-modal')"
                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-bold text-white rounded-lg shadow-sm transition-colors"
                    style="background-color: #1B5E4F;"
                    onmouseover="this.style.backgroundColor='#164539'"
                    onmouseout="this.style.backgroundColor='#1B5E4F'">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Rollover
            </button>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

        
        <div class="bg-white rounded-xl p-3.5 shadow-sm border border-gray-200 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Siswa Aktif</span>
                <span class="w-7 h-7 rounded-lg flex items-center justify-center bg-emerald-100 text-emerald-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <div class="text-2xl font-extrabold text-gray-900 leading-none">
                    <?php echo e(number_format($masterData['total_siswa'])); ?>

                </div>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded <?php echo e($masterData['persen_penempatan'] == 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800'); ?>">
                    <?php echo e($masterData['persen_penempatan']); ?>% Ditempatkan
                </span>
            </div>
            <div class="mt-2.5 pt-2 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-600">
                <span>Ditempatkan: <strong class="text-gray-900"><?php echo e($masterData['siswa_ditempatkan']); ?></strong></span>
                <span>Belum: <strong class="<?php echo e($masterData['siswa_belum_ditempatkan'] > 0 ? 'text-amber-700 font-bold' : 'text-gray-900'); ?>"><?php echo e($masterData['siswa_belum_ditempatkan']); ?></strong></span>
            </div>
        </div>

        
        <div class="bg-white rounded-xl p-3.5 shadow-sm border border-gray-200 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Guru & Kelompok</span>
                <span class="w-7 h-7 rounded-lg flex items-center justify-center bg-cyan-100 text-cyan-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <div class="text-2xl font-extrabold text-gray-900 leading-none">
                    <?php echo e(number_format($masterData['total_guru'])); ?> <span class="text-xs font-medium text-gray-500">Guru</span>
                </div>
                <span class="text-[11px] font-bold text-cyan-800 bg-cyan-100 px-2 py-0.5 rounded">
                    <?php echo e($masterData['total_kelompok']); ?> Kelompok
                </span>
            </div>
            <div class="mt-2.5 pt-2 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-600">
                <span>D: <strong><?php echo e($masterData['kelompok_dasar']); ?></strong></span>
                <span>M: <strong><?php echo e($masterData['kelompok_menengah']); ?></strong></span>
                <span>A: <strong><?php echo e($masterData['kelompok_atas']); ?></strong></span>
            </div>
        </div>

        
        <div class="bg-white rounded-xl p-3.5 shadow-sm border border-gray-200 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Setoran Bulan Ini</span>
                <span class="w-7 h-7 rounded-lg flex items-center justify-center bg-teal-100 text-teal-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <div class="text-2xl font-extrabold text-gray-900 leading-none">
                    <?php echo e(number_format($activityStats['setoran_bulan_ini'])); ?>

                </div>
                <span class="text-[11px] font-bold text-teal-800 bg-teal-100 px-2 py-0.5 rounded">
                    <?php echo e(now()->translatedFormat('F')); ?>

                </span>
            </div>
            <div class="mt-2.5 pt-2 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-600">
                <span>Hari Ini: <strong class="text-gray-900"><?php echo e($activityStats['setoran_hari_ini']); ?></strong></span>
                <span>Minggu Ini: <strong class="text-gray-900"><?php echo e($activityStats['setoran_minggu_ini']); ?></strong></span>
            </div>
        </div>

        
        <div class="bg-white rounded-xl p-3.5 shadow-sm border-2 border-emerald-600 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Lulusan Program</span>
                <span class="w-7 h-7 rounded-lg flex items-center justify-center bg-emerald-700 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline justify-between">
                <div class="text-2xl font-black text-emerald-900 leading-none">
                    <?php echo e(number_format($masterData['siswa_lulus_total'])); ?> <span class="text-xs font-medium text-gray-500">Siswa</span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($masterData['kelulusan_pending'] > 0): ?>
                    <a href="<?php echo e(route('admin.kelulusan-program.index')); ?>"
                       class="text-[10px] font-bold text-amber-900 bg-amber-200 hover:bg-amber-300 px-2 py-0.5 rounded transition-colors shadow-sm animate-pulse">
                        <?php echo e($masterData['kelulusan_pending']); ?> Usulan Baru
                    </a>
                <?php else: ?>
                    <span class="text-[11px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded">
                        Target <?php echo e($jenjang['target_kelulusan']); ?> Juz
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="mt-2.5 pt-2 border-t border-emerald-100 flex items-center justify-between text-[11px] text-gray-600">
                <span>Tahun <?php echo e(now()->year); ?>: <strong class="text-emerald-800"><?php echo e($masterData['kelulusan_tahun_ini']); ?> lulus</strong></span>
                <a href="<?php echo e(route('admin.kelulusan-program.index')); ?>" class="text-emerald-700 hover:text-emerald-900 font-bold hover:underline">
                    Detail Lulusan &rarr;
                </a>
            </div>
        </div>

    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-stretch">

        
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Distribusi Siswa & Standar Kurikulum</h2>
                        <p class="text-xs text-gray-500">Pemetaan siswa, kelompok, dan target per jenjang</p>
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 shrink-0">
                        Syarat: <?php echo e($jenjang['target_kelulusan']); ?> Juz
                    </span>
                </div>

                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem; margin: 0.5rem 0;">

                    
                    <div class="rounded-lg p-2.5 border border-emerald-200 bg-emerald-50/40 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="w-5 h-5 rounded bg-emerald-700 text-white text-[10px] font-bold flex items-center justify-center">1</span>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-800 truncate">Dasar</span>
                            </div>
                            <p class="text-lg font-black text-gray-900 leading-tight"><?php echo e($jenjang['dasar']['siswa']); ?> <span class="text-[10px] font-normal text-gray-500">Siswa</span></p>
                            <p class="text-[10px] text-emerald-700 font-semibold mt-0.5"><?php echo e($jenjang['dasar']['persen']); ?>%</p>
                        </div>
                        <div class="mt-2 pt-1.5 border-t border-emerald-200 flex items-center justify-between text-[10px] text-gray-700">
                            <span class="truncate font-semibold"><?php echo e($jenjang['dasar']['juz_label']); ?></span>
                            <span class="font-bold text-emerald-800"><?php echo e($jenjang['dasar']['kelompok']); ?>K</span>
                        </div>
                    </div>

                    
                    <div class="rounded-lg p-2.5 border border-cyan-200 bg-cyan-50/40 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="w-5 h-5 rounded bg-cyan-700 text-white text-[10px] font-bold flex items-center justify-center">2</span>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-cyan-800 truncate">Menengah</span>
                            </div>
                            <p class="text-lg font-black text-gray-900 leading-tight"><?php echo e($jenjang['menengah']['siswa']); ?> <span class="text-[10px] font-normal text-gray-500">Siswa</span></p>
                            <p class="text-[10px] text-cyan-700 font-semibold mt-0.5"><?php echo e($jenjang['menengah']['persen']); ?>%</p>
                        </div>
                        <div class="mt-2 pt-1.5 border-t border-cyan-200 flex items-center justify-between text-[10px] text-gray-700">
                            <span class="truncate font-semibold"><?php echo e($jenjang['menengah']['juz_label']); ?></span>
                            <span class="font-bold text-cyan-800"><?php echo e($jenjang['menengah']['kelompok']); ?>K</span>
                        </div>
                    </div>

                    
                    <div class="rounded-lg p-2.5 border border-amber-200 bg-amber-50/40 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="w-5 h-5 rounded bg-amber-600 text-white text-[10px] font-bold flex items-center justify-center">3</span>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-amber-800 truncate">Atas</span>
                            </div>
                            <p class="text-lg font-black text-gray-900 leading-tight"><?php echo e($jenjang['atas']['siswa']); ?> <span class="text-[10px] font-normal text-gray-500">Siswa</span></p>
                            <p class="text-[10px] text-amber-700 font-semibold mt-0.5"><?php echo e($jenjang['atas']['persen']); ?>%</p>
                        </div>
                        <div class="mt-2 pt-1.5 border-t border-amber-200 flex items-center justify-between text-[10px] text-gray-700">
                            <span class="truncate font-semibold"><?php echo e($jenjang['atas']['juz_label']); ?></span>
                            <span class="font-bold text-amber-800"><?php echo e($jenjang['atas']['kelompok']); ?>K</span>
                        </div>
                    </div>

                </div>
            </div>

            
            <div class="mt-2 pt-2 border-t border-gray-100">
                <div class="flex items-center justify-between text-[11px] text-gray-600 mb-1">
                    <span>Proporsi Penempatan:</span>
                    <span class="font-bold text-gray-900"><?php echo e($jenjang['total_siswa']); ?> Siswa Terdistribusi</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 flex overflow-hidden">
                    <div class="bg-emerald-600 transition-all duration-500" style="width: <?php echo e(max($jenjang['dasar']['persen'], $jenjang['dasar']['persen'] > 0 ? 3 : 0)); ?>%"></div>
                    <div class="bg-cyan-600 transition-all duration-500" style="width: <?php echo e(max($jenjang['menengah']['persen'], $jenjang['menengah']['persen'] > 0 ? 3 : 0)); ?>%"></div>
                    <div class="bg-amber-500 transition-all duration-500" style="width: <?php echo e(max($jenjang['atas']['persen'], $jenjang['atas']['persen'] > 0 ? 3 : 0)); ?>%"></div>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Validasi Usulan & Kelulusan</h2>
                        <p class="text-xs text-gray-500">Pusat persetujuan kenaikan tingkat & kelulusan program</p>
                    </div>
                </div>

                
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold text-gray-700">Usulan Kenaikan Tingkat</span>
                        <a href="<?php echo e(route('admin.usulan-kenaikan-tingkat.index')); ?>" class="text-[11px] font-semibold text-emerald-700 hover:underline">
                            Lihat Semua &rarr;
                        </a>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 0.375rem; text-align: center;">
                        <a href="<?php echo e(route('admin.usulan-kenaikan-tingkat.index', ['filterStatus' => 'diajukan'])); ?>"
                           class="py-2 px-1 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-300 transition-colors">
                            <span class="text-[9px] font-bold text-amber-800 uppercase block truncate">Diajukan</span>
                            <span class="text-base font-black text-amber-900 leading-none mt-1 block"><?php echo e($usulanStats['diajukan']); ?></span>
                        </a>
                        <a href="<?php echo e(route('admin.usulan-kenaikan-tingkat.index', ['filterStatus' => 'disetujui'])); ?>"
                           class="py-2 px-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 transition-colors">
                            <span class="text-[9px] font-bold text-emerald-800 uppercase block truncate">Disetujui</span>
                            <span class="text-base font-black text-emerald-900 leading-none mt-1 block"><?php echo e($usulanStats['disetujui']); ?></span>
                        </a>
                        <a href="<?php echo e(route('admin.usulan-kenaikan-tingkat.index', ['filterStatus' => 'ditempatkan'])); ?>"
                           class="py-2 px-1 rounded-lg bg-blue-50 hover:bg-blue-100 border border-blue-300 transition-colors">
                            <span class="text-[9px] font-bold text-blue-800 uppercase block truncate">Ditempatkan</span>
                            <span class="text-base font-black text-blue-900 leading-none mt-1 block"><?php echo e($usulanStats['ditempatkan']); ?></span>
                        </a>
                        <a href="<?php echo e(route('admin.usulan-kenaikan-tingkat.index', ['filterStatus' => 'ditolak'])); ?>"
                           class="py-2 px-1 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-300 transition-colors">
                            <span class="text-[9px] font-bold text-rose-800 uppercase block truncate">Ditolak</span>
                            <span class="text-base font-black text-rose-900 leading-none mt-1 block"><?php echo e($usulanStats['ditolak']); ?></span>
                        </a>
                    </div>
                </div>

                
                <div class="pt-2 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold text-gray-700">Usulan Kelulusan Program Tahfidz</span>
                        <a href="<?php echo e(route('admin.kelulusan-program.index')); ?>" class="text-[11px] font-semibold text-emerald-700 hover:underline">
                            Kelola &rarr;
                        </a>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestActivities['kelulusans']->count() > 0): ?>
                        <div class="space-y-1.5">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $latestActivities['kelulusans']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelulusan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="p-2 rounded-lg border border-amber-300 bg-amber-50 flex items-center justify-between text-xs">
                                    <div>
                                        <p class="font-bold text-gray-900"><?php echo e($kelulusan->siswa->nama ?? '-'); ?></p>
                                        <p class="text-[10px] text-gray-600">Pengusul: <?php echo e($kelulusan->guruPengusul->nama ?? '-'); ?></p>
                                    </div>
                                    <a href="<?php echo e(route('admin.kelulusan-program.index')); ?>"
                                       class="text-[10px] font-bold px-2 py-1 rounded bg-emerald-700 hover:bg-emerald-800 text-white shadow-sm transition-colors">
                                        Review
                                    </a>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-2.5 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-between text-xs text-gray-600">
                            <div class="flex items-center gap-1.5">
                                <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </span>
                                <span class="text-[11px]">Tidak ada usulan kelulusan pending</span>
                            </div>
                            <span class="font-bold text-[11px] text-gray-700"><?php echo e($masterData['siswa_lulus_total']); ?> Lulus</span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-md flex items-center justify-center text-white shrink-0 bg-teal-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xs sm:text-sm font-bold text-gray-900">Aktivitas Setoran Terkini</h2>
                    <p class="text-[11px] text-gray-500">Riwayat 5 transaksi setoran hafalan siswa terbaru dari guru pembimbing</p>
                </div>
            </div>
            <div class="text-xs text-gray-600">
                Total Bulan Ini: <strong class="text-gray-900"><?php echo e(number_format($activityStats['setoran_bulan_ini'])); ?> Setoran</strong>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestActivities['setorans']->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-700 text-[11px] uppercase tracking-wider bg-gray-50">
                            <th class="py-2 px-3 font-bold">Siswa</th>
                            <th class="py-2 px-3 font-bold">Materi / Ayat</th>
                            <th class="py-2 px-3 font-bold">Kelompok & Pembimbing</th>
                            <th class="py-2 px-3 font-bold text-right">Penilaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $latestActivities['setorans']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setoran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-2.5 px-3">
                                    <div class="font-bold text-gray-900"><?php echo e($setoran->absensi?->siswa?->nama ?? '-'); ?></div>
                                    <div class="text-[10px] text-gray-500 font-mono">NIS: <?php echo e($setoran->absensi?->siswa?->nis ?? '-'); ?></div>
                                </td>
                                <td class="py-2.5 px-3">
                                    <div class="font-bold text-gray-900">
                                        <?php echo e($setoran->surah?->nama_latin ?? '-'); ?>

                                    </div>
                                    <div class="text-[10px] text-gray-600">
                                        Ayat <?php echo e($setoran->ayat_mulai); ?>–<?php echo e($setoran->ayat_selesai); ?> (Juz <?php echo e($setoran->juz?->nomor_juz ?? $setoran->juz_id); ?>)
                                    </div>
                                </td>
                                <td class="py-2.5 px-3">
                                    <div class="font-bold text-gray-800"><?php echo e($setoran->absensi?->kelompok?->nama ?? '-'); ?></div>
                                    <div class="text-[10px] text-gray-600"><?php echo e($setoran->absensi?->kelompok?->guruPembimbing?->nama ?? '-'); ?></div>
                                </td>
                                <td class="py-2.5 px-3 text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <?php echo e($setoran->predikat?->name ?? 'Lancar'); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="py-8 text-center flex flex-col items-center justify-center bg-gray-50 rounded-lg border border-dashed border-gray-300">
                <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center mb-1.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-xs font-bold text-gray-700">Belum Ada Aktivitas Setoran Tercatat</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Data setoran akan terupdate otomatis saat guru pembimbing menginput hafalan harian siswa</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.integrated-rollover-modal');

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3056091863-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/admin/dashboard-admin.blade.php ENDPATH**/ ?>