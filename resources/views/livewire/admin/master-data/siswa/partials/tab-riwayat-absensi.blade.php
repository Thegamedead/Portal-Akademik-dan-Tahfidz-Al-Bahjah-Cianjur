{{-- Tab: Riwayat Absensi --}}
<div class="space-y-6" style="font-family:'Inter',sans-serif;">

    <div class="flex items-center justify-between p-4 rounded-lg" style="background-color:#F9FAFB;border:1px solid #E5E7EB;">
        <div>
            <h3 class="text-lg font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                Riwayat Absensi
            </h3>
            <p class="text-sm mt-1" style="color:#6B7280;">
                Catatan absensi harian dari seluruh kelompok
            </p>
        </div>
        <div class="text-right">
            <p class="text-sm font-medium" style="color:#6B7280;">Total Kehadiran (Hadir)</p>
            <p class="text-2xl font-bold" style="color:#1B5E4F;">
                {{ \App\Models\Absensi::where('siswa_id', $siswa->id)->where('status_kehadiran', 'Hadir')->count() }}
            </p>
        </div>
    </div>

    @if($this->riwayatAbsensi->count() > 0)
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-gray-50 border-b border-gray-200" style="color:#6B7280;">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Tanggal</th>
                        <th class="px-6 py-4 font-semibold">Kelompok</th>
                        <th class="px-6 py-4 font-semibold">Status Kehadiran</th>
                        <th class="px-6 py-4 font-semibold">Guru Pengisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($this->riwayatAbsensi as $absensi)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($absensi->tanggal)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900">{{ $absensi->kelompok->nama ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeColors = [
                                        'Hadir' => 'bg-emerald-100 text-emerald-800',
                                        'Izin' => 'bg-blue-100 text-blue-800',
                                        'Sakit' => 'bg-yellow-100 text-yellow-800',
                                        'Alfa' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusValue = $absensi->status_kehadiran instanceof \BackedEnum ? $absensi->status_kehadiran->value : (string) $absensi->status_kehadiran;
                                    $badgeClass = $badgeColors[$statusValue] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                    {{ $statusValue }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $absensi->guruPengisi->nama ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        <div class="mt-4">
            {{ $this->riwayatAbsensi->links(data: ['scrollTo' => false]) }}
        </div>
    @else
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center rounded-full mb-4" style="width:64px;height:64px;background-color:#F3F4F6;">
                <svg style="width:32px;height:32px;color:#9CA3AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                Belum Ada Data Absensi
            </h3>
            <p class="text-sm" style="color:#6B7280;">
                Siswa ini belum memiliki catatan absensi
            </p>
        </div>
    @endif

</div>
