<div class="p-5 space-y-4">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900" style="font-family:'Plus Jakarta Sans',sans-serif;">
                Riwayat Kelulusan
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                Daftar siswa yang telah menyelesaikan target hafalan dan lulus dari kelompok ini
            </p>
        </div>
        <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 rounded-lg border border-emerald-200">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span class="text-sm font-bold text-emerald-700">
                {{ count($alumniSiswas) }} Alumni
            </span>
        </div>
    </div>

    {{-- Alumni Table --}}
    @if(count($alumniSiswas) > 0)
        <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead style="background-color:#1B5E4F;">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-white">
                            No
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-white">
                            Nama Siswa
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-white">
                            NIS
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-white">
                            Kelas
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-white">
                            Tanggal Lulus
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-white">
                            Tingkat Tujuan
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider text-white">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($alumniSiswas as $index => $alumni)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ $alumni['nama'] }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-mono">
                                {{ $alumni['nis'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $alumni['kelas'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ \Carbon\Carbon::parse($alumni['tanggal_lulus'])->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $alumni['status'] === 'Menunggu Penempatan' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-emerald-100 text-emerald-700 border-emerald-200' }} border">
                                    {{ $alumni['tingkat_tujuan'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $alumniTrackUrl = auth()->guard('guru')->check()
                                        ? route('guru.setoran.riwayat-hafalan', $alumni['siswa_id']) . '?kelompok_id=' . $kelompok->id
                                        : route('admin.setoran.riwayat-hafalan', $alumni['siswa_id']) . '?kelompok_id=' . $kelompok->id;
                                @endphp
                                <a href="{{ $alumniTrackUrl }}"
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
    @else
        {{-- Empty State --}}
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2" style="font-family:'Plus Jakarta Sans',sans-serif;">
                Belum Ada Alumni
            </h3>
            <p class="text-sm text-gray-500 max-w-md mx-auto">
                Belum ada siswa yang lulus dari kelompok ini. Siswa yang telah menyelesaikan target hafalan akan muncul di sini.
            </p>
        </div>
    @endif
</div>
