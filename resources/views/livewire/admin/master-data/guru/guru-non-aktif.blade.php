<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Data Guru Non-Aktif
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Daftar guru yang sudah tidak aktif mengajar di sekolah
        </p>
    </div>

    {{-- SEARCH & FILTER ROW --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

            {{-- LEFT: Action Buttons --}}
            <div class="flex flex-wrap items-center gap-3">
                @if(count($selectedIds) > 0)
                    <button
                        wire:click="bukaModalKonfirmasiBatalkan"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none"
                        style="background-color:#E0F2FE;color:#0369A1;border:1.5px solid #BAE6FD;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Batalkan ({{ count($selectedIds) }})
                    </button>
                @else
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih minimal 1 guru untuk dibatalkan"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Batalkan
                    </button>
                @endif
            </div>

            {{-- RIGHT: Search & Filter --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-[400px]">
                <div class="flex-1">
                    <x-ui.search-field
                        wireModel="search"
                        placeholder="Cari guru (NIY, Nama)..."
                    />
                </div>

                {{-- Filter Dropdown Button --}}
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        type="button"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none whitespace-nowrap cursor-pointer w-full sm:w-auto"
                        style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                        @if($filterJabatan || $filterJenisKelamin)
                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                {{ collect([$filterJabatan, $filterJenisKelamin])->filter()->count() }}
                            </span>
                        @endif
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open"
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute z-50 right-0 mt-2 w-64 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                         style="display: none; border: 1px solid #E5E7EB;">

                        <div class="p-4 space-y-3">
                            {{-- Filter Header --}}
                            <div class="flex items-center justify-between pb-2" style="border-bottom: 1px solid #E5E7EB;">
                                <h3 class="text-sm font-semibold" style="color:#1A2422;font-family:'Plus Jakarta Sans',sans-serif;">
                                    Filter Guru
                                </h3>
                                @if($filterJabatan || $filterJenisKelamin)
                                    <button
                                        wire:click="resetFilters"
                                        type="button"
                                        class="text-xs font-medium text-gray-500 hover:text-[#1B5E4F] transition-colors"
                                    >
                                        Reset
                                    </button>
                                @endif
                            </div>

                            {{-- Filter Jabatan --}}
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Jabatan
                                </label>
                                <select
                                    wire:model.live="filterJabatan"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    @foreach($jabatanOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Jenis Kelamin --}}
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Jenis Kelamin
                                </label>
                                <select
                                    wire:model.live="filterJenisKelamin"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    <option value="Laki-laki">L</option>
                                    <option value="Perempuan">P</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Removed old collapsible filter panel --}}
    </div>

    {{-- TABLE WITH BULK SELECTION --}}
    <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="background-color:#1B5E4F;color:white;">
                        <th class="px-6 py-4 text-center" style="width:60px;">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                class="w-4 h-4 rounded transition-colors cursor-pointer"
                                style="color:#1B5E4F;border-color:#D1D5DB;"
                            >
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">NIY</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">JK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tempat Lahir</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tanggal Lahir</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Jabatan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Kelompok</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tingkat</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tanggal Mulai Kerja</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Jenis Keluar</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tanggal Keluar</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Keterangan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Kontak</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    @forelse($gurus as $guru)
                        <tr class="hover:bg-gray-50 transition-colors {{ in_array($guru->id, $selectedIds) ? 'bg-[#F4F5F4]' : '' }}">
                            <td class="px-6 py-4 text-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedIds"
                                    value="{{ $guru->id }}"
                                    class="w-4 h-4 rounded transition-colors cursor-pointer"
                                    style="color:#1B5E4F;border-color:#D1D5DB;"
                                >
                            </td>
                            <td class="px-6 py-4 text-sm font-medium" style="color:#1A2422;">{{ $guru->niy }}</td>
                            <td class="px-6 py-4 text-sm font-medium" style="color:#1A2422;">{{ $guru->nama }}</td>
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                {{ $guru->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}
                            </td>
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">{{ $guru->tempat_lahir ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                {{ $guru->tanggal_lahir ? $guru->tanggal_lahir->tgl() : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                @php
                                    $jabatanColor = match($guru->jabatan) {
                                        'Guru Pembimbing Tahfidz' => ['bg' => '#E8F5F1', 'text' => '#1B5E4F'],
                                        'Kepala Sekolah' => ['bg' => '#FEF3C7', 'text' => '#C9A227'],
                                        default => ['bg' => '#E8EEF2', 'text' => '#5B7A8C'],
                                    };
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium"
                                    style="background-color:{{ $jabatanColor['bg'] }};color:{{ $jabatanColor['text'] }};"
                                >
                                    {{ $guru->jabatan }}
                                </span>
                            </td>

                            {{-- Kelompok (Riwayat Terakhir) --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                @if($guru->jabatan === 'Guru Pembimbing Tahfidz')
                                    @php
                                        $riwayatTerakhir = $guru->riwayatGuruPembimbingKelompoks->first();
                                    @endphp
                                    @if($riwayatTerakhir && $riwayatTerakhir->kelompok)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" style="background-color:#E8F5F1;color:#1B5E4F;border:1px solid #D1EAE3;">
                                            {{ $riwayatTerakhir->kelompok->nama }}
                                        </span>
                                    @else
                                        <span class="text-xs italic" style="color:#9CA3AF;">-</span>
                                    @endif
                                @else
                                    <span class="text-xs" style="color:#D1D5DB;">-</span>
                                @endif
                            </td>

                            {{-- Tingkat (Riwayat Terakhir) --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                @if($guru->jabatan === 'Guru Pembimbing Tahfidz')
                                    @php
                                        $riwayatTerakhir = $guru->riwayatGuruPembimbingKelompoks->first();
                                    @endphp
                                    @if($riwayatTerakhir && $riwayatTerakhir->kelompok)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" style="background-color:#F4F5F4;color:#5B7A8C;border:1px solid #E5E7EB;">
                                            {{ $riwayatTerakhir->kelompok->tingkat }}
                                        </span>
                                    @else
                                        <span class="text-xs italic" style="color:#9CA3AF;">-</span>
                                    @endif
                                @else
                                    <span class="text-xs" style="color:#D1D5DB;">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                {{ $guru->tanggal_mulai_kerja ? \Carbon\Carbon::parse($guru->tanggal_mulai_kerja)->tgl() : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-medium" style="background-color:#FEE2E2;color:#991B1B;">
                                    {{ $guru->jenis_keluar ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                {{ $guru->tanggal_keluar ? \Carbon\Carbon::parse($guru->tanggal_keluar)->tgl() : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm" style="color:#6B7280;">
                                {{ $guru->keterangan_keluar ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <div class="flex flex-col gap-0.5">
                                    <span>{{ $guru->no_telepon ?? '-' }}</span>
                                    @if($guru->email)
                                        <span class="text-xs" style="color:#6B7280;">{{ $guru->email }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        @if($search || $filterJabatan || $filterJenisKelamin)
                                            Tidak ada guru yang sesuai dengan filter
                                        @else
                                            Belum ada data guru non-aktif
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($gurus->hasPages())
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                {{ $gurus->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL KONFIRMASI BATALKAN NON-AKTIF --}}
    @if($showModalKonfirmasiBatalkan)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-transparent" wire:click="tutupModalKonfirmasiBatalkan"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl" style="font-family:'Inter',sans-serif;">

                {{-- Header with blue background --}}
                <div class="flex items-center gap-3 px-6 pt-6 pb-4 rounded-t-2xl" style="background-color:#F0F9FF;border-bottom:1px solid #BAE6FD;">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" style="color:#0284C7;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#075985;">
                        Konfirmasi Pembatalan Status
                    </h3>
                </div>

                {{-- Body --}}
                <div class="px-6 py-4">
                    <p class="text-sm mb-4" style="color:#1A2422;">
                        Anda akan mengaktifkan kembali <strong>{{ count($selectedIds) }} guru</strong> yang sebelumnya berstatus Non-Aktif.
                    </p>

                    <div class="mt-4 p-3 rounded-lg" style="background-color:#F0FDF4;border-left:3px solid #16A34A;">
                        <p class="text-xs font-medium" style="color:#14532D;">
                            ✅ Guru-guru tersebut akan kembali dapat diakses dan dimasukkan ke dalam tugas/kelompok.
                        </p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB;">
                    <button wire:click="tutupModalKonfirmasiBatalkan" type="button" class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none" style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;">
                        Batal
                    </button>
                    <button wire:click="prosesBatalkanBulk" type="button" class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2" style="background-color:#0284C7;color:#FFFFFF;border:1.5px solid #0284C7;">
                        <span wire:loading.remove wire:target="prosesBatalkanBulk">Ya, Aktifkan Guru</span>
                        <span wire:loading wire:target="prosesBatalkanBulk">Memproses...</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
