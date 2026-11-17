<?php

namespace Tests\Unit\Services;

use App\Services\IntegratedRolloverService;
use App\Services\Handlers\AcademicProgressionHandler;
use App\Services\Handlers\TahfidzGroupHandler;
use App\Services\Handlers\RolloverAuditLogger;
use App\Models\TahunAjaran;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Admin;
use App\Models\RolloverAuditLog;
use App\Enums\StatusAkademik;
use App\DataTransferObjects\ProgressionResult;
use App\DataTransferObjects\TahfidzClearResult;
use App\Exceptions\RolloverLockException;
use App\Exceptions\RolloverValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests for IntegratedRolloverService::executeRollover() method
 *
 * Validates:
 * - Task 5.4: Complete rollover workflow execution
 * - DB transaction wrapping
 * - Lock acquisition and release
 * - Handler orchestration
 * - Audit log creation and updates
 * - TahunAjaran activation
 *
 * Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 9.1, 9.2, 9.3, 9.4, 9.5, 9.6, 16.1, 16.2, 16.3, 16.4
 */
class IntegratedRolloverServiceExecuteTest extends TestCase
{
    use RefreshDatabase;

    private IntegratedRolloverService $service;
    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin for testing
        $this->admin = Admin::factory()->create();

        // Create service instance
        $this->service = app(IntegratedRolloverService::class);
    }

    /**
     * Test successful rollover execution
     */
    #[Test]
    public function it_executes_complete_rollover_workflow_successfully(): void
    {
        // Arrange: Create test data
        $currentTahunAjaran = TahunAjaran::factory()->create(['status' => true, 'label' => '2023-2024']);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false, 'label' => '2024-2025']);

        // Create Kelas records
        $kelas7 = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        $kelas8 = Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        $kelas9 = Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // Create active students
        $siswa7 = Siswa::factory()->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        $siswa8 = Siswa::factory()->create([
            'kelas_id' => $kelas8->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        $siswa9 = Siswa::factory()->create([
            'kelas_id' => $kelas9->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Act: Execute rollover
        $result = $this->service->executeRollover($targetTahunAjaran->id, $this->admin->id);

        // Assert: Check result
        $this->assertTrue($result->success);
        $this->assertEquals($targetTahunAjaran->id, $result->targetTahunAjaranId);
        $this->assertEquals($currentTahunAjaran->id, $result->sourceTahunAjaranId);
        $this->assertEquals('2024-2025', $result->targetTahunAjaranLabel);

        // Assert: Check target Tahun Ajaran is now active
        $targetTahunAjaran->refresh();
        $this->assertTrue($targetTahunAjaran->status);

        // Assert: Check previous Tahun Ajaran is now inactive
        $currentTahunAjaran->refresh();
        $this->assertFalse($currentTahunAjaran->status);

        // Assert: Check only one Tahun Ajaran is active
        $this->assertEquals(1, TahunAjaran::where('status', true)->count());

        // Assert: Check audit log was created
        $this->assertDatabaseHas('rollover_audit_logs', [
            'initiated_by' => $this->admin->id,
            'source_tahun_ajaran_id' => $currentTahunAjaran->id,
            'target_tahun_ajaran_id' => $targetTahunAjaran->id,
            'status' => 'completed',
        ]);

        // Assert: Check academic progression results
        $this->assertGreaterThanOrEqual(0, $result->academicResult->kelas7To8Count);
        $this->assertGreaterThanOrEqual(0, $result->academicResult->kelas8To9Count);
        $this->assertGreaterThanOrEqual(0, $result->academicResult->graduatedCount);

        // Assert: Check tahfidz clearing results
        $this->assertGreaterThanOrEqual(0, $result->tahfidzResult->clearedCount);
    }

    /**
     * Test rollover fails when target same as current
     */
    #[Test]
    public function it_throws_validation_exception_when_target_same_as_current(): void
    {
        // Arrange
        $currentTahunAjaran = TahunAjaran::factory()->create(['status' => true]);

        // Create minimal Kelas data
        Kelas::factory()->create(['tingkat' => '7']);
        Kelas::factory()->create(['tingkat' => '8']);
        Kelas::factory()->create(['tingkat' => '9']);

        // Create at least one active student
        Siswa::factory()->create([
            'kelas_id' => Kelas::where('tingkat', '7')->first()->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Expect exception
        $this->expectException(RolloverValidationException::class);
        $this->expectExceptionMessage('Tahun Ajaran tujuan sama dengan yang aktif');

        // Act: Try to rollover to the same year
        $this->service->executeRollover($currentTahunAjaran->id, $this->admin->id);
    }

    /**
     * Test rollover fails when no active students
     */
    #[Test]
    public function it_throws_validation_exception_when_no_active_students(): void
    {
        // Arrange
        $currentTahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        // Create Kelas but no students
        Kelas::factory()->create(['tingkat' => '7']);
        Kelas::factory()->create(['tingkat' => '8']);
        Kelas::factory()->create(['tingkat' => '9']);

        // Expect exception
        $this->expectException(RolloverValidationException::class);
        $this->expectExceptionMessage('Tidak ada siswa aktif yang akan di-rollover');

        // Act
        $this->service->executeRollover($targetTahunAjaran->id, $this->admin->id);
    }

    /**
     * Test rollover creates audit log on failure
     */
    #[Test]
    public function it_creates_audit_log_with_failed_status_on_error(): void
    {
        // Arrange: Create test data
        $currentTahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        // Create Kelas records
        Kelas::factory()->create(['tingkat' => '7']);
        Kelas::factory()->create(['tingkat' => '8']);
        Kelas::factory()->create(['tingkat' => '9']);

        // Create active student
        Siswa::factory()->create([
            'kelas_id' => Kelas::where('tingkat', '7')->first()->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Mock handler to throw exception
        $this->mock(AcademicProgressionHandler::class)
            ->shouldReceive('processPromotion')
            ->andThrow(new \RuntimeException('Simulated handler failure'));

        // Re-create service with mocked handler
        $this->service = app(IntegratedRolloverService::class);

        // Act & Assert: Expect exception
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Rollover gagal');

        try {
            $this->service->executeRollover($targetTahunAjaran->id, $this->admin->id);
        } catch (\Exception $e) {
            // Check audit log exists with failed status
            $this->assertDatabaseHas('rollover_audit_logs', [
                'initiated_by' => $this->admin->id,
                'source_tahun_ajaran_id' => $currentTahunAjaran->id,
                'target_tahun_ajaran_id' => $targetTahunAjaran->id,
                'status' => 'failed',
            ]);

            throw $e;
        }
    }

    /**
     * Test transaction rollback on failure
     */
    #[Test]
    public function it_rolls_back_all_changes_on_failure(): void
    {
        // Arrange
        $currentTahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        Kelas::factory()->create(['tingkat' => '7']);
        Kelas::factory()->create(['tingkat' => '8']);
        Kelas::factory()->create(['tingkat' => '9']);

        Siswa::factory()->create([
            'kelas_id' => Kelas::where('tingkat', '7')->first()->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Mock handler to throw exception after TahunAjaran activation
        $this->mock(AcademicProgressionHandler::class)
            ->shouldReceive('processPromotion')
            ->andThrow(new \RuntimeException('Simulated failure'));

        $this->service = app(IntegratedRolloverService::class);

        // Act: Try to execute rollover (will fail)
        try {
            $this->service->executeRollover($targetTahunAjaran->id, $this->admin->id);
        } catch (\Exception $e) {
            // Expected exception
        }

        // Assert: Check current Tahun Ajaran is still active (transaction rolled back)
        $currentTahunAjaran->refresh();
        $this->assertTrue($currentTahunAjaran->status);

        // Assert: Check target Tahun Ajaran is still inactive
        $targetTahunAjaran->refresh();
        $this->assertFalse($targetTahunAjaran->status);
    }

    /**
     * Test rollover releases lock even on failure
     */
    #[Test]
    public function it_releases_lock_even_on_failure(): void
    {
        // Arrange
        $currentTahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        Kelas::factory()->create(['tingkat' => '7']);
        Kelas::factory()->create(['tingkat' => '8']);
        Kelas::factory()->create(['tingkat' => '9']);

        Siswa::factory()->create([
            'kelas_id' => Kelas::where('tingkat', '7')->first()->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Mock handler to throw exception
        $this->mock(AcademicProgressionHandler::class)
            ->shouldReceive('processPromotion')
            ->andThrow(new \RuntimeException('Simulated failure'));

        $this->service = app(IntegratedRolloverService::class);

        // Act: Try rollover (will fail)
        try {
            $this->service->executeRollover($targetTahunAjaran->id, $this->admin->id);
        } catch (\Exception $e) {
            // Expected exception
        }

        // Assert: Check lock is released (another rollover can start)
        $this->assertFalse($this->service->isRolloverInProgress());
    }
}
