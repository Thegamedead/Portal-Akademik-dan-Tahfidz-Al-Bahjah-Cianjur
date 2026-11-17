@props([
    'type' => 'default', // akademik, tahfidz, kehadiran, custom
    'status' => null,
    'size' => 'md', // sm, md, lg
    'customColors' => null, // ['bg' => '#hex', 'text' => '#hex']
])

@php
    // Size configurations
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-xs',
        'lg' => 'px-3 py-1.5 text-sm',
    ];

    // Status color configurations
    $statusColors = [
        // Status Akademik
        'Aktif' => ['bg' => '#D1FAE5', 'text' => '#065F46'],
        'Cuti' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
        'Lulus' => ['bg' => '#DBEAFE', 'text' => '#1E40AF'],
        'Keluar' => ['bg' => '#FEE2E2', 'text' => '#991B1B'],
        'Non-Aktif' => ['bg' => '#F3F4F6', 'text' => '#374151'],

        // Status Tahfidz
        'Belum Mulai' => ['bg' => '#F3F4F6', 'text' => '#6B7280'],
        'Sedang Berjalan' => ['bg' => '#E0E7FF', 'text' => '#3730A3'],
        'Lulus Program Tahfidz' => ['bg' => '#D1FAE5', 'text' => '#065F46'],

        // Status Kehadiran
        'Hadir' => ['bg' => '#D1FAE5', 'text' => '#065F46'],
        'Izin' => ['bg' => '#DBEAFE', 'text' => '#1E40AF'],
        'Sakit' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
        'Alfa' => ['bg' => '#FEE2E2', 'text' => '#991B1B'],

        // Status Penempatan
        'Belum Ditempatkan' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
        'Sudah Ditempatkan' => ['bg' => '#D1FAE5', 'text' => '#065F46'],

        // Status Usulan
        'Diajukan' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
        'Disetujui' => ['bg' => '#D1FAE5', 'text' => '#065F46'],
        'Ditolak' => ['bg' => '#FEE2E2', 'text' => '#991B1B'],

        // Keputusan Setoran
        'Lanjut' => ['bg' => '#D1FAE5', 'text' => '#065F46'],
        'Ulang' => ['bg' => '#FEF3C7', 'text' => '#92400E'],

        // Status Tes Penempatan
        'Belum Tes' => ['bg' => '#F3F4F6', 'text' => '#6B7280'],
        'Sudah Tes' => ['bg' => '#D1FAE5', 'text' => '#065F46'],
        'Perlu Review' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
    ];

    $colors = $customColors ?? ($statusColors[$status] ?? ['bg' => '#F3F4F6', 'text' => '#374151']);
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<span
    {{ $attributes->merge([
        'class' => "inline-flex items-center font-medium rounded-full whitespace-nowrap {$sizeClass}"
    ]) }}
    style="background-color: {{ $colors['bg'] }}; color: {{ $colors['text'] }};"
>
    {{ $slot->isEmpty() ? $status : $slot }}
</span>
