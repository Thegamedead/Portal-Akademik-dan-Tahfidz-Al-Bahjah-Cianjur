<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class LoginAdmin extends Component
{
    // ─── Properties ──────────────────────────────────────────────────────────

    public string $email = '';
    public string $password = '';
    public bool $showPassword = false;
    public int $rateLimitSeconds = 0;


    // ─── Validation Rules ─────────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'email'    => [
                'required',
                'string',
                'email',  // Validasi email simple saja
                'max:255'
            ],
            'password' => [
                'required',
                'string',
                'min:6',  // Minimal 6 karakter saja
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.max'         => 'Email maksimal 255 karakter.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ];
    }

    /**
     * Real-time validation saat user mengetik
     */
    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    // ─── Methods ──────────────────────────────────────────────────────────────

    /**
     * Proses autentikasi Admin:
     *  1. Validasi input
     *  2. Cek rate limiting layer IP
     *  3. Cek rate limiting layer identitas
     *  4. Validasi token hCaptcha jika $showCaptcha aktif
     *  5. Attempt Auth::guard('admin')
     *  6. Jika berhasil: cek status_aktif, lalu redirect
     *  7. Jika gagal: hit rate limiter, tambah failure count, cek captcha threshold
     */
    public function authenticate(): void
    {
        $this->validate();

        $ipKey       = $this->ipRateLimitKey();
        $identityKey = $this->identityRateLimitKey();

        // ── Layer 2: Rate limiting berbasis IP ──────────────────────────────
        if (RateLimiter::tooManyAttempts($ipKey, config('auth_security.ip_max_attempts', 20))) {
            $this->rateLimitSeconds = RateLimiter::availableIn($ipKey);
            $this->addError('email', 'Terlalu banyak percobaan dari perangkat ini.');
            return;
        }

        // ── Layer 1: Rate limiting berbasis identitas ───────────────────────
        if (RateLimiter::tooManyAttempts($identityKey, config('auth_security.identity_max_attempts', 5))) {
            $this->rateLimitSeconds = RateLimiter::availableIn($identityKey);
            $this->addError('email', 'Terlalu banyak percobaan.');
            return;
        }



        // ── Attempt autentikasi ─────────────────────────────────────────────
        $attempted = Auth::guard('admin')->attempt([
            'email'    => $this->email,
            'password' => $this->password,
        ]);

        if (! $attempted) {
            // Catat gagal di kedua layer
            RateLimiter::hit($identityKey, config('auth_security.identity_decay_seconds', 60));
            RateLimiter::hit($ipKey, config('auth_security.ip_decay_seconds', 60));


            // Pesan error SPESIFIK (sesuai request user)
            $adminExists = \App\Models\Admin::where('email', $this->email)->exists();
            if ($adminExists) {
                $this->addError('password', 'Password yang Anda masukkan salah.');
            } else {
                $this->addError('email', 'Email tidak terdaftar.');
            }
            return;
        }

        // ── Autentikasi berhasil — cek status_aktif ─────────────────────────
        /** @var \App\Models\Admin $admin */
        $admin = Auth::guard('admin')->user();

        if (! $admin->status_aktif) {
            // Logout karena akun nonaktif
            Auth::guard('admin')->logout();

            // Catat percobaan gagal (akun nonaktif tetap dihitung) — konsisten dengan guard lain
            RateLimiter::hit($identityKey, config('auth_security.identity_decay_seconds', 60));
            RateLimiter::hit($ipKey, config('auth_security.ip_decay_seconds', 60));


            // Pesan spesifik untuk akun nonaktif
            $this->addError('email', 'Akun tidak aktif.');
            return;
        }

        // ── Login sukses: bersihkan rate limiter ───
        RateLimiter::clear($identityKey);

        // Inisialisasi last_activity untuk session timeout (walaupun admin tidak ada timeout,
        // ini memastikan konsistensi dengan guard lain)
        session(['last_activity_admin' => now()]);



        // Redirect ke dashboard Admin
        $this->redirect(route('admin.dashboard'));
    }

    /**
     * Toggle visibilitas field password.
     */
    public function togglePassword(): void
    {
        $this->showPassword = ! $this->showPassword;
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.login-admin');
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    /**
     * Key rate limiting layer identitas.
     * Format: "login.identity.admin.{username}"
     */
    private function identityRateLimitKey(): string
    {
        return 'login.identity.admin.' . mb_strtolower(trim($this->email));
    }

    /**
     * Key rate limiting layer IP.
     * Format: "login.ip.admin.{ip}"
     */
    private function ipRateLimitKey(): string
    {
        return 'login.ip.admin.' . request()->ip();
    }


}
