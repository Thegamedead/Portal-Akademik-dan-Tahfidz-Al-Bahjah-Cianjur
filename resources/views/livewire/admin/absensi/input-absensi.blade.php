<div>
    @if($isOpen)
        {{-- Custom styles for date picker indicators --}}
        @if($isFlexibleDate)
            <style>
                /* Style for filled dates (green dot) */
                @foreach($filledDates as $date)
                    input[type="date"][value="{{ $date }}"]::-webkit-calendar-picker-indicator {
                        filter: opacity(0.8);
                    }
                @endforeach

                /* Note: Native date pickers don't support custom styling per date easily.
                   We'll use a visual indicator below the input instead */
            </style>
        @endif

        <div class="fixed inset-0 z-40 flex items-center justify-center p-4 sm:p-6 pointer-events-none"
             x-data="{ show: false }"
             x-init="setTimeout(() => show = true, 50)"
             x-show="show"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <div class="w-full max-w-4xl max-h-full pointer-events-auto bg-gray-50 flex flex-col rounded-2xl border border-gray-200 relative shadow-2xl overflow-hidden">

                {{-- Header --}}
                <div class="px-6 py-4 bg-white border-b border-gray-200 flex items-center justify-between shrink-0">
                    <div>
                        <h2 class="text-xl font-bold text-[#1A2422] font-jakarta">Absensi dan Setoran Hafalan</h2>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-sm font-semibold text-gray-700">{{ $kelompok->nama ?? '' }}</span>
                            <span class="text-gray-300">•</span>
                            <span class="flex items-center gap-1.5 text-sm font-medium text-gray-500">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ $kelompok->guruPembimbing->nama ?? 'Belum ditentukan' }}
                            </span>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 border border-gray-200 p-1.5 shadow-sm transition-colors hover:bg-gray-50">
                        <span class="sr-only">Tutup</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Scrollable Content --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-6">

                    {{-- Info fields row --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="flex items-stretch divide-x divide-gray-100">
                            {{-- Tanggal --}}
                            <div class="px-6 py-4">
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Tanggal</p>
                                @if($isFlexibleDate)
                                    {{-- Flexible date picker with clean design --}}
                                    <div class="relative">
                                        <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 shadow-sm cursor-pointer hover:border-gray-300 transition-colors">
                                            <svg class="h-4 w-4 text-zamrud flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-sm font-semibold text-[#1A2422] flex-1">
                                                {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y') }}
                                            </span>
                                            <svg class="h-3.5 w-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                        <input type="date"
                                               wire:model.live="tanggal"
                                               onclick="this.showPicker()"
                                               class="absolute inset-0 opacity-0 cursor-pointer z-10 w-full h-full">

                                        {{-- Status indicator for selected date --}}
                                        <div class="mt-2">
                                            @if(in_array($tanggal, $liburDates))
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                                    Tanggal Libur
                                                </span>
                                            @elseif(in_array($tanggal, $filledDates))
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-100 text-green-700 border border-green-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                    Sudah Terisi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                    Belum Terisi
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    {{-- Fixed date display --}}
                                    <p class="text-sm font-semibold text-[#1A2422] pt-1">
                                        {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y') }}
                                    </p>
                                @endif
                            </div>

                            {{-- Hari --}}
                            <div class="px-6 py-4">
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1.5">Hari</p>
                                <p class="text-sm font-semibold text-[#1A2422] pt-1">
                                    {{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l') }}
                                </p>
                            </div>

                            {{-- Waktu --}}
                            <div class="px-6 py-4">
                                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-2">Waktu</p>
                                <div class="flex items-center gap-2">
                                    <input type="time"
                                           wire:model="waktuMulai"
                                           step="60"
                                           pattern="[0-9]{2}:[0-9]{2}"
                                           class="text-sm font-semibold text-[#1A2422] border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F]/30 focus:border-[#1B5E4F] bg-white">
                                    <span class="text-gray-400 text-sm">—</span>
                                    <input type="time"
                                           wire:model="waktuSelesai"
                                           step="60"
                                           pattern="[0-9]{2}:[0-9]{2}"
                                           class="text-sm font-semibold text-[#1A2422] border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#1B5E4F]/30 focus:border-[#1B5E4F] bg-white">
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(is_null($targetSiswaId))
                        {{-- Toggle Hari Libur --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center justify-between"
                             style="{{ $isLiburPilihan ? 'border-color:#7c3aed;background:#faf5ff;' : '' }}">
                            <div>
                                <h3 class="text-sm font-bold" style="{{ $isLiburPilihan ? 'color:#6d28d9;' : 'color:#111827;' }}">
                                    {{ $isLiburPilihan ? '🗓️ Hari Libur Aktif' : 'Tandai sebagai Hari Libur' }}
                                </h3>
                                <p class="text-xs mt-0.5" style="color:#6b7280;">
                                    Jika diaktifkan, sesi ini akan ditandai libur untuk semua siswa dan tidak ada setoran.
                                </p>
                            </div>
                            {{-- Custom toggle button --}}
                            <button
                                type="button"
                                wire:click="toggleLibur"
                                {{ $isMinggu ? 'disabled' : '' }}
                                title="{{ $isMinggu ? 'Hari Minggu otomatis libur' : ($isLiburPilihan ? 'Klik untuk nonaktifkan libur' : 'Klik untuk tandai libur') }}"
                                style="
                                    position: relative;
                                    display: inline-flex;
                                    align-items: center;
                                    width: 52px;
                                    height: 28px;
                                    border-radius: 9999px;
                                    border: none;
                                    cursor: {{ $isMinggu ? 'not-allowed' : 'pointer' }};
                                    transition: background-color 0.2s ease;
                                    background-color: {{ $isLiburPilihan ? '#7c3aed' : '#d1d5db' }};
                                    opacity: {{ $isMinggu ? '0.5' : '1' }};
                                    flex-shrink: 0;
                                "
                            >
                                <span style="
                                    position: absolute;
                                    top: 3px;
                                    left: {{ $isLiburPilihan ? '27px' : '3px' }};
                                    width: 22px;
                                    height: 22px;
                                    border-radius: 9999px;
                                    background: white;
                                    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                                    transition: left 0.2s ease;
                                "></span>
                            </button>
                        </div>
                    @endif

                    {{-- Form will be rendered below this line --}}
                    {{-- Flash Messages --}}
                    @if (session()->has('success'))
                        <div class="bg-zamrud/10 border border-zamrud rounded-lg p-4">
                            <p class="text-sm text-zamrud">{{ session('success') }}</p>
                        </div>
                    @endif

                    {{-- Libur Banner (tampil jika hari libur diaktifkan) --}}
                    @if($isMinggu)
                        <div class="flex flex-col items-center justify-center gap-3 bg-red-50 border border-red-200 rounded-xl p-8 text-center">
                            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <div>
                                <p class="text-base font-bold text-red-800">Hari Minggu</p>
                                <p class="text-sm text-red-600 mt-1">Hari Minggu adalah hari libur. Tidak ada kegiatan tahfidz.</p>
                            </div>
                            <p class="text-xs text-red-500">Pilih tanggal lain untuk mengisi absensi.</p>
                        </div>
                    @elseif($isLiburPilihan)
                        <div class="flex flex-col items-center justify-center gap-3 bg-purple-50 border border-purple-200 rounded-xl p-8 text-center">
                            <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center">
                                <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-base font-bold text-purple-800">Hari Libur</p>
                                <p class="text-sm text-purple-600 mt-1">Tanggal ini ditandai sebagai hari libur. Semua siswa tercatat <strong>Libur</strong> dan tidak ada setoran hafalan.</p>
                            </div>
                            <p class="text-xs text-purple-500">Matikan toggle di atas untuk mengisi absensi normal.</p>
                        </div>
                    @else

                    {{-- Absensi Table --}}
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        {{-- Toolbar --}}
                        <div class="px-5 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between gap-3">
                            <p class="text-sm text-gray-500">
                                Total: <strong class="text-gray-800">{{ $this->activeSiswas->count() }} siswa</strong>
                            </p>
                            @if(!$isLiburPilihan && !$isMinggu && is_null($targetSiswaId))
                                <button wire:click="hadirSemua"
                                        type="button"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-zamrud hover:bg-zamrud/90 text-white text-sm font-medium rounded-lg transition-colors shadow-sm active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Hadir Semua
                                </button>
                            @endif
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#1B5E4F] text-white">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider w-10">No</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Siswa</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider">Status Kehadiran</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Hasil Setoran</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider">Aksi Setoran</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($this->activeSiswas as $index => $siswa)
                                        @php
                                            $statusSiswa = $absensis[$siswa->id] ?? '';
                                            $sudahHadir  = $statusSiswa === 'Hadir';
                                            $targetSelesai = $siswaCompletedStatus[$siswa->id] ?? false;
                                        @endphp
                                        <tr class="hover:bg-gray-50 transition-colors {{ $targetSelesai ? 'bg-green-50/30' : '' }}">
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                <div class="flex items-center gap-2">
                                                    <div>
                                                        {{ $siswa->nama }}<br>
                                                        <span class="text-xs text-gray-500 font-normal font-mono">{{ $siswa->nis }}</span>
                                                    </div>
                                                    @if($targetSelesai)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold text-green-700 bg-green-100 border border-green-300 rounded-full whitespace-nowrap" title="Siswa ini sudah menyelesaikan seluruh target hafalan kelompok. Tidak dapat input absensi/setoran lagi. Proses kelulusan di menu Progres Siswa.">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            Tuntas
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Status Dropdown --}}
                                            <td class="px-4 py-3 text-center">
                                                @if($targetSelesai)
                                                    <div class="text-center" title="Siswa sudah menyelesaikan seluruh target hafalan kelompok. Input absensi/setoran dinonaktifkan. Untuk proses kelulusan, buka menu Progres Siswa.">
                                                        <span class="inline-block px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-100 border border-green-300 rounded-md cursor-help">
                                                            Target Selesai ✓
                                                        </span>
                                                        <p class="text-[10px] text-gray-500 mt-1">Proses kelulusan di menu Progres Siswa</p>
                                                    </div>
                                                @else
                                                    <select wire:model.live="absensis.{{ $siswa->id }}"
                                                            class="block w-full min-w-[110px] pl-3 pr-8 py-1.5 text-sm border-gray-300 focus:outline-none focus:ring-zamrud focus:border-zamrud rounded-md shadow-sm
                                                                   {{ match($statusSiswa) {
                                                                        'Hadir' => 'bg-green-50 text-green-800 border-green-300',
                                                                        'Izin'  => 'bg-blue-50 text-blue-800 border-blue-300',
                                                                        'Sakit' => 'bg-yellow-50 text-yellow-800 border-yellow-300',
                                                                        'Alfa'  => 'bg-red-50 text-red-800 border-red-300',
                                                                        'Libur' => 'bg-purple-50 text-purple-800 border-purple-300',
                                                                        default => 'bg-gray-50 text-gray-500 border-gray-300',
                                                                   } }}"
                                                            {{ $isLiburPilihan || $isMinggu ? 'disabled' : '' }}>
                                                        <option value="">-- Pilih --</option>
                                                        <option value="Hadir">Hadir</option>
                                                        <option value="Izin">Izin</option>
                                                        <option value="Sakit">Sakit</option>
                                                        <option value="Alfa">Alfa</option>
                                                        <option value="Libur">Libur</option>
                                                    </select>
                                                @endif
                                            </td>

                                            {{-- Hasil Setoran --}}
                                            <td class="px-4 py-3 text-sm">
                                                @if($targetSelesai)
                                                    <div class="text-center" title="Seluruh target hafalan untuk kelompok ini sudah diselesaikan oleh siswa. Tidak ada setoran baru yang dapat diinput.">
                                                        <span class="text-xs text-green-600 font-semibold cursor-help">✓ Semua target selesai</span>
                                                    </div>
                                                @elseif(isset($setoranSiswa[$siswa->id]) && count($setoranSiswa[$siswa->id]) > 0)
                                                    <div class="space-y-1">
                                                        @foreach($setoranSiswa[$siswa->id] as $setoran)
                                                            <div class="text-[11px] text-gray-700 font-medium">
                                                                {{ $setoran['surah_awal'] ?? $setoran['surah_id'] }}: {{ $setoran['ayat_awal'] ?? $setoran['ayat_mulai'] }}-{{ $setoran['ayat_akhir'] ?? $setoran['ayat_selesai'] }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-[11px] text-gray-400 italic">Kosong</span>
                                                @endif
                                            </td>

                                            {{-- Tombol Setoran Hafalan --}}
                                            <td class="px-4 py-3 text-center">
                                                @if($targetSelesai)
                                                    <span class="text-xs text-gray-400 italic cursor-help" title="Input setoran tidak tersedia karena siswa sudah menyelesaikan seluruh target hafalan kelompok">-</span>
                                                @elseif($sudahHadir)
                                                    <button wire:click="$dispatch('openSetoranModal', { siswaId: {{ $siswa->id }}, kelompokId: {{ $this->kelompokId }}, date: '{{ $tanggal }}' })"
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white bg-[#C9A227] hover:bg-[#b8911f] active:scale-95 transition-all shadow-sm cursor-pointer whitespace-nowrap">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                        </svg>
                                                        Input Setoran
                                                    </button>
                                                @elseif(!$sudahHadir && $statusSiswa !== '')
                                                    <span class="text-xs text-gray-400 italic">Tidak hadir</span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1.5 text-xs rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed whitespace-nowrap"
                                                          title="Pilih status Hadir terlebih dahulu">
                                                        Input Setoran
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @endif {{-- end @if(!$isLiburPilihan) --}}

                </div>

                {{-- Submit Footer --}}
                <div class="px-6 py-4 bg-white border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 shrink-0">
                    <p class="text-xs text-gray-500">
                        @if($isMinggu)
                            <span class="text-red-600 font-medium">🚫 Hari Minggu — tidak ada kegiatan.</span>
                        @elseif($isLiburPilihan && $absensiSudahDisimpan)
                            <span class="text-purple-700 font-medium">🗓️ Hari libur telah tersimpan. Matikan toggle untuk mengisi absensi normal.</span>
                        @elseif($absensiSudahDisimpan && $justSaved)
                            <span class="text-green-700 font-medium">✔ Absensi tersimpan. Klik tombol kuning untuk input setoran siswa yang hadir.</span>
                        @else
                            <span class="text-amber-600 font-medium">⚡ Pilih status siswa untuk langsung aktifkan setoran.</span>
                        @endif
                    </p>
                    @if(!$isLiburPilihan && !$isMinggu)
                        <button wire:click="save"
                                type="button"
                                wire:loading.attr="disabled"
                                class="px-6 py-2 bg-zamrud hover:bg-zamrud/90 text-white rounded-lg transition-colors duration-200 font-semibold shadow-sm w-full sm:w-auto disabled:opacity-70 active:scale-95">
                            <span wire:loading.remove wire:target="save">Simpan</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    @endif
                </div>

            </div>
        </div>

    @endif

    {{-- Modal Setoran --}}
    <livewire:admin.setoran.modal-setoran />
</div>
