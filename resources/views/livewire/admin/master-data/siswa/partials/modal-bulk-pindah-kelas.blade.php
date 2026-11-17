{{-- Modal Bulk Pindah Kelas Akademik --}}
@if($showModalBulkPindahKelas)
    <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif; pointer-events: none;">
        <div class="flex min-h-full items-center justify-center p-4" style="pointer-events: auto;">
            <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6" style="border:1px solid #E5E7EB;">

                {{-- Header --}}
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-lg font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                            Pindah/Naik Kelas Akademik
                        </h3>
                        <p class="text-sm mt-1" style="color:#6B7280;">
                            Pindahkan <strong style="color:#1B5E4F;">{{ count($selectedIds) }} siswa</strong> terpilih ke kelas baru.
                        </p>
                    </div>
                    <button wire:click="tutupModalBulkPindahKelas" class="p-2 rounded-lg hover:bg-gray-100 transition-colors" style="color:#9CA3AF;">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Form Content --}}
                <div class="space-y-4">
                    {{-- Alert Info --}}
                    <div class="p-3 rounded-lg" style="background-color:#F0FDF4;border:1px solid #BBF7D0;">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5" style="color:#15803D;" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm" style="color:#166534;">
                                    Gunakan fitur ini untuk menaikkan kelas siswa di tahun ajaran baru atau memindahkan rombongan belajar.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Pilih Kelas --}}
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color:#1A2422;">
                            Pilih Kelas Tujuan <span style="color:#9B3A3A;">*</span>
                        </label>
                        <select
                            wire:model="bulkKelasId"
                            class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 transition-all"
                            style="background-color:#FFFFFF;border:1.5px solid {{ $errors->has('bulkKelasId') ? '#9B3A3A' : '#D1D5DB' }};color:#1A2422;"
                        >
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}">
                                    {{ $kelas->nama_lengkap }} (Wali: {{ $kelas->nama_wali_kelas ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('bulkKelasId')
                            <p class="mt-1.5 text-xs" style="color:#9B3A3A;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="mt-6 flex justify-end gap-3 pt-4" style="border-top:1px solid #E5E7EB;">
                    <button
                        type="button"
                        wire:click="tutupModalBulkPindahKelas"
                        class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #9B3A3A; border: 1.5px solid #9B3A3A;"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        wire:click="prosesBulkPindahKelas"
                        wire:loading.attr="disabled"
                        wire:target="prosesBulkPindahKelas"
                        class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium rounded-lg text-white transition-colors focus:outline-none" style="background-color: #1B5E4F; border: 1.5px solid #1B5E4F;"
                    >
                        <span wire:loading.remove wire:target="prosesBulkPindahKelas">Pindah Kelas</span>
                        <span wire:loading wire:target="prosesBulkPindahKelas" class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="10" stroke-width="4" class="opacity-25"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
