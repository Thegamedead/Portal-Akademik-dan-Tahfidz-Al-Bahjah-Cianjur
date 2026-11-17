

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showModalRegistrasiKeluar): ?>
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-registrasi-keluar-title"
    >
        
        <div
            class="absolute inset-0 bg-transparent"
            wire:click="tutupModalRegistrasiKeluar"
            aria-hidden="true"
        ></div>

        
        <div
            class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl"
            style="font-family:'Inter',sans-serif;"
        >
            
            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                <div>
                    <h3 id="modal-registrasi-keluar-title" class="text-base font-semibold"
                        style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                        Registrasi Keluar Siswa
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedIds) > 1): ?>
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" style="background-color:#1B5E4F;color:#FFFFFF;">
                                <?php echo e(count($selectedIds)); ?> siswa
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h3>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedIds) > 1): ?>
                        <p class="mt-1 text-xs" style="color:#6B7280;">Semua siswa yang dipilih akan diproses keluar dengan data yang sama</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <button
                    type="button"
                    wire:click="tutupModalRegistrasiKeluar"
                    class="rounded-lg p-1.5 transition-colors hover:bg-white/20 focus:outline-none focus:ring-2" style="color:white;"
                    aria-label="Tutup modal"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            
            <div class="px-6 py-4">
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($selectedIds) === 1): ?>
                    <?php
                        $siswaRegistrasiKeluar = App\Models\Siswa::find($selectedIds[0]);
                    ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswaRegistrasiKeluar): ?>
                        <div class="mb-4 p-3 rounded-lg" style="background-color:#F4F5F4;border-left:3px solid #1B5E4F;">
                            <div class="font-semibold text-sm" style="color:#1A2422;">
                                <?php echo e($siswaRegistrasiKeluar->nama); ?>

                            </div>
                            <div class="text-xs mt-0.5" style="color:#6B7280;">
                                NIS: <?php echo e($siswaRegistrasiKeluar->nis); ?>

                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php else: ?>
                    
                    <div class="mb-4 p-3 rounded-lg" style="background-color:#FFF7ED;border-left:3px solid #C2410C;">
                        <div class="font-semibold text-sm mb-2" style="color:#1A2422;">
                            <?php echo e(count($selectedIds)); ?> siswa akan diproses keluar:
                        </div>
                        <div class="max-h-24 overflow-y-auto space-y-1">
                            <?php
                                $selectedSiswasList = App\Models\Siswa::whereIn('id', $selectedIds)->get();
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $selectedSiswasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="text-xs flex items-center gap-2" style="color:#6B7280;">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span><?php echo e($s->nama); ?> (<?php echo e($s->nis); ?>)</span>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="space-y-4">
                    
                    <div>
                        <label for="jenis-keluar" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                            Jenis Keluar <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                        </label>
                        <select
                            id="jenis-keluar"
                            wire:model.live="jenisKeluar"
                            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                            style="background-color:#FFFFFF;border:1.5px solid <?php echo e($errors->has('jenisKeluar') ? '#9B3A3A' : '#D1D5DB'); ?>;color:#1A2422;"
                            aria-required="true"
                        >
                            <option value="">-- Pilih Jenis Keluar --</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Enums\JenisKeluar::options(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['jenisKeluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div>
                        <label for="tanggal-keluar" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                            Tanggal Keluar <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="tanggal-keluar"
                            type="date"
                            wire:model="tanggalKeluar"
                            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                            style="background-color:#FFFFFF;border:1.5px solid <?php echo e($errors->has('tanggalKeluar') ? '#9B3A3A' : '#D1D5DB'); ?>;color:#1A2422;"
                            aria-required="true"
                        >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tanggalKeluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($jenisKeluar === 'Pindah Sekolah'): ?>
                        <div x-show="true" x-transition>
                            <label for="tujuan-sekolah" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                                Tujuan Sekolah <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                            </label>
                            <input
                                id="tujuan-sekolah"
                                type="text"
                                wire:model="tujuanSekolah"
                                class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                                style="background-color:#FFFFFF;border:1.5px solid <?php echo e($errors->has('tujuanSekolah') ? '#9B3A3A' : '#D1D5DB'); ?>;color:#1A2422;"
                                placeholder="Nama sekolah tujuan"
                                aria-required="true"
                            >
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tujuanSekolah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div>
                        <label for="keterangan-keluar" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                            Keterangan
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($jenisKeluar, ['Meninggal', 'Keluar'])): ?>
                                <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </label>
                        <textarea
                            id="keterangan-keluar"
                            wire:model="keteranganKeluar"
                            rows="3"
                            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                            style="background-color:#FFFFFF;border:1.5px solid <?php echo e($errors->has('keteranganKeluar') ? '#9B3A3A' : '#D1D5DB'); ?>;color:#1A2422;resize:vertical;"
                            placeholder="Catatan tambahan tentang keberangkatan siswa"
                            <?php if(in_array($jenisKeluar, ['Meninggal', 'Keluar'])): ?>
                                aria-required="true"
                            <?php endif; ?>
                        ></textarea>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['keteranganKeluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="flex gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB;">
                <button
                    type="button"
                    wire:click="tutupModalRegistrasiKeluar"
                    class="btn-modal-cancel flex-1"
                >
                    Batal
                </button>
                <button
                    type="button"
                    wire:click="submitRegistrasiKeluar"
                    class="btn-modal-confirm flex-1"
                >
                    Submit
                </button>
            </div>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/admin/master-data/siswa/partials/modal-registrasi-keluar.blade.php ENDPATH**/ ?>