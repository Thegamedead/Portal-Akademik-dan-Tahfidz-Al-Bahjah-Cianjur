{{-- Tab: Riwayat Kelompok --}}
<div class="space-y-6" style="font-family:'Inter',sans-serif;">

    @if($siswa->riwayatKelompoks && $siswa->riwayatKelompoks->count() > 0)
        {{-- Header Section --}}
        <div class="flex items-center justify-between p-4 rounded-lg" style="background-color:#F9FAFB;border:1px solid #E5E7EB;">
            <div>
                <h3 class="text-lg font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Timeline Perpindahan Kelompok
                </h3>
                <p class="text-sm mt-1" style="color:#6B7280;">
                    Riwayat lengkap perpindahan kelompok {{ $siswa->nama }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm font-medium" style="color:#6B7280;">Total Perpindahan</p>
                <p class="text-2xl font-bold" style="color:#1B5E4F;">{{ $siswa->riwayatKelompoks->count() }}</p>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="relative" style="padding-left:60px;">
            {{-- Vertical Line --}}
            <div class="absolute left-6 top-0 bottom-0" style="width:2px;background-color:#D1D5DB;"></div>

            {{-- Timeline Items --}}
            <div class="space-y-8">
                @foreach($siswa->riwayatKelompoks as $index => $riwayat)
                    <div class="relative">
                        {{-- Timeline Dot --}}
                        <div class="absolute flex items-center justify-center rounded-full shadow-lg"
                             style="left:-60px;width:48px;height:48px;background-color:{{ $riwayat->kelompok_ke_id ? '#1B5E4F' : '#F59E0B' }};">
                            @if($riwayat->kelompok_ke_id)
                                {{-- Icon: Masuk (Arrow Right) --}}
                                <svg style="width:24px;height:24px;color:#FFFFFF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            @else
                                {{-- Icon: Keluar (Arrow Left) --}}
                                <svg style="width:24px;height:24px;color:#FFFFFF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                                </svg>
                            @endif
                        </div>

                        {{-- Content Card --}}
                        <div class="rounded-xl p-5 shadow-sm transition-all hover:shadow-md"
                             style="background-color:#FFFFFF;border:2px solid {{ $riwayat->kelompok_ke_id ? '#1B5E4F' : '#F59E0B' }};">

                            {{-- Header Row --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="text-base font-bold mb-1" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                                        @if($riwayat->kelompok_ke_id)
                                            Masuk ke {{ $riwayat->kelompokKe->nama ?? 'Kelompok tidak diketahui' }}
                                        @else
                                            Keluar dari {{ $riwayat->kelompokDari->nama ?? 'Kelompok tidak diketahui' }}
                                        @endif
                                    </h4>
                                    <div class="flex items-center gap-2 text-xs" style="color:#6B7280;">
                                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $riwayat->tanggal->format('d M Y, H:i') }} WIB
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold shrink-0"
                                      style="background-color:{{ $riwayat->kelompok_ke_id ? '#E8F5F1' : '#FEF3C7' }};color:{{ $riwayat->kelompok_ke_id ? '#1B5E4F' : '#92400E' }};">
                                    @if($riwayat->kelompok_ke_id)
                                        MASUK
                                    @else
                                        MENUNGGU
                                    @endif
                                </span>
                            </div>

                            {{-- Details Grid --}}
                            <div class="space-y-2 text-sm">
                                {{-- Dari Kelompok --}}
                                @if($riwayat->kelompokDari)
                                    <div class="flex items-start gap-2">
                                        <svg style="width:16px;height:16px;margin-top:2px;color:#6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <span class="font-medium" style="color:#6B7280;">Dari:</span>
                                            <span style="color:#1A2422;margin-left:4px;">{{ $riwayat->kelompokDari->nama }}</span>
                                            <span class="text-xs px-2 py-0.5 rounded ml-1" style="background-color:#F3F4F6;color:#4B5563;">
                                                {{ $riwayat->kelompokDari->tingkat }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Ke Kelompok --}}
                                @if($riwayat->kelompokKe)
                                    <div class="flex items-start gap-2">
                                        <svg style="width:16px;height:16px;margin-top:2px;color:#1B5E4F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <span class="font-medium" style="color:#6B7280;">Ke:</span>
                                            <span class="font-semibold ml-1" style="color:#1B5E4F;">{{ $riwayat->kelompokKe->nama }}</span>
                                            <span class="text-xs px-2 py-0.5 rounded ml-1" style="background-color:#E8F5F1;color:#1B5E4F;">
                                                {{ $riwayat->kelompokKe->tingkat }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-start gap-2">
                                        <svg style="width:16px;height:16px;margin-top:2px;color:#F59E0B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <span class="font-medium" style="color:#F59E0B;">Status:</span>
                                            <span class="italic ml-1" style="color:#92400E;">Menunggu Penempatan</span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Alasan --}}
                                <div class="flex items-start gap-2 pt-2 mt-2" style="border-top:1px solid #E5E7EB;">
                                    <svg style="width:16px;height:16px;margin-top:2px;color:#6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <span class="font-medium" style="color:#6B7280;">Alasan:</span>
                                        <p class="leading-relaxed ml-1" style="color:#1A2422;display:inline;">{{ $riwayat->alasan }}</p>
                                    </div>
                                </div>

                                {{-- Disetujui Oleh --}}
                                @if($riwayat->disetujuiOleh)
                                    <div class="flex items-start gap-2">
                                        <svg style="width:16px;height:16px;margin-top:2px;color:#6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <span class="font-medium" style="color:#6B7280;">Disetujui:</span>
                                            <span style="color:#1A2422;margin-left:4px;">{{ $riwayat->disetujuiOleh->name ?? 'Admin' }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    @else
        {{-- Empty State --}}
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center rounded-full mb-4" style="width:64px;height:64px;background-color:#F3F4F6;">
                <svg style="width:32px;height:32px;color:#9CA3AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                Belum Ada Riwayat Perpindahan
            </h3>
            <p class="text-sm" style="color:#6B7280;">
                Siswa ini belum pernah berpindah kelompok
            </p>
        </div>
    @endif

</div>
