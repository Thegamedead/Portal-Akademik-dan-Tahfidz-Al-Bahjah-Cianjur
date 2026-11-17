<?php

namespace Tests\Unit\Models;

use App\Enums\StatusAkademik;
use App\Enums\StatusPenempatan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test the new rollover-related methods added to models
 *
 * Tests task 1.2: Add new methods to existing models (Siswa, TahunAjaran, Kelas)
 * Requirements: 2.1, 2.2, 2.3, 2.4, 3.1, 3.2, 1.1, 1.2, 1.3
 */
class RolloverModelMethodsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Siswa::scopeEligibleForPromotion()
     */
    public function test_siswa_scope_eligible_for_promotion_returns_only_active_students_in_specified_kelas(): void
    {
        // Create test data
        $kelas7 = Kelas::factory()->create(['tingkat' => '7', 'nama_kelas' => '7A']);
        $kelas8 = Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);

        // Create active students in Kelas 7
        $activeStudentInKelas7_1 = Siswa::factory()->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);
        $activeStudentInKelas7_2 = Siswa::factory()->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Create non-active student in Kelas 7 (should be excluded)
        $graduatedStudentInKelas7 = Siswa::factory()->create([
            'kelas_id' => $kelas7->id,
            'status_akademik' => StatusAkademik::LULUS_SMP->value,
        ]);

        // Create active student in different Kelas (should be excluded)
        $activeStudentInKelas8 = Siswa::factory()->create([
            'kelas_id' => $kelas8->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
        ]);

        // Execute scope
        $eligibleStudents = Siswa::eligibleForPromotion($kelas7->id)->get();

        // Assert only active students in Kelas 7 are returned
        $this->assertCount(2, $eligibleStudents);
        $this->assertTrue($eligibleStudents->contains($activeStudentInKelas7_1));
        $this->assertTrue($eligibleStudents->contains($activeStudentInKelas7_2));
        $this->assertFalse($eligibleStudents->contains($graduatedStudentInKelas7));
        $this->assertFalse($eligibleStudents->contains($activeStudentInKelas8));
    }

    /**
     * Test Siswa::scopePendingPlacement()
     */
    public function test_siswa_scope_pending_placement_returns_only_active_students_without_kelompok(): void
    {
        // Create students with various states
        $pendingStudent1 = Siswa::factory()->create([
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::TERDAFTAR->value,
            'kelompok_id' => null,
        ]);

        $pendingStudent2 = Siswa::factory()->create([
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::TERDAFTAR->value,
            'kelompok_id' => null,
        ]);

        // Student with kelompok (should be excluded)
        $placedStudent = Siswa::factory()->create([
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
            'kelompok_id' => 1,
        ]);

        // Graduated student without kelompok (should be excluded)
        $graduatedStudent = Siswa::factory()->create([
            'status_akademik' => StatusAkademik::LULUS_SMP->value,
            'status_penempatan' => StatusPenempatan::TERDAFTAR->value,
            'kelompok_id' => null,
        ]);

        // Execute scope
        $pendingStudents = Siswa::pendingPlacement()->get();

        // Assert only active students without kelompok are returned
        $this->assertCount(2, $pendingStudents);
        $this->assertTrue($pendingStudents->contains($pendingStudent1));
        $this->assertTrue($pendingStudents->contains($pendingStudent2));
        $this->assertFalse($pendingStudents->contains($placedStudent));
        $this->assertFalse($pendingStudents->contains($graduatedStudent));
    }

    /**
     * Test TahunAjaran::activate()
     */
    public function test_tahun_ajaran_activate_sets_only_target_as_active(): void
    {
        // Create multiple Tahun Ajaran records
        $ta2023 = TahunAjaran::factory()->create([
            'label' => '2023-2024',
            'status' => true, // Currently active
        ]);

        $ta2024 = TahunAjaran::factory()->create([
            'label' => '2024-2025',
            'status' => false,
        ]);

        $ta2025 = TahunAjaran::factory()->create([
            'label' => '2025-2026',
            'status' => false,
        ]);

        // Activate the 2024-2025 academic year
        $ta2024->activate();

        // Refresh models from database
        $ta2023->refresh();
        $ta2024->refresh();
        $ta2025->refresh();

        // Assert only ta2024 is active
        $this->assertFalse($ta2023->status);
        $this->assertTrue($ta2024->status);
        $this->assertFalse($ta2025->status);

        // Verify only one active Tahun Ajaran exists
        $this->assertEquals(1, TahunAjaran::where('status', true)->count());
    }

    /**
     * Test TahunAjaran::getActive()
     */
    public function test_tahun_ajaran_get_active_returns_active_record(): void
    {
        // Create multiple Tahun Ajaran records
        $inactiveTa1 = TahunAjaran::factory()->create([
            'label' => '2022-2023',
            'status' => false,
        ]);

        $activeTa = TahunAjaran::factory()->create([
            'label' => '2023-2024',
            'status' => true,
        ]);

        $inactiveTa2 = TahunAjaran::factory()->create([
            'label' => '2024-2025',
            'status' => false,
        ]);

        // Execute getActive
        $result = TahunAjaran::getActive();

        // Assert correct record is returned
        $this->assertNotNull($result);
        $this->assertEquals($activeTa->id, $result->id);
        $this->assertEquals('2023-2024', $result->label);
        $this->assertTrue($result->status);
    }

    /**
     * Test TahunAjaran::getActive() returns null when no active year exists
     */
    public function test_tahun_ajaran_get_active_returns_null_when_no_active_year(): void
    {
        // Create only inactive Tahun Ajaran records
        TahunAjaran::factory()->create(['status' => false]);
        TahunAjaran::factory()->create(['status' => false]);

        // Execute getActive
        $result = TahunAjaran::getActive();

        // Assert null is returned
        $this->assertNull($result);
    }

    /**
     * Test Kelas::getNextGradeKelas() for Kelas 7
     */
    public function test_kelas_get_next_grade_returns_kelas_8_for_kelas_7(): void
    {
        // Create Kelas 7A and 8A
        $kelas7A = Kelas::factory()->create([
            'tingkat' => '7',
            'nama_kelas' => '7A',
        ]);

        $kelas8A = Kelas::factory()->create([
            'tingkat' => '8',
            'nama_kelas' => '8A',
        ]);

        // Execute getNextGradeKelas
        $nextKelas = $kelas7A->getNextGradeKelas();

        // Assert Kelas 8A is returned
        $this->assertNotNull($nextKelas);
        $this->assertEquals($kelas8A->id, $nextKelas->id);
        $this->assertEquals('8', $nextKelas->tingkat);
        $this->assertEquals('8A', $nextKelas->nama_kelas);
    }

    /**
     * Test Kelas::getNextGradeKelas() for Kelas 8
     */
    public function test_kelas_get_next_grade_returns_kelas_9_for_kelas_8(): void
    {
        // Create Kelas 8B and 9B
        $kelas8B = Kelas::factory()->create([
            'tingkat' => '8',
            'nama_kelas' => '8B',
        ]);

        $kelas9B = Kelas::factory()->create([
            'tingkat' => '9',
            'nama_kelas' => '9B',
        ]);

        // Execute getNextGradeKelas
        $nextKelas = $kelas8B->getNextGradeKelas();

        // Assert Kelas 9B is returned
        $this->assertNotNull($nextKelas);
        $this->assertEquals($kelas9B->id, $nextKelas->id);
        $this->assertEquals('9', $nextKelas->tingkat);
        $this->assertEquals('9B', $nextKelas->nama_kelas);
    }

    /**
     * Test Kelas::getNextGradeKelas() for Kelas 9 returns null
     */
    public function test_kelas_get_next_grade_returns_null_for_kelas_9(): void
    {
        // Create Kelas 9A (no next grade after 9)
        $kelas9A = Kelas::factory()->create([
            'tingkat' => '9',
            'nama_kelas' => '9A',
        ]);

        // Execute getNextGradeKelas
        $nextKelas = $kelas9A->getNextGradeKelas();

        // Assert null is returned (no grade after 9)
        $this->assertNull($nextKelas);
    }

    /**
     * Test Kelas::getNextGradeKelas() returns null when next grade section doesn't exist
     */
    public function test_kelas_get_next_grade_returns_null_when_next_section_not_found(): void
    {
        // Create Kelas 7C but no 8C
        $kelas7C = Kelas::factory()->create([
            'tingkat' => '7',
            'nama_kelas' => '7C',
        ]);

        // Create 8A and 8B but not 8C
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8A']);
        Kelas::factory()->create(['tingkat' => '8', 'nama_kelas' => '8B']);

        // Execute getNextGradeKelas
        $nextKelas = $kelas7C->getNextGradeKelas();

        // Assert null is returned (8C doesn't exist)
        $this->assertNull($nextKelas);
    }
}
