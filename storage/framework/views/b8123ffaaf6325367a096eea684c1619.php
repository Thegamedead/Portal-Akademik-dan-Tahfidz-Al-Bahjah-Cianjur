<div class="space-y-6 min-h-screen" style="background:#f8fafc; padding-bottom: 2rem;">
    
    
    
    <div class="px-6 pt-6">
        <div class="rounded-2xl bg-white shadow-sm p-6 flex flex-col md:flex-row md:items-center justify-between gap-4" style="border:1px solid #E5E7EB;">
            <div>
                <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Riwayat Absensi dan Setoran Hafalan
                </h1>
                <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelompokFilter): ?>
                        Track record setoran selama di <strong class="text-emerald-700"><?php echo e($kelompokFilter->nama); ?></strong>
                        (Tingkat: <strong class="text-emerald-700"><?php echo e($kelompokFilter->tingkat instanceof \BackedEnum ? $kelompokFilter->tingkat->value : $kelompokFilter->tingkat); ?></strong>)
                    <?php else: ?>
                        Track record seluruh setoran hafalan siswa <strong class="text-emerald-700">(Semua Tingkat: Dasar → Menengah → Atas)</strong>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($summaryStats['first_date']): ?>
                    <div class="flex flex-col justify-center px-4 py-2.5 rounded-xl text-right" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                        <span class="text-[10px] font-bold uppercase tracking-wider mb-0.5" style="color: #15803d;">Periode Setoran</span>
                        <span class="text-xs font-bold leading-tight" style="color: #14532d;">
                            <?php echo e(\Carbon\Carbon::parse($summaryStats['first_date'])->translatedFormat('d M Y')); ?>

                            <span class="text-green-400 mx-1">—</span>
                            <?php echo e(\Carbon\Carbon::parse($summaryStats['last_date'])->translatedFormat('d M Y')); ?>

                        </span>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <a href="<?php echo e(url()->previous()); ?>"
                   class="shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-sm group"
                   style="background-color:#1B5E4F; color:#FFFFFF; border:1.5px solid #1B5E4F;"
                   title="Kembali"
                   onmouseover="this.style.backgroundColor='#14503f'" onmouseout="this.style.backgroundColor='#1B5E4F'">
                    <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    
    <div class="px-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_2px_15px_-3px_rgba(6,81,237,0.05)] p-4 sm:p-5 relative overflow-hidden">
            
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-full blur-2xl opacity-70 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-24 h-24 bg-gradient-to-tr from-blue-50 to-indigo-50 rounded-full blur-2xl opacity-60 pointer-events-none"></div>

            <?php
                $isNonAktif = !$siswa->status_aktif || $siswa->status_akademik !== 'Aktif' || !empty($siswa->tanggal_keluar) || !empty($siswa->jenis_keluar);
            ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isNonAktif): ?>
                <div class="mb-5 p-4 rounded-xl border border-amber-300 bg-amber-50/80 flex flex-col lg:flex-row lg:items-center justify-between gap-4 shadow-sm relative z-10">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-400/30 flex items-center justify-center text-amber-700 shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm font-bold text-amber-900 font-jakarta">Siswa Berstatus Non-Aktif</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-200/80 text-amber-900 border border-amber-300">
                                    <?php echo e($siswa->status_akademik); ?>

                                </span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa->status_tahfidz === 'Lulus Program Tahfidz' || $summaryStats['is_lulus']): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        🎓 Alumni Tahfidz (Lulus)
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <p class="text-xs text-amber-800 mt-1 font-medium leading-relaxed">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa->jenis_keluar): ?>
                                    <strong>Alasan Keluar:</strong> <?php echo e($siswa->jenis_keluar); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa->tujuan_sekolah): ?> &bull; <strong>Tujuan:</strong> <?php echo e($siswa->tujuan_sekolah); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa->keterangan_keluar): ?> <em>(<?php echo e($siswa->keterangan_keluar); ?>)</em> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    Status akademik siswa ini tercatat sebagai <strong><?php echo e($siswa->status_akademik); ?></strong>.
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs bg-white/90 p-3 rounded-lg border border-amber-200/70 shrink-0 text-gray-700">
                        <div>
                            <span class="text-gray-500 block text-[11px]">Tanggal Keluar:</span>
                            <strong class="text-gray-900 font-semibold"><?php echo e($siswa->tanggal_keluar ? $siswa->tanggal_keluar->format('d/m/Y') : '-'); ?></strong>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-[11px]">Kelompok & Tingkat Saat Keluar:</span>
                            <strong class="text-gray-900 font-semibold"><?php echo e($siswa->kelompok ? $siswa->kelompok->nama . ' (' . ($siswa->kelompok->tingkat->value ?? $siswa->kelompok->tingkat) . ')' : '-'); ?></strong>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-4 z-10">
                
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-[#1B5E4F] text-white text-lg font-bold shadow-sm">
                            <?php echo e(mb_substr($siswa->nama, 0, 1)); ?>

                        </span>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 font-jakarta leading-none tracking-tight"><?php echo e($siswa->nama); ?></h1>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isNonAktif): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                Non-Aktif
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <table class="text-xs sm:text-sm text-gray-500 font-medium mt-4 border-separate" style="border-spacing: 0 0.625rem;">
                        <tbody>
                            
                            <tr>
                                <td class="whitespace-nowrap pr-2">
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/></svg>
                                        NIS
                                    </div>
                                </td>
                                <td class="px-3 text-gray-400">:</td>
                                <td><strong class="text-gray-900"><?php echo e($siswa->nis ?? '-'); ?></strong></td>
                            </tr>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isNonAktif): ?>
                                <tr>
                                    <td class="whitespace-nowrap pr-2">
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Status Siswa
                                        </div>
                                    </td>
                                    <td class="px-3 text-gray-400">:</td>
                                    <td>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                            <?php echo e($siswa->status_akademik); ?> (Non-Aktif)
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="whitespace-nowrap pr-2">
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Keterangan Keluar
                                        </div>
                                    </td>
                                    <td class="px-3 text-gray-400">:</td>
                                    <td>
                                        <strong class="text-gray-900"><?php echo e($siswa->jenis_keluar ?? $siswa->status_akademik); ?></strong>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa->tanggal_keluar): ?>
                                            <span class="text-xs text-gray-500 ml-1">(pada <?php echo e($siswa->tanggal_keluar->format('d/m/Y')); ?>)</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa->keterangan_keluar): ?>
                                            <span class="text-xs text-gray-600 ml-1">· <?php echo e($siswa->keterangan_keluar); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <tr>
                                <td class="whitespace-nowrap pr-2">
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Kelompok
                                    </div>
                                </td>
                                <td class="px-3 text-gray-400">:</td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelompokFilter): ?>
                                        <strong class="text-gray-900"><?php echo e($kelompokFilter->nama); ?> (Tingkat <?php echo e($kelompokFilter->tingkat->value ?? $kelompokFilter->tingkat); ?>)</strong>
                                    <?php else: ?>
                                        <strong class="text-emerald-700">Semua Tingkat (Dasar → Menengah → Atas)</strong>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isNonAktif && $siswa->kelompok): ?>
                                            <span class="text-xs text-gray-500 ml-1">(Terakhir: <?php echo e($siswa->kelompok->nama); ?>)</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa->kelas): ?>
                                <tr>
                                    <td class="whitespace-nowrap pr-2">
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            Kelas
                                        </div>
                                    </td>
                                    <td class="px-3 text-gray-400">:</td>
                                    <td><strong class="text-gray-900"><?php echo e($siswa->kelas->nama_kelas); ?></strong></td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <tr>
                                <td class="whitespace-nowrap pr-2">
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <svg class="w-4 h-4 text-purple-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        Target Hafalan
                                    </div>
                                </td>
                                <td class="px-3 text-gray-400">:</td>
                                <td><strong class="text-gray-900">Juz <?php echo e($summaryStats['target_juz_str']); ?> (<?php echo e(number_format($summaryStats['target_ayat'])); ?> Ayat)</strong></td>
                            </tr>

                            
                            <tr>
                                <td class="whitespace-nowrap pr-2">
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <svg class="w-4 h-4 text-sky-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                        Capaian Target
                                    </div>
                                </td>
                                <td class="px-3 text-gray-400">:</td>
                                <td>
                                    <strong class="text-gray-900"><?php echo e(number_format($summaryStats['ayat_tercakup'])); ?></strong> / <?php echo e(number_format($summaryStats['target_ayat'])); ?> Ayat
                                    <span class="text-xs text-gray-500 font-semibold ml-1">(<?php echo e($summaryStats['persentase_target']); ?>%)</span>
                                </td>
                            </tr>

                            
                            <tr>
                                <td class="whitespace-nowrap pr-2">
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <svg class="w-4 h-4 <?php echo e($summaryStats['is_lulus'] ? 'text-green-500' : 'text-amber-500'); ?> shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Predikat
                                    </div>
                                </td>
                                <td class="px-3 text-gray-400">:</td>
                                <td>
                                    <strong class="<?php echo e($summaryStats['is_lulus'] ? 'text-green-600' : 'text-amber-600'); ?>"><?php echo e($summaryStats['predikat_label']); ?></strong>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($summaryStats['is_lulus'] && $summaryStats['last_date']): ?>
                                        <span class="text-xs text-gray-500 font-semibold ml-2">(Lulus pada <?php echo e(\Carbon\Carbon::parse($summaryStats['last_date'])->translatedFormat('d M Y')); ?>)</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>

                            
                            <tr>
                                <td class="whitespace-nowrap pr-2">
                                    <div class="flex items-center gap-2 text-gray-600">
                                        <svg class="w-4 h-4 text-teal-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Durasi Belajar
                                    </div>
                                </td>
                                <td class="px-3 text-gray-400">:</td>
                                <td>
                                    <strong class="text-gray-900"><?php echo e($summaryStats['durasi_lengkap_str']); ?></strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                
                <div class="flex items-center gap-3 sm:gap-4 shrink-0 pb-1 sm:pb-0">
                    <div class="w-48 sm:w-64">
                        <?php if (isset($component)) { $__componentOriginal6962575527da6e31b80032f98e723ee8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6962575527da6e31b80032f98e723ee8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-field','data' => ['wireModel' => 'filterSurah','placeholder' => 'Cari Surah...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire-model' => 'filterSurah','placeholder' => 'Cari Surah...']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6962575527da6e31b80032f98e723ee8)): ?>
<?php $attributes = $__attributesOriginal6962575527da6e31b80032f98e723ee8; ?>
<?php unset($__attributesOriginal6962575527da6e31b80032f98e723ee8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6962575527da6e31b80032f98e723ee8)): ?>
<?php $component = $__componentOriginal6962575527da6e31b80032f98e723ee8; ?>
<?php unset($__componentOriginal6962575527da6e31b80032f98e723ee8); ?>
<?php endif; ?>
                    </div>
                    <div x-data="{
                        open: false,
                        top: 0,
                        right: 0,
                        toggle() {
                            const rect = this.$refs.btn.getBoundingClientRect();
                            this.top = rect.bottom + 8;
                            this.right = window.innerWidth - rect.right;
                            this.open = !this.open;
                        },
                        onScroll() { this.open = false; }
                    }" x-init="window.addEventListener('scroll', () => onScroll(), { passive: true })"
                       class="relative shrink-0">
                        <button x-ref="btn" @click="toggle()" type="button"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-colors focus:outline-none whitespace-nowrap cursor-pointer shadow-sm hover:bg-gray-50 h-[38px]"
                            style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Filter
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterJenis || $filterKeputusan || $sortBy !== 'tanggal_asc'): ?>
                                <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                    <?php echo e(collect([$filterJenis, $filterKeputusan, ($sortBy !== 'tanggal_asc' ? $sortBy : null)])->filter()->count()); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                             :style="`position:fixed; top:${top}px; right:${right}px; z-index:9999;`"
                             class="w-72 rounded-xl shadow-xl bg-white overflow-hidden"
                             style="display:none; border:1px solid #E5E7EB;">
                            <div class="p-4 space-y-4">
                                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                                    <h3 class="text-sm font-semibold text-gray-800">Filter Riwayat</h3>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterSurah || $filterJenis || $filterKeputusan || $sortBy !== 'tanggal_asc' || $kelompokId !== null): ?>
                                        <button wire:click="resetFilter" type="button" class="text-xs font-medium text-emerald-700 hover:underline">Reset</button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">Jenis Setoran</label>
                                    <select wire:model.live="filterJenis" class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all" style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                        <option value="">Semua Jenis</option>
                                        <option value="Hafalan Baru">Hafalan Baru</option>
                                        <option value="Ulang">Ulang</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">Keputusan</label>
                                    <select wire:model.live="filterKeputusan" class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all" style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                        <option value="">Semua Keputusan</option>
                                        <option value="Lanjut">Lanjut</option>
                                        <option value="Ulang">Ulang</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">Urutkan</label>
                                    <select wire:model.live="sortBy" class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all" style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                        <option value="tanggal_asc">Terlama Dulu</option>
                                        <option value="tanggal_desc">Terbaru Dulu</option>
                                        <option value="surah_asc">Urutan Surah</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($journeyStages)): ?>
        <div class="px-6">
            <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-xs">
                <div class="flex items-center justify-between gap-2 mb-3 pb-2 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <span class="text-base">📜</span>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 font-jakarta">
                            Tahapan Jenjang & Kelompok Siswa
                        </h3>
                        <span class="text-[11px] text-gray-400 font-medium">(Klik kartu untuk memfilter kelompok)</span>
                    </div>

                    
                    <button
                        wire:click="setFilterKelompok(null)"
                        type="button"
                        class="px-3 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer shadow-xs <?php echo e(is_null($kelompokId) ? 'bg-[#1B5E4F] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'); ?>"
                    >
                        🌐 Tampilkan Semua Tingkat
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $journeyStages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $stg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $isActive = $kelompokId == $stg['kelompok_id'];
                            $bgBadge = $stg['tingkat'] === 'Dasar' ? '#EFF6FF' : ($stg['tingkat'] === 'Menengah' ? '#FFFBEB' : '#ECFDF5');
                            $textBadge = $stg['tingkat'] === 'Dasar' ? '#1D4ED8' : ($stg['tingkat'] === 'Menengah' ? '#B45309' : '#047857');
                            $borderBadge = $stg['tingkat'] === 'Dasar' ? '#BFDBFE' : ($stg['tingkat'] === 'Menengah' ? '#FDE68A' : '#A7F3D0');
                        ?>
                        <div
                            wire:click="setFilterKelompok(<?php echo e($stg['kelompok_id']); ?>)"
                            class="rounded-xl p-3.5 transition-all cursor-pointer border <?php echo e($isActive ? 'border-[#1B5E4F] ring-2 ring-[#1B5E4F]/20 bg-emerald-50/40 shadow-sm' : 'border-gray-200 bg-gray-50/60 hover:bg-white hover:shadow-xs'); ?>"
                        >
                            <div class="flex items-center justify-between gap-1.5 pb-2 mb-2 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold"
                                          style="background-color: <?php echo e($bgBadge); ?>; color: <?php echo e($textBadge); ?>; border: 1px solid <?php echo e($borderBadge); ?>;">
                                        <?php echo e($stg['tingkat']); ?>

                                    </span>
                                    <strong class="text-xs text-gray-900 font-jakarta"><?php echo e($stg['kelompok_nama']); ?></strong>
                                </div>
                                <span class="text-[11px] font-bold text-emerald-800 bg-emerald-100/70 px-2 py-0.5 rounded">
                                    ⏱️ <?php echo e($stg['durasi_text']); ?>

                                </span>
                            </div>

                            <div class="space-y-1 text-xs">
                                <p class="text-gray-600 text-[11px] truncate">
                                    <span class="text-gray-400">Guru:</span> <strong><?php echo e($stg['guru_nama']); ?></strong>
                                </p>
                                <p class="text-gray-600 text-[11px]">
                                    <span class="text-gray-400">Periode:</span> <?php echo e($stg['tanggal_masuk']); ?> – <?php echo e($stg['tanggal_keluar']); ?>

                                </p>
                                <div class="flex items-center justify-between pt-1 border-t border-gray-100 text-[11px]">
                                    <span class="text-gray-500 font-medium">🎯 <?php echo e($stg['target_juz']); ?></span>
                                    <span class="font-bold <?php echo e($stg['is_tuntas'] ? 'text-emerald-700' : 'text-amber-700'); ?>">
                                        🏆 <?php echo e($stg['capaian_text']); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="px-6 pb-8">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden" style="border:1px solid #d1d5db;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($this->setoranData)): ?>
                <div class="px-6 py-16 text-center">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <p class="text-gray-400 text-sm font-medium">Belum ada data setoran ditemukan.</p>
                    <p class="text-gray-300 text-xs mt-1">Coba ubah filter atau siswa ini belum memiliki riwayat setoran pada kelompok ini.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm" style="border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f3f4f6; border-bottom:2px solid #6b7280;">
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wide" style="border-right:1px solid #6b7280;">Tanggal</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wide" style="border-right:1px solid #6b7280;">Absensi</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wide whitespace-nowrap" style="border-right:1px solid #6b7280;">Kelompok / Tingkat</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wide whitespace-nowrap" style="border-right:1px solid #6b7280;">Setoran Ke-</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wide" style="border-right:1px solid #6b7280;">Surah</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wide" style="border-right:1px solid #6b7280;">Jenis</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wide" style="border-right:1px solid #6b7280;">Ayat</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wide" style="border-right:1px solid #6b7280;">Jml</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wide" style="border-right:1px solid #6b7280;">Tajwid</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wide" style="border-right:1px solid #6b7280;">Kelancaran</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wide" style="border-right:1px solid #6b7280;">Makhraj</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wide" style="border-right:1px solid #6b7280;">Fashahah</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wide" style="border-right:1px solid #6b7280;">Predikat</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600 uppercase tracking-wide" style="border-right:1px solid #6b7280;">Keputusan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 uppercase tracking-wide">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->setoranData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date => $setoransOnDate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php
                                    $dateRowspan = count($setoransOnDate);
                                    $dateObj = \Carbon\Carbon::parse($date);
                                ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $setoransOnDate; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php
                                        $isLanjut = $row['keputusan'] !== null ? ($row['keputusan'] instanceof \App\Enums\Keputusan
                                            ? $row['keputusan'] === \App\Enums\Keputusan::Lanjut
                                            : $row['keputusan'] === 'Lanjut') : null;
                                        $rowBg = $row['is_absensi_only'] ? '#f9fafb' : ($isLanjut === false ? '#ef4444' : '#ffffff');
                                        $jenisVal = $row['jenis'] instanceof \App\Enums\JenisSetoran
                                            ? $row['jenis']->value
                                            : (string) $row['jenis'];
                                        $jenisBadgeLabel = $jenisVal === 'Hafalan Baru' ? 'Hafalan Baru' : 'Ulang';
                                        $jenisBadgeClass = $jenisVal === 'Hafalan Baru' ? 'bg-blue-100 text-blue-700' : 'bg-cyan-100 text-cyan-700';
                                        $predikatVal = $row['predikat'] ? (
                                            $row['predikat'] instanceof \App\Enums\SkorKomponen ? $row['predikat']->value : (string) $row['predikat']
                                        ) : null;
                                        $skorMap = fn($s) => $s ? ($s instanceof \App\Enums\SkorKomponen ? $s->value : (string) $s) : null;
                                        $skorColor = fn($v) => match($v) {
                                            'Lancar' => $isLanjut === false ? 'text-white font-bold' : 'text-green-700 font-bold',
                                            'Kurang Lancar' => $isLanjut === false ? 'text-white font-semibold' : 'text-amber-600 font-semibold',
                                            'Tidak Lancar' => $isLanjut === false ? 'text-white font-bold' : 'text-red-600 font-bold',
                                            default => $isLanjut === false ? 'text-white' : 'text-gray-400',
                                        };
                                        $isLastInDate = $idx === $dateRowspan - 1;
                                    ?>
                                    <tr style="background:<?php echo e($rowBg); ?>;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($idx === 0): ?>
                                        <?php
                                            $allUlangOnDate = collect($setoransOnDate)->where('is_absensi_only', false)->every(function($r) {
                                                $k = $r['keputusan'];
                                                return ($k instanceof \App\Enums\Keputusan ? $k === \App\Enums\Keputusan::Ulang : $k === 'Ulang');
                                            }) && collect($setoransOnDate)->where('is_absensi_only', false)->count() > 0;
                                            $headerCellBg = $allUlangOnDate ? '#ef4444' : '#f9fafb';
                                            $headerCellColor = $allUlangOnDate ? 'color:#ffffff;' : '';
                                        ?>
                                        <td rowspan="<?php echo e($dateRowspan); ?>" class="px-4 py-2.5 font-medium whitespace-nowrap align-top" style="background:<?php echo e($headerCellBg); ?>; <?php echo e($headerCellColor); ?> border-right:1px solid #9ca3af; border-bottom:2px solid #d1d5db;">
                                            <?php echo e($dateObj->translatedFormat('d M Y')); ?>

                                            <div class="<?php echo e($allUlangOnDate ? 'text-red-200' : 'text-gray-400'); ?>"><?php echo e($dateObj->translatedFormat('l')); ?></div>
                                        </td>

                                        <td rowspan="<?php echo e($dateRowspan); ?>" class="px-4 py-2.5 text-center align-top" style="background:<?php echo e($headerCellBg); ?>; border-right:1px solid #9ca3af; border-bottom:2px solid #d1d5db;">
                                            <?php
                                                $bgStatus = match($row['status_kehadiran']) {
                                                    'Hadir' => 'bg-green-100 text-green-700',
                                                    'Izin' => 'bg-blue-100 text-blue-700',
                                                    'Sakit' => 'bg-orange-100 text-orange-700',
                                                    'Alfa' => 'bg-red-100 text-red-700',
                                                    'Libur' => 'bg-purple-100 text-purple-700',
                                                    default => 'bg-gray-100 text-gray-700'
                                                };
                                            ?>
                                            <span class="inline-flex px-2 py-0.5 rounded font-bold <?php echo e($bgStatus); ?>">
                                                <?php echo e($row['status_kehadiran']); ?>

                                            </span>
                                        </td>

                                        
                                        <td rowspan="<?php echo e($dateRowspan); ?>" class="px-3 py-2.5 text-center align-top whitespace-nowrap" style="background:<?php echo e($headerCellBg); ?>; border-right:1px solid #9ca3af; border-bottom:2px solid #d1d5db;">
                                            <?php
                                                $tkt = $row['kelompok_tingkat'];
                                                $tktBadge = $tkt === 'Dasar' ? 'bg-blue-50 text-blue-700 border-blue-200' : ($tkt === 'Menengah' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200');
                                            ?>
                                            <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-bold border <?php echo e($tktBadge); ?>">
                                                <?php echo e($row['kelompok_nama']); ?> (<?php echo e($tkt); ?>)
                                            </span>
                                            <div class="text-[10px] text-gray-400 mt-0.5">Guru: <?php echo e($row['guru_nama']); ?></div>
                                        </td>

                                        <td rowspan="<?php echo e($dateRowspan); ?>" class="px-4 py-2.5 text-center align-top" style="background:<?php echo e($headerCellBg); ?>; border-right:1px solid #9ca3af; border-bottom:2px solid #d1d5db;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['setoran_ke'] === '-'): ?>
                                                <span class="font-bold <?php echo e($allUlangOnDate ? 'text-red-200' : 'text-gray-400'); ?>">—</span>
                                            <?php else: ?>
                                                <span class="font-bold" style="<?php echo e($allUlangOnDate ? 'color:#fee2e2;' : 'color:#1B5E4F;'); ?>">#<?php echo e($row['setoran_ke']); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <td class="px-4 py-2.5" style="border-right:1px solid #d1d5db; border-bottom:<?php echo e($isLastInDate ? '2px solid #d1d5db' : '1px solid #e5e7eb'); ?>;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['is_absensi_only']): ?>
                                                <div class="font-bold text-gray-500">—</div>
                                            <?php else: ?>
                                                <div class="font-bold <?php echo e($isLanjut === false ? 'text-white' : 'text-gray-800'); ?>"><?php echo e($row['surah_nama']); ?></div>
                                                <div style="<?php echo e($isLanjut === false ? 'color:#fecaca;' : 'color:#6b7280;'); ?>">Juz <?php echo e($row['juz_ke']); ?></div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2.5 text-center" style="border-right:1px solid #d1d5db; border-bottom:<?php echo e($isLastInDate ? '2px solid #d1d5db' : '1px solid #e5e7eb'); ?>;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['is_absensi_only']): ?>
                                                <span class="text-gray-300 font-sans">—</span>
                                            <?php elseif($isLanjut === false): ?>
                                                <span class="font-bold text-white"><?php echo e($jenisBadgeLabel); ?></span>
                                            <?php else: ?>
                                                <span class="px-2 py-0.5 rounded-full font-bold whitespace-nowrap <?php echo e($jenisBadgeClass); ?>">
                                                    <?php echo e($jenisBadgeLabel); ?>

                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2.5 text-center whitespace-nowrap" style="border-right:1px solid #d1d5db; border-bottom:<?php echo e($isLastInDate ? '2px solid #d1d5db' : '1px solid #e5e7eb'); ?>;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['is_absensi_only']): ?>
                                                <span class="text-gray-300 font-sans">—</span>
                                            <?php else: ?>
                                                <span class="<?php echo e($isLanjut === false ? 'text-white' : 'text-gray-800'); ?>"><?php echo e($row['ayat_mulai']); ?>–<?php echo e($row['ayat_selesai']); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2.5 text-center font-bold" style="border-right:1px solid #d1d5db; border-bottom:<?php echo e($isLastInDate ? '2px solid #d1d5db' : '1px solid #e5e7eb'); ?>;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['is_absensi_only']): ?>
                                                <span class="text-gray-300 font-normal">—</span>
                                            <?php else: ?>
                                                <span class="<?php echo e($isLanjut === false ? 'text-white' : 'text-gray-800'); ?>"><?php echo e($row['jumlah_ayat']); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2.5 text-center <?php echo e($skorColor($skorMap($row['skor_tajwid']))); ?>" style="border-right:1px solid #d1d5db; border-bottom:<?php echo e($isLastInDate ? '2px solid #d1d5db' : '1px solid #e5e7eb'); ?>;">
                                            <?php echo e($skorMap($row['skor_tajwid']) ?? '—'); ?>

                                        </td>
                                        <td class="px-4 py-2.5 text-center <?php echo e($skorColor($skorMap($row['skor_kelancaran']))); ?>" style="border-right:1px solid #d1d5db; border-bottom:<?php echo e($isLastInDate ? '2px solid #d1d5db' : '1px solid #e5e7eb'); ?>;">
                                            <?php echo e($skorMap($row['skor_kelancaran']) ?? '—'); ?>

                                        </td>
                                        <td class="px-4 py-2.5 text-center <?php echo e($skorColor($skorMap($row['skor_makhraj']))); ?>" style="border-right:1px solid #d1d5db; border-bottom:<?php echo e($isLastInDate ? '2px solid #d1d5db' : '1px solid #e5e7eb'); ?>;">
                                            <?php echo e($skorMap($row['skor_makhraj']) ?? '—'); ?>

                                        </td>
                                        <td class="px-4 py-2.5 text-center <?php echo e($skorColor($skorMap($row['skor_fashahah']))); ?>" style="border-right:1px solid #d1d5db; border-bottom:<?php echo e($isLastInDate ? '2px solid #d1d5db' : '1px solid #e5e7eb'); ?>;">
                                            <?php echo e($skorMap($row['skor_fashahah']) ?? '—'); ?>

                                        </td>
                                        <td class="px-4 py-2.5 text-center" style="border-right:1px solid #d1d5db; border-bottom:<?php echo e($isLastInDate ? '2px solid #d1d5db' : '1px solid #e5e7eb'); ?>;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['is_absensi_only']): ?>
                                                <span class="text-gray-300">—</span>
                                            <?php elseif($isLanjut === false && $predikatVal): ?>
                                                <span class="font-bold text-white"><?php echo e($predikatVal); ?></span>
                                            <?php elseif($predikatVal): ?>
                                                <span class="px-2 py-0.5 rounded font-bold <?php echo e($predikatVal === 'Lancar' ? 'bg-green-100 text-green-700' : ($predikatVal === 'Kurang Lancar' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')); ?>">
                                                    <?php echo e($predikatVal); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-gray-300">—</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2.5 text-center" style="border-right:1px solid #d1d5db; border-bottom:<?php echo e($isLastInDate ? '2px solid #d1d5db' : '1px solid #e5e7eb'); ?>;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['is_absensi_only']): ?>
                                                <span class="text-gray-300">—</span>
                                            <?php elseif($isLanjut === false): ?>
                                                <span class="inline-flex px-2 py-0.5 rounded font-bold bg-white text-red-600">
                                                    Ulang
                                                </span>
                                            <?php elseif($isLanjut === true): ?>
                                                <span class="inline-flex px-2 py-0.5 rounded font-bold bg-green-100 text-green-700">
                                                    Lanjut
                                                </span>
                                            <?php else: ?>
                                                <span class="text-gray-300">—</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-4 py-2.5 <?php echo e($isLanjut === false ? 'text-white' : 'text-gray-500'); ?>" style="border-bottom:<?php echo e($isLastInDate ? '2px solid #d1d5db' : '1px solid #e5e7eb'); ?>;">
                                            <?php echo e($row['catatan'] ?? '—'); ?>

                                        </td>
                                    </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tbody>
                        <tfoot>
                            <?php
                                $isLulus = ($summaryStats['status_tuntas'] === 'Tuntas' || $summaryStats['persentase_target'] >= 100);
                            ?>
                            <tr style="background:#1B5E4F; color:#FFFFFF; border-top:3px solid #134338;">
                                <td colspan="7" class="px-4 py-3.5 text-right font-bold tracking-wider uppercase text-xs">
                                    TOTAL SETORAN & KEPUTUSAN:
                                </td>
                                <td class="px-4 py-3.5 text-center font-extrabold text-white text-sm" style="border-right:1px solid rgba(255,255,255,0.2); background:rgba(0,0,0,0.15);">
                                    <?php echo e(number_format($summaryStats['total_ayat'])); ?>

                                </td>
                                <td colspan="4" class="px-4 py-3.5 text-center text-xs font-semibold text-emerald-100" style="border-right:1px solid rgba(255,255,255,0.2);">
                                    Baru: <strong><?php echo e($summaryStats['hafalan_baru']); ?></strong> • Ulang: <strong><?php echo e($summaryStats['ulang']); ?></strong>
                                </td>
                                <td class="px-4 py-3.5 text-center text-xs font-bold text-white" style="border-right:1px solid rgba(255,255,255,0.2);">
                                    <?php echo e($summaryStats['persen_lanjut']); ?>% Lanjut
                                </td>
                                <td class="px-4 py-3.5 text-center" style="border-right:1px solid rgba(255,255,255,0.2);">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isLulus): ?>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold shadow-sm bg-white text-emerald-800">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Lulus
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold shadow-sm bg-amber-400 text-amber-950">
                                            Belum
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-xs text-emerald-100 font-medium">
                                    <?php echo e($summaryStats['surah_dicapai']); ?> Surah (<?php echo e($summaryStats['total_sesi']); ?> Sesi)
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
                    <div class="flex items-center gap-4 text-gray-600 flex-wrap">
                        <span>Total Sesi: <strong class="text-gray-900"><?php echo e($summaryStats['total_sesi']); ?> Hari</strong></span>
                        <span>•</span>
                        <span>Total Ayat: <strong class="text-emerald-700 font-bold"><?php echo e(number_format($summaryStats['total_ayat'])); ?> Ayat</strong></span>
                        <span>•</span>
                        <span>Capaian: <strong class="text-gray-900"><?php echo e(number_format($summaryStats['ayat_tercakup'])); ?> / <?php echo e(number_format($summaryStats['target_ayat'])); ?> Ayat (<?php echo e($summaryStats['persentase_target']); ?>%)</strong></span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="font-bold text-gray-700">Keputusan :</span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isLulus): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-extrabold bg-emerald-600 text-white shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                LULUS
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-extrabold bg-amber-500 text-white shadow-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                BELUM
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/admin/setoran/riwayat-hafalan-siswa.blade.php ENDPATH**/ ?>