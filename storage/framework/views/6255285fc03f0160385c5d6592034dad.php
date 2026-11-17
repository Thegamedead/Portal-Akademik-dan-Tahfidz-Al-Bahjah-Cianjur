
<div>

    
    <div class="mb-8 text-center">

        
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl shadow-sm"
             style="background-color: #1B5E4F;">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-9 w-9 text-white"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="1.8"
                 stroke-linecap="round"
                 stroke-linejoin="round"
                 aria-hidden="true">
                
                <path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
            </svg>
        </div>

        <h1 class="text-xl font-semibold tracking-tight font-heading"
            style="color: #1A2422;">
            Sistem Informasi <br> Program Tahfidz SMP
        </h1>

        <p class="mt-1 text-sm font-medium"
           style="font-family: 'Inter', sans-serif; color: #6B7280;">
            Login Admin
        </p>

        <div class="mx-auto mt-4 h-px w-12 rounded-full"
             style="background-color: #1B5E4F; opacity: 0.35;"
             aria-hidden="true"></div>
    </div>
    


    
    <form
        wire:submit.prevent="authenticate"
        novalidate
        x-data="{
            seconds: <?php if ((object) ('rateLimitSeconds') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('rateLimitSeconds'->value()); ?>')<?php echo e('rateLimitSeconds'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('rateLimitSeconds'); ?>')<?php endif; ?>,
            timer: null,
            init() {
                if (this.seconds > 0) this.start();
                this.$watch('seconds', value => {
                    if (value > 0) this.start();
                });
            },
            start() {
                clearInterval(this.timer);
                this.timer = setInterval(() => {
                    if (this.seconds > 0) {
                        this.seconds--;
                    } else {
                        clearInterval(this.timer);
                    }
                }, 1000);
            }
        }"
    >

        
        <div class="mb-4">
            <label
                for="email"
                class="mb-1.5 block text-sm font-medium"
                style="font-family: 'Inter', sans-serif; color: #1A2422;"
            >
                Email
            </label>
            <input
                id="email"
                type="email"
                wire:model.live="email"
                autocomplete="email"
                autocapitalize="none"
                spellcheck="false"
                required
                aria-required="true"
                aria-describedby="<?php echo e($errors->has('email') ? 'email-error' : ''); ?>"
                class="block w-full rounded-lg px-3.5 py-2.5 text-sm transition focus:outline-none focus:ring-2"
                style="
                    font-family: 'Inter', sans-serif;
                    color: #1A2422;
                    background-color: #FFFFFF;
                    border: 1.5px solid <?php echo e($errors->has('email') ? '#9B3A3A' : '#D1D5DB'); ?>;
                    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
                "
                x-on:focus="$el.style.borderColor = '<?php echo e($errors->has('email') ? '#9B3A3A' : '#1B5E4F'); ?>'; $el.style.boxShadow = '0 0 0 3px rgba(27,94,79,0.15)'"
                x-on:blur="$el.style.borderColor = '<?php echo e($errors->has('email') ? '#9B3A3A' : '#D1D5DB'); ?>'; $el.style.boxShadow = '0 1px 2px rgba(0,0,0,0.04)'"
                placeholder="contoh@email.com"
            >
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p id="email-error" class="mt-1.5 text-xs" style="color: #9B3A3A; font-family: 'Inter', sans-serif;" role="alert">
                    <?php echo e($message); ?> <span x-show="seconds > 0" style="display: none;">Coba lagi dalam <span x-text="seconds"></span> detik.</span>
                </p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        


        
        <div class="mb-5"
             x-data="{ showPwd: false }">

            <label
                for="password"
                class="mb-1.5 block text-sm font-medium"
                style="font-family: 'Inter', sans-serif; color: #1A2422;"
            >
                Password
            </label>

            
            <div class="relative">
                <input
                    id="password"
                    :type="showPwd ? 'text' : 'password'"
                    wire:model.live="password"
                    autocomplete="current-password"
                    required
                    aria-required="true"
                    aria-describedby="<?php echo e($errors->has('password') ? 'password-error' : 'password-hint'); ?>"
                    :aria-label="showPwd ? 'Password (ditampilkan)' : 'Password (disembunyikan)'"
                    class="block w-full rounded-lg py-2.5 pl-3.5 pr-11 text-sm transition focus:outline-none focus:ring-2"
                    style="
                        font-family: 'Inter', sans-serif;
                        color: #1A2422;
                        background-color: #FFFFFF;
                        border: 1.5px solid <?php echo e($errors->has('password') ? '#9B3A3A' : '#D1D5DB'); ?>;
                        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
                    "
                    x-on:focus="$el.style.borderColor = '<?php echo e($errors->has('password') ? '#9B3A3A' : '#1B5E4F'); ?>'; $el.style.boxShadow = '0 0 0 3px rgba(27,94,79,0.15)'"
                    x-on:blur="$el.style.borderColor = '<?php echo e($errors->has('password') ? '#9B3A3A' : '#D1D5DB'); ?>'; $el.style.boxShadow = '0 1px 2px rgba(0,0,0,0.04)'"
                    placeholder="Masukkan password Anda"
                >

                
                <button
                    type="button"
                    @click="showPwd = !showPwd"
                    :aria-label="showPwd ? 'Sembunyikan password' : 'Tampilkan password'"
                    class="absolute inset-y-0 right-0 flex items-center px-3 transition-colors hover:opacity-70 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1"
                    style="color: #6B7280;"
                    tabindex="0"
                >
                    
                    <svg x-show="showPwd"
                         xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         viewBox="0 0 20 20"
                         fill="currentColor"
                         aria-hidden="true">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                        <path fill-rule="evenodd"
                              d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                              clip-rule="evenodd" />
                    </svg>

                    
                    <svg x-show="!showPwd"
                         xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round"
                         stroke-linejoin="round"
                         aria-hidden="true">
                        <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94" />
                        <path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19" />
                        <line x1="1" y1="1" x2="23" y2="23" />
                    </svg>
                </button>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$errors->has('password') && empty($password)): ?>
                <p id="password-hint" class="mt-1.5 text-xs" style="color:#6B7280;font-family:'Inter',sans-serif;">
                    Minimal 8 karakter dengan huruf besar, kecil, angka, dan simbol
                </p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p id="password-error" class="mt-1.5 text-xs" style="color: #9B3A3A; font-family: 'Inter', sans-serif;" role="alert">
                    <?php echo e($message); ?>

                </p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        





        
        <button
            type="submit"
            x-bind:disabled="seconds > 0"
            class="relative w-full rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition-all duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-70"
            style="
                background-color: #1B5E4F;
                font-family: 'Inter', sans-serif;
                letter-spacing: 0.01em;
                box-shadow: 0 1px 3px rgba(27,94,79,0.35), 0 1px 2px rgba(0,0,0,0.06);
            "
            x-on:mouseenter="$el.style.backgroundColor = '#174d40'"
            x-on:mouseleave="$el.style.backgroundColor = '#1B5E4F'"
            wire:loading.attr="disabled"
            wire:target="authenticate"
            aria-label="Masuk ke panel Admin"
        >
            
            <span class="flex items-center justify-center gap-2">
                <svg wire:loading wire:target="authenticate"
                     class="h-4 w-4 animate-spin shrink-0"
                     xmlns="http://www.w3.org/2000/svg"
                     fill="none" viewBox="0 0 24 24"
                     aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="authenticate">Masuk</span>
                <span wire:loading wire:target="authenticate">Memproses...</span>
            </span>
        </button>
        

    </form>
    

</div>
<?php /**PATH C:\laragon\www\Sistem-Informasi-Tahfidz-SMP\resources\views/livewire/auth/login-admin.blade.php ENDPATH**/ ?>