<div class="space-y-6" style="font-family:'Inter',sans-serif;">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- PAGE HEADER --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Hasil Tes Penempatan
                </h1>
                <p class="mt-2 text-sm" style="color:#6B7280;">
                    Hasil tes siswa: <strong>{{ $siswa->nama }}</strong> ({{ $siswa->nis }})
                </p>
            </div>
            <button
                wire:click="kembali"
                type="button"
                class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
            >
                Kembali
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- INFO TES --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;">
        <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
            Informasi Tes
        </h3>

        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                Tanggal Tes
            </label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm" style="color:#1A2422;">{{ $tes->tanggal_tes->tgl() }}</p>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                Waktu Tes
            </label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm" style="color:#1A2422;">
                    {{ $tes->waktu_tes ? $tes->waktu_tes->format('H:i') : '-' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                Status
            </label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                @if($tes->status->value === 'Selesai')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color:#FEF3C7;color:#92400E;">
                        Selesai
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                Diselesaikan
            </label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm" style="color:#1A2422;">
                    {{ $tes->submitted_at ? $tes->submitted_at->format('d M Y H:i') : '-' }}
                </p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- HASIL HAFALAN --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6 space-y-4" style="border:1px solid #E5E7EB;">
        <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
            Hasil Hafalan
        </h3>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="background-color:#F9FAFB;">
                        <th class="px-4 py-3 text-left text-xs font-semibold" style="color:#6B7280;">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold" style="color:#6B7280;">Juz</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold" style="color:#6B7280;">Surah</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold" style="color:#6B7280;">Ayat</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    @foreach($hasilTes as $index => $hasil)
                        <tr>
                            <td class="px-4 py-3 text-sm" style="color:#6B7280;">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm" style="color:#1A2422;">
                                Juz {{ $hasil->juz->juz_ke }}
                            </td>
                            <td class="px-4 py-3 text-sm" style="color:#1A2422;">
                                {{ $hasil->surah->nomor_surah }}. {{ $hasil->surah->nama_latin }}
                            </td>
                            <td class="px-4 py-3 text-sm" style="color:#1A2422;">
                                Ayat {{ $hasil->ayat_mulai }} - {{ $hasil->ayat_selesai }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- CATATAN KUALITAS --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;">
        <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
            Catatan Kualitas Hafalan
        </h3>

        <div class="p-4 rounded-lg" style="background-color:#F9FAFB;">
            <p class="text-sm whitespace-pre-line" style="color:#1A2422;">{{ $tes->catatan_kualitas }}</p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- REKOMENDASI & PENEMPATAN --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;">
        <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
            Rekomendasi & Penempatan
        </h3>

        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                Rekomendasi Tingkat
            </label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                @if($tes->rekomendasi_tingkat)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold"
                          style="background-color:{{ $tes->rekomendasi_tingkat->value === 'Dasar' ? '#E8F5F1' : ($tes->rekomendasi_tingkat->value === 'Menengah' ? '#E0F2F7' : '#FEF3C7') }};
                                 color:{{ $tes->rekomendasi_tingkat->value === 'Dasar' ? '#1B5E4F' : ($tes->rekomendasi_tingkat->value === 'Menengah' ? '#2E7D8C' : '#C9A227') }};">
                        {{ $tes->rekomendasi_tingkat->value }}
                    </span>
                @else
                    <p class="text-sm" style="color:#9CA3AF;">-</p>
                @endif
            </div>
        </div>

        @if($tes->finalKelompok)
            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                    Kelompok Penempatan
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <p class="text-sm font-semibold" style="color:#1B5E4F;">{{ $tes->finalKelompok->nama }}</p>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-3 items-center">
                <label class="col-span-3 text-sm font-medium" style="color:#6B7280;">
                    Ditempatkan Pada
                </label>
                <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
                <div class="col-span-8">
                    <p class="text-sm" style="color:#1A2422;">
                        {{ $tes->ditempatkan_at ? $tes->ditempatkan_at->format('d M Y H:i') : '-' }}
                    </p>
                </div>
            </div>
        @elseif($tes->ditempatkan_at)
            <div class="p-4 rounded-lg" style="background-color:#FEF3C7;border:1px solid #F59E0B;">
                <p class="text-sm" style="color:#92400E;">
                    <strong>Catatan:</strong> Siswa sudah diproses oleh admin, namun belum ditempatkan di kelompok tertentu.
                </p>
            </div>
        @else
            <div class="p-4 rounded-lg" style="background-color:#EFF6FF;border:1px solid #3B82F6;">
                <p class="text-sm" style="color:#1E40AF;">
                    <strong>Status:</strong> Menunggu admin untuk menempatkan siswa ke kelompok.
                </p>
            </div>
        @endif
    </div>

</div>
