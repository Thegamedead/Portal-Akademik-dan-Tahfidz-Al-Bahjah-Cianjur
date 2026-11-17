<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SiswaLoginController extends Controller
{
    /**
     * Tampilkan halaman login Siswa.
     */
    public function show(): View
    {
        return view('auth.siswa.login');
    }

    /**
     * Logout Siswa: invalidate sesi, regenerate CSRF token, redirect ke halaman login.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('siswa')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('siswa.login');
    }
}
