<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Target Minimal Hafalan untuk Lulus Program Tahfidz
    |--------------------------------------------------------------------------
    |
    | Menentukan jumlah juz minimal yang harus dihafal siswa untuk dinyatakan
    | "Lulus Program Tahfidz". Target ini bersifat independen dari kelulusan
    | akademik dan dapat disesuaikan dengan kebijakan sekolah.
    |
    | Default: 5 juz
    | Range: 1-30 juz
    |
    */

    'target_juz_minimal' => env('TAHFIDZ_TARGET_JUZ_MINIMAL', 5),

    /*
    |--------------------------------------------------------------------------
    | Estimasi Jumlah Ayat per Juz
    |--------------------------------------------------------------------------
    |
    | Rata-rata jumlah ayat dalam satu juz Al-Quran. Digunakan untuk
    | konversi dan kalkulasi progress.
    |
    | Note: Jumlah ayat per juz tidak sama persis, ini adalah estimasi
    | untuk keperluan tracking progress.
    |
    */

    'ayat_per_juz' => 200,

    /*
    |--------------------------------------------------------------------------
    | Total Ayat dalam Al-Quran
    |--------------------------------------------------------------------------
    |
    | Total ayat dalam Al-Quran (30 juz).
    | Digunakan untuk validasi dan batas maksimal.
    |
    */

    'total_ayat_quran' => 6236,

    /*
    |--------------------------------------------------------------------------
    | Kategori Level Hafalan
    |--------------------------------------------------------------------------
    |
    | Kategori prestasi berdasarkan jumlah juz yang telah dihafal.
    | Digunakan untuk badge, sertifikat, dan apresiasi.
    |
    */

    'kategori_hafalan' => [
        'pemula' => [
            'label' => 'Pemula',
            'min_juz' => 0,
            'max_juz' => 2,
            'color' => 'gray',
        ],
        'dasar' => [
            'label' => 'Dasar',
            'min_juz' => 3,
            'max_juz' => 4,
            'color' => 'blue',
        ],
        'lulus' => [
            'label' => 'Lulus Program',
            'min_juz' => 5,
            'max_juz' => 9,
            'color' => 'green',
        ],
        'menengah' => [
            'label' => 'Menengah',
            'min_juz' => 10,
            'max_juz' => 14,
            'color' => 'yellow',
        ],
        'lanjutan' => [
            'label' => 'Lanjutan',
            'min_juz' => 15,
            'max_juz' => 19,
            'color' => 'orange',
        ],
        'tinggi' => [
            'label' => 'Tingkat Tinggi',
            'min_juz' => 20,
            'max_juz' => 29,
            'color' => 'purple',
        ],
        'hafidz' => [
            'label' => 'Hafidz 30 Juz',
            'min_juz' => 30,
            'max_juz' => 30,
            'color' => 'gold',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Batas Minimal Juz untuk Kenaikan Tingkat Kelompok
    |--------------------------------------------------------------------------
    |
    | Menentukan syarat minimal juz yang harus dihafal siswa untuk naik tingkat.
    | Key = Tingkat Tujuan, Value = Array [syarat minimal juz]
    |
    | Format: ['Tingkat' => [min_juz]]
    | - Siswa dari Dasar ke Menengah: minimal harus hafal juz sesuai 'Menengah'
    | - Siswa dari Menengah ke Atas: minimal harus hafal juz sesuai 'Atas'
    |
    */

    'batas_tingkat' => [
        'Dasar' => [0],      // Tidak ada syarat untuk masuk Dasar
        'Menengah' => [3],   // Minimal 3 juz untuk naik ke Menengah
        'Atas' => [5],       // Minimal 5 juz untuk naik ke Atas
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto Update Status Tahfidz
    |--------------------------------------------------------------------------
    |
    | Jika true, status tahfidz siswa akan otomatis berubah menjadi "Lulus"
    | ketika jumlah hafalan mencapai target minimal.
    |
    | Jika false, admin harus manual mengubah status.
    |
    */

    'auto_update_status' => true,

    /*
    |--------------------------------------------------------------------------
    | Notifikasi Kelulusan Tahfidz
    |--------------------------------------------------------------------------
    |
    | Kirim notifikasi otomatis ketika siswa mencapai target minimal
    | (pertama kali lulus program tahfidz).
    |
    */

    'notifikasi_lulus' => true,

];
