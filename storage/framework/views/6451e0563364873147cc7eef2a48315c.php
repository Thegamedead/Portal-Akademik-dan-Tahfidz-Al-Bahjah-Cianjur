
<div>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth('siswa')->user()?->keterangan_akun === 'Password belum diganti'): ?>
        <div class="mb-4 flex items-start gap-3 rounded-xl px-4 py-3" style="background-color:#FFF8E1;border:1px solid #C9A227;">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="#C9A227" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <p class="text-xs leading-relaxed" style="color:#7B6209;">
                <strong>Wajib ganti password!</strong> Anda masih menggunakan password default.
                Setelah berhasil diganti, Anda akan <strong>otomatis logout</strong> dan diminta login ulang.
            </p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <form wire:submit.prevent="gantiPassword" class="space-y-4">

        
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#374151;">Password Saat Ini</label>
            <div x-data="{ show: false }" class="relative">
                <input
                    :type="show ? 'text' : 'password'"
                    wire:model="passwordLama"
                    class="w-full rounded-lg px-3 py-2 pr-10 text-sm border focus:outline-none transition-colors"
                    style="border-color:<?php echo e($errors->has('passwordLama') ? '#EF4444' : '#D1D5DB'); ?>; background:#F9FAFB; color:#1A2422;"
                    placeholder="Masukkan password saat ini"
                    autocomplete="current-password"
                    id="pw-lama-siswa"
                >
                <button
                    type="button"
                    @click="show = !show"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors"
                    :title="show ? 'Sembunyikan password' : 'Tampilkan password'"
                >
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['passwordLama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-xs" style="color:#EF4444;"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div
            x-data="{
                show: false,
                val: '',
                get strength() {
                    let v = this.val;
                    let score = 0;
                    if (v.length >= 8) score++;
                    if (/[A-Z]/.test(v)) score++;
                    if (/[0-9]/.test(v)) score++;
                    if (/[^A-Za-z0-9]/.test(v)) score++;
                    return score;
                },
                get strengthLabel() {
                    const labels = ['', 'Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'];
                    return labels[this.strength] || '';
                },
                get strengthColor() {
                    const colors = ['', '#EF4444', '#F59E0B', '#3B82F6', '#10B981'];
                    return colors[this.strength] || '';
                },
                get charCount() { return this.val.length; }
            }"
        >
            <label class="block text-xs font-medium mb-1" style="color:#374151;">
                Password Baru
            </label>
            <div class="relative">
                <input
                    :type="show ? 'text' : 'password'"
                    wire:model="passwordBaru"
                    x-model="val"
                    maxlength="20"
                    class="w-full rounded-lg px-3 py-2 pr-10 text-sm border focus:outline-none transition-colors"
                    style="border-color:<?php echo e($errors->has('passwordBaru') ? '#EF4444' : '#D1D5DB'); ?>; background:#F9FAFB; color:#1A2422;"
                    placeholder="8–20 karakter, huruf besar, angka"
                    autocomplete="new-password"
                    id="pw-baru-siswa"
                >
                <button
                    type="button"
                    @click="show = !show"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors"
                    :title="show ? 'Sembunyikan password' : 'Tampilkan password'"
                >
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>

            
            <div x-show="val.length > 0" class="mt-2 space-y-1">
                <div class="flex gap-1">
                    <template x-for="i in 4" :key="i">
                        <div
                            class="h-1 flex-1 rounded-full transition-all duration-300"
                            :style="i <= strength
                                ? 'background-color:' + strengthColor
                                : 'background-color:#E5E7EB'"
                        ></div>
                    </template>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium" :style="'color:' + strengthColor" x-text="strengthLabel"></p>
                    <p class="text-xs" style="color:#9CA3AF;" x-text="charCount + '/20'"></p>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['passwordBaru'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-xs" style="color:#EF4444;"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div
            x-data="{
                show: false,
                konfirmasi: '',
                get baru() {
                    const el = document.getElementById('pw-baru-siswa');
                    return el ? el.value : '';
                },
                get isMatch() {
                    return this.konfirmasi.length > 0 && this.konfirmasi === this.baru;
                },
                get isMismatch() {
                    return this.konfirmasi.length > 0 && this.konfirmasi !== this.baru;
                }
            }"
        >
            <label class="block text-xs font-medium mb-1" style="color:#374151;">Konfirmasi Password Baru</label>
            <div class="relative">
                <input
                    :type="show ? 'text' : 'password'"
                    wire:model="passwordKonfirmasi"
                    x-model="konfirmasi"
                    maxlength="20"
                    class="w-full rounded-lg px-3 py-2 pr-10 text-sm border focus:outline-none transition-colors"
                    :style="isMismatch
                        ? 'border-color:#EF4444; background:#F9FAFB; color:#1A2422;'
                        : isMatch
                            ? 'border-color:#10B981; background:#F9FAFB; color:#1A2422;'
                            : '<?php echo e($errors->has('passwordKonfirmasi') ? 'border-color:#EF4444;' : 'border-color:#D1D5DB;'); ?> background:#F9FAFB; color:#1A2422;'"
                    placeholder="Ulangi password baru"
                    autocomplete="new-password"
                >
                <button
                    type="button"
                    @click="show = !show"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 focus:outline-none transition-colors"
                    :title="show ? 'Sembunyikan password' : 'Tampilkan password'"
                >
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>

            
            <p x-show="isMatch" class="mt-1 text-xs flex items-center gap-1" style="color:#10B981; display:none;">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Password cocok
            </p>
            <p x-show="isMismatch" class="mt-1 text-xs flex items-center gap-1" style="color:#EF4444; display:none;">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Password tidak cocok
            </p>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['passwordKonfirmasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="mt-1 text-xs" style="color:#EF4444;"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="rounded-lg px-3 py-2.5 text-xs space-y-1" style="background-color:#F0FDF4;border:1px solid #BBF7D0;color:#166534;">
            <p class="font-semibold">Aturan password baru:</p>
            <ul class="list-disc list-inside space-y-0.5" style="color:#15803D;">
                <li>Minimal <strong>8 karakter</strong>, maksimal <strong>20 karakter</strong></li>
                <li>Mengandung setidaknya 1 <strong>huruf kapital</strong> (A–Z)</li>
                <li>Mengandung setidaknya 1 <strong>angka</strong> (0–9)</li>
                <li>Tidak boleh sama dengan password lama</li>
            </ul>
        </div>

        
        <button
            type="submit"
            class="w-full py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity"
            style="background-color:#1B5E4F;"
            onmouseover="this.style.opacity='0.9'"
            onmouseout="this.style.opacity='1'"
        >
            <span wire:loading.remove wire:target="gantiPassword">Simpan Password</span>
            <span wire:loading wire:target="gantiPassword" class="flex items-center justify-center gap-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Menyimpan...
            </span>
        </button>
    </form>
</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/siswa/password-form.blade.php ENDPATH**/ ?>