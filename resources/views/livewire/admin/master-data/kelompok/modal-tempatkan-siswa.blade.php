{{-- MODAL TEMPATKAN SISWA --}}
@if($showTempatkanSiswaModal && $kelompokTujuan)
    <div class="fixed inset-0 z-50 overflow-y-auto" style="font-family:'Inter',sans-serif;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="tutupTempatkanSiswaModal"></div>

            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-4xl overflow-y-auto" style="border:1px solid #E5E7EB; overflow-x: visible; max-height: 90vh;">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                    <h3 class="text-lg font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                        Tempatkan Siswa ke {{ $kelompokTujuan->nama }}
                    </h3>
                    <button wire:click="tutupTempatkanSiswaModal" class="p-2 rounded-lg transition-colors" style="color:white;">
                        <svg class="h-5 w-5 hover:opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Form Content --}}
                <div class="p-6">

                    {{-- Info Kelompok --}}
                    <div class="mb-5 p-4 rounded-xl" style="background-color:#F0FDF4;border:1px solid #BBF7D0;">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 mt-0.5 shrink-0" style="color:#15803D;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="text-sm" style="color:#15803D;">
                                <p class="font-semibold mb-1">Kelompok Tujuan</p>
                                <p class="text-xs"><strong>{{ $kelompokTujuan->nama }}</strong> ({{ $kelompokTujuan->tingkat }} - {{ $kelompokTujuan->jenis }})</p>
                                <p class="text-xs">Pembimbing: {{ $kelompokTujuan->guruPembimbing?->nama ?? '-' }}</p>
                                <p class="text-xs">Kapasitas: {{ $kelompokTujuan->siswas->count() }}/{{ $kelompokTujuan->kapasitas_maks ?? 12 }} siswa</p>
                            </div>
                        </div>
                    </div>

                    {{-- Filter & Search --}}
                    <div class="mb-5 flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="searchSiswaTempatkan"
                                placeholder="Cari nama, NIS, atau NISN..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all"
                            />
                        </div>
                        <div class="sm:w-48">
                            <select
                                wire:model.live="filterStatusSiswa"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all"
                            >
                                <option value="belum_ditempatkan">Belum Ditempatkan</option>
                                <option value="semua">Semua Siswa Aktif</option>
                            </select>
                        </div>
                    </div>

                    {{-- Daftar Siswa --}}
                    <div class="mb-5">
                        <div class="flex items-center justify-between mb-3">
                            <label class="flex items-center text-sm font-medium" style="color:#1A2422;">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectAllSiswa"
                                    class="mr-2 rounded border-gray-300 text-[#1B5E4F] focus:ring-[#1B5E4F]"
                                />
                                Pilih Semua
                            </label>
                            <span class="text-xs" style="color:#6B7280;">
                                {{ count($selectedSiswaIds) }} siswa dipilih
                            </span>
                        </div>

                        <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                            @php
                                $siswaList = $this->getAvailableSiswaQuery()->get();
                            @endphp

                            @if($siswaList->isEmpty())
                                <div class="p-8 text-center">
                                    <svg class="w-12 h-12 mx-auto mb-3" style="color:#9CA3AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">Tidak ada siswa yang tersedia</p>
                                    <p class="text-xs mt-1" style="color:#9CA3AF;">Tidak ada siswa yang sesuai dengan kriteria kelompok ini</p>
                                </div>
                            @else
                                @foreach($siswaList as $siswa)
                                    <label class="flex items-center gap-3 p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors">
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectedSiswaIds"
                                            value="{{ $siswa->id }}"
                                            class="rounded border-gray-300 text-[#1B5E4F] focus:ring-[#1B5E4F]"
                                        />
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shrink-0" style="background-color:#1B5E4F;color:#FFFFFF;">
                                            {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-sm truncate" style="color:#1A2422;">{{ $siswa->nama }}</p>
                                            <p class="text-xs" style="color:#6B7280;">
                                                NIS: {{ $siswa->nis }} • {{ $siswa->jenis_kelamin }}
                                                @if($siswa->kelas)
                                                    • Kelas {{ $siswa->kelas->nama }}
                                                @endif
                                            </p>
                                        </div>
                                        @if($siswa->status_penempatan === 'Belum Ditempatkan')
                                            <span class="shrink-0 px-2 py-1 text-xs font-medium rounded" style="background-color:#DBEAFE;color:#1E40AF;border:1px solid #BFDBFE;">
                                                Belum Ditempatkan
                                            </span>
                                        @else
                                            <span class="shrink-0 px-2 py-1 text-xs font-medium rounded" style="background-color:#FEF3C7;color:#92400E;border:1px solid #FDE68A;">
                                                {{ $siswa->kelompok?->nama ?? 'Ditempatkan' }}
                                            </span>
                                        @endif
                                    </label>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    {{-- Tanggal Penempatan --}}
                    <div class="mb-5">
                        <label for="tanggalPenempatanSiswa" class="block text-sm font-medium mb-2" style="color:#1A2422;">
                            Tanggal Penempatan <span style="color:#9B3A3A;">*</span>
                        </label>
                        <div wire:ignore>
                            <input
                                type="text"
                                id="tanggalPenempatanSiswa"
                                x-data="{
                                    init() {
                                        const fp = flatpickr($el, {
                                            dateFormat: 'Y-m-d',
                                            static: false,
                                            appendTo: document.body,
                                            position: 'auto center',
                                            locale: {
                                                firstDayOfWeek: 1,
                                                weekdays: {
                                                    shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                                                    longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
                                                },
                                                months: {
                                                    shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                                                    longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                                                }
                                            },
                                            onChange: function(selectedDates, dateStr) {
                                                @this.set('tanggalPenempatanSiswa', dateStr);
                                            },
                                            onOpen: function(selectedDates, dateStr, instance) {
                                                setTimeout(() => {
                                                    instance.calendarContainer.style.width = 'auto';
                                                }, 10);
                                            }
                                        });
                                    }
                                }"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1B5E4F] focus:border-transparent transition-all"
                                placeholder="Pilih tanggal penempatan"
                                value="{{ $tanggalPenempatanSiswa }}"
                            >
                        </div>
                        @error('tanggalPenempatanSiswa')
                            <p class="mt-1.5 text-xs flex items-center gap-1" style="color:#DC2626;">
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                </div>{{-- End Form Content --}}

                {{-- Footer --}}
                <div class="flex justify-end gap-3 pt-4 pb-2 px-6" style="border-top:1px solid #E5E7EB;">
                    <button wire:click="tutupTempatkanSiswaModal" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #9B3A3A; border: 1.5px solid #9B3A3A;">
                        Batal
                    </button>
                    @if(count($selectedSiswaIds) > 0)
                        <button wire:click="prosesTempatkanSiswa" type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #16A34A; border: 1.5px solid #16A34A;">
                            <span wire:loading.remove wire:target="prosesTempatkanSiswa">
                                Tempatkan {{ count($selectedSiswaIds) }} Siswa
                            </span>
                            <span wire:loading wire:target="prosesTempatkanSiswa">
                                Memproses...
                            </span>
                        </button>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endif
