<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Session Timeout per Guard (dalam menit)
    |--------------------------------------------------------------------------
    | Admin tidak memiliki timeout otomatis.
    | Guru dan Siswa otomatis logout setelah tidak ada aktivitas.
    */
    'guru' => env('SESSION_TIMEOUT_GURU', 120),   // 2 jam default
    'siswa' => env('SESSION_TIMEOUT_SISWA', 60),  // 1 jam default
];
