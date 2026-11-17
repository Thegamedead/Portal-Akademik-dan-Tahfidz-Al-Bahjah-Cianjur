<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| File ini hanya berisi route global (home redirect).
|
| Route per-role dikelola di file masing-masing yang dimuat oleh bootstrap/app.php:
|   - routes/admin.php  → prefix: /admin, middleware: auth.admin
|   - routes/guru.php   → prefix: /guru,  middleware: auth.guru, session.timeout
|   - routes/siswa.php  → prefix: /siswa, middleware: auth.siswa, session.timeout
|   - routes/auth.php   → login/logout endpoints
|
*/

Route::get('/', function () {
    return redirect()->route('admin.login');
})->name('home');

require __DIR__.'/auth.php';


