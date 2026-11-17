<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
use Carbon\Carbon;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 14.7 — IsolasiGuardPropertyTest
 *
 * Property 7: Isolasi Guard — Akses Lintas Guard Selalu Ditolak
 *
 * Untuk semua kombinasi (pengguna terautentikasi pada guard X, rute yang
 * dilindungi guard Y di mana X ≠ Y), akses HARUS selalu ditolak dan
 * pengguna diarahkan ke dashboard guard X yang sedang aktif.
 *
 * Enam kombinasi lintas guard yang diuji:
 *   - Admin  → rute Guru  (auth.guru middleware)   → redirect ke admin.dashboard
 *   - Admin  → rute Siswa (auth.siswa middleware)  → redirect ke admin.dashboard
 *   - Guru   → rute Admin (auth.admin middleware)  → redirect ke guru.dashboard
 *   - Guru   → rute Siswa (auth.siswa middleware)  → redirect ke guru.dashboard
 *   - Siswa  → rute Admin (auth.admin middleware)  → redirect ke siswa.dashboard
 *   - Siswa  → rute Guru  (auth.guru middleware)   → redirect ke siswa.dashboard
 *
 * **Validates: Requirements 6.2**
 */
class IsolasiGuardPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    // ─── Dashboard & Login URL ────────────────────────────────────────────────

    private const DASHBOARD_ADMIN = '/admin/dashboard';
    private const DASHBOARD_GURU  = '/guru/dashboard';
    private const DASHBOARD_SISWA = '/siswa/dashboard';

    // ─── Rute terlindungi per guard (pilihan rute yang akan dijadikan target lintas guard) ──

    /**
     * Rute-rute yang dilindungi middleware auth.admin.
     * @var string[]
     */
    private const RUTE_ADMIN = [
        '/admin/dashboard',
        '/admin/manajemen-akun',
        '/admin/manajemen-akun/admin',
        '/admin/profil',
    ];

    /**
     * Rute-rute yang dilindungi middleware auth.guru.
     * @var string[]
     */
    private const RUTE_GURU = [
        '/guru/dashboard',
        '/guru/profil',
    ];

    /**
     * Rute-rute yang dilindungi middleware auth.siswa.
     * @var string[]
     */
    private const RUTE_SISWA = [
        '/siswa/dashboard',
        '/siswa/profil',
    ];

    // ─── Factory Helpers ─────────────────────────────────────────────────────

    /**
     * Buat Admin aktif dengan username unik dari seed integer.
     */
    private function buatAdmin(int $seed): Admin
    {
        $suffix = str_pad((string) abs($seed % 999_999), 6, '0', STR_PAD_LEFT);

        return Admin::factory()->create([
            'username'     => 'pbt_ig_admin_' . $suffix,
            'password'     => Hash::make('PBT_ig_pw_' . $suffix),
            'status_aktif' => true,
        ]);
    }

    /**
     * Buat Guru aktif dengan NIY unik dari seed integer.
     */
    private function buatGuru(int $seed): Guru
    {
        $niy = '21' . str_pad((string) abs($seed % 99_999_999), 8, '0', STR_PAD_LEFT);

        return Guru::factory()->create([
            'niy'           => $niy,
            'tanggal_lahir' => '1985-06-15',
            'status_aktif'  => true,
        ]);
    }

    /**
     * Buat Siswa aktif dengan NIS unik dari seed integer.
     */
    private function buatSiswa(int $seed): Siswa
    {
        $nis = '31' . str_pad((string) abs($seed % 99_999_999), 8, '0', STR_PAD_LEFT);

        return Siswa::factory()->create([
            'nis'           => $nis,
            'tanggal_lahir' => '2008-03-20',
            'status_aktif'  => true,
        ]);
    }

    // ─── Assertion Helper ────────────────────────────────────────────────────

    /**
     * Verifikasi bahwa response adalah redirect ke dashboard guard X (guard yang aktif),
     * bukan ke login guard Y (guard dari rute yang dicoba diakses).
     *
     * @param  \Illuminate\Testing\TestResponse  $response
     * @param  string  $guardAktif      Guard yang sedang terautentikasi (X)
     * @param  string  $dashboardAktif  URL dashboard guard X (tujuan redirect yang benar)
     * @param  string  $ruteDituju      URL rute yang dicoba diakses (milik guard Y)
     * @param  string  $konteks         Keterangan untuk pesan kegagalan assertion
     */
    private function assertIsolasiGuard(
        \Illuminate\Testing\TestResponse $response,
        string $guardAktif,
        string $dashboardAktif,
        string $ruteDituju,
        string $konteks
    ): void {
        // 1. Response HARUS berupa redirect (ditolak, bukan HTTP 200)
        $response->assertRedirect(
            null,
            "Akses lintas guard harus menghasilkan redirect, bukan HTTP 200. ({$konteks})"
        );

        $location = $response->headers->get('Location', '');

        // 2. Redirect HARUS ke dashboard guard yang sedang aktif (guard X)
        $this->assertStringContainsString(
            $dashboardAktif,
            $location,
            "Akses lintas guard HARUS redirect ke dashboard guard aktif ({$guardAktif}). " .
            "Rute dituju: {$ruteDituju}. Location: {$location}. ({$konteks})"
        );

        // 3. TIDAK boleh redirect ke halaman login guard Y (rute yang dicoba diakses)
        //    Ini memastikan sistem tidak "memperlihatkan" form login guard lain.
        $loginGuardLain = $this->loginUrlDari($ruteDituju);
        if ($loginGuardLain !== null) {
            $this->assertStringNotContainsString(
                $loginGuardLain,
                $location,
                "Akses lintas guard TIDAK boleh redirect ke login guard lain ({$loginGuardLain}). " .
                "({$konteks})"
            );
        }
    }

    /**
     * Tentukan URL login guard dari prefix rute.
     * Mengembalikan null jika tidak bisa ditentukan.
     */
    private function loginUrlDari(string $rute): ?string
    {
        if (str_starts_with($rute, '/admin/')) {
            return '/admin/login';
        }
        if (str_starts_with($rute, '/guru/')) {
            return '/guru/login';
        }
        if (str_starts_with($rute, '/siswa/')) {
            return '/siswa/login';
        }
        return null;
    }

    // ─── Tests ────────────────────────────────────────────────────────────────

    /**
     * Property: Admin terautentikasi mengakses SEMUA rute Guru → selalu redirect
     * ke /admin/dashboard, tidak pernah ke /guru/login atau /guru/dashboard.
     *
     * Kombinasi: Admin (guard X) → rute Guru (guard Y)
     * Guard aktif: admin → dashboard yang benar: /admin/dashboard
     *
     * Strategi generator:
     * - Seed integer untuk membuat akun admin unik per iterasi.
     * - Index rute (0 atau 1) untuk memilih rute Guru yang akan dicoba.
     *
     * **Validates: Requirements 6.2**
     */
    public function test_property_admin_akses_rute_guru_selalu_redirect_ke_admin_dashboard(): void
    {
        $ruteGuru = self::RUTE_GURU;

        $this->limitTo(20)->forAll(
            Generators::choose(1, 499_999),
            Generators::elements(...$ruteGuru)
        )->then(function (int $seed, string $rute) {
            $admin = $this->buatAdmin($seed);

            // Admin terautentikasi mencoba mengakses rute Guru
            $response = $this
                ->actingAs($admin, 'admin')
                ->withSession(['last_activity_guru' => Carbon::now()]) // bukan sesi guru aktif
                ->get($rute);

            $konteks = "seed={$seed}, admin→guru, rute={$rute}";
            $this->assertIsolasiGuard(
                $response,
                'admin',
                self::DASHBOARD_ADMIN,
                $rute,
                $konteks
            );
        });
    }

    /**
     * Property: Admin terautentikasi mengakses SEMUA rute Siswa → selalu redirect
     * ke /admin/dashboard, tidak pernah ke /siswa/login atau /siswa/dashboard.
     *
     * Kombinasi: Admin (guard X) → rute Siswa (guard Y)
     *
     * **Validates: Requirements 6.2**
     */
    public function test_property_admin_akses_rute_siswa_selalu_redirect_ke_admin_dashboard(): void
    {
        $ruteSiswa = self::RUTE_SISWA;

        $this->limitTo(20)->forAll(
            Generators::choose(1, 499_999),
            Generators::elements(...$ruteSiswa)
        )->then(function (int $seed, string $rute) {
            $admin = $this->buatAdmin($seed);

            $response = $this
                ->actingAs($admin, 'admin')
                ->get($rute);

            $konteks = "seed={$seed}, admin→siswa, rute={$rute}";
            $this->assertIsolasiGuard(
                $response,
                'admin',
                self::DASHBOARD_ADMIN,
                $rute,
                $konteks
            );
        });
    }

    /**
     * Property: Guru terautentikasi mengakses SEMUA rute Admin → selalu redirect
     * ke /guru/dashboard, tidak pernah ke /admin/login atau /admin/dashboard.
     *
     * Kombinasi: Guru (guard X) → rute Admin (guard Y)
     * Guard aktif: guru → dashboard yang benar: /guru/dashboard
     *
     * Catatan: Rute Guru juga memiliki middleware session.timeout.
     * Kita sertakan last_activity_guru agar tidak terinterferensi timeout.
     *
     * **Validates: Requirements 6.2**
     */
    public function test_property_guru_akses_rute_admin_selalu_redirect_ke_guru_dashboard(): void
    {
        $ruteAdmin = self::RUTE_ADMIN;

        $this->limitTo(20)->forAll(
            Generators::choose(1, 499_999),
            Generators::elements(...$ruteAdmin)
        )->then(function (int $seed, string $rute) {
            $guru = $this->buatGuru($seed);

            $response = $this
                ->actingAs($guru, 'guru')
                ->withSession(['last_activity_guru' => Carbon::now()])
                ->get($rute);

            $konteks = "seed={$seed}, guru→admin, rute={$rute}";
            $this->assertIsolasiGuard(
                $response,
                'guru',
                self::DASHBOARD_GURU,
                $rute,
                $konteks
            );
        });
    }

    /**
     * Property: Guru terautentikasi mengakses SEMUA rute Siswa → selalu redirect
     * ke /guru/dashboard, tidak pernah ke /siswa/login atau /siswa/dashboard.
     *
     * Kombinasi: Guru (guard X) → rute Siswa (guard Y)
     *
     * **Validates: Requirements 6.2**
     */
    public function test_property_guru_akses_rute_siswa_selalu_redirect_ke_guru_dashboard(): void
    {
        $ruteSiswa = self::RUTE_SISWA;

        $this->limitTo(20)->forAll(
            Generators::choose(1, 499_999),
            Generators::elements(...$ruteSiswa)
        )->then(function (int $seed, string $rute) {
            $guru = $this->buatGuru($seed);

            $response = $this
                ->actingAs($guru, 'guru')
                ->withSession(['last_activity_guru' => Carbon::now()])
                ->get($rute);

            $konteks = "seed={$seed}, guru→siswa, rute={$rute}";
            $this->assertIsolasiGuard(
                $response,
                'guru',
                self::DASHBOARD_GURU,
                $rute,
                $konteks
            );
        });
    }

    /**
     * Property: Siswa terautentikasi mengakses SEMUA rute Admin → selalu redirect
     * ke /siswa/dashboard, tidak pernah ke /admin/login atau /admin/dashboard.
     *
     * Kombinasi: Siswa (guard X) → rute Admin (guard Y)
     * Guard aktif: siswa → dashboard yang benar: /siswa/dashboard
     *
     * **Validates: Requirements 6.2**
     */
    public function test_property_siswa_akses_rute_admin_selalu_redirect_ke_siswa_dashboard(): void
    {
        $ruteAdmin = self::RUTE_ADMIN;

        $this->limitTo(20)->forAll(
            Generators::choose(1, 499_999),
            Generators::elements(...$ruteAdmin)
        )->then(function (int $seed, string $rute) {
            $siswa = $this->buatSiswa($seed);

            $response = $this
                ->actingAs($siswa, 'siswa')
                ->withSession(['last_activity_siswa' => Carbon::now()])
                ->get($rute);

            $konteks = "seed={$seed}, siswa→admin, rute={$rute}";
            $this->assertIsolasiGuard(
                $response,
                'siswa',
                self::DASHBOARD_SISWA,
                $rute,
                $konteks
            );
        });
    }

    /**
     * Property: Siswa terautentikasi mengakses SEMUA rute Guru → selalu redirect
     * ke /siswa/dashboard, tidak pernah ke /guru/login atau /guru/dashboard.
     *
     * Kombinasi: Siswa (guard X) → rute Guru (guard Y)
     *
     * **Validates: Requirements 6.2**
     */
    public function test_property_siswa_akses_rute_guru_selalu_redirect_ke_siswa_dashboard(): void
    {
        $ruteGuru = self::RUTE_GURU;

        $this->limitTo(20)->forAll(
            Generators::choose(1, 499_999),
            Generators::elements(...$ruteGuru)
        )->then(function (int $seed, string $rute) {
            $siswa = $this->buatSiswa($seed);

            $response = $this
                ->actingAs($siswa, 'siswa')
                ->withSession(['last_activity_siswa' => Carbon::now()])
                ->get($rute);

            $konteks = "seed={$seed}, siswa→guru, rute={$rute}";
            $this->assertIsolasiGuard(
                $response,
                'siswa',
                self::DASHBOARD_SISWA,
                $rute,
                $konteks
            );
        });
    }

    /**
     * Property: Semua 6 kombinasi lintas guard diuji sekaligus dalam satu iterasi.
     *
     * Verifikasi komprehensif bahwa isolasi guard bekerja secara simetris —
     * tidak ada kombinasi yang bocor ke arah manapun.
     *
     * Per seed: buat satu akun per guard, lalu uji semua 6 arah lintas guard
     * terhadap satu rute representatif dari masing-masing guard (dashboard).
     *
     * **Validates: Requirements 6.2**
     */
    public function test_property_semua_6_kombinasi_lintas_guard_selalu_ditolak(): void
    {
        $this->limitTo(20)->forAll(
            Generators::choose(500_000, 999_999) // range berbeda agar seed unik
        )->then(function (int $seed) {
            $admin = $this->buatAdmin($seed);
            $guru  = $this->buatGuru($seed);
            $siswa = $this->buatSiswa($seed);

            // ── 1. Admin → rute Guru (dashboard) ────────────────────────────
            $r1 = $this->actingAs($admin, 'admin')->get(self::DASHBOARD_GURU);
            $this->assertIsolasiGuard($r1, 'admin', self::DASHBOARD_ADMIN, self::DASHBOARD_GURU, "admin→guru, seed={$seed}");

            // ── 2. Admin → rute Siswa (dashboard) ───────────────────────────
            $r2 = $this->actingAs($admin, 'admin')->get(self::DASHBOARD_SISWA);
            $this->assertIsolasiGuard($r2, 'admin', self::DASHBOARD_ADMIN, self::DASHBOARD_SISWA, "admin→siswa, seed={$seed}");

            // ── 3. Guru → rute Admin (dashboard) ────────────────────────────
            $r3 = $this->actingAs($guru, 'guru')
                ->withSession(['last_activity_guru' => Carbon::now()])
                ->get(self::DASHBOARD_ADMIN);
            $this->assertIsolasiGuard($r3, 'guru', self::DASHBOARD_GURU, self::DASHBOARD_ADMIN, "guru→admin, seed={$seed}");

            // ── 4. Guru → rute Siswa (dashboard) ────────────────────────────
            $r4 = $this->actingAs($guru, 'guru')
                ->withSession(['last_activity_guru' => Carbon::now()])
                ->get(self::DASHBOARD_SISWA);
            $this->assertIsolasiGuard($r4, 'guru', self::DASHBOARD_GURU, self::DASHBOARD_SISWA, "guru→siswa, seed={$seed}");

            // ── 5. Siswa → rute Admin (dashboard) ───────────────────────────
            $r5 = $this->actingAs($siswa, 'siswa')
                ->withSession(['last_activity_siswa' => Carbon::now()])
                ->get(self::DASHBOARD_ADMIN);
            $this->assertIsolasiGuard($r5, 'siswa', self::DASHBOARD_SISWA, self::DASHBOARD_ADMIN, "siswa→admin, seed={$seed}");

            // ── 6. Siswa → rute Guru (dashboard) ────────────────────────────
            $r6 = $this->actingAs($siswa, 'siswa')
                ->withSession(['last_activity_siswa' => Carbon::now()])
                ->get(self::DASHBOARD_GURU);
            $this->assertIsolasiGuard($r6, 'siswa', self::DASHBOARD_SISWA, self::DASHBOARD_GURU, "siswa→guru, seed={$seed}");
        });
    }

    /**
     * Property: Isolasi guard KONSISTEN di semua rute protected (bukan hanya dashboard).
     *
     * Menguji bahwa isolasi berlaku di SETIAP rute terlindungi, bukan hanya
     * rute dashboard — misal admin.profil, admin.manajemen-akun, guru.profil, dst.
     *
     * Strategi: generate seed untuk akun + index untuk pilih rute lintas guard secara random.
     *
     * **Validates: Requirements 6.2**
     */
    public function test_property_isolasi_berlaku_di_semua_rute_protected(): void
    {
        // Peta: guard aktif → guard target → daftar rute target
        $kombinasi = [
            ['aktif' => 'admin', 'target' => 'guru',  'rute' => self::RUTE_GURU],
            ['aktif' => 'admin', 'target' => 'siswa', 'rute' => self::RUTE_SISWA],
            ['aktif' => 'guru',  'target' => 'admin', 'rute' => self::RUTE_ADMIN],
            ['aktif' => 'guru',  'target' => 'siswa', 'rute' => self::RUTE_SISWA],
            ['aktif' => 'siswa', 'target' => 'admin', 'rute' => self::RUTE_ADMIN],
            ['aktif' => 'siswa', 'target' => 'guru',  'rute' => self::RUTE_GURU],
        ];

        $dashboard = [
            'admin' => self::DASHBOARD_ADMIN,
            'guru'  => self::DASHBOARD_GURU,
            'siswa' => self::DASHBOARD_SISWA,
        ];

        $this->limitTo(15)->forAll(
            Generators::choose(1_000_000, 1_499_999),
            // Index kombinasi (0–5)
            Generators::choose(0, 5)
        )->then(function (int $seed, int $idxKombinasi) use ($kombinasi, $dashboard) {
            $komb      = $kombinasi[$idxKombinasi];
            $guardAktif = $komb['aktif'];
            $rute      = $komb['rute'][array_rand($komb['rute'])];

            // Buat user sesuai guard aktif
            $user = match ($guardAktif) {
                'admin' => $this->buatAdmin($seed),
                'guru'  => $this->buatGuru($seed),
                'siswa' => $this->buatSiswa($seed),
            };

            // Bangun request dengan sesi yang sesuai
            $sessionData = [];
            if ($guardAktif === 'guru') {
                $sessionData['last_activity_guru'] = Carbon::now();
            } elseif ($guardAktif === 'siswa') {
                $sessionData['last_activity_siswa'] = Carbon::now();
            }

            $response = $this
                ->actingAs($user, $guardAktif)
                ->withSession($sessionData)
                ->get($rute);

            $konteks = "seed={$seed}, {$guardAktif}→{$komb['target']}, rute={$rute}";
            $this->assertIsolasiGuard(
                $response,
                $guardAktif,
                $dashboard[$guardAktif],
                $rute,
                $konteks
            );
        });
    }
}
