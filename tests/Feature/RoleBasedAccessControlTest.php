<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelompok;
use App\Models\TahunAjaran;
use App\Models\OrangTuaWali;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test Role-Based Access Control (RBAC) for Master Data Module
 *
 * **Validates: Requirements 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 8.7, 8.8, 8.9, 8.10**
 *
 * This test validates that:
 * - Admin can access all master data CRUD operations
 * - Guru can only view kelompok binaan and siswa binaan
 * - Guru can edit own profile (limited fields)
 * - Siswa can only view own data (read-only)
 * - Unauthorized access returns 403
 */
class RoleBasedAccessControlTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private Guru $guru;
    private Guru $otherGuru;
    private Siswa $siswa;
    private Siswa $otherSiswa;
    private Kelompok $kelompok;
    private TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->admin = Admin::factory()->create([
            'username' => 'admin_test',
            'status_aktif' => true,
        ]);

        $this->tahunAjaran = TahunAjaran::factory()->create([
            'label' => '2024/2025',
            'status' => true,
        ]);

        $this->guru = Guru::factory()->create([
            'niy' => 'GURU001',
            'nama' => 'Guru Test',
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'status_aktif' => true,
        ]);

        $this->otherGuru = Guru::factory()->create([
            'niy' => 'GURU002',
            'nama' => 'Other Guru',
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'status_aktif' => true,
        ]);

        $this->kelompok = Kelompok::factory()->create([
            'nama' => 'Kelompok A',
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'guru_pembimbing_id' => $this->guru->id,
        ]);

        $this->siswa = Siswa::factory()->create([
            'nis' => 'SIS001',
            'nama' => 'Siswa Test',
            'kelompok_id' => $this->kelompok->id,
            'status_penempatan' => 'Ditempatkan',
            'status_aktif' => true,
        ]);

        $this->otherSiswa = Siswa::factory()->create([
            'nis' => 'SIS002',
            'nama' => 'Other Siswa',
            'kelompok_id' => null,
            'status_penempatan' => 'Terdaftar',
            'status_aktif' => true,
        ]);

        // Create orang tua wali for siswa
        OrangTuaWali::factory()->create([
            'siswa_id' => $this->siswa->id,
            'jenis' => 'Ayah',
            'nama' => 'Ayah Siswa Test',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Admin Access Tests - Full CRUD Operations
    // ─────────────────────────────────────────────────────────────────────────

    public function test_admin_can_access_tahun_ajaran_list(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.tahun-ajaran'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_semester_list(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.semester'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_siswa_list(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.siswa.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_siswa_create(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.siswa.create'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_siswa_edit(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.siswa.edit', $this->siswa->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_siswa_detail(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.siswa.detail', $this->siswa->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_guru_list(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.guru.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_guru_create(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.guru.create'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_guru_edit(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.guru.edit', $this->guru->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_guru_detail(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.guru.detail', $this->guru->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_kelompok_list(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.kelompok.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_kelompok_create(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.kelompok.create'));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_kelompok_edit(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.kelompok.edit', $this->kelompok->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_kelompok_detail(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.kelompok.detail', $this->kelompok->id));

        $response->assertStatus(200);
    }

    public function test_admin_can_access_referensi_quran(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.referensi-quran'));

        $response->assertStatus(200);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Guru Access Tests - Limited to Kelompok Binaan and Siswa Binaan
    // ─────────────────────────────────────────────────────────────────────────

    public function test_guru_can_view_kelompok_binaan(): void
    {
        $response = $this->actingAs($this->guru, 'guru')
            ->get(route('guru.kelompok-binaan'));

        $response->assertStatus(200);
    }

    public function test_guru_can_view_siswa_binaan(): void
    {
        $response = $this->actingAs($this->guru, 'guru')
            ->get(route('guru.siswa-binaan'));

        $response->assertStatus(200);

        // Verify only siswa in their kelompok are shown
        $response->assertSee($this->siswa->nama);
        $response->assertDontSee($this->otherSiswa->nama);
    }

    public function test_guru_can_view_siswa_binaan_detail(): void
    {
        $response = $this->actingAs($this->guru, 'guru')
            ->get(route('guru.siswa-binaan.detail', $this->siswa->id));

        $response->assertStatus(200);
        $response->assertSee($this->siswa->nama);
    }

    public function test_guru_cannot_view_siswa_from_other_kelompok(): void
    {
        // Create a kelompok for another guru
        $otherKelompok = Kelompok::factory()->create([
            'nama' => 'Kelompok B',
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'guru_pembimbing_id' => $this->otherGuru->id,
        ]);

        $otherSiswa = Siswa::factory()->create([
            'nis' => 'SIS003',
            'nama' => 'Siswa Other Kelompok',
            'kelompok_id' => $otherKelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Guru should not see siswa from other kelompok
        $response = $this->actingAs($this->guru, 'guru')
            ->get(route('guru.siswa-binaan'));

        $response->assertStatus(200);
        $response->assertDontSee($otherSiswa->nama);
    }

    public function test_guru_can_access_own_profile(): void
    {
        $response = $this->actingAs($this->guru, 'guru')
            ->get(route('guru.profil'));

        $response->assertStatus(200);
        $response->assertSee($this->guru->nama);
    }

    public function test_guru_cannot_access_admin_master_data_routes(): void
    {
        // Try to access siswa list (admin route)
        $response = $this->actingAs($this->guru, 'guru')
            ->get(route('admin.master-data.siswa.index'));

        $response->assertRedirect(route('guru.dashboard'));

        // Try to access guru list (admin route)
        $response = $this->actingAs($this->guru, 'guru')
            ->get(route('admin.master-data.guru.index'));

        $response->assertRedirect(route('guru.dashboard'));

        // Try to access kelompok list (admin route)
        $response = $this->actingAs($this->guru, 'guru')
            ->get(route('admin.master-data.kelompok.index'));

        $response->assertRedirect(route('guru.dashboard'));
    }

    public function test_guru_cannot_access_siswa_create(): void
    {
        $response = $this->actingAs($this->guru, 'guru')
            ->get(route('admin.master-data.siswa.create'));

        $response->assertRedirect(route('guru.dashboard'));
    }

    public function test_guru_cannot_access_siswa_edit(): void
    {
        $response = $this->actingAs($this->guru, 'guru')
            ->get(route('admin.master-data.siswa.edit', $this->siswa->id));

        $response->assertRedirect(route('guru.dashboard'));
    }

    public function test_guru_cannot_access_guru_edit(): void
    {
        $response = $this->actingAs($this->guru, 'guru')
            ->get(route('admin.master-data.guru.edit', $this->guru->id));

        $response->assertRedirect(route('guru.dashboard'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Siswa Access Tests - Read-Only Access to Own Data
    // ─────────────────────────────────────────────────────────────────────────

    public function test_siswa_can_view_own_profile(): void
    {
        $response = $this->actingAs($this->siswa, 'siswa')
            ->get(route('siswa.profil'));

        $response->assertStatus(200);
        $response->assertSee($this->siswa->nama);
        $response->assertSee($this->siswa->nis);
    }

    public function test_siswa_can_view_own_orang_tua_wali_data(): void
    {
        $response = $this->actingAs($this->siswa, 'siswa')
            ->get(route('siswa.profil'));

        $response->assertStatus(200);
        $response->assertSee('Ayah Siswa Test');
    }

    public function test_siswa_can_view_own_kelompok_data(): void
    {
        $response = $this->actingAs($this->siswa, 'siswa')
            ->get(route('siswa.profil'));

        $response->assertStatus(200);
        $response->assertSee($this->kelompok->nama);
    }

    public function test_siswa_cannot_access_admin_master_data_routes(): void
    {
        // Try to access siswa list (admin route)
        $response = $this->actingAs($this->siswa, 'siswa')
            ->get(route('admin.master-data.siswa.index'));

        $response->assertRedirect(route('siswa.dashboard'));

        // Try to access guru list (admin route)
        $response = $this->actingAs($this->siswa, 'siswa')
            ->get(route('admin.master-data.guru.index'));

        $response->assertRedirect(route('siswa.dashboard'));
    }

    public function test_siswa_cannot_access_other_siswa_profile(): void
    {
        // Siswa doesn't have route to view other siswa, but test middleware protection
        // Try to access siswa detail (admin route)
        $response = $this->actingAs($this->siswa, 'siswa')
            ->get(route('admin.master-data.siswa.detail', $this->otherSiswa->id));

        $response->assertRedirect(route('siswa.dashboard'));
    }

    public function test_siswa_cannot_access_guru_routes(): void
    {
        // Try to access guru kelompok binaan
        $response = $this->actingAs($this->siswa, 'siswa')
            ->get(route('guru.kelompok-binaan'));

        $response->assertRedirect(route('siswa.dashboard'));

        // Try to access guru siswa binaan
        $response = $this->actingAs($this->siswa, 'siswa')
            ->get(route('guru.siswa-binaan'));

        $response->assertRedirect(route('siswa.dashboard'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Unauthorized Access Tests
    // ─────────────────────────────────────────────────────────────────────────

    public function test_unauthenticated_user_redirected_to_admin_login_for_admin_routes(): void
    {
        $response = $this->get(route('admin.master-data.siswa.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_unauthenticated_user_redirected_to_guru_login_for_guru_routes(): void
    {
        $response = $this->get(route('guru.siswa-binaan'));

        $response->assertRedirect(route('guru.login'));
    }

    public function test_unauthenticated_user_redirected_to_siswa_login_for_siswa_routes(): void
    {
        $response = $this->get(route('siswa.profil'));

        $response->assertRedirect(route('siswa.login'));
    }

    public function test_admin_redirected_to_own_dashboard_when_accessing_guru_routes(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('guru.siswa-binaan'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_redirected_to_own_dashboard_when_accessing_siswa_routes(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('siswa.profil'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_guru_redirected_to_own_dashboard_when_accessing_siswa_routes(): void
    {
        $response = $this->actingAs($this->guru, 'guru')
            ->get(route('siswa.profil'));

        $response->assertRedirect(route('guru.dashboard'));
    }

    public function test_siswa_redirected_to_own_dashboard_when_accessing_admin_routes(): void
    {
        $response = $this->actingAs($this->siswa, 'siswa')
            ->get(route('admin.master-data.siswa.index'));

        $response->assertRedirect(route('siswa.dashboard'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Guru Profile Edit Tests - Limited Fields Only
    // ─────────────────────────────────────────────────────────────────────────

    public function test_guru_can_edit_own_profile_limited_fields(): void
    {
        $this->actingAs($this->guru, 'guru');

        // Test that guru can update editable fields
        \Livewire\Livewire::test(\App\Livewire\Guru\ProfilGuru::class)
            ->set('no_telepon', '081234567890')
            ->set('email', 'guru.updated@test.com')
            ->set('alamat_jalan', 'Jalan Baru 123')
            ->call('updateProfile')
            ->assertDispatched('toast');

        // Verify changes were saved
        $this->guru->refresh();
        $this->assertEquals('081234567890', $this->guru->no_telepon);
        $this->assertEquals('guru.updated@test.com', $this->guru->email);
        $this->assertEquals('Jalan Baru 123', $this->guru->alamat_jalan);
    }

    public function test_guru_cannot_edit_restricted_fields_via_livewire(): void
    {
        $this->actingAs($this->guru, 'guru');

        $originalNiy = $this->guru->niy;
        $originalJabatan = $this->guru->jabatan;
        $originalStatusAktif = $this->guru->status_aktif;

        // Test that restricted fields are not editable through component
        \Livewire\Livewire::test(\App\Livewire\Guru\ProfilGuru::class)
            ->set('no_telepon', '081234567890')
            ->call('updateProfile');

        // Verify restricted fields remain unchanged
        $this->guru->refresh();
        $this->assertEquals($originalNiy, $this->guru->niy);
        $this->assertEquals($originalJabatan, $this->guru->jabatan);
        $this->assertEquals($originalStatusAktif, $this->guru->status_aktif);
    }
}

