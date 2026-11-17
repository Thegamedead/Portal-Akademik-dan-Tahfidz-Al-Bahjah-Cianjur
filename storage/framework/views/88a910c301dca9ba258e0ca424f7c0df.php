

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showModalRegistrasiMasuk && $siswaIdRegistrasiMasuk): ?>
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-registrasi-masuk-title"
        style="pointer-events: none;"
    >
        
        <div
            class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl"
            style="font-family:'Inter',sans-serif; pointer-events: auto;"
        >
            
            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                <h3 id="modal-registrasi-masuk-title" class="text-base font-semibold"
                    style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                    Registrasi Masuk Siswa
                </h3>
                <button
                    type="button"
                    wire:click="tutupModalRegistrasiMasuk"
                    class="rounded-lg p-1.5 transition-colors hover:bg-white/20 focus:outline-none focus:ring-2" style="color:white;"
                    aria-label="Tutup modal"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            
            <div class="px-6 py-4">
                
                <?php
                    $siswa = \App\Models\Siswa::find($siswaIdRegistrasiMasuk);
                ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($siswa): ?>
                    <div class="mb-4 p-3 rounded-lg" style="background-color:#F4F5F4;border-left:3px solid #1B5E4F;">
                        <div class="font-semibold text-sm" style="color:#1A2422;">
                            <?php echo e($siswa->nama); ?>

                        </div>
                        <div class="text-xs mt-0.5" style="color:#6B7280;">
                            NIS: <?php echo e($siswa->nis); ?>

                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <div class="space-y-4">
                    
                    <div>
                        <label for="status-masuk" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                            Status Masuk <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                        </label>
                        <select
                            id="status-masuk"
                            wire:model.live="statusMasuk"
                            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                            style="background-color:#FFFFFF;border:1.5px solid <?php echo e($errors->has('statusMasuk') ? '#9B3A3A' : '#D1D5DB'); ?>;color:#1A2422;"
                            aria-required="true"
                        >
                            <option value="">-- Pilih Status Masuk --</option>
                            <option value="Baru">Baru</option>
                            <option value="Pindahan">Pindahan</option>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['statusMasuk'];
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
                        <label for="tanggal-masuk" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                            Tanggal Masuk <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="tanggal-masuk"
                            type="date"
                            wire:model="tanggalMasuk"
                            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                            style="background-color:#FFFFFF;border:1.5px solid <?php echo e($errors->has('tanggalMasuk') ? '#9B3A3A' : '#D1D5DB'); ?>;color:#1A2422;"
                            aria-required="true"
                        >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tanggalMasuk'];
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

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statusMasuk === 'Pindahan'): ?>
                        <div x-show="true" x-transition>
                            <label for="asal-sekolah" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                                Asal Sekolah <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                            </label>
                            <input
                                id="asal-sekolah"
                                type="text"
                                wire:model="asalSekolah"
                                class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                                style="background-color:#FFFFFF;border:1.5px solid <?php echo e($errors->has('asalSekolah') ? '#9B3A3A' : '#D1D5DB'); ?>;color:#1A2422;"
                                placeholder="Nama sekolah asal"
                                aria-required="true"
                            >
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['asalSekolah'];
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

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statusMasuk === 'Pindahan'): ?>
                        <div x-show="true" x-transition>
                            <label for="alamat-sekolah-asal" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                                Alamat Sekolah Asal <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                            </label>
                            <textarea
                                id="alamat-sekolah-asal"
                                wire:model="alamatSekolahAsal"
                                rows="3"
                                class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                                style="background-color:#FFFFFF;border:1.5px solid <?php echo e($errors->has('alamatSekolahAsal') ? '#9B3A3A' : '#D1D5DB'); ?>;color:#1A2422;resize:vertical;"
                                placeholder="Alamat lengkap sekolah asal"
                                aria-required="true"
                            ></textarea>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['alamatSekolahAsal'];
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
                </div>
            </div>

            
            <div class="flex gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB;">
                <button
                    type="button"
                    wire:click="tutupModalRegistrasiMasuk"
                    class="btn-modal-cancel flex-1"
                >
                    Batal
                </button>
                <button
                    type="button"
                    wire:click="submitRegistrasiMasuk"
                    class="btn-modal-confirm flex-1"
                >
                    Submit
                </button>
            </div>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/admin/master-data/siswa/partials/modal-registrasi-masuk.blade.php ENDPATH**/ ?>