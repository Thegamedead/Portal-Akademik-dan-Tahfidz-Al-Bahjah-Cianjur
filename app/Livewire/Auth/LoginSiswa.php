<?php

namespace App\Livewire\Auth;

use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class LoginSiswa extends Component
{
    // ─── Properties ──────────────────────────────────────────────────────────

    public string $nis = '';
    public string $password = '';
    public int $rateLimitSeconds = 0;


    // ─── Validation Rules ─────────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'nis'      => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9\-_]+$/'  // NIS hanya boleh alfanumerik, dash, underscore
            ],
            'password' => [
                'required',
                'string',
                'min:8',  // Minimal 8 karakter
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_+\-=\[\]{};:\'",.<>\/\\|`~]).{8,}$/'  // Harus ada: huruf kecil, huruf besar, angka, karakter khusus
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'nis.required'      => 'NIS wajib diisi.',
            'nis.max'           => 'NIS maksimal 50 karakter.',
            'nis.regex'         => 'Format NIS tidak valid. Hanya boleh huruf, angka, dash (-), dan underscore (_).',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
            'password.regex'    => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus.',
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
     * Proses autentikasi Siswa:
     *  1. Validasi input
     *  2. Cek rate limiting layer IP
     *  3. Cek rate limiting layer identitas
     *  4. Validasi token hCaptcha jika $showCaptcha aktif
     *  5. Query manual Siswa::where('nis', ...) — tidak pakai attempt() karena
     *     tanggal_lahir bukan hash
     *  6. Bandingkan tanggal_lahir dari DB dengan input
     *  7. Jika berhasil: cek status_aktif, login, redirect dashboard
     *  8. Jika gagal: hit rate limiter, tambah failure count, cek captcha threshold
     */
    public function authenticate(): void
    {
        $this->validate();

        $ipKey       = $this->ipRateLimitKey();
        $identityKey = $this->identityRateLimitKey();

        // ── Layer 2: Rate limiting berbasis IP ──────────────────────────────
        if (RateLimiter::tooManyAttempts($ipKey, config('auth_security.ip_max_attempts', 20))) {
            $this->rateLimitSeconds = RateLimiter::availableIn($ipKey);
            $this->addError('nis', 'Terlalu banyak percobaan dari perangkat ini.');
            return;
        }

        // ── Layer 1: Rate limiting berbasis identitas ───────────────────────
        if (RateLimiter::tooManyAttempts($identityKey, config('auth_security.identity_max_attempts', 5))) {
            $this->rateLimitSeconds = RateLimiter::availableIn($identityKey);
            $this->addError('nis', 'Terlalu banyak percobaan.');
            return;
        }



        // ── Auth attempt (menggunakan password) ────────────────────────────
        $kredensialValid = Auth::guard('siswa')->attempt([
            'nis'      => $this->nis,
            'password' => $this->password,
        ]);

        if (! $kredensialValid) {
            // Catat gagal di kedua layer
            RateLimiter::hit($identityKey, config('auth_security.identity_decay_seconds', 60));
            RateLimiter::hit($ipKey, config('auth_security.ip_decay_seconds', 60));


            $this->addError('nis', 'Kredensial tidak valid. Silakan coba lagi.');
            return;
        }

        // Ambil user yang baru login
        $siswa = Auth::guard('siswa')->user();

        // ── Kredensial cocok — cek akun_aktif (akses login, bukan status akademik) ──
        if (! $siswa->akun_aktif) {
            Auth::guard('siswa')->logout();

            // Catat percobaan gagal (akun nonaktif tetap dihitung)
            RateLimiter::hit($identityKey, config('auth_security.identity_decay_seconds', 60));
            RateLimiter::hit($ipKey, config('auth_security.ip_decay_seconds', 60));

            // Pesan spesifik untuk akun nonaktif
            $this->addError('nis', 'Akun dinonaktifkan. Hubungi Admin.');
            return;
        }

        // ── Login sukses: bersihkan rate limiter ───
        RateLimiter::clear($identityKey);

        // Inisialisasi last_activity untuk session timeout
        session(['last_activity_siswa' => now()]);


        // Redirect ke dashboard Siswa
        $this->redirect(route('siswa.dashboard'));
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.login-siswa');
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    /**
     * Key rate limiting layer identitas.
     * Format: "login.identity.siswa.{nis}"
     */
    private function identityRateLimitKey(): string
    {
        return 'login.identity.siswa.' . mb_strtolower(trim($this->nis));
    }

    /**
     * Key rate limiting layer IP.
     * Format: "login.ip.siswa.{ip}"
     */
    private function ipRateLimitKey(): string
    {
        return 'login.ip.siswa.' . request()->ip();
    }


}
