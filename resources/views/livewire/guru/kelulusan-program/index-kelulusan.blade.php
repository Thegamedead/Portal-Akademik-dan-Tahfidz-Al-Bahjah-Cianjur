<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- PAGE HEADER --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;">
        <h1 class="text-2xl font-bold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1A2422;">
            Kelulusan Program Tahfidz
        </h1>
        <p class="mt-2 text-sm" style="color:#6B7280;font-family:'Inter',sans-serif;">
            Data kelulusan program tahfidz siswa dari kelompok yang Anda bimbing
        </p>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- SEARCH & FILTER ROW --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}

    <div class="rounded-2xl bg-white shadow-sm p-6" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
        {{-- Single Row: Flex Layout --}}
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">

            {{-- LEFT: Info Label --}}
            <div class="flex items-center">
                <span class="text-sm text-gray-500">
                    Total <strong style="color:#1A2422;">{{ $kelulusans->total() }}</strong> usulan
                </span>
            </div>

            {{-- RIGHT: Search Field + Filter Dropdown Button --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 lg:min-w-100">
                {{-- Search Input --}}
                <div class="flex-1">
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Cari nama siswa atau NIS..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg text-sm transition-all focus:outline-none focus:ring-2"
                            style="border:1.5px solid #D1D5DB;color:#1A2422;"
                            onfocus="this.style.borderColor='#1B5E4F';this.style.boxShadow='0 0 0 3px rgba(27,94,79,0.1)';"
                            onblur="this.style.borderColor='#D1D5DB';this.style.boxShadow='none';"
                        />
                        <svg class="h-5 w-5 absolute left-3 top-1/2 -translate-y-1/2" style="color:#6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
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
                        @if($filterStatus)
                            <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold rounded-full" style="background-color:#1B5E4F;color:#FFFFFF;">
                                1
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
                         class="absolute z-50 right-0 mt-2 w-72 rounded-xl shadow-xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                         style="display: none; border: 1px solid #E5E7EB;">

                        <div class="p-4 space-y-3">
                            {{-- Filter Header --}}
                            <div class="flex items-center justify-between pb-2" style="border-bottom: 1px solid #E5E7EB;">
                                <h3 class="text-sm font-semibold" style="color:#1A2422;font-family:'Plus Jakarta Sans',sans-serif;">
                                    Filter Kelulusan
                                </h3>
                                @if($filterStatus)
                                    <button
                                        wire:click="resetFilters"
                                        type="button"
                                        class="text-xs font-medium text-gray-500 hover:text-[#1B5E4F] transition-colors"
                                    >
                                        Reset
                                    </button>
                                @endif
                            </div>

                            {{-- Filter Status --}}
                            <div>
                                <label class="block text-xs font-medium mb-1.5" style="color:#6B7280;">
                                    Status Kelulusan
                                </label>
                                <select
                                    wire:model.live="filterStatus"
                                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none focus:ring-2 transition-all"
                                    style="border:1.5px solid #D1D5DB;color:#1A2422;"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="Diajukan">Diajukan</option>
                                    <option value="Disetujui">Disetujui</option>
                                    <option value="Ditolak">Ditolak</option>
                                </select>
                                         <thead>
                    <tr style="background-color:#1B5E4F;">
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Nama Siswa</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">NIS</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Kelas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Kelompok Terakhir</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Tanggal Tuntas</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold" style="color:white;">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color:#E5E7EB;">
                    @forelse($kelulusans as $kelulusan)
                        <tr class="hover:bg-gray-50 transition-colors">
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
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium" style="color:#1A2422;">{{ $kelulusan->kelompok->nama }}</span>
                                    <span class="text-xs" style="color:#6B7280;">
                                        {{ $kelulusan->kelompok->tingkat }} - {{ $kelulusan->kelompok->jenis }}
                                    </span>
                                </div>
                            </td>

                            {{-- Tanggal Tuntas --}}
                            <td class="px-6 py-4 text-sm font-medium" style="color:#1A2422;">
                                {{ $kelulusan->tanggal_tuntas ? $kelulusan->tanggal_tuntas->format('d M Y') : '-' }}
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'Diajukan' => ['bg' => '#E0F2FE', 'text' => '#0C4A6E'],
                                        'Disetujui' => ['bg' => '#E8F5F1', 'text' => '#1B5E4F'],
                                        'Ditolak' => ['bg' => '#FDEAEA', 'text' => '#9B3A3A'],
                                    ];
                                    $colors = $statusColors[$kelulusan->status_kelulusan->value] ?? ['bg' => '#F3F4F6', 'text' => '#374151'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                      style="background-color:{{ $colors['bg'] }};color:{{ $colors['text'] }};">
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
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4" style="color:#D1D5DB;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                    <p class="text-sm font-medium" style="color:#6B7280;">
                                        @if($search || $filterStatus)
                                            Tidak ada data kelulusan yang sesuai dengan filter
                                        @else
                                            Belum ada siswa yang lulus dari kelompok yang Anda bimbing
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse                                @if($search || $filterStatus)
                                            Tidak ada data kelulusan yang sesuai dengan filter
                                        @else
                                            Belum ada siswa yang lulus dari kelompok yang Anda bimbing
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
                                            <a href="{{ route('guru.setoran.riwayat-hafalan', ['siswa' => $journeyData['siswa_id'], 'kelompok_id' => $step['kelompok_id']]) }}"
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
