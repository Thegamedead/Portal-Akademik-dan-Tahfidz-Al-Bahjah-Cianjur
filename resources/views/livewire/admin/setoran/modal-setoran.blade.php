<div>
    @if($isOpen)
        <div class="fixed inset-0 z-50 pointer-events-none flex items-center justify-center p-4 sm:p-6"
             x-data="{
                show: false,
                x: 0,
                y: 0,
                startX: 0,
                startY: 0,
                isDragging: false,
                dragStart(e) {
                    this.isDragging = true;
                    this.startX = e.clientX - this.x;
                    this.startY = e.clientY - this.y;

                    const drag = (e) => {
                        if(this.isDragging) {
                            this.x = e.clientX - this.startX;
                            this.y = e.clientY - this.startY;
                        }
                    };
                    const dragEnd = () => {
                        this.isDragging = false;
                        document.removeEventListener('mousemove', drag);
                        document.removeEventListener('mouseup', dragEnd);
                    };

                    document.addEventListener('mousemove', drag);
                    document.addEventListener('mouseup', dragEnd);
                }
             }"
             x-init="setTimeout(() => show = true, 50)"
             x-show="show"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <div :style="`transform: translate(${x}px, ${y}px)`"
                 class="w-full max-w-md max-h-full pointer-events-auto bg-white flex flex-col rounded-2xl border border-gray-200 relative overflow-hidden transition-shadow"
                 :class="{ 'shadow-[0_30px_60px_rgba(0,0,0,0.3)]': isDragging, 'shadow-2xl': !isDragging }">

                {{-- Header Drawer --}}
                <div @mousedown="dragStart($event)" class="px-5 py-4 bg-gray-50 border-b border-gray-200 flex items-start justify-between shrink-0 cursor-grab active:cursor-grabbing select-none" title="Tahan dan geser untuk memindahkan">
                    <div class="pr-3">
                        <h2 class="text-base font-bold text-tinta leading-tight">{{ $namaSiswa ?? '' }}</h2>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 font-mono ring-1 ring-inset ring-gray-500/10">{{ $nisSiswa ?? '' }}</span>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 border border-gray-200 p-1.5 shadow-sm transition-colors hover:bg-gray-50">
                        <span class="sr-only">Tutup</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body Drawer --}}
                <div class="flex-1 overflow-y-auto p-4 md:p-5 bg-white">

                    <button type="button" wire:click="addEntry" class="w-full mb-4 py-2.5 border-2 border-dashed border-gray-300 rounded-lg text-xs font-bold text-gray-500 hover:border-zamrud hover:text-zamrud transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Setoran Lain
                    </button>

                    {{-- Banner: Sesi Mengulang --}}
                    @if($isMengulang)
                        <div class="mb-3 flex items-center gap-2 px-3 py-2 rounded-lg border border-orange-200 bg-orange-50">
                            <svg class="w-3.5 h-3.5 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <p class="text-[10px] font-semibold text-orange-700">Sesi Mengulang — Ayat diisi otomatis dari saran sesi sebelumnya.</p>
                        </div>
                    @endif

                    @foreach($entries as $index => $entry)
                        @php $isFirstAndMengulang = ($index === 0 && $isMengulang); @endphp
                        <div class="mb-4" wire:key="entry-{{ $index }}">
                            {{-- Header entri --}}
                            <div class="flex items-center justify-between px-4 pt-3 pb-2 {{ $isFirstAndMengulang ? 'bg-red-50 border-t-4 border-t-red-400 border-x border-red-200' : 'bg-gray-50 border-t-4 border-t-zamrud border-x border-gray-200' }} rounded-t-xl">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xs font-bold {{ $isFirstAndMengulang ? 'text-red-700' : 'text-gray-700' }} uppercase tracking-wider">Entri #{{ $index + 1 }}</h3>
                                    @if($isFirstAndMengulang)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-red-600 bg-red-100 border border-red-200 rounded px-1.5 py-0.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Mengulang
                                        </span>
                                    @endif
                                </div>
                                @if(count($entries) > 1)
                                    <button type="button" wire:click="removeEntry({{ $index }})" class="text-gray-400 hover:text-delima transition-colors" title="Hapus Entri">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                @endif
                            </div>

                            {{-- Body entri --}}
                            <div class="{{ $isFirstAndMengulang ? 'bg-red-50/40 border-x border-b border-red-200' : 'bg-gray-50 border-x border-b border-gray-200' }} rounded-b-xl shadow-sm p-4 space-y-3">

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Jenis Setoran</label>
                                        <select wire:model.live="entries.{{ $index }}.jenis_setoran" class="w-full rounded-md border-gray-300 shadow-sm focus:border-zamrud focus:ring focus:ring-zamrud focus:ring-opacity-50 text-xs py-1.5 cursor-pointer">
                                            <option value="Hafalan Baru">Hafalan Baru</option>
                                            <option value="Ulang">Ulang</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Juz</label>
                                        <select wire:model.live="entries.{{ $index }}.juz_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-zamrud focus:ring focus:ring-zamrud focus:ring-opacity-50 text-xs py-1.5 cursor-pointer">
                                            <option value="">-- Pilih --</option>
                                            @foreach($juzs as $juz)
                                                <option value="{{ $juz->id }}">Juz {{ $juz->juz_ke }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Surah</label>
                                        <select wire:model.live="entries.{{ $index }}.surah_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-zamrud focus:ring focus:ring-zamrud focus:ring-opacity-50 text-xs py-1.5 cursor-pointer" {{ empty($entry['juz_id']) ? 'disabled' : '' }}>
                                            <option value="">-- Pilih Surah --</option>
                                            @php
                                                $selectedNewHafalanSurahs = collect($entries)->filter(fn($e, $idx) => $e['jenis_setoran'] === 'Hafalan Baru' && $idx !== $index)->pluck('surah_id')->filter()->map(fn($v) => (int)$v)->toArray();
                                            @endphp
                                            @foreach($surahsOptions[$index] ?? [] as $surah)
                                                @php
                                                    $isAlreadyLulus = in_array($surah->id, $completedSurahIds ?? []);
                                                    $needsRepeat = in_array($surah->id, $surahToRepeatIds ?? []);
                                                    $isAlreadySelected = in_array($surah->id, $selectedNewHafalanSurahs);

                                                    // Filter logic based on jenis_setoran
                                                    if ($entry['jenis_setoran'] === 'Hafalan Baru') {
                                                        // Hafalan Baru: disable yang sudah tuntas atau sudah dipilih
                                                        $isDisabled = $isAlreadyLulus || $isAlreadySelected;
                                                        $statusLabel = '';
                                                        if ($isAlreadyLulus) $statusLabel = ' - Tuntas';
                                                        elseif ($isAlreadySelected) $statusLabel = ' - Dipilih';
                                                        $shouldShow = true; // Show all
                                                    } elseif ($entry['jenis_setoran'] === 'Ulang') {
                                                        // Ulang: hanya tampilkan yang keputusan terakhirnya "Ulang" (perlu diulang)
                                                        $shouldShow = $needsRepeat;
                                                        $isDisabled = false; // Tidak perlu disable karena sudah difilter
                                                        $statusLabel = '';
                                                    } else {
                                                        $shouldShow = true;
                                                        $isDisabled = false;
                                                        $statusLabel = '';
                                                    }
                                                @endphp
                                                @if($shouldShow)
                                                    <option value="{{ $surah->id }}" {{ $isDisabled ? 'disabled' : '' }}>
                                                        {{ $surah->nama_latin }} ({{ $surah->jumlah_ayat }}){{ $statusLabel }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Ayat</label>
                                        <div class="flex items-center gap-1.5">
                                            <input type="text" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')" wire:model.live="entries.{{ $index }}.ayat_mulai" class="w-full text-center rounded-md border-gray-300 shadow-sm focus:border-zamrud focus:ring focus:ring-zamrud focus:ring-opacity-50 text-xs py-1.5 px-1 bg-white" placeholder="Mulai">
                                            <span class="text-gray-400 font-bold text-[11px]">–</span>
                                            <input type="text" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')" wire:model.live="entries.{{ $index }}.ayat_selesai" class="w-full text-center rounded-md border-gray-300 shadow-sm focus:border-zamrud focus:ring focus:ring-zamrud focus:ring-opacity-50 text-xs py-1.5 px-1 bg-white" placeholder="Akhir">
                                        </div>
                                        @if(!empty($ayatRangeHints[$index]))
                                            @php $hint = $ayatRangeHints[$index]; @endphp
                                            <div class="mt-1 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-[9px] text-blue-600 font-medium">{{ $hint['hint'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($errors->has("entries.{$index}.ayat_mulai") || $errors->has("entries.{$index}.ayat_selesai"))
                                    <div class="text-right">
                                        @error("entries.{$index}.ayat_mulai") <p class="text-delima text-[10px] leading-tight">{{ $message }}</p> @enderror
                                        @error("entries.{$index}.ayat_selesai") <p class="text-delima text-[10px] leading-tight">{{ $message }}</p> @enderror
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    {{-- 2. Penilaian (Global untuk Sesi ini) --}}
                    <div class="bg-gray-50 border-t-4 border-t-zamrud rounded-b-xl shadow-sm border-x border-b border-gray-200 p-4 mb-4">
                        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
                            <h3 class="text-sm font-bold text-gray-800">Penilaian Sesi Ini</h3>
                            <span class="inline-flex items-center rounded-md bg-zamrud/10 px-2 py-1 text-xs font-bold text-zamrud ring-1 ring-inset ring-zamrud/20">
                                Total: {{ $this->totalAyatKumulatif }} Ayat
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            @foreach(['tajwid', 'kelancaran', 'makhraj', 'fashahah'] as $komponen)
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 mb-1 capitalize">{{ $komponen }}</label>
                                    <select wire:model.live="skor_{{ $komponen }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-zamrud focus:ring focus:ring-zamrud focus:ring-opacity-50 text-xs py-1.5 cursor-pointer">
                                        <option value="Lancar">Lancar</option>
                                        <option value="Kurang Lancar">Kurang Lancar</option>
                                        <option value="Tidak Lancar">Tidak Lancar</option>
                                    </select>
                                </div>
                            @endforeach
                        </div>

                        {{-- 3. Hasil --}}
                        <div class="flex items-center justify-between bg-white border border-gray-200 rounded-lg p-3 mb-4">
                            <div>
                                <span class="block text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Predikat</span>
                                @php
                                    $badgeColor = match($predikat) {
                                        'Lancar' => 'text-zamrud',
                                        'Kurang Lancar' => 'text-[#C9A227]',
                                        'Tidak Lancar' => 'text-delima',
                                        default => 'text-gray-800',
                                    };
                                @endphp
                                <span class="text-sm font-black {{ $badgeColor }}">{{ $predikat }}</span>
                            </div>
                            <div class="text-right">
                                <span class="block text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Keputusan</span>
                                <select wire:model.live="keputusan" class="block w-24 rounded border-gray-300 shadow-sm focus:border-zamrud text-xs py-1 pr-8 cursor-pointer {{ $keputusan === 'Ulang' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }}">
                                    <option value="Lanjut">Lanjut</option>
                                    <option value="Ulang">Ulang</option>
                                </select>
                            </div>
                        </div>

                        @if($keputusan === 'Ulang')
                            <div class="mt-1">
                                <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Catatan (Alasan Mengulang)</label>
                                <textarea wire:model.live="catatan" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-zamrud focus:ring focus:ring-zamrud focus:ring-opacity-50 text-xs py-1.5 px-2 resize-none bg-white" placeholder="Tulis catatan atau alasan perlu mengulang..."></textarea>
                            </div>
                        @endif
                    </div>


                </div>

                {{-- Footer Drawer --}}
                <div class="p-4 border-t border-gray-200 bg-white shrink-0">
                    <button wire:click="submit" type="button" class="w-full flex justify-center items-center gap-2 rounded-lg border border-transparent px-4 py-2.5 bg-zamrud text-sm font-bold text-white hover:bg-zamrud/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zamrud shadow-md transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Setoran
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
