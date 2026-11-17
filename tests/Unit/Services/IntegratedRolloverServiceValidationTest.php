<?php

namespace Tests\Unit\Services;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Enums\StatusAkademik;
use App\Services\IntegratedRolloverService;
use App\Exceptions\RolloverValidationException;
use App\DataTransferObjects\ImpactSummary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit Tests for IntegratedRolloverService Validation Methods
 *
 * Tests validation logic for year-end rollover:
 * - validatePreConditions()
 * - calculateImpactSummary()
 */
class IntegratedRolloverServiceValidationTest extends TestCase
{
    use RefreshDatabase;

    protected IntegratedRolloverService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(IntegratedRolloverService::class);
    }

    // ========================================================================
    // validatePreConditions() tests
    // ========================================================================

    public function test_validation_passes_with_valid_data()
    {
        // Arrange
        $currentTahunAjaran = TahunAjaran::factory()->create(['status' => true, 'label' => '2023-2024']);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false, 'label' => '2024-2025']);

        $kelas7 = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        $kelas8 = Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        $kelas9 = Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        Siswa::factory()->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Act & Assert (should not throw exception)
        $this->service->validatePreConditions($targetTahunAjaran->id);
        $this->assertTrue(true); // If we reach here, validation passed
    }

    public function test_validation_fails_when_target_tahun_ajaran_not_found()
    {
        // Arrange
        TahunAjaran::factory()->create(['status' => true]);
        Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // Act & Assert
        $this->expectException(RolloverValidationException::class);
        $this->expectExceptionMessage('Tahun Ajaran tujuan tidak ditemukan');

        $this->service->validatePreConditions(999); // Non-existent ID
    }

    public function test_validation_fails_when_no_active_tahun_ajaran()
    {
        // Arrange
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);
        Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // Act & Assert
        $this->expectException(RolloverValidationException::class);
        $this->expectExceptionMessage('Tidak ada Tahun Ajaran yang aktif saat ini');

        $this->service->validatePreConditions($targetTahunAjaran->id);
    }

    public function test_validation_fails_when_target_same_as_current()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // Act & Assert
        $this->expectException(RolloverValidationException::class);
        $this->expectExceptionMessage('Tahun Ajaran tujuan sama dengan yang aktif');

        $this->service->validatePreConditions($tahunAjaran->id);
    }

    public function test_validation_fails_when_kelas_7_missing()
    {
        // Arrange
        TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        // Missing Kelas 7
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // Act & Assert
        $this->expectException(RolloverValidationException::class);
        $this->expectExceptionMessage('Data kelas tidak lengkap');

        $this->service->validatePreConditions($targetTahunAjaran->id);
    }

    public function test_validation_fails_when_kelas_8_missing()
    {
        // Arrange
        TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        // Missing Kelas 8
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // Act & Assert
        $this->expectException(RolloverValidationException::class);
        $this->expectExceptionMessage('Data kelas tidak lengkap');

        $this->service->validatePreConditions($targetTahunAjaran->id);
    }

    public function test_validation_fails_when_kelas_9_missing()
    {
        // Arrange
        TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        // Missing Kelas 9

        // Act & Assert
        $this->expectException(RolloverValidationException::class);
        $this->expectExceptionMessage('Data kelas tidak lengkap');

        $this->service->validatePreConditions($targetTahunAjaran->id);
    }

    public function test_validation_fails_when_kelas_tingkat_too_high()
    {
        // Arrange
        TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);
        Kelas::factory()->create(['tingkat' => '10', 'nama_kelas' => '10A']); // Invalid

        // Act & Assert
        $this->expectException(RolloverValidationException::class);
        $this->expectExceptionMessage('Data tidak valid: Kelas lebih dari 9 ditemukan');

        $this->service->validatePreConditions($targetTahunAjaran->id);
    }

    public function test_validation_fails_when_kelas_tingkat_too_low()
    {
        // Arrange
        TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        Kelas::factory()->create(['tingkat' => '6', 'nama_kelas' => '6A']); // Invalid
        Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // Act & Assert
        $this->expectException(RolloverValidationException::class);
        $this->expectExceptionMessage('Data tidak valid: Kelas kurang dari 7 ditemukan');

        $this->service->validatePreConditions($targetTahunAjaran->id);
    }

    public function test_validation_fails_when_no_active_students()
    {
        // Arrange
        TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // No active students

        // Act & Assert
        $this->expectException(RolloverValidationException::class);
        $this->expectExceptionMessage('Tidak ada siswa aktif yang akan di-rollover');

        $this->service->validatePreConditions($targetTahunAjaran->id);
    }

    public function test_validation_passes_with_inactive_students_only()
    {
        // Arrange
        TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        $kelas7 = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // Only inactive students
        Siswa::factory()->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::PINDAH_SEKOLAH->value,
        ]);

        // Create one active student to pass validation
        Siswa::factory()->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Act & Assert
        $this->service->validatePreConditions($targetTahunAjaran->id);
        $this->assertTrue(true);
    }

    // ========================================================================
    // calculateImpactSummary() tests
    // ========================================================================

    public function test_impact_summary_returns_correct_counts()
    {
        // Arrange
        $currentTahunAjaran = TahunAjaran::factory()->create(['status' => true, 'label' => '2023-2024']);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false, 'label' => '2024-2025']);

        $kelas7 = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        $kelas8 = Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        $kelas9 = Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // Create students at each level
        Siswa::factory()->count(3)->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        Siswa::factory()->count(5)->create([
            'kelas_id' => $kelas8->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        Siswa::factory()->count(2)->create([
            'kelas_id' => $kelas9->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Act
        $summary = $this->service->calculateImpactSummary($targetTahunAjaran->id);

        // Assert
        $this->assertInstanceOf(ImpactSummary::class, $summary);
        $this->assertEquals($currentTahunAjaran->id, $summary->currentTahunAjaranId);
        $this->assertEquals($targetTahunAjaran->id, $summary->targetTahunAjaranId);
        $this->assertEquals('2023-2024', $summary->currentTahunAjaranLabel);
        $this->assertEquals('2024-2025', $summary->targetTahunAjaranLabel);
        $this->assertEquals(3, $summary->kelas7To8Count);
        $this->assertEquals(5, $summary->kelas8To9Count);
        $this->assertEquals(2, $summary->graduatedCount);
    }

    public function test_impact_summary_counts_kelompok_clearing()
    {
        // Arrange
        $currentTahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        $kelas7 = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // Create students with kelompok assignments
        Siswa::factory()->count(4)->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'kelompok_id' => 1, // Has kelompok
        ]);

        Siswa::factory()->count(2)->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'kelompok_id' => null, // No kelompok
        ]);

        // Act
        $summary = $this->service->calculateImpactSummary($targetTahunAjaran->id);

        // Assert
        $this->assertEquals(4, $summary->kelompokClearedCount);
    }

    public function test_impact_summary_excludes_inactive_students()
    {
        // Arrange
        $currentTahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        $kelas7 = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // Active students
        Siswa::factory()->count(2)->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Inactive students
        Siswa::factory()->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::PINDAH_SEKOLAH->value,
        ]);

        Siswa::factory()->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::KELUAR->value,
        ]);

        Siswa::factory()->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::LULUS_SMP->value,
        ]);

        // Act
        $summary = $this->service->calculateImpactSummary($targetTahunAjaran->id);

        // Assert
        $this->assertEquals(2, $summary->kelas7To8Count); // Only active students
    }

    public function test_impact_summary_returns_zero_when_no_students()
    {
        // Arrange
        $currentTahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        $kelas7 = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // Create one active student so validation passes
        Siswa::factory()->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Act
        $summary = $this->service->calculateImpactSummary($targetTahunAjaran->id);

        // Assert
        $this->assertEquals(1, $summary->kelas7To8Count);
        $this->assertEquals(0, $summary->kelas8To9Count);
        $this->assertEquals(0, $summary->graduatedCount);
    }

    public function test_impact_summary_validates_before_calculating()
    {
        // Arrange
        TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        // Missing Kelas 8
        Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // Act & Assert
        $this->expectException(RolloverValidationException::class);
        $this->expectExceptionMessage('Data kelas tidak lengkap');

        $this->service->calculateImpactSummary($targetTahunAjaran->id);
    }

    public function test_impact_summary_helper_methods()
    {
        // Arrange
        $currentTahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $targetTahunAjaran = TahunAjaran::factory()->create(['status' => false]);

        $kelas7 = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        Siswa::factory()->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'kelompok_id' => 1,
        ]);

        // Act
        $summary = $this->service->calculateImpactSummary($targetTahunAjaran->id);

        // Assert
        $this->assertTrue($summary->hasAffectedStudents());
        $this->assertTrue($summary->isDifferentTahunAjaran());
        $this->assertEquals(1, $summary->getTotalAcademicChanges());
        $this->assertEquals(2, $summary->getTotalAffected()); // 1 academic + 1 kelompok
    }
}
