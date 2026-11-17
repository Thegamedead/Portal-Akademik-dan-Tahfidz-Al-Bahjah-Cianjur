{{--
    Component: components/siswa/informasi-keluar.blade.php
    Purpose: Display exit information card for students who have jenis_keluar value

    Props:
    - $siswa: Siswa model instance

    Usage:
    <x-siswa.informasi-keluar :siswa="$siswa" />

    Features:
    - Only displays when jenis_keluar is not null
    - Shows Jenis Keluar, Tanggal Keluar, Status Akademik badge
    - Conditionally shows Tujuan Sekolah (when jenis_keluar = 'Pindah Sekolah')
    - Conditionally shows Keterangan (when not empty)
    - Displays Diproses Oleh (admin name + timestamp)
    - Responsive grid layout (1 column mobile, 2 columns desktop)

    Requirements: 9.1, 9.2, 9.3
--}}

@props(['siswa'])

@if($siswa->jenis_keluar)
<div class="rounded-2xl bg-white shadow-sm p-6 space-y-4" style="border:1px solid #E5E7EB;font-family:'Inter',sans-serif;">
    <h3 class="text-base font-semibold" style="font-family:'Plus Jakarta Sans',sans-serif;color:#1B5E4F;">
        Informasi Keluar Siswa
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Jenis Keluar --}}
        <div>
            <p class="text-xs font-medium mb-1" style="color:#6B7280;">Jenis Keluar</p>
            <p class="text-sm" style="color:#1A2422;">
                @php
                $jenisLabel = \App\Enums\JenisKeluar::options()[$siswa->jenis_keluar] ?? $siswa->jenis_keluar;
                @endphp
                {{ $jenisLabel }}
            </p>
        </div>

        {{-- Tanggal Keluar --}}
        <div>
            <p class="text-xs font-medium mb-1" style="color:#6B7280;">Tanggal Keluar</p>
            <p class="text-sm" style="color:#1A2422;">
                {{ $siswa->tanggal_keluar?->tgl() ?? '-' }}
            </p>
        </div>

        {{-- Status Akademik --}}
        <div>
            <p class="text-xs font-medium mb-1" style="color:#6B7280;">Status Akademik</p>
            <div class="mt-1">
                @php
                $badgeConfig = match($siswa->status_akademik) {
                    'Aktif' => ['text' => 'Aktif', 'bg' => '#1B5E4F', 'color' => '#FFFFFF'],
                    'Lulus SMP' => ['text' => 'Lulus SMP', 'bg' => '#1B5E4F', 'color' => '#FFFFFF'],
                    'Pindah Sekolah' => ['text' => 'Pindah Sekolah', 'bg' => '#5B7A8C', 'color' => '#FFFFFF'],
                    'Keluar' => ['text' => 'Keluar', 'bg' => '#9B3A3A', 'color' => '#FFFFFF'],
                    default => ['text' => $siswa->status_akademik, 'bg' => '#F4F5F4', 'color' => '#1A2422'],
                };
                @endphp
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                    style="background-color:{{ $badgeConfig['bg'] }};color:{{ $badgeConfig['color'] }};"
                >
                    {{ $badgeConfig['text'] }}
                </span>
            </div>
        </div>

        {{-- Tujuan Sekolah (conditional: only when jenis_keluar = 'Pindah Sekolah') --}}
        @if($siswa->jenis_keluar === 'Pindah Sekolah' && $siswa->tujuan_sekolah)
        <div>
            <p class="text-xs font-medium mb-1" style="color:#6B7280;">Tujuan Sekolah</p>
            <p class="text-sm" style="color:#1A2422;">{{ $siswa->tujuan_sekolah }}</p>
        </div>
        @endif

        {{-- Keterangan (conditional: only when not empty) --}}
        @if($siswa->keterangan_keluar)
        <div class="md:col-span-2">
            <p class="text-xs font-medium mb-1" style="color:#6B7280;">Keterangan</p>
            <p class="text-sm leading-relaxed" style="color:#1A2422;">{{ $siswa->keterangan_keluar }}</p>
        </div>
        @endif

        {{-- Diproses Oleh (admin name + timestamp) --}}
        <div class="md:col-span-2">
            <p class="text-xs font-medium mb-1" style="color:#6B7280;">Diproses Oleh</p>
            <p class="text-sm" style="color:#1A2422;">
                {{ $siswa->diprosesOleh?->nama ?? 'Admin' }}
                @if($siswa->updated_at)
                    <span style="color:#6B7280;">
                        • {{ $siswa->updated_at->tglJam() }}
                    </span>
                @endif
            </p>
        </div>
    </div>
</div>
@endif
