<div>
    {{-- Password Lama --}}
    <div x-data="{ show: false }" class="mb-4">
        <label for="pw-lama" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">Password Lama</label>
        <div class="relative">
            <input id="pw-lama" :type="show ? 'text' : 'password'" wire:model="passwordLama"
                autocomplete="current-password"
                class="w-full rounded-lg px-3.5 py-2.5 pr-11 text-sm focus:outline-none focus:ring-2"
                style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('passwordLama') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                placeholder="Masukkan password lama">
            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3" style="color:#9CA3AF;">
                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
            </button>
        </div>
        @error('passwordLama') <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p> @enderror
    </div>

    {{-- Password Baru --}}
    <div x-data="{ show: false }" class="mb-4">
        <label for="pw-baru" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">Password Baru</label>
        <div class="relative">
            <input id="pw-baru" :type="show ? 'text' : 'password'" wire:model="passwordBaru"
                autocomplete="new-password"
                class="w-full rounded-lg px-3.5 py-2.5 pr-11 text-sm focus:outline-none focus:ring-2"
                style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('passwordBaru') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                placeholder="8 karakter, besar/kecil, angka, simbol"
                maxlength="8">
            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3" style="color:#9CA3AF;">
                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
            </button>
        </div>
        @error('passwordBaru') <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p> @enderror
    </div>

    {{-- Konfirmasi --}}
    <div x-data="{ show: false }" class="mb-5">
        <label for="pw-konfirm" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">Konfirmasi Password Baru</label>
        <div class="relative">
            <input id="pw-konfirm" :type="show ? 'text' : 'password'" wire:model="passwordBaruKonfirmasi"
                autocomplete="new-password"
                class="w-full rounded-lg px-3.5 py-2.5 pr-11 text-sm focus:outline-none focus:ring-2"
                style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('passwordBaruKonfirmasi') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                placeholder="Ulangi password baru"
                maxlength="8">
            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3" style="color:#9CA3AF;">
                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
            </button>
        </div>
        @error('passwordBaruKonfirmasi') <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p> @enderror
    </div>

    {{-- Info kompleksitas --}}
    <p class="mb-4 text-xs rounded-lg px-3 py-2" style="background-color:#F9FAFB;color:#6B7280;border:1px solid #E5E7EB;">
        Password harus terdiri dari <strong>tepat 8 karakter</strong> (kombinasi huruf besar, huruf kecil, angka, dan simbol). Misal: <code>Admin@24!</code>
    </p>

    {{-- Tombol --}}
    <div class="flex justify-end">
        <button
            type="button"
            wire:click="gantiPassword"
            wire:loading.attr="disabled"
            wire:target="gantiPassword"
            class="inline-flex items-center gap-2 rounded-lg px-5 py-2 text-sm font-medium text-white transition-colors focus:outline-none disabled:opacity-70"
            style="background-color:#1B5E4F;"
        >
            <span wire:loading.remove wire:target="gantiPassword">Simpan Password</span>
            <span wire:loading.flex wire:target="gantiPassword" class="items-center gap-2">
                <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan…
            </span>
        </button>
    </div>
</div>
