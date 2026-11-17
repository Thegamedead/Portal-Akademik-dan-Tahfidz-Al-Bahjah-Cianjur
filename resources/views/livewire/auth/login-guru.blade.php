{{--
    View: livewire/auth/login-guru.blade.php
    Komponen Livewire: App\Livewire\Auth\LoginGuru

    Properti Livewire yang digunakan:
      - $niy            : string
      - $tanggal_lahir  : string — format Y-m-d dari Flatpickr
      - $showCaptcha    : bool — menjadi true setelah captcha_threshold kali gagal
    Method:
      - authenticate() — submit login
--}}
<div>

    {{-- ── Header Card ─────────────────────────────────────────────── --}}
    <div class="mb-8 text-center">

        {{-- Ikon Sekolah — inisial berbentuk shield zamrud --}}
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
                {{-- Book / Al Quran icon --}}
                <path d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
            </svg>
        </div>

        <h1 class="text-xl font-semibold tracking-tight font-heading"
            style="color: #1A2422;">
            Sistem Informasi <br> Program Tahfidz SMP
        </h1>

        <p class="mt-1 text-sm font-medium"
           style="font-family: 'Inter', sans-serif; color: #6B7280;">
            Login Guru
        </p>

        <div class="mx-auto mt-4 h-px w-12 rounded-full"
             style="background-color: #1B5E4F; opacity: 0.35;"
             aria-hidden="true"></div>
    </div>
    {{-- ── /Header Card ─────────────────────────────────────────────── --}}


    {{-- ── Form Login ───────────────────────────────────────────────── --}}
    <form
        wire:submit.prevent="authenticate"
        novalidate
        x-data="{
            seconds: @entangle('rateLimitSeconds'),
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

        {{-- ── Field Username ─────────────────────────────────────────────── --}}
        <div class="mb-4">
            <label
                for="niy"
                class="mb-1.5 block text-sm font-medium"
                style="font-family: 'Inter', sans-serif; color: #1A2422;"
            >
                Username <span style="color:#6B7280;">(NIY)</span>
            </label>
            <input
                id="niy"
                type="text"
                wire:model="niy"
                autocomplete="username"
                autocapitalize="none"
                spellcheck="false"
                inputmode="numeric"
                maxlength="16"
                required
                aria-required="true"
                aria-describedby="{{ $errors->has('niy') ? 'niy-error' : '' }}"
                class="block w-full rounded-lg px-3.5 py-2.5 text-sm transition focus:outline-none focus:ring-2"
                style="
                    font-family: 'Inter', sans-serif;
                    color: #1A2422;
                    background-color: #FFFFFF;
                    border: 1.5px solid {{ $errors->has('niy') ? '#9B3A3A' : '#D1D5DB' }};
                    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
                "
                x-on:focus="$el.style.borderColor = '{{ $errors->has('niy') ? '#9B3A3A' : '#1B5E4F' }}'; $el.style.boxShadow = '0 0 0 3px rgba(27,94,79,0.15)'"
                x-on:blur="$el.style.borderColor = '{{ $errors->has('niy') ? '#9B3A3A' : '#D1D5DB' }}'; $el.style.boxShadow = '0 1px 2px rgba(0,0,0,0.04)'"
                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '')"
                placeholder="16 digit angka NIY"
            >
            <p class="mt-1 text-xs" style="color:#9CA3AF;font-family:'Inter',sans-serif;">Masukkan 16 digit angka NIY Anda</p>
            @error('niy')
                <p id="niy-error" class="mt-1.5 text-xs" style="color: #9B3A3A; font-family: 'Inter', sans-serif;" role="alert">
                    {{ $message }} <span x-show="seconds > 0" style="display: none;">Coba lagi dalam <span x-text="seconds"></span> detik.</span>
                </p>
            @enderror
        </div>
        {{-- ── /Field NIY ────────────────────────────────────────────── --}}


        {{-- ── Field Password ─────────────────────────────────── --}}
        <div class="mb-5" x-data="{ showPassword: false }">
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
                    x-bind:type="showPassword ? 'text' : 'password'"
                    wire:model="password"
                    autocomplete="current-password"
                    maxlength="8"
                    required
                    aria-required="true"
                    aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}"
                    class="block w-full rounded-lg px-3.5 py-2.5 pr-10 text-sm transition focus:outline-none focus:ring-2"
                    style="
                        font-family: 'Inter', sans-serif;
                        color: #1A2422;
                        background-color: #FFFFFF;
                        border: 1.5px solid {{ $errors->has('password') ? '#9B3A3A' : '#D1D5DB' }};
                        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
                    "
                    x-on:focus="$el.style.borderColor = '{{ $errors->has('password') ? '#9B3A3A' : '#1B5E4F' }}'; $el.style.boxShadow = '0 0 0 3px rgba(27,94,79,0.15)'"
                    x-on:blur="$el.style.borderColor = '{{ $errors->has('password') ? '#9B3A3A' : '#D1D5DB' }}'; $el.style.boxShadow = '0 1px 2px rgba(0,0,0,0.04)'"
                    placeholder="8 karakter, besar/kecil, angka, simbol"
                >
                <button 
                    type="button"
                    x-on:click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 flex items-center pr-3"
                    style="color: #6B7280;"
                    tabindex="-1"
                >
                    <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p id="password-error" class="mt-1.5 text-xs" style="color: #9B3A3A; font-family: 'Inter', sans-serif;" role="alert">
                    {{ $message }}
                </p>
            @enderror
        </div>
        {{-- ── /Field Password ─────────────────────────────────── --}}


        {{-- ── Teks Informasi ───────────────────────────────────────── --}}
        <p class="mb-5 -mt-2 text-xs"
           style="font-family: 'Inter', sans-serif; color: #6B7280;">
            Lupa Password ? Hubungi Admin.
        </p>
        {{-- ── /Teks Informasi ─────────────────────────────────────── --}}





        {{-- ── Tombol Masuk ─────────────────────────────────────────── --}}
        <button
            type="submit"
            x-bind:disabled="seconds > 0"
            class="relative w-full rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition-all duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-70 transition-colors"
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
            aria-label="Masuk ke panel Guru"
        >
            {{-- Konten tombol: spinner inline di samping teks --}}
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
        {{-- ── /Tombol Masuk ──────────────────────────────────────────── --}}

    </form>
    {{-- ── /Form Login ─────────────────────────────────────────────── --}}

</div>
