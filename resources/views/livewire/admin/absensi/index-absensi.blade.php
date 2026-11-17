<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="rounded-2xl bg-white shadow-sm p-6 border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold font-jakarta text-[#1A2422]">
                    Absensi dan Setoran Hafalan
                </h1>
                <p class="mt-1 text-sm text-gray-500 font-inter">
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                </p>
            </div>

            {{-- GLOBAL SUMMARY CARDS --}}
            <div class="flex flex-wrap gap-3">
                <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-[#E8F5F1] border border-[#1B5E4F]/15">
                    <span class="text-xl font-bold text-[#1B5E4F]">{{ $globalStats['sudah_input'] }}</span>
                    <span class="text-xs text-[#1B5E4F] font-medium leading-tight">Kelompok<br>Sudah Input</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-50 border border-amber-200">
                    <span class="text-xl font-bold text-amber-700">{{ $globalStats['belum_input'] }}</span>
                    <span class="text-xs text-amber-700 font-medium leading-tight">Kelompok<br>Belum Input</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 border border-blue-200">
                    <span class="text-xl font-bold text-blue-700">{{ $globalStats['total_setoran_hari_ini'] }}</span>
                    <span class="text-xs text-blue-700 font-medium leading-tight">Setoran<br>Hari Ini</span>
                </div>
            </div>
        </div>
    </div>

    {{-- MENU PUSAT KOMANDO --}}
    <div class="rounded-2xl bg-white shadow-sm px-6 py-4 border border-gray-200 font-inter">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Cara Penggunaan</p>
                <p class="text-sm text-gray-500 mt-0.5">
                    Klik <strong class="text-[#1B5E4F]">Input</strong> untuk absensi harian · Klik <strong class="text-[#1B5E4F]">Dashboard</strong> untuk melihat seluruh data kelompok (absensi, setoran, progres)
                </p>
            </div>

        </div>
    </div>

    {{-- KELOMPOK CARDS --}}
    @if($kelompoks->isEmpty())
        <div class="rounded-2xl bg-white shadow-sm p-12 text-center border border-gray-200">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p class="text-sm font-medium text-gray-500">Belum ada kelompok terdaftar</p>
            <p class="mt-1 text-xs text-gray-400">Buat kelompok baru di menu Master Data</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden font-inter">
            <table class="min-w-full">
                <thead>
                    <tr style="background-color:#1B5E4F;">
                        <th class="px-5 py-3 text-left text-[11px] font-semibold text-white">Kelompok</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold text-white">Guru</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-white">Siswa</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-[#86efac]" title="Hadir hari ini">Hadir</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-[#fcd34d]" title="Izin+Sakit hari ini">Izin/Sakit</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-[#fca5a5]" title="Alfa hari ini">Alfa</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-[#93c5fd]" title="Setoran hari ini">Setoran</th>
                        <th class="px-4 py-3 text-center text-[11px] font-semibold text-white" title="Sesi bulan ini">Sesi/Bln</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-white">Status</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-white">Input</th>
                        <th class="px-5 py-3 text-center text-[11px] font-semibold text-white">Dashboard</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($kelompoks as $kelompok)
                        @php
                            $kid        = $kelompok->id;
                            $sudahDiisi = $absensiStatus[$kid] ?? false;
                            $stats      = $statsPerKelompok[$kid] ?? [];
                            $totalSiswa = $this->getTotalSiswaAktif($kelompok);
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <div class="font-semibold text-sm text-[#1A2422]">{{ $kelompok->nama }}</div>
                                <div class="flex items-center gap-1 mt-0.5">
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#E8F5F1] text-[#1B5E4F] font-medium">{{ $kelompok->tingkat }}</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 font-medium">{{ $kelompok->jenis }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 max-w-[140px] truncate">{{ $kelompok->guruPembimbing->nama ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-gray-700">{{ $totalSiswa }}</td>
                            <td class="px-4 py-3 text-center text-sm font-bold text-[#1B5E4F]">{{ $stats['hadir'] ?? 0 }}</td>
                            <td class="px-4 py-3 text-center text-sm font-bold text-amber-600">{{ ($stats['izin'] ?? 0) + ($stats['sakit'] ?? 0) }}</td>
                            <td class="px-4 py-3 text-center text-sm font-bold text-red-500">{{ $stats['alfa'] ?? 0 }}</td>
                            <td class="px-4 py-3 text-center text-sm font-bold text-blue-600">{{ $stats['setoran_hari_ini'] ?? 0 }}</td>
                            <td class="px-4 py-3 text-center text-xs text-gray-500">{{ $stats['sesi_bulan_ini'] ?? 0 }}x</td>
                            <td class="px-5 py-3 text-center">
                                @if($sudahDiisi)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#E8F5F1] text-[#1B5E4F] border border-[#1B5E4F]/20">
                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Sudah
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Belum</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <button wire:click="$dispatch('openInputAbsensi', { kelompokId: {{ $kelompok->id }} })"
                                   class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg text-white bg-[#1B5E4F] hover:bg-[#155441] transition-colors shadow-sm whitespace-nowrap">
                                    {{ $sudahDiisi ? 'Edit' : 'Input' }}
                                </button>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <a href="{{ route('admin.kelompok.dashboard', ['kelompok' => $kelompok->id]) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg text-[#1B5E4F] bg-[#E8F5F1] hover:bg-[#d1ede6] border border-[#1B5E4F]/20 transition-colors whitespace-nowrap">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    Dashboard
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Modal Input Absensi --}}
    <livewire:admin.absensi.input-absensi />
</div>
