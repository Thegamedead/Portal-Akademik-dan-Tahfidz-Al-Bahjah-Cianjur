<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOpen): ?>
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-transparent pointer-events-none">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden pointer-events-auto" style="border: 1px solid #E5E7EB; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);" @click.outside="$wire.closeModal()">
            <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB;">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-white" style="background-color: #1B5E4F;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold" style="color: #1A2422; font-family: 'Plus Jakarta Sans', sans-serif;">Tukar Akses (Impersonate)</h3>
                </div>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <p class="text-sm" style="color: #6B7280;">Pilih guru yang telah dibuatkan akunnya untuk masuk dan melihat tampilan portal guru secara langsung.</p>
                
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau NIY guru..." class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all" style="border: 1.5px solid #D1D5DB; color: #1A2422;">
                </div>

                <div class="mt-4 max-h-72 overflow-y-auto" style="border: 1.5px solid #E5E7EB; border-radius: 0.75rem;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $gurus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guru): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="flex items-center justify-between p-3.5 border-b border-gray-100 last:border-b-0 hover:bg-emerald-50/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center shrink-0">
                                    <?php echo e(strtoupper(substr($guru->nama, 0, 2))); ?>

                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 leading-tight"><?php echo e($guru->nama); ?></p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-xs text-gray-500 font-mono">NIY: <?php echo e($guru->niy ?? '-'); ?></span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($guru->kelompoksBimbingan->isNotEmpty()): ?>
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold bg-cyan-100 text-cyan-800">
                                                <?php echo e($guru->kelompoksBimbingan->first()->nama); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <button wire:click="impersonate(<?php echo e($guru->id); ?>)" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-lg transition-colors shadow-sm focus:outline-none" style="background-color: #1B5E4F; color: #FFFFFF;" onmouseover="this.style.backgroundColor='#164539'" onmouseout="this.style.backgroundColor='#1B5E4F'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                Masuk
                            </button>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="p-6 text-center">
                            <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty(trim($search))): ?>
                                <p class="text-xs font-semibold text-gray-700">Tidak ada guru akun aktif yang cocok</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">Coba cari dengan nama atau NIY yang berbeda</p>
                            <?php else: ?>
                                <p class="text-xs font-semibold text-gray-700">Belum ada akun guru yang digenerate</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">Silakan generate password guru terlebih dahulu di menu <strong>Manajemen Akun</strong></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="px-6 py-4 flex justify-end" style="border-top: 1px solid #E5E7EB; background-color: #F9FAFB;">
                <button wire:click="closeModal" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #9B3A3A; border: 1.5px solid #9B3A3A;">
                    Batal
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/admin/impersonate-modal.blade.php ENDPATH**/ ?>