<div class="p-6 h-[calc(100vh-(--spacing(16)))] flex flex-col md:flex-row gap-6">
    {{-- Sidebar: List Siswa Hadir --}}
    <div class="w-full md:w-1/4 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col max-h-full">
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <h2 class="font-semibold text-tinta">Siswa Hadir</h2>
            <p class="text-xs text-gray-500 mt-1">{{ count(array_filter($siswaHadir->toArray(), fn($s) => $s['is_completed'])) }} dari {{ count($siswaHadir) }} selesai</p>
        </div>
        <div class="overflow-y-auto flex-1 p-2 space-y-1">
            @foreach($siswaHadir as $siswa)
                <button
                    wire:click="navigateToSiswa({{ $siswa['siswa_id'] }})"
                    class="w-full text-left p-3 rounded-lg flex items-center justify-between transition-colors duration-150 {{ $currentSiswaId === $siswa['siswa_id'] ? 'bg-zamrud/10 border border-zamrud/30' : 'hover:bg-gray-100 border border-transparent' }}"
                >
                    <div class="flex flex-col">
                        <span class="text-sm font-medium {{ $currentSiswaId === $siswa['siswa_id'] ? 'text-zamrud' : 'text-gray-900' }}">
                            {{ $siswa['nama'] }}
                        </span>
                        <span class="text-xs text-gray-500">{{ $siswa['nis'] }}</span>
                    </div>
                    @if($siswa['is_completed'])
                        <svg class="w-5 h-5 text-zamrud" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- Main Area: Form Input Setoran --}}
    <div class="w-full md:w-3/4 flex flex-col max-h-full">
        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-4 bg-zamrud/10 border border-zamrud rounded-lg p-4">
                <p class="text-sm text-zamrud">{{ session('success') }}</p>
            </div>
        @endif

        {{-- TODO: Toast notifications handled in Livewire component --}}

        @php
            $currentSiswa = collect($siswaHadir)->firstWhere('siswa_id', $currentSiswaId);
        @endphp

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 flex-1 flex flex-col overflow-hidden">
            <div class="p-4 md:p-6 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-tinta">Setoran Hafalan: {{ $currentSiswa['nama'] ?? '' }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Tanggal: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }} •
                        Kelompok: {{ $kelompok->nama }} •
                        Guru: {{ $kelompok->guruPembimbing->nama_lengkap ?? '-' }}
                    </p>
                </div>
            </div>

            <div class="p-4 md:p-6 overflow-y-auto flex-1">
                @foreach($entries as $index => $entry)
                    <div class="mb-8 relative border border-gray-200 rounded-xl p-5 bg-white shadow-sm">
                        {{-- Delete Entry Button --}}
                        @if(count($entries) > 1)
                            <button type="button" wire:click="removeEntry({{ $index }})" class="absolute -top-3 -right-3 bg-white text-delima hover:bg-delima hover:text-white border border-delima rounded-full p-1.5 transition-colors shadow-sm"
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        @endif

                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                            <span class="bg-gray-100 text-gray-700 text-xs font-bold px-2.5 py-1 rounded-md">Entri #{{ $index + 1 }}</span>
                        </div>

                        {{-- Section 1: Informasi Surah --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Setoran <span class="text-delima">*</span></label>
                                <select wire:model.live="entries.{{ $index }}.jenis_setoran" class="w-full rounded-md border-gray-300 shadow-sm focus:border-zamrud focus:ring focus:ring-zamrud focus:ring-opacity-50 text-sm cursor-pointer">
                                    <option value="Hafalan Baru">Hafalan Baru</option>
                                    <option value="Ulang">Ulang</option>
                                </select>
                                @error("entries.{$index}.jenis_setoran") <span class="text-delima text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Juz <span class="text-delima">*</span></label>
                                <select wire:model.live="entries.{{ $index }}.juz_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-zamrud focus:ring focus:ring-zamrud focus:ring-opacity-50 text-sm cursor-pointer">
                                    <option value="">-- Pilih Juz --</option>
                                    @foreach($juzs as $juz)
                                        <option value="{{ $juz->id }}">Juz {{ $juz->juz_ke }}</option>
                                    @endforeach
                                </select>
                                @error("entries.{$index}.juz_id") <span class="text-delima text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Surah <span class="text-delima">*</span></label>
                                    <select wire:model.live="entries.{{ $index }}.surah_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-zamrud focus:ring focus:ring-zamrud focus:ring-opacity-50 text-sm cursor-pointer" {{ empty($entry['juz_id']) ? 'disabled' : '' }}>
                                        <option value="">-- Pilih Surah --</option>
                                        @foreach($surahsOptions[$index] ?? [] as $surah)
                                            <option value="{{ $surah->id }}">{{ $surah->nomor }}. {{ $surah->nama_latin }} ({{ $surah->jumlah_ayat }} ayat)</option>
                                        @endforeach
                                    </select>
                                    @error("entries.{$index}.surah_id") <span class="text-delima text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ayat Mulai <span class="text-delima">*</span></label>
                                    <input type="number" wire:model.live="entries.{{ $index }}.ayat_mulai" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-zamrud focus:ring focus:ring-zamrud focus:ring-opacity-50 text-sm cursor-pointer">
                                    @error("entries.{$index}.ayat_mulai") <span class="text-delima text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ayat Selesai <span class="text-delima">*</span></label>
                                    <input type="number" wire:model.live="entries.{{ $index }}.ayat_selesai" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-zamrud focus:ring focus:ring-zamrud focus:ring-opacity-50 text-sm cursor-pointer">
                                    @error("entries.{$index}.ayat_selesai") <span class="text-delima text-xs mt-1">{{ $message }}</span> @enderror
                                    @if(!empty($ayatRangeHints[$index]))
                                        @php $hint = $ayatRangeHints[$index]; @endphp
                                        <div class="mt-1.5 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-xs text-blue-600 font-medium">{{ $hint['hint'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Penilaian --}}
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 mb-4">
                            <h3 class="text-sm font-semibold text-gray-800 mb-3">Komponen Penilaian</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach(['tajwid', 'kelancaran', 'makhraj', 'fashahah'] as $komponen)
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1 capitalize">{{ $komponen }}</label>
                                        <select wire:model.live="entries.{{ $index }}.skor_{{ $komponen }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-zamrud focus:ring focus:ring-zamrud focus:ring-opacity-50 text-xs py-1.5 cursor-pointer">
                                            <option value="Lancar">Lancar</option>
                                            <option value="Kurang Lancar">Kurang Lancar</option>
                                            <option value="Tidak Lancar">Tidak Lancar</option>
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Section 3: Hasil & Keputusan --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white rounded-lg p-4 border border-gray-200 flex flex-col justify-center items-center">
                                <span class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Predikat</span>
                                @php
                                    $predikat = $entry['predikat'] ?? 'Lancar';
                                    $badgeColor = match($predikat) {
                                        'Lancar' => 'bg-zamrud/10 text-zamrud border-zamrud/20',
                                        'Kurang Lancar' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'Tidak Lancar' => 'bg-delima/10 text-delima border-delima/20',
                                        default => 'bg-gray-100 text-gray-800 border-gray-200',
                                    };
                                @endphp
                                <span class="px-4 py-1.5 rounded-full text-sm font-bold border {{ $badgeColor }}">
                                    {{ $predikat }}
                                </span>
                                <p class="text-[10px] text-gray-400 mt-2 text-center">Dihitung otomatis dari nilai terendah komponen</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keputusan <span class="text-delima">*</span></label>
                                <select wire:model.live="entries.{{ $index }}.keputusan" class="w-full rounded-md border-gray-300 shadow-sm focus:border-zamrud focus:ring focus:ring-zamrud focus:ring-opacity-50 text-sm cursor-pointer">
                                    <option value="Lanjut">Lanjut</option>
                                    <option value="Ulang">Ulang</option>
                                </select>
                                @error("entries.{$index}.keputusan") <span class="text-delima text-xs mt-1">{{ $message }}</span> @enderror

                                <div class="mt-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                                    <textarea wire:model.defer="entries.{{ $index }}.catatan" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-zamrud focus:ring focus:ring-zamrud focus:ring-opacity-50 text-sm" placeholder="Contoh: Perbaiki makhraj huruf dho' cursor-pointer"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <button type="button" wire:click="addEntry" class="mt-2 flex items-center justify-center w-full py-3 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:border-zamrud hover:text-zamrud transition-colors cursor-pointer">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Entri Setoran Lain
                </button>
            </div>

            <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                <button type="button" wire:click="submit" class="px-6 py-2.5 bg-zamrud hover:bg-zamrud/90 text-white rounded-lg font-medium transition-colors shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-zamrud w-full md:w-auto">
                    Simpan Setoran
                </button>
            </div>
        </div>
    </div>

    {{-- Confirmation Dialog --}}
    @if($showConfirmDialog)
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 pointer-events-none" wire:click="cancelSave"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-zamrud/10 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-zamrud" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Konfirmasi Setoran
                                </h3>
                                <div class="mt-2 text-sm text-gray-500">
                                    <p>Anda akan menyimpan <strong>{{ count($entries) }} entri setoran</strong> untuk santri <strong>{{ $currentSiswa['nama'] ?? '' }}</strong>.</p>
                                    @if($isOutsideEditWindow)
                                        <p class="mt-2 text-[#C9A227] font-semibold">⚠️ Edit sebagai Admin (Override) - akan tercatat di Audit Log</p>
                                    @endif
                                    <p class="mt-1">Apakah data yang dimasukkan sudah benar?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button wire:click="save" type="button" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-zamrud text-base font-medium text-white hover:bg-zamrud/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zamrud sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Ya, Simpan
                        </button>
                        <button wire:click="cancelSave" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto sm:text-sm cursor-pointer transition-colors">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
