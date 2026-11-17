<div>
    {{-- Nama --}}
    <div class="mb-4">
        <label for="pf-nama" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
            Nama <span style="color:#9B3A3A;">*</span>
        </label>
        <input
            id="pf-nama"
            type="text"
            wire:model="nama"
            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
            style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('nama') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
            placeholder="Nama lengkap"
        >
        @error('nama') <p class="mt-1.5 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
    </div>

    {{-- Email --}}
    <div class="mb-5">
        <label for="pf-email" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
            Email <span style="color:#9B3A3A;">*</span>
        </label>
        <input
            id="pf-email"
            type="email"
            wire:model="email"
            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
            style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('email') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
            placeholder="email@contoh.com"
            autocomplete="email"
        >
        @error('email') <p class="mt-1.5 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
    </div>

    {{-- Tombol --}}
    <div class="flex justify-end">
        <button
            type="button"
            wire:click="simpanProfil"
            wire:loading.attr="disabled"
            wire:target="simpanProfil"
            class="inline-flex items-center gap-2 rounded-lg px-5 py-2 text-sm font-medium text-white transition-colors focus:outline-none disabled:opacity-70"
            style="background-color:#1B5E4F;"
        >
            <span wire:loading.remove wire:target="simpanProfil">Simpan</span>
            <span wire:loading.flex wire:target="simpanProfil" class="items-center gap-2">
                <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan…
            </span>
        </button>
    </div>
</div>
