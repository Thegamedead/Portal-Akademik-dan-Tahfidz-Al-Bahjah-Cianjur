<div>
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
            class="fixed inset-0 z-50 overflow-y-auto"
            style="font-family:'Inter',sans-serif;"
        >
            <div class="fixed inset-0 bg-black/40 pointer-events-auto" wire:click="closeModal"></div>
            <div class="flex items-center justify-center min-h-screen px-4 py-6 pointer-events-none">
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
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                        <h3 class="text-lg font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                            Detail Usulan Kenaikan Tingkat
                        </h3>
                        <button type="button" wire:click="closeModal" class="rounded-lg p-1 transition-colors focus:outline-none" style="color:#6B7280;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-5 space-y-5">
                        <div class="rounded-lg p-4" style="background-color:#F9FAFB;border:1px solid #E5E7EB;">
                            <h4 class="text-sm font-semibold mb-3" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                                Informasi Usulan
                            </h4>
                            <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                                <div>
                                    <span class="text-xs font-medium" style="color:#6B7280;">Nama Siswa</span>
                                    <p class="mt-0.5 font-semibold" style="color:#1A2422;">{{ $usulan->siswa->nama }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium" style="color:#6B7280;">NIS</span>
                                    <p class="mt-0.5 font-semibold" style="color:#1A2422;">{{ $usulan->siswa->nis }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium" style="color:#6B7280;">Status</span>
                                    <p class="mt-0.5 font-semibold" style="color:#1A2422;">{{ $usulan->status_usulan->value }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium" style="color:#6B7280;">Guru Pengusul</span>
                                    <p class="mt-0.5 font-semibold" style="color:#1A2422;">{{ $usulan->guruPengusul->nama }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium" style="color:#6B7280;">Tingkat Asal</span>
                                    <p class="mt-0.5 font-semibold" style="color:#1A2422;">{{ $usulan->tingkat_asal->value }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium" style="color:#6B7280;">Tingkat Tujuan</span>
                                    <p class="mt-0.5 font-semibold" style="color:#1B5E4F;">{{ $usulan->tingkat_tujuan->value }}</p>
                                </div>
                                @if($usulan->tanggal_keputusan)
                                    <div>
                                        <span class="text-xs font-medium" style="color:#6B7280;">Tanggal Keputusan</span>
                                        <p class="mt-0.5 font-semibold" style="color:#1A2422;">{{ $usulan->tanggal_keputusan->format('d M Y, H:i') }}</p>
                                    </div>
                                @endif
                                @if($usulan->adminVerifikator)
                                    <div>
                                        <span class="text-xs font-medium" style="color:#6B7280;">Admin Verifikator</span>
                                        <p class="mt-0.5 font-semibold" style="color:#1A2422;">{{ $usulan->adminVerifikator->nama }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-lg p-4" style="background-color:#EDF7F5;border:1px solid #A7D9CD;">
                            <h4 class="text-sm font-semibold mb-3 flex items-center gap-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1B5E4F;">
                                Progres Hafalan Saat Pengusulan
                            </h4>
                            <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                                <div>
                                    <span class="text-xs font-medium" style="color:#065F46;">Juz Tuntas</span>
                                    <p class="mt-0.5 text-xl font-bold" style="color:#1B5E4F;">{{ $totalJuzTuntas }} Juz</p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium" style="color:#065F46;">Syarat Tingkat {{ $usulan->tingkat_tujuan->value }}</span>
                                    <p class="mt-0.5 text-xl font-bold" style="color:#1B5E4F;">{{ $requiredThreshold }} Juz</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color:#1A2422;">Alasan dari Guru Pengusul</label>
                            <div class="rounded-lg p-3 text-sm" style="background-color:#F4F5F4;border:1px solid #E5E7EB;">
                                {{ $usulan->alasan ?: '-' }}
                            </div>
                        </div>

                        @if($usulan->catatan_keputusan)
                            <div>
                                <label class="block text-sm font-medium mb-1.5" style="color:#1A2422;">Catatan Keputusan Admin</label>
                                <div class="rounded-lg p-3 text-sm" style="background-color:#FEF3C7;border:1px solid #FDE68A;">
                                    {{ $usulan->catatan_keputusan }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end px-6 py-4" style="border-top:1px solid #E5E7EB;">
                        <button type="button" wire:click="closeModal" class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none" style="background-color:#FFFFFF;color:#6B7280;border:1.5px solid #D1D5DB;">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
