<?php

namespace Tests\Unit\Services;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Enums\StatusAkademik;
use App\Services\Handlers\AcademicProgressionHandler;
use App\DataTransferObjects\ProgressionResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Unit Tests for AcademicProgressionHandler
 *
 * Tests academic promotion logic for year-end rollover:
 * - Kelas 7 → 8 promotion
 * - Kelas 8 → 9 promotion
 * - Kelas 9 → Graduation
 */
class AcademicProgressionHandlerTest extends TestCase
{
    use RefreshDatabase;

    protected AcademicProgressionHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = app(AcademicProgressionHandler::class);
    }

    public function test_it_promotes_kelas_7_students_to_kelas_8()
    {
        // Arrange
        $kelas7A = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        $kelas8A = Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);

        $siswa1 = Siswa::factory()->create([
            'kelas_id' => $kelas7A->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        $siswa2 = Siswa::factory()->create([
            'kelas_id' => $kelas7A->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Act
        $result = $this->handler->processPromotion();

        // Assert
        $this->assertInstanceOf(ProgressionResult::class, $result);
        $this->assertEquals(2, $result->kelas7To8Count);

        $siswa1->refresh();
        $siswa2->refresh();

        $this->assertEquals($kelas8A->id, $siswa1->kelas_id);
        $this->assertEquals($kelas8A->id, $siswa2->kelas_id);
        $this->assertEquals(StatusAkademik::AKTIF->value, $siswa1->status_akademik);
        $this->assertEquals(StatusAkademik::AKTIF->value, $siswa2->status_akademik);
    }

    public function test_it_promotes_kelas_8_students_to_kelas_9()
    {
        // Arrange
        $kelas8B = Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8B']);
        $kelas9B = Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9B']);

        $siswa = Siswa::factory()->create([
            'kelas_id' => $kelas8B->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Act
        $result = $this->handler->processPromotion();

        // Assert
        $this->assertEquals(1, $result->kelas8To9Count);

        $siswa->refresh();

        $this->assertEquals($kelas9B->id, $siswa->kelas_id);
        $this->assertEquals(StatusAkademik::AKTIF->value, $siswa->status_akademik);
    }

    public function test_it_graduates_kelas_9_students()
    {
        // Arrange
        $kelas9A = Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        $siswa1 = Siswa::factory()->create([
            'kelas_id' => $kelas9A->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'tanggal_lulus_akademik' => null,
        ]);

        $siswa2 = Siswa::factory()->create([
            'kelas_id' => $kelas9A->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'tanggal_lulus_akademik' => null,
        ]);

        // Act
        $result = $this->handler->processPromotion();

        // Assert
        $this->assertEquals(2, $result->graduatedCount);

        $siswa1->refresh();
        $siswa2->refresh();

        $this->assertEquals(StatusAkademik::LULUS_SMP->value, $siswa1->status_akademik);
        $this->assertEquals(StatusAkademik::LULUS_SMP->value, $siswa2->status_akademik);
        $this->assertEquals($kelas9A->id, $siswa1->kelas_id); // Kelas ID preserved
        $this->assertEquals($kelas9A->id, $siswa2->kelas_id);
        $this->assertNotNull($siswa1->tanggal_lulus_akademik);
        $this->assertNotNull($siswa2->tanggal_lulus_akademik);
        $this->assertTrue(Carbon::parse($siswa1->tanggal_lulus_akademik)->isToday());
    }

    public function test_it_processes_all_three_levels_simultaneously()
    {
        // Arrange
        // Create all Kelas levels
        $kelas7A = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        $kelas8A = Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        $kelas9A = Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // Create students at each level
        $siswa7 = Siswa::factory()->create([
            'kelas_id' => $kelas7A->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        $siswa8 = Siswa::factory()->create([
            'kelas_id' => $kelas8A->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        $siswa9 = Siswa::factory()->create([
            'kelas_id' => $kelas9A->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Act
        $result = $this->handler->processPromotion();

        // Assert
        $this->assertEquals(1, $result->kelas7To8Count);
        $this->assertEquals(1, $result->kelas8To9Count);
        $this->assertEquals(1, $result->graduatedCount);
        $this->assertEquals(3, $result->getTotalAffected());

        $siswa7->refresh();
        $siswa8->refresh();
        $siswa9->refresh();

        $this->assertEquals($kelas8A->id, $siswa7->kelas_id);
        $this->assertEquals($kelas9A->id, $siswa8->kelas_id);
        $this->assertEquals(StatusAkademik::LULUS_SMP->value, $siswa9->status_akademik);
    }

    public function test_it_only_promotes_active_students()
    {
        // Arrange
        $kelas7A = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        $kelas8A = Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);

        $siswaActive = Siswa::factory()->create([
            'kelas_id' => $kelas7A->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        $siswaPindah = Siswa::factory()->create([
            'kelas_id' => $kelas7A->id,
            'status_akademik' => StatusAkademik::PINDAH_SEKOLAH->value,
        ]);

        $siswaKeluar = Siswa::factory()->create([
            'kelas_id' => $kelas7A->id,
            'status_akademik' => StatusAkademik::KELUAR->value,
        ]);

        // Act
        $result = $this->handler->processPromotion();

        // Assert
        $this->assertEquals(1, $result->kelas7To8Count);

        $siswaActive->refresh();
        $siswaPindah->refresh();
        $siswaKeluar->refresh();

        $this->assertEquals($kelas8A->id, $siswaActive->kelas_id); // Promoted
        $this->assertEquals($kelas7A->id, $siswaPindah->kelas_id); // Not promoted
        $this->assertEquals($kelas7A->id, $siswaKeluar->kelas_id); // Not promoted
    }

    public function test_it_does_not_promote_already_graduated_students()
    {
        // Arrange
        $kelas9A = Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        $siswaLulus = Siswa::factory()->create([
            'kelas_id' => $kelas9A->id,
            'status_akademik' => StatusAkademik::LULUS_SMP->value,
            'tanggal_lulus_akademik' => Carbon::now()->subYear(),
        ]);

        // Act
        $result = $this->handler->processPromotion();

        // Assert
        $this->assertEquals(0, $result->graduatedCount);

        $siswaLulus->refresh();

        // Should remain unchanged
        $this->assertEquals(StatusAkademik::LULUS_SMP->value, $siswaLulus->status_akademik);
        $this->assertTrue(Carbon::parse($siswaLulus->tanggal_lulus_akademik)->lt(Carbon::now()));
    }

    public function test_it_handles_multiple_sections_per_grade()
    {
        // Arrange
        $kelas7A = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        $kelas7B = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7B']);
        $kelas8A = Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        $kelas8B = Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8B']);

        $siswa7A = Siswa::factory()->create([
            'kelas_id' => $kelas7A->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        $siswa7B = Siswa::factory()->create([
            'kelas_id' => $kelas7B->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Act
        $result = $this->handler->processPromotion();

        // Assert
        $this->assertEquals(2, $result->kelas7To8Count);

        $siswa7A->refresh();
        $siswa7B->refresh();

        // Each section should map to corresponding section in next grade
        $this->assertEquals($kelas8A->id, $siswa7A->kelas_id);
        $this->assertEquals($kelas8B->id, $siswa7B->kelas_id);
    }

    public function test_it_returns_affected_student_ids()
    {
        // Arrange
        $kelas7A = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        $kelas8A = Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        $kelas9A = Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        $siswa7 = Siswa::factory()->create([
            'kelas_id' => $kelas7A->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        $siswa9 = Siswa::factory()->create([
            'kelas_id' => $kelas9A->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Act
        $result = $this->handler->processPromotion();

        // Assert
        $this->assertContains($siswa7->id, $result->promotedStudentIds);
        $this->assertContains($siswa9->id, $result->graduatedStudentIds);
        $this->assertCount(2, $result->getAllAffectedStudentIds());
    }

    public function test_it_returns_zero_counts_when_no_students_exist()
    {
        // Arrange
        Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        // No students created

        // Act
        $result = $this->handler->processPromotion();

        // Assert
        $this->assertEquals(0, $result->kelas7To8Count);
        $this->assertEquals(0, $result->kelas8To9Count);
        $this->assertEquals(0, $result->graduatedCount);
        $this->assertFalse($result->hasAffectedStudents());
    }

    public function test_it_throws_exception_when_kelas_data_incomplete()
    {
        // Arrange
        // Only create Kelas 7, missing Kelas 8 and 9
        Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);

        Siswa::factory()->create([
            'kelas_id' => Kelas::where('tingkat', '7')->first()->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Assert & Act
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Data kelas tidak lengkap');

        $this->handler->processPromotion();
    }

    public function test_it_preserves_kelas_id_for_graduated_students()
    {
        // Arrange
        $kelas9A = Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        $siswa = Siswa::factory()->create([
            'kelas_id' => $kelas9A->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Act
        $result = $this->handler->processPromotion();

        // Assert
        $siswa->refresh();

        // Kelas ID should remain unchanged (for historical record)
        $this->assertEquals($kelas9A->id, $siswa->kelas_id);
        $this->assertEquals(StatusAkademik::LULUS_SMP->value, $siswa->status_akademik);
    }

    public function test_it_handles_missing_target_kelas_gracefully()
    {
        // Arrange
        // Create Kelas 7A but no corresponding Kelas 8A
        $kelas7A = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8B']); // Different section
        Kelas::factory()->create(['tingkat' => '9', 'nama_kelas' => '9A']);

        $siswa = Siswa::factory()->create([
            'kelas_id' => $kelas7A->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Act
        $result = $this->handler->processPromotion();

        // Assert
        // Should log warning but not crash
        $this->assertEquals(0, $result->kelas7To8Count);

        $siswa->refresh();

        // Student should remain in original Kelas
        $this->assertEquals($kelas7A->id, $siswa->kelas_id);
    }
}
