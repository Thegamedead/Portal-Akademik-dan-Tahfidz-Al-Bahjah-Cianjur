{{--
    Partial View: modal-konfirmasi-keluar.blade.php
    Confirmation modal for critical exit registration action

    Requirements: 4.1, 4.2, 4.3
    - Display warning message about critical action
    - Show summary of exit data (siswa name, jenis keluar, tanggal)
    - List consequences: status change, account deactivation, data preservation
    - Cancel and confirmation buttons
    - Styled with amber warning colors
--}}

@if ($showModalKonfirmasiKeluar && $siswaRegistrasiKeluar)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-konfirmasi-keluar-title"
    >
        {{-- Backdrop --}}
        <div
            class="absolute inset-0 bg-transparent"
            wire:click="tutupModalKonfirmasiKeluar"
            aria-hidden="true"
        ></div>

        {{-- Panel --}}
        <div
            class="relative w-full max-w-md rounded-2xl bg-white shadow-xl"
            style="font-family:'Inter',sans-serif;"
        >
            {{-- Header with amber warning background --}}
            <div class="flex items-center gap-3 px-6 pt-6 pb-4 rounded-t-2xl" style="background-color:#FFFBEB;border-bottom:1px solid #FCD34D;">
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" style="color:#C9A227;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 id="modal-konfirmasi-keluar-title" class="text-base font-semibold"
                    style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                    Konfirmasi Aksi Kritikal
                </h3>
            </div>

            {{-- Body --}}
            <div class="px-6 py-4">
                {{-- Warning Message --}}
                <p class="text-sm mb-4" style="color:#1A2422;">
                    Registrasi keluar adalah <strong>aksi kritikal</strong> yang akan menonaktifkan akun siswa
                    dan mencabut semua sesi login aktif.
                </p>

                {{-- Summary of Exit Data --}}
                <div class="mb-4 p-3 rounded-lg text-sm space-y-1.5" style="background-color:#F4F5F4;">
                    <div style="color:#1A2422;">
                        <span class="font-medium">Siswa:</span>
                        <span>{{ $siswaRegistrasiKeluar->nama }}</span>
                    </div>
                    <div style="color:#1A2422;">
                        <span class="font-medium">NIS:</span>
                        <span>{{ $siswaRegistrasiKeluar->nis }}</span>
                    </div>
                    <div style="color:#1A2422;">
                        <span class="font-medium">Jenis Keluar:</span>
                        <span>
                            @foreach(\App\Enums\JenisKeluar::options() as $value => $label)
                                @if($value === $jenisKeluar)
                                    {{ $label }}
                                @endif
                            @endforeach
                        </span>
                    </div>
                    <div style="color:#1A2422;">
                        <span class="font-medium">Tanggal Keluar:</span>
                        <span>{{ \Carbon\Carbon::parse($tanggalKeluar)->tgl() }}</span>
                    </div>
                    @if($jenisKeluar === 'Pindah Sekolah' && $tujuanSekolah)
                        <div style="color:#1A2422;">
                            <span class="font-medium">Tujuan Sekolah:</span>
                            <span>{{ $tujuanSekolah }}</span>
                        </div>
                    @endif
                </div>

                {{-- Consequences List --}}
                <p class="text-sm font-medium mb-2" style="color:#1A2422;">
                    Konsekuensi:
                </p>
                <ul class="text-sm space-y-1 list-disc list-inside" style="color:#6B7280;">
                    <li>
                        Status Akademik berubah menjadi
                        <strong style="color:#1A2422;">
                            @if($jenisKeluar === 'Lulus SMP')
                                Lulus SMP
                            @elseif($jenisKeluar === 'Pindah Sekolah')
                                Pindah Sekolah
                            @else
                                Keluar
                            @endif
                        </strong>
                    </li>
                    <li>Akun siswa akan <strong style="color:#1A2422;">dinonaktifkan</strong> (tidak bisa login)</li>
                    <li>Semua sesi login aktif akan <strong style="color:#1A2422;">dicabut otomatis</strong></li>
                    <li>Data historis siswa (absensi, setoran, progres hafalan) tetap <strong style="color:#1A2422;">tersimpan</strong></li>
                </ul>

                {{-- Final Warning --}}
                <div class="mt-4 p-3 rounded-lg" style="background-color:#FEF3C7;border-left:3px solid #C9A227;">
                    <p class="text-xs font-medium" style="color:#78350F;">
                        ⚠️ Aksi ini tidak dapat dibatalkan setelah diproses.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB;">
                <button
                    type="button"
                    wire:click="tutupModalKonfirmasiKeluar"
                    class="btn-modal-cancel flex-1"
                >
                    Batal
                </button>
                <button
                    type="button"
                    wire:click="prosesRegistrasiKeluar"
                    class="btn-modal-confirm flex-1"
                >
                    Ya, Proses Registrasi Keluar
                </button>
            </div>
        </div>
    </div>
@endif
