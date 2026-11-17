<div class="container mx-auto px-4 py-6">
    <!-- Card Informasi dengan Tombol Kembali -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start gap-4">
            <div class="shrink-0">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: #E8F5F1;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" style="color: #1B5E4F;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-lg font-semibold mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif; color: #1A2422;">
                    Profil Sekolah
                </h2>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Kelola informasi identitas, alamat, kontak, dan data legal sekolah. Data ini digunakan untuk keperluan administrasi dan pencetakan dokumen resmi.
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form wire:submit.prevent="save" class="bg-white rounded-lg shadow-md p-6">

        {{-- ── IDENTITAS SEKOLAH ───────────────────────────────────────── --}}
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-tinta mb-4 border-b pb-2">Identitas Sekolah</h2>
            <div class="space-y-3">

                {{-- Nama Sekolah --}}
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Nama Sekolah <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="nama_sekolah"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors @error('nama_sekolah') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                            placeholder="Contoh: SMP Islam Terpadu Al-Hijrah"
                        >
                        @error('nama_sekolah')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- NPSN --}}
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">NPSN <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="npsn"
                            maxlength="8"
                            inputmode="numeric"
                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 8)"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors @error('npsn') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                            placeholder="12345678"
                        >
                        <div class="flex items-center justify-between mt-1">
                            @error('npsn')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @else
                                <span></span>
                            @enderror
                            <span class="text-xs text-gray-400">{{ strlen($npsn ?? '') }}/8</span>
                        </div>
                    </div>
                </div>

                {{-- Status Sekolah --}}
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Status Sekolah <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <select
                            wire:model.live="status_sekolah"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors @error('status_sekolah') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                        >
                            <option value="">Pilih Status</option>
                            <option value="Negeri">Negeri</option>
                            <option value="Swasta">Swasta</option>
                        </select>
                        @error('status_sekolah')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Akreditasi --}}
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Akreditasi <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <select
                            wire:model.live="akreditasi"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors @error('akreditasi') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                        >
                            <option value="">Pilih Akreditasi</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="Belum Terakreditasi">Belum Terakreditasi</option>
                        </select>
                        @error('akreditasi')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- ── ALAMAT ──────────────────────────────────────────────────── --}}
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-tinta mb-4 border-b pb-2">Alamat</h2>
            <div class="space-y-3">

                {{-- Jalan --}}
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Jalan <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="alamat_jalan"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors @error('alamat_jalan') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                            placeholder="Jl. Raya Pendidikan No. 123"
                        >
                        @error('alamat_jalan')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Dusun --}}
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Dusun</label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="alamat_dusun"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors"
                        >
                    </div>
                </div>

                {{-- RT / RW --}}
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">RT / RW</label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8 grid grid-cols-2 gap-2">
                        <div>
                            <input
                                type="text"
                                wire:model.live="alamat_rt"
                                maxlength="3"
                                inputmode="numeric"
                                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 3)"
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors @error('alamat_rt') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                                placeholder="001"
                            >
                            <div class="flex items-center justify-between mt-1">
                                @error('alamat_rt')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @else
                                    <span></span>
                                @enderror
                                <span class="text-xs text-gray-400">{{ strlen($alamat_rt ?? '') }}/3</span>
                            </div>
                        </div>
                        <div>
                            <input
                                type="text"
                                wire:model.live="alamat_rw"
                                maxlength="3"
                                inputmode="numeric"
                                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 3)"
                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors @error('alamat_rw') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                                placeholder="002"
                            >
                            <div class="flex items-center justify-between mt-1">
                                @error('alamat_rw')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @else
                                    <span></span>
                                @enderror
                                <span class="text-xs text-gray-400">{{ strlen($alamat_rw ?? '') }}/3</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Desa/Kelurahan --}}
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Desa/Kelurahan <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="alamat_desa_kelurahan"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors @error('alamat_desa_kelurahan') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                        >
                        @error('alamat_desa_kelurahan')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Kecamatan --}}
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Kecamatan <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="alamat_kecamatan"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors @error('alamat_kecamatan') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                        >
                        @error('alamat_kecamatan')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Kabupaten/Kota --}}
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Kabupaten/Kota <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="alamat_kabupaten_kota"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors @error('alamat_kabupaten_kota') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                        >
                        @error('alamat_kabupaten_kota')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Provinsi --}}
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Provinsi <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="alamat_provinsi"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors @error('alamat_provinsi') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                        >
                        @error('alamat_provinsi')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Kode Pos --}}
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Kode Pos</label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="alamat_kode_pos"
                            maxlength="5"
                            inputmode="numeric"
                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 5)"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors @error('alamat_kode_pos') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                            placeholder="12345"
                        >
                        <div class="flex items-center justify-between mt-1">
                            @error('alamat_kode_pos')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @else
                                <span></span>
                            @enderror
                            <span class="text-xs text-gray-400">{{ strlen($alamat_kode_pos ?? '') }}/5</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── KONTAK ──────────────────────────────────────────────────── --}}
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-tinta mb-4 border-b pb-2">Kontak</h2>
            <div class="space-y-3">

                {{-- No Telepon --}}
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">No Telepon <span class="text-delima">*</span></label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="text"
                            wire:model.live="no_telepon"
                            maxlength="13"
                            inputmode="numeric"
                            x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 13)"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors @error('no_telepon') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                            placeholder="Contoh: 08123456789"
                        >
                        <div class="flex items-center justify-between mt-1">
                            @error('no_telepon')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @else
                                <span></span>
                            @enderror
                            <span class="text-xs text-gray-400">{{ strlen($no_telepon ?? '') }}/13</span>
                        </div>
                    </div>
                </div>

                {{-- Email --}}
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Email</label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="email"
                            wire:model.live="email"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors @error('email') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                            placeholder="info@sekolah.sch.id"
                        >
                        @error('email')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Website --}}
                <div class="grid grid-cols-12 gap-3 items-start">
                    <label class="col-span-3 text-sm font-medium text-gray-700 text-right pt-2">Website</label>
                    <span class="col-span-1 text-sm font-medium text-gray-700 text-center pt-2">:</span>
                    <div class="col-span-8">
                        <input
                            type="url"
                            wire:model.live="website"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zamrud transition-colors @error('website') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                            placeholder="https://www.sekolah.sch.id"
                        >
                        @error('website')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- ── SUBMIT ──────────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <x-ui.button variant="primary" type="submit">
                <span wire:loading.remove wire:target="save">{{ $isExisting ? 'Perbarui' : 'Simpan' }} Profil Sekolah</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </x-ui.button>
        </div>

    </form>
</div>
