<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 12.9 — ProfilFormTest
 *
 * Verifikasi form ganti kredensial ada di halaman profil masing-masing guard
 * (bukan menu terpisah).
 * Validates: Req 9.3
 */
class ProfilFormTest extends TestCase
{
    use RefreshDatabase;

    // ── Admin Profil ──────────────────────────────────────────────────────────

    /** Halaman profil admin dapat diakses. */
    public function test_halaman_profil_admin_dapat_diakses(): void
    {
        $admin = Admin::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.profil'));

        $response->assertStatus(200);
    }

    /** Halaman profil admin mengandung field ganti password lama. */
    public function test_profil_admin_mengandung_field_password_lama(): void
    {
        $admin = Admin::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.profil'));

        $response->assertStatus(200);
        // wire:model="passwordLama" ada di profil-admin.blade.php
        $response->assertSee('wire:model="passwordLama"', false);
    }

    /** Halaman profil admin mengandung field ganti password baru. */
    public function test_profil_admin_mengandung_field_password_baru(): void
    {
        $admin = Admin::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.profil'));

        $response->assertStatus(200);
        // wire:model="passwordBaru" ada di profil-admin.blade.php
        $response->assertSee('wire:model="passwordBaru"', false);
    }

    /** Halaman profil admin mengandung tombol simpan password. */
    public function test_profil_admin_mengandung_tombol_simpan_password(): void
    {
        $admin = Admin::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.profil'));

        $response->assertStatus(200);
        $response->assertSee('Ganti Password');
    }

    // ── Guru Profil ──────────────────────────────────────────────────────────

    /** Halaman profil guru dapat diakses. */
    public function test_halaman_profil_guru_dapat_diakses(): void
    {
        $guru = Guru::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($guru, 'guru')
            ->get(route('guru.profil'));

        $response->assertStatus(200);
    }

    /** Halaman profil guru mengandung field tanggal lahir saat ini (untuk verifikasi). */
    public function test_profil_guru_mengandung_field_tanggal_lahir_lama(): void
    {
        $guru = Guru::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($guru, 'guru')
            ->get(route('guru.profil'));

        $response->assertStatus(200);
        // wire:model="tanggalLahirLama" ada di profil-guru.blade.php
        $response->assertSee('wire:model="tanggalLahirLama"', false);
    }

    /** Halaman profil guru mengandung field tanggal lahir baru. */
    public function test_profil_guru_mengandung_field_tanggal_lahir_baru(): void
    {
        $guru = Guru::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($guru, 'guru')
            ->get(route('guru.profil'));

        $response->assertStatus(200);
        // wire:model="tanggalLahirBaru" ada di profil-guru.blade.php
        $response->assertSee('wire:model="tanggalLahirBaru"', false);
    }

    /** Halaman profil guru mengandung teks "Ganti Kredensial Login". */
    public function test_profil_guru_mengandung_teks_ganti_kredensial(): void
    {
        $guru = Guru::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($guru, 'guru')
            ->get(route('guru.profil'));

        $response->assertStatus(200);
        $response->assertSee('Ganti Kredensial Login');
    }

    // ── Siswa Profil ──────────────────────────────────────────────────────────

    /** Halaman profil siswa dapat diakses. */
    public function test_halaman_profil_siswa_dapat_diakses(): void
    {
        $siswa = Siswa::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($siswa, 'siswa')
            ->get(route('siswa.profil'));

        $response->assertStatus(200);
    }

    /** Halaman profil siswa mengandung field tanggal lahir saat ini. */
    public function test_profil_siswa_mengandung_field_tanggal_lahir_lama(): void
    {
        $siswa = Siswa::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($siswa, 'siswa')
            ->get(route('siswa.profil'));

        $response->assertStatus(200);
        // wire:model="tanggalLahirLama" ada di profil-siswa.blade.php
        $response->assertSee('wire:model="tanggalLahirLama"', false);
    }

    /** Halaman profil siswa mengandung field tanggal lahir baru. */
    public function test_profil_siswa_mengandung_field_tanggal_lahir_baru(): void
    {
        $siswa = Siswa::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($siswa, 'siswa')
            ->get(route('siswa.profil'));

        $response->assertStatus(200);
        // wire:model="tanggalLahirBaru" ada di profil-siswa.blade.php
        $response->assertSee('wire:model="tanggalLahirBaru"', false);
    }

    /** Halaman profil siswa mengandung teks "Ganti Kredensial Login". */
    public function test_profil_siswa_mengandung_teks_ganti_kredensial(): void
    {
        $siswa = Siswa::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($siswa, 'siswa')
            ->get(route('siswa.profil'));

        $response->assertStatus(200);
        $response->assertSee('Ganti Kredensial Login');
    }
}
