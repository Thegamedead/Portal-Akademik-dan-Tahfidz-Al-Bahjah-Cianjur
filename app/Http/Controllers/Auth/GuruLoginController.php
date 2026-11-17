<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GuruLoginController extends Controller
{
    /**
     * Tampilkan halaman login Guru.
     */
    public function show(): View
    {
        return view('auth.guru.login');
    }

    /**
     * Logout Guru: invalidate sesi, regenerate CSRF token, redirect ke halaman login.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('guru')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('guru.login');
    }
}
