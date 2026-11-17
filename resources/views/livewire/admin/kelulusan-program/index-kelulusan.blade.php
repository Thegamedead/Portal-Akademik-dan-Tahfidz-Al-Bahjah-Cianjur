<div class="space-y-6">

    {{-- Header --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
                    Kelulusan Program Tahfidz
                </h1>
                <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
                    Review dan proses kelulusan siswa tingkat Atas yang telah menyelesaikan seluruh program tahfidz
                </p>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            <div class="bg-linear-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide" style="font-family:'Inter',sans-serif;">Total Usulan</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-linear-to-br from-amber-50 to-amber-100 rounded-xl p-4 border border-amber-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-amber-600 uppercase tracking-wide" style="font-family:'Inter',sans-serif;">Menunggu Review</p>
                        <p class="text-2xl font-bold text-amber-900 mt-1" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $stats['diajukan'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-linear-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-600 uppercase tracking-wide" style="font-family:'Inter',sans-serif;">Disetujui</p>
                        <p class="text-2xl font-bold text-green-900 mt-1" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $stats['disetujui'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-linear-to-br from-red-50 to-red-100 rounded-xl p-4 border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-red-600 uppercase tracking-wide" style="font-family:'Inter',sans-serif;">Ditolak</p>
                        <p class="text-2xl font-bold text-red-900 mt-1" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $stats['ditolak'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar: Action Buttons LEFT + Search & Filter RIGHT --}}
    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

            {{-- LEFT: Action Buttons in Horizontal Row --}}
            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Review Button (enabled only when 1 item selected) --}}
                @if(count($selectedIds) === 1)
                    <button
                        wire:click="openReviewModal({{ $selectedIds[0] }})"
                        type="button"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none cursor-pointer shadow-xs"
                        style="background-color:#1B5E4F;color:#FFFFFF;border:1.5px solid #1B5E4F;"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Review
                    </button>

                    @if($this->selectedSiswa)
                        {{-- Tombol Track Record Seluruh Hafalan (1 Halaman Penuh) --}}
                        <a
                            href="{{ route('admin.setoran.riwayat-hafalan', ['siswa' => $this->selectedSiswa->id, 'kelompok_id' => 'all']) }}"
                            target="_blank"
                            class="inline-flex items-center px-3.5 py-2.5 text-sm font-semibold rounded-lg transition-all focus:outline-none shadow-xs hover:bg-emerald-50"
                            style="background-color:#FFFFFF;color:#1B5E4F;border:1.5px solid #1B5E4F;"
                            title="Buka rekam jejak seluruh riwayat hafalan siswa dalam 1 halaman penuh"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            <span>Track Record Seluruh Hafalan</span>
                        </a>

                        {{-- Tombol Rekam Jejak Perjalanan (Modal Popup) --}}
                        <button
                            wire:click="openJourneyModal({{ $this->selectedSiswa->id }})"
                            type="button"
                            class="inline-flex items-center px-3 py-2.5 text-sm font-semibold rounded-lg transition-all focus:outline-none shadow-xs hover:bg-blue-50 cursor-pointer"
                            style="background-color:#FFFFFF;color:#2563EB;border:1.5px solid #BFDBFE;"
                            title="Lihat modal rekam jejak tahapan perjalanan siswa"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span>Perjalanan Tingkat</span>
                        </button>
                    @endif
                @else
                    <button
                        disabled
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg cursor-not-allowed opacity-50 transition-colors"
                        style="background-color:#F3F4F6;color:#9CA3AF;border:1.5px solid #E5E7EB;"
                        title="Pilih tepat 1 usulan untuk di-review"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Review
                    </button>
                @endif

                {{-- Info Label --}}
                <div class="flex items-center sm:ml-2 sm:pl-4 sm:border-l border-gray-200">
                    <span class="text-sm text-gray-500">
                        Total <strong style="color:#1A2422;">{{ $kelulusans->total() }}</strong> usulan
                        @if(count($selectedIds) > 0)
                            <span class="mx-1.5">&bull;</span>
                            <strong style="color:#1B5E4F;">{{ count($selectedIds) }}</strong> terpilih
                        @endif
                    </span>
                </div>
            </div>

            {{-- RIGHT: Search Field + Filter Dropdown Button --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-100">

                {{-- Search Bar --}}
                <div class="flex-1">
                    <x-ui.search-field
                        wireModel="search"
                        placeholder="Cari nama siswa atau NIS..."
                    />
                </div>

            {{-- Filter Dropdown Button --}}
            <div x-data="{ open: false }" class="relative">
                <button
                    @click="open = !open"
                    type="button"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors focus:outline-none whitespace-nowrap cursor-pointer w-full sm:w-auto"
                    style="background-color:#FFFFFF;color:#1A2422;border:1.5px solid #D1D5DB;font-family:'Inter',sans-serif;"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filter
                    @if($filterStatus || $filterKelompok || $sortBy !== 'tanggal_usul_desc')
                        <span class="ml-1.5 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-green-600 rounded-full">
                            {{ collect([$filterStatus, $filterKelompok, $sortBy !== 'tanggal_usul_desc' ? 1 : 0])->filter()->count() }}
                        </span>
                    @endif
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                {{-- Dropdown Content --}}
                <div
                    x-show="open"
                    @click.away="open = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute z-50 right-0 mt-2 w-80 bg-white rounded-xl shadow-lg focus:outline-none overflow-hidden"
                    style="display:none; border:1px solid #E5E7EB;"
                >
                    <div class="p-4 space-y-4">
                        {{-- Filter Status --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2" style="font-family:'Inter',sans-serif;">Status Kelulusan</label>
                            <select wire:model.live="filterStatus" class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" style="border:1.5px solid #D1D5DB;font-family:'Inter',sans-serif;">
                                <option value="">Semua Status</option>
                                <option value="Diajukan">Diajukan</option>
                                <option value="Disetujui">Disetujui</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                        </div>

                        {{-- Filter Kelompok --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2" style="font-family:'Inter',sans-serif;">Kelompok</label>
                            <select wire:model.live="filterKelompok" class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" style="border:1.5px solid #D1D5DB;font-family:'Inter',sans-serif;">
                                <option value="">Semua Kelompok</option>
                                @foreach($kelompoks as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Sort --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2" style="font-family:'Inter',sans-serif;">Urutkan</label>
                            <select wire:model.live="sortBy" class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" style="border:1.5px solid #D1D5DB;font-family:'Inter',sans-serif;">
                                <option value="tanggal_usul_desc">Terbaru</option>
                                <option value="tanggal_usul_asc">Terlama</option>
                                <option value="tanggal_tuntas_desc">Tanggal Tuntas ↓</option>
                                <option value="tanggal_tuntas_asc">Tanggal Tuntas ↑</option>
                                <option value="nama_asc">Nama A-Z</option>
                            </select>
                        </div>

                        {{-- Reset Button --}}
                        @if($search || $filterStatus || $filterKelompok || $sortBy !== 'tanggal_usul_desc')
                            <div class="pt-2 border-t border-gray-200">
                                <button
                                    wire:click="resetFilters"
                                    type="button"
                                    class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
                                    style="font-family:'Inter',sans-serif;"
                                >
                                    Reset Semua Filter
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl bg-white shadow-sm overflow-hidden" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="background-color:#1B5E4F;color:white;">
                        {{-- Checkbox Column --}}
                        <th class="px-6 py-4 text-center" style="width:60px;">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                class="w-4 h-4 rounded transition-colors cursor-pointer"
                                style="color:#1B5E4F;border-color:#D1D5DB;"
                            >
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Nama Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">NIS</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Kelompok Terakhir</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tanggal Tuntas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Pengusul</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    @forelse($kelulusans as $index => $kelulusan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Checkbox --}}
                            <td class="px-6 py-4 text-center">
                                <input
                                    type="checkbox"
                                    wire:model.live="selectedIds"
                                    value="{{ $kelulusan->id }}"
                                    class="w-4 h-4 rounded transition-colors cursor-pointer"
                                    style="color:#1B5E4F;border-color:#D1D5DB;"
                                >
                            </td>
                            {{-- Nama Siswa --}}
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold" style="color:#1A2422;font-family:'Plus Jakarta Sans',sans-serif;">{{ $kelulusan->siswa->nama }}</p>
                            </td>
                            {{-- NIS --}}
                            <td class="px-6 py-4 text-sm font-medium text-gray-600">
                                {{ $kelulusan->siswa->nis }}
                            </td>
                            {{-- Kelas --}}
                            <td class="px-6 py-4 text-sm font-medium text-gray-700">
                                {{ $kelulusan->siswa->kelas->nama_kelas ?? '-' }}
                            </td>
                            {{-- Kelompok Terakhir --}}
                            <td class="px-6 py-4 text-sm" style="color:#1A2422;">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium" style="background-color:#F3E8FF;color:#6B21A8;">
                                    {{ $kelulusan->kelompok->nama }}
                                </span>
                            </td>
                            {{-- Tanggal Tuntas --}}
                            <td class="px-6 py-4 text-sm font-medium" style="color:#1A2422;">
                                {{ $kelulusan->tanggal_tuntas ? $kelulusan->tanggal_tuntas->format('d M Y') : '-' }}
                            </td>
                            {{-- Pengusul --}}
                            <td class="px-6 py-4 text-sm font-medium" style="color:#1A2422;">
                                {{ $kelulusan->guruPengusul->nama ?? 'Admin' }}
                            </td>
                            {{-- Status --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $kelulusan->status_kelulusan->badge() }}" style="font-family:'Inter',sans-serif;">
                                    {{ $kelulusan->status_kelulusan->label() }}
                                </span>
                            </td>
                            {{-- Keterangan --}}
                            <td class="px-6 py-4 text-sm text-gray-800">
                                @if($kelulusan->catatan)
                                    <span>{{ $kelulusan->catatan }}</span>
                                @else
                                    <span class="font-medium">Lulus Program Tahfidz</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        @if($search || $filterStatus || $filterKelompok)
                                            Tidak ada usulan kelulusan yang sesuai dengan filter
                                        @else
                                            Belum ada usulan kelulusan program tahfidz
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($kelulusans->hasPages())
            <div class="px-6 py-4" style="border-top:1px solid #E5E7EB;">
                {{ $kelulusans->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Review --}}
    @if($isReviewModalOpen && $selectedKelulusan)
        {{-- Backdrop --}}
        <div class="fixed inset-0 z-40 pointer-events-none" style="background:rgba(0,0,0,0.5);" wire:click="closeReviewModal"></div>

        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col pointer-events-auto" style="border:1px solid #E5E7EB;">
                {{-- Header --}}
                <div class="px-6 py-4 flex items-center justify-between shrink-0" style="background:linear-gradient(135deg,#1B5E4F,#134338);">
                    <div>
                        <h3 class="text-lg font-bold text-white" style="font-family:'Plus Jakarta Sans',sans-serif;">Review Kelulusan Program Tahfidz</h3>
                        <p class="text-sm mt-0.5" style="color:rgba(255,255,255,0.75);">Verifikasi progres hafalan dan proses kelulusan siswa</p>
                    </div>
                    <button wire:click="closeReviewModal" class="shrink-0 ml-4 text-white/70 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Content --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-4" style="font-family:'Inter',sans-serif;">
                    {{-- Identitas Siswa --}}
                    <div class="flex items-center gap-4 p-4 rounded-xl" style="background:#F8FAFC;border:1px solid #E2E8F0;">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg shrink-0" style="background:#1B5E4F;">
                            {{ mb_substr($selectedKelulusan->siswa->nama, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-900" style="font-family:'Plus Jakarta Sans',sans-serif;">{{ $selectedKelulusan->siswa->nama }}</p>
                            <p class="text-sm text-gray-500">NIS: {{ $selectedKelulusan->siswa->nis }} &bull; {{ $selectedKelulusan->siswa->kelas->nama_kelas ?? '-' }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $selectedKelulusan->status_kelulusan->badge() }}" style="flex-shrink:0;">
                            {{ $selectedKelulusan->status_kelulusan->label() }}
                        </span>
                    </div>

                    {{-- Progres Hafalan --}}
                    @php
                        $progres = $selectedKelulusan->siswa->progresHafalans->first();
                        $totalJuz = $progres?->total_juz_tuntas ?? 0;
                        $config = App\Models\ParameterKonfigurasi::getInstance();
                        $targetJuz = $config?->target_kelulusan ?? $progres?->target_juz ?? 3;
                        $progressPct = $targetJuz > 0 ? min(round(($totalJuz / $targetJuz) * 100), 100) : 0;
                    @endphp
                    <div class="rounded-xl p-4 space-y-3" style="background:#E8F5F1;border:1px solid #A7D7C5;">
                        <p class="text-xs font-bold uppercase tracking-wider" style="color:#1B5E4F;">Progres Hafalan (Lifetime)</p>
                        <div class="flex items-end gap-6">
                            <div>
                                <p class="text-4xl font-extrabold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">{{ $totalJuz }}</p>
                                <p class="text-xs text-gray-500">Juz Tuntas</p>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                                    <span>Target: {{ $targetJuz }} Juz</span>
                                    <span class="font-semibold" style="color:#1B5E4F;">{{ $progressPct }}%</span>
                                </div>
                                <div class="h-3 rounded-full overflow-hidden" style="background:#C6E6D8;">
                                    <div class="h-full rounded-full transition-all" style="width:{{ $progressPct }}%;background:linear-gradient(90deg,#1B5E4F,#27AE85);"></div>
                                </div>
                                @if($progres?->status_tahfidz === 'Lulus')
                                    <p class="text-xs font-semibold mt-1" style="color:#1B5E4F;">✓ Target hafalan tercapai</p>
                                @else
                                    <p class="text-xs mt-1" style="color:#6B7280;">{{ max(0, $targetJuz - $totalJuz) }} juz lagi untuk mencapai target</p>
                                @endif
                            </div>
                        </div>
                        @if($progres?->juz_berjalan)
                            <p class="text-xs" style="color:#374151;">Juz berjalan: <strong>Juz {{ $progres->juz_berjalan }}</strong>
                                @if($progres->juz_berjalan_percentage)
                                    ({{ $progres->juz_berjalan_percentage }}%)
                                @endif
                            </p>
                        @endif
                    </div>

                    {{-- Info Detail --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-xl p-3.5" style="background:#F8FAFC;border:1px solid #E2E8F0;">
                            <p class="text-xs text-gray-500 font-medium mb-1">Kelompok Terakhir</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $selectedKelulusan->kelompok->nama }}</p>
                            <p class="text-xs text-gray-400">Tingkat Atas</p>
                        </div>
                        <div class="rounded-xl p-3.5" style="background:#F8FAFC;border:1px solid #E2E8F0;">
                            <p class="text-xs text-gray-500 font-medium mb-1">Tanggal Tuntas Target</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $selectedKelulusan->tanggal_tuntas->translatedFormat('d F Y') }}</p>
                        </div>
                        <div class="rounded-xl p-3.5" style="background:#F8FAFC;border:1px solid #E2E8F0;">
                            <p class="text-xs text-gray-500 font-medium mb-1">Tanggal Pengajuan</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $selectedKelulusan->tanggal_usul->translatedFormat('d F Y') }}</p>
                        </div>
                        <div class="rounded-xl p-3.5" style="background:#F8FAFC;border:1px solid #E2E8F0;">
                            <p class="text-xs text-gray-500 font-medium mb-1">Diajukan Oleh</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $selectedKelulusan->guruPengusul->nama ?? 'Admin' }}</p>
                        </div>
                    </div>

                    {{-- Catatan usulan --}}
                    @if($selectedKelulusan->catatan && $selectedKelulusan->status_kelulusan->value === 'Diajukan')
                        <div class="rounded-xl p-3.5" style="background:#EFF6FF;border:1px solid #BFDBFE;">
                            <p class="text-xs font-bold text-blue-800 mb-1">Catatan dari Pengusul:</p>
                            <p class="text-sm text-blue-900">{{ $selectedKelulusan->catatan }}</p>
                        </div>
                    @endif

                    {{-- Form Keputusan (hanya jika masih Diajukan) --}}
                    @if($selectedKelulusan->status_kelulusan->value === 'Diajukan')
                        <div class="rounded-xl p-4 space-y-4" style="border:2px dashed #D1D5DB;">
                            <h4 class="font-bold text-gray-900" style="font-family:'Plus Jakarta Sans',sans-serif;">Ambil Keputusan</h4>
                            <div class="flex gap-3">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" wire:model.live="keputusan" value="setujui" class="peer sr-only">
                                    <div class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-gray-200 transition-all peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="font-semibold text-sm text-gray-900">Setujui Kelulusan</span>
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" wire:model.live="keputusan" value="tolak" class="peer sr-only">
                                    <div class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-gray-200 transition-all peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-300">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="font-semibold text-sm text-gray-900">Tolak Kelulusan</span>
                                    </div>
                                </label>
                            </div>
                            @error('keputusan') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

                            {{-- Input Tanggal Kelulusan (muncul saat setujui dipilih) --}}
                            @if($keputusan === 'setujui')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Tanggal Kelulusan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" wire:model="tanggalKelulusan"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1B5E4F] focus:border-[#1B5E4F] transition-all"
                                        max="{{ now()->format('Y-m-d') }}"
                                        value="{{ now()->format('Y-m-d') }}"
                                    >
                                    @error('tanggalKelulusan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Catatan Keputusan <span class="text-gray-400 font-normal">(opsional untuk setujui, wajib untuk tolak)</span>
                                </label>
                                <textarea wire:model="catatanKeputusan" rows="3"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1B5E4F] focus:border-[#1B5E4F] transition-all"
                                    placeholder="Tambahkan catatan keputusan..."></textarea>
                            </div>

                            @if($keputusan === 'setujui')
                                <div class="rounded-xl p-3" style="background:#FEF3C7;border:1px solid #FCD34D;">
                                    <p class="text-xs font-semibold text-amber-800">⚠ Perhatian: Menyetujui akan mengubah status siswa menjadi <strong>Lulus Program Tahfidz</strong>. Keputusan dapat dibatalkan jika diperlukan.</p>
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- Info keputusan yang sudah ada --}}
                        <div class="rounded-xl p-4 space-y-2" style="background:#F8FAFC;border:1px solid #E2E8F0;">
                            <h4 class="font-bold text-gray-900 text-sm">Informasi Keputusan</h4>
                            <div class="text-sm space-y-1.5">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Diproses oleh:</span>
                                    <span class="font-medium text-gray-900">{{ $selectedKelulusan->admin->nama ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Tanggal Keputusan:</span>
                                    <span class="font-medium text-gray-900">{{ $selectedKelulusan->tanggal_keputusan?->translatedFormat('d F Y H:i') ?? '-' }}</span>
                                </div>
                                @if($selectedKelulusan->catatan)
                                    <div class="mt-2 rounded-lg p-2.5" style="background:#F0FDF4;">
                                        <p class="text-xs text-gray-500 mb-1">Catatan:</p>
                                        <p class="text-sm text-gray-800">{{ $selectedKelulusan->catatan }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="flex justify-end gap-3 px-6 py-4 shrink-0" style="border-top:1px solid #E5E7EB;background:#F9FAFB;">
                    <button wire:click="closeReviewModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                        {{ $selectedKelulusan->status_kelulusan->value === 'Diajukan' ? 'Batal' : 'Tutup' }}
                    </button>
                    @if($selectedKelulusan->status_kelulusan->value === 'Diajukan')
                        <button wire:click="prosesKeputusan" wire:loading.attr="disabled"
                            class="px-6 py-2 text-sm font-bold text-white rounded-xl transition-all disabled:opacity-50"
                            style="background:#1B5E4F;">
                            <span wire:loading.remove>Proses Kelulusan</span>
                            <span wire:loading>Memproses...</span>
                        </button>
                    @elseif($selectedKelulusan->status_kelulusan->value === 'Disetujui')
                        <button wire:click="batalKeputusan" wire:loading.attr="disabled"
                            wire:confirm="Apakah Anda yakin ingin membatalkan keputusan kelulusan ini? Status tahfidz siswa akan dikembalikan menjadi Sedang Berjalan."
                            class="px-6 py-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl transition-all disabled:opacity-50">
                            <span wire:loading.remove>Batalkan Keputusan</span>
                            <span wire:loading>Memproses...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Journey (Perjalanan Siswa) --}}
    @if($isJourneyModalOpen && !empty($journeyData))
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none" role="dialog" aria-modal="true">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[82vh] overflow-hidden flex flex-col pointer-events-auto" style="border:1px solid #E5E7EB; font-family:'Inter',sans-serif;">
                {{-- Header Modal --}}
                <div class="px-5 py-3 flex items-center justify-between shrink-0" style="background: linear-gradient(135deg, #1B5E4F 0%, #134338 100%);">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white" style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white tracking-tight" style="font-family:'Plus Jakarta Sans',sans-serif;">
                                Rekam Jejak Perjalanan Siswa
                            </h3>
                            <p class="text-xs text-white font-medium mt-0.5">
                                {{ $journeyData['siswa_nama'] }} <span class="text-white/70">•</span> NIS: {{ $journeyData['siswa_nis'] }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="closeJourneyModal" class="w-7 h-7 rounded-lg flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Content Body --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-3.5" style="background-color: #F9FAFB;">
                    {{-- Compact Summary Bar --}}
                    <div class="flex items-center justify-between px-3.5 py-2 rounded-xl bg-white text-xs shadow-xs" style="border: 1px solid #E5E7EB;">
                        <div class="flex items-center gap-1.5">
                            <span class="text-gray-400 text-[11px]">Total Durasi:</span>
                            <span class="font-bold text-emerald-800">{{ $journeyData['total_duration_txt'] ?? ($journeyData['total_duration'] . ' Bulan') }}</span>
                            <span class="text-gray-400 text-[11px]">({{ $journeyData['total_days'] ?? 0 }} hari)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-gray-400 text-[11px]">Jenjang:</span>
                            <span class="font-bold text-blue-700">{{ $journeyData['total_tingkat'] }} Tingkat (D → M → A)</span>
                        </div>
                    </div>

                    {{-- Timeline Cards --}}
                    <div class="space-y-3">
                        @foreach($journeyData['journey'] as $index => $step)
                            <div class="relative pl-6">
                                {{-- Timeline Line --}}
                                @if(!$loop->last)
                                    <div class="absolute left-2.5 top-6 w-0.5 bottom-0 bg-gray-200"></div>
                                @endif

                                {{-- Step Number Dot --}}
                                <div class="absolute left-0 top-2.5 w-4.5 h-4.5 rounded-full flex items-center justify-center text-[9px] font-bold text-white z-10 shadow-xs"
                                    style="background-color: #1B5E4F; border: 2px solid #FFFFFF;">
                                    {{ $index + 1 }}
                                </div>

                                {{-- Main Step Card --}}
                                <div class="bg-white rounded-xl p-3.5 shadow-xs transition-all hover:shadow-sm" style="border: 1px solid #E5E7EB;">
                                    {{-- Header Card --}}
                                    <div class="flex items-start justify-between gap-2 pb-2 mb-2.5" style="border-bottom: 1px solid #F1F5F9;">
                                        <div class="flex items-center gap-2">
                                            @php
                                                $bgBadge = $step['tingkat'] === 'Dasar' ? '#EFF6FF' : ($step['tingkat'] === 'Menengah' ? '#FFFBEB' : '#ECFDF5');
                                                $textBadge = $step['tingkat'] === 'Dasar' ? '#1D4ED8' : ($step['tingkat'] === 'Menengah' ? '#B45309' : '#047857');
                                                $borderBadge = $step['tingkat'] === 'Dasar' ? '#BFDBFE' : ($step['tingkat'] === 'Menengah' ? '#FDE68A' : '#A7F3D0');
                                            @endphp
                                            <span class="px-2 py-0.5 rounded text-[11px] font-bold"
                                                style="background-color: {{ $bgBadge }}; color: {{ $textBadge }}; border: 1px solid {{ $borderBadge }};">
                                                {{ $step['tingkat'] }}
                                            </span>
                                            <div>
                                                <span class="font-bold text-gray-900 text-xs" style="font-family:'Plus Jakarta Sans',sans-serif;">
                                                    {{ $step['kelompok_nama'] }}
                                                </span>
                                                <span class="text-[11px] text-gray-400 ml-1">
                                                    (Guru: <strong class="text-gray-600 font-medium">{{ $step['guru_nama'] }}</strong>)
                                                </span>
                                            </div>
                                        </div>

                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold shrink-0" style="background-color: #E8F5F1; color: #1B5E4F; border: 1px solid #C4E5DC;">
                                            ⏱️ {{ $step['durasi_text'] }}
                                        </span>
                                    </div>

                                    {{-- 4-Cell Information Grid (Compact) --}}
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div class="p-2 rounded bg-gray-50/80" style="border: 1px solid #F1F5F9;">
                                            <span class="text-[10px] text-gray-400 uppercase font-semibold block">🎯 Target</span>
                                            <span class="font-bold text-gray-900 text-[11px]">{{ $step['target_hafalan'] }}</span>
                                        </div>
                                        <div class="p-2 rounded bg-gray-50/80" style="border: 1px solid #F1F5F9;">
                                            <span class="text-[10px] text-gray-400 uppercase font-semibold block">🏆 Capaian</span>
                                            <span class="font-bold {{ $step['is_tuntas'] ? 'text-emerald-700' : 'text-amber-700' }} text-[11px] truncate block" title="{{ $step['capaian_hafalan'] }}">
                                                {{ $step['capaian_hafalan'] }}
                                            </span>
                                        </div>
                                        <div class="p-2 rounded bg-gray-50/80" style="border: 1px solid #F1F5F9;">
                                            <span class="text-[10px] text-gray-400 uppercase font-semibold block">📥 Masuk</span>
                                            <span class="font-semibold text-gray-800 text-[11px]">{{ $step['tanggal_masuk'] }}</span>
                                        </div>
                                        <div class="p-2 rounded bg-gray-50/80" style="border: 1px solid #F1F5F9;">
                                            <span class="text-[10px] text-gray-400 uppercase font-semibold block">📤 Keluar</span>
                                            <span class="font-semibold text-gray-800 text-[11px]">{{ $step['tanggal_keluar'] }}</span>
                                        </div>
                                    </div>

                                    {{-- Action Strip & Keterangan --}}
                                    <div class="mt-2.5 pt-2 flex items-center justify-between gap-2" style="border-top: 1px solid #F1F5F9;">
                                        <p class="text-[10px] text-gray-500 italic truncate max-w-xs" title="{{ $step['alasan'] }}">
                                            @if($step['alasan'])
                                                Ket: {{ $step['alasan'] }}
                                            @endif
                                        </p>

                                        @if(!empty($step['kelompok_id']))
                                            <a href="{{ route('admin.setoran.riwayat-hafalan', ['siswa' => $journeyData['siswa_id'], 'kelompok_id' => $step['kelompok_id']]) }}"
                                               target="_blank"
                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold transition-all hover:opacity-90 shadow-xs shrink-0"
                                               style="background-color: #1B5E4F; color: #FFFFFF;"
                                               title="Buka detail riwayat setoran hafalan siswa di {{ $step['kelompok_nama'] }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                                <span>Track Record Hafalan</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Footer Modal --}}
                <div class="px-5 py-2.5 flex justify-end shrink-0 bg-white" style="border-top: 1px solid #E5E7EB;">
                    <button wire:click="closeJourneyModal" type="button" class="btn-modal-cancel text-xs py-1.5 px-4">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
