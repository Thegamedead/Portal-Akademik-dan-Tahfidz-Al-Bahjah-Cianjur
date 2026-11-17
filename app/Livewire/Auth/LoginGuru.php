<?php

namespace App\Livewire\Auth;

use App\Models\Guru;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class LoginGuru extends Component
{
    // ─── Properties ──────────────────────────────────────────────────────────

    public string $niy = '';
    public string $password = '';
    public int $rateLimitSeconds = 0;


    // ─── Validation Rules ─────────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'niy'      => [
                'required',
                'string',
                'digits:16',  // NIY harus tepat 16 digit angka
            ],
            'password' => [
                'required',
                'string',
                'size:8',  // Tepat 8 karakter
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_+\-=\[\]{};:\'",.<>\/\\|`~]).{8,}$/'  // Harus ada: huruf kecil, huruf besar, angka, karakter khusus
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'niy.required' => 'NIY wajib diisi.',
            'niy.digits'   => 'NIY harus berupa 16 digit angka.',
            'password.required' => 'Password wajib diisi.',
            'password.size'     => 'Password harus tepat 8 karakter.',
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
     * Proses autentikasi Guru:
     *  1. Validasi input
     *  2. Cek rate limiting layer IP
     *  3. Cek rate limiting layer identitas
     *  4. Validasi token hCaptcha jika $showCaptcha aktif
     *  5. Query manual Guru::where('niy', ...) — tidak pakai attempt() karena
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
            $this->addError('niy', 'Terlalu banyak percobaan dari perangkat ini.');
            return;
        }

        // ── Layer 1: Rate limiting berbasis identitas ───────────────────────
        if (RateLimiter::tooManyAttempts($identityKey, config('auth_security.identity_max_attempts', 5))) {
            $this->rateLimitSeconds = RateLimiter::availableIn($identityKey);
            $this->addError('niy', 'Terlalu banyak percobaan.');
            return;
        }



        // ── Auth attempt (menggunakan password) ────────────────────────────
        $kredensialValid = Auth::guard('guru')->attempt([
            'niy'      => $this->niy,
            'password' => $this->password,
        ]);

        if (! $kredensialValid) {
            // Catat gagal di kedua layer
            RateLimiter::hit($identityKey, config('auth_security.identity_decay_seconds', 60));
            RateLimiter::hit($ipKey, config('auth_security.ip_decay_seconds', 60));


            $this->addError('niy', 'Kredensial tidak valid. Silakan coba lagi.');
            return;
        }

        // Ambil user yang baru login
        $guru = Auth::guard('guru')->user();

        // ── Kredensial cocok — cek akun_aktif (akses login, bukan status akademik) ──
        if (! $guru->akun_aktif) {
            Auth::guard('guru')->logout();

            // Catat percobaan gagal (akun nonaktif tetap dihitung)
            RateLimiter::hit($identityKey, config('auth_security.identity_decay_seconds', 60));
            RateLimiter::hit($ipKey, config('auth_security.ip_decay_seconds', 60));

            // Pesan spesifik untuk akun nonaktif
            $this->addError('niy', 'Akun dinonaktifkan. Hubungi Admin.');
            return;
        }

        // ── Login sukses: bersihkan rate limiter ───
        RateLimiter::clear($identityKey);

        // Inisialisasi last_activity untuk session timeout
        session(['last_activity_guru' => now()]);


        // Redirect ke dashboard Guru
        $this->redirect(route('guru.dashboard'));
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        return view('livewire.auth.login-guru');
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    /**
     * Key rate limiting layer identitas.
     * Format: "login.identity.guru.{niy}"
     */
    private function identityRateLimitKey(): string
    {
        return 'login.identity.guru.' . mb_strtolower(trim($this->niy));
    }

    /**
     * Key rate limiting layer IP.
     * Format: "login.ip.guru.{ip}"
     */
    private function ipRateLimitKey(): string
    {
        return 'login.ip.guru.' . request()->ip();
    }


}
