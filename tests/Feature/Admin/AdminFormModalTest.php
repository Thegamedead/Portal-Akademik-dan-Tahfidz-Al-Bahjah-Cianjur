<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\ManajemenAkunAdmin;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * 12.8 — AdminFormModalTest
 *
 * Verifikasi halaman /admin/manajemen-akun/admin dapat diakses (HTTP 200),
 * mengandung tombol tambah admin, dan modal form punya ≤6 field.
 * Validates: Req 7.4
 */
class AdminFormModalTest extends TestCase
{
    use RefreshDatabase;

    /** Halaman manajemen akun admin dapat diakses oleh admin yang terautentikasi. */
    public function test_halaman_manajemen_akun_admin_dapat_diakses(): void
    {
        $admin = Admin::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.manajemen-akun.admin'));

        $response->assertStatus(200);
    }

    /** Halaman mengandung tombol "Tambah Admin". */
    public function test_halaman_mengandung_tombol_tambah_admin(): void
    {
        $admin = Admin::factory()->create(['status_aktif' => true]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.manajemen-akun.admin'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Admin');
    }

    /**
     * Modal form mengandung tidak lebih dari 6 field input/select.
     *
     * Berdasarkan implementasi, modal punya 4 field:
     * 1. username (formData.username)
     * 2. password (formData.password)
     * 3. password_confirmation (formData.password_confirmation)
     * 4. status_aktif (formData.status_aktif) — radio buttons
     * Total = 4 ≤ 6 ✓
     */
    public function test_modal_tambah_admin_memiliki_tidak_lebih_dari_enam_field(): void
    {
        $admin = Admin::factory()->create(['status_aktif' => true]);

        // Buka modal dengan set showModal = true
        $component = Livewire::actingAs($admin, 'admin')
            ->test(ManajemenAkunAdmin::class)
            ->set('showModal', true);

        $html = $component->html();

        // Hitung field input dan select dalam modal
        $inputCount  = substr_count($html, '<input');
        $selectCount = substr_count($html, '<select');
        $totalFields = $inputCount + $selectCount;

        $this->assertLessThanOrEqual(6, $totalFields,
            "Modal mengandung {$totalFields} field, seharusnya ≤6. "
            . "Pastikan modal sesuai dengan aturan UI design system."
        );
    }

    /** Modal mengandung field username. */
    public function test_modal_tambah_admin_mengandung_field_username(): void
    {
        $admin = Admin::factory()->create(['status_aktif' => true]);

        Livewire::actingAs($admin, 'admin')
            ->test(ManajemenAkunAdmin::class)
            ->set('showModal', true)
            ->assertSee('formData.username', false);
    }

    /** Modal mengandung field password. */
    public function test_modal_tambah_admin_mengandung_field_password(): void
    {
        $admin = Admin::factory()->create(['status_aktif' => true]);

        Livewire::actingAs($admin, 'admin')
            ->test(ManajemenAkunAdmin::class)
            ->set('showModal', true)
            ->assertSee('formData.password', false);
    }

    /** Modal mengandung field status_aktif. */
    public function test_modal_tambah_admin_mengandung_field_status_aktif(): void
    {
        $admin = Admin::factory()->create(['status_aktif' => true]);

        Livewire::actingAs($admin, 'admin')
            ->test(ManajemenAkunAdmin::class)
            ->set('showModal', true)
            ->assertSee('formData.status_aktif', false);
    }

    /** Halaman tidak dapat diakses tanpa autentikasi. */
    public function test_halaman_manajemen_akun_admin_tidak_dapat_diakses_tanpa_auth(): void
    {
        $response = $this->get(route('admin.manajemen-akun.admin'));

        $response->assertRedirect(route('admin.login'));
    }
}
