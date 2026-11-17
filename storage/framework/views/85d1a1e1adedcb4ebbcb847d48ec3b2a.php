<form wire:submit="save" class="space-y-4">
    
    <div>
        <label class="block text-sm font-medium mb-1.5" style="color: #374151;">
            Nama Lengkap <span style="color: #9B3A3A;">*</span>
        </label>
        <input
            type="text"
            wire:model="nama"
            class="w-full px-3 py-2 rounded-lg text-sm transition-colors focus:outline-none focus:ring-2"
            style="border: 1px solid #E5E7EB; color: #1A2422;"
            onfocus="this.style.borderColor='#1B5E4F'; this.style.boxShadow='0 0 0 3px rgba(27,94,79,0.1)';"
            onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none';"
        />
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p class="mt-1 text-xs" style="color: #9B3A3A;"><?php echo e($message); ?></p>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div>
        <label class="block text-sm font-medium mb-1.5" style="color: #374151;">
            Email <span style="color: #9B3A3A;">*</span>
        </label>
        <input
            type="email"
            wire:model="email"
            class="w-full px-3 py-2 rounded-lg text-sm transition-colors focus:outline-none focus:ring-2"
            style="border: 1px solid #E5E7EB; color: #1A2422;"
            onfocus="this.style.borderColor='#1B5E4F'; this.style.boxShadow='0 0 0 3px rgba(27,94,79,0.1)';"
            onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none';"
        />
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p class="mt-1 text-xs" style="color: #9B3A3A;"><?php echo e($message); ?></p>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div>
        <label class="block text-sm font-medium mb-1.5" style="color: #374151;">
            NIY
        </label>
        <input
            type="text"
            value="<?php echo e($niy); ?>"
            disabled
            class="w-full px-3 py-2 rounded-lg text-sm"
            style="border: 1px solid #E5E7EB; background-color: #F9FAFB; color: #6B7280; cursor: not-allowed;"
        />
    </div>

    
    <div class="flex gap-2 justify-end pt-2">
        <button
            type="button"
            @click="$dispatch('close-profil-modal')"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
            style="border: 1px solid #E5E7EB; color: #374151;"
            onmouseover="this.style.backgroundColor='#F4F5F4';"
            onmouseout="this.style.backgroundColor='transparent';"
        >
            Batal
        </button>
        <button
            type="submit"
            class="px-4 py-2 rounded-lg text-sm font-medium text-white transition-colors"
            style="background-color: #1B5E4F;"
            onmouseover="this.style.backgroundColor='#164539';"
            onmouseout="this.style.backgroundColor='#1B5E4F';"
        >
            Simpan
        </button>
    </div>
</form>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/guru/profil-form.blade.php ENDPATH**/ ?>