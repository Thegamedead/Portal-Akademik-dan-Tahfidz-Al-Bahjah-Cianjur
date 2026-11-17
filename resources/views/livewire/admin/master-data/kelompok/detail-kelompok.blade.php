{{--
    View: livewire/admin/master-data/kelompok/detail-kelompok.blade.php
    Komponen Livewire: App\Livewire\Admin\MasterData\Kelompok\DetailKelompok

    Read-only detail page untuk Kelompok Tahfidz
    Requirements: 5.9, 5.13
--}}
<div class="space-y-6">

    {{-- Header with Actions --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Detail Kelompok Tahfidz
                </h2>
                <p class="mt-1 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
                    Informasi lengkap kelompok {{ $kelompok->nama }}
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button
                    type="button"
                    wire:click="kembali"
                    class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                    style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;font-family:'Inter',sans-serif;focus:ring-color:#D1D5DB;"
                >
                    ← Kembali
                </button>
                <button
                    type="button"
                    wire:click="edit"
                    class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                    style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;font-family:'Inter',sans-serif;focus:ring-color:#1B5E4F;"
                >
                    Edit Kelompok
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- INFORMASI KELOMPOK --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <h3 class="text-base font-semibold mb-4" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
            Informasi Kelompok
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Nama Kelompok --}}
            <div>
                <label class="text-xs font-medium mb-1 block" style="color:#6B7280;">Nama Kelompok</label>
                <p class="text-sm font-medium" style="color:#1A2422;">{{ $kelompok->nama }}</p>
            </div>

            {{-- Jenis --}}
            <div>
                <label class="text-xs font-medium mb-1 block" style="color:#6B7280;">Jenis</label>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium"
                    style="background-color:{{ $this->getJenisBadgeColor() === 'slate' ? '#F1F5F9' : '#E0F2FE' }};
                           color:{{ $this->getJenisBadgeColor() === 'slate' ? '#475569' : '#0369A1' }};">
                    {{ $kelompok->jenis }}
                    @if($kelompok->jenis === 'Banin')
                        (Putra)
                    @else
                        (Putri)
                    @endif
                </span>
            </div>

            {{-- Tingkat --}}
            <div>
                <label class="text-xs font-medium mb-1 block" style="color:#6B7280;">Tingkat</label>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium"
                    @php
                        $bgColor = match($this->getTingkatBadgeColor()) {
                            'zamrud' => '#E8F5F1',
                            'emas' => '#FEF3C7',
                            default => '#F1F5F9'
                        };
                        $textColor = match($this->getTingkatBadgeColor()) {
                            'zamrud' => '#1B5E4F',
                            'emas' => '#92400E',
                            default => '#475569'
                        };
                    @endphp
                    style="background-color:{{ $bgColor }};color:{{ $textColor }};">
                    {{ $kelompok->tingkat }}
                </span>
            </div>

            {{-- Tahun Ajaran --}}
            <div>
                <label class="text-xs font-medium mb-1 block" style="color:#6B7280;">Tahun Ajaran</label>
                <p class="text-sm font-medium" style="color:#1A2422;">
                    {{ $kelompok->tahunAjaran->label }}
                    @if($kelompok->tahunAjaran->status)
                        <span class="text-xs" style="color:#1B5E4F;">(Aktif)</span>
                    @endif
                </p>
            </div>

            {{-- Guru Pembimbing --}}
            <div class="md:col-span-2">
                <label class="text-xs font-medium mb-1 block" style="color:#6B7280;">Guru Pembimbing Tahfidz</label>
                <p class="text-sm font-medium" style="color:#1A2422;">
                    {{ $kelompok->guruPembimbing->nama }}
                    <span class="text-xs" style="color:#6B7280;">(NIY: {{ $kelompok->guruPembimbing->niy }})</span>
                </p>
            </div>

            {{-- Kapasitas --}}
            <div>
                <label class="text-xs font-medium mb-1 block" style="color:#6B7280;">Kapasitas</label>
                <p class="text-sm" style="color:#1A2422;">
                    <span class="font-medium">{{ $jumlahSiswa }}</span> / {{ $kelompok->kapasitas_maks }} siswa
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium"
                        @php
                            $bgColor = $this->getStatusKapasitasColor() === 'zamrud' ? '#E8F5F1' : '#FCE7E7';
                            $textColor = $this->getStatusKapasitasColor() === 'zamrud' ? '#1B5E4F' : '#9B3A3A';
                        @endphp
                        style="background-color:{{ $bgColor }};color:{{ $textColor }};">
                        {{ $statusKapasitas }}
                    </span>
                </p>
                <p class="mt-1 text-xs" style="color:#6B7280;">
                    Min: {{ $kelompok->kapasitas_min }}, Maks: {{ $kelompok->kapasitas_maks }}
                </p>
            </div>

            {{-- Status Kapasitas --}}
            <div>
                <label class="text-xs font-medium mb-1 block" style="color:#6B7280;">Sisa Kapasitas</label>
                <p class="text-sm font-medium" style="color:#1A2422;">
                    {{ max(0, $kelompok->kapasitas_maks - $jumlahSiswa) }} tempat
                </p>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- JADWAL KEGIATAN --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <h3 class="text-base font-semibold mb-4" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
            Jadwal Kegiatan
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- DAFTAR ANGGOTA KELOMPOK --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                Daftar Anggota ({{ $kelompok->siswas->count() }})
            </h3>
        </div>

        @if($kelompok->siswas->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background-color:#1B5E4F;" class="text-white">
                            <th class="px-4 py-3 text-left text-xs font-semibold">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">NIS</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">Nama Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">Jenis Kelamin</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold">Status Akademik</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold" style="color:white;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelompok->siswas as $index => $siswa)
                            <tr style="border-bottom:1px solid #E5E7EB;">
                                <td class="px-4 py-3" style="color:#1A2422;">{{ $index + 1 }}</td>
                                <td class="px-4 py-3" style="color:#1A2422;">{{ $siswa->nis }}</td>
                                <td class="px-4 py-3 font-medium" style="color:#1A2422;">{{ $siswa->nama }}</td>
                                <td class="px-4 py-3" style="color:#6B7280;">{{ $siswa->jenis_kelamin }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium"
                                        @php
                                            $bgColor = $siswa->status_akademik === 'Aktif' ? '#E8F5F1' : '#F1F5F9';
                                            $textColor = $siswa->status_akademik === 'Aktif' ? '#1B5E4F' : '#475569';
                                        @endphp
                                        style="background-color:{{ $bgColor }};color:{{ $textColor }};">
                                        {{ $siswa->status_akademik }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a
                                        href="{{ route('admin.master-data.siswa.detail', $siswa->id) }}"
                                        class="text-xs font-medium hover:underline"
                                        style="color:#1B5E4F;"
                                    >
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-sm" style="color:#6B7280;">Belum ada siswa yang ditempatkan di kelompok ini</p>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- RIWAYAT GURU PEMBIMBING --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}

    @if($riwayatGuruPembimbing->count() > 0)
        <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
            <h3 class="text-base font-semibold mb-4" style="font-family:'Plus Jakarta Sans',sans-serif;color:white;">
                Riwayat Pergantian Guru Pembimbing
            </h3>

            <div class="space-y-4">
                @foreach($riwayatGuruPembimbing as $riwayat)
                    <div class="p-4 rounded-lg" style="background-color:#F9FAFB;border:1px solid #E5E7EB;">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color:#E8F5F1;">
                                    <svg class="w-5 h-5" style="color:#1B5E4F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-xs font-medium px-2 py-0.5 rounded" style="background-color:#E0E7FF;color:#3730A3;">
                                        {{ $this->formatTanggal($riwayat->tanggal_efektif) }}
                                    </span>
                                </div>
                                <p class="text-sm mb-1" style="color:#1A2422;">
                                    <span class="font-medium">Dari:</span> {{ $riwayat->guruDari->nama }}
                                    <span class="text-xs" style="color:#6B7280;">({{ $riwayat->guruDari->niy }})</span>
                                </p>
                                <p class="text-sm mb-2" style="color:#1A2422;">
                                    <span class="font-medium">Ke:</span> {{ $riwayat->guruKe->nama }}
                                    <span class="text-xs" style="color:#6B7280;">({{ $riwayat->guruKe->niy }})</span>
                                </p>
                                @if($riwayat->alasan)
                                    <div class="mt-2 p-2 rounded" style="background-color:#FFFBEB;border-left:3px solid #F59E0B;">
                                        <p class="text-xs font-medium mb-0.5" style="color:#92400E;">Alasan:</p>
                                        <p class="text-xs" style="color:#78350F;">{{ $riwayat->alasan }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
