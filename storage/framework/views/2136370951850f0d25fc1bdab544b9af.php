<div class="space-y-6">

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Riwayat Setoran Hafalan
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Histori setoran dan penilaian hafalan siswa
        </p>
    </div>

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($role, ['admin', 'guru'])): ?>
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4" style="color:#9CA3AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text"
                               wire:model.live.debounce.300ms="searchSiswa"
                               placeholder="Cari siswa (NIS, Nama)..."
                               class="w-full pl-10 px-4 py-2.5 text-sm rounded-lg transition-colors focus:outline-none focus:ring-2"
                               style="border:1.5px solid #D1D5DB;color:#1A2422;background-color:#FFFFFF;"
                               onfocus="this.style.borderColor='#1B5E4F';this.style.boxShadow='0 0 0 3px rgba(27,94,79,0.1)'"
                               onblur="this.style.borderColor='#D1D5DB';this.style.boxShadow='none'">
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
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
                        $activeFilters = collect([
                            $tanggalMulai,
                            $tanggalSelesai,
                            $filterJenisSetoran,
                            $filterPredikat,
                            $filterKeputusan,
                            $filterKelompokId
                        ])->filter()->count();
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
                     @click="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-40"
                     style="display: none;">
                </div>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     @click.stop
                     class="absolute z-50 right-0 mt-2 w-80 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                     style="display: none; border: 1px solid #E5E7EB;">

                    <div class="p-4 space-y-3">
                        
                        <div class="flex items-center justify-between pb-2" style="border-bottom: 1px solid #E5E7EB;">
                            <h3 class="text-sm font-semibold" style="color:#1A2422;font-family:'Plus Jakarta Sans',sans-serif;">
                                Filter Setoran
                            </h3>
                            <div class="flex items-center gap-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tanggalMulai || $tanggalSelesai || $filterJenisSetoran || $filterPredikat || $filterKeputusan || $filterKelompokId): ?>
                                    <button
                                        wire:click="resetFilters"
                                        type="button"
                                        class="text-xs font-medium text-gray-500 hover:text-[#1B5E4F] transition-colors"
                                    >
                                        Reset
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <button
                                    @click="open = false"
                                    type="button"
                                    class="text-gray-400 hover:text-gray-600 transition-colors"
                                    title="Tutup Filter"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        
                        <div>
                            <label class="block text-xs font-medium mb-2" style="color:#6B7280;">
                                Rentang Cepat
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <button wire:click="setDateRange('today')"
                                        type="button"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors hover:bg-emerald-50 hover:text-emerald-700"
                                        style="border:1px solid #D1D5DB;color:#6B7280;">
                                    Hari Ini
                                </button>
                                <button wire:click="setDateRange('yesterday')"
                                        type="button"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors hover:bg-emerald-50 hover:text-emerald-700"
                                        style="border:1px solid #D1D5DB;color:#6B7280;">
                                    Kemarin
                                </button>
                                <button wire:click="setDateRange('last7days')"
                                        type="button"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors hover:bg-emerald-50 hover:text-emerald-700"
                                        style="border:1px solid #D1D5DB;color:#6B7280;">
                                    7 Hari
                                </button>
                                <button wire:click="setDateRange('last30days')"
                                        type="button"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors hover:bg-emerald-50 hover:text-emerald-700"
                                        style="border:1px solid #D1D5DB;color:#6B7280;">
                                    30 Hari
                                </button>
                                <button wire:click="setDateRange('thisMonth')"
                                        type="button"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors hover:bg-emerald-50 hover:text-emerald-700"
                                        style="border:1px solid #D1D5DB;color:#6B7280;">
                                    Bulan Ini
                                </button>
                                <button wire:click="setDateRange('lastMonth')"
                                        type="button"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors hover:bg-emerald-50 hover:text-emerald-700"
                                        style="border:1px solid #D1D5DB;color:#6B7280;">
                                    Bulan Lalu
                                </button>
                            </div>
                        </div>

                        
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Tanggal Mulai
                            </label>
                            <input type="date"
                                   wire:model.live="tanggalMulai"
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                   style="border:1.5px solid #D1D5DB;color:#1A2422;">
                        </div>

                        
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Tanggal Selesai
                            </label>
                            <input type="date"
                                   wire:model.live="tanggalSelesai"
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                   style="border:1.5px solid #D1D5DB;color:#1A2422;">
                        </div>

                        
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Jenis Setoran
                            </label>
                            <select wire:model.live="filterJenisSetoran"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                <option value="">Semua</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $jenisSetoranOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jenis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($jenis->value); ?>"><?php echo e($jenis->value); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>

                        
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Predikat
                            </label>
                            <select wire:model.live="filterPredikat"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                <option value="">Semua</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $predikatOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $predikat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($predikat->value); ?>"><?php echo e($predikat->value); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>

                        
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Keputusan
                            </label>
                            <select wire:model.live="filterKeputusan"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                <option value="">Semua</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $keputusanOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $keputusan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($keputusan->value); ?>"><?php echo e($keputusan->value); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($role, ['admin', 'guru'])): ?>
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Kelompok
                                </label>
                                <select wire:model.live="filterKelompokId"
                                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                        style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                    <option value="">Semua</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kelompoks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelompok): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <option value="<?php echo e($kelompok->id); ?>"><?php echo e($kelompok->nama); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tanggal</th>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($role !== 'siswa'): ?>
                            <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Nama Siswa</th>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Juz - Surah - Ayat</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold" style="color:white;">Jenis</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold" style="color:white;">Predikat</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold" style="color:white;">Keputusan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold" style="color:white;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $setorans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setoran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php echo e(\Carbon\Carbon::parse($setoran->absensi->tanggal)->tgl()); ?>

                            </td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($role !== 'siswa'): ?>
                                <td class="px-6 py-4 text-sm font-medium" style="color:#1A2422;">
                                    <?php echo e($setoran->absensi->siswa->nama_lengkap); ?>

                                </td>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <div class="flex flex-col">
                                    <span class="font-medium">Juz <?php echo e($setoran->juz->juz_ke); ?> - <?php echo e($setoran->surah->nama_latin); ?></span>
                                    <span class="text-xs" style="color:#6B7280;">Ayat <?php echo e($setoran->ayat_mulai); ?> - <?php echo e($setoran->ayat_selesai); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php
                                    $jenisColors = [
                                        'Hafalan Baru' => ['bg' => '#FEF3C7', 'text' => '#C9A227'],
                                        'Ulang' => ['bg' => '#E0F2FE', 'text' => '#0369A1'],
                                    ];
                                    $jenisColor = $jenisColors[$setoran->jenis_setoran->value] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                                ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                      style="background-color:<?php echo e($jenisColor['bg']); ?>;color:<?php echo e($jenisColor['text']); ?>;">
                                    <?php echo e($setoran->jenis_setoran->value); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php
                                    $predikatColors = [
                                        'Lancar' => ['bg' => '#1B5E4F', 'text' => '#FFFFFF'],
                                        'Kurang Lancar' => ['bg' => '#C9A227', 'text' => '#FFFFFF'],
                                        'Tidak Lancar' => ['bg' => '#9B3A3A', 'text' => '#FFFFFF'],
                                    ];
                                    $predikatColor = $predikatColors[$setoran->predikat->value] ?? ['bg' => '#6B7280', 'text' => '#FFFFFF'];
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                                      style="background-color:<?php echo e($predikatColor['bg']); ?>;color:<?php echo e($predikatColor['text']); ?>;">
                                    <?php echo e($setoran->predikat->value); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php
                                    $keputusanColors = [
                                        'Lanjut' => ['bg' => '#E8F5F1', 'text' => '#1B5E4F'],
                                        'Ulang' => ['bg' => '#FEE2E2', 'text' => '#9B3A3A'],
                                    ];
                                    $keputusanColor = $keputusanColors[$setoran->keputusan->value] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                                ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                      style="background-color:<?php echo e($keputusanColor['bg']); ?>;color:<?php echo e($keputusanColor['text']); ?>;">
                                    <?php echo e($setoran->keputusan->value); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="showDetail(<?php echo e($setoran->id); ?>)"
                                        class="text-sm font-medium transition-colors"
                                        style="color:#1B5E4F;"
                                        onmouseover="this.style.color='#155441'"
                                        onmouseout="this.style.color='#1B5E4F'">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="<?php echo e($role !== 'siswa' ? '7' : '6'); ?>" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tanggalMulai || $tanggalSelesai || $filterJenisSetoran || $filterPredikat || $filterKeputusan || $filterKelompokId || $searchSiswa): ?>
                                            Tidak ada data setoran yang sesuai dengan filter
                                        <?php else: ?>
                                            Belum ada data setoran
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </p>
                                    <p class="mt-1 text-xs" style="color:#9CA3AF;">Coba ubah filter atau rentang tanggal</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($setorans->hasPages()): ?>
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                <?php echo e($setorans->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showDetailModal && $detailSetoran): ?>
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif;">
            
            <div class="fixed inset-0 pointer-events-none" wire:click="closeDetail"></div>

            
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-2xl w-full" style="border:1px solid #E5E7EB;">
                    
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl" style="background-color:#1B5E4F;">
                        <div>
                            <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">
                                Detail Setoran Hafalan
                            </h3>
                            <p class="mt-1 text-sm" style="color:#E8F5F1;">
                                <?php echo e($detailSetoran->absensi->siswa->nama_lengkap); ?> - <?php echo e(\Carbon\Carbon::parse($detailSetoran->absensi->tanggal)->tgl()); ?>

                            </p>
                        </div>
                        <button wire:click="closeDetail" class="text-white hover:text-gray-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    
                    <div class="px-6 py-5 space-y-4">
                        
                        <div class="rounded-lg p-4" style="background-color:#F9FAFB;">
                            <div class="text-sm mb-1" style="color:#6B7280;">Juz & Surah</div>
                            <div class="text-base font-semibold" style="color:#1A2422;">
                                Juz <?php echo e($detailSetoran->juz->juz_ke); ?> - <?php echo e($detailSetoran->surah->nama_latin); ?> (<?php echo e($detailSetoran->surah->nama_arab); ?>)
                            </div>
                            <div class="text-sm mt-2" style="color:#6B7280;">Ayat <?php echo e($detailSetoran->ayat_mulai); ?> - <?php echo e($detailSetoran->ayat_selesai); ?></div>
                        </div>

                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm mb-2" style="color:#6B7280;">Jenis Setoran</div>
                                <?php
                                    $jenisColors = [
                                        'Hafalan Baru' => ['bg' => '#FEF3C7', 'text' => '#C9A227'],
                                        'Ulang' => ['bg' => '#E0F2FE', 'text' => '#0369A1'],
                                    ];
                                    $jenisColor = $jenisColors[$detailSetoran->jenis_setoran->value] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                      style="background-color:<?php echo e($jenisColor['bg']); ?>;color:<?php echo e($jenisColor['text']); ?>;">
                                    <?php echo e($detailSetoran->jenis_setoran->value); ?>

                                </span>
                            </div>
                            <div>
                                <div class="text-sm mb-2" style="color:#6B7280;">Keputusan</div>
                                <?php
                                    $keputusanColors = [
                                        'Lanjut' => ['bg' => '#E8F5F1', 'text' => '#1B5E4F'],
                                        'Ulang' => ['bg' => '#FEE2E2', 'text' => '#9B3A3A'],
                                    ];
                                    $keputusanColor = $keputusanColors[$detailSetoran->keputusan->value] ?? ['bg' => '#F3F4F6', 'text' => '#6B7280'];
                                ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                      style="background-color:<?php echo e($keputusanColor['bg']); ?>;color:<?php echo e($keputusanColor['text']); ?>;">
                                    <?php echo e($detailSetoran->keputusan->value); ?>

                                </span>
                            </div>
                        </div>

                        
                        <div>
                            <div class="text-sm font-medium mb-3" style="color:#1A2422;">Komponen Penilaian</div>
                            <div class="grid grid-cols-2 gap-3">
                                <?php
                                    $skorColors = [
                                        'Lancar' => ['bg' => '#1B5E4F', 'text' => '#FFFFFF'],
                                        'Kurang Lancar' => ['bg' => '#C9A227', 'text' => '#FFFFFF'],
                                        'Tidak Lancar' => ['bg' => '#9B3A3A', 'text' => '#FFFFFF'],
                                    ];
                                ?>

                                
                                <div class="rounded-lg p-3" style="border:1px solid #E5E7EB;">
                                    <div class="text-xs mb-1" style="color:#6B7280;">Tajwid</div>
                                    <?php
                                        $skorColor = $skorColors[$detailSetoran->skor_tajwid->value] ?? ['bg' => '#6B7280', 'text' => '#FFFFFF'];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color:<?php echo e($skorColor['bg']); ?>;color:<?php echo e($skorColor['text']); ?>;">
                                        <?php echo e($detailSetoran->skor_tajwid->value); ?>

                                    </span>
                                </div>

                                
                                <div class="rounded-lg p-3" style="border:1px solid #E5E7EB;">
                                    <div class="text-xs mb-1" style="color:#6B7280;">Kelancaran</div>
                                    <?php
                                        $skorColor = $skorColors[$detailSetoran->skor_kelancaran->value] ?? ['bg' => '#6B7280', 'text' => '#FFFFFF'];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color:<?php echo e($skorColor['bg']); ?>;color:<?php echo e($skorColor['text']); ?>;">
                                        <?php echo e($detailSetoran->skor_kelancaran->value); ?>

                                    </span>
                                </div>

                                
                                <div class="rounded-lg p-3" style="border:1px solid #E5E7EB;">
                                    <div class="text-xs mb-1" style="color:#6B7280;">Makhraj</div>
                                    <?php
                                        $skorColor = $skorColors[$detailSetoran->skor_makhraj->value] ?? ['bg' => '#6B7280', 'text' => '#FFFFFF'];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color:<?php echo e($skorColor['bg']); ?>;color:<?php echo e($skorColor['text']); ?>;">
                                        <?php echo e($detailSetoran->skor_makhraj->value); ?>

                                    </span>
                                </div>

                                
                                <div class="rounded-lg p-3" style="border:1px solid #E5E7EB;">
                                    <div class="text-xs mb-1" style="color:#6B7280;">Fashahah</div>
                                    <?php
                                        $skorColor = $skorColors[$detailSetoran->skor_fashahah->value] ?? ['bg' => '#6B7280', 'text' => '#FFFFFF'];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color:<?php echo e($skorColor['bg']); ?>;color:<?php echo e($skorColor['text']); ?>;">
                                        <?php echo e($detailSetoran->skor_fashahah->value); ?>

                                    </span>
                                </div>
                            </div>
                        </div>

                        
                        <div class="rounded-lg p-4" style="background-color:#F9FAFB;">
                            <div class="text-sm mb-2" style="color:#6B7280;">Predikat Akhir</div>
                            <?php
                                $predikatColors = [
                                    'Lancar' => ['bg' => '#1B5E4F', 'text' => '#FFFFFF'],
                                    'Kurang Lancar' => ['bg' => '#C9A227', 'text' => '#FFFFFF'],
                                    'Tidak Lancar' => ['bg' => '#9B3A3A', 'text' => '#FFFFFF'],
                                ];
                                $predikatColor = $predikatColors[$detailSetoran->predikat->value] ?? ['bg' => '#6B7280', 'text' => '#FFFFFF'];
                            ?>
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-bold"
                                  style="background-color:<?php echo e($predikatColor['bg']); ?>;color:<?php echo e($predikatColor['text']); ?>;">
                                <?php echo e($detailSetoran->predikat->value); ?>

                            </span>
                            <p class="text-xs mt-2" style="color:#9CA3AF;">Predikat dihitung dari skor terendah komponen penilaian</p>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailSetoran->catatan): ?>
                            <div>
                                <div class="text-sm font-medium mb-2" style="color:#1A2422;">Catatan Guru</div>
                                <div class="rounded-lg p-3 text-sm" style="background-color:#F9FAFB;color:#1A2422;">
                                    <?php echo e($detailSetoran->catatan); ?>

                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailSetoran->saran_juz_id): ?>
                            <div class="rounded-lg p-3" style="background-color:#E0F2FE;">
                                <div class="text-xs mb-1" style="color:#0C4A6E;">Saran Ayat Berikutnya</div>
                                <div class="text-sm font-medium" style="color:#0C4A6E;">
                                    Juz <?php echo e($detailSetoran->saranJuz->juz_ke); ?> - <?php echo e($detailSetoran->saranSurah->nama_latin); ?>, Ayat <?php echo e($detailSetoran->saran_ayat_mulai); ?>

                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="flex justify-end px-6 py-4 rounded-b-2xl" style="background-color:#F9FAFB;border-top:1px solid #E5E7EB;">
                        <button
                            wire:click="closeDetail"
                            type="button"
                            class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                            style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/shared/riwayat-setoran.blade.php ENDPATH**/ ?>