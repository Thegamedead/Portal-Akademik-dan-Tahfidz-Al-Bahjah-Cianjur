<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceChangePassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('guru')->user() ?? auth('siswa')->user();

        if ($user && $user->keterangan_akun === 'Password belum diganti') {
            
            $currentRouteName = $request->route() ? $request->route()->getName() : '';
            $path = $request->path();

            // Allow access to profile and logout to avoid infinite loops and allow them to change password/logout
            $allowedRoutes = [
                'guru.profil',
                'siswa.profil',
            ];
            
            // Allow livewire updates since the profile form is a Livewire component
            $isLivewire = $request->is('livewire/update') || $request->is('livewire/*');
            
            // Allow logout
            $isLogout = $path === 'guru/logout' || $path === 'siswa/logout';

            if (!in_array($currentRouteName, $allowedRoutes) && !$isLivewire && !$isLogout) {
                $redirectRoute = auth('guru')->check() ? 'guru.profil' : 'siswa.profil';
                // Add a flash message that will be handled by the layout (using session)
                session()->flash('toast', [
                    'variant' => 'warning',
                    'message' => 'Anda diwajibkan untuk mengganti password default sebelum dapat mengakses sistem.'
                ]);
                return redirect()->route($redirectRoute);
            }
        }

        return $next($request);
    }
}
