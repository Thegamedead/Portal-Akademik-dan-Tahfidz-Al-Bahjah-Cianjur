<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('admin')->check()) {
            // Cek apakah guard lain sedang aktif — jika ya, arahkan ke dashboard-nya
            // (isolasi lintas guard: Req 6.2)
            foreach (['guru', 'siswa'] as $otherGuard) {
                if (auth($otherGuard)->check()) {
                    return redirect()->route($otherGuard . '.dashboard');
                }
            }

            return redirect()->route('admin.login');
        }
        return $next($request);
    }
}
