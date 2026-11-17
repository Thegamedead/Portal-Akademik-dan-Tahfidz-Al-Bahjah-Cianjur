<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Siswa Routes
|--------------------------------------------------------------------------
|
| Semua route siswa dengan prefix /siswa (diterapkan di bootstrap/app.php)
| Protected by: middleware 'auth.siswa', 'session.timeout'
|
*/

Route::middleware(['auth.siswa', 'session.timeout', 'force.change.password'])->group(function () {

    // ═══════════════════════════════════════════════════════════════════════════
    // DASHBOARD
    // ═══════════════════════════════════════════════════════════════════════════

    Route::get('/dashboard', \App\Livewire\Siswa\DashboardSiswa::class)
        ->name('siswa.dashboard');

    // ═══════════════════════════════════════════════════════════════════════════
    // PROGRES HAFALAN
    // ═══════════════════════════════════════════════════════════════════════════

    Route::get('/progres', \App\Livewire\Siswa\Progres\DashboardProgresSiswa::class)
        ->name('siswa.progres.dashboard');


    // ═══════════════════════════════════════════════════════════════════════════
    // TAHFIDZ & QURAN - REFERENSI
    // ═══════════════════════════════════════════════════════════════════════════

    Route::get('/referensi-juz', \App\Livewire\Shared\ReferensiJuz::class)
        ->name('siswa.referensi-juz');

    // ═══════════════════════════════════════════════════════════════════════════
    // KEHADIRAN (Read-Only Riwayat Absensi)
    // ═══════════════════════════════════════════════════════════════════════════

    Route::get('/kehadiran', \App\Livewire\Shared\RiwayatAbsensi::class)
        ->name('siswa.kehadiran')
        ->defaults('role', 'siswa');

    // ═══════════════════════════════════════════════════════════════════════════
    // HASIL HAFALAN (Read-Only Riwayat Setoran)
    // ═══════════════════════════════════════════════════════════════════════════

    Route::get('/hasil-hafalan', \App\Livewire\Siswa\HasilHafalanSiswa::class)
        ->name('siswa.hasil-hafalan');

    // ═══════════════════════════════════════════════════════════════════════════
    // PROFIL
    // ═══════════════════════════════════════════════════════════════════════════

    Route::get('/profil', \App\Livewire\Siswa\ProfilSiswa::class)
        ->name('siswa.profil');

});
