{{-- Dashboard Guru - Modern & Focused --}}
<div class="space-y-5">

    {{-- ═══════════════════════════════════════════════════════════════════════
        HERO SECTION - Welcome Guru
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden bg-linear-to-br from-blue-50 via-white to-emerald-50 rounded-xl shadow-sm border border-gray-100">
        {{-- Decorative Background --}}
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-40 h-40 bg-blue-100 rounded-full opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-emerald-100 rounded-full opacity-20 blur-2xl"></div>

        <div class="relative p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-1">Selamat Datang, Ustadz/ah! 👨‍🏫</h1>
                    <p class="text-sm text-gray-600">{{ auth('guru')->user()->nama }} - Pembimbing Tahfidz</p>
                </div>
                <div class="hidden sm:flex items-center gap-2 text-xs text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                </div>
            </div>

            {{-- Main KPI Cards untuk Guru --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Kelompok Dibimbing --}}
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow border border-gray-100">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2 rounded-lg bg-blue-50">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded">Kelas</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $dashboardData['kelompok_dibimbing'] }}</h3>
                    <p class="text-xs text-gray-600">Kelompok Bimbingan</p>
                </div>

                {{-- Total Siswa --}}
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow border border-gray-100">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2 rounded-lg bg-emerald-50">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Aktif</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $dashboardData['total_siswa_bimbingan'] }}</h3>
                    <p class="text-xs text-gray-600">Total Siswa</p>
                </div>

                {{-- Setoran Bulan Ini --}}
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow border border-gray-100">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2 rounded-lg bg-amber-50">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded">Bulan Ini</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $dashboardData['setoran_bulan_ini'] }}</h3>
                    <p class="text-xs text-gray-600">Total Setoran</p>
                </div>

                {{-- Sesi Mengajar --}}
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow border border-gray-100">
                    <div class="flex items-start justify-between mb-3">
                        <div class="p-2 rounded-lg bg-cyan-50">
                            <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-cyan-600 bg-cyan-50 px-2 py-0.5 rounded">Bulan Ini</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">{{ $dashboardData['absensi_bulan_ini'] }}</h3>
                    <p class="text-xs text-gray-600">Sesi Mengajar</p>
                </div>
            </div>

            {{-- Quick Actions untuk Guru --}}
            <div class="mt-5 flex flex-wrap gap-2">
                <a href="{{ route('guru.kelompok-tahfidz.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Kelola Kelompok
                </a>
                <a href="{{ route('guru.riwayat-lulusan.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Riwayat Lulusan
                </a>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
        KELOMPOK BIMBINGAN & DISTRIBUSI SISWA
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Daftar Kelompok Bimbingan --}}
        <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-base text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Kelompok Bimbingan
                </h3>
                <span class="text-xs text-gray-500">{{ $dashboardData['kelompok_dibimbing'] }} Kelas</span>
            </div>

            @if(count($dashboardData['kelompok_list']) > 0)
                <div class="space-y-3">
                    @foreach($dashboardData['kelompok_list'] as $kelompok)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    @if($kelompok['tingkat'] === 'Dasar')
                                        <span class="w-6 h-6 rounded flex items-center justify-center bg-emerald-600 text-white text-xs font-bold">D</span>
                                    @elseif($kelompok['tingkat'] === 'Menengah')
                                        <span class="w-6 h-6 rounded flex items-center justify-center bg-cyan-600 text-white text-xs font-bold">M</span>
                                    @else
                                        <span class="w-6 h-6 rounded flex items-center justify-center bg-amber-600 text-white text-xs font-bold">A</span>
                                    @endif
                                    <h4 class="text-sm font-medium text-gray-900">{{ $kelompok['nama'] }}</h4>
                                </div>
                                <p class="text-xs text-gray-500">Tingkat {{ $kelompok['tingkat'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900">{{ $kelompok['jumlah_siswa'] }}</p>
                                <p class="text-xs text-gray-500">dari {{ $kelompok['kapasitas'] }} siswa</p>
                                @php
                                    $percentage = $kelompok['kapasitas'] > 0 ? round(($kelompok['jumlah_siswa'] / $kelompok['kapasitas']) * 100) : 0;
                                @endphp
                                <div class="w-16 bg-gray-200 rounded-full h-1.5 mt-1">
                                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-sm">Belum ada kelompok yang dibimbing</p>
                </div>
            @endif
        </div>

        {{-- Distribusi Siswa per Tingkat --}}
        <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-base text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Distribusi Siswa
                </h3>
                <span class="text-xs text-gray-500">{{ array_sum($dashboardData['siswa_per_tingkat']) }} Siswa</span>
            </div>

            @php
                $totalSiswa = array_sum($dashboardData['siswa_per_tingkat']);
                $persenDasar = $totalSiswa > 0 ? round(($dashboardData['siswa_per_tingkat']['Dasar'] / $totalSiswa) * 100, 1) : 0;
                $persenMenengah = $totalSiswa > 0 ? round(($dashboardData['siswa_per_tingkat']['Menengah'] / $totalSiswa) * 100, 1) : 0;
                $persenAtas = $totalSiswa > 0 ? round(($dashboardData['siswa_per_tingkat']['Atas'] / $totalSiswa) * 100, 1) : 0;
            @endphp

            <div class="space-y-4">
                {{-- Dasar --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-600 text-white text-xs font-bold">D</span>
                            <span class="text-sm font-medium text-gray-700">Tingkat Dasar</span>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-gray-900">{{ $dashboardData['siswa_per_tingkat']['Dasar'] }}</span>
                            <span class="text-xs text-gray-500 ml-1">({{ $persenDasar }}%)</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-emerald-600 h-2 rounded-full transition-all duration-500" style="width: {{ $persenDasar }}%"></div>
                    </div>
                </div>

                {{-- Menengah --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center bg-cyan-600 text-white text-xs font-bold">M</span>
                            <span class="text-sm font-medium text-gray-700">Tingkat Menengah</span>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-gray-900">{{ $dashboardData['siswa_per_tingkat']['Menengah'] }}</span>
                            <span class="text-xs text-gray-500 ml-1">({{ $persenMenengah }}%)</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-cyan-600 h-2 rounded-full transition-all duration-500" style="width: {{ $persenMenengah }}%"></div>
                    </div>
                </div>

                {{-- Atas --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-600 text-white text-xs font-bold">A</span>
                            <span class="text-sm font-medium text-gray-700">Tingkat Atas</span>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-gray-900">{{ $dashboardData['siswa_per_tingkat']['Atas'] }}</span>
                            <span class="text-xs text-gray-500 ml-1">({{ $persenAtas }}%)</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-amber-600 h-2 rounded-full transition-all duration-500" style="width: {{ $persenAtas }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
        ROADMAP TAHFIDZ
    ═══════════════════════════════════════════════════════════════════════ --}}
    <x-roadmap-tahfidz />

    {{-- ═══════════════════════════════════════════════════════════════════════
        AKTIVITAS TERBARU SETORAN
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-base text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Aktivitas Setoran Terbaru
            </h3>
            <span class="text-xs text-gray-500">10 Terakhir</span>
        </div>

        @if(count($dashboardData['recent_activity']) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="py-2 px-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                            <th class="py-2 px-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Siswa</th>
                            <th class="py-2 px-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Kelompok</th>
                            <th class="py-2 px-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Setoran</th>
                            <th class="py-2 px-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($dashboardData['recent_activity'] as $activity)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-3 text-xs text-gray-900">
                                {{ \Carbon\Carbon::parse($activity['tanggal'])->isoFormat('DD MMM') }}
                            </td>
                            <td class="py-3 px-3 text-xs font-medium text-gray-900">
                                {{ Str::limit($activity['siswa_nama'], 15) }}
                            </td>
                            <td class="py-3 px-3 text-xs text-gray-600">
                                {{ $activity['kelompok_nama'] }}
                            </td>
                            <td class="py-3 px-3">
                                <div class="text-xs">
                                    <p class="font-medium text-gray-900">{{ $activity['jenis_setoran'] }}</p>
                                    <p class="text-gray-500">Juz {{ $activity['juz'] ?? '-' }} - {{ $activity['surah'] ?? '-' }}</p>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                @if($activity['keputusan'] === 'Lanjut')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Lanjut
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Ulang
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-sm">Belum ada aktivitas setoran</p>
            </div>
        @endif
    </div>
</div>
