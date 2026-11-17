{{-- Tab: Data Siswa --}}
<div class="space-y-6">

    {{-- Identitas & Status --}}
    <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
            Identitas & Status
        </h3>

        {{-- NIS --}}
        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">NIS</label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm" style="color:#1A2422;">{{ $siswa->nis }}</p>
            </div>
        </div>

        {{-- NISN --}}
        @if($siswa->nisn)
        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">NISN</label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm" style="color:#1A2422;">{{ $siswa->nisn }}</p>
            </div>
        </div>
        @endif

        {{-- Nama Lengkap --}}
        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">Nama Lengkap</label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm font-semibold" style="color:#1A2422;">{{ $siswa->nama }}</p>
            </div>
        </div>

        {{-- Jenis Kelamin --}}
        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">Jenis Kelamin</label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm" style="color:#1A2422;">{{ $siswa->jenis_kelamin }}</p>
            </div>
        </div>

        {{-- Tempat, Tanggal Lahir --}}
        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">Tempat, Tanggal Lahir</label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm" style="color:#1A2422;">
                    {{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir?->tgl() ?? '-' }}
                </p>
            </div>
        </div>

        {{-- Status Akademik --}}
        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">Status Akademik</label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                    style="background-color:{{ $this->getStatusBadgeColor('status_akademik', $siswa->status_akademik) === 'zamrud' ? '#1B5E4F' : ($this->getStatusBadgeColor('status_akademik', $siswa->status_akademik) === 'emas' ? '#C9A227' : '#9B3A3A') }};color:#FFFFFF;"
                >
                    {{ $siswa->status_akademik }}
                </span>
            </div>
        </div>

        {{-- Status Penempatan --}}
        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">Status Penempatan</label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                    style="background-color:{{ $this->getStatusBadgeColor('status_penempatan', $siswa->status_penempatan) === 'zamrud' ? '#1B5E4F' : '#5B7A8C' }};color:#FFFFFF;"
                >
                    {{ $siswa->status_penempatan }}
                </span>
            </div>
        </div>

        {{-- Status Masuk --}}
        @if($siswa->status_masuk)
        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">Status Masuk</label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm" style="color:#1A2422;">
                    {{ $siswa->status_masuk }}
                    @if($siswa->tanggal_masuk)
                        <span style="color:#6B7280;">({{ $siswa->tanggal_masuk->tgl() }})</span>
                    @endif
                </p>
            </div>
        </div>
        @endif

        {{-- Asal Sekolah (hanya jika Pindahan) --}}
        @if($siswa->status_masuk === 'Pindahan' && $siswa->asal_sekolah)
        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">Asal Sekolah</label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm" style="color:#1A2422;">{{ $siswa->asal_sekolah }}</p>
            </div>
        </div>
        @endif

        {{-- Kelas Akademik --}}
        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">Kelas Akademik</label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm" style="color:#1A2422;">
                    @if($siswa->kelas)
                        <span class="font-medium" style="color:#1B5E4F;">{{ $siswa->kelas->nama_lengkap }}</span>
                        <span class="text-xs" style="color:#6B7280;">({{ $siswa->kelas->tahunAjaran->label ?? '-' }})</span>
                    @else
                        <span style="color:#6B7280;">Belum Ditentukan</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Data Orang Tua --}}
    <div class="rounded-2xl bg-white shadow-sm p-6 space-y-3" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <h3 class="text-base font-semibold mb-4 pb-2" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;border-bottom:1px solid #E5E7EB;">
            Data Orang Tua
        </h3>

        {{-- Nama Ayah --}}
        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">Nama Ayah</label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm" style="color:#1A2422;">{{ $siswa->nama_ayah ?? '-' }}</p>
            </div>
        </div>

        {{-- Nama Ibu --}}
        <div class="grid grid-cols-12 gap-3 items-center">
            <label class="col-span-3 text-sm font-medium text-right" style="color:#6B7280;">Nama Ibu</label>
            <span class="col-span-1 text-sm font-medium text-center" style="color:#6B7280;">:</span>
            <div class="col-span-8">
                <p class="text-sm" style="color:#1A2422;">{{ $siswa->nama_ibu ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Kelompok Placement --}}
    @if($siswa->kelompok)
        <div class="rounded-2xl bg-white shadow-sm p-6 space-y-4" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
            <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                Penempatan Kelompok Saat Ini
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;">Nama Kelompok</p>
                    <p class="text-sm" style="color:#1A2422;">{{ $siswa->kelompok->nama }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;">Tingkat</p>
                    <p class="text-sm" style="color:#1A2422;">{{ $siswa->kelompok->tingkat }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;">Jenis</p>
                    <p class="text-sm" style="color:#1A2422;">{{ $siswa->kelompok->jenis }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;">Guru Pembimbing</p>
                    <p class="text-sm" style="color:#1A2422;">{{ $siswa->kelompok->guruPembimbing->nama ?? '-' }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Riwayat Tes Penempatan --}}
    @if($siswa->tesPenempatans && $siswa->tesPenempatans->count() > 0)
        <div class="rounded-2xl bg-white shadow-sm p-6 space-y-4" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
            <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                Riwayat Tes Penempatan
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead>
                        <tr style="border-bottom: 2px solid #E5E7EB;">
                            <th class="pb-2 font-medium" style="color:#6B7280;">Tanggal Tes</th>
                            <th class="pb-2 font-medium" style="color:#6B7280;">Guru Penguji</th>
                            <th class="pb-2 font-medium" style="color:#6B7280;">Status</th>
                            <th class="pb-2 font-medium" style="color:#6B7280;">Rekomendasi</th>
                            <th class="pb-2 font-medium" style="color:#6B7280;">Kelompok Akhir</th>
                            <th class="pb-2 font-medium text-right" style="color:#6B7280;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($siswa->tesPenempatans->sortByDesc('tanggal_tes') as $tes)
                            <tr>
                                <td class="py-3" style="color:#1A2422;">
                                    {{ $tes->tanggal_tes->tgl() }}
                                    @if($tes->ditempatkan_at)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Final
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3" style="color:#1A2422;">{{ $tes->guruPenguji->nama ?? '-' }}</td>
                                <td class="py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color:#E8F5F1;color:#1B5E4F;">
                                        {{ $tes->status->value }}
                                    </span>
                                </td>
                                <td class="py-3" style="color:#1A2422;">{{ $tes->rekomendasi_tingkat?->value ?? '-' }}</td>
                                <td class="py-3" style="color:#1A2422;">{{ $tes->finalKelompok?->nama ?? '-' }}</td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('admin.tes-penempatan.review', $tes->id) }}" class="text-sm font-medium hover:underline" style="color:#1B5E4F;">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Informasi Keluar Siswa --}}
    <x-siswa.informasi-keluar :siswa="$siswa" />

</div>
