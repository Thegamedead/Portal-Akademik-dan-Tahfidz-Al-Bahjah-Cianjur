<div>
    @if($show)
        <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" style="font-family:'Inter',sans-serif;">
            <div class="fixed inset-0 pointer-events-none" wire:click="close"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 flex flex-col" style="border:1px solid #E5E7EB; max-height:90vh;">
                    
                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-5 rounded-t-2xl flex-shrink-0" style="background-color:#1B5E4F;">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-delima-100">
                                <svg class="h-5 w-5 text-delima-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">Batalkan Tes Penempatan</h3>
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
                        <div class="overflow-y-auto flex-1 pr-1 space-y-4">
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Anda yakin ingin membatalkan jadwal tes untuk siswa <strong>{{ $tes?->siswa?->nama }}</strong>? Tindakan ini tidak dapat dibatalkan.
                                </p>
                            </div>
                            <div class="mt-4">
                                @if(session()->has('general'))
                                    <div class="rounded-md bg-delima-50 p-4 mb-4">
                                        <div class="text-sm text-delima-700">{{ session('general') }}</div>
                                    </div>
                                @endif
                                @error('general')
                                    <div class="rounded-md bg-delima-50 p-4 mb-4">
                                        <div class="text-sm text-delima-700">{{ $message }}</div>
                                    </div>
                                @enderror

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Alasan Pembatalan <span class="text-delima-500">*</span></label>
                                    <p class="text-xs text-gray-500 mb-1">Minimal 20 karakter.</p>
                                    <textarea wire:model="alasan_pembatalan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-delima-500 focus:border-delima-500 sm:text-sm" placeholder="Jelaskan alasan mengapa tes ini dibatalkan... cursor-pointer"></textarea>
                                    @error('alasan_pembatalan') <span class="text-xs text-delima-600 mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Footer Buttons --}}
                        <div class="mt-6 pt-4 flex gap-3 shrink-0" style="border-top:1px solid #E5E7EB;">
                            <button type="button" wire:click="close" class="btn-modal-cancel flex-1">Kembali</button>
                            <button type="submit" class="btn-modal-confirm flex-1">Ya, Batalkan Tes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
