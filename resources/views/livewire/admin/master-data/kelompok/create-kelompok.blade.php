{{--
    View: livewire/admin/master-data/kelompok/create-kelompok.blade.php
    Komponen Livewire: App\Livewire\Admin\MasterData\Kelompok\CreateKelompok

    Full-page form untuk create Kelompok Tahfidz (>6 fields)
    Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 10.1, 10.7
--}}
<div class="space-y-6">

    {{-- Header --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h2 class="text-xl font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Tambah Kelompok Tahfidz
        </h2>
        <p class="mt-1 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Buat kelompok tahfidz baru. Field bertanda <span style="color:#9B3A3A;">*</span> wajib diisi.
        </p>
    </div>

    {{-- Form --}}
    <form wire:submit.prevent="save">
        <div class="rounded-2xl bg-white shadow-sm p-6 space-y-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">

            {{-- ══════════════════════════════════════════════════════════════════ --}}
            {{-- DATA KELOMPOK --}}
            {{-- ══════════════════════════════════════════════════════════════════ --}}

            <div>
                <h3 class="text-base font-semibold mb-4" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;padding-bottom:8px;">
                    Informasi Kelompok
                </h3>
                <div class="space-y-3">

                    {{-- Nama Kelompok --}}
                    <div class="grid grid-cols-12 gap-3 items-center">
                        <label for="nama" class="col-span-3 text-sm font-medium text-right" style="color:#1A2422;">
                            Nama Kelompok <span style="color:#9B3A3A;">*</span>
                        </label>
                        <span class="col-span-1 text-sm font-medium text-center" style="color:#1A2422;">:</span>
                        <div class="col-span-8">
                            <input
                                id="nama"
                                type="text"
                                wire:model="nama"
                                maxlength="100"
                                class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-zamrud"
                                style="border:1.5px solid {{ $errors->has('nama') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                                placeholder="Contoh: Kelompok A Dasar Banin"
                            >
                            @error('nama')
                                <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Jenis Kelompok --}}
                    <div class="grid grid-cols-12 gap-3 items-center">
                        <label for="jenis" class="col-span-3 text-sm font-medium text-right" style="color:#1A2422;">
                            Jenis Kelompok <span style="color:#9B3A3A;">*</span>
                        </label>
                        <span class="col-span-1 text-sm font-medium text-center" style="color:#1A2422;">:</span>
                        <div class="col-span-8">
                            <select
                                id="jenis"
                                wire:model="jenis"
                                class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-zamrud"
                                style="border:1.5px solid {{ $errors->has('jenis') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                            >
                                <option value="">Pilih Jenis</option>
                                <option value="Banin">Banin (Putra)</option>
                                <option value="Banat">Banat (Putri)</option>
                            </select>
                            @error('jenis')
                                <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tingkat --}}
                    <div class="grid grid-cols-12 gap-3 items-center">
                        <label for="tingkat" class="col-span-3 text-sm font-medium text-right" style="color:#1A2422;">
                            Tingkat <span style="color:#9B3A3A;">*</span>
                        </label>
                        <span class="col-span-1 text-sm font-medium text-center" style="color:#1A2422;">:</span>
                        <div class="col-span-8">
                            <select
                                id="tingkat"
                                wire:model="tingkat"
                                class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-zamrud"
                                style="border:1.5px solid {{ $errors->has('tingkat') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                            >
                                <option value="">Pilih Tingkat</option>
                                <option value="Dasar">Dasar</option>
                                <option value="Menengah">Menengah</option>
                                <option value="Atas">Atas</option>
                            </select>
                            @error('tingkat')
                                <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Guru Pembimbing --}}
                    <div class="grid grid-cols-12 gap-3 items-center">
                        <label for="guru_pembimbing_id" class="col-span-3 text-sm font-medium text-right" style="color:#1A2422;">
                            Guru Pembimbing Tahfidz <span style="color:#9B3A3A;">*</span>
                        </label>
                        <span class="col-span-1 text-sm font-medium text-center" style="color:#1A2422;">:</span>
                        <div class="col-span-8">
                            <select
                                id="guru_pembimbing_id"
                                wire:model="guru_pembimbing_id"
                                class="w-full rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-zamrud"
                                style="border:1.5px solid {{ $errors->has('guru_pembimbing_id') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                            >
                                <option value="">Pilih Guru Pembimbing</option>
                                @foreach($pembimbingTahfidzList as $guru)
                                    <option value="{{ $guru->id }}">
                                        {{ $guru->nama }} ({{ $guru->niy }})
                                    </option>
                                @endforeach
                            </select>
                            @error('guru_pembimbing_id')
                                <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs" style="color:#6B7280;">
                                Hanya menampilkan guru dengan jabatan "Guru Pembimbing Tahfidz"
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════════════ --}}
            {{-- ACTION BUTTONS --}}
            {{-- ══════════════════════════════════════════════════════════════════ --}}

            <div class="flex justify-end gap-3 pt-4" style="border-top:1px solid #E5E7EB;">
                <button
                    type="button"
                    wire:click="cancel"
                    class="px-5 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                    style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;font-family:'Inter',sans-serif;"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-5 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                    style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;font-family:'Inter',sans-serif;"
                >
                    Simpan Kelompok
                </button>
            </div>

        </div>
    </form>

</div>
