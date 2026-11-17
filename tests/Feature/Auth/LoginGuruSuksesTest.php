<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\LoginGuru;
use App\Models\Guru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * 12.5 — LoginGuruSuksesTest
 *
 * Verifikasi:
 * - NIY + tanggal_lahir valid → redirect ke guru.dashboard
 * - Kredensial tidak valid → ada error (pesan generik)
 * - Akun nonaktif → login ditolak
 * Validates: Req 2.2, 2.3, 2.4
 */
class LoginGuruSuksesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('login.ip.guru.' . request()->ip());
    }

    /** NIY + tanggal_lahir valid → redirect ke guru.dashboard. */
    public function test_login_guru_dengan_kredensial_valid_redirect_ke_dashboard(): void
    {
        $guru = Guru::factory()->create([
            'niy'          => '1234567890',
            'tanggal_lahir' => '1990-05-15',
            'status_aktif'  => true,
        ]);

        RateLimiter::clear('login.identity.guru.1234567890');

        Livewire::test(LoginGuru::class)
            ->set('niy', '1234567890')
            ->set('tanggal_lahir', '1990-05-15')
            ->call('authenticate')
            ->assertRedirect(route('guru.dashboard'));
    }

    /** Tanggal lahir salah → ada error pada form. */
    public function test_login_guru_dengan_tanggal_lahir_salah_menampilkan_error(): void
    {
        $guru = Guru::factory()->create([
            'niy'          => '0987654321',
            'tanggal_lahir' => '1990-05-15',
            'status_aktif'  => true,
        ]);

        RateLimiter::clear('login.identity.guru.0987654321');

        Livewire::test(LoginGuru::class)
            ->set('niy', '0987654321')
            ->set('tanggal_lahir', '1991-01-01') // tanggal salah
            ->call('authenticate')
            ->assertHasErrors(['niy']);
    }

    /** NIY tidak ada → ada error pada form. */
    public function test_login_guru_dengan_niy_tidak_ada_menampilkan_error(): void
    {
        RateLimiter::clear('login.identity.guru.9999999999');

        Livewire::test(LoginGuru::class)
            ->set('niy', '9999999999')
            ->set('tanggal_lahir', '1990-01-01')
            ->call('authenticate')
            ->assertHasErrors(['niy']);
    }

    /** Akun nonaktif + kredensial benar → login ditolak. */
    public function test_login_guru_dengan_akun_nonaktif_ditolak(): void
    {
        $guru = Guru::factory()->nonaktif()->create([
            'niy'          => '1111111111',
            'tanggal_lahir' => '1985-03-20',
        ]);

        RateLimiter::clear('login.identity.guru.1111111111');

        $component = Livewire::test(LoginGuru::class)
            ->set('niy', '1111111111')
            ->set('tanggal_lahir', $guru->tanggal_lahir->format('Y-m-d'))
            ->call('authenticate');

        $component->assertNoRedirect();
        $component->assertHasErrors(['niy']);
    }

    /** Akun nonaktif tidak membuat sesi terautentikasi. */
    public function test_login_guru_akun_nonaktif_tidak_membuat_sesi(): void
    {
        $guru = Guru::factory()->nonaktif()->create([
            'niy'          => '2222222222',
            'tanggal_lahir' => '1985-03-20',
        ]);

        RateLimiter::clear('login.identity.guru.2222222222');

        Livewire::test(LoginGuru::class)
            ->set('niy', '2222222222')
            ->set('tanggal_lahir', $guru->tanggal_lahir->format('Y-m-d'))
            ->call('authenticate');

        $this->assertGuest('guru');
    }
}
