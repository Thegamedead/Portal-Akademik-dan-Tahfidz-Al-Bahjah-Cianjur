

<div class="space-y-6">

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Review Usulan Kenaikan Tingkat
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Kelola dan review usulan kenaikan tingkat dari guru pembimbing
        </p>

        
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mt-6">
            <div class="rounded-xl p-4 col-span-2 lg:col-span-1" style="background:linear-gradient(135deg,#E8F5F1,#D1EDE5);border:1px solid #A7D7C5;">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color:#1B5E4F;">Total Usulan</p>
                <p class="text-3xl font-extrabold mt-1" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;"><?php echo e($this->getStats()['total']); ?></p>
            </div>
            <div class="rounded-xl p-4" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border:1px solid #BFDBFE;">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Perlu Review (Diajukan)</p>
                <p class="text-3xl font-extrabold mt-1" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E40AF;"><?php echo e($this->getStats()['diajukan']); ?></p>
            </div>
            <div class="rounded-xl p-4" style="background:linear-gradient(135deg,#FFFBEB,#FEF3C7);border:1px solid #FCD34D;">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Disetujui (Tunggu Plotting)</p>
                <p class="text-3xl font-extrabold mt-1" style="font-family:'Plus Jakarta Sans',sans-serif;color:#92400E;"><?php echo e($this->getStats()['disetujui']); ?></p>
            </div>
            <div class="rounded-xl p-4" style="background:linear-gradient(135deg,#ECFDF5,#D1FAE5);border:1px solid #A7F3D0;">
                <p class="text-xs font-semibold uppercase tracking-wide text-green-700">Ditempatkan</p>
                <p class="text-3xl font-extrabold mt-1" style="font-family:'Plus Jakarta Sans',sans-serif;color:#065F46;"><?php echo e($this->getStats()['ditempatkan']); ?></p>
            </div>
            <div class="rounded-xl p-4" style="background:linear-gradient(135deg,#FEF2F2,#FEE2E2);border:1px solid #FECACA;">
                <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Ditolak</p>
                <p class="text-3xl font-extrabold mt-1" style="font-family:'Plus Jakarta Sans',sans-serif;color:#9B3A3A;"><?php echo e($this->getStats()['ditolak']); ?></p>
            </div>
        </div>
    </div>

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

            
            <div class="flex flex-wrap items-center gap-3">
                <?php
                    // Get selected usulan untuk determine available actions
                    $selectedUsulansCollection = \App\Models\UsulanKenaikanTingkat::whereIn('id', $selectedUsulans)->get();
                    $singleSelected = count($selectedUsulansCollection) === 1 ? $selectedUsulansCollection->first() : null;
                ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($singleSelected && $singleSelected->status_usulan->value === 'Diajukan'): ?>
                    <button
                        wire:click="openReview(<?php echo e($singleSelected->id); ?>)"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2"
                        style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Review
                    </button>
                <?php else: ?>
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih 1 usulan dengan status 'Diajukan'"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Review
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($singleSelected && $singleSelected->status_usulan->value === 'Disetujui'): ?>
                    <button
                        wire:click="openPlacement(<?php echo e($singleSelected->id); ?>)"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2"
                        style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Tempatkan
                    </button>
                <?php else: ?>
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih 1 usulan dengan status 'Disetujui'"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Tempatkan
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($singleSelected && in_array($singleSelected->status_usulan->value, ['Disetujui', 'Ditempatkan'])): ?>
                    <?php
                        $cancelType = $singleSelected->status_usulan->value === 'Ditempatkan' ? 'placement' : 'approval';
                    ?>
                    <button
                        wire:click="openCancelModal(<?php echo e($singleSelected->id); ?>, '<?php echo e($cancelType); ?>')"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                        style="background-color:#FFFFFF;color:#9B3A3A;border:1.5px solid #9B3A3A;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batalkan
                    </button>
                <?php else: ?>
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih 1 usulan dengan status 'Disetujui' atau 'Ditempatkan'"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batalkan
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($singleSelected): ?>
                    <button
                        wire:click="viewDetail(<?php echo e($singleSelected->id); ?>)"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                        style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Lihat Detail
                    </button>
                <?php else: ?>
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih 1 usulan untuk melihat detail"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Lihat Detail
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedUsulansCollection) > 0): ?>
                    <?php
                        $allDitolak = $selectedUsulansCollection->every(fn($u) => $u->status_usulan->value === 'Ditolak');
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allDitolak): ?>
                        <button
                            wire:click="openDeleteModal"
                            type="button"
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                            style="background-color:#9B3A3A;color:#FFFFFF;border:1.5px solid #9B3A3A;"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus (<?php echo e(count($selectedUsulansCollection)); ?>)
                        </button>
                    <?php else: ?>
                        <button
                            disabled
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50"
                            style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                            title="Hanya usulan dengan status 'Ditolak' yang dapat dihapus"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Hapus
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php else: ?>
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih minimal 1 usulan dengan status 'Ditolak'"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="flex items-center sm:ml-2 sm:pl-4 sm:border-l border-gray-200">
                    <span class="text-sm text-gray-500">
                        Total <strong style="color:#1A2422;"><?php echo e($proposals->total()); ?></strong> usulan
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedUsulans) > 0): ?>
                            <span class="mx-1.5">&bull;</span>
                            <strong style="color:#1B5E4F;"><?php echo e(count($selectedUsulans)); ?></strong> terpilih
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                </div>
            </div>

            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1 lg:flex-initial lg:min-w-[400px]">
                
                <div class="flex-1 sm:min-w-[250px]">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5" style="color:#6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari nama siswa atau NIS..."
                            class="w-full pl-10 pr-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                            style="border:1.5px solid #D1D5DB;color:#1A2422;"
                        >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search): ?>
                            <button
                                wire:click="$set('search', '')"
                                type="button"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
                
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        type="button"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none whitespace-nowrap cursor-pointer"
                        style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                        <?php
                            $activeFilters = collect([$filterStatus, $filterTingkatAsal, $filterTingkatTujuan, $filterKelompokAsal])->filter()->count();
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeFilters > 0): ?>
                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                <?php echo e($activeFilters); ?>

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
                     class="absolute z-50 right-0 mt-2 w-72 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                     style="display: none; border: 1px solid #E5E7EB;">

                    <div class="p-4 space-y-3">
                        
                        <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                            <h3 class="text-sm font-semibold" style="color:white;font-family:'Plus Jakarta Sans',sans-serif;">
                                Filter Usulan
                            </h3>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterStatus || $filterTingkatAsal || $filterTingkatTujuan || $filterKelompokAsal): ?>
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
                                Status Usulan
                            </label>
                            <select
                                wire:model.live="filterStatus"
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                <option value="">Semua</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e(strtolower($key)); ?>"><?php echo e($label); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>

                        
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Tingkat Asal
                            </label>
                            <select
                                wire:model.live="filterTingkatAsal"
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                <option value="">Semua</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tingkatOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>

                        
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Tingkat Tujuan
                            </label>
                            <select
                                wire:model.live="filterTingkatTujuan"
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                <option value="">Semua</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tingkatOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>

                        
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Kelompok Asal
                            </label>
                            <select
                                wire:model.live="filterKelompokAsal"
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                <option value="">Semua</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kelompoks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelompok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($kelompok->id); ?>">
                                        <?php echo e($kelompok->nama); ?> (<?php echo e($kelompok->tingkat); ?>)
                                    </option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
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
                        <th class="px-6 py-4 text-left" style="color:white;width:50px;">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                wire:click="toggleSelectAll"
                                class="w-4 h-4 rounded border-gray-300 focus:ring-2 focus:ring-offset-2"
                                style="cursor:pointer;"
                            >
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">
                            Tanggal Usul
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">
                            Tanggal Approve
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">
                            Nama Siswa
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">
                            NIS
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">
                            Kelas
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">
                            Guru Pengusul
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">
                            Kelompok Asal
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">
                            Tingkat Asal
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">
                            Tingkat Tujuan
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">
                            Status Usulan
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">
                            Tanggal Penempatan
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $proposals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usulan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            
                            <td class="px-6 py-4">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedUsulans"
                                    value="<?php echo e($usulan->id); ?>"
                                    class="w-4 h-4 rounded border-gray-300 focus:ring-2 focus:ring-offset-2"
                                    style="cursor:pointer;"
                                >
                            </td>
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;font-variant-numeric:tabular-nums;">
                                <span class="font-medium">
                                    <?php echo e($usulan->tanggal_usul ? $usulan->tanggal_usul->format('d F Y') : '-'); ?>

                                </span>
                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;font-variant-numeric:tabular-nums;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($usulan->tanggal_keputusan): ?>
                                    <span class="font-medium">
                                        <?php echo e($usulan->tanggal_keputusan->format('d F Y')); ?>

                                    </span>
                                <?php else: ?>
                                    <span style="color:#9CA3AF;">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>

                            
                            <td class="px-6 py-4 text-sm font-medium" style="color:#1A2422;">
                                <?php echo e($usulan->siswa->nama); ?>

                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;font-variant-numeric:tabular-nums;">
                                <?php echo e($usulan->siswa->nis); ?>

                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php echo e($usulan->siswa->kelas->nama_kelas ?? '-'); ?>

                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php echo e($usulan->guruPengusul->nama); ?>

                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php echo e($usulan->kelompokAsal->nama ?? '-'); ?>

                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php echo e($usulan->tingkat_asal->value ?? '-'); ?>

                            </td>

                            
                            <td class="px-6 py-4 text-sm font-medium" style="color:#1A2422;">
                                <?php echo e($usulan->tingkat_tujuan->value); ?>

                            </td>

                            
                            <td class="px-6 py-4">
                                <?php
                                    $badgeColor = $this->getStatusBadgeColor($usulan->status_usulan);
                                ?>
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium whitespace-nowrap"
                                    style="background-color:<?php echo e($badgeColor['bg']); ?>;color:<?php echo e($badgeColor['text']); ?>;"
                                >
                                    <?php echo e($usulan->status_usulan->value); ?>

                                </span>
                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($usulan->tanggal_penempatan): ?>
                                    <span class="font-medium text-xs text-gray-900">
                                        <?php echo e(\Carbon\Carbon::parse($usulan->tanggal_penempatan)->format('d F Y')); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="12" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterStatus || $filterTingkatAsal || $filterTingkatTujuan || $filterKelompokAsal): ?>
                                            Tidak ada usulan yang sesuai dengan filter
                                        <?php else: ?>
                                            Belum ada usulan kenaikan tingkat
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </p>
                                    <p class="mt-1 text-xs" style="color:#9CA3AF;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterStatus || $filterTingkatAsal || $filterTingkatTujuan || $filterKelompokAsal): ?>
                                            Coba ubah atau reset filter untuk melihat usulan lainnya
                                        <?php else: ?>
                                            Usulan dari guru pembimbing akan muncul di sini
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($proposals->hasPages()): ?>
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                <?php echo e($proposals->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.usulan-kenaikan-tingkat.detail-usulan');

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1688408023-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

    
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.usulan-kenaikan-tingkat.review-usulan');

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1688408023-1', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

    
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.usulan-kenaikan-tingkat.penempatan-siswa');

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1688408023-2', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCancelModal): ?>
        <div
            x-data="{ show: <?php if ((object) ('showCancelModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showCancelModal'->value()); ?>')<?php echo e('showCancelModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showCancelModal'); ?>')<?php endif; ?> }"
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto pointer-events-none"
            style="font-family:'Inter',sans-serif;"
        >
            
            <div class="fixed inset-0 bg-black/40 pointer-events-auto" wire:click="closeCancelModal"></div>

            
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div
                    x-show="show"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl pointer-events-auto"
                    style="border:1px solid #E5E7EB;"
                >
                    
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full" style="background-color:#FEE2E2;">
                                <svg class="w-5 h-5" style="color:#9B3A3A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                                <?php echo e($cancelType === 'placement' ? 'Batalkan Penempatan & Persetujuan' : 'Batalkan Persetujuan Usulan'); ?>

                            </h3>
                        </div>
                        <button
                            type="button"
                            wire:click="closeCancelModal"
                            class="rounded-lg p-1 transition-colors focus:outline-none"
                            style="color:#6B7280;"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    
                    <div class="px-6 py-5 space-y-4">
                        
                        <div class="rounded-lg px-4 py-3 text-sm" style="background-color:#FEF3C7;color:#92400E;border:1px solid #FDE047;">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="font-semibold mb-1">Peringatan!</p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cancelType === 'placement'): ?>
                                        <p class="mb-2">Tindakan ini akan membatalkan penempatan sekaligus persetujuan usulan:</p>
                                        <ul class="list-disc list-inside space-y-1 pl-2">
                                            <li>Siswa dikembalikan ke <strong>kelompok asal</strong></li>
                                            <li>Riwayat kelompok penempatan akan <strong>dihapus</strong></li>
                                            <li>Status usulan langsung kembali menjadi <strong>"Diajukan"</strong></li>
                                        </ul>
                                    <?php else: ?>
                                        <p>Tindakan ini akan membatalkan persetujuan usulan dan mengembalikan status langsung menjadi <strong>"Diajukan"</strong>.</p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        
                        <div>
                            <label for="alasanPembatalan" class="block text-sm font-medium mb-1.5" style="color:#1A2422;">
                                Alasan Pembatalan <span style="color:#DC2626;">*</span>
                            </label>
                            <textarea
                                id="alasanPembatalan"
                                wire:model="alasanPembatalan"
                                rows="4"
                                class="w-full rounded-lg py-2.5 px-3 text-sm focus:outline-none focus:ring-2 <?php $__errorArgs = ['alasanPembatalan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                style="background-color:#FFFFFF;border:1.5px solid #E5E7EB;color:#1A2422;focus:ring-color:#9B3A3A;resize:vertical;"
                                placeholder="Jelaskan alasan pembatalan (minimal 10 karakter)"
                            ></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['alasanPembatalan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1.5 text-xs" style="color:#DC2626;"><?php echo e($message); ?></p>
                            <?php else: ?>
                                <p class="mt-1.5 text-xs" style="color:#6B7280;">
                                    Alasan wajib diisi minimal 10 karakter.
                                </p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="flex items-center justify-end gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB; background-color: #F9FAFB;">
                        
                        <button
                            type="button"
                            wire:click="closeCancelModal"
                            wire:loading.attr="disabled"
                            class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none disabled:opacity-50"
                            style="background-color:#FFFFFF;color:#6B7280;border:1.5px solid #D1D5DB;"
                        >
                            Tutup
                        </button>

                        
                        <button
                            type="button"
                            wire:click="cancelAction"
                            wire:loading.attr="disabled"
                            wire:target="cancelAction"
                            class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            style="background-color:#9B3A3A;color:#FFFFFF;border:1.5px solid #9B3A3A;focus:ring-color:#9B3A3A;"
                        >
                            <span wire:loading.remove wire:target="cancelAction">
                                Ya, Batalkan
                            </span>
                            <span wire:loading wire:target="cancelAction" class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showDeleteModal): ?>
        <div
            x-data="{ show: <?php if ((object) ('showDeleteModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showDeleteModal'->value()); ?>')<?php echo e('showDeleteModal'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('showDeleteModal'); ?>')<?php endif; ?> }"
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto pointer-events-none"
            style="font-family:'Inter',sans-serif;"
        >
            
            <div class="fixed inset-0 bg-black/40 pointer-events-auto" wire:click="closeDeleteModal"></div>

            
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div
                    x-show="show"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl pointer-events-auto"
                    style="border:1px solid #E5E7EB;"
                >
                    
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full" style="background-color:#FEE2E2;">
                                <svg class="w-5 h-5" style="color:#9B3A3A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                                Konfirmasi Hapus Usulan
                            </h3>
                        </div>
                        <button
                            type="button"
                            wire:click="closeDeleteModal"
                            class="rounded-lg p-1 transition-colors focus:outline-none"
                            style="color:#6B7280;"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    
                    <div class="px-6 py-5">
                        
                        <div class="rounded-lg px-4 py-3 text-sm mb-4" style="background-color:#FEE2E2;color:#991B1B;border:1px solid #FCA5A5;">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="font-semibold mb-1">Peringatan Penghapusan!</p>
                                    <p>Anda akan menghapus <strong><?php echo e(count($selectedUsulans)); ?> usulan</strong>. Tindakan ini tidak dapat dibatalkan.</p>
                                    <p class="mt-2 text-xs">Hanya usulan dengan status <strong>"Ditolak"</strong> yang dapat dihapus.</p>
                                </div>
                            </div>
                        </div>

                        <p class="text-sm" style="color:#6B7280;">
                            Apakah Anda yakin ingin melanjutkan?
                        </p>
                    </div>

                    
                    <div class="flex items-center justify-end gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB; background-color: #F9FAFB;">
                        
                        <button
                            type="button"
                            wire:click="closeDeleteModal"
                            wire:loading.attr="disabled"
                            class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none disabled:opacity-50"
                            style="background-color:#FFFFFF;color:#6B7280;border:1.5px solid #D1D5DB;"
                        >
                            Batal
                        </button>

                        
                        <button
                            type="button"
                            wire:click="bulkDelete"
                            wire:loading.attr="disabled"
                            wire:target="bulkDelete"
                            class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            style="background-color:#9B3A3A;color:#FFFFFF;border:1.5px solid #9B3A3A;focus:ring-color:#9B3A3A;"
                        >
                            <span wire:loading.remove wire:target="bulkDelete">Ya, Hapus <?php echo e(count($selectedUsulans)); ?> Usulan</span>
                            <span wire:loading wire:target="bulkDelete" class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menghapus...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/admin/usulan-kenaikan-tingkat/index-usulan.blade.php ENDPATH**/ ?>