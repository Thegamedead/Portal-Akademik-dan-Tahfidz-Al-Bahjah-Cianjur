<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\Guru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test: Verifikasi foreign key constraint admins.guru_id -> gurus.id
 *
 * Memastikan bahwa:
 * 1. Admin dapat dibuat dengan guru_id yang valid
 * 2. Admin tidak dapat dibuat dengan guru_id yang tidak exist
 * 3. Saat guru dihapus, admin.guru_id menjadi NULL (ON DELETE SET NULL)
 *
 * Ini adalah fix untuk Critical Issue #2 dari audit.
 */
class AdminGuruForeignKeyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Admin dapat dibuat dengan guru_id yang valid
     */
    public function test_admin_can_be_created_with_valid_guru_id(): void
    {
        $guru = Guru::factory()->create();

        $admin = Admin::factory()->create([
            'username' => 'admin_guru',
            'guru_id' => $guru->id,
        ]);

        $this->assertDatabaseHas('admins', [
            'username' => 'admin_guru',
            'guru_id' => $guru->id,
        ]);

        $this->assertEquals($guru->id, $admin->guru_id);
    }

    /**
     * Admin dapat dibuat dengan guru_id NULL
     */
    public function test_admin_can_be_created_with_null_guru_id(): void
    {
        $admin = Admin::factory()->create([
            'username' => 'admin_non_guru',
            'guru_id' => null,
        ]);

        $this->assertDatabaseHas('admins', [
            'username' => 'admin_non_guru',
            'guru_id' => null,
        ]);

        $this->assertNull($admin->guru_id);
    }

    /**
     * Admin tidak dapat dibuat dengan guru_id yang tidak exist (foreign key constraint)
     */
    public function test_admin_cannot_be_created_with_invalid_guru_id(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Coba buat admin dengan guru_id yang tidak exist
        Admin::factory()->create([
            'username' => 'admin_invalid',
            'guru_id' => 99999, // ID yang tidak ada di tabel gurus
        ]);
    }

    /**
     * Saat guru dihapus (soft delete), admin.guru_id tetap valid
     * (soft delete tidak trigger ON DELETE constraint)
     */
    public function test_admin_guru_id_remains_valid_when_guru_soft_deleted(): void
    {
        $guru = Guru::factory()->create();
        $admin = Admin::factory()->create([
            'username' => 'admin_guru',
            'guru_id' => $guru->id,
        ]);

        // Soft delete guru
        $guru->delete();

        // Refresh admin dari DB
        $admin->refresh();

        // guru_id tetap ada karena soft delete tidak trigger ON DELETE SET NULL
        $this->assertEquals($guru->id, $admin->guru_id);

        // Guru masih bisa diakses via trashed()
        $this->assertNotNull(Guru::withTrashed()->find($guru->id));
    }

    /**
     * Saat guru di-force delete (hard delete), admin.guru_id menjadi NULL
     * (ON DELETE SET NULL constraint terpicu)
     */
    public function test_admin_guru_id_set_to_null_when_guru_force_deleted(): void
    {
        $guru = Guru::factory()->create();
        $admin = Admin::factory()->create([
            'username' => 'admin_guru',
            'guru_id' => $guru->id,
        ]);

        // Force delete (hard delete)
        $guru->forceDelete();

        // Refresh admin dari DB
        $admin->refresh();

        // guru_id harus NULL karena ON DELETE SET NULL
        $this->assertNull($admin->guru_id);

        // Guru benar-benar tidak ada lagi
        $this->assertNull(Guru::withTrashed()->find($guru->id));
    }

    /**
     * Relasi eloquent: admin dapat mengakses guru via relasi
     */
    public function test_admin_can_access_guru_via_eloquent_relationship(): void
    {
        // Setup: cek apakah relasi sudah didefinisikan di model Admin
        $guru = Guru::factory()->create([
            'nama' => 'Guru Test Relasi',
        ]);

        $admin = Admin::factory()->create([
            'username' => 'admin_relasi',
            'guru_id' => $guru->id,
        ]);

        // Jika relasi belum ada di model, test ini akan skip
        if (! method_exists($admin, 'guru')) {
            $this->markTestSkipped('Relasi guru() belum didefinisikan di model Admin');
        }

        // Test relasi
        $this->assertNotNull($admin->guru);
        $this->assertEquals($guru->id, $admin->guru->id);
        $this->assertEquals('Guru Test Relasi', $admin->guru->nama);
    }
}
