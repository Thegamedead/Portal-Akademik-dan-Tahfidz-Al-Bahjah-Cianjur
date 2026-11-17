
<div class="space-y-3 text-sm">
    
    <div class="flex items-center gap-3 py-2 border-b" style="border-color:#F3F4F6;">
        <span class="w-20 text-xs font-medium shrink-0" style="color:#6B7280;">Nama</span>
        <span class="font-semibold" style="color:#1A2422;"><?php echo e($siswa->nama); ?></span>
    </div>
    
    <div class="flex items-center gap-3 py-2 border-b" style="border-color:#F3F4F6;">
        <span class="w-20 text-xs font-medium shrink-0" style="color:#6B7280;">NIS</span>
        <span style="color:#374151;"><?php echo e($siswa->nis ?? '-'); ?></span>
    </div>
    
    <div class="flex items-center gap-3 py-2 border-b" style="border-color:#F3F4F6;">
        <span class="w-20 text-xs font-medium shrink-0" style="color:#6B7280;">Kelas</span>
        <span style="color:#374151;"><?php echo e($siswa->kelas ?? '-'); ?></span>
    </div>
    
    <div class="flex items-center gap-3 py-2 border-b" style="border-color:#F3F4F6;">
        <span class="w-20 text-xs font-medium shrink-0" style="color:#6B7280;">Kelompok</span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa->kelompok): ?>
            <div>
                <span class="font-medium" style="color:#1B5E4F;"><?php echo e($siswa->kelompok->nama); ?></span>
                <span class="text-xs ml-1" style="color:#6B7280;">(<?php echo e($siswa->kelompok->tingkat ?? '-'); ?>)</span>
            </div>
        <?php else: ?>
            <span class="text-xs italic" style="color:#9CA3AF;">Belum ditempatkan</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    
    <div class="flex items-center gap-3 py-2">
        <span class="w-20 text-xs font-medium shrink-0" style="color:#6B7280;">Pembimbing</span>
        <span style="color:#374151;"><?php echo e($siswa->kelompok?->guruPembimbing?->nama ?? '-'); ?></span>
    </div>
</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/siswa/profil-form.blade.php ENDPATH**/ ?>