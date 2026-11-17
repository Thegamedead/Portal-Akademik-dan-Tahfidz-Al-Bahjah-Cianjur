{{--
    Partial View: modal-registrasi-masuk.blade.php
    Modal for student entry registration (≤6 fields → modal sesuai aturan UI design system)

    Fields:
    - Status Masuk dropdown (Baru/Pindahan) - required
    - Tanggal Masuk date picker - required
    - Conditional Asal Sekolah field (shown when status_masuk = 'Pindahan') - required for Pindahan
    - Conditional Alamat Sekolah Asal textarea (shown when status_masuk = 'Pindahan') - required for Pindahan
--}}

@if ($showModalRegistrasiMasuk && $siswaIdRegistrasiMasuk)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-registrasi-masuk-title"
        style="pointer-events: none;"
    >
        {{-- Panel --}}
        <div
            class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl"
            style="font-family:'Inter',sans-serif; pointer-events: auto;"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                <h3 id="modal-registrasi-masuk-title" class="text-base font-semibold"
                    style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                    Registrasi Masuk Siswa
                </h3>
                <button
                    type="button"
                    wire:click="tutupModalRegistrasiMasuk"
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
                @php
                    $siswa = \App\Models\Siswa::find($siswaIdRegistrasiMasuk);
                @endphp

                @if($siswa)
                    <div class="mb-4 p-3 rounded-lg" style="background-color:#F4F5F4;border-left:3px solid #1B5E4F;">
                        <div class="font-semibold text-sm" style="color:#1A2422;">
                            {{ $siswa->nama }}
                        </div>
                        <div class="text-xs mt-0.5" style="color:#6B7280;">
                            NIS: {{ $siswa->nis }}
                        </div>
                    </div>
                @endif

                {{-- Form Fields --}}
                <div class="space-y-4">
                    {{-- Field 1: Status Masuk (Dropdown) --}}
                    <div>
                        <label for="status-masuk" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                            Status Masuk <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                        </label>
                        <select
                            id="status-masuk"
                            wire:model.live="statusMasuk"
                            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                            style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('statusMasuk') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                            aria-required="true"
                        >
                            <option value="">-- Pilih Status Masuk --</option>
                            <option value="Baru">Baru</option>
                            <option value="Pindahan">Pindahan</option>
                        </select>
                        @error('statusMasuk')
                            <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Field 2: Tanggal Masuk (Date Picker) --}}
                    <div>
                        <label for="tanggal-masuk" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                            Tanggal Masuk <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                        </label>
                        <input
                            id="tanggal-masuk"
                            type="date"
                            wire:model="tanggalMasuk"
                            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                            style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('tanggalMasuk') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                            aria-required="true"
                        >
                        @error('tanggalMasuk')
                            <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Field 3: Asal Sekolah (Conditional - only for 'Pindahan') --}}
                    @if($statusMasuk === 'Pindahan')
                        <div x-show="true" x-transition>
                            <label for="asal-sekolah" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                                Asal Sekolah <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                            </label>
                            <input
                                id="asal-sekolah"
                                type="text"
                                wire:model="asalSekolah"
                                class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                                style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('asalSekolah') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                                placeholder="Nama sekolah asal"
                                aria-required="true"
                            >
                            @error('asalSekolah')
                                <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    {{-- Field 4: Alamat Sekolah Asal (Conditional - only for 'Pindahan') --}}
                    @if($statusMasuk === 'Pindahan')
                        <div x-show="true" x-transition>
                            <label for="alamat-sekolah-asal" class="mb-1.5 block text-sm font-medium" style="color:#1A2422;">
                                Alamat Sekolah Asal <span style="color:#9B3A3A;" aria-hidden="true">*</span>
                            </label>
                            <textarea
                                id="alamat-sekolah-asal"
                                wire:model="alamatSekolahAsal"
                                rows="3"
                                class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2"
                                style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('alamatSekolahAsal') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;resize:vertical;"
                                placeholder="Alamat lengkap sekolah asal"
                                aria-required="true"
                            ></textarea>
                            @error('alamatSekolahAsal')
                                <p class="mt-1.5 text-xs" style="color:#9B3A3A;" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB;">
                <button
                    type="button"
                    wire:click="tutupModalRegistrasiMasuk"
                    class="btn-modal-cancel flex-1"
                >
                    Batal
                </button>
                <button
                    type="button"
                    wire:click="submitRegistrasiMasuk"
                    class="btn-modal-confirm flex-1"
                >
                    Submit
                </button>
            </div>
        </div>
    </div>
@endif
