<div>
    @if($isOpen)
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-transparent pointer-events-none">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden pointer-events-auto" style="border: 1px solid #E5E7EB; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);" @click.outside="$wire.closeModal()">

            {{-- Modal Header --}}
            <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid #E5E7EB; background-color: #F9FAFB;">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-white" style="background-color: #9B3A3A;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold" style="color: #1A2422; font-family: 'Plus Jakarta Sans', sans-serif;">Rollover Tahun Ajaran Terintegrasi</h3>
                </div>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-5">

                {{-- Warning Box --}}
                <div class="p-4 rounded-lg" style="background-color: #FFF8F1; border: 1px solid #FFEDD5;">
                    <h4 class="text-sm font-semibold" style="color: #9A3412;">Peringatan Penting</h4>
                    <p class="mt-1 text-sm text-gray-700">
                        Rollover akan:
                    </p>
                    <ul class="mt-2 ml-4 text-sm text-gray-700 list-disc space-y-1">
                        <li><span class="font-bold">Mengaktifkan Tahun Ajaran baru</span></li>
                        <li><span class="font-bold">Menaikkan kelas siswa</span> (7→8, 8→9)</li>
                        <li><span class="font-bold">Meluluskan siswa Kelas 9</span></li>
                        <li><span class="font-bold text-red-600">Me-reset seluruh kelompok tahfidz</span> (mengosongkan kelompok untuk penempatan ulang)</li>
                    </ul>
                    <p class="mt-2 text-sm text-gray-700">
                        Pastikan semua aktivitas akademik di tahun ajaran sebelumnya sudah selesai.
                    </p>
                </div>

                {{-- Current and Target Tahun Ajaran Display --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 rounded-lg border border-gray-200 bg-gray-50 text-center">
                        <p class="text-xs text-gray-500 font-medium">Tahun Ajaran Saat Ini</p>
                        <p class="text-base font-bold text-gray-800 mt-1">{{ $currentTa ? $currentTa->label : 'Belum Ada' }}</p>
                    </div>
                    <div class="p-3 rounded-lg border border-gray-200 bg-blue-50 text-center">
                        <p class="text-xs text-blue-600 font-medium">Target Tahun Ajaran</p>
                        <p class="text-base font-bold text-blue-800 mt-1">
                            @if($impactSummary)
                                {{ $impactSummary['target_tahun_ajaran_label'] }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Tahun Ajaran Selector --}}
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: #374151;">Pilih Tahun Ajaran Tujuan</label>
                    <select
                        wire:model.live="targetTahunAjaranId"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                        style="border: 1.5px solid #D1D5DB; color: #1A2422;"
                        @if($isLoading) disabled @endif
                    >
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach($availableTa as $ta)
                            <option value="{{ $ta->id }}">
                                {{ $ta->label }} ({{ $ta->tanggal_mulai->format('M Y') }} - {{ $ta->tanggal_selesai->format('M Y') }})
                            </option>
                        @endforeach
                    </select>

                    @if($errorMessage)
                        <div class="mt-3 p-3 rounded-lg flex items-start gap-2 text-xs text-red-800 bg-red-50 border border-red-200">
                            <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <span class="font-bold block">Tidak dapat memproses rollover ke tahun ini:</span>
                                <span>{{ $errorMessage }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Impact Summary Table --}}
                @if($impactSummary)
                <div class="mt-4">
                    <h4 class="text-sm font-semibold mb-3" style="color: #1A2422;">Ringkasan Dampak Rollover</h4>

                    <div class="overflow-hidden rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead style="background-color: #F9FAFB;">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Jenis Perubahan
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Jumlah Siswa
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                {{-- Kelas 7 → 8 --}}
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                            </svg>
                                            <span>Kelas 7 → Kelas 8</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-right text-gray-900">
                                        {{ $impactSummary['kelas_7_to_8_count'] }} siswa
                                    </td>
                                </tr>

                                {{-- Kelas 8 → 9 --}}
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                            </svg>
                                            <span>Kelas 8 → Kelas 9</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-right text-gray-900">
                                        {{ $impactSummary['kelas_8_to_9_count'] }} siswa
                                    </td>
                                </tr>

                                {{-- Lulus SMP --}}
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                            </svg>
                                            <span>Lulus SMP</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-right text-gray-900">
                                        {{ $impactSummary['graduated_count'] }} siswa
                                    </td>
                                </tr>

                                {{-- Kelompok Cleared --}}
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <span>Kelompok Tahfidz Di-clear</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-right text-gray-900">
                                        {{ $impactSummary['kelompok_cleared_count'] }} siswa
                                    </td>
                                </tr>

                                {{-- Total --}}
                                <tr class="bg-gray-50 font-bold">
                                    <td class="px-4 py-3 text-sm text-gray-900 uppercase">
                                        Total Siswa Terdampak
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900">
                                        {{ $impactSummary['total_affected'] }} siswa
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Loading State Message --}}
                @if($isLoading && !$impactSummary)
                <div class="flex items-center justify-center py-4">
                    <svg class="animate-spin h-5 w-5 text-gray-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm text-gray-600">Menghitung dampak rollover...</span>
                </div>
                @endif

            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 flex justify-between items-center" style="border-top: 1px solid #E5E7EB; background-color: #F9FAFB;">

                {{-- Rollback Button (shown if recent rollover exists) --}}
                <div>
                    @if($canRollback && $mostRecentRollover)
                    <button
                        wire:click="executeRollback"
                        type="button"
                        class="inline-flex items-center justify-center px-4 py-2 text-xs font-medium rounded-lg transition-colors focus:outline-none"
                        style="background-color: #F59E0B; color: #FFFFFF; border: 1.5px solid #F59E0B;"
                        wire:loading.attr="disabled"
                        title="Rollback rollover terakhir ({{ $mostRecentRollover->completed_at->diffForHumans() }})"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Rollback
                    </button>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-3">
                    <button
                        wire:click="closeModal"
                        type="button"
                        class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none"
                        style="background-color: #9B3A3A; border: 1.5px solid #9B3A3A;"
                        wire:loading.attr="disabled"
                    >
                        Batal
                    </button>
                    <button
                        wire:click="confirmRollover"
                        type="button"
                        class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                        style="background-color: #1B5E4F; border: 1.5px solid #1B5E4F;"
                        wire:loading.attr="disabled"
                        @if(!$impactSummary) disabled @endif
                    >
                        <span wire:loading.remove wire:target="confirmRollover">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Konfirmasi Rollover
                        </span>
                        <span wire:loading wire:target="confirmRollover">
                            <svg class="animate-spin h-4 w-4 mr-2 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>

            {{-- Loading Spinner Overlay --}}
            @if($isLoading)
            <div class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-2xl z-10">
                <div class="text-center">
                    <svg class="animate-spin h-12 w-12 text-gray-600 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-sm font-medium text-gray-700">Sedang memproses rollover...</p>
                    <p class="text-xs text-gray-500 mt-1">Mohon tunggu, jangan tutup halaman ini</p>
                </div>
            </div>
            @endif

        </div>
    </div>
    @endif

    {{-- Alpine.js Rollback Confirmation Script --}}
    @script
    <script>
        // Listen for rollback confirmation event from Livewire
        $wire.on('confirm-rollback', (event) => {
            const data = event[0];

            // Display confirmation dialog
            if (confirm(
                `⚠️ PERINGATAN: Rollback akan mengembalikan sistem ke tahun ajaran sebelumnya.\n\n` +
                `Detail Rollover yang akan di-rollback:\n` +
                `• Dari: ${data.sourceTahunAjaran} → Ke: ${data.targetTahunAjaran}\n` +
                `• Dilakukan: ${data.completedAt}\n` +
                `• Total siswa terdampak: ${data.totalAffected} siswa\n\n` +
                `Perubahan yang akan di-revert:\n` +
                `• Kelas 7→8: ${data.kelas7To8Count} siswa akan kembali ke Kelas 7\n` +
                `• Kelas 8→9: ${data.kelas8To9Count} siswa akan kembali ke Kelas 8\n` +
                `• Lulus: ${data.graduatedCount} siswa akan kembali menjadi Aktif\n` +
                `• Kelompok: ${data.kelompokClearedCount} siswa akan dipulihkan ke kelompok lama\n\n` +
                `Apakah Anda yakin ingin melanjutkan rollback?`
            )) {
                // User confirmed, trigger Livewire method
                $wire.dispatch('confirm-rollback-execution', { rolloverLogId: data.rolloverLogId });
            }
        });
    </script>
    @endscript
</div>
