{{--
    Partial View: modal-registrasi-keluar.blade.php
    Modal for student exit registration (≤6 fields → modal sesuai aturan UI design system)

    Requirements: 2.1, 2.2, 2.3, 2.4, 2.5
    - Display student info (nama, NIS)
    - Form fields: Jenis Keluar dropdown, Tanggal Keluar date picker
    - Conditional Tujuan Sekolah field (shown when jenis_keluar = 'Pindah Sekolah')
    - Keterangan textarea with conditional required indicator
    - Wire all fields with wire:model or wire:model.live
    - Display validation errors with @error directives
    - Modal header, body, footer with cancel and submit buttons
--}}

@if ($showModalRegistrasiKeluar)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-registrasi-keluar-title"
    >
        {{-- Backdrop --}}
        <div
            class="absolute inset-0 bg-transparent"
            wire:click="tutupModalRegistrasiKeluar"
            aria-hidden="true"
        ></div>

        {{-- Panel --}}
        <div
            class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl"
            style="font-family:'Inter',sans-serif;"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                <div>
                    <h3 id="modal-registrasi-keluar-title" class="text-base font-semibold"
                        style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                        Registrasi Keluar Siswa
                        @if(count($selectedIds) > 1)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" style="background-color:#1B5E4F;color:#FFFFFF;">
                                {{ count($selectedIds) }} siswa
                            </span>
                        @endif
                    </h3>
                    @if(count($selectedIds) > 1)
                        <p class="mt-1 text-xs" style="color:#6B7280;">Semua siswa yang dipilih akan diproses keluar dengan data yang sama</p>
                    @endif
                </div>
                <button
                    type="button"
                    wire:click="tutupModalRegistrasiKeluar"
                    class="rounded-lg p-1.5 transition-colors hover:bg-white/20 focus:outline-none focus:ring-2" style="color:white;"
                    aria-label="Tutup modal"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-4">
                {{-- Student Info Card --}}
                @if(count($selectedIds) === 1)
                    @php
                        $siswaRegistrasiKeluar = App\Models\Siswa::find($selectedIds[0]);
                    @endphp
                    @if($siswaRegistrasiKeluar)
                        <div class="mb-4 p-3 rounded-lg" style="background-color:#F4F5F4;border-left:3px solid #1B5E4F;">
                            <div class="font-semibold text-sm" style="color:#1A2422;">
                                {{ $siswaRegistrasiKeluar->nama }}
                            </div>
                            <div class="text-xs mt-0.5" style="color:#6B7280;">
                                NIS: {{ $siswaRegistrasiKeluar->nis }}
                            </div>
                        </div>
                    @endif
                @else
                    {{-- Preview List Siswa yang Dipilih (Bulk) --}}
                    <div class="mb-4 p-3 rounded-lg" style="background-color:#FFF7ED;border-left:3px solid #C2410C;">
                        <div class="font-semibold text-sm mb-2" style="color:#1A2422;">
                            {{ count($selectedIds) }} siswa akan diproses keluar:
                        </div>
                        <div class="max-h-24 overflow-y-auto space-y-1">
                            @php
                                $selectedSiswasList = App\Models\Siswa::whereIn('id', $selectedIds)->get();
                            @endphp
                            @foreach($selectedSiswasList as $s)
                                <div class="text-xs flex items-center gap-2" style="color:#6B7280;">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $s->nama }} ({{ $s->nis }})</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Form Fields --}}
                <div class="space-y-4">
                    {{-- Field 1: Jenis Keluar (Dropdown) --}}
                    <div>
                        <label for="jenis-keluar" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                            Jenis Keluar <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                        </label>
                        <select
                            id="jenis-keluar"
                            wire:model.live="jenisKeluar"
                            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                            style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('jenisKeluar') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                            aria-required="true"
                        >
                            <option value="">-- Pilih Jenis Keluar --</option>
                            @foreach(\App\Enums\JenisKeluar::options() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('jenisKeluar')
                            <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Field 2: Tanggal Keluar (Date Picker) --}}
                    <div>
                        <label for="tanggal-keluar" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                            Tanggal Keluar <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="tanggal-keluar"
                            type="date"
                            wire:model="tanggalKeluar"
                            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                            style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('tanggalKeluar') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                            aria-required="true"
                        >
                        @error('tanggalKeluar')
                            <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Field 3: Tujuan Sekolah (Conditional - only for 'Pindah Sekolah') --}}
                    @if($jenisKeluar === 'Pindah Sekolah')
                        <div x-show="true" x-transition>
                            <label for="tujuan-sekolah" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                                Tujuan Sekolah <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                            </label>
                            <input
                                id="tujuan-sekolah"
                                type="text"
                                wire:model="tujuanSekolah"
                                class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                                style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('tujuanSekolah') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                                placeholder="Nama sekolah tujuan"
                                aria-required="true"
                            >
                            @error('tujuanSekolah')
                                <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    {{-- Field 4: Keterangan (Textarea with conditional required indicator) --}}
                    <div>
                        <label for="keterangan-keluar" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                            Keterangan
                            @if(in_array($jenisKeluar, ['Meninggal', 'Keluar']))
                                <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                            @endif
                        </label>
                        <textarea
                            id="keterangan-keluar"
                            wire:model="keteranganKeluar"
                            rows="3"
                            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                            style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('keteranganKeluar') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;resize:vertical;"
                            placeholder="Catatan tambahan tentang keberangkatan siswa"
                            @if(in_array($jenisKeluar, ['Meninggal', 'Keluar']))
                                aria-required="true"
                            @endif
                        ></textarea>
                        @error('keteranganKeluar')
                            <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB;">
                <button
                    type="button"
                    wire:click="tutupModalRegistrasiKeluar"
                    class="btn-modal-cancel flex-1"
                >
                    Batal
                </button>
                <button
                    type="button"
                    wire:click="submitRegistrasiKeluar"
                    class="btn-modal-confirm flex-1"
                >
                    Submit
                </button>
            </div>
        </div>
    </div>
@endif
