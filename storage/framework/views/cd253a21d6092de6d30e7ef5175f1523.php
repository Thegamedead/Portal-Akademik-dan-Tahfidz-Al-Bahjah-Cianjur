
<div class="space-y-6">

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Data Kelas Akademik
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Kelola data kelas akademik dan wali kelas
        </p>
    </div>

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

            
            <div class="flex flex-wrap items-center gap-3">
                
                <button
                    wire:click="openModal"
                    type="button"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2"
                    style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah
                </button>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedIds) === 1): ?>
                    <button
                        wire:click="openModal(<?php echo e($selectedIds[0]); ?>)"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                        style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                <?php else: ?>
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#FFFFFF;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih tepat 1 kelas untuk edit"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedIds) > 0): ?>
                    <button
                        wire:click="openBulkDeleteConfirm"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                        style="background-color:#9B3A3A;color:#FFFFFF;border:1.5px solid #9B3A3A;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus (<?php echo e(count($selectedIds)); ?>)
                    </button>
                <?php else: ?>
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih minimal 1 kelas untuk hapus"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedIds) === 1): ?>
                    <button
                        wire:click="bukaAnggotaModal(<?php echo e($selectedIds[0]); ?>)"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                        style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Anggota Kelas
                    </button>
                <?php else: ?>
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#FFFFFF;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih tepat 1 kelas untuk lihat anggota"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Anggota Kelas
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <button
                    wire:click="bukaModalLuluskanKelas9"
                    type="button"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                    style="background-color:#4F46E5;color:#FFFFFF;border:1.5px solid #4F46E5;"
                    title="Luluskan semua siswa aktif yang berada di kelas 9"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v7"></path>
                    </svg>
                    Luluskan Kelas 9
                </button>
            </div>

            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-[400px]">
                
                <div class="flex-1">
                    <?php if (isset($component)) { $__componentOriginal6962575527da6e31b80032f98e723ee8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6962575527da6e31b80032f98e723ee8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-field','data' => ['wireModel' => 'search','placeholder' => 'Cari nama kelas atau wali kelas...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire-model' => 'search','placeholder' => 'Cari nama kelas atau wali kelas...']); ?>
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

                
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        type="button"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none whitespace-nowrap cursor-pointer w-full sm:w-auto"
                        style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterTingkat): ?>
                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                1
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open"
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute z-50 right-0 mt-2 w-64 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                         style="display: none; border: 1px solid #E5E7EB;">

                        <div class="p-4 space-y-3">
                            
                            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                                <h3 class="text-sm font-semibold" style="color:white;font-family:'Plus Jakarta Sans',sans-serif;">
                                    Filter Kelas
                                </h3>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterTingkat): ?>
                                    <button
                                        wire:click="$set('filterTingkat', '')"
                                        type="button"
                                        class="text-xs font-medium text-gray-500 hover:text-[#1B5E4F] transition-colors"
                                    >
                                        Reset
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Tingkat
                                </label>
                                <select
                                    wire:model.live="filterTingkat"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    <option value="7">Tingkat 7</option>
                                    <option value="8">Tingkat 8</option>
                                    <option value="9">Tingkat 9</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    
    
    

    <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="background-color:#1B5E4F;" class="text-white">
                        
                        <th class="px-6 py-4 text-center" style="width:60px;">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                class="w-4 h-4 rounded transition-colors cursor-pointer"
                                style="color:#1B5E4F;border-color:#D1D5DB;"
                            >
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold">Tingkat</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold">Nama Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold">Ruang Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold">Jumlah Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold">Wali Kelas</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $kelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            
                            <td class="px-6 py-4 text-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedIds"
                                    value="<?php echo e($k->id); ?>"
                                    class="w-4 h-4 rounded transition-colors cursor-pointer"
                                    style="color:#1B5E4F;border-color:#D1D5DB;"
                                >
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold" style="background-color:#E8F5F1;color:#1B5E4F;">
                                    <?php echo e($k->tingkat); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold" style="color:#1A2422;">
                                <?php echo e($k->nama_kelas); ?>

                            </td>
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php echo e($k->ruang_kelas ?? '-'); ?>

                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold" style="background-color:#E8F5F1;color:#1B5E4F;border:1px solid #C4E5DC;" title="Siswa Aktif">
                                        <?php echo e($k->siswas_aktif_count ?? 0); ?> Aktif
                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($k->siswas_non_aktif_count ?? 0) > 0): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium" style="background-color:#F3F4F6;color:#6B7280;border:1px solid #E5E7EB;" title="Siswa Lulus / Non-Aktif">
                                            <?php echo e($k->siswas_non_aktif_count); ?> Non-Aktif
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php echo e($k->nama_wali_kelas); ?>

                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || $filterTingkat): ?>
                                            Tidak ada kelas yang sesuai dengan filter
                                        <?php else: ?>
                                            Belum ada data kelas
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelas->hasPages()): ?>
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                <?php echo e($kelas->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showBulkDeleteConfirm): ?>
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif; pointer-events: none;">
            <div class="flex min-h-full items-center justify-center p-4" style="pointer-events: auto;">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6" style="border:1px solid #E5E7EB;">
                    
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full" style="background-color:#FEE2E2;">
                        <svg class="w-6 h-6" style="color:#9B3A3A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>

                    
                    <h3 class="text-lg font-semibold text-center mb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                        Konfirmasi Hapus Massal
                    </h3>

                    
                    <div class="text-sm mb-6" style="color:#6B7280;">
                        <p class="text-center mb-3">
                            Apakah Anda yakin ingin menghapus <strong style="color:#9B3A3A;"><?php echo e(count($selectedIds)); ?> kelas</strong> yang dipilih?
                        </p>

                        <div class="mt-3 p-3 rounded-lg" style="background-color:#FEF3C7;border-left:3px solid #C9A227;">
                            <p class="font-medium mb-2" style="color:#1A2422;font-size:0.8125rem;">
                                Peringatan:
                            </p>
                            <ul class="space-y-1 ml-4 text-xs" style="color:#4B5563;list-style-type:disc;">
                                <li>Semua data siswa di kelas ini akan kehilangan referensi kelas</li>
                                <li>Data tidak dapat dikembalikan setelah dihapus</li>
                            </ul>
                        </div>
                    </div>

                    
                    <div class="flex gap-3">
                        <button
                            wire:click="closeBulkDeleteConfirm"
                            type="button"
                            class="btn-modal-cancel flex-1"
                        >
                            Batal
                        </button>
                        <button
                            wire:click="bulkDelete"
                            type="button"
                            class="btn-modal-confirm flex-1"
                        >
                            Hapus Semua
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAnggotaModal && $kelasAnggota): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none" role="dialog" aria-modal="true" style="pointer-events: none;">
            <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-xl flex flex-col" style="font-family:'Inter',sans-serif; max-height:85vh; pointer-events: auto;">

                
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                    <div>
                        <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Anggota Kelas
                        </h3>
                        <p class="text-sm mt-0.5" style="color:#A7D8CC;">
                            Kelas <?php echo e($kelasAnggota->nama_kelas); ?> &bull; Tingkat <?php echo e($kelasAnggota->tingkat); ?>

                        </p>
                    </div>
                    <button wire:click="tutupAnggotaModal" class="text-white hover:text-green-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                
                <div class="flex items-center gap-6 px-6 py-3 flex-shrink-0" style="background-color:#F0FDF9; border-bottom:1px solid #D1FAE5;">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" style="color:#1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-xs font-medium" style="color:#065F46;">Wali Kelas: <?php echo e($kelasAnggota->nama_wali_kelas ?? '-'); ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" style="color:#1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-xs font-medium" style="color:#065F46;">Jumlah Siswa: <strong><?php echo e($kelasAnggota->siswas->count()); ?> siswa</strong></span>
                    </div>
                </div>

                
                <div class="px-6 pt-4 pb-2 flex-shrink-0">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9CA3AF;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"></path>
                        </svg>
                        <input
                            type="text"
                            wire:model.live="searchAnggota"
                            placeholder="Cari nama atau NIS siswa..."
                            class="w-full pl-9 pr-4 py-2 text-sm rounded-lg focus:outline-none focus:ring-2"
                            style="border:1px solid #D1D5DB;color:#1A2422;"
                        >
                    </div>
                </div>

                
                <div class="overflow-y-auto flex-1 px-6 pb-6">
                    <?php
                        $anggotaList = $kelasAnggota->siswas;
                        if ($searchAnggota) {
                            $s = strtolower($searchAnggota);
                            $anggotaList = $anggotaList->filter(fn($sw) =>
                                str_contains(strtolower($sw->nama), $s) ||
                                str_contains(strtolower($sw->nis ?? ''), $s)
                            );
                        }
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($anggotaList->isEmpty()): ?>
                        <div class="flex flex-col items-center justify-center py-12">
                            <svg class="w-14 h-14 mb-3" style="color:#D1D5DB;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <p class="text-sm font-medium" style="color:#9CA3AF;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($searchAnggota): ?> Tidak ada siswa yang cocok <?php else: ?> Belum ada siswa di kelas ini <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <table class="min-w-full mt-2">
                            <thead>
                                <tr style="border-bottom:2px solid #E5E7EB;">
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">No</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">Nama Siswa</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">NIS</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">JK</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="border-color:#F3F4F6;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $anggotaList->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $siswa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-3 pr-4 text-sm" style="color:#9CA3AF;"><?php echo e($i + 1); ?></td>
                                        <td class="py-3 pr-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0" style="background-color:#E8F5F1;color:#1B5E4F;">
                                                    <?php echo e(substr($siswa->nama, 0, 1)); ?>

                                                </div>
                                                <span class="text-sm font-medium" style="color:#1A2422;"><?php echo e($siswa->nama); ?></span>
                                            </div>
                                        </td>
                                        <td class="py-3 pr-4 text-sm" style="color:#6B7280;"><?php echo e($siswa->nis ?? '-'); ?></td>
                                        <td class="py-3 pr-4 text-sm" style="color:#6B7280;"><?php echo e($siswa->jenis_kelamin === 'Laki-laki' ? 'L' : ($siswa->jenis_kelamin === 'Perempuan' ? 'P' : '-')); ?></td>
                                        <td class="py-3 pr-4 text-xs">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa->status_aktif): ?>
                                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                                    Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-700">
                                                    <?php echo e($siswa->status_akademik ?? 'Non-Aktif'); ?>

                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                    </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tbody>
                        </table>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="flex justify-end px-6 py-4 flex-shrink-0" style="border-top:1px solid #E5E7EB;background-color:#F9FAFB;border-radius:0 0 1rem 1rem;">
                    <button wire:click="tutupAnggotaModal" type="button" class="btn-modal-cancel">Tutup</button>
                </div>

            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showModalLuluskanKelas9): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none" role="dialog" aria-modal="true" style="pointer-events: none;">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 pointer-events-auto" style="font-family:'Inter',sans-serif; border:1px solid #E5E7EB; pointer-events: auto; max-height: 90vh; overflow-y: auto;">
                
                <div class="flex items-center gap-3 pb-4 mb-4" style="border-bottom: 1px solid #E5E7EB;">
                    <div class="flex items-center justify-center w-11 h-11 rounded-xl flex-shrink-0" style="background-color:#EEF2FF;">
                        <svg class="w-6 h-6" style="color:#4F46E5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0v7"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                            Kelulusan Siswa Kelas 9 (Lulus SMP)
                        </h3>
                        <p class="text-xs text-gray-500">
                            Proses kelulusan akhir jenjang untuk seluruh siswa aktif kelas 9
                        </p>
                    </div>
                </div>

                
                <div class="space-y-4 text-sm mb-6">
                    
                    <div class="p-3.5 rounded-xl text-xs space-y-1.5" style="background-color:#F8FAFC;border:1px solid #E2E8F0;">
                        <div class="flex items-center justify-between font-semibold text-gray-800">
                            <span>Siswa Kelas 9 Ditemukan:</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold" style="background-color:#EEF2FF;color:#4F46E5;">
                                <?php echo e($totalSiswaKelas9); ?> Siswa Aktif
                            </span>
                        </div>
                        <p class="text-gray-500">
                            Seluruh siswa yang terdaftar di kelas tingkat 9 akan diubah status akademiknya menjadi <strong>Lulus SMP</strong> dan dipindahkan ke daftar <strong>Siswa Non-Aktif</strong>.
                        </p>
                    </div>

                    
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color:#1A2422;">
                            Tanggal Kelulusan <span style="color:#DC2626;">*</span>
                        </label>
                        <input
                            type="date"
                            wire:model="tanggalLulus"
                            class="w-full px-3.5 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all block"
                            style="border:1.5px solid #D1D5DB !important;color:#1A2422;background-color:#FFFFFF;"
                        >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tanggalLulus'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-xs text-red-600 mt-1 block"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color:#1A2422;">
                            Keterangan / Catatan Keluar
                        </label>
                        <input
                            type="text"
                            wire:model="keteranganLulus"
                            placeholder="Contoh: Lulusan Kelas 9 (Lulus SMP) Tahun Ajaran 2025/2026"
                            class="w-full px-3.5 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all block"
                            style="border:1.5px solid #D1D5DB !important;color:#1A2422;background-color:#FFFFFF;"
                        >
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($previewSiswaKelas9)): ?>
                        <div>
                            <label class="block text-xs font-semibold mb-1 text-gray-700">
                                Daftar Siswa yang Akan Diluluskan:
                            </label>
                            <div class="max-h-40 overflow-y-auto rounded-lg divide-y divide-gray-100" style="border: 1px solid #E5E7EB;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $previewSiswaKelas9; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <div class="px-3 py-2 flex items-center justify-between text-xs hover:bg-gray-50">
                                        <div>
                                            <span class="font-semibold text-gray-800"><?php echo e($item['nama']); ?></span>
                                            <span class="text-gray-400 ml-1">(<?php echo e($item['nis']); ?>)</span>
                                            <span class="ml-2 px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-700">
                                                Kelas <?php echo e($item['kelas']); ?>

                                            </span>
                                        </div>
                                        <div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item['status_tahfidz'] === 'Lulus Program Tahfidz'): ?>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                                    🎓 Tahfidz Lulus
                                                </span>
                                            <?php else: ?>
                                                <span class="text-[10px] text-gray-500">
                                                    <?php echo e($item['total_juz']); ?> Juz
                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="flex gap-3 pt-2" style="border-top:1px solid #F3F4F6;">
                    <button
                        wire:click="tutupModalLuluskanKelas9"
                        type="button"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none flex-1"
                        style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                    >
                        Batal
                    </button>
                    <button
                        wire:click="prosesLuluskanKelas9"
                        type="button"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none flex-1 <?php echo e($totalSiswaKelas9 === 0 ? 'opacity-50 cursor-not-allowed' : ''); ?>"
                        style="background-color:#4F46E5;border:1.5px solid #4F46E5;"
                        <?php if($totalSiswaKelas9 === 0): ?> disabled <?php endif; ?>
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="prosesLuluskanKelas9">
                            Luluskan
                        </span>
                        <span wire:loading wire:target="prosesLuluskanKelas9">
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Modal CRUD -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showModal): ?>
        <?php echo $__env->make('livewire.admin.master-data.kelas.partials.modal-kelas', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/admin/master-data/kelas/index-kelas.blade.php ENDPATH**/ ?>