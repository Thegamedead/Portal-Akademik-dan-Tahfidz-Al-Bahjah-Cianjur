<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(base_path('routes/auth.php'));
            Route::middleware('web')->prefix('admin')->group(base_path('routes/admin.php'));
            Route::middleware('web')->prefix('guru')->group(base_path('routes/guru.php'));
            Route::middleware('web')->prefix('siswa')->group(base_path('routes/siswa.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.admin' => \App\Http\Middleware\AuthenticateAdmin::class,
            'auth.guru' => \App\Http\Middleware\AuthenticateGuru::class,
            'auth.siswa' => \App\Http\Middleware\AuthenticateSiswa::class,
            'redirect_if_authenticated' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'session.timeout' => \App\Http\Middleware\SessionTimeout::class,
            'verify.kelompok.ownership' => \App\Http\Middleware\VerifyKelompokOwnership::class,
            'feature' => \App\Http\Middleware\CheckFeatureToggle::class,
            'force.change.password' => \App\Http\Middleware\ForceChangePassword::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
