<div>
    <div class="container mx-auto px-4 py-6">

        {{-- Page Header Card --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm mb-6" style="border:1px solid #E5E7EB;">
            <h1 class="text-2xl font-bold mb-2" style="color:#1A2422;font-family:'Plus Jakarta Sans',sans-serif;">
                Referensi Al-Qur'an
            </h1>
            <p class="text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
                Kelola data referensi juz, surah, dan ayat Al-Qur'an
            </p>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" style="font-family: 'Inter', sans-serif;">
                    <thead class="bg-[#1B5E4F] text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider w-16">
                                No
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                Juz Ke-
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                Awal
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                                Akhir
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">
                                Jumlah Surat
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">
                                Jumlah Ayat
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider w-24">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($referensiQurans as $index => $referensi)
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-gray-100 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" style="font-variant-numeric: tabular-nums;">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#1A2422]" style="font-variant-numeric: tabular-nums;">
                                    Juz {{ $referensi->juz_ke }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <span class="font-medium text-[#1A2422]">{{ \App\Models\ReferensiQuran::getNamaSurah($referensi->awal_surah) }}</span>, Ayat {{ $referensi->awal_ayat }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <span class="font-medium text-[#1A2422]">{{ \App\Models\ReferensiQuran::getNamaSurah($referensi->akhir_surah) }}</span>, Ayat {{ $referensi->akhir_ayat }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right font-medium" style="font-variant-numeric: tabular-nums;">
                                    {{ ($referensi->akhir_surah - $referensi->awal_surah) + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right font-medium" style="font-variant-numeric: tabular-nums;">
                                    {{ $referensi->jumlah_ayat }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <button wire:click="openDetail({{ $referensi->juz_ke }})" class="inline-flex items-center px-3 py-1.5 border border-zamrud text-zamrud bg-transparent hover:bg-zamrud hover:text-white rounded-md text-xs transition-colors duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        <p class="text-lg font-medium text-gray-700 mb-1">Data referensi belum tersedia</p>
                                        <p class="text-sm text-gray-500">Silakan jalankan seeder untuk mengisi data 30 juz Al-Qur'an</p>
                                        <p class="text-xs text-gray-400 mt-2 font-mono">php artisan db:seed --class=JuzSeeder</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer info --}}
            @if($referensiQurans->isNotEmpty())
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        Total: <span class="font-medium text-[#1A2422]">{{ $referensiQurans->count() }}</span> juz
                    </p>
                </div>
            @endif
        </div>

        {{-- Info note --}}
        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-800">
                        <span class="font-medium">Informasi:</span> Data referensi ini bersifat <strong>read-only</strong> dan digunakan sebagai basis perhitungan progres hafalan siswa. Data ini diisi melalui seeder dan tidak dapat diubah melalui antarmuka.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail Juz --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none">
            {{-- Overlay Transparan (Bisa diklik untuk tutup) --}}
            <div class="fixed inset-0 bg-transparent pointer-events-none" wire:click="closeModal"></div>

            {{-- Panel --}}
            <div class="relative bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.2)] border border-gray-300 w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden">
                
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-[#E8F5F1] text-[#1B5E4F]">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Detail Rincian Juz {{ $selectedJuz }}</h3>
                            <p class="text-xs text-gray-500">Rincian surat dan ayat yang termasuk ke dalam juz ini.</p>
                        </div>
                    </div>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body (Scrollable) --}}
                <div class="p-6 overflow-y-auto">
                    <div class="border rounded-xl overflow-hidden shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Surat</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Ayat Awal</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Ayat Akhir</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah Ayat</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($detailSurahs as $idx => $surah)
                                    <tr class="hover:bg-gray-50 transition-colors" wire:key="surah-{{ $selectedJuz }}-{{ $idx }}">
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $idx + 1 }}</td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $surah['nama'] }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $surah['awal'] }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 text-center">{{ $surah['akhir'] }}</td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">{{ $surah['jumlah'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50/80">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Total Ayat di Juz Ini:</td>
                                    <td class="px-4 py-3 text-right text-sm font-bold text-[#1B5E4F]">{{ collect($detailSurahs)->sum('jumlah') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button type="button" wire:click="closeModal" class="inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zamrud transition-colors">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
