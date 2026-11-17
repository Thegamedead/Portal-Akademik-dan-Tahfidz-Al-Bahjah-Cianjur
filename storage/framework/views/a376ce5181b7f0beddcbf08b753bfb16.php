<div class="space-y-6" style="font-family:'Inter',sans-serif;">

    
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Manajemen Tes Penempatan
                </h1>
                <p class="mt-1 text-sm" style="color:#6B7280;">
                    Kelola jadwal, hasil tes, dan penempatan siswa baru ke kelompok hafalan
                </p>
            </div>
            <button
                wire:click="$dispatch('openScheduleModal')"
                type="button"
                class="inline-flex items-center px-4 py-2.5 text-sm font-semibold rounded-xl transition-all shadow-sm"
                style="background-color:#1B5E4F;color:#FFFFFF;"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Jadwalkan Tes Baru
            </button>
        </div>

        
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mt-6">
            <div class="rounded-xl p-4 col-span-2 lg:col-span-1" style="background:linear-gradient(135deg,#E8F5F1,#D1EDE5);border:1px solid #A7D7C5;">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color:#1B5E4F;">Total Tes</p>
                <p class="text-3xl font-extrabold mt-1" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;"><?php echo e($stats['total']); ?></p>
            </div>
            <div class="rounded-xl p-4" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border:1px solid #BFDBFE;">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Terjadwal</p>
                <p class="text-3xl font-extrabold mt-1" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1E40AF;"><?php echo e($stats['terjadwal']); ?></p>
            </div>
            <div class="rounded-xl p-4" style="background:linear-gradient(135deg,#ECFDF5,#D1FAE5);border:1px solid #A7F3D0;">
                <p class="text-xs font-semibold uppercase tracking-wide text-green-700">Selesai</p>
                <p class="text-3xl font-extrabold mt-1" style="font-family:'Plus Jakarta Sans',sans-serif;color:#065F46;"><?php echo e($stats['selesai']); ?></p>
            </div>
            <div class="rounded-xl p-4" style="background:linear-gradient(135deg,#FFFBEB,#FEF3C7);border:1px solid #FCD34D;">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Perlu Penempatan</p>
                <p class="text-3xl font-extrabold mt-1" style="font-family:'Plus Jakarta Sans',sans-serif;color:#92400E;"><?php echo e($stats['belumDitempatkan']); ?></p>
            </div>
            <div class="rounded-xl p-4" style="background:linear-gradient(135deg,#FEF2F2,#FEE2E2);border:1px solid #FECACA;">
                <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Dibatalkan</p>
                <p class="text-3xl font-extrabold mt-1" style="font-family:'Plus Jakarta Sans',sans-serif;color:#9B3A3A;"><?php echo e($stats['dibatalkan']); ?></p>
            </div>
        </div>
    </div>

    
    <div class="rounded-2xl bg-white shadow-sm p-4" style="border:1px solid #E5E7EB;">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            
            <div class="flex flex-wrap items-center gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedIds) > 0): ?>
                    <span class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="background:#E8F5F1;color:#1B5E4F;">
                        <?php echo e(count($selectedIds)); ?> dipilih
                    </span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedIds) === 1): ?>
                        <?php $singleTest = $tests->firstWhere('id', $selectedIds[0]); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($singleTest && $singleTest->status?->value === 'Selesai' && !$singleTest->ditempatkan_at): ?>
                            <a href="<?php echo e(route('admin.tes-penempatan.review', $selectedIds[0])); ?>"
                               class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg"
                               style="background:#1E40AF;color:#FFF;">
                                Review & Tempatkan
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($singleTest && $singleTest->status?->value === 'Terjadwal'): ?>
                            <button wire:click="cancelSingle(<?php echo e($selectedIds[0]); ?>)" type="button"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg"
                                    style="background:#92400E;color:#FFF;">
                                Batalkan
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <button
                        wire:click="deleteSelected"
                        wire:confirm="Yakin hapus <?php echo e(count($selectedIds)); ?> tes terpilih? (Hanya status Terjadwal/Dibatalkan)"
                        type="button"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg"
                        style="background:#9B3A3A;color:#FFF;">
                        Hapus Terpilih
                    </button>
                <?php else: ?>
                    <span class="text-sm" style="color:#9CA3AF;">Pilih baris untuk aksi massal</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-[480px]">
                <div class="flex-1 relative">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari siswa (NIS, Nama)..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                        onfocus="this.style.borderColor='#1B5E4F'" onblur="this.style.borderColor='#D1D5DB'"/>
                    <svg class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2" style="color:#9CA3AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" type="button"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-xl transition-colors whitespace-nowrap w-full sm:w-auto"
                        style="background:#FFF;color:#1A2422;border:1.5px solid #D1D5DB;">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filter
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($status || $guru_penguji_id || $tanggal_mulai || $tanggal_akhir): ?>
                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background:#1B5E4F;color:#FFF;">
                                <?php echo e(collect([$status, $guru_penguji_id, $tanggal_mulai, $tanggal_akhir])->filter()->count()); ?>

                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         class="absolute z-50 right-0 mt-2 w-80 rounded-2xl shadow-xl bg-white overflow-hidden"
                         style="display:none;border:1px solid #E5E7EB;">
                        <div class="p-4 space-y-3">
                            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                                <h3 class="text-sm font-bold" style="color:white;">Filter Tes Penempatan</h3>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($status || $guru_penguji_id || $tanggal_mulai || $tanggal_akhir): ?>
                                    <button wire:click="resetFilters" @click="open=false" type="button" class="text-xs font-medium" style="color:#1B5E4F;">Reset Semua</button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold mb-1.5" style="color:#6B7280;">Status</label>
                                <select wire:model.live="status" class="w-full px-3 py-2 text-sm rounded-lg" style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                    <option value="">Semua Status</option>
                                    <option value="Terjadwal">Terjadwal</option>
                                    <option value="Selesai">Selesai</option>
                                    <option value="Dibatalkan">Dibatalkan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold mb-1.5" style="color:#6B7280;">Guru Penguji</label>
                                <select wire:model.live="guru_penguji_id" class="w-full px-3 py-2 text-sm rounded-lg" style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                    <option value="">Semua Guru</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $gurus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guru): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <option value="<?php echo e($guru->id); ?>"><?php echo e($guru->nama); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold mb-1.5" style="color:#6B7280;">Dari Tanggal</label>
                                    <input type="date" wire:model.live="tanggal_mulai" class="w-full px-3 py-2 text-sm rounded-lg" style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold mb-1.5" style="color:#6B7280;">Sampai</label>
                                    <input type="date" wire:model.live="tanggal_akhir" class="w-full px-3 py-2 text-sm rounded-lg" style="border:1.5px solid #D1D5DB;color:#1A2422;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="background-color:#1B5E4F;">
                        <th class="px-5 py-4 w-10 text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-white text-[#1B5E4F] focus:ring-white">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white">Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white">Jadwal & Penguji</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white">Rekomendasi / Kelompok</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-white">Tanggal Penempatan</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $test): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-gray-50 transition-colors" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processElementKey('test-{{ $test->id }}', get_defined_vars()); ?>wire:key="test-<?php echo e($test->id); ?>">
                            
                            <td class="px-5 py-4 text-center">
                                <input type="checkbox" wire:model.live="selectedIds" value="<?php echo e($test->id); ?>" class="rounded border-gray-300 text-[#1B5E4F] focus:ring-[#1B5E4F]">
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold" style="color:#1A2422;"><?php echo e($test->siswa->nama); ?></div>
                                <div class="text-xs" style="color:#6B7280;"><?php echo e($test->siswa->nis); ?></div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium" style="color:#1A2422;">
                                    <?php echo e($test->tanggal_tes->translatedFormat('d M Y')); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($test->waktu_tes): ?>
                                        <span class="text-xs ml-1" style="color:#6B7280;"><?php echo e(substr($test->waktu_tes, 0, 5)); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="text-xs" style="color:#6B7280;"><?php echo e($test->guruPenguji->nama); ?></div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($test->status?->value === 'Terjadwal'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background:#DBEAFE;color:#1D4ED8;">
                                        Terjadwal
                                    </span>
                                <?php elseif($test->status?->value === 'Selesai'): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($test->ditempatkan_at): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background:#E8F5F1;color:#1B5E4F;">
                                            ✓ Ditempatkan
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background:#FEF3C7;color:#92400E;">
                                            ⚠ Perlu Penempatan
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background:#FEE2E2;color:#9B3A3A;">
                                        Dibatalkan
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            
                            <td class="px-6 py-4">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($test->rekomendasi_tingkat): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium" style="background:#F3E8FF;color:#6B21A8;">
                                        <?php echo e($test->rekomendasi_tingkat?->value); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($test->finalKelompok): ?>
                                    <div class="text-xs font-medium mt-1" style="color:#1B5E4F;"><?php echo e($test->finalKelompok->nama); ?></div>
                                <?php elseif(!$test->rekomendasi_tingkat): ?>
                                    <span class="text-xs" style="color:#9CA3AF;">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            
                            <td class="px-6 py-4">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($test->ditempatkan_at): ?>
                                    <div class="text-sm" style="color:#1A2422;"><?php echo e(\Carbon\Carbon::parse($test->ditempatkan_at)->translatedFormat('d M Y')); ?></div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($test->admin): ?>
                                        <div class="text-xs" style="color:#6B7280;">oleh <?php echo e($test->admin->nama); ?></div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <span class="text-xs" style="color:#9CA3AF;">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($test->status?->value === 'Selesai' && !$test->ditempatkan_at): ?>
                                        <a href="<?php echo e(route('admin.tes-penempatan.review', $test->id)); ?>"
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
                                           style="background:#1B5E4F;color:#FFF;"
                                           title="Review & Tempatkan siswa">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Tempatkan
                                        </a>
                                    <?php elseif($test->status?->value === 'Selesai' && $test->ditempatkan_at): ?>
                                        <a href="<?php echo e(route('admin.tes-penempatan.review', $test->id)); ?>"
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium transition-all"
                                           style="background:#F3F4F6;color:#374151;border:1px solid #E5E7EB;">
                                            Lihat Detail
                                        </a>
                                    <?php elseif($test->status?->value === 'Terjadwal'): ?>
                                        <button type="button" wire:click="cancelSingle(<?php echo e($test->id); ?>)"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium transition-all"
                                                style="background:#FEE2E2;color:#9B3A3A;border:1px solid #FECACA;">
                                            Batalkan
                                        </button>
                                    <?php else: ?>
                                        <span class="text-xs" style="color:#9CA3AF;">-</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search || $status || $guru_penguji_id || $tanggal_mulai || $tanggal_akhir): ?>
                                            Tidak ada tes sesuai filter. <button wire:click="resetFilters" class="underline" style="color:#1B5E4F;">Reset filter</button>
                                        <?php else: ?>
                                            Belum ada data tes penempatan
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tests->hasPages()): ?>
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                <?php echo e($tests->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Modals -->
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.tes-penempatan.schedule-test', []);

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3981874202-0', $__key);

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
[$__name, $__params] = $__split('admin.tes-penempatan.cancel-test', []);

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3981874202-1', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/admin/tes-penempatan/index-tes-penempatan.blade.php ENDPATH**/ ?>