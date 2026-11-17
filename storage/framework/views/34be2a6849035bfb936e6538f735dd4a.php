
<div class="space-y-6">

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Kelompok Tahfidz
                </h2>
                <p class="mt-1 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
                    Kelola data kelompok tahfidz dan anggota kelompok
                </p>
            </div>
        </div>
    </div>

    
    
    

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

            
            <div class="flex flex-wrap items-center gap-3">
                
                <button
                    wire:click="openCreateModal"
                    type="button"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 cursor-pointer"
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
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
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
                        title="Pilih tepat 1 kelompok untuk edit"
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
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
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
                        title="Pilih minimal 1 kelompok untuk hapus"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Hapus
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <span class="text-gray-300 text-xl font-light select-none">|</span>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedIds) === 1): ?>
                    <button
                        wire:click="bukaAnggotaModal(<?php echo e($selectedIds[0]); ?>)"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
                        style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Anggota Kelompok
                    </button>
                    <button
                        wire:click="bukaTempatkanSiswaModal(<?php echo e($selectedIds[0]); ?>)"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
                        style="background-color:#16A34A;color:#FFFFFF;border:1.5px solid #16A34A;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Tempatkan Siswa
                    </button>
                    <a
                        href="<?php echo e(route('admin.kelompok.absensisetoran', $selectedIds[0])); ?>"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
                        style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Setoran Hafalan
                    </a>

                
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        @click.away="open = false"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
                        style="background-color:#FFFFFF;color:#374151;border:1.5px solid #D1D5DB;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Pengaturan
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute z-50 left-0 mt-2 w-56 rounded-xl shadow-xl bg-white focus:outline-none overflow-hidden"
                         style="display: none; border: 1.5px solid #D1D5DB; box-shadow: 0 8px 24px rgba(0,0,0,0.12);">
                        <div class="p-1.5 space-y-0.5">
                            <button type="button" wire:click="bukaPengaturanModal" @click="open = false"
                                class="group flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg transition-all"
                                style=""
                                onmouseover="this.style.backgroundColor='#F0FDF4';this.style.color='#166534';this.querySelector('svg').style.color='#166534'"
                                onmouseout="this.style.backgroundColor='';this.style.color='';this.querySelector('svg').style.color='#9CA3AF'">
                                <svg class="mr-3 h-4 w-4 transition-colors" style="color:#9CA3AF" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Kapasitas Kelompok
                            </button>
                            <button type="button" wire:click="bukaTargetHafalanModal" @click="open = false"
                                class="group flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg transition-all"
                                onmouseover="this.style.backgroundColor='#F0FDF4';this.style.color='#166534';this.querySelector('svg').style.color='#166534'"
                                onmouseout="this.style.backgroundColor='';this.style.color='';this.querySelector('svg').style.color='#9CA3AF'">
                                <svg class="mr-3 h-4 w-4 transition-colors" style="color:#9CA3AF" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Target Hafalan
                            </button>
                            <button type="button" wire:click="bukaKomponenPenilaianModal" @click="open = false"
                                class="group flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg transition-all"
                                onmouseover="this.style.backgroundColor='#F0FDF4';this.style.color='#166534';this.querySelector('svg').style.color='#166534'"
                                onmouseout="this.style.backgroundColor='';this.style.color='';this.querySelector('svg').style.color='#9CA3AF'">
                                <svg class="mr-3 h-4 w-4 transition-colors" style="color:#9CA3AF" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                Komponen Penilaian
                            </button>
                        </div>
                    </div>
                </div>

                
                <div class="flex items-center sm:ml-2 sm:pl-4 sm:border-l border-gray-200">
                    <span class="text-sm text-gray-500">
                        Total <strong style="color:#1A2422;"><?php echo e($kelompoks->total()); ?></strong> kelompok
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedIds) > 0): ?>
                            <span class="mx-1.5">&bull;</span>
                            <strong style="color:#1B5E4F;"><?php echo e(count($selectedIds)); ?></strong> terpilih
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                </div>
                <?php else: ?>
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih tepat 1 kelompok untuk lihat anggota"
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
                        title="Pilih tepat 1 kelompok untuk tempatkan siswa"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Tempatkan Siswa
                    </button>
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih tepat 1 kelompok untuk lihat setoran hafalan"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Setoran Hafalan
                    </button>

                
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        @click.away="open = false"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer"
                        style="background-color:#FFFFFF;color:#374151;border:1.5px solid #D1D5DB;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Pengaturan
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute z-50 left-0 mt-2 w-56 rounded-xl bg-white focus:outline-none overflow-hidden"
                         style="display: none; border: 1.5px solid #D1D5DB; box-shadow: 0 8px 24px rgba(0,0,0,0.12);">
                        <div class="p-1.5 space-y-0.5">
                            <button type="button" wire:click="bukaPengaturanModal" @click="open = false"
                                class="group flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg transition-all"
                                onmouseover="this.style.backgroundColor='#F0FDF4';this.style.color='#166534';this.querySelector('svg').style.color='#166534'"
                                onmouseout="this.style.backgroundColor='';this.style.color='';this.querySelector('svg').style.color='#9CA3AF'">
                                <svg class="mr-3 h-4 w-4 transition-colors" style="color:#9CA3AF" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Kapasitas Kelompok
                            </button>
                            <button type="button" wire:click="bukaTargetHafalanModal" @click="open = false"
                                class="group flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg transition-all"
                                onmouseover="this.style.backgroundColor='#F0FDF4';this.style.color='#166534';this.querySelector('svg').style.color='#166534'"
                                onmouseout="this.style.backgroundColor='';this.style.color='';this.querySelector('svg').style.color='#9CA3AF'">
                                <svg class="mr-3 h-4 w-4 transition-colors" style="color:#9CA3AF" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Target Hafalan
                            </button>
                            <button type="button" wire:click="bukaKomponenPenilaianModal" @click="open = false"
                                class="group flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg transition-all"
                                onmouseover="this.style.backgroundColor='#F0FDF4';this.style.color='#166534';this.querySelector('svg').style.color='#166534'"
                                onmouseout="this.style.backgroundColor='';this.style.color='';this.querySelector('svg').style.color='#9CA3AF'">
                                <svg class="mr-3 h-4 w-4 transition-colors" style="color:#9CA3AF" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                Komponen Penilaian
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-[400px]">
                
                <div class="flex-1">
                    <?php if (isset($component)) { $__componentOriginal6962575527da6e31b80032f98e723ee8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6962575527da6e31b80032f98e723ee8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.search-field','data' => ['wireModel' => 'search','placeholder' => 'Cari nama kelompok atau guru...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.search-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wireModel' => 'search','placeholder' => 'Cari nama kelompok atau guru...']); ?>
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
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterTingkat || $filterJenis || $filterGuruPembimbing): ?>
                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                <?php echo e(collect([$filterTingkat, $filterJenis, $filterGuruPembimbing])->filter()->count()); ?>

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
                                    Filter Kelompok
                                </h3>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterTingkat || $filterJenis || $filterGuruPembimbing): ?>
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
                                    <option value="">Semua</option>
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
                                    <option value="">Semua</option>
                                    <option value="Banin">Banin</option>
                                    <option value="Banat">Banat</option>
                                </select>
                            </div>

                            
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Guru Pembimbing
                                </label>
                                <select
                                    wire:model.live="filterGuruPembimbing"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $guruPembimbings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guru): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <option value="<?php echo e($guru->id); ?>">
                                            <?php echo e($guru->nama); ?>

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

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php
                                    $tingkatColor = $this->getTingkatBadgeColor($kelompok->tingkat);
                                    $tingkatBg = match($tingkatColor) {
                                        'zamrud' => '#E8F5F1',
                                        'emas' => '#FEF3C7',
                                        default => '#F1F5F9'
                                    };
                                    $tingkatText = match($tingkatColor) {
                                        'zamrud' => '#1B5E4F',
                                        'emas' => '#92400E',
                                        default => '#475569'
                                    };
                                ?>
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                    style="background-color:<?php echo e($tingkatBg); ?>;color:<?php echo e($tingkatText); ?>;"
                                >
                                    <?php echo e($kelompok->tingkat); ?>

                                </span>
                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php
                                    $jenisColor = $this->getJenisBadgeColor($kelompok->jenis);
                                    $jenisBg = match($jenisColor) {
                                        'zamrud' => '#E8F5F1',
                                        'emas' => '#FEF3C7',
                                        default => '#F1F5F9'
                                    };
                                    $jenisText = match($jenisColor) {
                                        'zamrud' => '#1B5E4F',
                                        'emas' => '#92400E',
                                        default => '#475569'
                                    };
                                ?>
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                    style="background-color:<?php echo e($jenisBg); ?>;color:<?php echo e($jenisText); ?>;"
                                >
                                    <?php echo e($kelompok->jenis); ?>

                                </span>
                            </td>

                            
                            <td class="px-6 py-4 text-sm font-medium" style="color:#1A2422;">
                                <?php echo e($kelompok->nama); ?>

                            </td>

                            
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <?php echo e($kelompok->guruPembimbing->nama); ?>

                            </td>

                            
                            <td class="px-6 py-4 text-sm text-center" style="color:#1A2422;">
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
                                <span class="font-medium"><?php echo e($kelompok->jumlah_siswa); ?></span> / <?php echo e($kelompok->kapasitas_maks); ?>

                            </td>

                            
                            <td class="px-6 py-4">
                                <?php
                                    $statusColor = $this->getStatusKapasitasBadgeColor($kelompok->status_kapasitas);
                                    $statusBg = $statusColor === 'zamrud' ? '#1B5E4F' : '#9B3A3A';
                                ?>
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                    style="background-color:<?php echo e($statusBg); ?>;color:#FFFFFF;"
                                >
                                    <?php echo e($kelompok->status_kapasitas); ?>

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
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($filterTingkat || $filterJenis || $filterTahunAjaran || $filterGuruPembimbing): ?>
                                            Tidak ada kelompok yang sesuai dengan filter
                                        <?php else: ?>
                                            Belum ada data kelompok
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

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showBulkDeleteConfirm): ?>
        
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif;">
            <div class="fixed inset-0 pointer-events-none" wire:click="closeBulkDeleteConfirm"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6" style="border:2px solid #D1D5DB;">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full" style="background-color:#FEE2E2;">
                        <svg class="w-6 h-6" style="color:#9B3A3A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-center mb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                        Konfirmasi Penghapusan Massal
                    </h3>
                    <div class="text-sm mb-6" style="color:#6B7280;">
                        <p class="text-center mb-3">
                            Apakah Anda yakin ingin menghapus <strong style="color:#9B3A3A;"><?php echo e(count($selectedIds)); ?> kelompok</strong> yang dipilih?
                        </p>
                        <div class="mt-3 p-3 rounded-lg" style="background-color:#FEF3C7;border-left:3px solid #C9A227;">
                            <p class="font-medium mb-2" style="color:#1A2422;font-size:0.8125rem;">
                                Konsekuensi penghapusan:
                            </p>
                            <ul class="space-y-1 ml-4 text-xs" style="color:#4B5563;list-style-type:disc;">
                                <li>Siswa di kelompok ini akan kehilangan kelompoknya</li>
                                <li>Siswa akan berstatus "Belum Ditempatkan"</li>
                                <li>Semua riwayat sesi, absensi, dan setoran kelompok ini akan terhapus permanen</li>
                                <li>Data tidak dapat dikembalikan setelah dihapus</li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'secondary','wire:click' => 'closeBulkDeleteConfirm','class' => 'flex-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','wire:click' => 'closeBulkDeleteConfirm','class' => 'flex-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Batal <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'danger','wire:click' => 'bulkDelete','class' => 'flex-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','wire:click' => 'bulkDelete','class' => 'flex-1']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
