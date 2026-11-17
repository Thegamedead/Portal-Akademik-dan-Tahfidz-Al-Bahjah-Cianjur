<?php

namespace Tests\Feature\Navigation;

use App\Models\Admin;
use App\Models\Guru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsulanKenaikanTingkatNavigationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin can access usulan kenaikan tingkat page
     */
    public function test_admin_can_access_usulan_kenaikan_tingkat_page(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.usulan-kenaikan-tingkat.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(\App\Livewire\Admin\UsulanKenaikanTingkat\IndexUsulan::class);
    }

    /**
     * Test guru can access usulan kenaikan tingkat page
     */
    public function test_guru_can_access_usulan_kenaikan_tingkat_page(): void
    {
        $guru = Guru::factory()->create();

        $response = $this->actingAs($guru, 'guru')
            ->get(route('guru.usulan-kenaikan-tingkat.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire(\App\Livewire\Guru\UsulanKenaikanTingkat\IndexUsulan::class);
    }

    /**
     * Test admin sidebar contains usulan kenaikan tingkat link
     */
    public function test_admin_sidebar_contains_usulan_kenaikan_tingkat_link(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Usulan Kenaikan Tingkat', false);
    }

    /**
     * Test guru sidebar contains usulan kenaikan tingkat link
     */
    public function test_guru_sidebar_contains_usulan_kenaikan_tingkat_link(): void
    {
        $guru = Guru::factory()->create();

        $response = $this->actingAs($guru, 'guru')
            ->get(route('guru.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Usulan Kenaikan Tingkat', false);
    }

    /**
     * Test guest cannot access admin usulan kenaikan tingkat page
     */
    public function test_guest_cannot_access_admin_usulan_kenaikan_tingkat(): void
    {
        $response = $this->get(route('admin.usulan-kenaikan-tingkat.index'));

        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test guest cannot access guru usulan kenaikan tingkat page
     */
    public function test_guest_cannot_access_guru_usulan_kenaikan_tingkat(): void
    {
        $response = $this->get(route('guru.usulan-kenaikan-tingkat.index'));

        $response->assertRedirect(route('guru.login'));
    }
}
