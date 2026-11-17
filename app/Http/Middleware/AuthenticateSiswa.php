<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateSiswa
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('siswa')->check()) {
            // Cek apakah guard lain sedang aktif — jika ya, arahkan ke dashboard-nya
            // (isolasi lintas guard: Req 6.2)
            foreach (['admin', 'guru'] as $otherGuard) {
                if (auth($otherGuard)->check()) {
                    return redirect()->route($otherGuard . '.dashboard');
                }
            }

            return redirect()->route('siswa.login');
        }
        return $next($request);
    }
}
