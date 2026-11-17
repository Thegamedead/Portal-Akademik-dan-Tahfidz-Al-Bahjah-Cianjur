{{--
    View: livewire/shared/progres/modal-transkrip-siswa.blade.php
    Component: App\Livewire\Shared\Progres\ModalTranskripSiswa

    Modal untuk menampilkan ringkasan transkrip hafalan siswa terakhir.
--}}

@if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto pointer-events-none" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="font-family:'Inter',sans-serif;">
        {{-- Backdrop --}}
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 pointer-events-none" aria-hidden="true" wire:click="close"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Panel --}}
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">

                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b flex justify-between items-center" style="border-color:#E5E7EB; background-color:#F4F5F4;">
                    <h3 class="text-lg font-semibold" style="color:#1A2422;font-family:'Plus Jakarta Sans',sans-serif;">
                        Transkrip Capaian Hafalan
                    </h3>
                    <button wire:click="close" class="text-gray-400 hover:text-gray-600 focus:outline-none transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="px-6 py-5 bg-white space-y-6">
                    @if($siswa)
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 text-white font-bold text-lg" style="background-color:#1B5E4F;">
                                {{ mb_strtoupper(mb_substr($siswa->nama, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="text-lg font-bold" style="color:#1A2422;">{{ $siswa->nama }}</h4>
                                <p class="text-sm" style="color:#6B7280;">NIS: {{ $siswa->nis }} • {{ $siswa->kelompok->nama ?? '-' }}</p>
                            </div>
                        </div>
                    @endif

                    @if(empty($transkrip))
                        <div class="p-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-300 cursor-pointer">
                            <p class="text-gray-500">Belum ada data transkrip/snapshot untuk siswa ini.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="p-4 rounded-lg bg-gray-50 border border-gray-100 text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Juz Tuntas</p>
                                <p class="text-2xl font-bold mt-1 text-zamrud">{{ $transkrip['detail_capaian']['total_juz_tuntas'] ?? 0 }}</p>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50 border border-gray-100 text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Progres Target</p>
                                <p class="text-2xl font-bold mt-1 text-tinta">{{ number_format($transkrip['detail_capaian']['progress_toward_target'] ?? 0, 1) }}%</p>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50 border border-gray-100 text-center sm:col-span-2">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Tanggal Transkrip</p>
                                <p class="text-lg font-semibold mt-1 text-tinta">{{ \Carbon\Carbon::parse($transkrip['tanggal_generate'])->isoFormat('DD MMMM YYYY') }}</p>
                                <p class="text-xs text-gray-400 mt-1">Alasan: {{ ucwords(str_replace('_', ' ', $transkrip['reason'] ?? '-')) }}</p>
                            </div>
                        </div>

                        {{-- Visualisasi Snapshot --}}
                        <div class="pt-4 border-t border-gray-100">
                            <h4 class="text-sm font-semibold mb-3 text-tinta">Detail Capaian Per Juz</h4>
                            <x-untaian-progres
                                :capaian-juzs="$transkrip['detail_capaian']['capaian_juz'] ?? []"
                                :target-kelulusan="$transkrip['target_kelulusan'] ?? 5"
                                size="sm"
                            />
                        </div>
                    @endif
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-right">
                    <button wire:click="close" type="button" class="inline-flex justify-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:outline-none cursor-pointer transition-colors">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>
@endif
