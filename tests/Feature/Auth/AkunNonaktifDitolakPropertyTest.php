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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * 14.2 — AkunNonaktifDitolakPropertyTest
 *
 * Property 2: Akun Nonaktif Selalu Ditolak Login
 *
 * Untuk SEMUA akun (admin/guru/siswa) dengan status_aktif = false,
 * upaya login dengan kredensial yang secara teknis benar HARUS selalu
 * ditolak — tanpa pengecualian.
 *
 * **Validates: Requirements 1.4, 2.3, 3.4**
 */
class AkunNonaktifDitolakPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    /**
     * Pesan error yang diharapkan untuk akun Admin nonaktif.
     * Berbeda dengan Guru/Siswa — tanpa "Hubungi Admin".
     */
    private const PESAN_NONAKTIF_ADMIN = 'Akun tidak aktif.';

    /**
     * Pesan error yang diharapkan untuk akun Guru/Siswa nonaktif.
     */
    private const PESAN_NONAKTIF_GURU_SISWA = 'Akun tidak aktif. Hubungi Admin.';

    protected function setUp(): void
    {
        parent::setUp();
        $this->clearAllRateLimiters();
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Bersihkan semua rate limiter IP dan identitas yang mungkin terakumulasi.
     */
    private function clearAllRateLimiters(): void
    {
        foreach (['admin', 'guru', 'siswa'] as $guard) {
            RateLimiter::clear('login.ip.' . $guard . '.' . request()->ip());
        }
    }

    /**
     * Bersihkan rate limiter identitas untuk akun tertentu.
     */
    private function clearIdentityRateLimiter(string $guard, string $identifier): void
    {
        RateLimiter::clear('login.identity.' . $guard . '.' . mb_strtolower($identifier));
        RateLimiter::clear('login.ip.' . $guard . '.' . request()->ip());
    }

    // ─── Tests ────────────────────────────────────────────────────────────────

    /**
     * Property: Admin nonaktif — login dengan kredensial BENAR selalu ditolak.
     *
     * Strategi:
     * - Buat 1 akun Admin nonaktif dengan password yang diketahui sebelum loop.
     * - Setiap iterasi: generate password yang benar (deterministik dari seed),
     *   buat admin nonaktif baru, coba login dengan password yang benar.
     * - Verifikasi: ditolak, ada error pada field 'username', pesan spesifik nonaktif,
     *   dan guard 'admin' tidak terotentikasi setelah percobaan.
     */
    public function test_property_admin_nonaktif_selalu_ditolak_dengan_kredensial_benar(): void
    {
        $this->limitTo(50)->forAll(
            // Integer positif sebagai seed untuk membuat password unik per iterasi
            Generators::choose(1, 9999999)
        )->then(function (int $seed) {
            // Buat password yang diketahui dari seed
            $passwordBenar  = 'PasSword_pbt_' . $seed;
            $usernameUnik   = 'nonaktif_adm_' . str_pad((string) $seed, 7, '0', STR_PAD_LEFT);

            // Buat akun Admin nonaktif dengan password yang diketahui
            $admin = Admin::factory()->create([
                'username'     => $usernameUnik,
                'password'     => bcrypt($passwordBenar),
                'status_aktif' => false,
            ]);

            $this->clearIdentityRateLimiter('admin', $admin->username);

            // Coba login dengan kredensial yang BENAR
            $component = Livewire::test(LoginAdmin::class)
                ->set('username', $admin->username)
                ->set('password', $passwordBenar)
                ->call('authenticate');

            // 1. Tidak boleh ada redirect ke dashboard
            $component->assertNoRedirect();

            // 2. Harus ada error pada field 'username'
            $component->assertHasErrors(['username']);

            $errors = $component->errors()->get('username');
            $this->assertNotEmpty(
                $errors,
                "Admin nonaktif harus menghasilkan error (seed={$seed}, username={$usernameUnik})"
            );

            // 3. Pesan error harus spesifik untuk akun nonaktif
            $this->assertContains(
                self::PESAN_NONAKTIF_ADMIN,
                $errors,
                "Pesan error admin nonaktif harus '{$this->const_admin()}'. " .
                "Diterima: " . implode(', ', $errors) . " (seed={$seed})"
            );

            // 4. Guard 'admin' harus TIDAK terotentikasi setelah percobaan
            $this->assertFalse(
                Auth::guard('admin')->check(),
                "Guard 'admin' tidak boleh terotentikasi setelah login akun nonaktif (seed={$seed})"
            );

            // Cleanup: hapus akun yang dibuat iterasi ini
            $admin->delete();
        });
    }

    /**
     * Property: Guru nonaktif — login dengan kredensial BENAR selalu ditolak.
     *
     * Strategi:
     * - Setiap iterasi: buat Guru nonaktif dengan tanggal lahir dari generator.
     * - Login dengan NIY dan tanggal lahir yang BENAR.
     * - Verifikasi penolakan + pesan nonaktif + guard tidak terotentikasi.
     */
    public function test_property_guru_nonaktif_selalu_ditolak_dengan_kredensial_benar(): void
    {
        $this->limitTo(50)->forAll(
            // Tanggal lahir random untuk Guru (rentang guru aktif: 25–55 tahun)
            Generators::map(
                fn(\DateTime $d) => $d->format('Y-m-d'),
                Generators::date('1970-01-01', '1999-12-31')
            ),
            // Integer sebagai seed untuk NIY unik
            Generators::choose(1, 9999999)
        )->then(function (string $tanggalLahir, int $seed) {
            $niyUnik = '77' . str_pad((string) $seed, 8, '0', STR_PAD_LEFT);

            // Buat akun Guru nonaktif dengan tanggal lahir yang diketahui
            $guru = Guru::factory()->create([
                'niy'           => $niyUnik,
                'tanggal_lahir' => $tanggalLahir,
                'status_aktif'  => false,
            ]);

            $this->clearIdentityRateLimiter('guru', $guru->niy);

            // Coba login dengan NIY dan tanggal lahir yang BENAR
            $component = Livewire::test(LoginGuru::class)
                ->set('niy', $guru->niy)
                ->set('tanggal_lahir', $tanggalLahir)
                ->call('authenticate');

            // 1. Tidak boleh ada redirect ke dashboard
            $component->assertNoRedirect();

            // 2. Harus ada error pada field 'niy'
            $component->assertHasErrors(['niy']);

            $errors = $component->errors()->get('niy');
            $this->assertNotEmpty(
                $errors,
                "Guru nonaktif harus menghasilkan error (seed={$seed}, niy={$niyUnik})"
            );

            // 3. Pesan error harus spesifik untuk akun nonaktif
            $this->assertContains(
                self::PESAN_NONAKTIF_GURU_SISWA,
                $errors,
                "Pesan error guru nonaktif harus '{$this->const_guru_siswa()}'. " .
                "Diterima: " . implode(', ', $errors) . " (seed={$seed})"
            );

            // 4. Guard 'guru' harus TIDAK terotentikasi setelah percobaan
            $this->assertFalse(
                Auth::guard('guru')->check(),
                "Guard 'guru' tidak boleh terotentikasi setelah login akun nonaktif (seed={$seed})"
            );

            // Cleanup: hapus akun yang dibuat iterasi ini
            $guru->forceDelete();
        });
    }

    /**
     * Property: Siswa nonaktif — login dengan kredensial BENAR selalu ditolak.
     *
     * Strategi:
     * - Setiap iterasi: buat Siswa nonaktif dengan tanggal lahir dari generator.
     * - Login dengan NIS dan tanggal lahir yang BENAR.
     * - Verifikasi penolakan + pesan nonaktif + guard tidak terotentikasi.
     */
    public function test_property_siswa_nonaktif_selalu_ditolak_dengan_kredensial_benar(): void
    {
        $this->limitTo(50)->forAll(
            // Tanggal lahir random untuk Siswa (rentang siswa SMP: 11–18 tahun)
            Generators::map(
                fn(\DateTime $d) => $d->format('Y-m-d'),
                Generators::date('2006-01-01', '2013-12-31')
            ),
            // Integer sebagai seed untuk NIS unik
            Generators::choose(1, 9999999)
        )->then(function (string $tanggalLahir, int $seed) {
            $nisUnik = '66' . str_pad((string) $seed, 8, '0', STR_PAD_LEFT);

            // Buat akun Siswa nonaktif dengan tanggal lahir yang diketahui
            $siswa = Siswa::factory()->create([
                'nis'           => $nisUnik,
                'tanggal_lahir' => $tanggalLahir,
                'status_aktif'  => false,
            ]);

            $this->clearIdentityRateLimiter('siswa', $siswa->nis);

            // Coba login dengan NIS dan tanggal lahir yang BENAR
            $component = Livewire::test(LoginSiswa::class)
                ->set('nis', $siswa->nis)
                ->set('tanggal_lahir', $tanggalLahir)
                ->call('authenticate');

            // 1. Tidak boleh ada redirect ke dashboard
            $component->assertNoRedirect();

            // 2. Harus ada error pada field 'nis'
            $component->assertHasErrors(['nis']);

            $errors = $component->errors()->get('nis');
            $this->assertNotEmpty(
                $errors,
                "Siswa nonaktif harus menghasilkan error (seed={$seed}, nis={$nisUnik})"
            );

            // 3. Pesan error harus spesifik untuk akun nonaktif
            $this->assertContains(
                self::PESAN_NONAKTIF_GURU_SISWA,
                $errors,
                "Pesan error siswa nonaktif harus '{$this->const_guru_siswa()}'. " .
                "Diterima: " . implode(', ', $errors) . " (seed={$seed})"
            );

            // 4. Guard 'siswa' harus TIDAK terotentikasi setelah percobaan
            $this->assertFalse(
                Auth::guard('siswa')->check(),
                "Guard 'siswa' tidak boleh terotentikasi setelah login akun nonaktif (seed={$seed})"
            );

            // Cleanup: hapus akun yang dibuat iterasi ini
            $siswa->forceDelete();
        });
    }

    /**
     * Property: Mengaktifkan kembali akun (status_aktif = true) memungkinkan login —
     * membuktikan penolakan sebelumnya KHUSUS karena status nonaktif, bukan sebab lain.
     *
     * Strategi:
     * - Buat 1 akun per guard (admin/guru/siswa) dalam keadaan nonaktif.
     * - Verifikasi login ditolak.
     * - Aktifkan kembali akun (status_aktif = true).
     * - Verifikasi login BERHASIL setelah reaktivasi.
     * - Ini membuktikan kausalitas: penolakan disebabkan status_aktif = false,
     *   bukan faktor lain seperti kredensial yang salah atau masalah teknis.
     *
     * Test ini tidak memerlukan generator PBT karena tidak menguji variasi data —
     * melainkan menguji perubahan state yang deterministik.
     */
    public function test_property_reaktivasi_akun_memungkinkan_login_membuktikan_kausalitas(): void
    {
        $this->limitTo(30)->forAll(
            Generators::choose(1, 9999999)
        )->then(function (int $seed) {
            $suffix = str_pad((string) $seed, 7, '0', STR_PAD_LEFT);

            // ── Admin ─────────────────────────────────────────────────────────
            $passwordAdmin = 'ReAkT_' . $seed . '_Pass!';
            $adminNonaktif = Admin::factory()->create([
                'username'     => 'rkt_adm_' . $suffix,
                'password'     => bcrypt($passwordAdmin),
                'status_aktif' => false,
            ]);

            $this->clearIdentityRateLimiter('admin', $adminNonaktif->username);

            // Verifikasi: ditolak saat nonaktif
            $beforeAdmin = Livewire::test(LoginAdmin::class)
                ->set('username', $adminNonaktif->username)
                ->set('password', $passwordAdmin)
                ->call('authenticate');

            $beforeAdmin->assertNoRedirect();
            $beforeAdmin->assertHasErrors(['username']);
            $this->assertFalse(Auth::guard('admin')->check());

            // Aktifkan kembali akun admin
            $adminNonaktif->update(['status_aktif' => true]);
            $this->clearIdentityRateLimiter('admin', $adminNonaktif->username);

            // Verifikasi: login BERHASIL setelah reaktivasi
            $afterAdmin = Livewire::test(LoginAdmin::class)
                ->set('username', $adminNonaktif->username)
                ->set('password', $passwordAdmin)
                ->call('authenticate');

            $afterAdmin->assertRedirect(route('admin.dashboard'));
            Auth::guard('admin')->logout();
            $adminNonaktif->forceDelete();

            // ── Guru ──────────────────────────────────────────────────────────
            $tanggalLahirGuru = '1988-06-15';
            $guruNonaktif = Guru::factory()->create([
                'niy'           => '55' . $suffix,
                'tanggal_lahir' => $tanggalLahirGuru,
                'status_aktif'  => false,
            ]);

            $this->clearIdentityRateLimiter('guru', $guruNonaktif->niy);

            // Verifikasi: ditolak saat nonaktif
            $beforeGuru = Livewire::test(LoginGuru::class)
                ->set('niy', $guruNonaktif->niy)
                ->set('tanggal_lahir', $tanggalLahirGuru)
                ->call('authenticate');

            $beforeGuru->assertNoRedirect();
            $beforeGuru->assertHasErrors(['niy']);
            $this->assertFalse(Auth::guard('guru')->check());

            // Aktifkan kembali akun guru
            $guruNonaktif->update(['status_aktif' => true]);
            $this->clearIdentityRateLimiter('guru', $guruNonaktif->niy);

            // Verifikasi: login BERHASIL setelah reaktivasi
            $afterGuru = Livewire::test(LoginGuru::class)
                ->set('niy', $guruNonaktif->niy)
                ->set('tanggal_lahir', $tanggalLahirGuru)
                ->call('authenticate');

            $afterGuru->assertRedirect(route('guru.dashboard'));
            Auth::guard('guru')->logout();
            $guruNonaktif->forceDelete();

            // ── Siswa ─────────────────────────────────────────────────────────
            $tanggalLahirSiswa = '2010-03-22';
            $siswaNonaktif = Siswa::factory()->create([
                'nis'           => '44' . $suffix,
                'tanggal_lahir' => $tanggalLahirSiswa,
                'status_aktif'  => false,
            ]);

            $this->clearIdentityRateLimiter('siswa', $siswaNonaktif->nis);

            // Verifikasi: ditolak saat nonaktif
            $beforeSiswa = Livewire::test(LoginSiswa::class)
                ->set('nis', $siswaNonaktif->nis)
                ->set('tanggal_lahir', $tanggalLahirSiswa)
                ->call('authenticate');

            $beforeSiswa->assertNoRedirect();
            $beforeSiswa->assertHasErrors(['nis']);
            $this->assertFalse(Auth::guard('siswa')->check());

            // Aktifkan kembali akun siswa
            $siswaNonaktif->update(['status_aktif' => true]);
            $this->clearIdentityRateLimiter('siswa', $siswaNonaktif->nis);

            // Verifikasi: login BERHASIL setelah reaktivasi
            $afterSiswa = Livewire::test(LoginSiswa::class)
                ->set('nis', $siswaNonaktif->nis)
                ->set('tanggal_lahir', $tanggalLahirSiswa)
                ->call('authenticate');

            $afterSiswa->assertRedirect(route('siswa.dashboard'));
            Auth::guard('siswa')->logout();
            $siswaNonaktif->forceDelete();
        });
    }

    // ─── Const Helpers ───────────────────────────────────────────────────────

    /**
     * Helper untuk pesan error admin (dipakai di pesan kegagalan assertion).
     */
    private function const_admin(): string
    {
        return self::PESAN_NONAKTIF_ADMIN;
    }

    /**
     * Helper untuk pesan error guru/siswa (dipakai di pesan kegagalan assertion).
     */
    private function const_guru_siswa(): string
    {
        return self::PESAN_NONAKTIF_GURU_SISWA;
    }
}
