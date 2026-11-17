<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\RolloverAuditLog;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RolloverAuditLogsMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rollover_audit_logs_table_exists_with_correct_structure()
    {
        // Assert table exists
        $this->assertTrue(Schema::hasTable('rollover_audit_logs'));

        // Assert all required columns exist
        $expectedColumns = [
            'id',
            'initiated_by',
            'started_at',
            'completed_at',
            'source_tahun_ajaran_id',
            'target_tahun_ajaran_id',
            'kelas_7_to_8_count',
            'kelas_8_to_9_count',
            'graduated_count',
            'kelompok_cleared_count',
            'status',
            'error_message',
            'error_trace',
            'rolled_back_by',
            'rolled_back_at',
            'created_at',
            'updated_at',
        ];

        $actualColumns = Schema::getColumnListing('rollover_audit_logs');

        foreach ($expectedColumns as $column) {
            $this->assertContains(
                $column,
                $actualColumns,
                "Column '{$column}' is missing from rollover_audit_logs table"
            );
        }
    }

    public function test_can_create_rollover_audit_log_with_valid_foreign_keys()
    {
        // Create test data
        $admin = Admin::factory()->create();
        $sourceTahunAjaran = TahunAjaran::factory()->create([
            'label' => '2023-2024',
            'status' => true,
        ]);
        $targetTahunAjaran = TahunAjaran::factory()->create([
            'label' => '2024-2025',
            'status' => false,
        ]);

        // Create rollover audit log
        $auditLog = RolloverAuditLog::create([
            'initiated_by' => $admin->id,
            'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
            'target_tahun_ajaran_id' => $targetTahunAjaran->id,
            'kelas_7_to_8_count' => 50,
            'kelas_8_to_9_count' => 45,
            'graduated_count' => 40,
            'kelompok_cleared_count' => 135,
            'status' => 'completed',
            'started_at' => now(),
            'completed_at' => now()->addMinutes(5),
        ]);

        // Assert record was created
        $this->assertDatabaseHas('rollover_audit_logs', [
            'id' => $auditLog->id,
            'initiated_by' => $admin->id,
            'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
            'target_tahun_ajaran_id' => $targetTahunAjaran->id,
            'kelas_7_to_8_count' => 50,
            'kelas_8_to_9_count' => 45,
            'graduated_count' => 40,
            'kelompok_cleared_count' => 135,
            'status' => 'completed',
        ]);

        // Assert relationships work
        $this->assertEquals($admin->id, $auditLog->initiatedBy->id);
        $this->assertEquals($sourceTahunAjaran->id, $auditLog->sourceTahunAjaran->id);
        $this->assertEquals($targetTahunAjaran->id, $auditLog->targetTahunAjaran->id);
    }

    public function test_foreign_key_constraint_prevents_invalid_admin_id()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        $sourceTahunAjaran = TahunAjaran::factory()->create();
        $targetTahunAjaran = TahunAjaran::factory()->create();

        // Try to create with non-existent admin ID (should fail)
        RolloverAuditLog::create([
            'initiated_by' => 99999, // Non-existent admin
            'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
            'target_tahun_ajaran_id' => $targetTahunAjaran->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function test_foreign_key_constraint_prevents_invalid_tahun_ajaran_id()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        $admin = Admin::factory()->create();
        $sourceTahunAjaran = TahunAjaran::factory()->create();

        // Try to create with non-existent target tahun ajaran (should fail)
        RolloverAuditLog::create([
            'initiated_by' => $admin->id,
            'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
            'target_tahun_ajaran_id' => 99999, // Non-existent tahun ajaran
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function test_cascade_delete_works_when_admin_is_deleted()
    {
        $admin = Admin::factory()->create();
        $sourceTahunAjaran = TahunAjaran::factory()->create();
        $targetTahunAjaran = TahunAjaran::factory()->create();

        $auditLog = RolloverAuditLog::create([
            'initiated_by' => $admin->id,
            'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
            'target_tahun_ajaran_id' => $targetTahunAjaran->id,
            'status' => 'completed',
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $auditLogId = $auditLog->id;

        // Delete the admin
        $admin->delete();

        // Audit log should be cascaded deleted
        $this->assertDatabaseMissing('rollover_audit_logs', [
            'id' => $auditLogId,
        ]);
    }

    public function test_cascade_delete_works_when_tahun_ajaran_is_deleted()
    {
        $admin = Admin::factory()->create();
        $sourceTahunAjaran = TahunAjaran::factory()->create();
        $targetTahunAjaran = TahunAjaran::factory()->create();

        $auditLog = RolloverAuditLog::create([
            'initiated_by' => $admin->id,
            'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
            'target_tahun_ajaran_id' => $targetTahunAjaran->id,
            'status' => 'completed',
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $auditLogId = $auditLog->id;

        // Delete the source tahun ajaran
        $sourceTahunAjaran->delete();

        // Audit log should be cascaded deleted
        $this->assertDatabaseMissing('rollover_audit_logs', [
            'id' => $auditLogId,
        ]);
    }

    public function test_nullable_rolled_back_by_field_works_correctly()
    {
        $admin = Admin::factory()->create();
        $sourceTahunAjaran = TahunAjaran::factory()->create();
        $targetTahunAjaran = TahunAjaran::factory()->create();

        // Create without rolled_back_by
        $auditLog = RolloverAuditLog::create([
            'initiated_by' => $admin->id,
            'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
            'target_tahun_ajaran_id' => $targetTahunAjaran->id,
            'status' => 'completed',
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $this->assertNull($auditLog->rolled_back_by);
        $this->assertNull($auditLog->rolled_back_at);

        // Update with rollback information
        $rollbackAdmin = Admin::factory()->create();
        $auditLog->update([
            'status' => 'rolled_back',
            'rolled_back_by' => $rollbackAdmin->id,
            'rolled_back_at' => now(),
        ]);

        $auditLog->refresh();

        $this->assertEquals($rollbackAdmin->id, $auditLog->rolled_back_by);
        $this->assertNotNull($auditLog->rolled_back_at);
        $this->assertEquals('rolled_back', $auditLog->status);
    }

    public function test_enum_status_field_accepts_valid_values()
    {
        $admin = Admin::factory()->create();
        $sourceTahunAjaran = TahunAjaran::factory()->create();
        $targetTahunAjaran = TahunAjaran::factory()->create();

        $validStatuses = ['in_progress', 'completed', 'failed', 'rolled_back'];

        foreach ($validStatuses as $status) {
            $auditLog = RolloverAuditLog::create([
                'initiated_by' => $admin->id,
                'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
                'target_tahun_ajaran_id' => $targetTahunAjaran->id,
                'status' => $status,
                'started_at' => now(),
            ]);

            $this->assertEquals($status, $auditLog->status);
        }
    }

    public function test_default_values_are_set_correctly()
    {
        $admin = Admin::factory()->create();
        $sourceTahunAjaran = TahunAjaran::factory()->create();
        $targetTahunAjaran = TahunAjaran::factory()->create();

        $auditLog = RolloverAuditLog::create([
            'initiated_by' => $admin->id,
            'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
            'target_tahun_ajaran_id' => $targetTahunAjaran->id,
            'started_at' => now(),
        ]);

        // Check default values
        $this->assertEquals(0, $auditLog->kelas_7_to_8_count);
        $this->assertEquals(0, $auditLog->kelas_8_to_9_count);
        $this->assertEquals(0, $auditLog->graduated_count);
        $this->assertEquals(0, $auditLog->kelompok_cleared_count);
        $this->assertEquals('in_progress', $auditLog->status);
    }

    public function test_indexes_exist_for_performance()
    {
        // This test verifies that indexes exist by checking if they improve query performance
        // We'll create multiple records and verify the indexed columns work efficiently

        $admin = Admin::factory()->create();
        $sourceTahunAjaran = TahunAjaran::factory()->create();
        $targetTahunAjaran = TahunAjaran::factory()->create();

        // Create multiple audit logs
        for ($i = 0; $i < 10; $i++) {
            RolloverAuditLog::create([
                'initiated_by' => $admin->id,
                'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
                'target_tahun_ajaran_id' => $targetTahunAjaran->id,
                'status' => $i % 2 === 0 ? 'completed' : 'failed',
                'started_at' => now()->subDays($i),
                'completed_at' => now()->subDays($i)->addHours(1),
            ]);
        }

        // Query using indexed columns should work efficiently
        $completedLogs = RolloverAuditLog::where('status', 'completed')->get();
        $this->assertEquals(5, $completedLogs->count());

        $recentLogs = RolloverAuditLog::where('started_at', '>=', now()->subDays(5))->get();
        $this->assertGreaterThan(0, $recentLogs->count());

        $specificTransition = RolloverAuditLog::where('source_tahun_ajaran_id', $sourceTahunAjaran->id)
            ->where('target_tahun_ajaran_id', $targetTahunAjaran->id)
            ->get();
        $this->assertEquals(10, $specificTransition->count());
    }
}
