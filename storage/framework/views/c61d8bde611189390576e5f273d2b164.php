<?php
    $config = \App\Models\ParameterKonfigurasi::getInstance();
    $batas = $config->batas_tingkat ?? [];

    $dasar    = is_array($batas['Dasar'] ?? null) ? $batas['Dasar'] : [30];
    $menengah = is_array($batas['Menengah'] ?? null) ? $batas['Menengah'] : [29, 28];
    $atas     = is_array($batas['Atas'] ?? null) ? $batas['Atas'] : [27, 26];

    $countDasar    = count($dasar);
    $countMenengah = count($menengah);
    $countAtas     = count($atas);
    $totalLulus    = $config->target_kelulusan ?? 5;

    // Hitung jumlah ayat per kelompok dari database
    $ayatDasar    = \App\Models\ReferensiQuran::whereIn('juz_ke', $dasar)->sum('jumlah_ayat');
    $ayatMenengah = \App\Models\ReferensiQuran::whereIn('juz_ke', $menengah)->sum('jumlah_ayat');
    $ayatAtas     = \App\Models\ReferensiQuran::whereIn('juz_ke', $atas)->sum('jumlah_ayat');
    $ayatTotal    = $ayatDasar + $ayatMenengah + $ayatAtas;

    $formatJuz = function($juzs) {
        if (empty($juzs)) return '-';
        sort($juzs);
        if (count($juzs) === 1) return 'Juz ' . $juzs[0];
        if (count($juzs) <= 3)  return 'Juz ' . implode(' & ', $juzs);
        return 'Juz ' . $juzs[0] . ' – ' . end($juzs);
    };

    $stages = [
        [
            'label'  => 'Tahap 1: Dasar',
            'count'  => $countDasar,
            'juzs'   => $dasar,
            'ayat'   => $ayatDasar,
            'icon'   => '1',
            'bg'     => '#E8F5F1',
            'border' => '#1B5E4F',
            'color'  => '#1B5E4F',
            'circle' => '#1B5E4F',
        ],
        [
            'label'  => 'Tahap 2: Menengah',
            'count'  => $countMenengah,
            'juzs'   => $menengah,
            'ayat'   => $ayatMenengah,
            'icon'   => '2',
            'bg'     => '#E6F3F5',
            'border' => '#2E7D8C',
            'color'  => '#2E7D8C',
            'circle' => '#2E7D8C',
        ],
        [
            'label'  => 'Tahap 3: Atas',
            'count'  => $countAtas,
            'juzs'   => $atas,
            'ayat'   => $ayatAtas,
            'icon'   => '3',
            'bg'     => '#FEF9E7',
            'border' => '#C9A227',
            'color'  => '#C9A227',
            'circle' => '#C9A227',
        ],
        [
            'label'  => 'Syarat Kelulusan',
            'count'  => $totalLulus,
            'juzs'   => [],
            'ayat'   => 0, // Cannot be exactly calculated if flexible
            'icon'   => 'check',
            'bg'     => '#F3F4F6',
            'border' => '#1A2422',
            'color'  => '#1A2422',
            'circle' => '#1A2422',
        ],
    ];
?>

<div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100 mb-6">
    <h3 class="font-semibold text-base mb-4 text-gray-800 flex items-center gap-2">
        <svg class="w-4 h-4" style="color:#1B5E4F" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
        </svg>
        Peta Perjalanan (Roadmap) Tahfidz
    </h3>

    
    <div class="w-full overflow-x-auto">
        <div class="min-w-[900px]">
            
            <div class="flex justify-between items-stretch gap-3 relative">
                
                <div class="absolute left-0 right-0 h-0.5 bg-gray-300 top-6 z-0"></div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="flex-1 flex flex-col items-center relative z-10">
                    
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md transition-all duration-300 hover:scale-105 mb-3 bg-white"
                         style="background-color:<?php echo e($s['circle']); ?>;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s['icon'] === 'check'): ?>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <?php else: ?>
                        <?php echo e($s['icon']); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="w-full h-full rounded-lg p-3 shadow-sm transition-all duration-300 hover:shadow-md flex flex-col justify-between"
                         style="background-color:<?php echo e($s['bg']); ?>; border: 1.5px solid <?php echo e($s['border']); ?>;">
                        
                        <p class="text-[9px] font-bold uppercase tracking-wide mb-1.5 text-center"
                           style="color:<?php echo e($s['color']); ?>;">
                            <?php echo e($s['label']); ?>

                        </p>

                        
                        <h4 class="text-lg font-bold text-gray-900 mb-0.5 text-center"><?php echo e($s['count']); ?> Juz</h4>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($s['juzs'])): ?>
                        <p class="text-[10px] text-gray-600 mb-1.5 text-center"><?php echo e($formatJuz($s['juzs'])); ?></p>
                        <?php else: ?>
                        <p class="text-[10px] text-gray-600 mb-1.5 text-center">Total</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s['ayat'] > 0): ?>
                        <div class="pt-1.5 mt-auto border-t" style="border-color:<?php echo e($s['border']); ?>40;">
                            <p class="text-[10px] font-semibold text-center" style="color:<?php echo e($s['color']); ?>;">
                                ± <?php echo e(number_format($s['ayat'])); ?> Ayat
                            </p>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="mt-4 pt-3 border-t border-gray-200">
        <p class="text-[10px] text-gray-500 text-center flex items-center justify-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Target hafalan disesuaikan dengan konfigurasi parameter tingkat kelompok
        </p>
    </div>
</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views\components\roadmap-tahfidz.blade.php ENDPATH**/ ?>