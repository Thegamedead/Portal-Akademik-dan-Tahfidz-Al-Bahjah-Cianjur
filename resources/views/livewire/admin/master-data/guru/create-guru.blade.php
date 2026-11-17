{{--
    View: livewire/admin/master-data/guru/create-guru.blade.php
    Komponen Livewire: App\Livewire\Admin\MasterData\Guru\CreateGuru

    Full page form untuk registrasi guru baru (Requirements 4.1-4.7)
--}}
<div class="space-y-6" style="font-family:'Inter',sans-serif;">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- HEADER --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Registrasi Guru Baru
                </h2>
                <p class="mt-1 text-sm" style="color:#6B7280;">
                    Lengkapi formulir untuk mendaftarkan guru baru
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

    <form wire:submit.prevent="save">
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- DATA IDENTITAS GURU --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}

        <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;">
            <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
                Data Identitas Guru
            </h3>

            {{-- NIY --}}
            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">
                    NIY <span style="color:#9B3A3A;">*</span>
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <input
                        type="text"
                        wire:model="niy"
                        placeholder="Nomor Induk Yayasan"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                    >
                    @error('niy')
                        <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                    @enderror
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
                        placeholder="Nama lengkap guru"
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
                    <select
                        wire:model="jenis_kelamin"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                    >
                        <option value="">Pilih</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
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
        {{-- PENDIDIKAN & JABATAN --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}

        <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;">
            <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
                Pendidikan & Jabatan
            </h3>

            {{-- Pendidikan Terakhir --}}
            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">
                    Pendidikan Terakhir <span style="color:#9B3A3A;">*</span>
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <select
                        wire:model.live="pendidikan_terakhir"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                    >
                        <option value="">Pilih</option>
                        <option value="SD">SD</option>
                        <option value="SMP">SMP</option>
                        <option value="SMA">SMA</option>
                        <option value="D1">D1</option>
                        <option value="D2">D2</option>
                        <option value="D3">D3</option>
                        <option value="D4">D4</option>
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                        <option value="S3">S3</option>
                    </select>
                    @error('pendidikan_terakhir')
                        <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Program Studi --}}
            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">
                    Program Studi
                    @if(in_array($pendidikan_terakhir, ['D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3']))
                        <span style="color:#9B3A3A;">*</span>
                    @else
                        <span style="color:#6B7280;">(Opsional)</span>
                    @endif
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <input
                        type="text"
                        wire:model="program_studi"
                        placeholder="Jurusan/program studi"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                    >
                    @error('program_studi')
                        <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Jabatan --}}
            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">
                    Jabatan <span style="color:#9B3A3A;">*</span>
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <select
                        wire:model="jabatan"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                    >
                        <option value="">Pilih</option>
                        <option value="Guru Pembimbing Tahfidz">Guru Pembimbing Tahfidz</option>
                        <option value="Guru Mapel">Guru Mapel</option>
                        <option value="Kepala Sekolah">Kepala Sekolah</option>
                        <option value="Tendik">Tendik</option>
                    </select>
                    @error('jabatan')
                        <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- KONTAK --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}

        <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;">
            <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
                Kontak
            </h3>

            {{-- No Telepon --}}
            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">
                    No. Telepon <span style="color:#9B3A3A;">*</span>
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <input
                        type="text"
                        wire:model="no_telepon"
                        placeholder="08xxxxxxxxxx"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                    >
                    @error('no_telepon')
                        <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Email --}}
            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">
                    Email (Opsional)
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <input
                        type="email"
                        wire:model="email"
                        placeholder="email@example.com"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border:1.5px solid #D1D5DB;color:#1A2422;"
                    >
                    @error('email')
                        <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- FORM ACTIONS --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}

        <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
            <div class="flex flex-col sm:flex-row gap-3 justify-end">
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
                    <span wire:loading.remove wire:target="save">Simpan Data Guru</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </form>

</div>
