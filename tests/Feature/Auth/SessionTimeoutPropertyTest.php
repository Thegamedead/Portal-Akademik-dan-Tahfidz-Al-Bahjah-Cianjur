<?php

namespace Tests\Feature\Auth;

use App\Models\Guru;
use App\Models\Siswa;
use Carbon\Carbon;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * 14.5 — SessionTimeoutPropertyTest
 *
 * Property 5: Sesi Timeout Berakhir dan Redirect ke Guard yang Tepat
 *
 * Untuk semua sesi guard guru dan siswa dengan waktu last_activity melebihi
 * durasi timeout yang dikonfigurasi, request berikutnya HARUS selalu
 * mengakhiri sesi dan mengarahkan ke halaman login guard yang sesuai
 * (bukan halaman login guard lain).
 *
 * **Validates: Requirements 5.1, 5.3**
 */
class SessionTimeoutPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    /**
     * Timeout default guru (menit) — diambil dari config saat setUp.
     */
    private int $timeoutGuru;

    /**
     * Timeout default siswa (menit) — diambil dari config saat setUp.
     */
    private int $timeoutSiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->timeoutGuru  = (int) config('session_timeout.guru', 120);
        $this->timeoutSiswa = (int) config('session_timeout.siswa', 60);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Buat Guru aktif dengan NIY unik berdasarkan seed.
     */
    private function buatGuru(int $seed): Guru
    {
        $niy = '80' . str_pad((string) ($seed % 99_999_999), 8, '0', STR_PAD_LEFT);

        return Guru::factory()->create([
            'niy'           => $niy,
            'tanggal_lahir' => '1985-03-15',
            'status_aktif'  => true,
        ]);
    }

    /**
     * Buat Siswa aktif dengan NIS unik berdasarkan seed.
     */
    private function buatSiswa(int $seed): Siswa
    {
        $nis = '90' . str_pad((string) ($seed % 99_999_999), 8, '0', STR_PAD_LEFT);

        return Siswa::factory()->create([
            'nis'           => $nis,
            'tanggal_lahir' => '2010-07-20',
            'status_aktif'  => true,
        ]);
    }

    /**
     * Lakukan request GET ke route protected dengan sesi yang sudah timeout.
     *
     * Cara kerjanya:
     * - `actingAs($user, $guard)` → menyuntikkan autentikasi guard ke test session
     * - `withSession([...])` → menyuntikkan last_activity_xxx ke session yang sama
     *
     * Middleware SessionTimeout akan membaca last_activity dari session ini dan
     * mendeteksi timeout, lalu redirect SEBELUM view dirender — tidak ada
     * overhead render Blade berat.
     *
     * @param  \App\Models\Guru|\App\Models\Siswa  $user
     * @param  string  $guard        'guru' atau 'siswa'
     * @param  int     $menitLampau  Berapa menit yang lalu last_activity di-set
     */
    private function requestDenganSesiTimeout(
        $user,
        string $guard,
        int $menitLampau
    ): \Illuminate\Testing\TestResponse {
        $lastActivity = Carbon::now()->subMinutes($menitLampau)->toIso8601String();

        return $this
            ->actingAs($user, $guard)
            ->withSession(["last_activity_{$guard}" => Carbon::now()->subMinutes($menitLampau)])
            ->get(route("{$guard}.dashboard"));
    }

    /**
     * Lakukan request GET ke route protected dengan sesi yang belum timeout.
     *
     * @param  \App\Models\Guru|\App\Models\Siswa  $user
     * @param  string  $guard        'guru' atau 'siswa'
     * @param  int     $menitLampau  Berapa menit yang lalu last_activity di-set (< timeout)
     */
    private function requestDenganSesiValid(
        $user,
        string $guard,
        int $menitLampau
    ): \Illuminate\Testing\TestResponse {
        return $this
            ->actingAs($user, $guard)
            ->withSession(["last_activity_{$guard}" => Carbon::now()->subMinutes($menitLampau)])
            ->get(route("{$guard}.dashboard"));
    }

    /**
     * Verifikasi bahwa response adalah redirect ke login guard yang sesuai
     * (bukan guard lain).
     *
     * @param  \Illuminate\Testing\TestResponse  $response
     * @param  string  $guard       'guru' atau 'siswa'
     * @param  string  $guardLain   Guard yang bukan $guard
     * @param  string  $konteks     Untuk pesan kegagalan assertion
     */
    private function assertTimeoutRedirectBenar(
        \Illuminate\Testing\TestResponse $response,
        string $guard,
        string $guardLain,
        string $konteks
    ): void {
        // 1. Harus redirect (bukan HTTP 200 / tetap di dashboard)
        $response->assertRedirect();

        // 2. Redirect HARUS ke login guard yang sesuai
        $response->assertRedirect(route("{$guard}.login"));

        // 3. TIDAK boleh redirect ke login guard LAIN
        $this->assertNotEquals(
            route("{$guardLain}.login"),
            $response->headers->get('Location'),
            "Timeout {$guard} TIDAK boleh redirect ke login {$guardLain}. ({$konteks})"
        );
    }

    // ─── Tests ────────────────────────────────────────────────────────────────

    /**
     * Property: Sesi Guru dengan last_activity melampaui timeout SELALU redirect
     * ke guru.login (bukan siswa.login atau tempat lain).
     *
     * Strategi generator:
     * - Seed untuk membuat NIY unik per iterasi.
     * - Delta menit di atas timeout: varies 1–1000 menit, membuktikan property
     *   berlaku untuk semua besaran elapsed time di atas threshold.
     * - limitTo(10): cukup untuk menemukan counterexample; lebih hemat memori.
     *
     * **Validates: Requirements 5.1, 5.3**
     */
    public function test_property_guru_timeout_selalu_redirect_ke_guru_login(): void
    {
        $timeoutGuru = $this->timeoutGuru;

        $this->limitTo(10)->forAll(
            // Seed untuk membuat identitas unik per iterasi
            Generators::choose(1, 9_999_999),
            // Jumlah menit lampau yang MELEBIHI timeout (timeout+1 sampai timeout+1000)
            Generators::map(
                fn(int $delta) => $timeoutGuru + $delta,
                Generators::choose(1, 1000)
            )
        )->then(function (int $seed, int $menitLampau) {
            $guru = $this->buatGuru($seed);

            $response = $this->requestDenganSesiTimeout($guru, 'guru', $menitLampau);

            $konteks = "seed={$seed}, menitLampau={$menitLampau}";
            $this->assertTimeoutRedirectBenar($response, 'guru', 'siswa', $konteks);

            $guru->forceDelete();
        });
    }

    /**
     * Property: Sesi Siswa dengan last_activity melampaui timeout SELALU redirect
     * ke siswa.login (bukan guru.login atau tempat lain).
     *
     * **Validates: Requirements 5.1, 5.3**
     */
    public function test_property_siswa_timeout_selalu_redirect_ke_siswa_login(): void
    {
        $timeoutSiswa = $this->timeoutSiswa;

        $this->limitTo(10)->forAll(
            Generators::choose(1, 9_999_999),
            Generators::map(
                fn(int $delta) => $timeoutSiswa + $delta,
                Generators::choose(1, 1000)
            )
        )->then(function (int $seed, int $menitLampau) {
            $siswa = $this->buatSiswa($seed);

            $response = $this->requestDenganSesiTimeout($siswa, 'siswa', $menitLampau);

            $konteks = "seed={$seed}, menitLampau={$menitLampau}";
            $this->assertTimeoutRedirectBenar($response, 'siswa', 'guru', $konteks);

            $siswa->forceDelete();
        });
    }

    /**
     * Property: Sesi Guru yang BELUM melampaui timeout TIDAK di-redirect ke login.
     *
     * Verifikasi sisi negatif: request dari sesi yang masih valid HARUS dilayani
     * (bukan salah logout). Ini memastikan property 5 tidak terlalu agresif.
     *
     * **Validates: Requirements 5.1**
     */
    public function test_property_guru_sesi_belum_timeout_tidak_di_redirect(): void
    {
        $timeoutGuru = $this->timeoutGuru;

        $this->limitTo(10)->forAll(
            Generators::choose(1, 9_999_999),
            // Menit lampau yang BELUM melampaui timeout (1 hingga timeout-1)
            Generators::choose(1, max(1, $timeoutGuru - 1))
        )->then(function (int $seed, int $menitLampau) use ($timeoutGuru) {
            // Pastikan nilai yang dihasilkan benar-benar < timeout
            if ($menitLampau >= $timeoutGuru) {
                return;
            }

            $guru = $this->buatGuru($seed);

            $response = $this->requestDenganSesiValid($guru, 'guru', $menitLampau);

            $konteks = "guard=guru, seed={$seed}, menitLampau={$menitLampau}";

            // Tidak boleh redirect ke guru.login (sesi masih valid)
            $location = $response->headers->get('Location');
            if ($location !== null) {
                $this->assertNotEquals(
                    route('guru.login'),
                    $location,
                    "Sesi guru BELUM timeout tidak boleh redirect ke guru.login. ({$konteks})"
                );
            }

            // Harus HTTP 200 (view dirender) atau redirect ke dashboard (bukan login)
            $statusCode = $response->getStatusCode();
            $this->assertNotEquals(
                302,
                $statusCode,
                "Sesi guru yang belum timeout seharusnya tidak redirect sama sekali. ({$konteks})"
            );

            $guru->forceDelete();
        });
    }

    /**
     * Property: Sesi Siswa yang BELUM melampaui timeout TIDAK di-redirect ke login.
     *
     * **Validates: Requirements 5.1**
     */
    public function test_property_siswa_sesi_belum_timeout_tidak_di_redirect(): void
    {
        $timeoutSiswa = $this->timeoutSiswa;

        $this->limitTo(10)->forAll(
            Generators::choose(1, 9_999_999),
            Generators::choose(1, max(1, $timeoutSiswa - 1))
        )->then(function (int $seed, int $menitLampau) use ($timeoutSiswa) {
            if ($menitLampau >= $timeoutSiswa) {
                return;
            }

            $siswa = $this->buatSiswa($seed);

            $response = $this->requestDenganSesiValid($siswa, 'siswa', $menitLampau);

            $konteks = "guard=siswa, seed={$seed}, menitLampau={$menitLampau}";

            $location = $response->headers->get('Location');
            if ($location !== null) {
                $this->assertNotEquals(
                    route('siswa.login'),
                    $location,
                    "Sesi siswa BELUM timeout tidak boleh redirect ke siswa.login. ({$konteks})"
                );
            }

            $statusCode = $response->getStatusCode();
            $this->assertNotEquals(
                302,
                $statusCode,
                "Sesi siswa yang belum timeout seharusnya tidak redirect sama sekali. ({$konteks})"
            );

            $siswa->forceDelete();
        });
    }

    /**
     * Property: Timeout sesi guru TIDAK mempengaruhi guard siswa — sesi siswa
     * yang belum timeout tetap valid setelah guru timeout.
     *
     * Pengujian isolasi guard: middleware hanya me-logout guard yang timeout,
     * tidak guard lain.
     *
     * **Validates: Requirements 5.1, 5.3**
     */
    public function test_property_timeout_guru_tidak_mempengaruhi_guard_siswa(): void
    {
        $timeoutGuru = $this->timeoutGuru;

        $this->limitTo(10)->forAll(
            Generators::choose(1, 9_999_999),
            // Menit lampau guru: melebihi timeout guru
            Generators::map(
                fn(int $delta) => $timeoutGuru + $delta,
                Generators::choose(1, 500)
            )
        )->then(function (int $seed, int $menitGuruLampau) {
            $guru  = $this->buatGuru($seed);
            $siswa = $this->buatSiswa($seed);

            $konteks = "seed={$seed}, menitGuruLampau={$menitGuruLampau}";

            // ── Request guru dengan sesi timeout ──────────────────────────────
            $responseGuru = $this
                ->actingAs($guru, 'guru')
                ->withSession([
                    'last_activity_guru'  => Carbon::now()->subMinutes($menitGuruLampau),
                    // Sesi siswa tidak ada di request ini — ini murni request guru
                ])
                ->get(route('guru.dashboard'));

            // Timeout guru → redirect ke guru.login
            $responseGuru->assertRedirect(route('guru.login'));

            // Tidak boleh redirect ke siswa.login
            $this->assertNotEquals(
                route('siswa.login'),
                $responseGuru->headers->get('Location'),
                "Timeout guru tidak boleh redirect ke siswa.login. ({$konteks})"
            );

            // ── Verifikasi: siswa masih bisa login normal ─────────────────────
            // Request siswa dengan sesi valid (1 menit lalu) tidak terpengaruh
            $responseSiswa = $this
                ->actingAs($siswa, 'siswa')
                ->withSession([
                    'last_activity_siswa' => Carbon::now()->subMinutes(1),
                ])
                ->get(route('siswa.dashboard'));

            // Siswa TIDAK boleh di-redirect ke siswa.login (sesi masih valid)
            $locationSiswa = $responseSiswa->headers->get('Location');
            if ($locationSiswa !== null) {
                $this->assertNotEquals(
                    route('siswa.login'),
                    $locationSiswa,
                    "Sesi siswa yang valid tidak boleh di-redirect ke siswa.login. ({$konteks})"
                );
            }

            // Cleanup
            $guru->forceDelete();
            $siswa->forceDelete();
        });
    }

    /**
     * Property: Timeout tepat pada batas — last_activity TEPAT sama dengan
     * durasi timeout (diffInMinutes == timeout) HARUS juga dianggap expired.
     *
     * Memverifikasi kondisi batas dari operator >= di SessionTimeoutService.
     *
     * **Validates: Requirements 5.1**
     */
    public function test_property_timeout_tepat_pada_batas_dianggap_expired(): void
    {
        $this->limitTo(10)->forAll(
            Generators::choose(1, 9_999_999),
            // Guard yang diuji: guru atau siswa
            Generators::elements('guru', 'siswa')
        )->then(function (int $seed, string $guard) {
            $timeout   = (int) config("session_timeout.{$guard}");
            $guardLain = $guard === 'guru' ? 'siswa' : 'guru';

            $user = $guard === 'guru'
                ? $this->buatGuru($seed)
                : $this->buatSiswa($seed);

            $konteks = "guard={$guard}, seed={$seed}, batas tepat={$timeout} menit";

            // Set last_activity TEPAT pada batas (diffInMinutes == timeout → expired)
            $response = $this
                ->actingAs($user, $guard)
                ->withSession(["last_activity_{$guard}" => Carbon::now()->subMinutes($timeout)])
                ->get(route("{$guard}.dashboard"));

            // Harus redirect
            $response->assertRedirect();

            // Harus ke login guard yang sesuai
            $response->assertRedirect(route("{$guard}.login"));

            // Tidak boleh ke login guard lain
            $this->assertNotEquals(
                route("{$guardLain}.login"),
                $response->headers->get('Location'),
                "Timeout tepat batas: redirect ke {$guard}.login, bukan {$guardLain}.login. ({$konteks})"
            );

            $user->forceDelete();
        });
    }

    /**
     * Property: Dengan konfigurasi timeout yang berbeda-beda (diubah via Config::set),
     * sistem TETAP menggunakan nilai konfigurasi yang berlaku saat itu dan redirect
     * ke guard yang tepat.
     *
     * Memverifikasi bahwa SessionTimeoutService membaca dari config dinamis,
     * bukan nilai hardcode.
     *
     * **Validates: Requirements 5.1, 5.3**
     */
    public function test_property_timeout_mengikuti_konfigurasi_dinamis(): void
    {
        $originalTimeout = $this->timeoutGuru;

        $this->limitTo(10)->forAll(
            Generators::choose(1, 9_999_999),
            // Timeout kustom (1–30 menit)
            Generators::choose(1, 30),
            // Delta lampau di atas timeout kustom (1–60 menit)
            Generators::choose(1, 60)
        )->then(function (int $seed, int $timeoutKustom, int $deltaLampau) use ($originalTimeout) {
            $menitLampau = $timeoutKustom + $deltaLampau;

            // Override konfigurasi timeout guru secara dinamis
            Config::set('session_timeout.guru', $timeoutKustom);

            $guru = $this->buatGuru($seed);

            $konteks = "seed={$seed}, timeoutKustom={$timeoutKustom}, menitLampau={$menitLampau}";

            $response = $this
                ->actingAs($guru, 'guru')
                ->withSession(['last_activity_guru' => Carbon::now()->subMinutes($menitLampau)])
                ->get(route('guru.dashboard'));

            // Harus expired dan redirect ke guru.login
            $response->assertRedirect(route('guru.login'));

            // Tidak boleh ke siswa.login
            $this->assertNotEquals(
                route('siswa.login'),
                $response->headers->get('Location'),
                "Timeout kustom guru harus redirect ke guru.login, bukan siswa.login. ({$konteks})"
            );

            // Restore konfigurasi default
            Config::set('session_timeout.guru', $originalTimeout);

            $guru->forceDelete();
        });
    }
}
