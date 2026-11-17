<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\LoginSiswa;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * 12.6 — LoginSiswaSuksesTest
 *
 * Verifikasi:
 * - NIS + tanggal_lahir valid → redirect ke siswa.dashboard
 * - Kredensial tidak valid → ada error (pesan generik)
 * - Akun nonaktif → login ditolak
 * Validates: Req 3.2, 3.3, 3.4
 */
class LoginSiswaSuksesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('login.ip.siswa.' . request()->ip());
    }

    /** NIS + tanggal_lahir valid → redirect ke siswa.dashboard. */
    public function test_login_siswa_dengan_kredensial_valid_redirect_ke_dashboard(): void
    {
        $siswa = Siswa::factory()->create([
            'nis'          => '1234567890',
            'tanggal_lahir' => '2008-06-10',
            'status_aktif'  => true,
        ]);

        RateLimiter::clear('login.identity.siswa.1234567890');

        Livewire::test(LoginSiswa::class)
            ->set('nis', '1234567890')
            ->set('tanggal_lahir', '2008-06-10')
            ->call('authenticate')
            ->assertRedirect(route('siswa.dashboard'));
    }

    /** Tanggal lahir salah → ada error pada form. */
    public function test_login_siswa_dengan_tanggal_lahir_salah_menampilkan_error(): void
    {
        $siswa = Siswa::factory()->create([
            'nis'          => '0987654321',
            'tanggal_lahir' => '2008-06-10',
            'status_aktif'  => true,
        ]);

        RateLimiter::clear('login.identity.siswa.0987654321');

        Livewire::test(LoginSiswa::class)
            ->set('nis', '0987654321')
            ->set('tanggal_lahir', '2010-01-01') // tanggal salah
            ->call('authenticate')
            ->assertHasErrors(['nis']);
    }

    /** NIS tidak ada → ada error pada form. */
    public function test_login_siswa_dengan_nis_tidak_ada_menampilkan_error(): void
    {
        RateLimiter::clear('login.identity.siswa.9999999999');

        Livewire::test(LoginSiswa::class)
            ->set('nis', '9999999999')
            ->set('tanggal_lahir', '2008-01-01')
            ->call('authenticate')
            ->assertHasErrors(['nis']);
    }

    /** Akun nonaktif + kredensial benar → login ditolak. */
    public function test_login_siswa_dengan_akun_nonaktif_ditolak(): void
    {
        $siswa = Siswa::factory()->nonaktif()->create([
            'nis'          => '1111111111',
            'tanggal_lahir' => '2007-09-25',
        ]);

        RateLimiter::clear('login.identity.siswa.1111111111');

        $component = Livewire::test(LoginSiswa::class)
            ->set('nis', '1111111111')
            ->set('tanggal_lahir', $siswa->tanggal_lahir->format('Y-m-d'))
            ->call('authenticate');

        $component->assertNoRedirect();
        $component->assertHasErrors(['nis']);
    }

    /** Akun nonaktif tidak membuat sesi terautentikasi. */
    public function test_login_siswa_akun_nonaktif_tidak_membuat_sesi(): void
    {
        $siswa = Siswa::factory()->nonaktif()->create([
            'nis'          => '2222222222',
            'tanggal_lahir' => '2007-09-25',
        ]);

        RateLimiter::clear('login.identity.siswa.2222222222');

        Livewire::test(LoginSiswa::class)
            ->set('nis', '2222222222')
            ->set('tanggal_lahir', $siswa->tanggal_lahir->format('Y-m-d'))
            ->call('authenticate');

        $this->assertGuest('siswa');
    }
}
