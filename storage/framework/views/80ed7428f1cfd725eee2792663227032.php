
<div class="space-y-6">

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Data Guru
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Kelola data guru, guru pembimbing tahfidz, dan tugas tambahan
        </p>
    </div>

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

            
            <div class="flex flex-wrap items-center gap-3">
                
                <button
                    wire:click="openCreateModal"
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
                        wire:click="openEditModal(<?php echo e($selectedIds[0]); ?>)"
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
                        title="Pilih tepat 1 guru untuk edit"
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
                        Hapus
                    </button>
                <?php else: ?>
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih minimal 1 guru untuk hapus"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard('admin')->check()): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedIds) === 1): ?>
                        <button
                            type="button"
                            wire:click="bukaModalRegistrasiKeluar"
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                            style="background-color:#9B3A3A;color:#FFFFFF;border:1.5px solid #9B3A3A;"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Registrasi Keluar
                        </button>
                    <?php else: ?>
                        <button
                            disabled
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                            style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                            title="Pilih tepat 1 guru untuk registrasi keluar"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Registrasi Keluar
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="flex items-center sm:ml-2 sm:pl-4 sm:border-l border-gray-200">
                    <span class="text-sm text-gray-500">
                        Total <strong style="color:#1A2422;"><?php echo e($gurus->total()); ?></strong> guru
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedIds) > 0): ?>
                            <span class="mx-1.5">&bull;</span>
                            <strong style="color:#1B5E4F;"><?php echo e(count($selectedIds)); ?></strong> terpilih
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                </div>

            </div>

            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-100">
                
                <div class="flex-1">
                    <?php if (isset($component)) { $__componentOriginal6962575527da6e31b80032f98e723ee8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6962575527da6e31b80032f98e723ee8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-field','data' => ['wireModel' => 'search','placeholder' => 'Cari guru (NIY, Nama)...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wireModel' => 'search','placeholder' => 'Cari guru (NIY, Nama)...']); ?>
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
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterJabatan || $filterJenisKelamin): ?>
                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                <?php echo e(collect([$filterJabatan, $filterJenisKelamin])->filter()->count()); ?>

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
                            
                            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl shrink-0" style="background-color:#1B5E4F;">
                                <h3 class="text-sm font-semibold" style="color:white;font-family:'Plus Jakarta Sans',sans-serif;">
                                    Filter Guru
                                </h3>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterJabatan || $filterJenisKelamin): ?>
                                    <button
                                        wire:click="resetFilters"
                                        type="button"
                                        class="text-xs font-medium text-gray-500 hover:text-[#1B5E4F] transition-colors"
                                    >
                                        Reset
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Jabatan
                                </label>
                                <select
                                    wire:model.live="filterJabatan"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    <option value="Guru Pembimbing Tahfidz">Pembimbing Tahfidz</option>
                                    <option value="Guru Mapel">Guru Mapel</option>
                                    <option value="Kepala Sekolah">Kepala Sekolah</option>
                                    <option value="Tendik">Tendik</option>
                                </select>
                            </div>

                            
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Jenis Kelamin
                                </label>
                                <select
                                    wire:model.live="filterJenisKelamin"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    <option value="Laki-laki">L</option>
                                    <option value="Perempuan">P</option>
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
                    <tr style="background-color:#1B5E4F;color:white;">
                        
                        <th class="px-6 py-4 text-center" style="width:60px;">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                class="w-4 h-4 rounded transition-colors cursor-pointer"
                                style="color:#1B5E4F;border-color:#D1D5DB;"
                            >
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">NIY</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">JK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tempat Lahir</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tanggal Lahir</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Jabatan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">TMT</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Nama Kelompok</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tingkat Kelompok</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tanggal Penempatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $gurus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guru): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            
                            <td class="px-6 py-4 text-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedIds"
                                    value="<?php echo e($guru->id); ?>"
                                    class="w-4 h-4 rounded transition-colors cursor-pointer"
                                    style="color:#1B5E4F;border-color:#D1D5DB;"
                                >
                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php echo e($guru->niy); ?>

                            </td>

                            
                            <td class="px-6 py-4 text-sm font-medium" style="color:#1A2422;">
                                <?php echo e($guru->nama); ?>

                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <span
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold"
                                    style="background-color:<?php echo e($guru->jenis_kelamin === 'Laki-laki' ? '#E0F2FE' : '#FCE7F3'); ?>;color:<?php echo e($guru->jenis_kelamin === 'Laki-laki' ? '#0C4A6E' : '#831843'); ?>;"
                                >
                                    <?php echo e($guru->jenis_kelamin === 'Laki-laki' ? 'L' : 'P'); ?>

                                </span>
                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php echo e($guru->tempat_lahir ?? '-'); ?>

                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php echo e($guru->tanggal_lahir ? \Carbon\Carbon::parse($guru->tanggal_lahir)->tgl() : '-'); ?>

                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php
                                    $jabatanColor = match($guru->jabatan) {
                                        'Guru Pembimbing Tahfidz' => ['bg' => '#E8F5F1', 'text' => '#1B5E4F'],
                                        'Kepala Sekolah' => ['bg' => '#FEF3C7', 'text' => '#C9A227'],
                                        default => ['bg' => '#E8EEF2', 'text' => '#5B7A8C'],
                                    };
                                ?>
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                    style="background-color:<?php echo e($jabatanColor['bg']); ?>;color:<?php echo e($jabatanColor['text']); ?>;"
                                >
                                    <?php echo e($guru->jabatan); ?>

                                </span>
                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php echo e($guru->tanggal_mulai_kerja ? \Carbon\Carbon::parse($guru->tanggal_mulai_kerja)->tgl() : '-'); ?>

                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guru->jabatan === 'Guru Pembimbing Tahfidz'): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guru->kelompoksBimbingan->count() > 0): ?>
                                        <div class="flex flex-col gap-1">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $guru->kelompoksBimbingan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" style="background-color:#E8F5F1;color:#1B5E4F;border:1px solid #D1EAE3;width:max-content;">
                                                    <?php echo e($kel->nama); ?>

                                                </span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs italic" style="color:#9CA3AF;">Belum ditempatkan</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <span class="text-xs" style="color:#D1D5DB;">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guru->jabatan === 'Guru Pembimbing Tahfidz'): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guru->kelompoksBimbingan->count() > 0): ?>
                                        <div class="flex flex-col gap-1">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $guru->kelompoksBimbingan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" style="background-color:#F4F5F4;color:#5B7A8C;border:1px solid #E5E7EB;width:max-content;">
                                                    <?php echo e($kel->tingkat); ?>

                                                </span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs italic" style="color:#9CA3AF;">-</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <span class="text-xs" style="color:#D1D5DB;">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guru->jabatan === 'Guru Pembimbing Tahfidz'): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guru->kelompoksBimbingan->count() > 0): ?>
                                        <div class="flex flex-col gap-1">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $guru->kelompoksBimbingan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kel->tanggal_penempatan_guru): ?>
                                                    <span class="text-xs" style="color:#1A2422;">
                                                        <?php echo e($kel->tanggal_penempatan_guru->tgl()); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-xs italic" style="color:#9CA3AF;">-</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs italic" style="color:#9CA3AF;">-</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <span class="text-xs" style="color:#D1D5DB;">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>

                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="11" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || $filterJabatan || $filterJenisKelamin): ?>
                                            Tidak ada guru yang sesuai dengan filter
                                        <?php else: ?>
                                            Belum ada data guru
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gurus->hasPages()): ?>
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                <?php echo e($gurus->links()); ?>

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
                            Apakah Anda yakin ingin menghapus <strong style="color:#9B3A3A;"><?php echo e(count($selectedIds)); ?> guru</strong> yang dipilih?
                        </p>

                        <div class="mt-3 p-3 rounded-lg" style="background-color:#FEF3C7;border-left:3px solid #C9A227;">
                            <p class="font-medium mb-2" style="color:#1A2422;font-size:0.8125rem;">
                                Peringatan:
                            </p>
                            <ul class="space-y-1 ml-4 text-xs" style="color:#4B5563;list-style-type:disc;">
                                <li>Guru yang membimbing kelompok akan kehilangan pembimbingnya</li>
                                <li>Semua tugas tambahan akan ikut terhapus</li>
                                <li>Semua riwayat penilaian setoran dan absensi akan terhapus permanen</li>
                                <li>Data tidak dapat dikembalikan setelah dihapus</li>
                            </ul>
                        </div>
                    </div>

                    
                    <div class="flex gap-3">
                        <button
                            wire:click="closeBulkDeleteConfirm"
                            type="button"
                            class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                            style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                        >
                            Batal
                        </button>
                        <button
                            wire:click="bulkDelete"
                            type="button"
                            class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                            style="background-color:#9B3A3A;color:#FFFFFF;border:1.5px solid #9B3A3A;"
                        >
                            Hapus Semua
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showRegistrasiKeluarModal): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none" role="dialog" aria-modal="true" style="pointer-events: none;">
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl" style="font-family:'Inter',sans-serif; pointer-events: auto;">
                
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl" style="background-color:#9B3A3A;">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Registrasi Keluar Guru
                        </h3>
                    </div>
                    <button wire:click="tutupModalRegistrasiKeluar" class="text-white hover:text-red-200 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                
                <div class="p-6">

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($kelompokAktif)): ?>
                        
                        <div class="flex gap-3 p-4 mb-5 rounded-xl" style="background-color:#FEF2F2; border:1.5px solid #FCA5A5;">
                            <svg class="w-5 h-5 shrink-0 mt-0.5" style="color:#DC2626;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold mb-2" style="color:#991B1B;">
                                    Guru ini tidak dapat diregistrasi keluar!
                                </p>
                                <p class="text-sm mb-3" style="color:#991B1B;">
                                    Guru ini masih terdaftar sebagai pembimbing pada kelompok berikut. Silakan ganti pembimbing pada kelompok-kelompok tersebut terlebih dahulu melalui menu <strong>Kelompok Tahfidz</strong>.
                                </p>
                                <ul class="space-y-1">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kelompokAktif; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $namaKelompok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <li class="flex items-center gap-2 text-sm" style="color:#7F1D1D;">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full shrink-0" style="background-color:#DC2626;"></span>
                                            <strong><?php echo e($namaKelompok); ?></strong>
                                        </li>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </ul>
                            </div>
                        </div>

                        <?php $guruTerpilih = $gurus->firstWhere('id', $selectedIds[0] ?? null); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guruTerpilih): ?>
                            <div class="flex items-center gap-4 p-4 rounded-xl" style="background-color:#F8FAFC; border:1px solid #E2E8F0;">
                                <div class="shrink-0 w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg" style="background-color:#E0F2FE;color:#0369A1;">
                                    <?php echo e(substr($guruTerpilih->nama, 0, 1)); ?>

                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900"><?php echo e($guruTerpilih->nama); ?></h4>
                                    <p class="text-xs text-gray-500">NIY: <?php echo e($guruTerpilih->niy); ?> &bull; <?php echo e($guruTerpilih->jabatan); ?></p>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php else: ?>
                        
                        <div class="flex gap-3 p-4 mb-6 rounded-lg" style="background-color:#FEF2F2; border:1px solid #FEE2E2;">
                            <svg class="w-5 h-5 shrink-0 mt-0.5" style="color:#DC2626;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="text-sm" style="color:#991B1B;">
                                Perhatian: Guru yang didaftarkan keluar akan dipindahkan ke tab <strong>Guru Non-Aktif</strong>. Aksi ini tidak akan menghapus data riwayat di masa lalu, namun guru tersebut tidak lagi aktif di sistem.
                            </p>
                        </div>

                        <?php $guruTerpilih = $gurus->firstWhere('id', $selectedIds[0] ?? null); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guruTerpilih): ?>
                            <div class="flex items-center gap-4 p-4 mb-6 rounded-xl" style="background-color:#F8FAFC; border:1px solid #E2E8F0;">
                                <div class="shrink-0 w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg" style="background-color:#E0F2FE;color:#0369A1;">
                                    <?php echo e(substr($guruTerpilih->nama, 0, 1)); ?>

                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900"><?php echo e($guruTerpilih->nama); ?></h4>
                                    <p class="text-xs text-gray-500">NIY: <?php echo e($guruTerpilih->niy); ?> &bull; Jabatan: <?php echo e($guruTerpilih->jabatan); ?></p>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="space-y-4">
                            
                            <div>
                                <label class="block text-sm font-medium mb-1" style="color:#374151;">
                                    Jenis Keluar <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="jenis_keluar" class="w-full rounded-lg px-3 py-2 text-sm focus:ring-2 focus:outline-none" style="border:1px solid #D1D5DB; color:#1A2422;">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="Pensiun">Pensiun</option>
                                    <option value="Mutasi">Mutasi / Pindah Tugas</option>
                                    <option value="Berhenti">Berhenti</option>
                                    <option value="Meninggal">Meninggal Dunia</option>
                                </select>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jenis_keluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div>
                                <label class="block text-sm font-medium mb-1" style="color:#374151;">
                                    Tanggal Efektif Keluar <span class="text-red-500">*</span>
                                </label>
                                <input type="date" wire:model="tanggal_keluar" class="w-full rounded-lg px-3 py-2 text-sm focus:ring-2 focus:outline-none" style="border:1px solid #D1D5DB; color:#1A2422;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tanggal_keluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <div>
                                <label class="block text-sm font-medium mb-1" style="color:#374151;">
                                    Keterangan / Catatan
                                </label>
                                <textarea wire:model="keterangan_keluar" rows="3" class="w-full rounded-lg px-3 py-2 text-sm focus:ring-2 focus:outline-none" style="border:1px solid #D1D5DB; color:#1A2422;" placeholder="Tuliskan keterangan detail jika ada..."></textarea>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['keterangan_keluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="flex gap-3 px-6 py-4 rounded-b-2xl" style="background-color:#F9FAFB; border-top:1px solid #E5E7EB;">
                    <button wire:click="tutupModalRegistrasiKeluar" type="button" class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors" style="background-color:#FFFFFF; color:#374151; border:1px solid #D1D5DB;">
                        <?php echo e(!empty($kelompokAktif) ? 'Tutup' : 'Batal'); ?>

                    </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($kelompokAktif)): ?>
                        <button wire:click="simpanRegistrasiKeluar" type="button" class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors" style="background-color:#9B3A3A; color:#FFFFFF; border:1px solid #9B3A3A;">
                            <span wire:loading.remove wire:target="simpanRegistrasiKeluar">Konfirmasi &amp; Keluarkan Guru</span>
                            <span wire:loading wire:target="simpanRegistrasiKeluar">Memproses...</span>
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showFormModal): ?>
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif; pointer-events: none;">
            <div class="flex min-h-full items-center justify-center p-4" style="pointer-events: auto;">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-4xl w-full overflow-visible flex flex-col" style="border:1px solid #E5E7EB; max-height: 90vh;">

                    
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl shrink-0" style="background-color:#1B5E4F;">
                        <h3 class="text-xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                            <?php echo e($isEdit ? 'Edit Data Guru' : 'Registrasi Guru Baru'); ?>

                        </h3>
                        <button wire:click="closeFormModal" class="text-white hover:text-gray-200 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    
                    <div class="overflow-y-auto overflow-x-visible flex-1 p-6 space-y-6">

                        
                        <div>
                            <h4 class="text-lg font-semibold text-[#1A2422] mb-4 border-b pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">Identitas</h4>
                            <div class="space-y-3">
                                
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">NIY <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <input
                                            type="text"
                                            wire:model.live="niy"
                                            maxlength="16"
                                            inputmode="numeric"
                                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 16)"
                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                'border-red-400 bg-red-50' => $errors->has('niy'),
                                                'border-gray-300' => !$errors->has('niy'),
                                            ]); ?>"
                                            placeholder="16 digit angka"
                                        >
                                        <div class="flex items-center justify-between mt-1">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['niy'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <p class="text-xs" style="color:#9B3A3A;"><?php echo e($message); ?></p>
                                            <?php else: ?>
                                                <span></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <span class="text-xs text-gray-400"><?php echo e(strlen($niy ?? '')); ?>/16</span>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Nama Lengkap <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <input
                                            type="text"
                                            wire:model.live="nama"
                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                'border-red-400 bg-red-50' => $errors->has('nama'),
                                                'border-gray-300' => !$errors->has('nama'),
                                            ]); ?>"
                                            placeholder="Nama lengkap guru"
                                        >
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs" style="color:#9B3A3A;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                
                                <div class="grid grid-cols-12 gap-3 items-center">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Jenis Kelamin <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                    <div class="col-span-8">
                                        <div class="flex gap-6">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" wire:model.live="jenis_kelamin" value="Laki-laki" class="accent-[#1B5E4F]">
                                                <span class="text-sm text-gray-700">L</span>
                                            </label>
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio" wire:model.live="jenis_kelamin" value="Perempuan" class="accent-[#1B5E4F]">
                                                <span class="text-sm text-gray-700">P</span>
                                            </label>
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jenis_kelamin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs" style="color:#9B3A3A;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Tempat Lahir <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <input
                                            type="text"
                                            wire:model.live="tempat_lahir"
                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                'border-red-400 bg-red-50' => $errors->has('tempat_lahir'),
                                                'border-gray-300' => !$errors->has('tempat_lahir'),
                                            ]); ?>"
                                            placeholder="Kota/Kabupaten"
                                        >
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tempat_lahir'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs" style="color:#9B3A3A;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Tanggal Lahir <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8 relative">
                                        <div wire:ignore>
                                            <input
                                                type="text"
                                                id="tanggal_lahir"
                                                x-data="{
                                                    init() {
                                                        const fp = flatpickr($el, {
                                                            dateFormat: 'Y-m-d',
                                                            maxDate: 'today',
                                                            static: false,
                                                            appendTo: document.body,
                                                            position: 'auto center',
                                                            locale: {
                                                                firstDayOfWeek: 1,
                                                                weekdays: {
                                                                    shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                                                                    longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
                                                                },
                                                                months: {
                                                                    shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                                                                    longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                                                                }
                                                            },
                                                            onChange: function(selectedDates, dateStr) {
                                                                window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('tanggal_lahir', dateStr);
                                                            },
                                                            onOpen: function(selectedDates, dateStr, instance) {
                                                                // Pastikan calendar ter-render dengan benar
                                                                setTimeout(() => {
                                                                    instance.calendarContainer.style.width = 'auto';
                                                                }, 10);
                                                            }
                                                        });
                                                    }
                                                }"
                                                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                    'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                    'border-red-400 bg-red-50' => $errors->has('tanggal_lahir'),
                                                    'border-gray-300' => !$errors->has('tanggal_lahir'),
                                                ]); ?>"
                                                placeholder="Pilih tanggal"
                                                value="<?php echo e($tanggal_lahir); ?>"
                                            >
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tanggal_lahir'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs" style="color:#9B3A3A;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Jabatan <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <select
                                            wire:model.live="jabatan"
                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                'border-red-400 bg-red-50' => $errors->has('jabatan'),
                                                'border-gray-300' => !$errors->has('jabatan'),
                                            ]); ?>"
                                        >
                                            <option value="">Pilih</option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $jabatanOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <option value="<?php echo e($jab); ?>"><?php echo e($jab); ?></option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </select>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jabatan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs" style="color:#9B3A3A;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Tanggal Mulai Kerja <span class="text-gray-400 font-normal">(Opsional)</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8 relative">
                                        <div wire:ignore>
                                            <input
                                                type="text"
                                                id="tanggal_mulai_kerja"
                                                x-data="{
                                                    init() {
                                                        const fp = flatpickr($el, {
                                                            dateFormat: 'Y-m-d',
                                                            maxDate: 'today',
                                                            static: false,
                                                            appendTo: document.body,
                                                            position: 'auto center',
                                                            locale: {
                                                                firstDayOfWeek: 1,
                                                                weekdays: {
                                                                    shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                                                                    longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
                                                                },
                                                                months: {
                                                                    shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                                                                    longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                                                                }
                                                            },
                                                            onChange: function(selectedDates, dateStr) {
                                                                window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('tanggal_mulai_kerja', dateStr);
                                                            },
                                                            onOpen: function(selectedDates, dateStr, instance) {
                                                                // Pastikan calendar ter-render dengan benar
                                                                setTimeout(() => {
                                                                    instance.calendarContainer.style.width = 'auto';
                                                                }, 10);
                                                            }
                                                        });
                                                    }
                                                }"
                                                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                    'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                    'border-red-400 bg-red-50' => $errors->has('tanggal_mulai_kerja'),
                                                    'border-gray-300' => !$errors->has('tanggal_mulai_kerja'),
                                                ]); ?>"
                                                placeholder="Pilih tanggal"
                                                value="<?php echo e($tanggal_mulai_kerja); ?>"
                                            >
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tanggal_mulai_kerja'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs" style="color:#9B3A3A;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div>
                            <h4 class="text-lg font-semibold text-[#1A2422] mb-4 border-b pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">Kontak</h4>
                            <div class="space-y-3">
                                
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">No. Telepon <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <input
                                            type="text"
                                            wire:model.live="no_telepon"
                                            maxlength="13"
                                            inputmode="numeric"
                                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 13)"
                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                'border-red-400 bg-red-50' => $errors->has('no_telepon'),
                                                'border-gray-300' => !$errors->has('no_telepon'),
                                            ]); ?>"
                                            placeholder="08xxxxxxxxxx"
                                        >
                                        <div class="flex items-center justify-between mt-1">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['no_telepon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <p class="text-xs" style="color:#9B3A3A;"><?php echo e($message); ?></p>
                                            <?php else: ?>
                                                <span></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <span class="text-xs text-gray-400"><?php echo e(strlen($no_telepon ?? '')); ?>/13</span>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Email <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <input
                                            type="email"
                                            wire:model.live="email"
                                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                'w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm',
                                                'border-red-400 bg-red-50' => $errors->has('email'),
                                                'border-gray-300' => !$errors->has('email'),
                                            ]); ?>"
                                            placeholder="email@example.com"
                                        >
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs" style="color:#9B3A3A;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    
                    <div class="mt-6 pt-6 pb-2 px-6 flex justify-end gap-3 shrink-0" style="border-top:1px solid #E5E7EB;">
                        <button wire:click="closeFormModal" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #9B3A3A; border: 1.5px solid #9B3A3A;">
                            Batal
                        </button>
                        <button wire:click="saveForm" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #1B5E4F; border: 1.5px solid #1B5E4F;">
                            <span wire:loading.remove wire:target="saveForm">Simpan Data</span>
                            <span wire:loading wire:target="saveForm">Menyimpan...</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/admin/master-data/guru/index-guru.blade.php ENDPATH**/ ?>