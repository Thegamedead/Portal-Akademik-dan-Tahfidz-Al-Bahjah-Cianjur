

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['siswa']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['siswa']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa->jenis_keluar): ?>
<div class="rounded-2xl bg-white shadow-sm p-6 space-y-4" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
    <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1B5E4F;">
        Informasi Keluar Siswa
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <div>
            <p class="text-xs font-medium mb-1" style="color:#6B7280;">Jenis Keluar</p>
            <p class="text-sm" style="color:#1A2422;">
                <?php
                $jenisLabel = \App\Enums\JenisKeluar::options()[$siswa->jenis_keluar] ?? $siswa->jenis_keluar;
                ?>
                <?php echo e($jenisLabel); ?>

            </p>
        </div>

        
        <div>
            <p class="text-xs font-medium mb-1" style="color:#6B7280;">Tanggal Keluar</p>
            <p class="text-sm" style="color:#1A2422;">
                <?php echo e($siswa->tanggal_keluar?->tgl() ?? '-'); ?>

            </p>
        </div>

        
        <div>
            <p class="text-xs font-medium mb-1" style="color:#6B7280;">Status Akademik</p>
            <div class="mt-1">
                <?php
                $badgeConfig = match($siswa->status_akademik) {
                    'Aktif' => ['text' => 'Aktif', 'bg' => '#1B5E4F', 'color' => '#FFFFFF'],
                    'Lulus SMP' => ['text' => 'Lulus SMP', 'bg' => '#1B5E4F', 'color' => '#FFFFFF'],
                    'Pindah Sekolah' => ['text' => 'Pindah Sekolah', 'bg' => '#5B7A8C', 'color' => '#FFFFFF'],
                    'Keluar' => ['text' => 'Keluar', 'bg' => '#9B3A3A', 'color' => '#FFFFFF'],
                    default => ['text' => $siswa->status_akademik, 'bg' => '#F4F5F4', 'color' => '#1A2422'],
                };
                ?>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                    style="background-color:<?php echo e($badgeConfig['bg']); ?>;color:<?php echo e($badgeConfig['color']); ?>;"
                >
                    <?php echo e($badgeConfig['text']); ?>

                </span>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa->jenis_keluar === 'Pindah Sekolah' && $siswa->tujuan_sekolah): ?>
        <div>
            <p class="text-xs font-medium mb-1" style="color:#6B7280;">Tujuan Sekolah</p>
            <p class="text-sm" style="color:#1A2422;"><?php echo e($siswa->tujuan_sekolah); ?></p>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa->keterangan_keluar): ?>
        <div class="md:col-span-2">
            <p class="text-xs font-medium mb-1" style="color:#6B7280;">Keterangan</p>
            <p class="text-sm leading-relaxed" style="color:#1A2422;"><?php echo e($siswa->keterangan_keluar); ?></p>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="md:col-span-2">
            <p class="text-xs font-medium mb-1" style="color:#6B7280;">Diproses Oleh</p>
            <p class="text-sm" style="color:#1A2422;">
                <?php echo e($siswa->diprosesOleh?->nama ?? 'Admin'); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa->updated_at): ?>
                    <span style="color:#6B7280;">
                        • <?php echo e($siswa->updated_at->tglJam()); ?>

                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>
        </div>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views\components\siswa\informasi-keluar.blade.php ENDPATH**/ ?>