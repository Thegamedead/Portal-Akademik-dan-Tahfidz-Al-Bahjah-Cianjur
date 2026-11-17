<div>
    @if($show)
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif;">
            <div class="fixed inset-0 pointer-events-none" wire:click="close"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 flex flex-col" style="border:1px solid #E5E7EB; max-height:90vh;">
                    
                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-zamrud-100">
                                <svg class="h-5 w-5 text-zamrud-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">Jadwalkan Tes Penempatan</h3>
                            </div>
                        </div>
                        <button wire:click="close" type="button" class="p-2 rounded-lg hover:bg-gray-100" style="color:#6B7280;">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Form Content --}}
                    <form wire:submit.prevent="submit" class="flex flex-col flex-1 overflow-hidden">
                        <div class="overflow-y-auto flex-1 pr-1 space-y-6">
                            @if(session()->has('general'))
                                <div class="rounded-md bg-delima-50 p-4">
                                    <div class="text-sm text-delima-700">{{ session('general') }}</div>
                                </div>
                            @endif
                            @error('general')
                                <div class="rounded-md bg-delima-50 p-4">
                                    <div class="text-sm text-delima-700">{{ $message }}</div>
                                </div>
                            @enderror

                            {{-- Siswa --}}
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 sm:gap-6 items-start">
                                <label class="sm:col-span-4 text-sm font-medium mt-2" style="color:#1A2422;">Siswa <span style="color:#9B3A3A;">*</span></label>
                                <div class="sm:col-span-8">
                                    <select wire:model="siswa_id" class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 cursor-pointer" style="background-color:#FFFFFF;border:1.5px solid #D1D5DB;color:#1A2422; focus:ring-color:#1B5E4F;">
                                        <option value="">-- Pilih Siswa --</option>
                                        @foreach($siswas as $siswa)
                                            <option value="{{ $siswa->id }}">{{ $siswa->nama }} ({{ $siswa->nis }})</option>
                                        @endforeach
                                    </select>
                                    @error('siswa_id') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Guru Penguji --}}
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 sm:gap-6 items-start">
                                <label class="sm:col-span-4 text-sm font-medium mt-2" style="color:#1A2422;">Guru Penguji <span style="color:#9B3A3A;">*</span></label>
                                <div class="sm:col-span-8">
                                    <select wire:model="guru_penguji_id" class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 cursor-pointer" style="background-color:#FFFFFF;border:1.5px solid #D1D5DB;color:#1A2422; focus:ring-color:#1B5E4F;">
                                        <option value="">-- Pilih Guru --</option>
                                        @foreach($gurus as $guru)
                                            <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('guru_penguji_id') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Waktu & Tanggal --}}
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 sm:gap-6 items-start">
                                <label class="sm:col-span-4 text-sm font-medium mt-2" style="color:#1A2422;">Jadwal <span style="color:#9B3A3A;">*</span></label>
                                <div class="sm:col-span-8 flex flex-col sm:flex-row gap-3">
                                    <div class="flex-1">
                                        <input type="date" wire:model="tanggal_tes" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 cursor-pointer" style="background-color:#FFFFFF;border:1.5px solid #D1D5DB;color:#1A2422; focus:ring-color:#1B5E4F;" title="Tanggal Tes">
                                        @error('tanggal_tes') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="flex-1">
                                        <input type="time" wire:model="waktu_tes" class="w-full rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 cursor-pointer" style="background-color:#FFFFFF;border:1.5px solid #D1D5DB;color:#1A2422; focus:ring-color:#1B5E4F;" title="Waktu Tes (Opsional)">
                                        @error('waktu_tes') <p class="mt-1 text-xs" style="color:#9B3A3A;">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Buttons --}}
                        <div class="mt-6 pt-4 flex gap-3 shrink-0" style="border-top:1px solid #E5E7EB;">
                            <button type="button" wire:click="close" class="btn-modal-cancel flex-1">Batal</button>
                            <button type="submit" class="btn-modal-confirm flex-1">Jadwalkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
