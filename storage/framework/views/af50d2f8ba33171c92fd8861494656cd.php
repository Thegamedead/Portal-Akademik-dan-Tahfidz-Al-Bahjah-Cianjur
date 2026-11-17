<div class="space-y-6">

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Usulan Kenaikan Tingkat
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Lihat usulan kenaikan tingkat siswa dari kelompok binaan Anda
        </p>
    </div>

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

            
            <div class="flex items-center">
                <span class="text-sm text-gray-500">
                    Total <strong style="color:#1A2422;"><?php echo e($usulans->total()); ?></strong> usulan
                </span>
            </div>

            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-[400px]">
                
                <div class="flex-1">
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari nama siswa atau NIS..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg text-sm transition-all focus:outline-none focus:ring-2"
                            style="border:1.5px solid #D1D5DB;color:#1A2422;"
                            onfocus="this.style.borderColor='#1B5E4F';this.style.boxShadow='0 0 0 3px rgba(27,94,79,0.1)';"
                            onblur="this.style.borderColor='#D1D5DB';this.style.boxShadow='none';"
                        />
                        <svg class="h-5 w-5 absolute left-3 top-1/2 -translate-y-1/2" style="color:#6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
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
                        <?php
                            $activeFilters = collect([$filterStatus, $filterTingkatAsal, $filterTingkatTujuan])->filter()->count();
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
                            
                            <div class="flex items-center justify-between pb-2" style="border-bottom: 1px solid #E5E7EB;">
                                <h3 class="text-sm font-semibold" style="color:#1A2422;font-family:'Plus Jakarta Sans',sans-serif;">
                                    Filter Usulan
                                </h3>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterStatus || $filterTingkatAsal || $filterTingkatTujuan): ?>
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
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="Diajukan">Diajukan</option>
                                    <option value="Disetujui">Disetujui</option>
                                    <option value="Ditolak">Ditolak</option>
                                    <option value="Ditempatkan">Ditempatkan</option>
                                </select>
                            </div>

                            
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Tingkat Asal
                                </label>
                                <select
                                    wire:model.live="filterTingkatAsal"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua Tingkat</option>
                                    <option value="Dasar">Dasar</option>
                                    <option value="Menengah">Menengah</option>
                                    <option value="Atas">Atas</option>
                                </select>
                            </div>

                            
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Tingkat Tujuan
                                </label>
                                <select
                                    wire:model.live="filterTingkatTujuan"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua Tingkat</option>
                                    <option value="Dasar">Dasar</option>
                                    <option value="Menengah">Menengah</option>
                                    <option value="Atas">Atas</option>
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
                    <tr style="background-color:#1B5E4F;">
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tanggal Usul</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tanggal Approve</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Nama Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">NIS</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Kelompok Asal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tingkat Asal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tingkat Tujuan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Status Usulan</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $usulans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usulan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium" style="color:#1A2422;">
                                        <?php echo e($usulan->tanggal_usul ? \Carbon\Carbon::parse($usulan->tanggal_usul)->format('d M Y') : '-'); ?>

                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($usulan->tanggal_usul): ?>
                                        <span class="text-xs" style="color:#6B7280;">
                                            <?php echo e(\Carbon\Carbon::parse($usulan->tanggal_usul)->format('H:i')); ?> WIB
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>

                            
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($usulan->tanggal_keputusan): ?>
                                        <span class="text-sm font-medium" style="color:#1B5E4F;">
                                            <?php echo e(\Carbon\Carbon::parse($usulan->tanggal_keputusan)->format('d M Y')); ?>

                                        </span>
                                        <span class="text-xs" style="color:#6B7280;">
                                            <?php echo e(\Carbon\Carbon::parse($usulan->tanggal_keputusan)->format('H:i')); ?> WIB
                                        </span>
                                    <?php else: ?>
                                        <span class="text-sm font-medium text-gray-400">-</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>

                            
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium" style="color:#1A2422;"><?php echo e($usulan->siswa->nama); ?></div>
                            </td>

                            
                            <td class="px-6 py-4">
                                <div class="text-sm" style="color:#1A2422;"><?php echo e($usulan->siswa->nis); ?></div>
                            </td>

                            
                            <td class="px-6 py-4">
                                <div class="text-sm" style="color:#1A2422;"><?php echo e($usulan->siswa->kelas->nama_kelas ?? '-'); ?></div>
                            </td>

                            
                            <td class="px-6 py-4">
                                <div class="text-sm" style="color:#1A2422;"><?php echo e($usulan->kelompokAsal->nama ?? '-'); ?></div>
                            </td>

                            
                            <td class="px-6 py-4">
                                <?php
                                    $tingkatAsalStr = $usulan->tingkat_asal instanceof \BackedEnum ? $usulan->tingkat_asal->value : (string) $usulan->tingkat_asal;
                                ?>
                                <div class="text-sm" style="color:#6B7280;"><?php echo e($tingkatAsalStr ?: '-'); ?></div>
                            </td>

                            
                            <td class="px-6 py-4">
                                <?php
                                    $tingkatTujuanStr = $usulan->tingkat_tujuan instanceof \BackedEnum ? $usulan->tingkat_tujuan->value : (string) $usulan->tingkat_tujuan;
                                ?>
                                <div class="text-sm font-medium" style="color:#1B5E4F;"><?php echo e($tingkatTujuanStr ?: '-'); ?></div>
                            </td>

                            
                            <td class="px-6 py-4">
                                <?php
                                    $statusStr = $usulan->status_usulan instanceof \BackedEnum ? $usulan->status_usulan->value : (string) $usulan->status_usulan;
                                    $statusColors = [
                                        'Diajukan' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
                                        'Disetujui' => ['bg' => '#E8F5F1', 'text' => '#1B5E4F'],
                                        'Ditolak' => ['bg' => '#FEE2E2', 'text' => '#9B3A3A'],
                                        'Ditempatkan' => ['bg' => '#E0F2FE', 'text' => '#075985'],
                                    ];
                                    $colors = $statusColors[$statusStr] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                                ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                      style="background-color:<?php echo e($colors['bg']); ?>;color:<?php echo e($colors['text']); ?>;">
                                    <?php echo e($statusStr); ?>

                                </span>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || $filterStatus || $filterTingkatAsal || $filterTingkatTujuan): ?>
                                            Tidak ada usulan yang sesuai dengan filter
                                        <?php else: ?>
                                            Belum ada usulan kenaikan tingkat dari kelompok binaan Anda
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($usulans->hasPages()): ?>
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                <?php echo e($usulans->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/guru/usulan-kenaikan/index-usulan.blade.php ENDPATH**/ ?>