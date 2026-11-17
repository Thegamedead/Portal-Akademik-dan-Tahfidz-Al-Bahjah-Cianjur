<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\OrangTuaWali;
use App\Services\MasterData\GuruService;
use App\Services\MasterData\SiswaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoftDeleteRestoreTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    // ========================================================================
    // SISWA SOFT DELETE TESTS
    // ========================================================================

    /** @test */
    public function it_soft_deletes_siswa_when_status_changes_to_lulus_smp()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create([
            'status_akademik' => 'Aktif',
            'status_aktif' => true,
        ]);

        $siswaService = app(SiswaService::class);
        $siswaService->updateStatus($siswa->id, 'Lulus SMP');

        // Check soft delete applied
        $this->assertSoftDeleted('siswas', ['id' => $siswa->id]);

        // Check status updated
        $siswaDeleted = Siswa::withTrashed()->find($siswa->id);
        $this->assertEquals('Lulus SMP', $siswaDeleted->status_akademik);
        $this->assertFalse($siswaDeleted->status_aktif);
        $this->assertNotNull($siswaDeleted->deleted_at);
    }

    /** @test */
    public function it_soft_deletes_siswa_when_status_changes_to_pindah_sekolah()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create([
            'status_akademik' => 'Aktif',
            'status_aktif' => true,
        ]);

        $siswaService = app(SiswaService::class);
        $siswaService->updateStatus($siswa->id, 'Pindah Sekolah');

        $this->assertSoftDeleted('siswas', ['id' => $siswa->id]);

        $siswaDeleted = Siswa::withTrashed()->find($siswa->id);
        $this->assertEquals('Pindah Sekolah', $siswaDeleted->status_akademik);
        $this->assertFalse($siswaDeleted->status_aktif);
    }

    /** @test */
    public function it_soft_deletes_siswa_when_status_changes_to_keluar()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create([
            'status_akademik' => 'Aktif',
            'status_aktif' => true,
        ]);

        $siswaService = app(SiswaService::class);
        $siswaService->updateStatus($siswa->id, 'Keluar');

        $this->assertSoftDeleted('siswas', ['id' => $siswa->id]);

        $siswaDeleted = Siswa::withTrashed()->find($siswa->id);
        $this->assertEquals('Keluar', $siswaDeleted->status_akademik);
        $this->assertFalse($siswaDeleted->status_aktif);
    }

    /** @test */
    public function it_does_not_soft_delete_siswa_when_status_remains_aktif()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create([
            'status_akademik' => 'Aktif',
            'status_aktif' => true,
        ]);

        $siswaService = app(SiswaService::class);
        $siswaService->updateStatus($siswa->id, 'Aktif');

        // Should NOT be soft deleted
        $this->assertDatabaseHas('siswas', [
            'id' => $siswa->id,
            'deleted_at' => null,
        ]);

        $siswa->refresh();
        $this->assertTrue($siswa->status_aktif);
        $this->assertNull($siswa->deleted_at);
    }

    /** @test */
    public function it_restores_soft_deleted_siswa()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create([
            'status_akademik' => 'Lulus SMP',
            'status_aktif' => false,
        ]);
        $siswa->delete(); // Soft delete

        $this->assertSoftDeleted('siswas', ['id' => $siswa->id]);

        $siswaService = app(SiswaService::class);
        $restoredSiswa = $siswaService->restore($siswa->id);

        // Check restored
        $this->assertDatabaseHas('siswas', [
            'id' => $siswa->id,
            'deleted_at' => null,
        ]);

        $this->assertNull($restoredSiswa->deleted_at);
        $this->assertTrue($restoredSiswa->status_aktif);
        $this->assertEquals('Aktif', $restoredSiswa->status_akademik);
    }

    /** @test */
    public function it_preserves_orang_tua_wali_when_siswa_is_soft_deleted()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create(['status_akademik' => 'Aktif']);
        $orangTua = OrangTuaWali::factory()->create(['siswa_id' => $siswa->id]);

        $siswaService = app(SiswaService::class);
        $siswaService->updateStatus($siswa->id, 'Lulus SMP');

        $this->assertSoftDeleted('siswas', ['id' => $siswa->id]);

        // OrangTuaWali should still exist (not deleted on soft delete)
        $this->assertDatabaseHas('orang_tua_walis', ['id' => $orangTua->id]);
    }

    /** @test */
    public function it_excludes_soft_deleted_siswa_from_default_queries()
    {
        $this->actingAs($this->admin, 'admin');

        $siswaActive = Siswa::factory()->create(['status_akademik' => 'Aktif']);
        $siswaDeleted = Siswa::factory()->create(['status_akademik' => 'Lulus SMP']);
        $siswaDeleted->delete();

        // Default query should not include soft deleted
        $allSiswa = Siswa::all();
        $this->assertCount(1, $allSiswa);
        $this->assertTrue($allSiswa->contains($siswaActive));
        $this->assertFalse($allSiswa->contains('id', $siswaDeleted->id));

        // WithTrashed should include both
        $allWithTrashed = Siswa::withTrashed()->get();
        $this->assertCount(2, $allWithTrashed);
    }

    // ========================================================================
    // GURU SOFT DELETE TESTS
    // ========================================================================

    /** @test */
    public function it_soft_deletes_guru_when_status_aktif_becomes_false()
    {
        $this->actingAs($this->admin, 'admin');

        $guru = Guru::factory()->create([
            'status_aktif' => true,
            'jabatan' => 'Guru Mapel',
        ]);

        $guruService = app(GuruService::class);
        $guruService->updateStatus($guru->id, false);

        // Check soft delete applied
        $this->assertSoftDeleted('gurus', ['id' => $guru->id]);

        // Check status updated
        $guruDeleted = Guru::withTrashed()->find($guru->id);
        $this->assertFalse($guruDeleted->status_aktif);
        $this->assertNotNull($guruDeleted->deleted_at);
    }

    /** @test */
    public function it_does_not_soft_delete_guru_when_status_remains_active()
    {
        $this->actingAs($this->admin, 'admin');

        $guru = Guru::factory()->create([
            'status_aktif' => true,
            'jabatan' => 'Guru Mapel',
        ]);

        $guruService = app(GuruService::class);
        $guruService->updateStatus($guru->id, true);

        // Should NOT be soft deleted
        $this->assertDatabaseHas('gurus', [
            'id' => $guru->id,
            'deleted_at' => null,
        ]);

        $guru->refresh();
        $this->assertTrue($guru->status_aktif);
        $this->assertNull($guru->deleted_at);
    }

    /** @test */
    public function it_restores_soft_deleted_guru()
    {
        $this->actingAs($this->admin, 'admin');

        $guru = Guru::factory()->create([
            'status_aktif' => false,
            'jabatan' => 'Guru Mapel',
        ]);
        $guru->delete(); // Soft delete

        $this->assertSoftDeleted('gurus', ['id' => $guru->id]);

        $guruService = app(GuruService::class);
        $restoredGuru = $guruService->restore($guru->id);

        // Check restored
        $this->assertDatabaseHas('gurus', [
            'id' => $guru->id,
            'deleted_at' => null,
        ]);

        $this->assertNull($restoredGuru->deleted_at);
        $this->assertTrue($restoredGuru->status_aktif);
    }

    /** @test */
    public function it_excludes_soft_deleted_guru_from_default_queries()
    {
        $this->actingAs($this->admin, 'admin');

        $guruActive = Guru::factory()->create(['status_aktif' => true]);
        $guruDeleted = Guru::factory()->create(['status_aktif' => false]);
        $guruDeleted->delete();

        // Default query should not include soft deleted
        $allGuru = Guru::all();
        $this->assertCount(1, $allGuru);
        $this->assertTrue($allGuru->contains($guruActive));
        $this->assertFalse($allGuru->contains('id', $guruDeleted->id));

        // WithTrashed should include both
        $allWithTrashed = Guru::withTrashed()->get();
        $this->assertCount(2, $allWithTrashed);
    }

    // ========================================================================
    // REQUIREMENT 9.8 & 9.9 TESTS
    // ========================================================================

    /** @test */
    public function it_sets_deleted_at_to_non_null_on_soft_delete()
    {
        $this->actingAs($this->admin, 'admin');

        // Test Siswa
        $siswa = Siswa::factory()->create(['status_akademik' => 'Aktif']);
        $siswaService = app(SiswaService::class);
        $siswaService->updateStatus($siswa->id, 'Lulus SMP');

        $siswaDeleted = Siswa::withTrashed()->find($siswa->id);
        $this->assertNotNull($siswaDeleted->deleted_at);

        // Test Guru
        $guru = Guru::factory()->create(['status_aktif' => true]);
        $guruService = app(GuruService::class);
        $guruService->updateStatus($guru->id, false);

        $guruDeleted = Guru::withTrashed()->find($guru->id);
        $this->assertNotNull($guruDeleted->deleted_at);
    }

    /** @test */
    public function it_sets_deleted_at_to_null_on_restore()
    {
        $this->actingAs($this->admin, 'admin');

        // Test Siswa
        $siswa = Siswa::factory()->create(['status_akademik' => 'Lulus SMP']);
        $siswa->delete();
        $this->assertNotNull(Siswa::withTrashed()->find($siswa->id)->deleted_at);

        $siswaService = app(SiswaService::class);
        $restoredSiswa = $siswaService->restore($siswa->id);
        $this->assertNull($restoredSiswa->deleted_at);

        // Test Guru
        $guru = Guru::factory()->create(['status_aktif' => false]);
        $guru->delete();
        $this->assertNotNull(Guru::withTrashed()->find($guru->id)->deleted_at);

        $guruService = app(GuruService::class);
        $restoredGuru = $guruService->restore($guru->id);
        $this->assertNull($restoredGuru->deleted_at);
    }

    // ========================================================================
    // QUERY SCOPE TESTS
    // ========================================================================

    /** @test */
    public function it_can_query_only_trashed_records()
    {
        $this->actingAs($this->admin, 'admin');

        $siswaActive = Siswa::factory()->create(['status_akademik' => 'Aktif']);
        $siswaDeleted = Siswa::factory()->create(['status_akademik' => 'Lulus SMP']);
        $siswaDeleted->delete();

        // onlyTrashed should return only soft deleted
        $trashedSiswa = Siswa::onlyTrashed()->get();
        $this->assertCount(1, $trashedSiswa);
        $this->assertEquals($siswaDeleted->id, $trashedSiswa->first()->id);
    }

    /** @test */
    public function it_can_query_with_trashed_records()
    {
        $this->actingAs($this->admin, 'admin');

        $guruActive = Guru::factory()->create(['status_aktif' => true]);
        $guruDeleted = Guru::factory()->create(['status_aktif' => false]);
        $guruDeleted->delete();

        // withTrashed should return all records
        $allGuru = Guru::withTrashed()->get();
        $this->assertCount(2, $allGuru);
    }

    // ========================================================================
    // EDGE CASE TESTS
    // ========================================================================

    /** @test */
    public function it_prevents_double_soft_delete()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create(['status_akademik' => 'Aktif']);

        $siswaService = app(SiswaService::class);
        $siswaService->updateStatus($siswa->id, 'Lulus SMP');

        $firstDeletedAt = Siswa::withTrashed()->find($siswa->id)->deleted_at;

        // Try to soft delete again
        sleep(1); // Ensure different timestamp if it were to update
        $siswaService->updateStatus($siswa->id, 'Pindah Sekolah');

        $secondDeletedAt = Siswa::withTrashed()->find($siswa->id)->deleted_at;

        // deleted_at should remain the same (not updated)
        $this->assertEquals($firstDeletedAt->timestamp, $secondDeletedAt->timestamp);
    }

    /** @test */
    public function it_can_restore_multiple_times()
    {
        $this->actingAs($this->admin, 'admin');

        $guru = Guru::factory()->create(['status_aktif' => true]);
        $guruService = app(GuruService::class);

        // First soft delete and restore
        $guruService->updateStatus($guru->id, false);
        $this->assertSoftDeleted('gurus', ['id' => $guru->id]);

        $guruService->restore($guru->id);
        $this->assertDatabaseHas('gurus', ['id' => $guru->id, 'deleted_at' => null]);

        // Second soft delete and restore
        $guruService->updateStatus($guru->id, false);
        $this->assertSoftDeleted('gurus', ['id' => $guru->id]);

        $guruService->restore($guru->id);
        $this->assertDatabaseHas('gurus', ['id' => $guru->id, 'deleted_at' => null]);

        // Guru should be active after both restores
        $guru->refresh();
        $this->assertTrue($guru->status_aktif);
        $this->assertNull($guru->deleted_at);
    }
}
