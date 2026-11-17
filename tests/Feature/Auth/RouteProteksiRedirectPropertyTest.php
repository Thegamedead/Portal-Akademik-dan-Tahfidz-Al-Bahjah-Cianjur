<?php

namespace Tests\Feature\Auth;

use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 14.8 — RouteProteksiRedirectPropertyTest
 *
 * Property 8: Rute Terlindungi Tanpa Auth Selalu Redirect ke Login Guard yang Tepat
 *
 * Untuk semua rute yang dilindungi oleh guard tertentu, akses tanpa autentikasi
 * apapun HARUS selalu menghasilkan redirect ke halaman login guard yang
 * bertanggung jawab atas rute tersebut.
 *
 * Guard dan rute yang diuji:
 *   - admin:  /admin/dashboard, /admin/manajemen-akun,
 *             /admin/manajemen-akun/admin, /admin/profil
 *   - guru:   /guru/dashboard, /guru/profil
 *   - siswa:  /siswa/dashboard, /siswa/profil
 *
 * **Validates: Requirements 6.3**
 */
class RouteProteksiRedirectPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    // ─── Konstanta rute per guard ─────────────────────────────────────────────

    /**
     * Pemetaan rute terlindungi ke URL login guard yang bertanggung jawab.
     * Format: 'url_rute' => 'url_login_guard'
     */
    private const PETA_RUTE_KE_LOGIN = [
        // Admin routes → redirect ke /admin/login
        '/admin/dashboard'             => '/admin/login',
        '/admin/manajemen-akun'        => '/admin/login',
        '/admin/manajemen-akun/admin'  => '/admin/login',
        '/admin/profil'                => '/admin/login',

        // Guru routes → redirect ke /guru/login
        '/guru/dashboard'              => '/guru/login',
        '/guru/profil'                 => '/guru/login',

        // Siswa routes → redirect ke /siswa/login
        '/siswa/dashboard'             => '/siswa/login',
        '/siswa/profil'                => '/siswa/login',
    ];

    /**
     * URL login masing-masing guard — dipakai untuk verifikasi negatif
     * (redirect tidak boleh ke login guard lain).
     */
    private const LOGIN_URLS = [
        'admin' => '/admin/login',
        'guru'  => '/guru/login',
        'siswa' => '/siswa/login',
    ];

    // ─── Helper ───────────────────────────────────────────────────────────────

    /**
     * Verifikasi bahwa response merupakan redirect ke URL login yang tepat.
     *
     * @param  \Illuminate\Testing\TestResponse  $response
     * @param  string  $rute        URL rute yang diakses tanpa auth
     * @param  string  $loginYangBenar  URL login guard yang seharusnya menjadi tujuan redirect
     * @param  string  $konteks     Keterangan untuk pesan kegagalan assertion
     */
    private function assertRedirectKeLoginGuardYangBenar(
        \Illuminate\Testing\TestResponse $response,
        string $rute,
        string $loginYangBenar,
        string $konteks
    ): void {
        // 1. Response HARUS berupa redirect (302)
        $response->assertRedirect();

        $location = $response->headers->get('Location');
        $this->assertNotNull(
            $location,
            "Response harus mengandung Location header untuk rute '{$rute}'. ({$konteks})"
        );

        // 2. Redirect HARUS mengarah ke URL login guard yang tepat
        $this->assertStringContainsString(
            $loginYangBenar,
            $location,
            "Akses tanpa auth ke '{$rute}' harus redirect ke '{$loginYangBenar}', " .
            "tapi redirect ke: '{$location}'. ({$konteks})"
        );

        // 3. Redirect TIDAK BOLEH mengarah ke login guard lain
        foreach (self::LOGIN_URLS as $namaGuard => $loginGuardLain) {
            if ($loginGuardLain !== $loginYangBenar) {
                $this->assertStringNotContainsString(
                    $loginGuardLain,
                    $location,
                    "Akses tanpa auth ke '{$rute}' tidak boleh redirect ke login guard lain " .
                    "'{$loginGuardLain}'. ({$konteks})"
                );
            }
        }
    }

    // ─── Tests ────────────────────────────────────────────────────────────────

    /**
     * Property: Rute admin tanpa auth SELALU redirect ke /admin/login.
     *
     * Strategi: gunakan Generator\elements() untuk mengiterasi semua rute admin.
     * Tidak ada sesi aktif — request dibuat tanpa actingAs().
     * Verifikasi: setiap rute admin menghasilkan redirect 302 ke /admin/login,
     * bukan ke /guru/login atau /siswa/login.
     *
     * **Validates: Requirements 6.3**
     */
    public function test_property_rute_admin_tanpa_auth_selalu_redirect_ke_admin_login(): void
    {
        $ruteAdmin = [
            '/admin/dashboard',
            '/admin/manajemen-akun',
            '/admin/manajemen-akun/admin',
            '/admin/profil',
        ];

        $this->limitTo(50)->forAll(
            Generators::elements(...$ruteAdmin)
        )->then(function (string $rute) {
            // Tidak ada actingAs() — request sebagai pengguna yang tidak terotentikasi
            $response = $this->get($rute);

            $this->assertRedirectKeLoginGuardYangBenar(
                $response,
                $rute,
                '/admin/login',
                "rute_admin={$rute}"
            );
        });
    }

    /**
     * Property: Rute guru tanpa auth SELALU redirect ke /guru/login.
     *
     * Strategi: iterasi semua rute guru tanpa sesi aktif apapun.
     * Verifikasi: redirect ke /guru/login, tidak ke /admin/login atau /siswa/login.
     *
     * **Validates: Requirements 6.3**
     */
    public function test_property_rute_guru_tanpa_auth_selalu_redirect_ke_guru_login(): void
    {
        $ruteGuru = [
            '/guru/dashboard',
            '/guru/profil',
        ];

        $this->limitTo(50)->forAll(
            Generators::elements(...$ruteGuru)
        )->then(function (string $rute) {
            $response = $this->get($rute);

            $this->assertRedirectKeLoginGuardYangBenar(
                $response,
                $rute,
                '/guru/login',
                "rute_guru={$rute}"
            );
        });
    }

    /**
     * Property: Rute siswa tanpa auth SELALU redirect ke /siswa/login.
     *
     * Strategi: iterasi semua rute siswa tanpa sesi aktif apapun.
     * Verifikasi: redirect ke /siswa/login, tidak ke /admin/login atau /guru/login.
     *
     * **Validates: Requirements 6.3**
     */
    public function test_property_rute_siswa_tanpa_auth_selalu_redirect_ke_siswa_login(): void
    {
        $ruteSiswa = [
            '/siswa/dashboard',
            '/siswa/profil',
        ];

        $this->limitTo(50)->forAll(
            Generators::elements(...$ruteSiswa)
        )->then(function (string $rute) {
            $response = $this->get($rute);

            $this->assertRedirectKeLoginGuardYangBenar(
                $response,
                $rute,
                '/siswa/login',
                "rute_siswa={$rute}"
            );
        });
    }

    /**
     * Property komprehensif: Semua rute terlindungi dari ketiga guard,
     * diakses tanpa autentikasi, SELALU redirect ke login guard yang tepat.
     *
     * Strategi: gunakan Generator\elements() atas seluruh daftar pasangan
     * (rute → login_guard) sehingga setiap iterasi Eris mengambil satu pasang
     * secara acak. Ini memverifikasi property berlaku untuk keseluruhan set rute
     * tanpa memandang guard mana yang bertanggung jawab.
     *
     * **Validates: Requirements 6.3**
     */
    public function test_property_semua_rute_terlindungi_tanpa_auth_redirect_ke_login_guard_yang_tepat(): void
    {
        // Bangun array pasangan [rute, loginYangBenar] dari konstanta peta
        $pasanganRuteLogin = array_map(
            fn(string $rute, string $login) => [$rute, $login],
            array_keys(self::PETA_RUTE_KE_LOGIN),
            array_values(self::PETA_RUTE_KE_LOGIN)
        );

        $this->limitTo(100)->forAll(
            // Pilih satu pasang [rute, loginYangBenar] secara acak per iterasi
            Generators::elements(...$pasanganRuteLogin)
        )->then(function (array $pasangan) {
            [$rute, $loginYangBenar] = $pasangan;

            // Pastikan tidak ada sesi aktif dengan memulai session bersih
            $this->flushSession();

            // Akses rute tanpa autentikasi — tidak ada actingAs()
            $response = $this->get($rute);

            $this->assertRedirectKeLoginGuardYangBenar(
                $response,
                $rute,
                $loginYangBenar,
                "komprehensif: rute={$rute}, login_yg_benar={$loginYangBenar}"
            );
        });
    }

    /**
     * Property: Status HTTP response untuk rute terlindungi tanpa auth
     * SELALU 302 (Found/Redirect) — tidak pernah 200, 403, 404, atau 401.
     *
     * Ini memverifikasi bahwa middleware AuthenticateAdmin/Guru/Siswa menggunakan
     * redirect, bukan mengembalikan error HTTP langsung.
     *
     * **Validates: Requirements 6.3**
     */
    public function test_property_status_redirect_selalu_302_untuk_semua_rute_tanpa_auth(): void
    {
        $semuaRute = array_keys(self::PETA_RUTE_KE_LOGIN);

        $this->limitTo(100)->forAll(
            Generators::elements(...$semuaRute)
        )->then(function (string $rute) {
            $this->flushSession();

            $response = $this->get($rute);

            // Status HTTP HARUS 302 — bukan 200, 401, atau 403
            $statusCode = $response->getStatusCode();
            $this->assertSame(
                302,
                $statusCode,
                "Akses tanpa auth ke '{$rute}' harus menghasilkan HTTP 302, " .
                "tapi diterima HTTP {$statusCode}."
            );
        });
    }

    /**
     * Property: Redirect dari rute terlindungi tanpa auth TIDAK PERNAH
     * mengarah ke dashboard (hanya ke halaman login).
     *
     * Ini memverifikasi bahwa pengguna yang belum auth tidak "diselundupkan"
     * ke dalam area terproteksi — selalu diarahkan ke login, bukan dashboard.
     *
     * **Validates: Requirements 6.3**
     */
    public function test_property_redirect_tanpa_auth_tidak_pernah_ke_dashboard(): void
    {
        $semuaRute = array_keys(self::PETA_RUTE_KE_LOGIN);
        $dashboardUrls = ['/admin/dashboard', '/guru/dashboard', '/siswa/dashboard'];

        $this->limitTo(100)->forAll(
            Generators::elements(...$semuaRute)
        )->then(function (string $rute) use ($dashboardUrls) {
            $this->flushSession();

            $response = $this->get($rute);

            $location = $response->headers->get('Location') ?? '';

            foreach ($dashboardUrls as $dashboard) {
                $this->assertStringNotContainsString(
                    $dashboard,
                    $location,
                    "Akses tanpa auth ke '{$rute}' tidak boleh redirect ke dashboard " .
                    "'{$dashboard}'. Location: '{$location}'."
                );
            }
        });
    }
}
