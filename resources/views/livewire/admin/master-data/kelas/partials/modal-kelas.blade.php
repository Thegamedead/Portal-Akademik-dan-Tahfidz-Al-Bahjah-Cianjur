<div class="fixed inset-0 flex items-center justify-center z-50 pointer-events-none" style="pointer-events: none;">
    <div class="bg-white rounded-lg w-full max-w-md shadow-2xl overflow-hidden" style="border: 1px solid #D1D5DB; pointer-events: auto;">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-5" style="background-color:#1B5E4F;">
            <h3 class="text-lg font-semibold" style="color:white;font-family:'Plus Jakarta Sans',sans-serif;">
                {{ $kelasId ? 'Edit' : 'Tambah' }} Kelas
            </h3>
        </div>

        <div class="p-6 space-y-4" style="font-family:'Inter',sans-serif;">

            {{-- Tingkat --}}
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#1A2422;">
                    Tingkat <span style="color:#9B3A3A;">*</span>
                </label>
                <select
                    wire:model.live="tingkat"
                    class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 transition-all @error('tingkat') border-red-400 bg-red-50 @enderror"
                    style="border:1.5px solid {{ $errors->has('tingkat') ? '#f87171' : '#D1D5DB' }};color:#1A2422;"
                >
                    <option value="">Pilih Tingkat</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                </select>
                @error('tingkat')
                    <span class="text-xs mt-1 block" style="color:#9B3A3A;">{{ $message }}</span>
                @enderror
            </div>

            {{-- Nama Kelas --}}
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#1A2422;">
                    Nama Kelas <span style="color:#9B3A3A;">*</span>
                </label>
                <input
                    type="text"
                    wire:model.live="nama_kelas"
                    maxlength="10"
                    placeholder="Contoh: 7A, 7B, 8A..."
                    class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 transition-all"
                    style="border:1.5px solid {{ $errors->has('nama_kelas') ? '#f87171' : '#D1D5DB' }};color:#1A2422;{{ $errors->has('nama_kelas') ? 'background-color:#fef2f2;' : '' }}"
                >
                <p class="text-xs mt-1" style="color:#6B7280;">Format: 7A, 7B, 8A, 9C, dll.</p>
                @error('nama_kelas')
                    <span class="text-xs mt-0.5 block" style="color:#9B3A3A;">{{ $message }}</span>
                @enderror
            </div>

            {{-- Ruang Kelas --}}
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#1A2422;">Ruang Kelas</label>
                <input
                    type="text"
                    wire:model.live="ruang_kelas"
                    maxlength="50"
                    placeholder="Contoh: R101, Lantai 2..."
                    class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 transition-all"
                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                >
                <p class="text-xs mt-1" style="color:#6B7280;">Opsional - lokasi atau ruang kelas</p>
                @error('ruang_kelas')
                    <span class="text-xs mt-0.5 block" style="color:#9B3A3A;">{{ $message }}</span>
                @enderror
            </div>

            {{-- Wali Kelas --}}
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#1A2422;">
                    Wali Kelas <span style="color:#9B3A3A;">*</span>
                </label>
                <input
                    type="text"
                    wire:model.live="nama_wali_kelas"
                    placeholder="Nama lengkap wali kelas"
                    class="w-full rounded-lg px-3 py-2 focus:outline-none focus:ring-2 transition-all"
                    style="border:1.5px solid {{ $errors->has('nama_wali_kelas') ? '#f87171' : '#D1D5DB' }};color:#1A2422;{{ $errors->has('nama_wali_kelas') ? 'background-color:#fef2f2;' : '' }}"
                >
                @error('nama_wali_kelas')
                    <span class="text-xs mt-1 block" style="color:#9B3A3A;">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="flex justify-end gap-3 px-6 py-4" style="background-color: #F9FAFB; border-top: 1px solid #E5E7EB;">
            <button wire:click="$set('showModal', false)" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #9B3A3A; border: 1.5px solid #9B3A3A;">
                Batal
            </button>
            <button wire:click="save" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #1B5E4F; border: 1.5px solid #1B5E4F;" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">Simpan</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </div>
    </div>
</div>
