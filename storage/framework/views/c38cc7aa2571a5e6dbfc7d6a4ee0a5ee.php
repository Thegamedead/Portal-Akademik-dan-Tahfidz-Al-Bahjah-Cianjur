<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'absensi',
]));

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

foreach (array_filter(([
    'absensi',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $statusConfig = [
        'Hadir' => [
            'bg' => 'bg-green-100',
            'text' => 'text-green-700',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        ],
        'Izin' => [
            'bg' => 'bg-blue-100',
            'text' => 'text-blue-700',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        ],
        'Sakit' => [
            'bg' => 'bg-yellow-100',
            'text' => 'text-yellow-700',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
        ],
        'Alfa' => [
            'bg' => 'bg-red-100',
            'text' => 'text-red-700',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
        ],
    ];

    $config = $statusConfig[$absensi->status_kehadiran->value] ?? $statusConfig['Alfa'];
?>

<div <?php echo e($attributes->merge(['class' => 'border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition'])); ?>>
    <div class="flex items-center justify-between">
        
        <div class="flex items-center gap-4">
            <div class="text-center min-w-15">
                <p class="text-xs text-gray-500"><?php echo e($absensi->tanggal->isoFormat('ddd')); ?></p>
                <p class="text-2xl font-bold text-gray-900"><?php echo e($absensi->tanggal->format('d')); ?></p>
                <p class="text-xs text-gray-500"><?php echo e($absensi->tanggal->isoFormat('MMM')); ?></p>
            </div>

            
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900"><?php echo e($absensi->tanggal->isoFormat('dddd, D MMMM Y')); ?></p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($absensi->waktu_mulai && $absensi->waktu_selesai): ?>
                    <p class="text-xs text-gray-600 mt-1">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <?php echo e($absensi->waktu_mulai->format('H:i')); ?> - <?php echo e($absensi->waktu_selesai->format('H:i')); ?>

                    </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($absensi->guruPengisi): ?>
                    <p class="text-xs text-gray-500 mt-1">Pengisi: <?php echo e($absensi->guruPengisi->nama); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="text-right">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium <?php echo e($config['bg']); ?> <?php echo e($config['text']); ?>">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <?php echo $config['icon']; ?>

                </svg>
                <?php echo e($absensi->status_kehadiran->label()); ?>

            </span>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($absensi->setorans->count() > 0): ?>
                <p class="text-xs text-gray-500 mt-2">
                    <svg class="w-4 h-4 inline mr-1 text-zamrud-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                    <?php echo e($absensi->setorans->count()); ?> setoran
                </p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views\components\siswa\absensi-card.blade.php ENDPATH**/ ?>