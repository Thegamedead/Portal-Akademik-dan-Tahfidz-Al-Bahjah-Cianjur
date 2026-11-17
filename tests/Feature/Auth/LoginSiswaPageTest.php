<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

/**
 * 12.3 — LoginSiswaPageTest
 *
 * Verifikasi halaman /siswa/login dapat diakses (HTTP 200),
 * mengandung field NIS, field tanggal lahir, dan catatan "Satu NIS untuk satu akun".
 * Validates: Req 3.1
 */
class LoginSiswaPageTest extends TestCase
{
    /** Halaman login siswa dapat diakses tanpa autentikasi. */
    public function test_halaman_login_siswa_dapat_diakses(): void
    {
        $response = $this->get(route('siswa.login'));

        $response->assertStatus(200);
    }

    /** Halaman login siswa mengandung field NIS. */
    public function test_halaman_login_siswa_mengandung_field_nis(): void
    {
        $response = $this->get(route('siswa.login'));

        $response->assertStatus(200);
        // Input dengan wire:model="nis"
        $response->assertSee('wire:model="nis"', false);
    }

    /** Halaman login siswa mengandung field tanggal lahir. */
    public function test_halaman_login_siswa_mengandung_field_tanggal_lahir(): void
    {
        $response = $this->get(route('siswa.login'));

        $response->assertStatus(200);
        // Input dengan wire:model="tanggal_lahir"
        $response->assertSee('wire:model="tanggal_lahir"', false);
        // Label "Tanggal Lahir"
        $response->assertSee('Tanggal Lahir');
    }

    /** Halaman login siswa mengandung teks "Satu NIS untuk satu akun". */
    public function test_halaman_login_siswa_mengandung_teks_satu_nis(): void
    {
        $response = $this->get(route('siswa.login'));

        $response->assertStatus(200);
        $response->assertSee('Satu NIS untuk satu akun');
    }

    /** Halaman login siswa menampilkan judul yang sesuai. */
    public function test_halaman_login_siswa_menampilkan_judul(): void
    {
        $response = $this->get(route('siswa.login'));

        $response->assertStatus(200);
        $response->assertSee('Login Siswa');
    }
}
