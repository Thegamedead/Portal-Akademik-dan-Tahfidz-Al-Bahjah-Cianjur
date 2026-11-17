<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\GuruLoginController;
use App\Http\Controllers\Auth\SiswaLoginController;
use Illuminate\Support\Facades\Route;

// ─── Login (GET) ─────────────────────────────────────────────────────────────
// Masing-masing dilindungi redirect_if_authenticated agar pengguna yang sudah
// login tidak bisa mengakses halaman login lagi.

Route::get('/admin/login', [AdminLoginController::class, 'show'])
    ->name('admin.login')
    ->middleware('redirect_if_authenticated:admin');

Route::get('/guru/login', [GuruLoginController::class, 'show'])
    ->name('guru.login')
    ->middleware('redirect_if_authenticated:guru');

Route::get('/siswa/login', [SiswaLoginController::class, 'show'])
    ->name('siswa.login')
    ->middleware('redirect_if_authenticated:siswa');

// ─── Logout (POST) ────────────────────────────────────────────────────────────
// Masing-masing dilindungi auth:{guard} — hanya pengguna yang sedang
// terautentikasi pada guard tersebut yang boleh melakukan logout.

Route::post('/admin/logout', [AdminLoginController::class, 'logout'])
    ->name('admin.logout')
    ->middleware('auth:admin');

Route::post('/guru/logout', [GuruLoginController::class, 'logout'])
    ->name('guru.logout')
    ->middleware('auth:guru');

Route::post('/siswa/logout', [SiswaLoginController::class, 'logout'])
    ->name('siswa.logout')
    ->middleware('auth:siswa');
