<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="text-sm mb-4">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-zamrud">Dashboard</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li><a href="#" class="text-gray-600 hover:text-zamrud">Master Data</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li><a href="{{ route('admin.master-data.kelas.index') }}" class="text-gray-600 hover:text-zamrud">Kelas Akademik</a></li>
            <li><span class="text-gray-400">/</span></li>
            <li class="text-zamrud font-medium">{{ $kelas->nama_lengkap }}</li>
        </ol>
    </nav>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-tinta">Detail Kelas {{ $kelas->nama_lengkap }}</h1>
            <p class="text-gray-600 mt-1">{{ $kelas->tahunAjaran->label }}</p>
        </div>
        <a href="{{ route('admin.master-data.kelas.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">← Kembali</a>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b">
            <nav class="flex">
                <button wire:click="setTab('info')" class="px-6 py-3 {{ $activeTab === 'info' ? 'border-b-2 border-zamrud text-zamrud' : 'text-gray-600' }} transition-colors">
                    Informasi Kelas
                </button>
                <button wire:click="setTab('siswas')" class="px-6 py-3 {{ $activeTab === 'siswas' ? 'border-b-2 border-zamrud text-zamrud' : 'text-gray-600' }} transition-colors">
                    Daftar Siswa ({{ $kelas->jumlah_siswa }})
                </button>
            </nav>
        </div>

        <div class="p-6">
            @if($activeTab === 'info')
                <!-- Info Tab -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold text-sm text-gray-600 mb-1">Nama Kelas</h3>
                        <p class="text-lg font-semibold">{{ $kelas->nama_lengkap }}</p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-sm text-gray-600 mb-1">Tahun Ajaran</h3>
                        <p>{{ $kelas->tahunAjaran->label }}</p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-sm text-gray-600 mb-1">Tingkat</h3>
                        <p>{{ $kelas->tingkat }}</p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-sm text-gray-600 mb-1">Rombel</h3>
                        <p>{{ $kelas->rombel }}</p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-sm text-gray-600 mb-1">Wali Kelas</h3>
                        <p>{{ $kelas->waliKelas->nama ?? '-' }}</p>
                    </div>

                    <div>
                        <h3 class="font-semibold text-sm text-gray-600 mb-1">Kapasitas</h3>
                        <p>
                            <span class="@if($kelas->is_over_capacity) text-delima @elseif($kelas->jumlah_siswa / $kelas->kapasitas > 0.8) text-emas-pudar @else text-zamrud @endif font-semibold">
                                {{ $kelas->jumlah_siswa }}/{{ $kelas->kapasitas }}
                            </span>
                            @if($kelas->is_over_capacity)
                                <span class="text-sm text-delima">(Melebihi Kapasitas)</span>
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            @if($activeTab === 'siswas')
                <!-- Siswa Tab -->
                <div class="mb-4">
                    <div class="w-full md:w-1/3">
                        <x-ui.search-field
                            wireModel="searchSiswa"
                            placeholder="Cari siswa..."
                        />
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-[#1B5E4F] text-white">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold">NIS</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Nama</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Jenis Kelamin</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($siswas as $siswa)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $siswa->nis }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $siswa->nama }}</td>
                                    <td class="px-4 py-3">{{ $siswa->jenis_kelamin->label() ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="@if($siswa->status_akademik->value === 'aktif') text-zamrud @else text-gray-500 @endif">
                                            {{ $siswa->status_akademik->label() ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Belum ada siswa di kelas ini</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if($siswas->isNotEmpty())
                        <div class="p-4">{{ $siswas->links() }}</div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