Hapus Semua <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showFormModal): ?>
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif;">
            <div class="fixed inset-0 pointer-events-none" wire:click="closeFormModal"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-4xl w-full overflow-visible flex flex-col" style="border:1px solid #E5E7EB; max-height:90vh;">

                    
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                        <h3 class="text-xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                            <?php echo e($isEdit ? 'Edit Kelompok Tahfidz' : 'Tambah Kelompok Tahfidz'); ?>

                        </h3>
                        <button wire:click="closeFormModal" class="text-white hover:text-gray-200 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    
                    <div class="overflow-y-auto flex-1 p-6 space-y-6" style="overflow-x: visible;">
                        <div>
                            <h4 class="text-lg font-semibold text-[#1A2422] mb-4 border-b pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">Informasi Kelompok</h4>
                            <div class="space-y-3">
                                
                                <div class="grid grid-cols-12 gap-3 items-center">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Nama Kelompok <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                    <div class="col-span-8">
                                        <input type="text" wire:model="nama" maxlength="100" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] transition-all text-sm" placeholder="Contoh: Kelompok A Dasar Banin">
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
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Jenis Kelompok <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                    <div class="col-span-8">
                                        <select wire:model.live="jenis" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] transition-all text-sm">
                                            <option value="">Pilih Jenis</option>
                                            <option value="Banin">Banin (Putra)</option>
                                            <option value="Banat">Banat (Putri)</option>
                                        </select>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jenis'];
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
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right">Tingkat <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center">:</span>
                                    <div class="col-span-8">
                                        <select wire:model="tingkat" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] transition-all text-sm">
                                            <option value="">Pilih Tingkat</option>
                                            <option value="Dasar">Dasar</option>
                                            <option value="Menengah">Menengah</option>
                                            <option value="Atas">Atas</option>
                                        </select>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tingkat'];
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
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Guru Pembimbing <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8">
                                        <select wire:model="guru_pembimbing_id" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] transition-all text-sm">
                                            <option value="">Pilih Guru Pembimbing</option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->formGuruPembimbings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guru): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <option value="<?php echo e($guru->id); ?>">
                                                    <?php echo e($guru->nama); ?>

                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guru->kelompoksBimbingan->count() > 0): ?>
                                                        (Membimbing: <?php echo e($guru->kelompoksBimbingan->pluck('nama')->join(', ')); ?>)
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </select>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['guru_pembimbing_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs" style="color:#9B3A3A;"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guru_pembimbing_id): ?>
                                            <?php
                                                $selectedGuru = $this->formGuruPembimbings->firstWhere('id', $guru_pembimbing_id);
                                            ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedGuru && $selectedGuru->kelompoksBimbingan->count() > 0): ?>
                                                <div class="mt-2 p-3 rounded-lg" style="background-color:#E0F2FE;border:1px solid #7DD3FC;">
                                                    <div class="flex items-start gap-2">
                                                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" style="color:#0369A1;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <div class="flex-1">
                                                            <p class="text-xs font-semibold mb-1" style="color:#0C4A6E;">
                                                                Guru ini sudah membimbing kelompok lain:
                                                            </p>
                                                            <ul class="space-y-1">
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $selectedGuru->kelompoksBimbingan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                                    <li class="flex items-center gap-1.5">
                                                                        <span class="inline-block w-1 h-1 rounded-full flex-shrink-0" style="background-color:#0369A1;"></span>
                                                                        <span class="text-xs" style="color:#075985;">
                                                                            <strong><?php echo e($kel->nama); ?></strong> (<?php echo e($kel->tingkat); ?> - <?php echo e($kel->jenis); ?>)
                                                                        </span>
                                                                    </li>
                                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEdit && $guru_pembimbing_id != $originalGuruPembimbingId): ?>
                                            <div class="mt-2 p-2.5 rounded-lg" style="background-color:#FEF3C7;border:1px solid #F59E0B;">
                                                <p class="text-xs" style="color:#92400E;">
                                                    <strong>Perhatian:</strong> Anda mengubah guru pembimbing. Perubahan ini akan dicatat dalam riwayat.
                                                </p>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                
                                <div class="grid grid-cols-12 gap-3 items-start">
                                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Tanggal Penempatan <span style="color:#9B3A3A;">*</span></label>
                                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                                    <div class="col-span-8 relative">
                                        <div wire:ignore>
                                            <input
                                                type="text"
                                                id="tanggal_penempatan_guru"
                                                x-data="{
                                                    init() {
                                                        const fp = flatpickr($el, {
                                                            dateFormat: 'Y-m-d',
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
                                                                window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('tanggal_penempatan_guru', dateStr);
                                                            },
                                                            onOpen: function(selectedDates, dateStr, instance) {
                                                                setTimeout(() => {
                                                                    instance.calendarContainer.style.width = 'auto';
                                                                }, 10);
                                                            }
                                                        });
                                                    }
                                                }"
                                                class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all text-sm"
                                                placeholder="Pilih tanggal"
                                                value="<?php echo e($tanggal_penempatan_guru); ?>"
                                            >
                                        </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tanggal_penempatan_guru'];
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
                        <button wire:click="closeFormModal" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #9B3A3A; border: 1.5px solid #9B3A3A;">Batal</button>
                        <button wire:click="saveForm" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #1B5E4F; border: 1.5px solid #1B5E4F;">
                            <span wire:loading.remove wire:target="saveForm">Simpan Data</span>
                            <span wire:loading wire:target="saveForm">Menyimpan...</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showConfirmationDialog): ?>
        <div class="fixed inset-0 z-[60] flex items-center justify-center overflow-y-auto pointer-events-none" style="background-color:rgba(26,36,34,0.6);">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">Konfirmasi Perubahan Guru</h3>
                    <p class="mt-2 text-sm" style="color:#6B7280;">Anda akan mengubah guru pembimbing kelompok ini. Silakan berikan alasan perubahan.</p>
                </div>
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">Alasan Perubahan <span style="color:#9B3A3A;">*</span></label>
                    <textarea wire:model="alasanPerubahanGuru" rows="3" class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2" style="background-color:#FFFFFF;border:1.5px solid #D1D5DB;color:#1A2422;focus:ring-color:#1B5E4F;" placeholder="Contoh: Guru pindah tugas"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" wire:click="cancelConfirmation" class="btn-modal-cancel flex-1">Batal</button>
                    <button type="button" wire:click="confirmUpdate" class="btn-modal-confirm flex-1">Konfirmasi</button>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showDetailModal && $selectedKelompok): ?>
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif;">
            <div class="fixed inset-0 pointer-events-none" wire:click="closeDetailModal"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-4xl w-full p-6 flex flex-col" style="border:1px solid #E5E7EB; max-height:90vh;">

                    
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                        <div>
                            <h3 class="text-xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">Detail Kelompok</h3>
                            <p class="mt-1 text-sm" style="color:#6B7280;"><?php echo e($selectedKelompok->nama); ?></p>
                        </div>
                        <button wire:click="closeDetailModal" class="p-2 rounded-lg hover:bg-gray-100" style="color:#6B7280;">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    
                    <div class="overflow-y-auto flex-1 pr-1 space-y-6">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Nama Kelompok</span>
                                <span class="font-medium" style="color:#1A2422;"><?php echo e($selectedKelompok->nama); ?></span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Jenis</span>
                                <span style="color:#1A2422;"><?php echo e($selectedKelompok->jenis); ?></span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Tingkat</span>
                                <span style="color:#1A2422;"><?php echo e($selectedKelompok->tingkat); ?></span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Guru Pembimbing</span>
                                <span style="color:#1A2422;"><?php echo e($selectedKelompok->guruPembimbing?->nama ?? '-'); ?></span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Tahun Ajaran</span>
                                <span style="color:#1A2422;"><?php echo e($selectedKelompok->tahunAjaran?->label ?? '-'); ?></span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color:#6B7280;">Kapasitas</span>
                                <span style="color:#1A2422;"><?php echo e($jumlahSiswaDetail); ?> / <?php echo e($selectedKelompok->kapasitas_maks); ?> Siswa</span>
                            </div>
                        </div>

                    </div>

                    
                    <div class="mt-6 pt-4 flex justify-end shrink-0" style="border-top:1px solid #E5E7EB;">
                        <button wire:click="closeDetailModal" type="button" class="btn-modal-cancel">Tutup</button>
                    </div>

                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAnggotaModal && $kelompokAnggota): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-xl flex flex-col" style="font-family:'Inter',sans-serif; max-height:85vh;">

                
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                    <div>
                        <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Anggota Kelompok
                        </h3>
                        <p class="text-sm mt-0.5" style="color:#A7D8CC;">
                            <?php echo e($kelompokAnggota->nama); ?> &bull; <?php echo e($kelompokAnggota->tingkat); ?> &bull; <?php echo e($kelompokAnggota->jenis); ?>

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
                        <span class="text-xs font-medium" style="color:#065F46;">Guru: <?php echo e($kelompokAnggota->guruPembimbing?->nama ?? '-'); ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" style="color:#1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-xs font-medium" style="color:#065F46;">Jumlah Anggota: <strong><?php echo e($kelompokAnggota->siswas->count()); ?> siswa</strong></span>
                    </div>
                </div>



                
                <div class="px-6 pt-4 pb-2 flex-shrink-0 flex items-center justify-between gap-4">
                    <div class="relative flex-1">
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

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedAnggotaIds) > 0): ?>
                        <button
                            wire:click="konfirmasiKeluarkanBulk"
                            type="button"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                            style="background-color:#FEF2F2;color:#DC2626;border:1px solid #FECACA;"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Keluarkan (<?php echo e(count($selectedAnggotaIds)); ?>)
                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div class="overflow-y-auto flex-1 px-6 pb-6">
                    <?php
                        $anggotaList = $kelompokAnggota->siswas;
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
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($searchAnggota): ?> Tidak ada siswa yang cocok <?php else: ?> Belum ada anggota di kelompok ini <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <table class="min-w-full mt-2">
                            <thead>
                                <tr style="border-bottom:2px solid #E5E7EB;">
                                    <th class="py-2 pr-4 text-left" style="width: 40px;">
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectAllAnggota"
                                            class="w-4 h-4 rounded transition-colors cursor-pointer"
                                            style="color:#1B5E4F;border-color:#D1D5DB;"
                                        >
                                    </th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">No</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">Nama Siswa</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">NIS</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">Kelas</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">Tgl. Masuk</th>
                                    <th class="py-2 pr-4 text-left text-xs font-semibold" style="color:#6B7280;">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="border-color:#F3F4F6;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $anggotaList->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $siswa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php
                                        $riwayatSiswa    = $riwayatAnggota[$siswa->id] ?? ['tanggal_masuk'=>null];
                                        $tglMasuk        = $riwayatSiswa['tanggal_masuk']  ?? null;
                                    ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-3 pr-4">
                                            <input
                                                type="checkbox"
                                                wire:model.live="selectedAnggotaIds"
                                                value="<?php echo e($siswa->id); ?>"
                                                class="w-4 h-4 rounded transition-colors cursor-pointer"
                                                style="color:#1B5E4F;border-color:#D1D5DB;"
                                            >
                                        </td>
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
                                        <td class="py-3 pr-4 text-sm" style="color:#6B7280;"><?php echo e($siswa->kelas?->nama_lengkap ?? '-'); ?></td>
                                        <td class="py-3 pr-4">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tglMasuk): ?>
                                                <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-full"
                                                      style="background-color:#E8F5F1; color:#1B5E4F;">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    <?php echo e($tglMasuk); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-xs text-gray-400 italic">Tidak tercatat</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="py-3 pr-4">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full"
                                                  style="background-color:#DCFCE7; color:#15803D;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block animate-pulse"></span>
                                                Aktif
                                            </span>
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

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showKonfirmasiKeluarkan && $kelompokAnggota): ?>
        <div class="fixed inset-0 flex items-center justify-center p-4 z-[60] pointer-events-none" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-transparent" wire:click="batalKeluarkan"></div>

            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden" style="font-family:'Inter',sans-serif;">
                
                <div class="flex items-center justify-between px-6 py-4 bg-red-50 border-b border-red-100">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-red-900" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Konfirmasi Keluarkan
                        </h3>
                    </div>
                    <button wire:click="batalKeluarkan" type="button" class="text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                
                <div class="px-6 py-5">
                    <p class="text-gray-800 text-base mb-2">
                        Keluarkan <strong><?php echo e(count($selectedAnggotaIds)); ?> siswa</strong> yang dipilih dari kelompok ini?
                    </p>
                    <p class="text-sm text-gray-500">
                        Siswa akan dikembalikan ke status "Belum Ditempatkan" dan bisa ditempatkan ulang ke kelompok lain.
                    </p>
                </div>

                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                    <button wire:click="batalKeluarkan" type="button" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button wire:click="keluarkanAnggota" type="button" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 transition-colors shadow-sm">
                        <span wire:loading.remove wire:target="keluarkanAnggota">Ya, Keluarkan</span>
                        <span wire:loading wire:target="keluarkanAnggota">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPengaturanModal): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="absolute inset-0" wire:click="tutupPengaturanModal"></div>

            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl flex flex-col" style="font-family:'Inter',sans-serif;">

                
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F; border-bottom:1px solid #047857;">
                    <div>
                        <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Pengaturan Kapasitas
                        </h3>
                        <p class="text-sm mt-0.5" style="color:#A7D8CC;">
                            Atur batas minimal dan maksimal jumlah siswa per kelompok
                        </p>
                    </div>
                    <button wire:click="tutupPengaturanModal" class="text-white hover:text-green-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                
                <div class="p-6">
                    <form wire:submit.prevent="savePengaturanKelompok" class="space-y-5">

                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Min. Siswa</label>
                                <input type="number" wire:model="kapasitas_kelompok_min" class="block w-full rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:border-transparent" style="border:1.5px solid #D1D5DB; color:#1A2422;" min="1" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['kapasitas_kelompok_min'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Maks. Siswa</label>
                                <input type="number" wire:model="kapasitas_kelompok_max" class="block w-full rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:border-transparent" style="border:1.5px solid #D1D5DB; color:#1A2422;" min="1" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['kapasitas_kelompok_max'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-xs text-red-500 mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="flex justify-end gap-3 pt-6 mt-2" style="border-top:1px solid #E5E7EB;">
                            <button wire:click="tutupPengaturanModal" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #9B3A3A; border: 1.5px solid #9B3A3A;">
                                Batal
                            </button>
                            <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #1B5E4F; border: 1.5px solid #1B5E4F;">
                                <span wire:loading.remove wire:target="savePengaturanKelompok">Simpan Pengaturan</span>
                                <span wire:loading wire:target="savePengaturanKelompok">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showTargetHafalanModal): ?>

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="absolute inset-0" wire:click="tutupTargetHafalanModal"></div>

            <div class="relative w-full max-w-5xl rounded-2xl bg-white shadow-xl flex flex-col" style="font-family:'Inter',sans-serif; max-height:90vh;">


                
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                    <div>
                        <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">
                            Target Hafalan Per Tingkat
                        </h3>
                    </div>
                    <button wire:click="tutupTargetHafalanModal" class="text-white hover:text-green-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                
                <div class="p-6 overflow-y-auto flex-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($semuaJuz->isEmpty()): ?>
                        <div class="p-6 rounded-xl text-center" style="background-color:#FEF3C7;border:1.5px solid #F59E0B;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3" style="color:#F59E0B;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <h4 class="font-semibold mb-2" style="color:#92400E;font-family:'Plus Jakarta Sans',sans-serif;">Data Referensi Al-Quran Kosong!</h4>
                            <p class="text-sm mb-4" style="color:#92400E;">Belum ada data referensi 30 Juz. Klik tombol di bawah untuk mengisi data secara otomatis.</p>
                            <button
                                wire:click="runJuzSeeder"
                                type="button"
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors"
                                style="background-color:#C9A227;color:#FFFFFF;border:1.5px solid #C9A227;"
                                onmouseover="this.style.backgroundColor='#B8911F'"
                                onmouseout="this.style.backgroundColor='#C9A227'">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Isi Data 30 Juz Sekarang
                            </button>
                        </div>
                    <?php else: ?>
                        <form wire:submit.prevent="saveTargetHafalan">
                            <?php
                                // Hitung total ayat per tingkat berdasarkan juz yang dipilih
                                $juzMap = $semuaJuz->keyBy('juz_ke');
                                $totalAyatDasar   = collect($target_dasar)->sum(fn($j) => $juzMap[$j]?->jumlah_ayat ?? 0);
                                $totalAyatMenengah = collect($target_menengah)->sum(fn($j) => $juzMap[$j]?->jumlah_ayat ?? 0);
                                $totalAyatAtas    = collect($target_atas)->sum(fn($j) => $juzMap[$j]?->jumlah_ayat ?? 0);
                                $totalJuzSemua    = count($target_dasar) + count($target_menengah) + count($target_atas);
                                $totalAyatSemua   = $totalAyatDasar + $totalAyatMenengah + $totalAyatAtas;
                            ?>

                            
                            <div class="flex items-center gap-6 px-5 py-4 rounded-xl mb-6" style="background-color:#F4F5F4; border:1.5px solid #D1D5DB;">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" style="color:#1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    <span class="text-sm font-bold" style="color:#1A2422;">Total Target: <span style="color:#1B5E4F;"><?php echo e($totalJuzSemua); ?> Juz</span></span>
                                </div>
                                <div class="w-px h-5" style="background-color:#D1D5DB;"></div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" style="color:#1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <span class="text-sm font-bold" style="color:#1A2422;">Total Ayat: <span style="color:#1B5E4F;"><?php echo e(number_format($totalAyatSemua)); ?> ayat</span></span>
                                </div>
                                <div class="w-px h-5" style="background-color:#D1D5DB;"></div>
                                <span class="text-sm font-bold" style="color:#6B7280;">dari 30 Juz (6.236 ayat)</span>
                                <div class="w-px h-5" style="background-color:#D1D5DB;"></div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5" style="color:#1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="text-sm font-bold" style="color:#1A2422;"><span style="color:#1B5E4F;"><?php echo e($totalJuzSemua); ?> Juz</span> dalam 3 Tahun</span>
                                </div>
                            </div>



                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

                                
                                <div class="rounded-xl border-2 overflow-hidden flex flex-col shadow-sm" style="background-color:#FFFFFF; border-color:#1B5E4F;">
                                    <div class="px-5 py-4" style="background: linear-gradient(135deg, #1B5E4F 0%, #2D7A66 100%);">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-bold text-base text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">📘 Tingkat Dasar</h4>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($target_dasar) > 0): ?>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background-color:rgba(255,255,255,0.25); color:#FFFFFF;"><?php echo e(count($target_dasar)); ?> Juz</span>
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background-color:#FFFFFF; color:#1B5E4F;"><?php echo e(number_format($totalAyatDasar)); ?> ayat</span>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-xs inline-block py-0.5" style="color:rgba(255,255,255,0.7);">Belum diatur</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="p-5 flex flex-col gap-3 flex-1" style="background-color:#F4F5F4;">
                                        <select wire:model.live="selected_juz_dasar" class="block w-full rounded-lg border-2 bg-white py-2.5 px-3 text-sm focus:outline-none focus:ring-2 transition-all" style="border-color:#1B5E4F; focus:ring-color:#1B5E4F;">
                                            <option value="">— Pilih Juz —</option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $semuaJuz; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $juz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <?php $isDisabled = in_array((int)$juz->juz_ke, array_merge($target_dasar, $target_menengah, $target_atas)); ?>
                                                <option value="<?php echo e($juz->juz_ke); ?>" <?php if($isDisabled): ?> disabled <?php endif; ?>>Juz <?php echo e($juz->juz_ke); ?><?php echo e($isDisabled ? ' ✓' : ''); ?></option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </select>
                                        <button type="button" wire:click="addJuz('Dasar')" <?php if(empty($selected_juz_dasar)): ?> disabled <?php endif; ?> class="w-full py-2.5 text-sm font-bold rounded-lg text-white transition-all <?php if(empty($selected_juz_dasar)): ?> opacity-50 cursor-not-allowed <?php else: ?> hover:opacity-90 <?php endif; ?>" style="background-color:#1B5E4F;">
                                            + Tambah Juz
                                        </button>
                                        <div class="bg-white rounded-lg border-2 p-3 min-h-[80px]" style="border-color:#D1D5DB;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($target_dasar) > 0): ?>
                                                <div class="flex flex-wrap gap-2">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $target_dasar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $juzItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                        <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold" style="background-color:#1B5E4F; color:#FFFFFF;">
                                                            Juz <?php echo e($juzItem); ?>

                                                            <button type="button" wire:click="removeJuz('Dasar', <?php echo e($juzItem); ?>)" class="ml-0.5 hover:text-red-400 font-bold text-base leading-none transition-colors">&times;</button>
                                                        </span>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <p class="text-center text-xs py-4" style="color:#9CA3AF;">Belum ada Juz dipilih</p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="rounded-xl border-2 overflow-hidden flex flex-col shadow-sm" style="background-color:#FFFFFF; border-color:#5B7A8C;">
                                    <div class="px-5 py-4" style="background: linear-gradient(135deg, #5B7A8C 0%, #6B8A9C 100%);">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-bold text-base text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">📗 Tingkat Menengah</h4>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($target_menengah) > 0): ?>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background-color:rgba(255,255,255,0.25); color:#FFFFFF;"><?php echo e(count($target_menengah)); ?> Juz</span>
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background-color:#FFFFFF; color:#5B7A8C;"><?php echo e(number_format($totalAyatMenengah)); ?> ayat</span>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-xs inline-block py-0.5" style="color:rgba(255,255,255,0.7);">Belum diatur</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="p-5 flex flex-col gap-3 flex-1" style="background-color:#F4F5F4;">
                                        <select wire:model.live="selected_juz_menengah" class="block w-full rounded-lg border-2 bg-white py-2.5 px-3 text-sm focus:outline-none focus:ring-2 transition-all" style="border-color:#5B7A8C; focus:ring-color:#5B7A8C;">
                                            <option value="">— Pilih Juz —</option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $semuaJuz; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $juz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <?php $isDisabled = in_array((int)$juz->juz_ke, array_merge($target_dasar, $target_menengah, $target_atas)); ?>
                                                <option value="<?php echo e($juz->juz_ke); ?>" <?php if($isDisabled): ?> disabled <?php endif; ?>>Juz <?php echo e($juz->juz_ke); ?><?php echo e($isDisabled ? ' ✓' : ''); ?></option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </select>
                                        <button type="button" wire:click="addJuz('Menengah')" <?php if(empty($selected_juz_menengah)): ?> disabled <?php endif; ?> class="w-full py-2.5 text-sm font-bold rounded-lg text-white transition-all <?php if(empty($selected_juz_menengah)): ?> opacity-50 cursor-not-allowed <?php else: ?> hover:opacity-90 <?php endif; ?>" style="background-color:#5B7A8C;">
                                            + Tambah Juz
                                        </button>
                                        <div class="bg-white rounded-lg border-2 p-3 min-h-[80px]" style="border-color:#D1D5DB;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($target_menengah) > 0): ?>
                                                <div class="flex flex-wrap gap-2">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $target_menengah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $juzItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                        <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold" style="background-color:#5B7A8C; color:#FFFFFF;">
                                                            Juz <?php echo e($juzItem); ?>

                                                            <button type="button" wire:click="removeJuz('Menengah', <?php echo e($juzItem); ?>)" class="ml-0.5 hover:text-red-400 font-bold text-base leading-none transition-colors">&times;</button>
                                                        </span>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <p class="text-center text-xs py-4" style="color:#9CA3AF;">Belum ada Juz dipilih</p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="rounded-xl border-2 overflow-hidden flex flex-col shadow-sm" style="background-color:#FFFFFF; border-color:#C9A227;">
                                    <div class="px-5 py-4" style="background: linear-gradient(135deg, #C9A227 0%, #D4B03C 100%);">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-bold text-base text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">📙 Tingkat Atas</h4>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($target_atas) > 0): ?>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background-color:rgba(255,255,255,0.25); color:#FFFFFF;"><?php echo e(count($target_atas)); ?> Juz</span>
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background-color:#FFFFFF; color:#C9A227;"><?php echo e(number_format($totalAyatAtas)); ?> ayat</span>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-xs inline-block py-0.5" style="color:rgba(255,255,255,0.7);">Belum diatur</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="p-5 flex flex-col gap-3 flex-1" style="background-color:#F4F5F4;">
                                        <select wire:model.live="selected_juz_atas" class="block w-full rounded-lg border-2 bg-white py-2.5 px-3 text-sm focus:outline-none focus:ring-2 transition-all" style="border-color:#C9A227; focus:ring-color:#C9A227;">
                                            <option value="">— Pilih Juz —</option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $semuaJuz; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $juz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <?php $isDisabled = in_array((int)$juz->juz_ke, array_merge($target_dasar, $target_menengah, $target_atas)); ?>
                                                <option value="<?php echo e($juz->juz_ke); ?>" <?php if($isDisabled): ?> disabled <?php endif; ?>>Juz <?php echo e($juz->juz_ke); ?><?php echo e($isDisabled ? ' ✓' : ''); ?></option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </select>
                                        <button type="button" wire:click="addJuz('Atas')" <?php if(empty($selected_juz_atas)): ?> disabled <?php endif; ?> class="w-full py-2.5 text-sm font-bold rounded-lg text-white transition-all <?php if(empty($selected_juz_atas)): ?> opacity-50 cursor-not-allowed <?php else: ?> hover:opacity-90 <?php endif; ?>" style="background-color:#C9A227;">
                                            + Tambah Juz
                                        </button>
                                        <div class="bg-white rounded-lg border-2 p-3 min-h-[80px]" style="border-color:#D1D5DB;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($target_atas) > 0): ?>
                                                <div class="flex flex-wrap gap-2">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $target_atas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $juzItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                        <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold" style="background-color:#C9A227; color:#FFFFFF;">
                                                            Juz <?php echo e($juzItem); ?>

                                                            <button type="button" wire:click="removeJuz('Atas', <?php echo e($juzItem); ?>)" class="ml-0.5 hover:text-red-400 font-bold text-base leading-none transition-colors">&times;</button>
                                                        </span>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <p class="text-center text-xs py-4" style="color:#9CA3AF;">Belum ada Juz dipilih</p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                            </div>

                             
                            <div class="flex justify-between items-center gap-3 pt-4" style="border-top:1px solid #E5E7EB;">
                                <button wire:click="runJuzSeeder" type="button" class="px-4 py-2 text-xs font-medium rounded-lg text-white hover:opacity-90" style="background-color:#6B7280;">
                                    <span wire:loading.remove wire:target="runJuzSeeder">Sinkronisasi Database Al-Quran</span>
                                    <span wire:loading wire:target="runJuzSeeder">Memproses...</span>
                                </button>
                                <div class="flex gap-3">
                                    <button wire:click="tutupTargetHafalanModal" type="button" class="px-5 py-2.5 text-sm font-medium rounded-lg" style="background-color:#FFFFFF;color:#374151;border:1.5px solid #D1D5DB;">Batal</button>
                                    <button type="submit" class="px-5 py-2.5 text-sm font-medium rounded-lg text-white" style="background-color:#1B5E4F;">
                                        <span wire:loading.remove wire:target="saveTargetHafalan">Simpan Target Hafalan</span>
                                        <span wire:loading wire:target="saveTargetHafalan">Menyimpan...</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showKomponenPenilaianModal): ?>
        <?php
            $komponenList = [
                'skor_tajwid'     => ['id' => 'cb_tajwid',     'label' => 'Tajwid',     'desc' => 'Ketepatan hukum bacaan & panjang-pendek mad'],
                'skor_kelancaran' => ['id' => 'cb_kelancaran', 'label' => 'Kelancaran', 'desc' => 'Hafalan tanpa terbata-bata atau berhenti lama'],
                'skor_makhraj'    => ['id' => 'cb_makhraj',    'label' => 'Makhraj',    'desc' => 'Ketepatan pelafalan Makharijul Huruf'],
                'skor_fashahah'   => ['id' => 'cb_fashahah',   'label' => 'Fashahah',   'desc' => 'Keindahan nada & kesempurnaan harakat'],
            ];
            $savedSelected = is_array($komponen_penilaian) ? $komponen_penilaian : [];
        ?>

        <style>
            .kp-label { display:flex; align-items:center; gap:1rem; padding:1rem; border-radius:0.75rem; cursor:pointer; user-select:none; transition:all .15s; border:1.5px solid #D1D5DB; background:#fff; }
            .kp-label:has(.kp-cb:checked) { border-color:#1B5E4F; background:#F0FDF4; }
            .kp-track { flex-shrink:0; width:2.75rem; height:1.5rem; border-radius:9999px; background:#D1D5DB; display:flex; align-items:center; padding:0 2px; transition:background .15s; }
            .kp-label:has(.kp-cb:checked) .kp-track { background:#1B5E4F; }
            .kp-thumb { width:1.25rem; height:1.25rem; border-radius:9999px; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,0.2); transform:translateX(0); transition:transform .15s; }
            .kp-label:has(.kp-cb:checked) .kp-thumb { transform:translateX(1.25rem); }
            .kp-name { font-size:.875rem; font-weight:600; color:#4B5563; }
            .kp-label:has(.kp-cb:checked) .kp-name { color:#111827; }
            .kp-badge { display:none; font-size:.75rem; font-weight:600; padding:.125rem .5rem; border-radius:.375rem; background:#D1FAE5; color:#065F46; flex-shrink:0; }
            .kp-label:has(.kp-cb:checked) .kp-badge { display:inline-flex; }
        </style>

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="absolute inset-0" wire:click="tutupKomponenPenilaianModal"></div>

            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl" style="font-family:'Inter',sans-serif;">

                
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl" style="background-color:#1B5E4F; border-bottom:1px solid #047857;">
                    <div>
                        <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">Komponen Penilaian</h3>
                        <p class="text-sm mt-0.5" style="color:#A7D8CC;">Pilih aspek penilaian untuk setoran santri</p>
                    </div>
                    <button wire:click="tutupKomponenPenilaianModal" type="button" class="text-white hover:text-green-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                
                <form wire:submit.prevent="saveKomponenPenilaian" class="p-6">
                    <div class="space-y-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $komponenList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <label class="kp-label" for="<?php echo e($item['id']); ?>">
                                <input class="kp-cb sr-only"
                                       type="checkbox"
                                       id="<?php echo e($item['id']); ?>"
                                       value="<?php echo e($key); ?>"
                                       <?php if(in_array($key, $savedSelected)): echo 'checked'; endif; ?>>
                                <div class="kp-track"><div class="kp-thumb"></div></div>
                                <div class="flex-1">
                                    <span class="kp-name block"><?php echo e($item['label']); ?></span>
                                    <span class="block text-xs mt-0.5" style="color:#9CA3AF;"><?php echo e($item['desc']); ?></span>
                                </div>
                                <span class="kp-badge">Aktif</span>
                            </label>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['komponen_penilaian'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-xs text-red-500 mt-3"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="flex justify-end gap-3 mt-6 pt-4" style="border-top:1px solid #E5E7EB;">
                        <button wire:click="tutupKomponenPenilaianModal" type="button"
                                class="px-5 py-2.5 text-sm font-medium rounded-lg"
                                style="background:#fff;color:#374151;border:1.5px solid #D1D5DB;">
                            Batal
                        </button>
                        <button type="button"
                                @click="
                                    const checked = [...document.querySelectorAll('.kp-cb:checked')].map(cb => cb.value);
                                    if (checked.length < 1) { alert('Pilih minimal satu komponen penilaian.'); return; }
                                    $wire.saveKomponenPenilaian(checked);
                                "
                                class="px-5 py-2.5 text-sm font-medium rounded-lg text-white"
                                style="background-color:#1B5E4F;">
                            <span wire:loading.remove wire:target="saveKomponenPenilaian">Simpan Komponen</span>
                            <span wire:loading wire:target="saveKomponenPenilaian">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo $__env->make('livewire.admin.master-data.kelompok.modal-tempatkan-siswa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</div>



<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/admin/master-data/kelompok/index-kelompok.blade.php ENDPATH**/ ?>