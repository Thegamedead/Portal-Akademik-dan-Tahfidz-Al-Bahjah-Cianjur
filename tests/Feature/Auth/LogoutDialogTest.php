<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 12.10 — LogoutDialogTest
 *
 * Verifikasi dashboard masing-masing guard mengandung markup dialog konfirmasi
 * logout (Alpine.js). Dialog harus ada di HTML meskipun tersembunyi.
 * Validates: Req 10.1
 */
class LogoutDialogTest extends TestCase
{
    use RefreshDatabase;

    // ── Admin Dashboard ───────────────────────────────────────────────────────

    /** Dashboard admin mengandung variabel Alpine showLogoutDialog. */
    public function test_dashboard_admin_mengandung_show_logout_dialog_variable(): void
    {
        $admin = Admin::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('showLogoutDialog', false);
    }

    /** Dashboard admin mengandung teks konfirmasi "Apakah Anda yakin". */
    public function test_dashboard_admin_mengandung_teks_konfirmasi_logout(): void
    {
        $admin = Admin::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Apakah Anda yakin ingin keluar dari sesi ini?');
    }

    /** Dashboard admin mengandung tombol konfirmasi "Ya, Keluar". */
    public function test_dashboard_admin_mengandung_tombol_ya_keluar(): void
    {
        $admin = Admin::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Ya, Keluar');
    }

    // ── Guru Dashboard ────────────────────────────────────────────────────────

    /** Dashboard guru mengandung variabel Alpine showLogoutDialog. */
    public function test_dashboard_guru_mengandung_show_logout_dialog_variable(): void
    {
        $guru = Guru::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($guru, 'guru')
            ->get(route('guru.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('showLogoutDialog', false);
    }

    /** Dashboard guru mengandung teks konfirmasi "Apakah Anda yakin". */
    public function test_dashboard_guru_mengandung_teks_konfirmasi_logout(): void
    {
        $guru = Guru::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($guru, 'guru')
            ->get(route('guru.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Apakah Anda yakin ingin keluar dari sesi ini?');
    }

    /** Dashboard guru mengandung tombol konfirmasi "Ya, Keluar". */
    public function test_dashboard_guru_mengandung_tombol_ya_keluar(): void
    {
        $guru = Guru::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($guru, 'guru')
            ->get(route('guru.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Ya, Keluar');
    }

    // ── Siswa Dashboard ───────────────────────────────────────────────────────

    /** Dashboard siswa mengandung variabel Alpine showLogoutDialog. */
    public function test_dashboard_siswa_mengandung_show_logout_dialog_variable(): void
    {
        $siswa = Siswa::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($siswa, 'siswa')
            ->get(route('siswa.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('showLogoutDialog', false);
    }

    /** Dashboard siswa mengandung teks konfirmasi "Apakah Anda yakin". */
    public function test_dashboard_siswa_mengandung_teks_konfirmasi_logout(): void
    {
        $siswa = Siswa::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($siswa, 'siswa')
            ->get(route('siswa.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Apakah Anda yakin ingin keluar dari sesi ini?');
    }

    /** Dashboard siswa mengandung tombol konfirmasi "Ya, Keluar". */
    public function test_dashboard_siswa_mengandung_tombol_ya_keluar(): void
    {
        $siswa = Siswa::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($siswa, 'siswa')
            ->get(route('siswa.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Ya, Keluar');
    }
}
