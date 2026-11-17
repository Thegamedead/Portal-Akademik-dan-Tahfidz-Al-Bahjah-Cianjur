{{--
    View: livewire/admin/usulan-kenaikan-tingkat/review-usulan.blade.php
    Komponen Livewire: App\Livewire\Admin\UsulanKenaikanTingkat\ReviewUsulan

    Modal component for admin to approve or reject promotion proposals.
    Displays proposal details, student progress, and provides decision buttons.

    Task 14.1: Implement ReviewUsulan component (Admin namespace)
    Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6
--}}

<div>
    {{-- Modal --}}
    @if($showModal && $usulan)
        <div
            x-data="{ show: @entangle('showModal') }"
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto pointer-events-none"
            style="font-family:'Inter',sans-serif;"
        >
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/40 pointer-events-auto" wire:click="closeModal"></div>

            {{-- Modal Container --}}
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div
                    x-show="show"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl pointer-events-auto"
                    style="border:1px solid #E5E7EB;"
                >
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                        <h3 class="text-lg font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                            Review Usulan Kenaikan Tingkat
                        </h3>
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="rounded-lg p-1 transition-colors focus:outline-none"
                            style="color:#6B7280;"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="px-6 py-5 space-y-5">

                        {{-- General Error --}}
                        @error('action')
                            <div class="rounded-lg px-4 py-3 text-sm" style="background-color:#FEE2E2;color:#991B1B;border:1px solid #FCA5A5;">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            </div>
                        @enderror

                        {{-- Proposal Details Section (Req 4.1) --}}
                        <div class="rounded-lg p-4" style="background-color:#F9FAFB;border:1px solid #E5E7EB;">
                            <h4 class="text-sm font-semibold mb-3" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                                Detail Usulan
                            </h4>
                            <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                                {{-- Student Name --}}
                                <div>
                                    <span class="text-xs font-medium" style="color:#6B7280;">Nama Siswa</span>
                                    <p class="mt-0.5 font-semibold" style="color:#1A2422;">{{ $usulan->siswa->nama }}</p>
                                </div>

                                {{-- NIS --}}
                                <div>
                                    <span class="text-xs font-medium" style="color:#6B7280;">NIS</span>
                                    <p class="mt-0.5 font-semibold" style="color:#1A2422;font-variant-numeric:tabular-nums;">{{ $usulan->siswa->nis }}</p>
                                </div>

                                {{-- Current Group --}}
                                <div>
                                    <span class="text-xs font-medium" style="color:#6B7280;">Kelompok Asal</span>
                                    <p class="mt-0.5 font-semibold" style="color:#1A2422;">{{ $usulan->kelompokAsal->nama ?? '-' }}</p>
                                </div>

                                {{-- Current Level --}}
                                <div>
                                    <span class="text-xs font-medium" style="color:#6B7280;">Tingkat Asal</span>
                                    <p class="mt-0.5 font-semibold" style="color:#1A2422;">{{ $usulan->tingkat_asal->value }}</p>
                                </div>

                                {{-- Target Level --}}
                                <div>
                                    <span class="text-xs font-medium" style="color:#6B7280;">Tingkat Tujuan</span>
                                    <p class="mt-0.5 font-semibold flex items-center gap-2" style="color:#1B5E4F;">
                                        {{ $usulan->tingkat_tujuan->value }}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                    </p>
                                </div>

                                {{-- Proposing Teacher --}}
                                <div>
                                    <span class="text-xs font-medium" style="color:#6B7280;">Guru Pengusul</span>
                                    <p class="mt-0.5 font-semibold" style="color:#1A2422;">{{ $usulan->guruPengusul->nama }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Progress Information Section (Req 4.1) --}}
                        <div class="rounded-lg p-4" style="background-color:#EDF7F5;border:1px solid #A7D9CD;">
                            <h4 class="text-sm font-semibold mb-3 flex items-center gap-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1B5E4F;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Progres Hafalan Siswa
                            </h4>

                            @if(!$hasProgresData)
                                {{-- Warning: No Progres Data --}}
                                <div class="mb-3 p-3 rounded-lg" style="background-color:#FFF7ED;border:1px solid #FFEDD5;">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 mt-0.5 shrink-0" style="color:#C2410C;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <div class="text-xs" style="color:#C2410C;">
                                            <p class="font-semibold mb-1">⚠️ Data Progres Hafalan Tidak Ditemukan</p>
                                            <p>Siswa ini belum memiliki catatan progres hafalan di sistem. Kemungkinan penyebab:</p>
                                            <ul class="mt-1 ml-4 list-disc space-y-0.5">
                                                <li>Guru belum pernah mencatat setoran hafalan siswa ini</li>
                                                <li>Data tahun ajaran kelompok asal tidak sesuai</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Info: Data Source --}}
                                <div class="mb-3 p-2 rounded text-xs flex items-center gap-2" style="background-color:#D1FAE5;color:#065F46;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ $progresDataInfo }}</span>
                                </div>
                            @endif

                            <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                                {{-- Total Juz Tuntas --}}
                                <div>
                                    <span class="text-xs font-medium" style="color:#065F46;">Juz Tuntas Saat Ini</span>
                                    <p class="mt-0.5 text-2xl font-bold" style="color:#1B5E4F;font-variant-numeric:tabular-nums;">
                                        {{ $totalJuzTuntas }} Juz
                                    </p>
                                </div>

                                {{-- Required Threshold --}}
                                <div>
                                    <span class="text-xs font-medium" style="color:#065F46;">Syarat Tingkat {{ $usulan->tingkat_tujuan->value }}</span>
                                    <p class="mt-0.5 text-2xl font-bold" style="color:#1B5E4F;font-variant-numeric:tabular-nums;">
                                        {{ $requiredThreshold }} Juz
                                    </p>
                                </div>
                            </div>

                            {{-- Eligibility Status --}}
                            @if($totalJuzTuntas >= $requiredThreshold)
                                <div class="mt-3 rounded-lg px-3 py-2 text-xs font-medium flex items-center gap-2" style="background-color:#D1FAE5;color:#065F46;">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Siswa memenuhi syarat kenaikan tingkat ({{ $totalJuzTuntas }} ≥ {{ $requiredThreshold }} juz)
                                </div>
                            @else
                                <div class="mt-3 rounded-lg px-3 py-2 text-xs font-medium flex items-center gap-2" style="background-color:#FEF3C7;color:#92400E;">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Perhatian: Siswa kurang {{ $requiredThreshold - $totalJuzTuntas }} juz dari syarat minimal
                                </div>
                            @endif
                        </div>

                        {{-- Guru's Reason (Alasan) --}}
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:#1A2422;">
                                Alasan dari Guru Pengusul
                            </label>
                            <div class="rounded-lg p-3 text-sm" style="background-color:#F4F5F4;border:1px solid #E5E7EB;color:#1A2422;">
                                {{ $usulan->alasan }}
                            </div>
                        </div>

                        {{-- Tanggal Keputusan / Review --}}
                        <div>
                            <label for="tanggal_keputusan" class="block text-sm font-medium mb-1.5" style="color:#1A2422;">
                                Tanggal Keputusan / Review <span style="color:#DC2626;">*</span>
                            </label>
                            <input
                                type="date"
                                id="tanggal_keputusan"
                                wire:model="tanggal_keputusan"
                                data-no-flatpickr="true"
                                class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 block @error('tanggal_keputusan') border-red-500 @enderror no-flatpickr"
                                style="background-color:#FFFFFF;border:1.5px solid #D1D5DB;color:#1A2422;"
                            >
                            @error('tanggal_keputusan')
                                <p class="mt-1.5 text-xs" style="color:#DC2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Catatan Keputusan (Req 4.4, 4.5) --}}
                        <div>
                            <label for="catatan_keputusan" class="block text-sm font-medium mb-1.5" style="color:#1A2422;">
                                Catatan Keputusan
                                <span class="text-xs font-normal" style="color:#6B7280;">(Opsional untuk persetujuan, wajib untuk penolakan)</span>
                            </label>
                            <textarea
                                id="catatan_keputusan"
                                wire:model="catatan_keputusan"
                                rows="3"
                                class="w-full rounded-lg py-2.5 px-3 text-sm focus:outline-none focus:ring-2 @error('catatan_keputusan') border-red-500 @enderror"
                                style="background-color:#FFFFFF;border:1.5px solid #E5E7EB;color:#1A2422;focus:ring-color:#1B5E4F;resize:vertical;"
                                placeholder="Tambahkan catatan keputusan (contoh: alasan persetujuan atau penolakan)"
                            ></textarea>
                            @error('catatan_keputusan')
                                <p class="mt-1.5 text-xs" style="color:#DC2626;">{{ $message }}</p>
                            @else
                                <p class="mt-1.5 text-xs" style="color:#6B7280;">
                                    Minimal 10 karakter jika menolak usulan.
                                </p>
                            @enderror
                        </div>

                    </div>

                    {{-- Modal Footer with Action Buttons (Req 4.2, 4.3) --}}
                    <div class="flex items-center justify-end gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB; background-color: #F9FAFB;">
                        {{-- Cancel Button --}}
                        <button
                            type="button"
                            wire:click="closeModal"
                            wire:loading.attr="disabled"
                            class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none disabled:opacity-50"
                            style="background-color:#FFFFFF;color:#6B7280;border:1.5px solid #D1D5DB;"
                        >
                            Batal
                        </button>

                        {{-- Tolak Button (Delima color for rejection - Req 4.3) --}}
                        <button
                            type="button"
                            wire:click="reject"
                            wire:loading.attr="disabled"
                            wire:target="reject,approve"
                            class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            style="background-color:#FFFFFF;color:#9B3A3A;border:1.5px solid #9B3A3A;focus:ring-color:#9B3A3A;"
                        >
                            <span wire:loading.remove wire:target="reject">Tolak</span>
                            <span wire:loading.inline-flex wire:target="reject" class="items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>

                        {{-- Setuju Button (Zamrud color for approval - Req 4.2) --}}
                        <button
                            type="button"
                            wire:click="approve"
                            wire:loading.attr="disabled"
                            wire:target="approve,reject"
                            class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;focus:ring-color:#1B5E4F;"
                        >
                            <span wire:loading.remove wire:target="approve">Setuju</span>
                            <span wire:loading.inline-flex wire:target="approve" class="items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>
