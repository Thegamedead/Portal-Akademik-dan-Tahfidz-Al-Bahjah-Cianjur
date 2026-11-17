<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\LoginAdmin;
use App\Livewire\Auth\LoginGuru;
use App\Livewire\Auth\LoginSiswa;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * 14.6 — LoginRedirectDashboardPropertyTest
 *
 * Property 6: Login Redirect ke Dashboard Guard yang Benar
 *
 * Untuk semua login berhasil dari salah satu dari tiga guard, sistem HARUS
 * selalu mengarahkan ke dashboard yang sesuai dengan guard tersebut:
 *   - Admin  → /admin/dashboard  (route('admin.dashboard'))
 *   - Guru   → /guru/dashboard   (route('guru.dashboard'))
 *   - Siswa  → /siswa/dashboard  (route('siswa.dashboard'))
 *
 * Tidak pernah ke dashboard guard lain.
 *
 * **Validates: Requirements 6.1**
 */
class LoginRedirectDashboardPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    // ─── URL Dashboard per guard ──────────────────────────────────────────────

    private const DASHBOARD_ADMIN = '/admin/dashboard';
    private const DASHBOARD_GURU  = '/guru/dashboard';
    private const DASHBOARD_SISWA = '/siswa/dashboard';

    // ─── Setup ────────────────────────────────────────────────────────────────

    protected function setUp(): void
    {
        parent::setUp();
        $this->clearAllIpRateLimiters();
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Bersihkan rate limiter IP untuk ketiga guard.
     */
    private function clearAllIpRateLimiters(): void
    {
        $ip = request()->ip();
        RateLimiter::clear("login.ip.admin.{$ip}");
        RateLimiter::clear("login.ip.guru.{$ip}");
        RateLimiter::clear("login.ip.siswa.{$ip}");
    }

    /**
     * Hasilkan password unik yang deterministik dari integer seed.
     * Prefix 'PBT_pw_' + padded number memastikan panjang min terpenuhi.
     */
    private static function seedToPassword(int $seed): string
    {
        return 'PBT_pw_' . str_pad((string) abs($seed % 1_000_000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Hasilkan username unik yang deterministik dari integer seed.
     * Prefix 'pbt_admin_' memastikan tidak bertabrakan dengan data lain.
     */
    private static function seedToUsername(int $seed): string
    {
        return 'pbt_admin_' . str_pad((string) abs($seed % 1_000_000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Hasilkan NIY unik dari integer seed.
     * Format: '21' + 8 digit → total 10 karakter.
     */
    private static function seedToNiy(int $seed): string
    {
        return '21' . str_pad((string) abs($seed % 100_000_000), 8, '0', STR_PAD_LEFT);
    }

    /**
     * Hasilkan NIS unik dari integer seed.
     * Format: '31' + 8 digit → total 10 karakter.
     */
    private static function seedToNis(int $seed): string
    {
        return '31' . str_pad((string) abs($seed % 100_000_000), 8, '0', STR_PAD_LEFT);
    }

    // ─── Tests ────────────────────────────────────────────────────────────────

    /**
     * Property: Admin login berhasil SELALU redirect ke /admin/dashboard —
     * tidak pernah ke /guru/dashboard atau /siswa/dashboard.
     *
     * Strategi: generate seed integer untuk membuat akun admin unik per iterasi
     * dengan password yang diketahui. Verifikasi redirect tujuan dan bukan redirect
     * ke dashboard guard lain.
     */
    public function test_property_admin_login_sukses_selalu_redirect_ke_admin_dashboard(): void
    {
        $this->limitTo(30)->forAll(
            Generators::choose(1, 999_999)
        )->then(function (int $seed) {
            $username = self::seedToUsername($seed);
            $password = self::seedToPassword($seed);

            // Buat akun admin aktif baru untuk iterasi ini
            Admin::factory()->create([
                'username'     => $username,
                'password'     => Hash::make($password),
                'status_aktif' => true,
            ]);

            // Bersihkan rate limiter untuk identitas ini
            RateLimiter::clear('login.identity.admin.' . mb_strtolower($username));
            $this->clearAllIpRateLimiters();

            $component = Livewire::test(LoginAdmin::class)
                ->set('username', $username)
                ->set('password', $password)
                ->call('authenticate');

            // ── Properti utama: harus redirect ke admin.dashboard ──────────
            $component->assertRedirect(self::DASHBOARD_ADMIN);

            // ── Properti negatif: tidak boleh redirect ke dashboard lain ───
            $redirectUrl = $component->effects['redirect'] ?? null;
            $this->assertNotNull($redirectUrl, "Login admin harus menghasilkan redirect (seed={$seed})");

            $this->assertStringNotContainsString(
                self::DASHBOARD_GURU,
                $redirectUrl,
                "Admin login tidak boleh redirect ke guru dashboard (seed={$seed})"
            );
            $this->assertStringNotContainsString(
                self::DASHBOARD_SISWA,
                $redirectUrl,
                "Admin login tidak boleh redirect ke siswa dashboard (seed={$seed})"
            );
        });
    }

    /**
     * Property: Guru login berhasil SELALU redirect ke /guru/dashboard —
     * tidak pernah ke /admin/dashboard atau /siswa/dashboard.
     *
     * Strategi: generate seed integer untuk membuat akun guru unik per iterasi
     * dengan NIY dan tanggal lahir yang diketahui. Verifikasi redirect tujuan.
     */
    public function test_property_guru_login_sukses_selalu_redirect_ke_guru_dashboard(): void
    {
        $this->limitTo(30)->forAll(
            Generators::choose(1, 999_999)
        )->then(function (int $seed) {
            $niy          = self::seedToNiy($seed);
            $tanggalLahir = '1985-06-15'; // tanggal lahir tetap — kredensial yang diketahui

            // Buat akun guru aktif baru untuk iterasi ini
            Guru::factory()->create([
                'niy'          => $niy,
                'tanggal_lahir' => $tanggalLahir,
                'status_aktif'  => true,
            ]);

            // Bersihkan rate limiter untuk identitas ini
            RateLimiter::clear('login.identity.guru.' . mb_strtolower($niy));
            $this->clearAllIpRateLimiters();

            $component = Livewire::test(LoginGuru::class)
                ->set('niy', $niy)
                ->set('tanggal_lahir', $tanggalLahir)
                ->call('authenticate');

            // ── Properti utama: harus redirect ke guru.dashboard ───────────
            $component->assertRedirect(self::DASHBOARD_GURU);

            // ── Properti negatif: tidak boleh redirect ke dashboard lain ───
            $redirectUrl = $component->effects['redirect'] ?? null;
            $this->assertNotNull($redirectUrl, "Login guru harus menghasilkan redirect (seed={$seed})");

            $this->assertStringNotContainsString(
                self::DASHBOARD_ADMIN,
                $redirectUrl,
                "Guru login tidak boleh redirect ke admin dashboard (seed={$seed})"
            );
            $this->assertStringNotContainsString(
                self::DASHBOARD_SISWA,
                $redirectUrl,
                "Guru login tidak boleh redirect ke siswa dashboard (seed={$seed})"
            );
        });
    }

    /**
     * Property: Siswa login berhasil SELALU redirect ke /siswa/dashboard —
     * tidak pernah ke /admin/dashboard atau /guru/dashboard.
     *
     * Strategi: generate seed integer untuk membuat akun siswa unik per iterasi
     * dengan NIS dan tanggal lahir yang diketahui. Verifikasi redirect tujuan.
     */
    public function test_property_siswa_login_sukses_selalu_redirect_ke_siswa_dashboard(): void
    {
        $this->limitTo(30)->forAll(
            Generators::choose(1, 999_999)
        )->then(function (int $seed) {
            $nis          = self::seedToNis($seed);
            $tanggalLahir = '2008-03-20'; // tanggal lahir tetap — kredensial yang diketahui

            // Buat akun siswa aktif baru untuk iterasi ini
            Siswa::factory()->create([
                'nis'          => $nis,
                'tanggal_lahir' => $tanggalLahir,
                'status_aktif'  => true,
            ]);

            // Bersihkan rate limiter untuk identitas ini
            RateLimiter::clear('login.identity.siswa.' . mb_strtolower($nis));
            $this->clearAllIpRateLimiters();

            $component = Livewire::test(LoginSiswa::class)
                ->set('nis', $nis)
                ->set('tanggal_lahir', $tanggalLahir)
                ->call('authenticate');

            // ── Properti utama: harus redirect ke siswa.dashboard ─────────
            $component->assertRedirect(self::DASHBOARD_SISWA);

            // ── Properti negatif: tidak boleh redirect ke dashboard lain ───
            $redirectUrl = $component->effects['redirect'] ?? null;
            $this->assertNotNull($redirectUrl, "Login siswa harus menghasilkan redirect (seed={$seed})");

            $this->assertStringNotContainsString(
                self::DASHBOARD_ADMIN,
                $redirectUrl,
                "Siswa login tidak boleh redirect ke admin dashboard (seed={$seed})"
            );
            $this->assertStringNotContainsString(
                self::DASHBOARD_GURU,
                $redirectUrl,
                "Siswa login tidak boleh redirect ke guru dashboard (seed={$seed})"
            );
        });
    }

    /**
     * Property: Lintas guard — tidak ada satu pun login sukses yang menghasilkan
     * redirect ke dashboard guard yang SALAH.
     *
     * Ini adalah uji orthogonalitas: untuk setiap seed, buat satu akun dari
     * masing-masing guard dan verifikasi bahwa masing-masing redirect ke
     * dashboard yang benar, bukan dashboard guard lain.
     *
     * Verifikasi kunci:
     * - Admin → /admin/dashboard (bukan /guru atau /siswa)
     * - Guru  → /guru/dashboard  (bukan /admin atau /siswa)
     * - Siswa → /siswa/dashboard (bukan /admin atau /guru)
     */
    public function test_property_lintas_guard_redirect_tidak_pernah_ke_dashboard_guard_lain(): void
    {
        $this->limitTo(20)->forAll(
            Generators::choose(100_000, 199_999) // range berbeda dari test lain
        )->then(function (int $seed) {
            $suffix = str_pad((string) $seed, 6, '0', STR_PAD_LEFT);

            // ── Persiapkan data ketiga guard ──────────────────────────────
            $adminUsername = 'pbt_lintas_' . $suffix;
            $adminPassword = 'PBT_lintas_' . $suffix;
            $guruNiy       = '41' . $suffix . '00';
            $siswaNis      = '51' . $suffix . '00';
            $tanggalGuru   = '1987-09-10';
            $tanggalSiswa  = '2009-11-05';

            Admin::factory()->create([
                'username'     => $adminUsername,
                'password'     => Hash::make($adminPassword),
                'status_aktif' => true,
            ]);
            Guru::factory()->create([
                'niy'          => $guruNiy,
                'tanggal_lahir' => $tanggalGuru,
                'status_aktif'  => true,
            ]);
            Siswa::factory()->create([
                'nis'          => $siswaNis,
                'tanggal_lahir' => $tanggalSiswa,
                'status_aktif'  => true,
            ]);

            // ── Login Admin ───────────────────────────────────────────────
            RateLimiter::clear('login.identity.admin.' . mb_strtolower($adminUsername));
            $this->clearAllIpRateLimiters();

            $adminResult = Livewire::test(LoginAdmin::class)
                ->set('username', $adminUsername)
                ->set('password', $adminPassword)
                ->call('authenticate');

            $adminRedirect = $adminResult->effects['redirect'] ?? '';

            $this->assertStringContainsString(
                self::DASHBOARD_ADMIN,
                $adminRedirect,
                "Admin harus redirect ke /admin/dashboard (seed={$seed}). Dapat: {$adminRedirect}"
            );
            $this->assertStringNotContainsString(
                self::DASHBOARD_GURU,
                $adminRedirect,
                "Admin tidak boleh redirect ke /guru/dashboard (seed={$seed})"
            );
            $this->assertStringNotContainsString(
                self::DASHBOARD_SISWA,
                $adminRedirect,
                "Admin tidak boleh redirect ke /siswa/dashboard (seed={$seed})"
            );

            // ── Login Guru ────────────────────────────────────────────────
            RateLimiter::clear('login.identity.guru.' . mb_strtolower($guruNiy));
            $this->clearAllIpRateLimiters();

            $guruResult = Livewire::test(LoginGuru::class)
                ->set('niy', $guruNiy)
                ->set('tanggal_lahir', $tanggalGuru)
                ->call('authenticate');

            $guruRedirect = $guruResult->effects['redirect'] ?? '';

            $this->assertStringContainsString(
                self::DASHBOARD_GURU,
                $guruRedirect,
                "Guru harus redirect ke /guru/dashboard (seed={$seed}). Dapat: {$guruRedirect}"
            );
            $this->assertStringNotContainsString(
                self::DASHBOARD_ADMIN,
                $guruRedirect,
                "Guru tidak boleh redirect ke /admin/dashboard (seed={$seed})"
            );
            $this->assertStringNotContainsString(
                self::DASHBOARD_SISWA,
                $guruRedirect,
                "Guru tidak boleh redirect ke /siswa/dashboard (seed={$seed})"
            );

            // ── Login Siswa ───────────────────────────────────────────────
            RateLimiter::clear('login.identity.siswa.' . mb_strtolower($siswaNis));
            $this->clearAllIpRateLimiters();

            $siswaResult = Livewire::test(LoginSiswa::class)
                ->set('nis', $siswaNis)
                ->set('tanggal_lahir', $tanggalSiswa)
                ->call('authenticate');

            $siswaRedirect = $siswaResult->effects['redirect'] ?? '';

            $this->assertStringContainsString(
                self::DASHBOARD_SISWA,
                $siswaRedirect,
                "Siswa harus redirect ke /siswa/dashboard (seed={$seed}). Dapat: {$siswaRedirect}"
            );
            $this->assertStringNotContainsString(
                self::DASHBOARD_ADMIN,
                $siswaRedirect,
                "Siswa tidak boleh redirect ke /admin/dashboard (seed={$seed})"
            );
            $this->assertStringNotContainsString(
                self::DASHBOARD_GURU,
                $siswaRedirect,
                "Siswa tidak boleh redirect ke /guru/dashboard (seed={$seed})"
            );
        });
    }

    /**
     * Property: Setelah login sukses, guard yang benar terautentikasi —
     * dan guard lain TIDAK terautentikasi.
     *
     * Ini memverifikasi tidak hanya redirect URL, tapi juga state sesi
     * yang terbentuk setelah login berhasil.
     */
    public function test_property_login_sukses_hanya_mengautentikasi_guard_yang_benar(): void
    {
        $this->limitTo(20)->forAll(
            Generators::choose(200_000, 299_999) // range berbeda dari test lain
        )->then(function (int $seed) {
            $suffix = str_pad((string) $seed, 6, '0', STR_PAD_LEFT);

            // ── Test Admin: hanya guard 'admin' yang auth setelah login ───
            $adminUsername = 'pbt_sess_admin_' . $suffix;
            $adminPassword = 'PBT_sess_' . $suffix;

            $admin = Admin::factory()->create([
                'username'     => $adminUsername,
                'password'     => Hash::make($adminPassword),
                'status_aktif' => true,
            ]);

            RateLimiter::clear('login.identity.admin.' . mb_strtolower($adminUsername));
            $this->clearAllIpRateLimiters();

            Livewire::test(LoginAdmin::class)
                ->set('username', $adminUsername)
                ->set('password', $adminPassword)
                ->call('authenticate');

            // Guard admin harus terautentikasi
            $this->assertAuthenticatedAs($admin, 'admin');
            // Guard guru dan siswa harus tidak terautentikasi
            $this->assertGuest('guru');
            $this->assertGuest('siswa');

            // ── Reset sesi untuk iterasi bersih ───────────────────────────
            \Illuminate\Support\Facades\Auth::guard('admin')->logout();

            // ── Test Guru: hanya guard 'guru' yang auth setelah login ─────
            $guruNiy       = '61' . $suffix . '00';
            $tanggalGuru   = '1988-04-22';

            $guru = Guru::factory()->create([
                'niy'          => $guruNiy,
                'tanggal_lahir' => $tanggalGuru,
                'status_aktif'  => true,
            ]);

            RateLimiter::clear('login.identity.guru.' . mb_strtolower($guruNiy));
            $this->clearAllIpRateLimiters();

            Livewire::test(LoginGuru::class)
                ->set('niy', $guruNiy)
                ->set('tanggal_lahir', $tanggalGuru)
                ->call('authenticate');

            // Guard guru harus terautentikasi
            $this->assertAuthenticatedAs($guru, 'guru');
            // Guard admin dan siswa harus tidak terautentikasi
            $this->assertGuest('admin');
            $this->assertGuest('siswa');

            // Reset
            \Illuminate\Support\Facades\Auth::guard('guru')->logout();

            // ── Test Siswa: hanya guard 'siswa' yang auth setelah login ───
            $siswaNis     = '71' . $suffix . '00';
            $tanggalSiswa = '2009-07-14';

            $siswa = Siswa::factory()->create([
                'nis'          => $siswaNis,
                'tanggal_lahir' => $tanggalSiswa,
                'status_aktif'  => true,
            ]);

            RateLimiter::clear('login.identity.siswa.' . mb_strtolower($siswaNis));
            $this->clearAllIpRateLimiters();

            Livewire::test(LoginSiswa::class)
                ->set('nis', $siswaNis)
                ->set('tanggal_lahir', $tanggalSiswa)
                ->call('authenticate');

            // Guard siswa harus terautentikasi
            $this->assertAuthenticatedAs($siswa, 'siswa');
            // Guard admin dan guru harus tidak terautentikasi
            $this->assertGuest('admin');
            $this->assertGuest('guru');

            // Cleanup
            \Illuminate\Support\Facades\Auth::guard('siswa')->logout();
        });
    }
}
