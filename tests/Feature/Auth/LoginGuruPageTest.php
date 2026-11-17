<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

/**
 * 12.2 — LoginGuruPageTest
 *
 * Verifikasi halaman /guru/login dapat diakses (HTTP 200),
 * mengandung field NIY, field tanggal lahir, dan link "Hubungi Admin".
 * Validates: Req 2.1
 */
class LoginGuruPageTest extends TestCase
{
    /** Halaman login guru dapat diakses tanpa autentikasi. */
    public function test_halaman_login_guru_dapat_diakses(): void
    {
        $response = $this->get(route('guru.login'));

        $response->assertStatus(200);
    }

    /** Halaman login guru mengandung field NIY. */
    public function test_halaman_login_guru_mengandung_field_niy(): void
    {
        $response = $this->get(route('guru.login'));

        $response->assertStatus(200);
        // Input dengan wire:model="niy"
        $response->assertSee('wire:model="niy"', false);
    }

    /** Halaman login guru mengandung field tanggal lahir. */
    public function test_halaman_login_guru_mengandung_field_tanggal_lahir(): void
    {
        $response = $this->get(route('guru.login'));

        $response->assertStatus(200);
        // Input dengan wire:model="tanggal_lahir"
        $response->assertSee('wire:model="tanggal_lahir"', false);
        // Label "Tanggal Lahir"
        $response->assertSee('Tanggal Lahir');
    }

    /** Halaman login guru mengandung teks "Hubungi Admin". */
    public function test_halaman_login_guru_mengandung_teks_hubungi_admin(): void
    {
        $response = $this->get(route('guru.login'));

        $response->assertStatus(200);
        $response->assertSee('Hubungi Admin');
    }

    /** Halaman login guru menampilkan judul yang sesuai. */
    public function test_halaman_login_guru_menampilkan_judul(): void
    {
        $response = $this->get(route('guru.login'));

        $response->assertStatus(200);
        $response->assertSee('Login Guru');
    }
}
