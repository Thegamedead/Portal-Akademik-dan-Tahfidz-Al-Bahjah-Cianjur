<div class="space-y-6">

    {{-- HEADER --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Riwayat Lulusan / Naik Tingkat
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Daftar seluruh siswa yang telah lulus atau naik tingkat dari kelompok yang Anda bimbing
        </p>
    </div>

    {{-- TOOLBAR (SEARCH & INFO) --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            
            {{-- LEFT: Info --}}
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500">
                    Total <strong style="color:#1A2422;">{{ $alumniPaginator->total() }}</strong> histori kelulusan
                </span>
            </div>

            {{-- RIGHT: Search Field --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-[400px]">
                <div class="flex-1">
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari siswa (Nama/NIS)..."
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
            </div>

        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if(count($alumniPaginator) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead style="background-color:#1B5E4F;">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-white">No</th>
                            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-white">Nama Siswa</th>
                            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-white">Kelompok Asal</th>
                            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-white">Tanggal Lulus</th>
                            <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-white">Tingkat Tujuan</th>
                            <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider text-white">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($alumniPaginator as $index => $alumni)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $alumniPaginator->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-semibold text-gray-900">{{ $alumni->nama }}</div>
                                            <div class="text-xs text-gray-500 font-mono">{{ $alumni->nis }} &bull; {{ $alumni->kelas }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $alumni->kelompok_asal }}</div>
                                    <div class="text-xs text-gray-500">{{ $alumni->tingkat_asal }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $alumni->tanggal_indo }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $alumni->status === 'Menunggu Penempatan' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-emerald-100 text-emerald-700 border-emerald-200' }} border">
                                        {{ $alumni->tingkat_tujuan }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <a href="{{ route('guru.setoran.riwayat-hafalan', $alumni->siswa_id) }}?kelompok_id={{ $alumni->kelompok_asal_id }}"
                                       class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold text-white shadow-sm transition-all hover:opacity-90 hover:shadow-md"
                                       style="background:linear-gradient(135deg,#1B5E4F,#2d7a67);" title="Lihat track record hafalan">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                        </svg>
                                        Track Record
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($alumniPaginator->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $alumniPaginator->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">Belum Ada Alumni</h3>
                <p class="text-sm text-gray-500 max-w-md mx-auto">Belum ada siswa yang lulus atau naik tingkat dari kelompok yang Anda bimbing.</p>
            </div>
        @endif
    </div>
</div>
