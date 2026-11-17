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
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * 14.1 — LoginErrorGenerikPropertyTest
 *
 * Property 1: Pesan Error Login Selalu Generik
 *
 * Untuk 100 kombinasi (guard, identitas, kredensial) random yang tidak valid —
 * baik karena identitas tidak ditemukan maupun karena kredensial tidak cocok —
 * sistem HARUS selalu mengembalikan satu pesan error yang identik tanpa
 * membedakan penyebabnya.
 *
 * **Validates: Requirements 1.3, 2.4, 3.3**
 */
class LoginErrorGenerikPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    /**
     * Pesan error generik yang harus dikembalikan untuk semua kegagalan kredensial.
     */
    private const PESAN_ERROR_GENERIK = 'Kredensial tidak valid. Silakan coba lagi.';

    /**
     * IP dummy yang dipakai di semua tes agar rate limiter IP tidak interferensi.
     * Setiap iterasi membersihkan key ini sebelum percobaan.
     */
    private const IP_TEST = '127.99.99.99';

    protected function setUp(): void
    {
        parent::setUp();

        // Bersihkan rate limiter IP di awal setiap test method
        $this->clearAllIpRateLimiters();
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    /**
     * Bersihkan semua rate limiter IP untuk ketiga guard.
     */
    private function clearAllIpRateLimiters(): void
    {
        RateLimiter::clear('login.ip.admin.' . request()->ip());
        RateLimiter::clear('login.ip.guru.' . request()->ip());
        RateLimiter::clear('login.ip.siswa.' . request()->ip());
    }

    /**
     * Buat string alfanumerik acak dari integer seed untuk menggantikan regex generator.
     * Menghasilkan string 8 karakter yang deterministik dari input int.
     */
    private static function intToAlphanumeric(int $n): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $abs   = abs($n);
        $result = '';
        for ($i = 0; $i < 8; $i++) {
            $result .= $chars[$abs % 36];
            $abs = (int) ($abs / 36) + 1;
        }
        return $result;
    }

    // ─── Tests ────────────────────────────────────────────────────────────────

    /**
     * Property: Admin — username tidak ditemukan selalu menghasilkan pesan generik.
     *
     * Strategi: generate username random dari integer (dijamin tidak ada di DB
     * karena diawali prefix 'pbt_notfound_').
     * Verifikasi: error field 'username' berisi pesan generik yang identik.
     */
    public function test_property_admin_username_tidak_ada_selalu_pesan_generik(): void
    {
        $this->limitTo(100)->forAll(
            // Username: prefix unik + string dari integer → tidak mungkin ada di DB
            Generators::map(
                fn(int $n) => 'pbt_notfound_' . self::intToAlphanumeric($n),
                Generators::int()
            ),
            // Password: integer positif sebagai representasi password random
            Generators::map(
                fn(int $n) => 'pass_' . abs($n % 999999),
                Generators::int()
            )
        )->then(function (string $username, string $password) {
            // Bersihkan rate limiter identitas dan IP untuk iterasi ini
            RateLimiter::clear('login.identity.admin.' . mb_strtolower($username));
            $this->clearAllIpRateLimiters();

            $component = Livewire::test(LoginAdmin::class)
                ->set('username', $username)
                ->set('password', $password)
                ->call('authenticate');

            // Pastikan tidak ada redirect ke dashboard
            $component->assertNoRedirect();

            // Pastikan ada error pada field 'username'
            $component->assertHasErrors(['username']);

            $errors = $component->errors()->get('username');
            $this->assertNotEmpty($errors, "Harus ada pesan error untuk username='{$username}'");

            // Verifikasi pesan error adalah tepat pesan generik yang sama
            $this->assertContains(
                self::PESAN_ERROR_GENERIK,
                $errors,
                "Pesan error untuk admin (username tidak ada) harus generik. " .
                "Diterima: " . implode(', ', $errors)
            );
        });
    }

    /**
     * Property: Admin — password salah (username ada, password salah) selalu pesan generik identik
     * dengan kasus username-tidak-ada.
     *
     * Strategi: buat satu admin sebelum loop, lalu test dengan password berbeda setiap iterasi.
     */
    public function test_property_admin_password_salah_pesan_identik_dengan_username_tidak_ada(): void
    {
        // Buat satu admin aktif sebagai referensi
        $admin = Admin::factory()->create([
            'username'     => 'pbt_admin_ref_pw',
            'password'     => bcrypt('RahAsiaPas5word!Correct'),
            'status_aktif' => true,
        ]);

        $this->limitTo(100)->forAll(
            // Password yang SALAH: string yang pasti berbeda dari password benar
            Generators::map(
                fn(int $n) => 'wrong_' . abs($n % 999999),
                Generators::nat()
            )
        )->then(function (string $passwordSalah) use ($admin) {
            RateLimiter::clear('login.identity.admin.' . mb_strtolower($admin->username));
            $this->clearAllIpRateLimiters();

            $component = Livewire::test(LoginAdmin::class)
                ->set('username', $admin->username)
                ->set('password', $passwordSalah)
                ->call('authenticate');

            $component->assertNoRedirect();
            $component->assertHasErrors(['username']);

            $errors = $component->errors()->get('username');

            $this->assertContains(
                self::PESAN_ERROR_GENERIK,
                $errors,
                "Pesan error untuk admin (password salah) harus identik dengan kasus username-tidak-ada. " .
                "Diterima: " . implode(', ', $errors)
            );
        });
    }

    /**
     * Property: Guru — NIY tidak ditemukan selalu menghasilkan pesan generik.
     *
     * Strategi: generate NIY dari integer, diawali '99' agar tidak bertabrakan
     * dengan data yang mungkin dibuat oleh factory.
     */
    public function test_property_guru_niy_tidak_ada_selalu_pesan_generik(): void
    {
        $this->limitTo(100)->forAll(
            // NIY: '99' + 8 digit dari integer → dijamin tidak ada di DB test
            Generators::map(
                fn(int $n) => '99' . str_pad((string) abs($n % 100000000), 8, '0', STR_PAD_LEFT),
                Generators::int()
            ),
            // Tanggal lahir random: tahun 1950–2010
            Generators::map(
                fn(\DateTime $d) => $d->format('Y-m-d'),
                Generators::date('1950-01-01', '2010-12-31')
            )
        )->then(function (string $niy, string $tanggalLahir) {
            RateLimiter::clear('login.identity.guru.' . mb_strtolower($niy));
            $this->clearAllIpRateLimiters();

            $component = Livewire::test(LoginGuru::class)
                ->set('niy', $niy)
                ->set('tanggal_lahir', $tanggalLahir)
                ->call('authenticate');

            $component->assertNoRedirect();
            $component->assertHasErrors(['niy']);

            $errors = $component->errors()->get('niy');
            $this->assertNotEmpty($errors, "Harus ada pesan error untuk niy='{$niy}'");

            $this->assertContains(
                self::PESAN_ERROR_GENERIK,
                $errors,
                "Pesan error untuk guru (NIY tidak ada) harus generik. " .
                "Diterima: " . implode(', ', $errors)
            );
        });
    }

    /**
     * Property: Guru — tanggal lahir salah (NIY ada, tanggal salah) pesan identik dengan NIY-tidak-ada.
     *
     * Strategi: buat guru aktif, lalu test dengan tanggal lahir yang berbeda dari yang tersimpan.
     */
    public function test_property_guru_tanggal_lahir_salah_pesan_identik_dengan_niy_tidak_ada(): void
    {
        $guru = Guru::factory()->create([
            'niy'          => '88000111001',
            'tanggal_lahir' => '1990-05-15',
            'status_aktif'  => true,
        ]);

        $this->limitTo(100)->forAll(
            // Tanggal lahir yang BERBEDA dari yang tersimpan (1990-05-15)
            // Gunakan pilihan tahun saja: 1980–1989 dan 1991–2000, pasti berbeda
            Generators::map(
                fn(\DateTime $d) => $d->format('Y-m-d'),
                Generators::date('1980-01-01', '1989-12-31')
            )
        )->then(function (string $tanggalSalah) use ($guru) {
            RateLimiter::clear('login.identity.guru.' . mb_strtolower($guru->niy));
            $this->clearAllIpRateLimiters();

            $component = Livewire::test(LoginGuru::class)
                ->set('niy', $guru->niy)
                ->set('tanggal_lahir', $tanggalSalah)
                ->call('authenticate');

            $component->assertNoRedirect();
            $component->assertHasErrors(['niy']);

            $errors = $component->errors()->get('niy');

            $this->assertContains(
                self::PESAN_ERROR_GENERIK,
                $errors,
                "Pesan error untuk guru (tanggal lahir salah) harus identik dengan kasus NIY-tidak-ada. " .
                "Diterima: " . implode(', ', $errors)
            );
        });
    }

    /**
     * Property: Siswa — NIS tidak ditemukan selalu menghasilkan pesan generik.
     *
     * Strategi: generate NIS dari integer, diawali '88' agar tidak bertabrakan
     * dengan data factory.
     */
    public function test_property_siswa_nis_tidak_ada_selalu_pesan_generik(): void
    {
        $this->limitTo(100)->forAll(
            // NIS: '88' + 8 digit dari integer → dijamin tidak ada di DB test
            Generators::map(
                fn(int $n) => '88' . str_pad((string) abs($n % 100000000), 8, '0', STR_PAD_LEFT),
                Generators::int()
            ),
            // Tanggal lahir random: tahun 2005–2015 (range siswa SMP)
            Generators::map(
                fn(\DateTime $d) => $d->format('Y-m-d'),
                Generators::date('2005-01-01', '2015-12-31')
            )
        )->then(function (string $nis, string $tanggalLahir) {
            RateLimiter::clear('login.identity.siswa.' . mb_strtolower($nis));
            $this->clearAllIpRateLimiters();

            $component = Livewire::test(LoginSiswa::class)
                ->set('nis', $nis)
                ->set('tanggal_lahir', $tanggalLahir)
                ->call('authenticate');

            $component->assertNoRedirect();
            $component->assertHasErrors(['nis']);

            $errors = $component->errors()->get('nis');
            $this->assertNotEmpty($errors, "Harus ada pesan error untuk nis='{$nis}'");

            $this->assertContains(
                self::PESAN_ERROR_GENERIK,
                $errors,
                "Pesan error untuk siswa (NIS tidak ada) harus generik. " .
                "Diterima: " . implode(', ', $errors)
            );
        });
    }

    /**
     * Property: Siswa — tanggal lahir salah (NIS ada, tanggal salah) pesan identik dengan NIS-tidak-ada.
     *
     * Strategi: buat siswa aktif, lalu test dengan tanggal lahir yang berbeda dari yang tersimpan.
     */
    public function test_property_siswa_tanggal_lahir_salah_pesan_identik_dengan_nis_tidak_ada(): void
    {
        $siswa = Siswa::factory()->create([
            'nis'          => '77000111001',
            'tanggal_lahir' => '2010-08-20',
            'status_aktif'  => true,
        ]);

        $this->limitTo(100)->forAll(
            // Tanggal lahir yang BERBEDA dari yang tersimpan (2010-08-20)
            // Gunakan range 2005–2009: pasti berbeda dari tahun 2010
            Generators::map(
                fn(\DateTime $d) => $d->format('Y-m-d'),
                Generators::date('2005-01-01', '2009-12-31')
            )
        )->then(function (string $tanggalSalah) use ($siswa) {
            RateLimiter::clear('login.identity.siswa.' . mb_strtolower($siswa->nis));
            $this->clearAllIpRateLimiters();

            $component = Livewire::test(LoginSiswa::class)
                ->set('nis', $siswa->nis)
                ->set('tanggal_lahir', $tanggalSalah)
                ->call('authenticate');

            $component->assertNoRedirect();
            $component->assertHasErrors(['nis']);

            $errors = $component->errors()->get('nis');

            $this->assertContains(
                self::PESAN_ERROR_GENERIK,
                $errors,
                "Pesan error untuk siswa (tanggal lahir salah) harus identik dengan kasus NIS-tidak-ada. " .
                "Diterima: " . implode(', ', $errors)
            );
        });
    }

    /**
     * Property: Lintas guard — semua kegagalan kredensial menghasilkan pesan yang IDENTIK.
     *
     * Ini adalah uji komparasi lintas guard: verifikasi bahwa pesan error dari
     * Admin (username tidak ada), Guru (NIY tidak ada), dan Siswa (NIS tidak ada)
     * adalah string yang persis sama — tidak ada perbedaan wording antar guard.
     *
     * Strategi: gunakan integer untuk generate identitas unik per iterasi,
     * lalu bandingkan pesan error dari ketiga guard.
     */
    public function test_property_pesan_error_lintas_guard_identik(): void
    {
        $this->limitTo(100)->forAll(
            // Integer untuk membentuk identitas unik per iterasi
            Generators::choose(1, 9999999)
        )->then(function (int $seed) {
            $suffix = str_pad((string) $seed, 7, '0', STR_PAD_LEFT);

            // ── Admin ─────────────────────────────────────────────────────
            $adminUsername = 'xpbt_' . $suffix;
            RateLimiter::clear('login.identity.admin.' . $adminUsername);
            $this->clearAllIpRateLimiters();

            $adminComponent = Livewire::test(LoginAdmin::class)
                ->set('username', $adminUsername)
                ->set('password', 'somepassword_' . $suffix)
                ->call('authenticate');

            // ── Guru ──────────────────────────────────────────────────────
            $guruNiy = '99' . $suffix . '0';
            RateLimiter::clear('login.identity.guru.' . $guruNiy);
            $this->clearAllIpRateLimiters();

            $guruComponent = Livewire::test(LoginGuru::class)
                ->set('niy', $guruNiy)
                ->set('tanggal_lahir', '1990-01-01')
                ->call('authenticate');

            // ── Siswa ─────────────────────────────────────────────────────
            $siswaNis = '88' . $suffix . '0';
            RateLimiter::clear('login.identity.siswa.' . $siswaNis);
            $this->clearAllIpRateLimiters();

            $siswaComponent = Livewire::test(LoginSiswa::class)
                ->set('nis', $siswaNis)
                ->set('tanggal_lahir', '2010-01-01')
                ->call('authenticate');

            // ── Ambil pesan error ─────────────────────────────────────────
            $adminErrors = $adminComponent->errors()->get('username') ?? [];
            $guruErrors  = $guruComponent->errors()->get('niy') ?? [];
            $siswaErrors = $siswaComponent->errors()->get('nis') ?? [];

            // ── Verifikasi semua pesan ada ────────────────────────────────
            $this->assertNotEmpty($adminErrors, "Admin harus mengembalikan error (seed={$seed})");
            $this->assertNotEmpty($guruErrors, "Guru harus mengembalikan error (seed={$seed})");
            $this->assertNotEmpty($siswaErrors, "Siswa harus mengembalikan error (seed={$seed})");

            // ── Verifikasi semua == PESAN_ERROR_GENERIK ───────────────────
            $this->assertContains(
                self::PESAN_ERROR_GENERIK,
                $adminErrors,
                "Admin: pesan error harus generik (seed={$seed}). Diterima: " . implode(', ', $adminErrors)
            );
            $this->assertContains(
                self::PESAN_ERROR_GENERIK,
                $guruErrors,
                "Guru: pesan error harus generik (seed={$seed}). Diterima: " . implode(', ', $guruErrors)
            );
            $this->assertContains(
                self::PESAN_ERROR_GENERIK,
                $siswaErrors,
                "Siswa: pesan error harus generik (seed={$seed}). Diterima: " . implode(', ', $siswaErrors)
            );

            // ── Verifikasi eksplisit ketiga pesan error identik ───────────
            $adminPesan = collect($adminErrors)->first(fn($e) => $e === self::PESAN_ERROR_GENERIK);
            $guruPesan  = collect($guruErrors)->first(fn($e) => $e === self::PESAN_ERROR_GENERIK);
            $siswaPesan = collect($siswaErrors)->first(fn($e) => $e === self::PESAN_ERROR_GENERIK);

            $this->assertSame(
                $adminPesan,
                $guruPesan,
                "Pesan error admin dan guru harus identik (seed={$seed})"
            );
            $this->assertSame(
                $guruPesan,
                $siswaPesan,
                "Pesan error guru dan siswa harus identik (seed={$seed})"
            );
        });
    }
}
