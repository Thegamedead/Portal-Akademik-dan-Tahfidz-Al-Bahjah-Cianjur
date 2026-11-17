<div class="space-y-6">

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Kelompok Tahfidz
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Lihat data kelompok tahfidz yang Anda bimbing
        </p>
    </div>

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

            
            <div class="flex items-center gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedIds) === 1): ?>
                    <button
                        wire:click="bukaAnggotaModal(<?php echo e(collect($selectedIds)->first()); ?>)"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
                        style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Anggota Kelompok
                    </button>
                    <a
                        href="<?php echo e(route('guru.kelompok-tahfidz.setoran', collect($selectedIds)->first())); ?>"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
                        style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Setoran Hafalan
                    </a>
                <?php else: ?>
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih tepat 1 kelompok untuk aksi"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Anggota Kelompok
                    </button>
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih tepat 1 kelompok untuk aksi"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Setoran Hafalan
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span class="text-sm text-gray-500 ml-2 border-l pl-4 border-gray-200">
                    Total <strong style="color:#1A2422;"><?php echo e($kelompoks->total()); ?></strong> kelompok
                </span>
            </div>

            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-[400px]">
                
                <div class="flex-1">
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari nama kelompok..."
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
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterTingkat || $filterJenis): ?>
                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                <?php echo e(collect([$filterTingkat, $filterJenis])->filter()->count()); ?>

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
                                    Filter Kelompok
                                </h3>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterTingkat || $filterJenis): ?>
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
                                    Tingkat
                                </label>
                                <select
                                    wire:model.live="filterTingkat"
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
                                    Jenis
                                </label>
                                <select
                                    wire:model.live="filterJenis"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua Jenis</option>
                                    <option value="Banin">Banin</option>
                                    <option value="Banat">Banat</option>
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
                        <th class="px-6 py-4 text-center" style="width:60px;">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                class="w-4 h-4 rounded transition-colors cursor-pointer"
                                style="color:#1B5E4F;border-color:#D1D5DB;"
                            >
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tingkat</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Jenis</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Nama Kelompok</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Guru Pembimbing</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Jumlah Juz</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Juz Ke</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Ayat</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Jumlah Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Status Kapasitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $kelompoks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelompok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            
                            <td class="px-6 py-4 text-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedIds"
                                    value="<?php echo e($kelompok->id); ?>"
                                    class="w-4 h-4 rounded transition-colors cursor-pointer"
                                    style="color:#1B5E4F;border-color:#D1D5DB;"
                                >
                            </td>

                            
                            <td class="px-6 py-4">
                                <?php
                                    $tingkatBg = match($kelompok->tingkat) {
                                        'Dasar' => '#F3F4F6',
                                        'Menengah' => '#E8F5F1',
                                        'Atas' => '#FEF3C7',
                                        default => '#F1F5F9'
                                    };
                                    $tingkatText = match($kelompok->tingkat) {
                                        'Dasar' => '#475569',
                                        'Menengah' => '#1B5E4F',
                                        'Atas' => '#92400E',
                                        default => '#475569'
                                    };
                                ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                      style="background-color:<?php echo e($tingkatBg); ?>;color:<?php echo e($tingkatText); ?>;">
                                    <?php echo e($kelompok->tingkat); ?>

                                </span>
                            </td>

                            
                            <td class="px-6 py-4">
                                <?php
                                    $jenisBg = $kelompok->jenis === 'Banin' ? '#E8F5F1' : '#FEF3C7';
                                    $jenisText = $kelompok->jenis === 'Banin' ? '#1B5E4F' : '#92400E';
                                ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                      style="background-color:<?php echo e($jenisBg); ?>;color:<?php echo e($jenisText); ?>;">
                                    <?php echo e($kelompok->jenis); ?>

                                </span>
                            </td>

                            
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium" style="color:#1A2422;"><?php echo e($kelompok->nama); ?></div>
                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php echo e($kelompok->guruPembimbing->nama); ?>

                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php
                                    $config = \App\Models\ParameterKonfigurasi::first();
                                    $batasTingkat = $config ? ($config->batas_tingkat ?? []) : [];
                                    $juzList = $batasTingkat[$kelompok->tingkat] ?? [];
                                    $jumlahJuz = count($juzList);
                                ?>
                                <span class="font-medium" style="font-variant-numeric: tabular-nums;"><?php echo e($jumlahJuz); ?></span> Juz
                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($jumlahJuz > 0): ?>
                                    <?php
                                        // Group consecutive juz into ranges
                                        $ranges = [];
                                        $sortedJuz = $juzList;
                                        sort($sortedJuz);

                                        if (count($sortedJuz) > 0) {
                                            $start = $sortedJuz[0];
                                            $end = $sortedJuz[0];

                                            for ($i = 1; $i < count($sortedJuz); $i++) {
                                                if ($sortedJuz[$i] == $end + 1) {
                                                    $end = $sortedJuz[$i];
                                                } else {
                                                    $ranges[] = $start == $end ? $start : "$start-$end";
                                                    $start = $sortedJuz[$i];
                                                    $end = $sortedJuz[$i];
                                                }
                                            }
                                            $ranges[] = $start == $end ? $start : "$start-$end";
                                        }
                                    ?>
                                    <span class="text-xs" style="color:#1A2422;">
                                        Juz <?php echo e(implode(', ', $ranges)); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-xs" style="color:#9CA3AF;">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>

                            
                            <td class="px-6 py-4 text-sm text-center" style="color:#1A2422;">
                                <?php
                                    $totalAyat = 0;
                                    if ($jumlahJuz > 0) {
                                        $totalAyat = \App\Models\ReferensiQuran::whereIn('juz_ke', $juzList)
                                            ->sum('jumlah_ayat');
                                    }
                                ?>
                                <span class="font-medium" style="font-variant-numeric: tabular-nums;"><?php echo e(number_format($totalAyat, 0, ',', '.')); ?></span> Ayat
                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <span class="font-medium"><?php echo e($kelompok->siswas_count); ?></span> / <?php echo e($kelompok->kapasitas_maks); ?>

                            </td>

                            
                            <td class="px-6 py-4">
                                <?php
                                    $statusKapasitas = $kelompok->siswas_count >= $kelompok->kapasitas_maks ? 'Penuh' : 'Tersedia';
                                    $statusBg = $statusKapasitas === 'Tersedia' ? '#1B5E4F' : '#9B3A3A';
                                ?>
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                    style="background-color:<?php echo e($statusBg); ?>;color:#FFFFFF;"
                                >
                                    <?php echo e($statusKapasitas); ?>

                                </span>
                            </td>

                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || $filterTingkat || $filterJenis): ?>
                                            Tidak ada kelompok yang sesuai dengan filter
                                        <?php else: ?>
                                            Anda belum membimbing kelompok tahfidz
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelompoks->hasPages()): ?>
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                <?php echo e($kelompoks->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAnggotaModal && $selectedKelompokId): ?>
        <div class="fixed inset-0 z-[60] overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif;">
            <div class="fixed inset-0 pointer-events-none" wire:click="tutupAnggotaModal"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-4xl w-full flex flex-col" style="border:1px solid #E5E7EB; max-height:90vh;">

                    
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                        <div>
                            <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">Anggota Kelompok</h3>
                            <p class="text-sm mt-0.5" style="color:#A7D8CC;"><?php echo e($kelompokNama); ?> &bull; <?php echo e($kelompokTingkat); ?> &bull; <?php echo e($kelompokJenis); ?></p>
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
                            <span class="text-xs font-medium" style="color:#065F46;">Guru: <?php echo e($guruNama); ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" style="color:#1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-xs font-medium" style="color:#065F46;">Jumlah Anggota: <strong><?php echo e(count($anggotaList)); ?> siswa</strong></span>
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
                                onfocus="this.style.borderColor='#1B5E4F';"
                                onblur="this.style.borderColor='#D1D5DB';"
                            >
                        </div>
                    </div>

                    <div class="flex-1 overflow-auto px-6 pb-6">
                        <table class="min-w-full divide-y divide-gray-200 rounded-xl overflow-hidden border border-gray-200">
                            <thead class="sticky top-0 z-10" style="background-color:#F9FAFB;">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NIS</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelas</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tgl. Masuk</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                    $filteredAnggota = collect($anggotaList);
                                    if ($searchAnggota) {
                                        $s = strtolower($searchAnggota);
                                        $filteredAnggota = $filteredAnggota->filter(fn($sw) =>
                                            str_contains(strtolower($sw['nama']), $s) ||
                                            str_contains(strtolower($sw['nis'] ?? ''), $s)
                                        );
                                    }
                                    $filteredAnggota = $filteredAnggota->values();
                                ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $filteredAnggota; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $siswa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($index + 1); ?></td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="text-sm font-semibold" style="color:#1A2422;"><?php echo e($siswa['nama']); ?></span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm" style="color:#6B7280;"><?php echo e($siswa['nis']); ?></td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm" style="color:#1A2422;"><?php echo e($siswa['kelas_nama']); ?></td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa['tanggal_masuk'] !== '—'): ?>
                                                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-full"
                                                      style="background-color:#E8F5F1; color:#1B5E4F;">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    <?php echo e($siswa['tanggal_masuk']); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-xs text-gray-400 italic">Tidak tercatat</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full"
                                                  style="background-color:#DCFCE7; color:#15803D;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block animate-pulse"></span>
                                                Aktif
                                            </span>
                                        </td>
                                    </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($searchAnggota): ?>
                                                Tidak ada siswa yang sesuai pencarian.
                                            <?php else: ?>
                                                Belum ada siswa di kelompok ini.
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/guru/kelompok/index-kelompok-tahfidz.blade.php ENDPATH**/ ?>