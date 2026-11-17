<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\LoginAdmin;
use App\Livewire\Auth\LoginGuru;
use App\Livewire\Auth\LoginSiswa;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * 14.3 — RateLimitingIdentitasPropertyTest
 *
 * Property 3: Rate Limiting Identitas Konsisten di Ketiga Guard
 *
 * Untuk semua guard (admin/guru/siswa) dan semua identitas random, setelah
 * tepat N percobaan login gagal dalam jendela waktu yang dikonfigurasi,
 * percobaan ke-(N+1) HARUS selalu diblokir — berlaku konsisten tanpa
 * membedakan guard mana yang digunakan.
 *
 * **Validates: Requirements 4.1, 4.4**
 */
class RateLimitingIdentitasPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    /**
     * IP dummy yang dipakai di seluruh tes agar rate limiter IP tidak interferensi.
     */
    private const IP_TEST = '127.99.88.77';

    /**
     * Awalan identitas yang dijamin tidak ada di DB (setelah RefreshDatabase).
     */
    private const PREFIX_ADMIN = 'pbt_rl_admin_';
    private const PREFIX_GURU  = '96';   // prefix NIY → 96XXXXXXXX
    private const PREFIX_SISWA = '97';   // prefix NIS → 97XXXXXXXX

    // ─── Setup ────────────────────────────────────────────────────────────────

    protected function setUp(): void
    {
        parent::setUp();

        // Set captcha_threshold tinggi (di atas max_attempts) agar captcha tidak
        // interferensi dengan penghitungan rate limiting dalam tes ini.
        // Captcha diuji terpisah; di sini kita fokus pada rate limiting identitas.
        Config::set('auth_security.captcha_threshold', 999);

        // Pastikan IP rate limit tidak interferensi: set ambang IP sangat tinggi.
        Config::set('auth_security.ip_max_attempts', 999);

        // Bersihkan semua key rate limiter identitas dan IP di awal setiap test method.
        $this->clearAllRateLimiters('pbt_dummy');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Konversi integer ke string alfanumerik 10 karakter (deterministik).
     */
    private static function intToSuffix(int $n): string
    {
        $chars  = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $abs    = abs($n) + 1;
        $result = '';
        for ($i = 0; $i < 10; $i++) {
            $result .= $chars[$abs % 36];
            $abs     = (int) ($abs / 36) + 1;
        }
        return $result;
    }

    /**
     * Bersihkan semua rate limiter (identitas + IP) untuk identitas yang diberikan
     * di ketiga guard, serta session failure counter.
     */
    private function clearAllRateLimiters(string $identifier): void
    {
        foreach (['admin', 'guru', 'siswa'] as $guard) {
            RateLimiter::clear("login.identity.{$guard}." . mb_strtolower($identifier));
            RateLimiter::clear("login.ip.{$guard}." . self::IP_TEST);
            RateLimiter::clear("login.ip.{$guard}.127.0.0.1");
        }
    }

    /**
     * Bersihkan session failure counter untuk identitas di semua guard.
     */
    private function clearSessionCounters(string $identifier): void
    {
        Session::forget('admin_login_failures.' . $identifier);
        Session::forget('guru_login_failures.' . $identifier);
        Session::forget('siswa_login_failures.' . $identifier);
    }

    /**
     * Ambil N dari konfigurasi — jumlah percobaan maksimum sebelum diblokir.
     */
    private function maxAttempts(): int
    {
        return (int) config('auth_security.identity_max_attempts', 5);
    }

    /**
     * Lakukan satu percobaan login gagal untuk guard ADMIN dengan identitas tersebut.
     * Mengembalikan array errors dari field 'username'.
     *
     * @return string[]
     */
    private function cobaLoginAdmin(string $username): array
    {
        $component = Livewire::test(LoginAdmin::class)
            ->set('username', $username)
            ->set('password', 'password_salah_pbt_12345')
            ->call('authenticate');

        return $component->errors()->get('username') ?? [];
    }

    /**
     * Lakukan satu percobaan login gagal untuk guard GURU dengan NIY tersebut.
     *
     * @return string[]
     */
    private function cobaLoginGuru(string $niy): array
    {
        $component = Livewire::test(LoginGuru::class)
            ->set('niy', $niy)
            ->set('tanggal_lahir', '1990-01-01')
            ->call('authenticate');

        return $component->errors()->get('niy') ?? [];
    }

    /**
     * Lakukan satu percobaan login gagal untuk guard SISWA dengan NIS tersebut.
     *
     * @return string[]
     */
    private function cobaLoginSiswa(string $nis): array
    {
        $component = Livewire::test(LoginSiswa::class)
            ->set('nis', $nis)
            ->set('tanggal_lahir', '2010-01-01')
            ->call('authenticate');

        return $component->errors()->get('nis') ?? [];
    }

    /**
     * Verifikasi bahwa array errors mengandung pesan BLOKIR rate limiting identitas,
     * yaitu string yang mengandung "Terlalu banyak percobaan".
     */
    private function assertTerblokir(array $errors, string $konteks): void
    {
        $pesanBlokir = collect($errors)->first(
            fn(string $e) => str_contains($e, 'Terlalu banyak percobaan')
        );

        $this->assertNotNull(
            $pesanBlokir,
            "Percobaan N+1 HARUS diblokir rate limiter identitas di {$konteks}. " .
            "Errors diterima: " . implode(', ', $errors)
        );
    }

    /**
     * Verifikasi bahwa errors ke-1 s.d. N adalah error GAGAL KREDENSIAL (bukan blokir).
     * Ini memastikan sistem tidak memblokir lebih awal dari N.
     */
    private function assertBelumTerblokir(array $errors, int $percobaan, string $konteks): void
    {
        // Percobaan 1..N harus mengembalikan error BUKAN blokir rate limiting identitas
        $pesanBlokir = collect($errors)->first(
            fn(string $e) => str_contains($e, 'Terlalu banyak percobaan')
        );

        $this->assertNull(
            $pesanBlokir,
            "Percobaan #{$percobaan} di {$konteks} seharusnya BELUM diblokir " .
            "(blokir terjadi di percobaan N+1). " .
            "Errors diterima: " . implode(', ', $errors)
        );

        $this->assertNotEmpty(
            $errors,
            "Percobaan #{$percobaan} di {$konteks} harus mengembalikan SUATU error."
        );
    }

    // ─── Tests ────────────────────────────────────────────────────────────────

    /**
     * Property: Guard Admin — setelah tepat N percobaan gagal, percobaan N+1 diblokir.
     *
     * Strategi: generate username random dari integer (prefix pbt_rl_admin_),
     * dijamin tidak ada di DB sehingga setiap percobaan pasti gagal dengan
     * error kredensial, bukan blokir dini.
     *
     * Iterasi sedikit (limitTo 30) karena setiap iterasi melakukan N+1 HTTP call.
     */
    public function test_property_admin_percobaan_ke_n_plus_1_selalu_terblokir(): void
    {
        $maxN = $this->maxAttempts();

        $this->limitTo(30)->forAll(
            Generators::map(
                fn(int $n) => self::PREFIX_ADMIN . self::intToSuffix($n),
                Generators::choose(1, 9_999_999)
            )
        )->then(function (string $username) use ($maxN) {
            // Bersihkan state sebelum iterasi ini
            $this->clearAllRateLimiters($username);
            $this->clearSessionCounters($username);

            // ── Percobaan 1 s.d. N: harus gagal dengan error BUKAN blokir ──
            for ($attempt = 1; $attempt <= $maxN; $attempt++) {
                $errors = $this->cobaLoginAdmin($username);

                $this->assertBelumTerblokir(
                    $errors,
                    $attempt,
                    "admin/username={$username}"
                );
            }

            // ── Percobaan N+1: HARUS diblokir ────────────────────────────
            $errorsBlokir = $this->cobaLoginAdmin($username);

            $this->assertTerblokir(
                $errorsBlokir,
                "admin/username={$username} (setelah {$maxN} gagal)"
            );
        });
    }

    /**
     * Property: Guard Guru — setelah tepat N percobaan gagal, percobaan N+1 diblokir.
     *
     * Strategi: generate NIY dari integer diawali prefix '96' → tidak ada di DB.
     * Tanggal lahir dummy '1990-01-01' — karena NIY tidak ada, selalu gagal.
     */
    public function test_property_guru_percobaan_ke_n_plus_1_selalu_terblokir(): void
    {
        $maxN = $this->maxAttempts();

        $this->limitTo(30)->forAll(
            Generators::map(
                fn(int $n) => self::PREFIX_GURU . str_pad((string) abs($n % 99_999_999), 8, '0', STR_PAD_LEFT),
                Generators::choose(1, 9_999_999)
            )
        )->then(function (string $niy) use ($maxN) {
            $this->clearAllRateLimiters($niy);
            $this->clearSessionCounters($niy);

            for ($attempt = 1; $attempt <= $maxN; $attempt++) {
                $errors = $this->cobaLoginGuru($niy);

                $this->assertBelumTerblokir(
                    $errors,
                    $attempt,
                    "guru/niy={$niy}"
                );
            }

            $errorsBlokir = $this->cobaLoginGuru($niy);

            $this->assertTerblokir(
                $errorsBlokir,
                "guru/niy={$niy} (setelah {$maxN} gagal)"
            );
        });
    }

    /**
     * Property: Guard Siswa — setelah tepat N percobaan gagal, percobaan N+1 diblokir.
     *
     * Strategi: generate NIS dari integer diawali prefix '97' → tidak ada di DB.
     */
    public function test_property_siswa_percobaan_ke_n_plus_1_selalu_terblokir(): void
    {
        $maxN = $this->maxAttempts();

        $this->limitTo(30)->forAll(
            Generators::map(
                fn(int $n) => self::PREFIX_SISWA . str_pad((string) abs($n % 99_999_999), 8, '0', STR_PAD_LEFT),
                Generators::choose(1, 9_999_999)
            )
        )->then(function (string $nis) use ($maxN) {
            $this->clearAllRateLimiters($nis);
            $this->clearSessionCounters($nis);

            for ($attempt = 1; $attempt <= $maxN; $attempt++) {
                $errors = $this->cobaLoginSiswa($nis);

                $this->assertBelumTerblokir(
                    $errors,
                    $attempt,
                    "siswa/nis={$nis}"
                );
            }

            $errorsBlokir = $this->cobaLoginSiswa($nis);

            $this->assertTerblokir(
                $errorsBlokir,
                "siswa/nis={$nis} (setelah {$maxN} gagal)"
            );
        });
    }

    /**
     * Property: Konsistensi lintas guard — blokir di percobaan N+1 berlaku IDENTIK
     * di ketiga guard untuk seed yang sama.
     *
     * Ini adalah uji komparasi: bukan hanya masing-masing guard diblokir,
     * tapi pola blokirnya konsisten — tidak ada guard yang diblokir lebih awal
     * atau lebih lambat dari guard lain.
     *
     * Strategi: dari satu seed integer, buat identitas berbeda untuk ketiga guard,
     * lakukan N percobaan gagal di masing-masing, lalu verifikasi ke-(N+1) diblokir
     * di ketiganya.
     */
    public function test_property_blokir_konsisten_lintas_ketiga_guard(): void
    {
        $maxN = $this->maxAttempts();

        $this->limitTo(30)->forAll(
            Generators::choose(1, 9_999_999)
        )->then(function (int $seed) use ($maxN) {
            $suffix   = self::intToSuffix($seed);
            $username = self::PREFIX_ADMIN . $suffix;
            $niy      = self::PREFIX_GURU . str_pad((string) ($seed % 99_999_999), 8, '0', STR_PAD_LEFT);
            $nis      = self::PREFIX_SISWA . str_pad((string) ($seed % 99_999_999), 8, '0', STR_PAD_LEFT);

            // Bersihkan state semua identitas
            $this->clearAllRateLimiters($username);
            $this->clearAllRateLimiters($niy);
            $this->clearAllRateLimiters($nis);
            $this->clearSessionCounters($username);
            $this->clearSessionCounters($niy);
            $this->clearSessionCounters($nis);

            // ── Percobaan 1..N di ketiga guard: semua harus BELUM terblokir ──
            for ($attempt = 1; $attempt <= $maxN; $attempt++) {
                $adminErrors = $this->cobaLoginAdmin($username);
                $guruErrors  = $this->cobaLoginGuru($niy);
                $siswaErrors = $this->cobaLoginSiswa($nis);

                $this->assertBelumTerblokir($adminErrors, $attempt, "admin (seed={$seed})");
                $this->assertBelumTerblokir($guruErrors, $attempt, "guru (seed={$seed})");
                $this->assertBelumTerblokir($siswaErrors, $attempt, "siswa (seed={$seed})");
            }

            // ── Percobaan N+1 di ketiga guard: semua HARUS terblokir ─────────
            $adminBlokirErrors = $this->cobaLoginAdmin($username);
            $guruBlokirErrors  = $this->cobaLoginGuru($niy);
            $siswaBlokirErrors = $this->cobaLoginSiswa($nis);

            $this->assertTerblokir($adminBlokirErrors, "admin (seed={$seed})");
            $this->assertTerblokir($guruBlokirErrors, "guru (seed={$seed})");
            $this->assertTerblokir($siswaBlokirErrors, "siswa (seed={$seed})");
        });
    }

    /**
     * Property: Setelah N+2, N+3, dst — blokir tetap berlaku (tidak ada celah).
     *
     * Verifikasi bahwa setelah terblokir di N+1, percobaan berikutnya (N+2) juga
     * tetap diblokir — blokir tidak hilang setelah satu percobaan.
     *
     * Diuji hanya untuk guard Admin sebagai representatif ketiga guard.
     */
    public function test_property_blokir_tetap_berlaku_setelah_n_plus_1(): void
    {
        $maxN = $this->maxAttempts();

        $this->limitTo(20)->forAll(
            Generators::map(
                fn(int $n) => self::PREFIX_ADMIN . 'persist_' . self::intToSuffix($n),
                Generators::choose(1, 9_999_999)
            )
        )->then(function (string $username) use ($maxN) {
            $this->clearAllRateLimiters($username);
            $this->clearSessionCounters($username);

            // Lakukan N percobaan untuk mencapai batas
            for ($attempt = 1; $attempt <= $maxN; $attempt++) {
                $this->cobaLoginAdmin($username);
            }

            // Percobaan N+1: terblokir
            $errorsN1 = $this->cobaLoginAdmin($username);
            $this->assertTerblokir($errorsN1, "admin/N+1/{$username}");

            // Percobaan N+2: masih harus terblokir (blokir tidak reset setelah 1 percobaan)
            $errorsN2 = $this->cobaLoginAdmin($username);
            $this->assertTerblokir($errorsN2, "admin/N+2/{$username}");
        });
    }
}
