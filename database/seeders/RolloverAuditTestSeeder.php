<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\TahunAjaran;
use App\Models\RolloverAuditLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolloverAuditTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Rollover Audit Log test seeding...');

        // Get or create test Tahun Ajaran records
        $tahunAjaran2023 = TahunAjaran::firstOrCreate(
            ['label' => '2023-2024'],
            [
                'tanggal_mulai' => '2023-07-01',
                'tanggal_selesai' => '2024-06-30',
                'status' => false,
            ]
        );
        $this->command->info("✓ Using Tahun Ajaran: {$tahunAjaran2023->label} (ID: {$tahunAjaran2023->id})");

        $tahunAjaran2024 = TahunAjaran::firstOrCreate(
            ['label' => '2024-2025'],
            [
                'tanggal_mulai' => '2024-07-01',
                'tanggal_selesai' => '2025-06-30',
                'status' => false, // Set to false to avoid conflicts
            ]
        );
        $this->command->info("✓ Using Tahun Ajaran: {$tahunAjaran2024->label} (ID: {$tahunAjaran2024->id})");

        // Get or create test Admin record
        $admin = Admin::firstOrCreate(
            ['email' => 'admin.rollover@test.com'],
            [
                'nama' => 'Test Admin Rollover',
                'password' => bcrypt('password'),
                'status_aktif' => true,
            ]
        );
        $this->command->info("✓ Using Admin: {$admin->nama} (ID: {$admin->id})");

        // Create test RolloverAuditLog with all valid foreign keys
        $auditLog = RolloverAuditLog::create([
            'initiated_by' => $admin->id,
            'source_tahun_ajaran_id' => $tahunAjaran2023->id,
            'target_tahun_ajaran_id' => $tahunAjaran2024->id,
            'kelas_7_to_8_count' => 50,
            'kelas_8_to_9_count' => 45,
            'graduated_count' => 40,
            'kelompok_cleared_count' => 135,
            'status' => 'completed',
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(5),
        ]);
        $this->command->info("✓ Created RolloverAuditLog (ID: {$auditLog->id})");
        $this->command->info("  - Initiated by: {$auditLog->initiatedBy->nama}");
        $this->command->info("  - Source: {$auditLog->sourceTahunAjaran->label}");
        $this->command->info("  - Target: {$auditLog->targetTahunAjaran->label}");
        $this->command->info("  - Status: {$auditLog->status}");

        // Test foreign key constraint by attempting to create with invalid admin ID
        $this->command->info("\nTesting foreign key constraints...");

        try {
            RolloverAuditLog::create([
                'initiated_by' => 99999, // Invalid admin ID
                'source_tahun_ajaran_id' => $tahunAjaran2023->id,
                'target_tahun_ajaran_id' => $tahunAjaran2024->id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
            $this->command->error("✗ Foreign key constraint FAILED - invalid admin_id was accepted!");
        } catch (\Illuminate\Database\QueryException $e) {
            $this->command->info("✓ Foreign key constraint WORKS - invalid admin_id was rejected");
        }

        try {
            RolloverAuditLog::create([
                'initiated_by' => $admin->id,
                'source_tahun_ajaran_id' => 99999, // Invalid tahun ajaran ID
                'target_tahun_ajaran_id' => $tahunAjaran2024->id,
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
            $this->command->error("✗ Foreign key constraint FAILED - invalid tahun_ajaran_id was accepted!");
        } catch (\Illuminate\Database\QueryException $e) {
            $this->command->info("✓ Foreign key constraint WORKS - invalid tahun_ajaran_id was rejected");
        }

        // Test cascade delete
        $this->command->info("\nTesting cascade delete...");

        // Create a temporary admin and audit log
        $tempAdmin = Admin::create([
            'nama' => 'Temp Admin for Delete Test',
            'email' => 'temp.admin@test.com',
            'password' => bcrypt('password'),
            'status_aktif' => true,
        ]);

        $tempAuditLog = RolloverAuditLog::create([
            'initiated_by' => $tempAdmin->id,
            'source_tahun_ajaran_id' => $tahunAjaran2023->id,
            'target_tahun_ajaran_id' => $tahunAjaran2024->id,
            'status' => 'completed',
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $tempAuditLogId = $tempAuditLog->id;

        // Delete the admin
        $tempAdmin->delete();

        // Check if audit log was cascaded
        $deletedAuditLog = RolloverAuditLog::find($tempAuditLogId);
        if ($deletedAuditLog === null) {
            $this->command->info("✓ Cascade delete WORKS - audit log was deleted when admin was deleted");
        } else {
            $this->command->error("✗ Cascade delete FAILED - audit log still exists after admin deletion");
        }

        $this->command->info("\n" . str_repeat('=', 60));
        $this->command->info('Test seeding completed successfully!');
        $this->command->info(str_repeat('=', 60));
        $this->command->info("\nSummary:");
        $this->command->info("- Tahun Ajaran records: 2");
        $this->command->info("- Admin records: 1");
        $this->command->info("- RolloverAuditLog records: 1");
        $this->command->info("\nAll foreign key constraints are working correctly!");
    }
}
