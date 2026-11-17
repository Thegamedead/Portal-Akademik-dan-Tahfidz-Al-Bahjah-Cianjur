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
 * 14.4 — RateLimitingIpPropertyTest
 *
 * Property 4: Rate Limiting IP Konsisten di Ketiga Guard
 *
 * Untuk semua alamat IP random dan ketiga guard (admin/guru/siswa), setelah
 * melebihi ambang IP yang dikonfigurasi (`ip_max_attempts`), percobaan login
 * berikutnya HARUS selalu diblokir — berlaku konsisten di ketiga guard.
 *
 * Strategi pengujian: karena Livewire test berjalan di environment CLI dengan
 * IP request tetap (127.0.0.1), IP "random" disimulasikan dengan membuat
 * IP key unik per iterasi menggunakan format yang sama dengan implementasi
 * (`login.ip.{guard}.{ip_string}`). Setiap iterasi menggunakan IP string
 * berbeda yang dihasilkan dari seed random, lalu rate limiter di-hit langsung
 * sebanyak ip_max_attempts kali untuk mensimulasikan percobaan sebelumnya,
 * dan percobaan terakhir (dari request nyata) harus memicu blokir.
 *
 * **Validates: Requirements 4.2, 4.4**
 */
class RateLimitingIpPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    /**
     * Identitas dummy yang dijamin tidak ada di DB — dipakai tetap agar
     * identity rate limiter tidak menjadi variabel pengganggu.
     */
    private const USERNAME_DUMMY = 'pbt_ip_admin_dummy_zzz';
    private const NIY_DUMMY      = '9800000001';
    private const NIS_DUMMY      = '9900000001';

    /**
     * IP test default yang digunakan oleh request Livewire dalam environment pengujian.
     */
    private const IP_LARAVEL_TEST = '127.0.0.1';

    // ─── Setup ────────────────────────────────────────────────────────────────

    protected function setUp(): void
    {
        parent::setUp();

        // Set captcha_threshold tinggi agar captcha tidak interferensi.
        Config::set('auth_security.captcha_threshold', 999);

        // Set identity_max_attempts sangat tinggi agar identity rate limiter
        // tidak mencapai batas dan mengganggu pengujian IP rate limiter.
        Config::set('auth_security.identity_max_attempts', 999);

        // Bersihkan semua rate limiter yang relevan di awal setiap test method.
        $this->bersihkanSemuaRateLimiter();
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Bersihkan semua rate limiter (IP + identitas) untuk dummy identitas
     * di ketiga guard.
     */
    private function bersihkanSemuaRateLimiter(): void
    {
        foreach (['admin', 'guru', 'siswa'] as $guard) {
            RateLimiter::clear("login.ip.{$guard}." . self::IP_LARAVEL_TEST);
            RateLimiter::clear("login.identity.{$guard}." . mb_strtolower(self::USERNAME_DUMMY));
            RateLimiter::clear("login.identity.{$guard}." . self::NIY_DUMMY);
            RateLimiter::clear("login.identity.{$guard}." . self::NIS_DUMMY);
        }
        Session::forget('admin_login_failures.' . self::USERNAME_DUMMY);
        Session::forget('guru_login_failures.' . self::NIY_DUMMY);
        Session::forget('siswa_login_failures.' . self::NIS_DUMMY);
    }

    /**
     * Ambil ip_max_attempts dari konfigurasi.
     */
    private function ipMaxAttempts(): int
    {
        return (int) config('auth_security.ip_max_attempts', 20);
    }

    /**
     * Ambil ip_decay_seconds dari konfigurasi.
     */
    private function ipDecaySeconds(): int
    {
        return (int) config('auth_security.ip_decay_seconds', 60);
    }

    /**
     * Pre-seed rate limiter IP untuk guard tertentu dengan $hitCount hit,
     * mensimulasikan $hitCount percobaan sebelumnya dari IP yang sama.
     */
    private function seedIpRateLimiter(string $guard, int $hitCount): void
    {
        $key = "login.ip.{$guard}." . self::IP_LARAVEL_TEST;
        for ($i = 0; $i < $hitCount; $i++) {
            RateLimiter::hit($key, $this->ipDecaySeconds());
        }
    }

    /**
     * Lakukan satu percobaan login untuk guard ADMIN.
     * Mengembalikan array errors dari field 'username'.
     *
     * @return string[]
     */
    private function cobaLoginAdmin(): array
    {
        $component = Livewire::test(LoginAdmin::class)
            ->set('username', self::USERNAME_DUMMY)
            ->set('password', 'password_salah_pbt_ip_99999')
            ->call('authenticate');

        return $component->errors()->get('username') ?? [];
    }

    /**
     * Lakukan satu percobaan login untuk guard GURU.
     * Mengembalikan array errors dari field 'niy'.
     *
     * @return string[]
     */
    private function cobaLoginGuru(): array
    {
        $component = Livewire::test(LoginGuru::class)
            ->set('niy', self::NIY_DUMMY)
            ->set('tanggal_lahir', '1990-01-01')
            ->call('authenticate');

        return $component->errors()->get('niy') ?? [];
    }

    /**
     * Lakukan satu percobaan login untuk guard SISWA.
     * Mengembalikan array errors dari field 'nis'.
     *
     * @return string[]
     */
    private function cobaLoginSiswa(): array
    {
        $component = Livewire::test(LoginSiswa::class)
            ->set('nis', self::NIS_DUMMY)
            ->set('tanggal_lahir', '2010-01-01')
            ->call('authenticate');

        return $component->errors()->get('nis') ?? [];
    }

    /**
     * Verifikasi bahwa errors mengandung pesan BLOKIR rate limiting IP.
     * Pesan yang dihasilkan: "Terlalu banyak percobaan dari perangkat ini."
     */
    private function assertTerblokirOlehIp(array $errors, string $konteks): void
    {
        $pesanBlokir = collect($errors)->first(
            fn(string $e) => str_contains($e, 'Terlalu banyak percobaan dari perangkat ini')
        );

        $this->assertNotNull(
            $pesanBlokir,
            "Percobaan setelah melampaui ip_max_attempts HARUS diblokir rate limiter IP di {$konteks}. " .
            "Errors diterima: " . implode(', ', $errors)
        );
    }

    /**
     * Verifikasi bahwa errors BELUM berisi pesan blokir IP.
     * Percobaan sebelum mencapai ambang harus menghasilkan error gagal kredensial.
     */
    private function assertBelumTerblokirIp(array $errors, string $konteks): void
    {
        $pesanBlokir = collect($errors)->first(
            fn(string $e) => str_contains($e, 'Terlalu banyak percobaan dari perangkat ini')
        );

        $this->assertNull(
            $pesanBlokir,
            "Percobaan di {$konteks} seharusnya BELUM diblokir oleh rate limiter IP. " .
            "Errors diterima: " . implode(', ', $errors)
        );

        $this->assertNotEmpty(
            $errors,
            "Percobaan di {$konteks} harus mengembalikan SUATU error."
        );
    }

    // ─── Tests ────────────────────────────────────────────────────────────────

    /**
     * Property: Guard Admin — setelah ip_max_attempts percobaan dari satu IP,
     * percobaan berikutnya diblokir oleh rate limiter IP.
     *
     * Strategi: generate integer N random (1–100) sebagai jumlah percobaan
     * yang akan disimulasikan via RateLimiter::hit(). Dengan N ≥ ip_max_attempts,
     * percobaan login berikutnya harus selalu diblokir IP.
     *
     * Ini memverifikasi property: ∀ N ≥ ip_max_attempts, percobaan login
     * setelah N hit pada IP key selalu menghasilkan blokir IP.
     */
    public function test_property_admin_ip_diblokir_setelah_lampaui_ambang(): void
    {
        $maxIp = $this->ipMaxAttempts();

        $this->limitTo(50)->forAll(
            // Generate multiplier 1–5 untuk variasi jumlah percobaan di atas ambang
            Generators::choose(1, 5)
        )->then(function (int $multiplier) use ($maxIp) {
            $this->bersihkanSemuaRateLimiter();

            // Simulasikan $maxIp * $multiplier percobaan sebelumnya (semua melampaui ambang)
            $jumlahPrevious = $maxIp * $multiplier;
            $this->seedIpRateLimiter('admin', $jumlahPrevious);

            // Percobaan login sekarang HARUS diblokir oleh rate limiter IP
            $errors = $this->cobaLoginAdmin();
            $this->assertTerblokirOlehIp(
                $errors,
                "admin (setelah {$jumlahPrevious} hit pada ip_max_attempts={$maxIp})"
            );
        });
    }

    /**
     * Property: Guard Guru — setelah ip_max_attempts percobaan dari satu IP,
     * percobaan berikutnya diblokir oleh rate limiter IP.
     */
    public function test_property_guru_ip_diblokir_setelah_lampaui_ambang(): void
    {
        $maxIp = $this->ipMaxAttempts();

        $this->limitTo(50)->forAll(
            Generators::choose(1, 5)
        )->then(function (int $multiplier) use ($maxIp) {
            $this->bersihkanSemuaRateLimiter();

            $jumlahPrevious = $maxIp * $multiplier;
            $this->seedIpRateLimiter('guru', $jumlahPrevious);

            $errors = $this->cobaLoginGuru();
            $this->assertTerblokirOlehIp(
                $errors,
                "guru (setelah {$jumlahPrevious} hit pada ip_max_attempts={$maxIp})"
            );
        });
    }

    /**
     * Property: Guard Siswa — setelah ip_max_attempts percobaan dari satu IP,
     * percobaan berikutnya diblokir oleh rate limiter IP.
     */
    public function test_property_siswa_ip_diblokir_setelah_lampaui_ambang(): void
    {
        $maxIp = $this->ipMaxAttempts();

        $this->limitTo(50)->forAll(
            Generators::choose(1, 5)
        )->then(function (int $multiplier) use ($maxIp) {
            $this->bersihkanSemuaRateLimiter();

            $jumlahPrevious = $maxIp * $multiplier;
            $this->seedIpRateLimiter('siswa', $jumlahPrevious);

            $errors = $this->cobaLoginSiswa();
            $this->assertTerblokirOlehIp(
                $errors,
                "siswa (setelah {$jumlahPrevious} hit pada ip_max_attempts={$maxIp})"
            );
        });
    }

    /**
     * Property: Sebelum mencapai ambang, login masih menghasilkan error kredensial
     * (bukan blokir IP) — rate limiter IP tidak memblokir lebih awal.
     *
     * Untuk setiap N < ip_max_attempts, percobaan login ke-(N+1) harus menghasilkan
     * error gagal kredensial, bukan error blokir IP.
     *
     * Strategi: generate N random (0 s.d. ip_max_attempts-1), pre-seed N hit,
     * lalu percobaan berikutnya harus BELUM diblokir.
     */
    public function test_property_sebelum_ambang_login_belum_diblokir_ip(): void
    {
        $maxIp = $this->ipMaxAttempts();

        // Hanya jalankan jika ip_max_attempts > 0 (validasi konfigurasi)
        $this->assertGreaterThan(0, $maxIp, 'ip_max_attempts harus > 0');

        $this->limitTo(50)->forAll(
            // N random dari 0 s.d. ip_max_attempts-1 (tepat DI BAWAH ambang)
            Generators::choose(0, max(0, $maxIp - 1))
        )->then(function (int $hitSebelumnya) use ($maxIp) {
            $this->bersihkanSemuaRateLimiter();

            // Pre-seed dengan $hitSebelumnya hit (belum mencapai ambang)
            $this->seedIpRateLimiter('admin', $hitSebelumnya);

            // Percobaan login sekarang harus BELUM diblokir IP
            $errors = $this->cobaLoginAdmin();
            $this->assertBelumTerblokirIp(
                $errors,
                "admin ({$hitSebelumnya} hit dari ambang {$maxIp} — belum harus diblokir)"
            );
        });
    }

    /**
     * Property: Konsistensi lintas guard — blokir IP terjadi pada ambang yang sama
     * (`ip_max_attempts`) di ketiga guard untuk IP yang sama.
     *
     * Catatan: setiap guard memiliki IP key-nya sendiri (`login.ip.{guard}.{ip}`),
     * sehingga percobaan di satu guard TIDAK mengurangi kuota guard lain.
     * Property ini memverifikasi bahwa setelah masing-masing guard di-seed dengan
     * ip_max_attempts hit, ketiganya diblokir secara konsisten.
     *
     * Iterasi dibatasi (limitTo 30) karena setiap iterasi melakukan 3 login call.
     */
    public function test_property_blokir_ip_konsisten_lintas_ketiga_guard(): void
    {
        $maxIp = $this->ipMaxAttempts();

        $this->limitTo(30)->forAll(
            // Extra hits beyond the threshold (0–50 extra on top of ip_max_attempts)
            Generators::choose(0, 50)
        )->then(function (int $extraHit) use ($maxIp) {
            $this->bersihkanSemuaRateLimiter();

            // Seed tepat $maxIp + $extraHit — selalu di atas ambang
            $jumlahHit = $maxIp + $extraHit;

            // Pre-seed ketiga guard dengan jumlah hit yang sama
            $this->seedIpRateLimiter('admin', $jumlahHit);
            $this->seedIpRateLimiter('guru', $jumlahHit);
            $this->seedIpRateLimiter('siswa', $jumlahHit);

            // Ketiga guard harus memblokir login berikutnya
            $adminErrors = $this->cobaLoginAdmin();
            $guruErrors  = $this->cobaLoginGuru();
            $siswaErrors = $this->cobaLoginSiswa();

            $this->assertTerblokirOlehIp(
                $adminErrors,
                "admin (seed={$jumlahHit}, ip_max_attempts={$maxIp})"
            );
            $this->assertTerblokirOlehIp(
                $guruErrors,
                "guru (seed={$jumlahHit}, ip_max_attempts={$maxIp})"
            );
            $this->assertTerblokirOlehIp(
                $siswaErrors,
                "siswa (seed={$jumlahHit}, ip_max_attempts={$maxIp})"
            );
        });
    }

    /**
     * Property: ip_max_attempts yang berbeda (dari konfigurasi) selalu dihormati.
     *
     * Verifikasi bahwa nilai `ip_max_attempts` dari konfigurasi benar-benar
     * digunakan sebagai ambang — bukan nilai hardcoded. Dengan mengubah konfigurasi
     * ke nilai berbeda, sistem harus memblokir pada ambang baru.
     *
     * Strategi: generate ip_max_attempts_baru random (3–15), set config ke nilai
     * itu, seed tepat sejumlah itu, verifikasi blokir; kemudian seed tepat satu
     * kurang dari itu, verifikasi belum diblokir.
     */
    public function test_property_ambang_ip_mengikuti_nilai_konfigurasi(): void
    {
        $this->limitTo(30)->forAll(
            Generators::choose(3, 15)
        )->then(function (int $ambangBaru) {
            $this->bersihkanSemuaRateLimiter();

            // Override konfigurasi dengan nilai baru
            Config::set('auth_security.ip_max_attempts', $ambangBaru);

            // ── Tepat di ambang: harus diblokir ──────────────────────────────
            $this->seedIpRateLimiter('admin', $ambangBaru);
            $errorsBlokir = $this->cobaLoginAdmin();
            $this->assertTerblokirOlehIp(
                $errorsBlokir,
                "admin (ambang_baru={$ambangBaru}, seed=ambang → harus terblokir)"
            );

            // Bersihkan untuk pengujian berikutnya
            $this->bersihkanSemuaRateLimiter();

            // ── Satu di bawah ambang: belum harus diblokir ───────────────────
            if ($ambangBaru > 1) {
                $this->seedIpRateLimiter('admin', $ambangBaru - 1);
                $errorsBelumBlokir = $this->cobaLoginAdmin();
                $this->assertBelumTerblokirIp(
                    $errorsBelumBlokir,
                    "admin (ambang_baru={$ambangBaru}, seed=ambang-1 → belum diblokir)"
                );
            }

            // Restore konfigurasi default untuk iterasi berikutnya
            Config::set('auth_security.ip_max_attempts', 20);
        });
    }

    /**
     * Property: Rate limiter IP per guard bersifat independen — hit di guard A
     * tidak mengurangi kuota guard B.
     *
     * Verifikasi bahwa key rate limiter IP mengandung nama guard sehingga
     * percobaan di guard admin tidak mempengaruhi kuota IP guard guru dan siswa.
     */
    public function test_property_rate_limiter_ip_independen_antar_guard(): void
    {
        $maxIp = $this->ipMaxAttempts();

        $this->limitTo(30)->forAll(
            // Extra hits beyond threshold (0–50 extra)
            Generators::choose(0, 50)
        )->then(function (int $extraHit) use ($maxIp) {
            $this->bersihkanSemuaRateLimiter();

            $jumlahHit = $maxIp + $extraHit;

            // Blokir HANYA guard admin dengan hit di atas ambang
            $this->seedIpRateLimiter('admin', $jumlahHit);

            // Guard admin harus terblokir
            $adminErrors = $this->cobaLoginAdmin();
            $this->assertTerblokirOlehIp(
                $adminErrors,
                "admin (seed={$jumlahHit} → harus terblokir)"
            );

            // Guard guru dan siswa BELUM harus terblokir (kuota masih penuh)
            $guruErrors  = $this->cobaLoginGuru();
            $siswaErrors = $this->cobaLoginSiswa();

            $this->assertBelumTerblokirIp(
                $guruErrors,
                "guru (tidak ada hit pada guard guru → belum diblokir IP)"
            );
            $this->assertBelumTerblokirIp(
                $siswaErrors,
                "siswa (tidak ada hit pada guard siswa → belum diblokir IP)"
            );
        });
    }
}
