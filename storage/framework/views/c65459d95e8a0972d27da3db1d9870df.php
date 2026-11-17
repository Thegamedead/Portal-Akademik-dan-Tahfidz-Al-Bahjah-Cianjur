<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'setoran',
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
    'setoran',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $isObject = is_object($setoran);
    
    $keputusan = $isObject ? $setoran->keputusan : ($setoran['keputusan'] ?? null);
    $isLanjut = $keputusan instanceof \App\Enums\Keputusan
        ? $keputusan->value === 'Lanjut'
        : $keputusan === 'Lanjut';
        
    $badgeConfig = $isLanjut
        ? ['bg' => 'bg-green-100', 'text' => 'text-green-700']
        : ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'];
        
    $surahNama = $isObject 
        ? ($setoran->surah->nama_latin ?? 'Surah ' . $setoran->surah_id)
        : (($setoran['surah']['nama_latin'] ?? null) ?? 'Surah ' . ($setoran['surah_id'] ?? '-'));
        
    $ayatMulai = $isObject ? $setoran->ayat_mulai : ($setoran['ayat_mulai'] ?? '-');
    $ayatSelesai = $isObject ? $setoran->ayat_selesai : ($setoran['ayat_selesai'] ?? '-');
    
    $jenisSetoran = $isObject ? $setoran->jenis_setoran : ($setoran['jenis_setoran'] ?? null);
    $jenisSetoranLabel = $jenisSetoran instanceof \App\Enums\JenisSetoran 
        ? $jenisSetoran->label() 
        : $jenisSetoran;
        
    $predikat = $isObject ? $setoran->predikat : ($setoran['predikat'] ?? null);
    $predikatValue = $predikat instanceof \App\Enums\SkorKomponen 
        ? $predikat->value 
        : $predikat;
        
    $tanggal = $isObject 
        ? $setoran->absensi->tanggal->isoFormat('D MMM')
        : (\Carbon\Carbon::parse($setoran['absensi']['tanggal'] ?? ($setoran['created_at'] ?? now()))->isoFormat('D MMM'));
?>

<div <?php echo e($attributes->merge(['class' => 'flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition'])); ?>>
    
    <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center <?php echo e($badgeConfig['bg']); ?> <?php echo e($badgeConfig['text']); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isLanjut): ?>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
        <?php else: ?>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
            </svg>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="flex-1 min-w-0">
        <p class="text-sm font-medium text-gray-900">
            <?php echo e($surahNama); ?>

        </p>
        <p class="text-xs text-gray-600 mt-0.5">
            Ayat <?php echo e($ayatMulai); ?>-<?php echo e($ayatSelesai); ?> |
            <?php echo e($jenisSetoranLabel); ?>

        </p>
        <div class="flex items-center gap-2 mt-1 flex-wrap">
            <?php if (isset($component)) { $__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.status-badge','data' => ['size' => 'sm','status' => $predikatValue ?? '-','customColors' => ($predikatValue === 'Lancar')
                    ? ['bg' => '#D1FAE5', 'text' => '#065F46']
                    : ['bg' => '#FEF3C7', 'text' => '#92400E']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($predikatValue ?? '-'),'custom-colors' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($predikatValue === 'Lancar')
                    ? ['bg' => '#D1FAE5', 'text' => '#065F46']
                    : ['bg' => '#FEF3C7', 'text' => '#92400E'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8)): ?>
<?php $attributes = $__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8; ?>
<?php unset($__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8)): ?>
<?php $component = $__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8; ?>
<?php unset($__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8); ?>
<?php endif; ?>
            <span class="text-xs text-gray-500">
                <?php echo e($tanggal); ?>

            </span>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views\components\siswa\setoran-card.blade.php ENDPATH**/ ?>