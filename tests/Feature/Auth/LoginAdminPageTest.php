<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

/**
 * 12.1 — LoginAdminPageTest
 *
 * Verifikasi halaman /admin/login dapat diakses (HTTP 200),
 * mengandung field username, field password, dan elemen toggle show/hide.
 * Validates: Req 1.1
 */
class LoginAdminPageTest extends TestCase
{
    /** Halaman login admin dapat diakses tanpa autentikasi. */
    public function test_halaman_login_admin_dapat_diakses(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
    }

    /** Halaman login admin mengandung field username. */
    public function test_halaman_login_admin_mengandung_field_username(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
        // Input text dengan wire:model="username"
        $response->assertSee('wire:model="username"', false);
    }

    /** Halaman login admin mengandung field password. */
    public function test_halaman_login_admin_mengandung_field_password(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
        // Input password dengan wire:model="password"
        $response->assertSee('wire:model="password"', false);
    }

    /** Halaman login admin mengandung elemen toggle show/hide password. */
    public function test_halaman_login_admin_mengandung_toggle_show_hide_password(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
        // Tombol toggle menggunakan @click="showPwd = !showPwd" (Alpine.js)
        $response->assertSee('showPwd = !showPwd', false);
    }

    /** Halaman login admin menampilkan judul yang sesuai. */
    public function test_halaman_login_admin_menampilkan_judul(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
        $response->assertSee('Login Admin');
    }
}
