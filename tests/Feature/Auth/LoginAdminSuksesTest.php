<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\LoginAdmin;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * 12.4 — LoginAdminSuksesTest
 *
 * Verifikasi:
 * - Kredensial valid → redirect ke admin.dashboard
 * - Kredensial tidak valid → ada error (pesan generik)
 * - Akun nonaktif → login ditolak
 * Validates: Req 1.2, 1.3, 1.4
 */
class LoginAdminSuksesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Bersihkan rate limiter agar tidak interferensi antar test
        RateLimiter::clear('login.ip.admin.' . request()->ip());
    }

    /** Kredensial valid → redirect ke admin.dashboard. */
    public function test_login_admin_dengan_kredensial_valid_redirect_ke_dashboard(): void
    {
        $admin = Admin::factory()->create([
            'username'    => 'admintest',
            'password'    => bcrypt('password123'),
            'status_aktif' => true,
        ]);

        RateLimiter::clear('login.identity.admin.admintest');

        Livewire::test(LoginAdmin::class)
            ->set('username', 'admintest')
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertRedirect(route('admin.dashboard'));
    }

    /** Kredensial tidak valid (password salah) → ada error pada form. */
    public function test_login_admin_dengan_password_salah_menampilkan_error(): void
    {
        $admin = Admin::factory()->create([
            'username'    => 'admintest2',
            'password'    => bcrypt('password123'),
            'status_aktif' => true,
        ]);

        RateLimiter::clear('login.identity.admin.admintest2');

        Livewire::test(LoginAdmin::class)
            ->set('username', 'admintest2')
            ->set('password', 'wrongpassword')
            ->call('authenticate')
            ->assertHasErrors(['username']);
    }

    /** Username tidak ada → ada error pada form. */
    public function test_login_admin_dengan_username_tidak_ada_menampilkan_error(): void
    {
        RateLimiter::clear('login.identity.admin.tidakada');

        Livewire::test(LoginAdmin::class)
            ->set('username', 'tidakada')
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertHasErrors(['username']);
    }

    /** Akun nonaktif + kredensial benar → login ditolak. */
    public function test_login_admin_dengan_akun_nonaktif_ditolak(): void
    {
        $admin = Admin::factory()->nonaktif()->create([
            'username' => 'adminnonaktif',
            'password' => bcrypt('password123'),
        ]);

        RateLimiter::clear('login.identity.admin.adminnonaktif');

        $component = Livewire::test(LoginAdmin::class)
            ->set('username', 'adminnonaktif')
            ->set('password', 'password123')
            ->call('authenticate');

        // Harus tidak redirect ke dashboard
        $component->assertNoRedirect();

        // Harus ada error
        $component->assertHasErrors(['username']);
    }

    /** Akun nonaktif tidak menghasilkan autentikasi sesi. */
    public function test_login_admin_akun_nonaktif_tidak_membuat_sesi(): void
    {
        $admin = Admin::factory()->nonaktif()->create([
            'username' => 'adminnonaktif2',
            'password' => bcrypt('password123'),
        ]);

        RateLimiter::clear('login.identity.admin.adminnonaktif2');

        Livewire::test(LoginAdmin::class)
            ->set('username', 'adminnonaktif2')
            ->set('password', 'password123')
            ->call('authenticate');

        $this->assertGuest('admin');
    }
}
