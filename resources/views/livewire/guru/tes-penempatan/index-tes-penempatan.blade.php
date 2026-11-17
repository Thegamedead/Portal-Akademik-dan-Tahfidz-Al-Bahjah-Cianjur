<div class="space-y-6">

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="rounded-xl p-4" style="background-color:#E8F5F1;border:1px solid #1B5E4F;">
            <p class="text-sm font-medium" style="color:#1B5E4F;">{{ session('success') }}</p>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="rounded-xl p-4" style="background-color:#FEE2E2;border:1px solid #9B3A3A;">
            <p class="text-sm font-medium" style="color:#9B3A3A;">{{ session('error') }}</p>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- PAGE HEADER --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Tes Penempatan
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Centang <strong>satu siswa</strong> berstatus Terjadwal, lalu klik <strong>Lakukan Tes</strong> untuk memulai pengisian hasil tes.
        </p>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- SEARCH & FILTER ROW --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

            {{-- LEFT: Bulk Action --}}
            <div>
                <button
                    wire:click="openBulkTestModal"
                    type="button"
                    class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    style="background-color:#1B5E4F;color:#FFFFFF;"
                    @if(count($selectedIds) !== 1) disabled @endif
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Lakukan Tes
                </button>
            </div>

            {{-- RIGHT: Search Field + Filter Dropdown Button --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-[400px]">
                {{-- Search Input --}}
                <div class="flex-1">
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari siswa (NIS, Nama)..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg text-sm transition-all focus:outline-none focus:ring-2"
                            style="border:1.5px solid #D1D5DB;color:#1A2422;"
                            onfocus="this.style.borderColor='#1B5E4F';this.style.boxShadow='0 0 0 3px rgba(27,94,79,0.1)';"
                            onblur="this.style.borderColor='#D1D5DB';this.style.boxShadow='none';"
                        />
                        <svg class="h-5 w-5 absolute left-3 top-1/2 -translate-y-1/2" style="color:#6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Filter Dropdown Button --}}
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        type="button"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none whitespace-nowrap cursor-pointer w-full sm:w-auto"
                        style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                        @if($filterStatus)
                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                1
                            </span>
                        @endif
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open"
                         @click.outside="open = false"
                         class="absolute z-50 right-0 mt-2 w-72 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                         style="display: none; border: 1px solid #E5E7EB;">

                        <div class="p-4 space-y-3">
                            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                                <h3 class="text-sm font-semibold" style="color:white;font-family:'Plus Jakarta Sans',sans-serif;">
                                    Filter Tes Penempatan
                                </h3>
                                @if($filterStatus)
                                    <button
                                        wire:click="resetFilters"
                                        type="button"
                                        class="text-xs font-medium text-gray-500 hover:text-[#1B5E4F] transition-colors"
                                    >
                                        Reset
                                    </button>
                                @endif
                            </div>

                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Status
                                </label>
                                <select
                                    wire:model.live="filterStatus"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="Terjadwal">Terjadwal</option>
                                    <option value="Selesai">Selesai</option>
                                    <option value="Dibatalkan">Dibatalkan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TABLE --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="background-color:#1B5E4F;">
                        <th class="px-6 py-4 w-12 text-center">
                            <span class="sr-only">Pilih</span>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Jadwal Tes</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Status Tes</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Rekomendasi / Penempatan</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold" style="color:white;">Hasil Tes</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    @forelse($tesPenempatans as $tes)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Checkbox --}}
                            <td class="px-6 py-4 text-center">
                                @if($tes->status?->value === 'Terjadwal')
                                    <input type="checkbox" wire:model.live="selectedIds" value="{{ $tes->id }}" class="rounded border-gray-300 text-[#1B5E4F] focus:ring-[#1B5E4F]">
                                @endif
                            </td>

                            {{-- Siswa --}}
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium" style="color:#1A2422;">{{ $tes->siswa->nama }}</div>
                                <div class="text-xs" style="color:#6B7280;">{{ $tes->siswa->nis }}</div>
                            </td>

                            {{-- Jadwal Tes --}}
                            <td class="px-6 py-4">
                                <div class="text-sm" style="color:#1A2422;">
                                    {{ $tes->tanggal_tes ? \Carbon\Carbon::parse($tes->tanggal_tes)->format('d M Y') : '-' }}
                                    @if($tes->waktu_tes)
                                        {{ \Carbon\Carbon::parse($tes->waktu_tes)->format('H:i') }}
                                    @endif
                                </div>
                            </td>

                            {{-- Status Tes --}}
                            <td class="px-6 py-4">
                                @if($tes->status?->value === 'Terjadwal')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color:#E8F5F1;color:#1B5E4F;">
                                        Belum (Terjadwal)
                                    </span>
                                @elseif($tes->status?->value === 'Selesai')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color:#FEF3C7;color:#92400E;">
                                        Sudah Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color:#FEE2E2;color:#9B3A3A;">
                                        Dibatalkan
                                    </span>
                                @endif
                            </td>

                            {{-- Rekomendasi / Penempatan --}}
                            <td class="px-6 py-4">
                                @if($tes->rekomendasi_tingkat)
                                    <div class="text-sm" style="color:#1A2422;">Rek: {{ $tes->rekomendasi_tingkat->value }}</div>
                                @endif
                                @if($tes->finalKelompok)
                                    <div class="text-xs font-medium" style="color:#1B5E4F;">Grup: {{ $tes->finalKelompok->nama }}</div>
                                @elseif($tes->ditempatkan_at)
                                    <div class="text-xs font-medium" style="color:#6B7280;">Tanpa Kelompok</div>
                                @else
                                    <div class="text-xs" style="color:#9CA3AF;">-</div>
                                @endif
                            </td>

                            {{-- Hasil Tes (Aksi) --}}
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                @if($tes->status?->value === 'Selesai')
                                    <a
                                        href="{{ route('guru.tes-penempatan.hasil', $tes->id) }}"
                                        class="inline-flex items-center text-sm font-medium transition-colors hover:underline"
                                        style="color:#1B5E4F;"
                                    >
                                        Lihat Hasil
                                    </a>
                                @else
                                    <span class="text-xs" style="color:#9CA3AF;">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        Tidak ada data tes penempatan
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($tesPenempatans->hasPages())
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                {{ $tesPenempatans->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL LAKUKAN TES --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if($showModalTes && $currentTes)
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="font-family:'Inter',sans-serif;">
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-transparent pointer-events-none" wire:click="closeTesModal"></div>

                <div class="relative transform overflow-hidden bg-white rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl">
                    <form wire:submit.prevent="submitTes">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[80vh] overflow-y-auto">
                            
                            {{-- Header Modal --}}
                            <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                                <div>
                                    <h3 class="text-lg font-bold" style="color:white;font-family:'Plus Jakarta Sans',sans-serif;">
                                        Lakukan Tes Penempatan
                                    </h3>
                                    <p class="text-sm mt-1" style="color:#6B7280;">
                                        Siswa: <strong>{{ $currentTes->siswa->nama }}</strong> ({{ $currentTes->siswa->nis }})
                                    </p>
                                </div>
                                <button type="button" wire:click="closeTesModal" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-6">
                                {{-- HASIL HAFALAN --}}
                                <div class="rounded-xl bg-gray-50 p-4 border border-gray-200">
                                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
                                        <h4 class="text-sm font-semibold" style="color:#1B5E4F;">Hasil Hafalan <span style="color:#9B3A3A;">*</span></h4>
                                        <button type="button" wire:click="addHafalan" class="text-xs font-medium hover:underline" style="color:#1B5E4F;">
                                            + Tambah Hafalan
                                        </button>
                                    </div>

                                    @foreach($hasil_hafalan as $index => $hasil)
                                        <div class="mb-4 pb-4 border-b border-gray-200 last:border-0 last:mb-0 last:pb-0">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-xs font-bold text-gray-500">Hafalan #{{ $index + 1 }}</span>
                                                @if(count($hasil_hafalan) > 1)
                                                    <button type="button" wire:click="removeHafalan({{ $index }})" class="text-xs text-red-600 hover:underline">Hapus</button>
                                                @endif
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs font-medium mb-1" style="color:#6B7280;">Juz <span style="color:#9B3A3A;">*</span></label>
                                                    <select wire:model.live="hasil_hafalan.{{ $index }}.juz_id" class="w-full px-3 py-2 text-sm rounded-lg border-gray-300 focus:ring-[#1B5E4F] focus:border-[#1B5E4F]">
                                                        <option value="">Pilih Juz</option>
                                                        @foreach($juzOptions as $juz)
                                                            <option value="{{ $juz['id'] }}">Juz {{ $juz['juz_ke'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error("hasil_hafalan.{$index}.juz_id") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-medium mb-1" style="color:#6B7280;">Surah <span style="color:#9B3A3A;">*</span></label>
                                                    <select wire:model.live="hasil_hafalan.{{ $index }}.surah_id" class="w-full px-3 py-2 text-sm rounded-lg border-gray-300 focus:ring-[#1B5E4F] focus:border-[#1B5E4F]" @if(empty($hasil['juz_id'])) disabled @endif>
                                                        <option value="">Pilih Surah</option>
                                                        @if(isset($surahOptions[$index]))
                                                            @foreach($surahOptions[$index] as $surah)
                                                                <option value="{{ $surah['id'] }}">{{ $surah['nomor_surah'] }}. {{ $surah['nama_latin'] }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @error("hasil_hafalan.{$index}.surah_id") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-medium mb-1" style="color:#6B7280;">Ayat Mulai <span style="color:#9B3A3A;">*</span></label>
                                                    <input type="number" wire:model="hasil_hafalan.{{ $index }}.ayat_mulai" min="1" class="w-full px-3 py-2 text-sm rounded-lg border-gray-300 focus:ring-[#1B5E4F] focus:border-[#1B5E4F]" placeholder="1">
                                                    @error("hasil_hafalan.{$index}.ayat_mulai") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-medium mb-1" style="color:#6B7280;">Ayat Selesai <span style="color:#9B3A3A;">*</span></label>
                                                    <input type="number" wire:model="hasil_hafalan.{{ $index }}.ayat_selesai" min="1" class="w-full px-3 py-2 text-sm rounded-lg border-gray-300 focus:ring-[#1B5E4F] focus:border-[#1B5E4F]" placeholder="10">
                                                    @error("hasil_hafalan.{$index}.ayat_selesai") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                                    @if(!empty($ayatRangeHints[$index]))
                                                        @php $hint = $ayatRangeHints[$index]; @endphp
                                                        <div class="mt-1 flex items-start gap-1 text-blue-600">
                                                            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            <span class="text-xs font-medium">{{ $hint['hint'] }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @error('hasil_hafalan') <span class="text-xs text-red-500 mt-2 block">{{ $message }}</span> @enderror
                                </div>

                                {{-- CATATAN KUALITAS & REKOMENDASI --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-2" style="color:#1A2422;">Catatan Kualitas <span style="color:#9B3A3A;">*</span></label>
                                        <textarea wire:model="catatan_kualitas" rows="4" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-[#1B5E4F] focus:border-[#1B5E4F]" placeholder="Kelancaran, tajwid..."></textarea>
                                        @error('catatan_kualitas') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-2" style="color:#1A2422;">Rekomendasi Tingkat <span style="color:#9B3A3A;">*</span></label>
                                        <div class="space-y-2">
                                            @foreach(['Dasar', 'Menengah', 'Atas'] as $val)
                                                <label class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors" :class="$wire.rekomendasi_tingkat === '{{ $val }}' ? 'border-[#1B5E4F] bg-[#E8F5F1]' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                                    <input type="radio" wire:model="rekomendasi_tingkat" value="{{ $val }}" class="text-[#1B5E4F] focus:ring-[#1B5E4F]">
                                                    <div class="ml-3">
                                                        <span class="block text-sm font-semibold text-gray-900">{{ $val }}</span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                        @error('rekomendasi_tingkat') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Modal --}}
                        <div class="bg-gray-50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                            <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-[#1B5E4F] text-sm font-medium text-white hover:bg-[#154a3e] focus:outline-none sm:ml-3 sm:w-auto" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submitTes">
                                    Simpan Tes
                                </span>
                                <span wire:loading wire:target="submitTes">Menyimpan...</span>
                            </button>
                            <button type="button" wire:click="closeTesModal" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
