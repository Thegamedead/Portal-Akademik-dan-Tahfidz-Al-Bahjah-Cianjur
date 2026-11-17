<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Data Siswa Non-Aktif
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Daftar siswa yang berstatus tidak aktif (Lulus, Keluar, Pindah, dll).
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

                    @if(count($selectedIds) === 1)
                        <a
                            href="{{ route('admin.setoran.riwayat-hafalan', ['siswa' => $selectedIds[0], 'kelompok_id' => 'all']) }}"
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none shadow-sm"
                            style="background-color:#0D9488;color:#FFFFFF;border:1.5px solid #0D9488;"
                            title="Lihat rekam jejak riwayat hafalan siswa"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Track Record Hafalan
                        </a>
                    @endif
                @else
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih minimal 1 siswa untuk dibatalkan"
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
                        placeholder="Cari siswa (NIS, NISN, Nama)..."
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
                        @if($filterStatusAkademik || $filterJenisKelamin)
                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                {{ collect([$filterStatusAkademik, $filterJenisKelamin])->filter()->count() }}
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
                                    Filter Siswa
                                </h3>
                                @if($filterStatusAkademik || $filterJenisKelamin)
                                    <button
                                        wire:click="resetFilters"
                                        type="button"
                                        class="text-xs font-medium text-gray-500 hover:text-[#1B5E4F] transition-colors"
                                    >
                                        Reset
                                    </button>
                                @endif
                            </div>

                            {{-- Filter Status Akademik --}}
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Status Akademik
                                </label>
                                <select
                                    wire:model.live="filterStatusAkademik"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua</option>
                                    <option value="Lulus SMP">Lulus SMP</option>
                                    <option value="Pindah Sekolah">Pindah Sekolah</option>
                                    <option value="Keluar">Keluar</option>
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

    {{-- DATA TABLE --}}
    <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
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
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">NIS/NISN</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">JK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tempat, Tanggal Lahir</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Status / Jenis Keluar</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tanggal Keluar</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($siswas as $siswa)
                        <tr class="hover:bg-gray-50 transition-colors {{ in_array($siswa->id, $selectedIds) ? 'bg-green-50' : '' }}">
                            <td class="px-6 py-4 text-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedIds"
                                    value="{{ $siswa->id }}"
                                    class="w-4 h-4 rounded transition-colors cursor-pointer"
                                    style="color:#1B5E4F;border-color:#D1D5DB;"
                                >
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium" style="color:#1A2422;">{{ $siswa->nis }}</div>
                                <div class="text-xs" style="color:#6B7280;">{{ $siswa->nisn ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5">
                                    <div class="font-medium text-sm" style="color:#1A2422;">{{ $siswa->nama }}</div>
                                    <a href="{{ route('admin.setoran.riwayat-hafalan', ['siswa' => $siswa->id, 'kelompok_id' => 'all']) }}"
                                       class="inline-flex items-center justify-center w-6 h-6 rounded hover:bg-emerald-100 text-[#1B5E4F] transition-colors"
                                       title="Track Record Hafalan: {{ $siswa->nama }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </a>
                                </div>
                                @if($siswa->status_tahfidz === 'Lulus Program Tahfidz' || $siswa->progresHafalan?->status_tahfidz === 'Lulus')
                                    <div class="text-[11px] font-semibold text-emerald-700 mt-0.5 flex items-center gap-1">
                                        🎓 Alumni Tahfidz ({{ $siswa->progresHafalan?->total_juz_tuntas ?? 0 }} Juz)
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">{{ $siswa->jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }}</td>
                            <td class="px-6 py-4 text-sm whitespace-nowrap" style="color:#1A2422;">
                                @php
                                    $tempat = $siswa->tempat_lahir;
                                    $tglLahir = $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d/m/Y') : null;
                                    $ttl = $tempat && $tglLahir 
                                        ? "{$tempat}, {$tglLahir}" 
                                        : ($tempat ?? ($tglLahir ?? '-'));
                                @endphp
                                {{ $ttl }}
                            </td>
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">{{ $siswa->kelas?->nama_lengkap ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($siswa->status_tahfidz === 'Lulus Program Tahfidz' || $siswa->progresHafalan?->status_tahfidz === 'Lulus')
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold" style="background-color:#E6F4EA;color:#137333;border:1px solid #CEEAD6;">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Lulus Program Tahfidz
                                        </span>
                                        <span class="text-[11px] font-medium" style="color:#1B5E4F;">
                                            Akademik: {{ $siswa->status_akademik ?? 'Lulus SMP' }}
                                        </span>
                                    </div>
                                @elseif($siswa->jenis_keluar === 'Pindah Sekolah')
                                    <div class="flex flex-col gap-0.5 items-start">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium" style="background-color:#FEF3C7;color:#92400E;border:1px solid #FDE68A;">
                                            Pindah Sekolah
                                        </span>
                                        @if($siswa->tujuan_sekolah)
                                            <span class="text-[11px] text-gray-500">Ke: {{ $siswa->tujuan_sekolah }}</span>
                                        @endif
                                    </div>
                                @elseif($siswa->jenis_keluar === 'Meninggal')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium" style="background-color:#F3F4F6;color:#374151;border:1px solid #E5E7EB;">
                                        Meninggal
                                    </span>
                                @elseif($siswa->jenis_keluar === 'Keluar')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium" style="background-color:#FEE2E2;color:#991B1B;border:1px solid #FECACA;">
                                        Keluar (DO / Dikeluarkan)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium" style="background-color:#F3F4F6;color:#4B5563;">
                                        {{ $siswa->jenis_keluar ?? $siswa->status_akademik ?? 'Non-Aktif' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                {{ $siswa->tanggal_keluar ? \Carbon\Carbon::parse($siswa->tanggal_keluar)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm" style="color:#6B7280;">
                                {{ $siswa->keterangan_keluar ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <span class="block text-sm font-medium">Tidak ada data siswa non-aktif.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($siswas->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $siswas->links() }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL KONFIRMASI BATALKAN NON-AKTIF --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

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
                        Anda akan mengaktifkan kembali <strong>{{ count($selectedIds) }} siswa</strong> yang sebelumnya berstatus Non-Aktif.
                    </p>

                    <div class="mt-4 p-3 rounded-lg" style="background-color:#F0FDF4;border-left:3px solid #16A34A;">
                        <p class="text-xs font-medium" style="color:#14532D;">
                            ✅ Data riwayat keluar siswa-siswa tersebut akan dihapus, dan mereka akan kembali ke daftar Siswa Aktif.
                        </p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex gap-3 px-6 py-4" style="border-top:1px solid #E5E7EB;">
                    <button wire:click="tutupModalKonfirmasiBatalkan" type="button" class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none" style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;">
                        Batal
                    </button>
                    <button wire:click="prosesBatalkanBulk" type="button" class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2" style="background-color:#0284C7;color:#FFFFFF;border:1.5px solid #0284C7;">
                        <span wire:loading.remove wire:target="prosesBatalkanBulk">Ya, Aktifkan Siswa</span>
                        <span wire:loading wire:target="prosesBatalkanBulk">Memproses...</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
