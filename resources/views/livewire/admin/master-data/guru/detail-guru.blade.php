{{--
    View: livewire/admin/master-data/guru/detail-guru.blade.php
    Komponen Livewire: App\Livewire\Admin\MasterData\Guru\DetailGuru

    Read-only detail view untuk Guru (Requirements 4.14)
--}}
<div class="space-y-6">

    {{-- Header --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Detail Data Guru
                </h2>
                <p class="mt-1 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
                    Informasi lengkap guru dan kelompok bimbingan
                </p>
            </div>
            <div class="flex gap-3">
                <button
                    wire:click="kembali"
                    type="button"
                    class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                    style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;font-family:'Inter',sans-serif;"
                >
                    Kembali
                </button>
                <button
                    wire:click="edit"
                    type="button"
                    class="px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                    style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;font-family:'Inter',sans-serif;"
                >
                    Edit
                </button>
            </div>
        </div>
    </div>

    {{-- Header & Info --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div>
            <h3 class="text-2xl font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                {{ $guru->nama }}
            </h3>
            <p class="mt-1 text-sm" style="color:#6B7280;">
                NIY: {{ $guru->niy }}
            </p>
            <div class="mt-3 flex flex-wrap gap-2">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                    style="background-color:{{ $guru->status_aktif ? '#1B5E4F' : '#9B3A3A' }};color:#FFFFFF;"
                >
                    {{ $this->getStatusText($guru->status_aktif) }}
                </span>
                @php
                    $jabatanColor = match($guru->jabatan) {
                        'Guru Pembimbing Tahfidz' => ['bg' => '#E8F5F1', 'text' => '#1B5E4F'],
                        'Kepala Sekolah' => ['bg' => '#FEF3C7', 'text' => '#C9A227'],
                        default => ['bg' => '#E8EEF2', 'text' => '#5B7A8C'],
                    };
                @endphp
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                    style="background-color:{{ $jabatanColor['bg'] }};color:{{ $jabatanColor['text'] }};"
                >
                    {{ $guru->jabatan }}
                </span>
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                    style="background-color:#1B5E4F;color:#1A2422;border:1px solid #D1D5DB;"
                >
                    {{ $guru->jenis_kelamin }}
                </span>
            </div>
        </div>
    </div>

    {{-- Data Pribadi --}}
    <div class="rounded-2xl bg-white shadow-sm p-6 space-y-4" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1B5E4F;">
            Data Pribadi
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-medium mb-1" style="color:#6B7280;">Tempat, Tanggal Lahir</p>
                <p class="text-sm" style="color:#1A2422;">
                    {{ $guru->tempat_lahir }}, {{ $guru->tanggal_lahir?->tgl() ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-medium mb-1" style="color:#6B7280;">Jenis Kelamin</p>
                <p class="text-sm" style="color:#1A2422;">{{ $guru->jenis_kelamin ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Pendidikan & Jabatan --}}
    <div class="rounded-2xl bg-white shadow-sm p-6 space-y-4" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1B5E4F;">
            Pendidikan & Jabatan
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-medium mb-1" style="color:#6B7280;">Pendidikan Terakhir</p>
                <p class="text-sm" style="color:#1A2422;">{{ $guru->pendidikan_terakhir ?? '-' }}</p>
            </div>

            @if($guru->program_studi)
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;">Program Studi</p>
                    <p class="text-sm" style="color:#1A2422;">{{ $guru->program_studi }}</p>
                </div>
            @endif

            <div>
                <p class="text-xs font-medium mb-1" style="color:#6B7280;">Jabatan</p>
                <p class="text-sm" style="color:#1A2422;">{{ $guru->jabatan }}</p>
            </div>
        </div>
    </div>

    {{-- Kontak --}}
    <div class="rounded-2xl bg-white shadow-sm p-6 space-y-4" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1B5E4F;">
            Kontak
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-medium mb-1" style="color:#6B7280;">No. Telepon</p>
                <p class="text-sm" style="color:#1A2422;">{{ $guru->no_telepon ?? '-' }}</p>
            </div>

            @if($guru->email)
                <div>
                    <p class="text-xs font-medium mb-1" style="color:#6B7280;">Email</p>
                    <p class="text-sm" style="color:#1A2422;">{{ $guru->email }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Tugas Tambahan --}}
    @if($guru->tugasTambahans && $guru->tugasTambahans->count() > 0)
        <div class="rounded-2xl bg-white shadow-sm p-6 space-y-4" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
            <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1B5E4F;">
                Tugas Tambahan
            </h3>

            <div class="flex flex-wrap gap-2">
                @foreach($guru->tugasTambahans as $tugas)
                    <span
                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium"
                        style="background-color:#E8EEF2;color:#5B7A8C;"
                    >
                        {{ $tugas->jenis_tugas }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Kelompok Bimbingan --}}
    @if($guru->kelompoksBimbingan && $guru->kelompoksBimbingan->count() > 0)
        <div class="rounded-2xl bg-white shadow-sm p-6 space-y-4" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
            <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1B5E4F;">
                Kelompok Bimbingan
            </h3>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr style="background-color:#1B5E4F;">
                            <th class="px-4 py-3 text-left text-xs font-semibold" >Nama Kelompok</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold" >Tingkat</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold" >Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold" >Tahun Ajaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color:#E5E7EB;">
                        @foreach($guru->kelompoksBimbingan as $kelompok)
                            <tr>
                                <td class="px-4 py-3 text-sm" style="color:#1A2422;">{{ $kelompok->nama }}</td>
                                <td class="px-4 py-3 text-sm" style="color:#1A2422;">{{ $kelompok->tingkat }}</td>
                                <td class="px-4 py-3 text-sm" style="color:#1A2422;">{{ $kelompok->jenis }}</td>
                                <td class="px-4 py-3 text-sm" style="color:#1A2422;">
                                    {{ $kelompok->tahunAjaran->label ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
