<div class="space-y-6" style="font-family:'Inter',sans-serif;">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- PAGE HEADER --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Lakukan Tes Penempatan
                </h1>
                <p class="mt-2 text-sm" style="color:#6B7280;">
                    Input hasil hafalan siswa: <strong>{{ $siswa->nama }}</strong> ({{ $siswa->nis }})
                </p>
            </div>
            <button
                wire:click="cancel"
                type="button"
                class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
            >
                Kembali
            </button>
        </div>
    </div>

    <form wire:submit.prevent="submit">

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- INFO TES --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}

        <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;">
            <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
                Informasi Tes
            </h3>

            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                    Tanggal Tes
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <p class="text-sm" style="color:#1A2422;">{{ $tes->tanggal_tes->tgl() }}</p>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                    Waktu Tes
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <p class="text-sm" style="color:#1A2422;">
                        {{ $tes->waktu_tes ? $tes->waktu_tes->format('H:i') : '-' }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                    Siswa
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <p class="text-sm font-semibold" style="color:#1A2422;">{{ $siswa->nama }}</p>
                    <p class="text-xs" style="color:#6B7280;">NIS: {{ $siswa->nis }}</p>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- HASIL HAFALAN --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}

        <div class="rounded-2xl bg-white shadow-sm p-6 space-y-4" style="border:1px solid #E5E7EB;">
            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                    Hasil Hafalan <span style="color:#9B3A3A;">*</span>
                </h3>
                <button
                    type="button"
                    wire:click="addHafalan"
                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg transition-colors"
                    style="background-color:#1B5E4F;color:#FFFFFF;"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Hafalan
                </button>
            </div>

            @foreach($hasil_hafalan as $index => $hasil)
                <div class="p-4 rounded-xl space-y-3" style="border:1.5px solid #E5E7EB;background-color:#F9FAFB;">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-semibold" style="color:#1A2422;">Hafalan #{{ $index + 1 }}</h4>
                        @if(count($hasil_hafalan) > 1)
                            <button
                                type="button"
                                wire:click="removeHafalan({{ $index }})"
                                class="text-xs font-medium transition-colors"
                                style="color:#9B3A3A;"
                            >
                                Hapus
                            </button>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        {{-- Juz --}}
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Juz <span style="color:#9B3A3A;">*</span>
                            </label>
                            <select
                                wire:model.live="hasil_hafalan.{{ $index }}.juz_id"
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                style="border:1.5px solid #D1D5DB;color:#1A2422;"
                            >
                                <option value="">Pilih Juz</option>
                                @foreach($juzOptions as $juz)
                                    <option value="{{ $juz->id }}">Juz {{ $juz->juz_ke }}</option>
                                @endforeach
                            </select>
                            @error("hasil_hafalan.{$index}.juz_id")
                                <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Surah --}}
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Surah <span style="color:#9B3A3A;">*</span>
                            </label>
                            <select
                                wire:model.live="hasil_hafalan.{{ $index }}.surah_id"
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                @if(empty($hasil_hafalan[$index]['juz_id'])) disabled @endif
                            >
                                <option value="">{{ empty($hasil_hafalan[$index]['juz_id']) ? 'Pilih Juz dulu' : 'Pilih Surah' }}</option>
                                @if(isset($surahOptions[$index]))
                                    @foreach($surahOptions[$index] as $surah)
                                        <option value="{{ $surah->id }}">{{ $surah->nomor_surah }}. {{ $surah->nama_latin }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error("hasil_hafalan.{$index}.surah_id")
                                <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ayat Mulai --}}
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Ayat Mulai <span style="color:#9B3A3A;">*</span>
                            </label>
                            <input
                                type="number"
                                wire:model="hasil_hafalan.{{ $index }}.ayat_mulai"
                                min="1"
                                placeholder="Contoh: 1"
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                style="border:1.5px solid #D1D5DB;color:#1A2422;"
                            >
                            @error("hasil_hafalan.{$index}.ayat_mulai")
                                <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ayat Selesai --}}
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                Ayat Selesai <span style="color:#9B3A3A;">*</span>
                            </label>
                            <input
                                type="number"
                                wire:model="hasil_hafalan.{{ $index }}.ayat_selesai"
                                min="1"
                                placeholder="Contoh: 10"
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                style="border:1.5px solid #D1D5DB;color:#1A2422;"
                            >
                            @error("hasil_hafalan.{$index}.ayat_selesai")
                                <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                            @enderror
                            @if(!empty($ayatRangeHints[$index]))
                                @php $hint = $ayatRangeHints[$index]; @endphp
                                <div class="mt-1.5 flex items-start gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-xs text-blue-600 font-medium">{{ $hint['hint'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            @error('hasil_hafalan')
                <p class="text-xs" style="color:#9B3A3A;">{{ $message }}</p>
            @enderror
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- CATATAN KUALITAS --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}

        <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;">
            <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
                Catatan Kualitas Hafalan <span style="color:#9B3A3A;">*</span>
            </h3>

            <div>
                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                    Jelaskan kualitas hafalan siswa (Kelancaran, Tajwid, Makhraj, dll)
                </label>
                <textarea
                    wire:model="catatan_kualitas"
                    rows="5"
                    placeholder="Contoh: Hafalan lancar, tajwid baik, makhraj cukup jelas. Perlu perbaikan di beberapa ayat panjang..."
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                ></textarea>
                <div class="flex items-center justify-between mt-1">
                    @error('catatan_kualitas')
                        <p class="text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                    @else
                        <span></span>
                    @enderror
                    <span class="text-xs" style="color:#9CA3AF;">{{ strlen($catatan_kualitas) }}/1000</span>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- REKOMENDASI TINGKAT --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}

        <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;">
            <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
                Rekomendasi Tingkat <span style="color:#9B3A3A;">*</span>
            </h3>

            <div>
                <label class="block text-xs font-medium mb-3" style="color:#6B7280;">
                    Berdasarkan hasil tes, siswa direkomendasikan untuk tingkat:
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    {{-- Dasar --}}
                    <label class="flex items-center p-4 rounded-lg cursor-pointer transition-all border-2"
                           :class="$wire.rekomendasi_tingkat === 'Dasar' ? 'border-[#1B5E4F] bg-[#E8F5F1]' : 'border-gray-200 bg-white hover:border-gray-300'"
                    >
                        <input
                            type="radio"
                            wire:model="rekomendasi_tingkat"
                            value="Dasar"
                            class="w-4 h-4 cursor-pointer"
                            style="accent-color:#1B5E4F;"
                        >
                        <div class="ml-3">
                            <span class="block text-sm font-semibold" style="color:#1A2422;">Dasar</span>
                            <span class="block text-xs" style="color:#6B7280;">Untuk pemula / Juz 30</span>
                        </div>
                    </label>

                    {{-- Lanjutan --}}
                    <label class="flex items-center p-4 rounded-lg cursor-pointer transition-all border-2"
                           :class="$wire.rekomendasi_tingkat === 'Lanjutan' ? 'border-[#2E7D8C] bg-[#E0F2F7]' : 'border-gray-200 bg-white hover:border-gray-300'"
                    >
                        <input
                            type="radio"
                            wire:model="rekomendasi_tingkat"
                            value="Lanjutan"
                            class="w-4 h-4 cursor-pointer"
                            style="accent-color:#2E7D8C;"
                        >
                        <div class="ml-3">
                            <span class="block text-sm font-semibold" style="color:#1A2422;">Lanjutan</span>
                            <span class="block text-xs" style="color:#6B7280;">Untuk menengah / Juz 1-5</span>
                        </div>
                    </label>

                    {{-- Mahir --}}
                    <label class="flex items-center p-4 rounded-lg cursor-pointer transition-all border-2"
                           :class="$wire.rekomendasi_tingkat === 'Mahir' ? 'border-[#C9A227] bg-[#FEF3C7]' : 'border-gray-200 bg-white hover:border-gray-300'"
                    >
                        <input
                            type="radio"
                            wire:model="rekomendasi_tingkat"
                            value="Mahir"
                            class="w-4 h-4 cursor-pointer"
                            style="accent-color:#C9A227;"
                        >
                        <div class="ml-3">
                            <span class="block text-sm font-semibold" style="color:#1A2422;">Mahir</span>
                            <span class="block text-xs" style="color:#6B7280;">Untuk lanjut / Juz 29</span>
                        </div>
                    </label>
                </div>
                @error('rekomendasi_tingkat')
                    <p class="mt-2 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- FORM ACTIONS --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}

        <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
            <div class="flex justify-end gap-3">
                <button
                    wire:click="cancel"
                    type="button"
                    class="px-6 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                    style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-6 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                    style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove wire:target="submit">Simpan Hasil Tes</span>
                    <span wire:loading wire:target="submit">Menyimpan...</span>
                </button>
            </div>
        </div>

    </form>

</div>
