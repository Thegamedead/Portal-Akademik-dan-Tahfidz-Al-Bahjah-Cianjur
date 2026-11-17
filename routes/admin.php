<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Semua route admin dengan prefix /admin (diterapkan di bootstrap/app.php)
| Protected by: middleware 'auth.admin'
|
*/

Route::middleware(['auth.admin'])->group(function () {

    // ═══════════════════════════════════════════════════════════════════════════
    // DASHBOARD
    // ═══════════════════════════════════════════════════════════════════════════

    Route::get('/dashboard', \App\Livewire\Admin\DashboardAdmin::class)
        ->name('admin.dashboard');

    // ═══════════════════════════════════════════════════════════════════════════
    // MANAJEMEN AKUN
    // ═══════════════════════════════════════════════════════════════════════════

    // Akun Guru & Siswa
    Route::get('/manajemen-akun', \App\Livewire\Admin\ManajemenAkun::class)
        ->name('admin.manajemen-akun');

    // Akun Admin
    Route::get('/manajemen-akun/admin', \App\Livewire\Admin\ManajemenAkunAdmin::class)
        ->name('admin.manajemen-akun.admin');

    // Cetak Kartu Login
    Route::get('/manajemen-akun/cetak-kartu', [\App\Http\Controllers\Admin\CetakKartuLoginController::class, 'cetak'])
        ->name('admin.kartu-login.pdf');

    // ═══════════════════════════════════════════════════════════════════════════
    // MASTER DATA
    // ═══════════════════════════════════════════════════════════════════════════

    Route::prefix('master-data')->name('admin.master-data.')->group(function () {

        // Profil Sekolah
        Route::get('/profil-sekolah', \App\Livewire\Admin\MasterData\Sekolah\ProfilSekolah::class)
            ->name('profil-sekolah');

        // Kelas Akademik
        Route::prefix('kelas')->name('kelas.')->group(function () {
            Route::get('/', \App\Livewire\Admin\MasterData\Kelas\IndexKelas::class)->name('index');
            Route::get('/bulk-create', \App\Livewire\Admin\MasterData\Kelas\BulkCreateKelas::class)->name('bulk-create');
            Route::get('/{id}', \App\Livewire\Admin\MasterData\Kelas\DetailKelas::class)->name('detail');
        });


        // Siswa
        Route::prefix('siswa')->name('siswa.')->group(function () {
            Route::get('/', \App\Livewire\Admin\MasterData\Siswa\IndexSiswa::class)->name('index');
            Route::get('/non-aktif', \App\Livewire\Admin\MasterData\Siswa\SiswaNonAktif::class)->name('nonaktif');
            Route::get('/{id}', \App\Livewire\Admin\MasterData\Siswa\DetailSiswa::class)->name('detail');
        });

        // Guru
        Route::prefix('guru')->name('guru.')->group(function () {
            Route::get('/', \App\Livewire\Admin\MasterData\Guru\IndexGuru::class)->name('index');
            Route::get('/non-aktif', \App\Livewire\Admin\MasterData\Guru\GuruNonAktif::class)->name('nonaktif');
        });

        // Kelompok Tahfidz
        Route::prefix('kelompok')->name('kelompok.')->group(function () {
            Route::get('/', \App\Livewire\Admin\MasterData\Kelompok\IndexKelompok::class)->name('index');
        });

        // Tahun Ajaran
        Route::prefix('tahun-ajaran')->name('tahun-ajaran.')->group(function () {
            Route::get('/', \App\Livewire\Admin\MasterData\TahunAjaran\IndexTahunAjaran::class)->name('index');
        });

        // Referensi Al-Quran
        Route::get('/referensi-quran', \App\Livewire\Admin\MasterData\ReferensiQuran\IndexReferensiQuran::class)
            ->name('referensi-quran');
    });

    // ═══════════════════════════════════════════════════════════════════════════
    // TAHFIDZ & QURAN - REFERENSI
    // ═══════════════════════════════════════════════════════════════════════════

    Route::get('/referensi-juz', \App\Livewire\Shared\ReferensiJuz::class)
        ->name('admin.referensi-juz');

    // ═══════════════════════════════════════════════════════════════════════════
    // ABSENSI & SETORAN
    // ═══════════════════════════════════════════════════════════════════════════



    Route::get('/kelompok/{kelompok}/absensisetoran', \App\Livewire\Admin\Kelompok\DashboardKelompok::class)
        ->name('admin.kelompok.absensisetoran');


    Route::prefix('setoran')->name('admin.setoran.')->group(function () {
        Route::get('/input/{kelompok}', \App\Livewire\Admin\Setoran\InputSetoran::class)->name('input');
        Route::get('/riwayat-hafalan/{siswa}', \App\Livewire\Admin\Setoran\RiwayatHafalanSiswa::class)
            ->name('riwayat-hafalan');
    });

    // ═══════════════════════════════════════════════════════════════════════════
    // PROGRES HAFALAN
    // ═══════════════════════════════════════════════════════════════════════════

    Route::get('/progres', \App\Livewire\Admin\Progres\DashboardProgresAdmin::class)
        ->name('admin.progres.dashboard');

    // ═══════════════════════════════════════════════════════════════════════════
    // USULAN KENAIKAN TINGKAT
    // ═══════════════════════════════════════════════════════════════════════════

    Route::prefix('usulan-kenaikan-tingkat')->name('admin.usulan-kenaikan-tingkat.')->group(function () {
        Route::get('/', \App\Livewire\Admin\UsulanKenaikanTingkat\IndexUsulan::class)->name('index');
    });

    // ═══════════════════════════════════════════════════════════════════════════
    // KELULUSAN PROGRAM TAHFIDZ
    // ═══════════════════════════════════════════════════════════════════════════

    Route::prefix('kelulusan-program')->name('admin.kelulusan-program.')->group(function () {
        Route::get('/', \App\Livewire\Admin\KelulusanProgram\IndexKelulusan::class)->name('index');
    });

    // ═══════════════════════════════════════════════════════════════════════════
    // TES PENEMPATAN (Feature Toggle)
    // ═══════════════════════════════════════════════════════════════════════════

    Route::prefix('tes-penempatan')->name('admin.tes-penempatan.')
        ->middleware('feature:tes_penempatan')
        ->group(function () {
            Route::get('/', \App\Livewire\Admin\TesPenempatan\IndexTesPenempatan::class)->name('index');
            Route::get('/review/{id}', \App\Livewire\Admin\TesPenempatan\ReviewTestResults::class)->name('review');
        });

    // ═══════════════════════════════════════════════════════════════════════════
    // ROLLOVER TAHUN AJARAN
    // ═══════════════════════════════════════════════════════════════════════════

    Route::get('/rollover-tahun-ajaran', \App\Livewire\Admin\IntegratedRolloverModal::class)
        ->name('admin.rollover-tahun-ajaran');

    // ═══════════════════════════════════════════════════════════════════════════
    // ALAT ADMINISTRATIF & PENGATURAN
    // ═══════════════════════════════════════════════════════════════════════════

    Route::get('/audit-log', \App\Livewire\Admin\AuditLog\IndexAuditLog::class)
        ->name('admin.audit-log');

    Route::get('/pengaturan', \App\Livewire\Admin\PengaturanAdmin::class)
        ->name('admin.pengaturan');

});  // end auth.admin middleware group

// ═══════════════════════════════════════════════════════════════════════════
// IMPERSONATION STOP (outside auth.admin: only requires guru session)
// ═══════════════════════════════════════════════════════════════════════════
Route::get('/impersonate/stop', [\App\Http\Controllers\Admin\ImpersonateController::class, 'stop'])
    ->name('admin.impersonate.stop')
    ->middleware('auth:guru');
