

<div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showModal): ?>
    
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-ta-title"
        x-data="{
            tahun: parseInt('<?php echo e($tahun_mulai); ?>'),
            get tahunAkhir()  { return parseInt(this.tahun) + 1 },
            get labelTA()     { return this.tahun + '/' + this.tahunAkhir },
            get tglMulai()    { return '1 Juli ' + this.tahun },
            get tglSelesai()  { return '30 Juni ' + this.tahunAkhir },
            get isValid()     { return this.tahun >= 2000 && this.tahun <= 2100 },
            syncWire()        { if (this.isValid) $wire.tahun_mulai = this.tahun },
        }"
    >
        
        <div
            class="relative w-full max-w-sm rounded-2xl bg-white shadow-2xl pointer-events-auto"
            @click.outside="$wire.tutupModal()"
            style="font-family:'Inter',sans-serif;"
        >
            
            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(255,255,255,0.2);">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" style="color:white;" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 id="modal-ta-title" class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                        <?php echo e($tahunAjaranId ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran'); ?>

                    </h3>
                </div>
                <button
                    type="button"
                    wire:click="tutupModal"
                    class="rounded-lg p-1.5 transition-colors hover:bg-white/20"
                    style="color:white;"
                    aria-label="Tutup"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            
            <div class="px-6 py-5 space-y-5">

                
                <div>
                    <label for="ta-tahun" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                        Awal Tahun Ajaran <span style="color:#9B3A3A;">*</span>
                    </label>

                    
                    <input
                        id="ta-tahun"
                        type="number"
                        min="2000"
                        max="2100"
                        step="1"
                        x-model.number="tahun"
                        @input.debounce.300ms="syncWire()"
                        @change="syncWire()"
                        class="w-full rounded-xl px-4 py-2.5 text-sm font-medium transition-colors focus:outline-none"
                        :style="isValid
                            ? 'background:#F9FAFB;border:1.5px solid <?php echo e($errors->has('tahun_mulai') ? '#9B3A3A' : '#D1D5DB'); ?>;color:#1A2422;'
                            : 'background:#FEF2F2;border:1.5px solid #FCA5A5;color:#991B1B;'"
                        placeholder="Contoh: 2026"
                        wire:ignore
                    >

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tahun_mulai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1.5 flex items-center gap-1 text-xs" style="color:#9B3A3A;" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <?php echo e($message); ?>

                        </p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <div
                    class="rounded-xl p-4 space-y-2 transition-opacity"
                    :class="isValid ? 'opacity-100' : 'opacity-40'"
                    style="background:linear-gradient(135deg,#E0F2F1 0%,#F0FDF9 100%);border:1px solid #B2DFDB;"
                >
                    <p class="text-xs font-semibold uppercase tracking-wider" style="color:#1B5E4F;">Preview Tahun Ajaran</p>

                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" style="color:#1B5E4F;" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-bold" style="color:#1A2422;">
                            TA <span x-text="labelTA"></span>
                        </p>
                    </div>

                    <div class="flex items-center gap-2 text-sm" style="color:#374151;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 opacity-60" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        <span x-text="tglMulai"></span>
                        <span class="opacity-40">—</span>
                        <span x-text="tglSelesai"></span>
                    </div>

                    <div class="flex items-center gap-2 text-xs" style="color:#4B9B8D;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Semester Ganjil &amp; Genap dibuat otomatis
                    </div>
                </div>

                
                <label
                    for="ta-aktif"
                    class="flex items-center gap-3 rounded-xl px-4 py-3 cursor-pointer transition-all"
                    style="border:1.5px solid <?php echo e($langsung_aktif ? '#1B5E4F' : '#E5E7EB'); ?>; background: <?php echo e($langsung_aktif ? '#F0FDF9' : '#F9FAFB'); ?>;"
                >
                    <input
                        id="ta-aktif"
                        type="checkbox"
                        wire:model.live="langsung_aktif"
                        class="h-4 w-4 rounded"
                        style="accent-color:#1B5E4F;"
                    >
                    <div class="flex-1">
                        <p class="text-sm font-medium" style="color:#1A2422;">Langsung aktifkan</p>
                        <p class="text-xs mt-0.5" style="color:#6B7280;">Tahun ajaran lain akan dinonaktifkan otomatis</p>
                    </div>
                </label>

            </div>

            
            <div class="flex items-center justify-end gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB; background-color: #F9FAFB;">

                
                <button
                    type="button"
                    wire:click="tutupModal"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none"
                    style="background-color: #9B3A3A; border: 1.5px solid #9B3A3A;"
                >
                    Batal
                </button>

                
                <button
                    type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none"
                    style="background-color: #1B5E4F; border: 1.5px solid #1B5E4F;"
                >
                    <span class="flex items-center gap-2">
                        <svg wire:loading wire:target="save" class="h-4 w-4 shrink-0 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="white" stroke-width="3" style="opacity:.2"/>
                            <path fill="white" style="opacity:.9" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span wire:loading.remove wire:target="save">
                            <?php echo e($tahunAjaranId ? 'Perbarui' : 'Buat Tahun Ajaran'); ?>

                        </span>
                        <span wire:loading wire:target="save">Menyimpan…</span>
                    </span>
                </button>

            </div>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/admin/master-data/tahun-ajaran/modal-tahun-ajaran.blade.php ENDPATH**/ ?>