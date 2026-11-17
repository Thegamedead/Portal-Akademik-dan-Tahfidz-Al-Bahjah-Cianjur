<?php

namespace App\Http\Middleware;

use App\Services\SessionTimeoutService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SessionTimeout
{
    public function __construct(
        protected SessionTimeoutService $timeoutService,
    ) {}

    /**
     * Handle an incoming request.
     *
     * Cek timeout sesi untuk guard guru & siswa.
     * Admin tidak memiliki timeout otomatis — middleware melewati request admin tanpa sentuhan.
     *
     * Alur:
     *   1. Deteksi guard dari prefix URL (/guru/* atau /siswa/*)
     *   2. Jika bukan guru/siswa → pass through (admin, dsb.)
     *   3. Jika sesi expired → logout, hapus last_activity, flash pesan, redirect login
     *   4. Jika sesi valid → update last_activity = now(), lanjutkan request
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        // Tentukan guard dari prefix route
        $guard = $this->detectGuard($request);

        // 7.5 — Admin dan rute lain yang bukan guru/siswa: pass through tanpa tindakan
        if (! in_array($guard, ['guru', 'siswa'])) {
            return $next($request);
        }

        // Cek hanya jika pengguna terautentikasi pada guard yang sesuai
        if (Auth::guard($guard)->check()) {
            // 7.2/7.1 — Gunakan SessionTimeoutService untuk pengecekan
            if ($this->timeoutService->isExpired($guard)) {
                // 7.4 — Timeout terdeteksi: logout, bersihkan session, flash pesan, redirect
                Auth::guard($guard)->logout();
                session()->forget("last_activity_{$guard}");
                session()->flash(
                    'timeout_message',
                    'Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.'
                );

                return redirect()->route("{$guard}.login");
            }

            // 7.3 — Sesi valid: perbarui last_activity setiap request
            session(["last_activity_{$guard}" => now()]);
        }

        return $next($request);
    }

    /**
     * Deteksi guard dari prefix URL request.
     *
     * /guru  atau /guru/*  → 'guru'
     * /siswa atau /siswa/* → 'siswa'
     * lainnya              → null (termasuk /admin/* yang tidak ada timeout)
     */
    protected function detectGuard(Request $request): ?string
    {
        if ($request->is('guru') || $request->is('guru/*')) {
            return 'guru';
        }

        if ($request->is('siswa') || $request->is('siswa/*')) {
            return 'siswa';
        }

        return null;
    }
}
