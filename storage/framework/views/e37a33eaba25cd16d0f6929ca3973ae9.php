<div class="space-y-6" style="font-family:'Inter',sans-serif;">

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Hasil Tes Penempatan
                </h1>
                <p class="mt-2 text-sm" style="color:#6B7280;">
                    Hasil tes siswa: <strong><?php echo e($siswa->nama); ?></strong> (<?php echo e($siswa->nis); ?>)
                </p>
            </div>
            <button
                wire:click="kembali"
                type="button"
                class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
            >
                Kembali
            </button>
        </div>
    </div>

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;">
        <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
            Informasi Tes
        </h3>

        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                Tanggal Tes
            </label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm" style="color:#1A2422;"><?php echo e($tes->tanggal_tes->tgl()); ?></p>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                Waktu Tes
            </label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm" style="color:#1A2422;">
                    <?php echo e($tes->waktu_tes ? $tes->waktu_tes->format('H:i') : '-'); ?>

                </p>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                Status
            </label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tes->status->value === 'Selesai'): ?>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color:#FEF3C7;color:#92400E;">
                        Selesai
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                Diselesaikan
            </label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm" style="color:#1A2422;">
                    <?php echo e($tes->submitted_at ? $tes->submitted_at->format('d M Y H:i') : '-'); ?>

                </p>
            </div>
        </div>
    </div>

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6 space-y-4" style="border:1px solid #E5E7EB;">
        <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
            Hasil Hafalan
        </h3>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="background-color:#F9FAFB;">
                        <th class="px-4 py-3 text-left text-xs font-semibold" style="color:#6B7280;">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold" style="color:#6B7280;">Juz</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold" style="color:#6B7280;">Surah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold" style="color:#6B7280;">Ayat</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $hasilTes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $hasil): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr>
                            <td class="px-4 py-3 text-sm" style="color:#6B7280;"><?php echo e($index + 1); ?></td>
                            <td class="px-4 py-3 text-sm" style="color:#1A2422;">
                                Juz <?php echo e($hasil->juz->juz_ke); ?>

                            </td>
                            <td class="px-4 py-3 text-sm" style="color:#1A2422;">
                                <?php echo e($hasil->surah->nomor_surah); ?>. <?php echo e($hasil->surah->nama_latin); ?>

                            </td>
                            <td class="px-4 py-3 text-sm" style="color:#1A2422;">
                                Ayat <?php echo e($hasil->ayat_mulai); ?> - <?php echo e($hasil->ayat_selesai); ?>

                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;">
        <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
            Catatan Kualitas Hafalan
        </h3>

        <div class="p-4 rounded-lg" style="background-color:#F9FAFB;">
            <p class="text-sm whitespace-pre-line" style="color:#1A2422;"><?php echo e($tes->catatan_kualitas); ?></p>
        </div>
    </div>

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;">
        <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
            Rekomendasi & Penempatan
        </h3>

        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                Rekomendasi Tingkat
            </label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tes->rekomendasi_tingkat): ?>
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold"
                          style="background-color:<?php echo e($tes->rekomendasi_tingkat->value === 'Dasar' ? '#E8F5F1' : ($tes->rekomendasi_tingkat->value === 'Menengah' ? '#E0F2F7' : '#FEF3C7')); ?>;
                                 color:<?php echo e($tes->rekomendasi_tingkat->value === 'Dasar' ? '#1B5E4F' : ($tes->rekomendasi_tingkat->value === 'Menengah' ? '#2E7D8C' : '#C9A227')); ?>;">
                        <?php echo e($tes->rekomendasi_tingkat->value); ?>

                    </span>
                <?php else: ?>
                    <p class="text-sm" style="color:#9CA3AF;">-</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tes->finalKelompok): ?>
            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                    Kelompok Penempatan
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <p class="text-sm font-semibold" style="color:#1B5E4F;"><?php echo e($tes->finalKelompok->nama); ?></p>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                    Ditempatkan Pada
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <p class="text-sm" style="color:#1A2422;">
                        <?php echo e($tes->ditempatkan_at ? $tes->ditempatkan_at->format('d M Y H:i') : '-'); ?>

                    </p>
                </div>
            </div>
        <?php elseif($tes->ditempatkan_at): ?>
            <div class="p-4 rounded-lg" style="background-color:#FEF3C7;border:1px solid #F59E0B;">
                <p class="text-sm" style="color:#92400E;">
                    <strong>Catatan:</strong> Siswa sudah diproses oleh admin, namun belum ditempatkan di kelompok tertentu.
                </p>
            </div>
        <?php else: ?>
            <div class="p-4 rounded-lg" style="background-color:#EFF6FF;border:1px solid #3B82F6;">
                <p class="text-sm" style="color:#1E40AF;">
                    <strong>Status:</strong> Menunggu admin untuk menempatkan siswa ke kelompok.
                </p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/guru/tes-penempatan/hasil-tes.blade.php ENDPATH**/ ?>