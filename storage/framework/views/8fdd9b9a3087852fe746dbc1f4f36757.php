
<div>

    
    <div class="mb-6 rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex items-center gap-4">
            <div
                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full text-white text-xl font-bold"
                style="background-color:#1B5E4F;font-family:'Plus Jakarta Sans',sans-serif;"
                aria-hidden="true"
            >
                <?php echo e(strtoupper(substr($siswa->nama ?? 'S', 0, 1))); ?>

            </div>
            <div>
                <p class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    <?php echo e($siswa->nama ?? '—'); ?>

                </p>
                <p class="text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
                    Siswa &mdash; NIS: <?php echo e($siswa->nis ?? '—'); ?>

                </p>
            </div>
        </div>
    </div>

    
    <div class="mb-6 rounded-2xl bg-white shadow-sm" style="border:1px solid #E5E7EB;">

        
        <div class="flex items-center gap-4 px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg shrink-0" style="background-color:#E8F5F1;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="#1B5E4F" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#FFFFFF;">Data Pribadi</p>
                <p class="text-xs" style="color:#D1D5DB;font-family:'Inter',sans-serif;">Informasi pribadi siswa (read-only)</p>
            </div>
        </div>

        
        <div class="px-6 py-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">NIS</p>
                    <p class="text-sm" style="color:#1A2422;font-family:'Inter',sans-serif;"><?php echo e($siswa->nis ?? '—'); ?></p>
                </div>

                
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">NISN</p>
                    <p class="text-sm" style="color:#1A2422;font-family:'Inter',sans-serif;"><?php echo e($siswa->nisn ?? '—'); ?></p>
                </div>

                
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Nama Lengkap</p>
                    <p class="text-sm" style="color:#1A2422;font-family:'Inter',sans-serif;"><?php echo e($siswa->nama ?? '—'); ?></p>
                </div>

                
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Jenis Kelamin</p>
                    <p class="text-sm" style="color:#1A2422;font-family:'Inter',sans-serif;"><?php echo e($siswa->jenis_kelamin === 'L' ? 'Laki-laki' : ($siswa->jenis_kelamin === 'P' ? 'Perempuan' : ($siswa->jenis_kelamin ?? '—'))); ?></p>
                </div>

                
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Tempat Lahir</p>
                    <p class="text-sm" style="color:#1A2422;font-family:'Inter',sans-serif;"><?php echo e($siswa->tempat_lahir ?? '—'); ?></p>
                </div>

                
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Tanggal Lahir</p>
                    <p class="text-sm" style="color:#1A2422;font-family:'Inter',sans-serif;">
                        <?php echo e($siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') : '—'); ?>

                    </p>
                </div>

                
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Status Akademik</p>
                    <p class="text-sm" style="color:#1A2422;font-family:'Inter',sans-serif;"><?php echo e($siswa->status_akademik ?? '—'); ?></p>
                </div>

                
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Kelas</p>
                    <p class="text-sm" style="color:#1A2422;font-family:'Inter',sans-serif;"><?php echo e($siswa->kelas->nama_kelas ?? '—'); ?></p>
                </div>

                
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Status Masuk</p>
                    <p class="text-sm" style="color:#1A2422;font-family:'Inter',sans-serif;"><?php echo e($siswa->status_masuk ?? '—'); ?></p>
                </div>

                
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Tanggal Masuk</p>
                    <p class="text-sm" style="color:#1A2422;font-family:'Inter',sans-serif;">
                        <?php echo e($siswa->tanggal_masuk ? $siswa->tanggal_masuk->tgl() : '—'); ?>

                    </p>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($siswa->asal_sekolah)): ?>
                    <div class="md:col-span-2">
                        <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Asal Sekolah</p>
                        <p class="text-sm" style="color:#1A2422;font-family:'Inter',sans-serif;"><?php echo e($siswa->asal_sekolah); ?></p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="mb-6 rounded-2xl bg-white shadow-sm" style="border:1px solid #E5E7EB;">

        
        <div class="flex items-center gap-4 px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg shrink-0" style="background-color:#E8F5F1;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="#1B5E4F" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#FFFFFF;">Data Orang Tua / Wali</p>
                <p class="text-xs" style="color:#D1D5DB;font-family:'Inter',sans-serif;">Informasi orang tua atau wali siswa (read-only)</p>
            </div>
        </div>

        
        <div class="px-6 py-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Nama Ayah</p>
                    <p class="text-sm" style="color:#1A2422;font-family:'Inter',sans-serif;"><?php echo e($siswa->nama_ayah ?? '—'); ?></p>
                </div>

                
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;font-family:'Inter',sans-serif;">Nama Ibu</p>
                    <p class="text-sm" style="color:#1A2422;font-family:'Inter',sans-serif;"><?php echo e($siswa->nama_ibu ?? '—'); ?></p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="mb-6 rounded-2xl bg-white shadow-sm" style="border:1px solid #E5E7EB;">

        
        <div class="flex items-center gap-4 px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg shrink-0" style="background-color:#E8F5F1;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="#1B5E4F" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#FFFFFF;">Kelompok Tahfidz</p>
                <p class="text-xs" style="color:#D1D5DB;font-family:'Inter',sans-serif;">Penempatan kelompok tahfidz saat ini</p>
            </div>
        </div>

        
        <div class="px-6 py-5">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa->kelompok): ?>
                
                <div class="mb-6 rounded-xl p-5" style="background-color:#E8F5F1;border:2px solid #1B5E4F;">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="flex-1">
                            
                            <div class="mb-3">
                                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold shadow-sm"
                                      style="background-color:#1B5E4F;color:#FFFFFF;font-family:'Plus Jakarta Sans',sans-serif;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Tingkat <?php echo e($siswa->kelompok->tingkat); ?>

                                </span>
                            </div>

                            
                            <h3 class="text-2xl font-bold mb-1" style="color:#1A2422;font-family:'Plus Jakarta Sans',sans-serif;">
                                <?php echo e($siswa->kelompok->nama); ?>

                            </h3>
                            <p class="text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
                                Kelompok <?php echo e($siswa->kelompok->jenis); ?> • Tahun Ajaran <?php echo e($siswa->kelompok->tahunAjaran->label ?? '—'); ?>

                            </p>
                        </div>

                        
                        <div class="flex h-12 w-12 items-center justify-center rounded-full shrink-0" style="background-color:#1B5E4F;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="#FFFFFF" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    
                    <div class="grid grid-cols-2 gap-4 pt-4" style="border-top:1px solid #1B5E4F;">
                        <div>
                            <p class="text-xs font-medium mb-1" style="color:#1B5E4F;font-family:'Inter',sans-serif;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 inline mr-1 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Guru Pembimbing
                            </p>
                            <p class="text-sm font-semibold" style="color:#1A2422;font-family:'Inter',sans-serif;">
                                <?php echo e($siswa->kelompok->guruPembimbing->nama ?? '—'); ?>

                            </p>
                        </div>
                    </div>
                </div>


            <?php else: ?>
                <div class="text-center py-8">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full" style="background-color:#FEF3C7;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="#C9A227" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium" style="color:#6B7280;font-family:'Inter',sans-serif;">Siswa belum ditempatkan dalam kelompok tahfidz</p>
                        <p class="text-xs" style="color:#9CA3AF;font-family:'Inter',sans-serif;">Penempatan kelompok akan dilakukan oleh Administrator</p>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>



</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/siswa/profil-siswa.blade.php ENDPATH**/ ?>