<?php

namespace Tests\Unit\Models;

use App\Models\RolloverAuditLog;
use App\Models\Admin;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolloverAuditLogTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test RolloverAuditLog model can be created with required fields
     */
    public function test_can_create_rollover_audit_log(): void
    {
        // Create necessary related models
        $admin = Admin::factory()->create();
        $sourceTahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        // Create RolloverAuditLog
        $log = RolloverAuditLog::create([
            'initiated_by' => $admin->id,
            'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
            'target_tahun_ajaran_id' => $targetTahunAjaran->id,
            'kelas_7_to_8_count' => 50,
            'kelas_8_to_9_count' => 45,
            'graduated_count' => 40,
            'kelompok_cleared_count' => 135,
            'status' => 'completed',
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        // Assert model was created
        $this->assertDatabaseHas('rollover_audit_logs', [
            'id' => $log->id,
            'initiated_by' => $admin->id,
            'status' => 'completed',
        ]);
    }

    /**
     * Test relationships are properly defined
     */
    public function test_relationships_work(): void
    {
        $admin = Admin::factory()->create();
        $sourceTahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        $log = RolloverAuditLog::create([
            'initiated_by' => $admin->id,
            'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
            'target_tahun_ajaran_id' => $targetTahunAjaran->id,
            'started_at' => now(),
        ]);

        // Test relationships
        $this->assertInstanceOf(Admin::class, $log->initiatedBy);
        $this->assertEquals($admin->id, $log->initiatedBy->id);
        $this->assertInstanceOf(TahunAjaran::class, $log->sourceTahunAjaran);
        $this->assertInstanceOf(TahunAjaran::class, $log->targetTahunAjaran);
    }

    /**
     * Test getTotalAffectedAttribute calculates correctly
     */
    public function test_total_affected_attribute(): void
    {
        $admin = Admin::factory()->create();
        $sourceTahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        $log = RolloverAuditLog::create([
            'initiated_by' => $admin->id,
            'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
            'target_tahun_ajaran_id' => $targetTahunAjaran->id,
            'kelas_7_to_8_count' => 10,
            'kelas_8_to_9_count' => 20,
            'graduated_count' => 30,
            'kelompok_cleared_count' => 40,
            'started_at' => now(),
        ]);

        $this->assertEquals(100, $log->totalAffected);
    }

    /**
     * Test canRollback method returns true for recent completed rollover
     */
    public function test_can_rollback_recent_completed(): void
    {
        $admin = Admin::factory()->create();
        $sourceTahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        $log = RolloverAuditLog::create([
            'initiated_by' => $admin->id,
            'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
            'target_tahun_ajaran_id' => $targetTahunAjaran->id,
            'status' => 'completed',
            'started_at' => now()->subHours(2),
            'completed_at' => now()->subHours(1),
        ]);

        $this->assertTrue($log->canRollback());
    }

    /**
     * Test canRollback method returns false for old rollover
     */
    public function test_cannot_rollback_old_rollover(): void
    {
        $admin = Admin::factory()->create();
        $sourceTahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        $log = RolloverAuditLog::create([
            'initiated_by' => $admin->id,
            'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
            'target_tahun_ajaran_id' => $targetTahunAjaran->id,
            'status' => 'completed',
            'started_at' => now()->subHours(26),
            'completed_at' => now()->subHours(25),
        ]);

        $this->assertFalse($log->canRollback());
    }

    /**
     * Test canRollback returns false for failed rollover
     */
    public function test_cannot_rollback_failed_rollover(): void
    {
        $admin = Admin::factory()->create();
        $sourceTahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        $log = RolloverAuditLog::create([
            'initiated_by' => $admin->id,
            'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
            'target_tahun_ajaran_id' => $targetTahunAjaran->id,
            'status' => 'failed',
            'started_at' => now()->subHours(1),
            'error_message' => 'Test error',
        ]);

        $this->assertFalse($log->canRollback());
    }

    /**
     * Test status helper methods
     */
    public function test_status_helper_methods(): void
    {
        $admin = Admin::factory()->create();
        $sourceTahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        $log = RolloverAuditLog::create([
            'initiated_by' => $admin->id,
            'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
            'target_tahun_ajaran_id' => $targetTahunAjaran->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $this->assertTrue($log->isInProgress());
        $this->assertFalse($log->isCompleted());
        $this->assertFalse($log->isFailed());
        $this->assertFalse($log->isRolledBack());

        $log->update(['status' => 'completed', 'completed_at' => now()]);
        $this->assertFalse($log->isInProgress());
        $this->assertTrue($log->isCompleted());
    }
}
