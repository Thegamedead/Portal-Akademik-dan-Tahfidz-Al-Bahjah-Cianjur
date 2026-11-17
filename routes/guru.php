<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guru Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.guru', 'session.timeout', 'force.change.password'])->group(function () {


    // Dashboard
    Route::get('/dashboard', \App\Livewire\Guru\DashboardGuru::class)
        ->name('guru.dashboard');

    // Profil
    Route::get('/profil', \App\Livewire\Guru\ProfilGuru::class)
        ->name('guru.profil');

    // TAHFIDZ & QURAN
    // Referensi Juz
    Route::get('/referensi-juz', \App\Livewire\Shared\ReferensiJuz::class)
        ->name('guru.referensi-juz');

    // Tes Penempatan (with feature toggle)
    Route::get('/tes-penempatan', \App\Livewire\Guru\TesPenempatan\IndexTesPenempatan::class)
        ->name('guru.tes-penempatan.index');
    Route::get('/tes-penempatan/{id}/hasil', \App\Livewire\Guru\TesPenempatan\HasilTes::class)
        ->name('guru.tes-penempatan.hasil');

    // Kelompok Tahfidz
    Route::get('/kelompok-tahfidz', \App\Livewire\Guru\Kelompok\IndexKelompokTahfidz::class)
        ->name('guru.kelompok-tahfidz.index');



    // Kelompok Tahfidz - Setoran (Dashboard with calendar matrix)
    Route::get('/kelompok-tahfidz/{kelompok}/setoran', \App\Livewire\Guru\Kelompok\DashboardKelompok::class)
        ->name('guru.kelompok-tahfidz.setoran');

    // Riwayat Hafalan Siswa
    Route::get('/riwayat-hafalan/{siswa}', \App\Livewire\Guru\Setoran\RiwayatHafalanSiswa::class)
        ->name('guru.setoran.riwayat-hafalan');

    // Riwayat Lulusan / Naik Tingkat (Global untuk Guru)
    Route::get('/riwayat-lulusan', \App\Livewire\Guru\RiwayatLulusan\IndexRiwayatLulusan::class)
        ->name('guru.riwayat-lulusan.index');

    // Usulan Kenaikan Tingkat
    Route::get('/usulan-kenaikan', \App\Livewire\Guru\UsulanKenaikan\IndexUsulan::class)
        ->name('guru.usulan-kenaikan.index');

    // Kelulusan Program
    Route::get('/kelulusan-program', \App\Livewire\Guru\KelulusanProgram\IndexKelulusan::class)
        ->name('guru.kelulusan-program.index');

});
