{{--
    View: livewire/admin/master-data/siswa/edit-siswa.blade.php
    Komponen Livewire: App\Livewire\Admin\MasterData\Siswa\EditSiswa

    Full page form untuk Edit Data Siswa dengan multi-entry orang tua/wali (Requirements 2.1-2.7, 3.4)
--}}
<div class="space-y-6" style="font-family:'Inter',sans-serif;">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- HEADER --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Edit Data Siswa
                </h2>
                <p class="mt-1 text-sm" style="color:#6B7280;">
                    Lengkapi formulir untuk perbarui informasi siswa dan data orang tua/wali
                </p>
            </div>
            <button
                wire:click="cancel"
                type="button"
                class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
            >
                Kembali
            </button>
        </div>
    </div>

    <form wire:submit.prevent="update">
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- DATA IDENTITAS SISWA --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}

        <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;">
            <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
                Data Identitas Siswa
            </h3>

            {{-- NIS --}}
            <div class="grid grid-cols-12 gap-3 items-start">
                <label class="col-span-3 text-sm font-medium text-right pt-2" style="color:#6B7280;">
                    NIS <span style="color:#9B3A3A;">*</span>
                </label>
                <span class="col-span-1 text-sm font-medium text-center pt-2" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <input
                        type="text"
                        wire:model.live="nis"
                        maxlength="9"
                        inputmode="numeric"
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 9)"
                        placeholder="9 digit angka"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                    >
                    <div class="flex items-center justify-between mt-1">
                        @error('nis')
                            <p class="text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                        @else
                            <span></span>
                        @enderror
                        <span class="text-xs text-gray-400">{{ strlen($nis ?? '') }}/9</span>
                    </div>
                </div>
            </div>

            {{-- NISN --}}
            <div class="grid grid-cols-12 gap-3 items-start">
                <label class="col-span-3 text-sm font-medium text-right pt-2" style="color:#6B7280;">
                    NISN (Opsional)
                </label>
                <span class="col-span-1 text-sm font-medium text-center pt-2" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <input
                        type="text"
                        wire:model.live="nisn"
                        maxlength="9"
                        inputmode="numeric"
                        x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 9)"
                        placeholder="9 digit angka"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                    >
                    <div class="flex items-center justify-between mt-1">
                        @error('nisn')
                            <p class="text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                        @else
                            <span></span>
                        @enderror
                        <span class="text-xs text-gray-400">{{ strlen($nisn ?? '') }}/9</span>
                    </div>
                </div>
            </div>

            {{-- Nama Lengkap --}}
            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">
                    Nama Lengkap <span style="color:#9B3A3A;">*</span>
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <input
                        type="text"
                        wire:model="nama"
                        placeholder="Nama lengkap siswa"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                    >
                    @error('nama')
                        <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Jenis Kelamin --}}
            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">
                    Jenis Kelamin <span style="color:#9B3A3A;">*</span>
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model.live="jenis_kelamin" value="Laki-laki" class="accent-[#1B5E4F]">
                            <span class="text-sm" style="color:#1A2422;">L</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model.live="jenis_kelamin" value="Perempuan" class="accent-[#1B5E4F]">
                            <span class="text-sm" style="color:#1A2422;">P</span>
                        </label>
                    </div>
                    @error('jenis_kelamin')
                        <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tempat Lahir --}}
            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">
                    Tempat Lahir <span style="color:#9B3A3A;">*</span>
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <input
                        type="text"
                        wire:model="tempat_lahir"
                        placeholder="Kota/Kabupaten"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                    >
                    @error('tempat_lahir')
                        <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tanggal Lahir --}}
            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">
                    Tanggal Lahir <span style="color:#9B3A3A;">*</span>
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <input
                        type="date"
                        wire:model="tanggal_lahir"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                    >
                    @error('tanggal_lahir')
                        <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- DATA ORANG TUA --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}

        <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;">
            <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
                Data Orang Tua
            </h3>

            {{-- Nama Ayah --}}
            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">
                    Nama Ayah (Opsional)
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <input
                        type="text"
                        wire:model="nama_ayah"
                        placeholder="Nama lengkap ayah"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                    >
                    @error('nama_ayah')
                        <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Nama Ibu --}}
            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">
                    Nama Ibu (Opsional)
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <input
                        type="text"
                        wire:model="nama_ibu"
                        placeholder="Nama lengkap ibu"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                    >
                    @error('nama_ibu')
                        <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- KELAS AKADEMIK --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}

        <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;">
            <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
                Kelas Akademik
            </h3>

            {{-- Kelas Akademik --}}
            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">
                    Kelas Akademik
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <select
                        wire:model="kelas_id"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                    >
                        <option value="">Belum Ditentukan</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->nama_lengkap }}</option>
                        @endforeach
                    </select>
                    @error('kelas_id')
                        <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- FORM ACTIONS --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}

        <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
            <div class="flex justify-end gap-3">
                <button
                    wire:click="cancel"
                    type="button"
                    class="px-6 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                    style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-6 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                    style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;"
                >
                    Simpan
                </button>
            </div>
        </div>
    </form>

</div>

