<div class="space-y-6" style="font-family:'Inter',sans-serif;">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- PAGE HEADER --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Review & Penempatan Siswa
                </h1>
                <p class="mt-1 text-sm" style="color:#6B7280;">
                    Review hasil tes penempatan dan tentukan kelompok untuk siswa baru.
                </p>
            </div>
            <a
                href="{{ route('admin.tes-penempatan.index') }}"
                class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors"
                style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- KOLOM KIRI: Info & Hasil Hafalan --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Informasi Siswa --}}
            <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;">
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                    <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                        Informasi Siswa
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <dt class="text-xs font-medium" style="color:#6B7280;">Nama Lengkap</dt>
                            <dd class="mt-1 text-sm font-semibold" style="color:#1A2422;">{{ $tes->siswa->nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium" style="color:#6B7280;">NIS / NISN</dt>
                            <dd class="mt-1 text-sm" style="color:#1A2422;">{{ $tes->siswa->nis }} / {{ $tes->siswa->nisn ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium" style="color:#6B7280;">Guru Penguji</dt>
                            <dd class="mt-1 text-sm" style="color:#1A2422;">{{ $tes->guruPenguji->nama }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium" style="color:#6B7280;">Waktu Pelaksanaan</dt>
                            <dd class="mt-1 text-sm" style="color:#1A2422;">{{ $tes->tanggal_tes->tgl() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Analisis Hafalan --}}
            <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;">
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                    <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                        Hasil Analisis Hafalan
                    </h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" style="background-color:#E8F5F1;color:#1B5E4F;">
                        {{ $baselineData['total_ayat'] ?? 0 }} Ayat Dikuasai
                    </span>
                </div>

                <div class="px-6 py-5 space-y-5">
                    {{-- Juz Tuntas --}}
                    <div>
                        <h4 class="text-xs font-semibold mb-2" style="color:#6B7280;text-transform:uppercase;letter-spacing:.05em;">
                            Juz yang Telah Tuntas (100%)
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            @forelse($baselineData['complete_juzs'] ?? [] as $juzId)
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium" style="background-color:#FEF3C7;color:#92400E;border:1px solid #FDE68A;">
                                    Juz {{ $juzId }}
                                </span>
                            @empty
                                <span class="text-sm italic" style="color:#9CA3AF;">Belum ada juz yang tuntas 100%.</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- Rincian Per Juz --}}
                    <div>
                        <h4 class="text-xs font-semibold mb-3" style="color:#6B7280;text-transform:uppercase;letter-spacing:.05em;">
                            Rincian Capaian Per Juz
                        </h4>
                        <div class="overflow-x-auto rounded-xl" style="border:1px solid #E5E7EB;">
                            <table class="min-w-full">
                                <thead>
                                    <tr style="background-color:#1B5E4F;">
                                        <th class="px-4 py-3 text-left text-xs font-semibold" style="color:white;">Juz</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold" style="color:white;">Ayat Dikuasai</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold" style="color:white;">Total Ayat</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold" style="color:white;">Capaian</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y" style="border-color:#E5E7EB;">
                                    @forelse($baselineData['juzs_covered'] ?? [] as $juzId => $data)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 text-sm font-medium" style="color:#1A2422;">Juz {{ $juzId }}</td>
                                            <td class="px-4 py-3 text-sm" style="color:#374151;">{{ $data['ayat_covered'] }}</td>
                                            <td class="px-4 py-3 text-sm" style="color:#374151;">{{ $data['total_ayat'] }}</td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1 rounded-full h-2" style="background-color:#E5E7EB;">
                                                        <div class="h-2 rounded-full" style="width:{{ $data['percentage'] }}%;background-color:#1B5E4F;"></div>
                                                    </div>
                                                    <span class="text-xs font-medium w-10 text-right" style="color:#374151;">{{ $data['percentage'] }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-6 text-sm text-center" style="color:#9CA3AF;">Tidak ada hafalan tercatat.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Catatan Kualitas & Rekomendasi Guru --}}
            <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;">
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                    <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                        Catatan Kualitas & Rekomendasi Guru
                    </h3>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <span class="text-xs font-medium block mb-1.5" style="color:#6B7280;">Rekomendasi Tingkat</span>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold" style="background-color:#DBEAFE;color:#1E40AF;">
                            {{ $tes->rekomendasi_tingkat?->value ?? 'Tidak ada' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-xs font-medium block mb-1.5" style="color:#6B7280;">Catatan Penguji</span>
                        <div class="p-4 rounded-xl text-sm whitespace-pre-wrap" style="background-color:#F9FAFB;border:1px solid #E5E7EB;color:#374151;">{{ $tes->catatan_kualitas ?: 'Tidak ada catatan.' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- KOLOM KANAN: Keputusan Penempatan --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-1">
            <div class="rounded-2xl bg-white shadow-sm overflow-hidden sticky top-6" style="border:1.5px solid #1B5E4F;">
                <div class="px-6 py-4" style="background-color:#1B5E4F;">
                    <h3 class="text-base font-semibold" style="color:white;font-family:'Plus Jakarta Sans',sans-serif;">
                        Keputusan Penempatan
                    </h3>
                    <p class="text-xs mt-0.5" style="color:#A7F3D0;">Pilih tingkat dan kelompok untuk siswa ini.</p>
                </div>

                <form wire:submit.prevent="submitPlacement" class="p-6 space-y-5">

                    {{-- Pilih Tingkat --}}
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color:#374151;">Tingkat Kelompok</label>
                        <select wire:model.live="selectedTingkat" class="w-full px-3 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all" style="border:1.5px solid #D1D5DB;color:#1A2422;">
                            <option value="Dasar">Dasar</option>
                            <option value="Menengah">Menengah</option>
                            <option value="Atas">Atas</option>
                        </select>
                        <p class="mt-1 text-xs" style="color:#9CA3AF;">Secara default mengikuti rekomendasi guru.</p>
                    </div>

                    {{-- Tanggal Penempatan --}}
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color:#374151;">
                            Tanggal Penempatan <span style="color:#9B3A3A;">*</span>
                        </label>
                        <input
                            type="date"
                            wire:model="tanggal_penempatan"
                            class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-[#1B5E4F] transition-all"
                            style="color:#1A2422;"
                        >
                        @error('tanggal_penempatan')
                            <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pilih Kelompok --}}
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color:#374151;">
                            Pilih Kelompok <span style="color:#9B3A3A;">*</span>
                        </label>
                        <select wire:model="kelompok_id" class="w-full px-3 py-2.5 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all" style="border:1.5px solid #D1D5DB;color:#1A2422;">
                            <option value="">-- Pilih Kelompok --</option>
                            @forelse($availableKelompoks as $kel)
                                <option value="{{ $kel->id }}">
                                    {{ $kel->nama }} (Sisa: {{ $kel->kapasitas_maks - $kel->siswas_count }})
                                </option>
                            @empty
                                <option value="" disabled>Tidak ada kelompok tersedia</option>
                            @endforelse
                        </select>
                        @if($availableKelompoks->isEmpty())
                            <p class="mt-1.5 text-xs" style="color:#9B3A3A;">
                                Tidak ada kelompok tingkat "{{ $selectedTingkat }}" yang tersedia atau masih memiliki kuota.
                            </p>
                        @endif
                        @error('kelompok_id') <span class="text-xs mt-1 block" style="color:#9B3A3A;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Peringatan --}}
                    <div class="rounded-xl p-4" style="background-color:#FFFBEB;border:1px solid #FDE68A;">
                        <div class="flex gap-3">
                            <svg class="h-5 w-5 shrink-0 mt-0.5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-xs" style="color:#92400E;">
                                Menempatkan siswa akan menghitung Baseline Progres Hafalan berdasarkan tes ini. Pastikan data sudah benar.
                            </p>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full flex justify-center items-center py-2.5 px-4 rounded-lg text-sm font-semibold text-white transition-colors"
                        style="background-color:#1B5E4F;"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="submitPlacement">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                            Simpan & Tempatkan Siswa
                        </span>
                        <span wire:loading wire:target="submitPlacement">Menyimpan...</span>
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>
