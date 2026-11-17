<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    /**
     * Dashboard routes per guard.
     */
    protected array $dashboards = [
        'admin' => 'admin.dashboard',
        'guru'  => 'guru.dashboard',
        'siswa' => 'siswa.dashboard',
    ];

    /**
     * Handle an incoming request.
     *
     * Accepts an optional guard parameter (e.g. redirect_if_authenticated:admin).
     * Checks all three guards and redirects to the dashboard of whichever guard
     * is currently authenticated — prevents already-logged-in users from
     * accessing login pages again.
     *
     * @param  string  ...$guards  Optional guard names passed via middleware parameter syntax
     */
    public function handle(Request $request, Closure $next, string ...$guards): mixed
    {
        // Always check all three guards regardless of which login page we're on.
        // This way, e.g. an authenticated guru who navigates to /admin/login
        // is still redirected to guru.dashboard instead of seeing the admin form.
        foreach ($this->dashboards as $guard => $dashboardRoute) {
            if (auth($guard)->check()) {
                return redirect()->route($dashboardRoute);
            }
        }

        return $next($request);
    }
}
