{{--
    View: livewire/admin/master-data/semester/modal-semester.blade.php
    Komponen Livewire: App\Livewire\Admin\MasterData\Semester\ModalSemester

    Modal form untuk create/edit Semester (≤6 fields: tahun_ajaran_id, jenis, tanggal_mulai, tanggal_selesai, status_aktif)
--}}

<div>
@if ($showModal)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-semester-title"
    >
        {{-- Backdrop --}}
        <div
            class="absolute inset-0 bg-black/40"
            wire:click="tutupModal"
            aria-hidden="true"
        ></div>

        {{-- Panel --}}
        <div
            class="relative w-full max-w-md rounded-2xl bg-white shadow-xl"
            style="font-family:'Inter',sans-serif;"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                <h3 id="modal-semester-title" class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                    {{ $semesterId ? 'Edit Semester' : 'Tambah Semester' }}
                </h3>
                <button
                    type="button"
                    wire:click="tutupModal"
                    class="rounded-lg p-1.5 transition-colors hover:bg-white/20 focus:outline-none focus:ring-2"
                    style="color:white;"
                    aria-label="Tutup modal"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-4 space-y-4">

                {{-- Tahun Ajaran --}}
                <div>
                    <label for="tahun-ajaran" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                        Tahun Ajaran <span style="color:#9B3A3A;">*</span>
                    </label>
                    <select
                        id="tahun-ajaran"
                        wire:model="tahun_ajaran_id"
                        class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                        style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('tahun_ajaran_id') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                        aria-required="true"
                    >
                        <option value="">Pilih Tahun Ajaran</option>
                        @foreach($tahunAjaranList as $tahunAjaran)
                            <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->label }}</option>
                        @endforeach
                    </select>
                    @error('tahun_ajaran_id')
                        <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jenis Semester --}}
                <div>
                    <label for="jenis" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                        Jenis Semester <span style="color:#9B3A3A;">*</span>
                    </label>
                    <select
                        id="jenis"
                        wire:model="jenis"
                        class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                        style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('jenis') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                        aria-required="true"
                    >
                        <option value="">Pilih Jenis</option>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                    @error('jenis')
                        <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Mulai --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:#374151;">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" wire:model="tanggal_mulai" class="w-full rounded-lg px-3 py-2 text-sm focus:ring-2 focus:outline-none" style="border:1px solid #D1D5DB; color:#1A2422;">
                    @error('tanggal_mulai') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Tanggal Selesai --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:#374151;">
                        Tanggal Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" wire:model="tanggal_selesai" class="w-full rounded-lg px-3 py-2 text-sm focus:ring-2 focus:outline-none" style="border:1px solid #D1D5DB; color:#1A2422;">
                    @error('tanggal_selesai') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Status Aktif --}}
                <div class="flex items-start gap-3">
                    <div class="flex items-center h-10">
                        <input
                            id="status-aktif"
                            type="checkbox"
                            wire:model="status_aktif"
                            class="h-4 w-4 rounded focus:ring-2 focus:ring-offset-2 transition-colors cursor-pointer"
                            style="color:#1B5E4F;border-color:#D1D5DB;"
                        >
                    </div>
                    <div class="flex-1">
                        <label for="status-aktif" class="block text-sm font-medium cursor-pointer" style="color:#1A2422;">
                            Status Aktif
                        </label>
                        <p class="mt-0.5 text-xs" style="color:#6B7280;">
                            Tandai sebagai semester yang sedang berjalan saat ini.
                        </p>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB; background-color: #F9FAFB;">
                <button
                    type="button"
                    wire:click="tutupModal"
                    class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none"
                    style="background-color: #9B3A3A; border: 1.5px solid #9B3A3A;"
                >
                    Batalkan
                </button>
                <button
                    type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none"
                    style="background-color: #1B5E4F; border: 1.5px solid #1B5E4F;"
                >
                    <span wire:loading.remove wire:target="save">Simpan</span>
                    <span wire:loading wire:target="save" class="flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan…
                    </span>
                </button>
            </div>
        </div>
    </div>
@endif
</div>
