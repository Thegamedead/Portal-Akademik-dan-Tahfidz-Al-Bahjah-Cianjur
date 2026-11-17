<?php

namespace Tests\Feature\MasterData;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Kelompok;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\MasterData\SemesterService;
use App\Services\MasterData\TahunAjaranService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Integration Tests for Tahun Ajaran and Semester Management
 *
 * **Requirements**: 1.2, 1.4, 1.5, 1.7, 1.8, 1.9
 *
 * This test suite verifies the following properties:
 * - Property 1: Only one active TahunAjaran at any time
 * - Property 2: Semester dates within TahunAjaran range
 * - Property 3: Unique jenis constraint per TahunAjaran
 * - Property 13: Cascade deactivation of Semesters
 * - Property 15: Deletion prevention with dependencies
 *
 * Run with: php artisan test --filter TahunAjaranSemesterTest
 */
class TahunAjaranSemesterTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected TahunAjaranService $tahunAjaranService;
    protected SemesterService $semesterService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin and authenticate
        $this->admin = Admin::factory()->create();
        $this->actingAs($this->admin, 'admin');

        // Initialize services
        $this->tahunAjaranService = app(TahunAjaranService::class);
        $this->semesterService = app(SemesterService::class);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Create a TahunAjaran with default or custom attributes
     */
    private function makeTahunAjaran(array $overrides = []): TahunAjaran
    {
        return TahunAjaran::factory()->create(array_merge([
            'label' => '2024/2025',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2025-06-30',
            'status' => false,
        ], $overrides));
    }

    /**
     * Create a Semester with required TahunAjaran relation
     */
    private function makeSemester(TahunAjaran $ta, array $overrides = []): Semester
    {
        return Semester::factory()->create(array_merge([
            'tahun_ajaran_id' => $ta->id,
            'jenis' => 'Ganjil',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif' => false,
        ], $overrides));
    }

    /**
     * Create a Guru Pembimbing Tahfidz for testing
     */
    private function makeGuruPembimbing(array $overrides = []): Guru
    {
        return Guru::factory()->create(array_merge([
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'status_aktif' => true,
        ], $overrides));
    }

    // =========================================================================
    // PROPERTY 1: At most one TahunAjaran has status = true at any time
    // Requirements: 1.2
    // =========================================================================

    /**
     * Test: Creating tahun ajaran with active status deactivates others
     * Validates: Requirements 1.2
     */
    public function test_property_1_creating_active_tahun_ajaran_deactivates_all_others(): void
    {
        // Arrange: Create two active tahun ajaran
        $ta1 = $this->tahunAjaranService->create([
            'label' => '2023/2024',
            'tanggal_mulai' => '2023-07-01',
            'tanggal_selesai' => '2024-06-30',
            'status' => true,
        ]);

        $ta2 = $this->tahunAjaranService->create([
            'label' => '2024/2025',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2025-06-30',
            'status' => true,
        ]);

        // Assert: Only one is active
        $this->assertEquals(1, TahunAjaran::where('status', true)->count());
        $this->assertFalse($ta1->fresh()->status);
        $this->assertTrue($ta2->fresh()->status);
    }

    /**
     * Test: Updating tahun ajaran to active deactivates all others
     * Validates: Requirements 1.2
     */
    public function test_property_1_updating_to_active_deactivates_all_others(): void
    {
        // Arrange: Create multiple tahun ajaran, one active
        $ta1 = $this->makeTahunAjaran(['label' => '2022/2023', 'status' => true]);
        $ta2 = $this->makeTahunAjaran(['label' => '2023/2024', 'status' => false]);
        $ta3 = $this->makeTahunAjaran(['label' => '2024/2025', 'status' => false]);

        // Act: Activate ta2
        $this->tahunAjaranService->update($ta2->id, ['status' => true]);

        // Assert: Only ta2 is active
        $this->assertEquals(1, TahunAjaran::where('status', true)->count());
        $this->assertFalse($ta1->fresh()->status);
        $this->assertTrue($ta2->fresh()->status);
        $this->assertFalse($ta3->fresh()->status);
    }

    /**
     * Test: At most one active tahun ajaran exists at any time
     * Validates: Requirements 1.2
     */
    public function test_property_1_at_most_one_active_tahun_ajaran(): void
    {
        // Arrange: Create inactive tahun ajaran
        $ta1 = $this->makeTahunAjaran(['status' => false]);

        // Act: Create active tahun ajaran
        $ta2 = $this->tahunAjaranService->create([
            'label' => '2025/2026',
            'tanggal_mulai' => '2025-07-01',
            'tanggal_selesai' => '2026-06-30',
            'status' => true,
        ]);

        // Assert: Exactly one active
        $activeCount = TahunAjaran::where('status', true)->count();
        $this->assertEquals(1, $activeCount, 'There must be at most one active TahunAjaran');
        $this->assertTrue($ta2->status);
    }

    // =========================================================================
    // PROPERTY 2: Semester dates within parent TahunAjaran date range
    // Requirements: 1.4
    // =========================================================================

    /**
     * Test: Semester tanggal_mulai before TahunAjaran tanggal_mulai is rejected
     * Validates: Requirements 1.4
     */
    public function test_property_2_semester_start_before_tahun_ajaran_rejected(): void
    {
        // Arrange
        $ta = $this->makeTahunAjaran([
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2025-06-30',
        ]);

        // Act & Assert: Should throw ValidationException
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Tanggal mulai semester harus berada dalam rentang Tahun Ajaran');

        $this->semesterService->create([
            'tahun_ajaran_id' => $ta->id,
            'jenis' => 'Ganjil',
            'tanggal_mulai' => '2024-06-30', // One day before TA start
            'tanggal_selesai' => '2024-12-31',
            'status_aktif' => false,
        ]);
    }

    /**
     * Test: Semester tanggal_selesai after TahunAjaran tanggal_selesai is rejected
     * Validates: Requirements 1.4
     */
    public function test_property_2_semester_end_after_tahun_ajaran_rejected(): void
    {
        // Arrange
        $ta = $this->makeTahunAjaran([
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2025-06-30',
        ]);

        // Act & Assert: Should throw ValidationException
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Tanggal selesai semester harus berada dalam rentang Tahun Ajaran');

        $this->semesterService->create([
            'tahun_ajaran_id' => $ta->id,
            'jenis' => 'Genap',
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-07-01', // One day after TA end
            'status_aktif' => false,
        ]);
    }

    /**
     * Test: Valid semester dates within TahunAjaran range are accepted
     * Validates: Requirements 1.4
     */
    public function test_property_2_valid_semester_dates_within_range_accepted(): void
    {
        // Arrange
        $ta = $this->makeTahunAjaran([
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2025-06-30',
        ]);

        // Act: Create semester with valid dates
        $semester = $this->semesterService->create([
            'tahun_ajaran_id' => $ta->id,
            'jenis' => 'Ganjil',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif' => false,
        ]);

        // Assert: Semester is created and dates are within range
        $this->assertDatabaseHas('semesters', ['id' => $semester->id]);
        $this->assertTrue($semester->tanggal_mulai >= $ta->tanggal_mulai);
        $this->assertTrue($semester->tanggal_selesai <= $ta->tanggal_selesai);
    }

    /**
     * Test: Updating semester dates outside TahunAjaran range is rejected
     * Validates: Requirements 1.4
     */
    public function test_property_2_updating_semester_dates_outside_range_rejected(): void
    {
        // Arrange
        $ta = $this->makeTahunAjaran([
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2025-06-30',
        ]);

        $semester = $this->makeSemester($ta, [
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
        ]);

        // Act & Assert: Should throw ValidationException
        $this->expectException(ValidationException::class);

        $this->semesterService->update($semester->id, [
            'tanggal_selesai' => '2025-07-15', // After TA end
        ]);
    }

    // =========================================================================
    // PROPERTY 3: Unique jenis constraint per TahunAjaran
    // Requirements: 1.5
    // =========================================================================

    /**
     * Test: Database constraint prevents duplicate jenis in same TahunAjaran
     * Validates: Requirements 1.5
     */
    public function test_property_3_db_constraint_prevents_duplicate_jenis(): void
    {
        // Arrange
        $ta = $this->makeTahunAjaran();

        Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'jenis' => 'Ganjil',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif' => false,
        ]);

        // Act & Assert: Should throw QueryException (unique constraint violation)
        $this->expectException(QueryException::class);

        Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'jenis' => 'Ganjil', // Duplicate jenis
            'tanggal_mulai' => '2024-08-01',
            'tanggal_selesai' => '2024-12-15',
            'status_aktif' => false,
        ]);
    }

    /**
     * Test: Service layer rejects duplicate jenis per TahunAjaran
     * Validates: Requirements 1.5
     */
    public function test_property_3_service_rejects_duplicate_jenis(): void
    {
        // Arrange
        $ta = $this->makeTahunAjaran();

        $this->semesterService->create([
            'tahun_ajaran_id' => $ta->id,
            'jenis' => 'Ganjil',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif' => false,
        ]);

        // Act & Assert: Should throw ValidationException
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Semester Ganjil sudah ada untuk Tahun Ajaran ini');

        $this->semesterService->create([
            'tahun_ajaran_id' => $ta->id,
            'jenis' => 'Ganjil', // Duplicate jenis
            'tanggal_mulai' => '2024-08-01',
            'tanggal_selesai' => '2024-12-15',
            'status_aktif' => false,
        ]);
    }

    /**
     * Test: Different jenis allowed in same TahunAjaran
     * Validates: Requirements 1.5
     */
    public function test_property_3_different_jenis_allowed_in_same_tahun_ajaran(): void
    {
        // Arrange
        $ta = $this->makeTahunAjaran();

        // Act: Create Ganjil and Genap semesters
        $ganjil = $this->semesterService->create([
            'tahun_ajaran_id' => $ta->id,
            'jenis' => 'Ganjil',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif' => false,
        ]);

        $genap = $this->semesterService->create([
            'tahun_ajaran_id' => $ta->id,
            'jenis' => 'Genap',
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-06-30',
            'status_aktif' => false,
        ]);

        // Assert: Both semesters exist
        $this->assertEquals(2, Semester::where('tahun_ajaran_id', $ta->id)->count());
        $this->assertDatabaseHas('semesters', ['id' => $ganjil->id, 'jenis' => 'Ganjil']);
        $this->assertDatabaseHas('semesters', ['id' => $genap->id, 'jenis' => 'Genap']);
    }

    /**
     * Test: Same jenis allowed in different TahunAjaran
     * Validates: Requirements 1.5
     */
    public function test_property_3_same_jenis_allowed_in_different_tahun_ajaran(): void
    {
        // Arrange: Create two different tahun ajaran
        $ta1 = $this->makeTahunAjaran([
            'label' => '2023/2024',
            'tanggal_mulai' => '2023-07-01',
            'tanggal_selesai' => '2024-06-30',
        ]);

        $ta2 = $this->makeTahunAjaran([
            'label' => '2024/2025',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2025-06-30',
        ]);

        // Act: Create same jenis in both
        $s1 = $this->semesterService->create([
            'tahun_ajaran_id' => $ta1->id,
            'jenis' => 'Ganjil',
            'tanggal_mulai' => '2023-07-01',
            'tanggal_selesai' => '2023-12-31',
            'status_aktif' => false,
        ]);

        $s2 = $this->semesterService->create([
            'tahun_ajaran_id' => $ta2->id,
            'jenis' => 'Ganjil',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif' => false,
        ]);

        // Assert: Both exist
        $this->assertDatabaseHas('semesters', ['id' => $s1->id]);
        $this->assertDatabaseHas('semesters', ['id' => $s2->id]);
    }

    // =========================================================================
    // PROPERTY 13: Cascade deactivation of Semesters when TahunAjaran deactivated
    // Requirements: 1.7
    // =========================================================================

    /**
     * Test: All related Semesters deactivated when TahunAjaran is deactivated
     * Validates: Requirements 1.7
     */
    public function test_property_13_cascade_deactivation_of_semesters(): void
    {
        // Arrange: Create active TahunAjaran with active Semesters
        $ta = $this->makeTahunAjaran(['status' => true]);

        $s1 = $this->makeSemester($ta, [
            'jenis' => 'Ganjil',
            'status_aktif' => true,
        ]);

        $s2 = $this->makeSemester($ta, [
            'jenis' => 'Genap',
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-06-30',
            'status_aktif' => true,
        ]);

        // Act: Deactivate TahunAjaran
        $this->tahunAjaranService->update($ta->id, ['status' => false]);

        // Assert: All related Semesters are deactivated
        $this->assertFalse($s1->fresh()->status_aktif);
        $this->assertFalse($s2->fresh()->status_aktif);
        $this->assertEquals(0, Semester::where('tahun_ajaran_id', $ta->id)
            ->where('status_aktif', true)
            ->count());
    }

    /**
     * Test: Semesters remain inactive when TahunAjaran is deactivated
     * Validates: Requirements 1.7
     */
    public function test_property_13_inactive_semesters_remain_inactive(): void
    {
        // Arrange: Create TahunAjaran with mix of active and inactive Semesters
        $ta = $this->makeTahunAjaran(['status' => true]);

        $activeS = $this->makeSemester($ta, [
            'jenis' => 'Ganjil',
            'status_aktif' => true,
        ]);

        $inactiveS = $this->makeSemester($ta, [
            'jenis' => 'Genap',
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-06-30',
            'status_aktif' => false,
        ]);

        // Act: Deactivate TahunAjaran
        $this->tahunAjaranService->update($ta->id, ['status' => false]);

        // Assert: All Semesters are inactive
        $this->assertFalse($activeS->fresh()->status_aktif);
        $this->assertFalse($inactiveS->fresh()->status_aktif);
    }

    /**
     * Test: Only Semesters of deactivated TahunAjaran are affected
     * Validates: Requirements 1.7
     */
    public function test_property_13_only_related_semesters_deactivated(): void
    {
        // Arrange: Create two TahunAjaran with Semesters
        $ta1 = $this->makeTahunAjaran(['label' => '2023/2024', 'status' => true]);
        $ta2 = $this->makeTahunAjaran(['label' => '2024/2025', 'status' => false]);

        $s1 = $this->makeSemester($ta1, ['status_aktif' => true]);
        $s2 = $this->makeSemester($ta2, [
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif' => true,
        ]);

        // Act: Deactivate only ta1
        $this->tahunAjaranService->update($ta1->id, ['status' => false]);

        // Assert: Only ta1's Semesters are deactivated
        $this->assertFalse($s1->fresh()->status_aktif);
        $this->assertTrue($s2->fresh()->status_aktif); // ta2's semester unchanged
    }

    // =========================================================================
    // PROPERTY 15: Deletion prevention when dependencies exist
    // Requirements: 1.8, 1.9
    // =========================================================================

    /**
     * Test: TahunAjaran deletion allowed when no dependencies exist
     * Validates: Requirements 1.8
     */
    public function test_property_15_tahun_ajaran_deletion_allowed_without_dependencies(): void
    {
        // Arrange: Create TahunAjaran with Semesters but no other dependencies
        $ta = $this->makeTahunAjaran();
        $s1 = $this->makeSemester($ta, ['jenis' => 'Ganjil']);
        $s2 = $this->makeSemester($ta, [
            'jenis' => 'Genap',
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-06-30',
        ]);

        // Act: Delete TahunAjaran
        $result = $this->tahunAjaranService->delete($ta->id);

        // Assert: TahunAjaran and Semesters are deleted
        $this->assertTrue($result);
        $this->assertDatabaseMissing('tahun_ajarans', ['id' => $ta->id]);
        $this->assertDatabaseMissing('semesters', ['id' => $s1->id]);
        $this->assertDatabaseMissing('semesters', ['id' => $s2->id]);
    }

    /**
     * Test: TahunAjaran deletion blocked when Kelompok exists
     * Validates: Requirements 1.9
     */
    public function test_property_15_tahun_ajaran_deletion_blocked_with_kelompok(): void
    {
        // Arrange: Create TahunAjaran with Kelompok dependency
        $ta = $this->makeTahunAjaran();
        $guru = $this->makeGuruPembimbing();

        Kelompok::factory()->create([
            'tahun_ajaran_id' => $ta->id,
            'guru_pembimbing_id' => $guru->id,
        ]);

        // Act & Assert: Should throw exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Tidak dapat menghapus Tahun Ajaran ini.*kelompok/');

        $this->tahunAjaranService->delete($ta->id);
    }

    /**
     * Test: TahunAjaran with dependencies cannot be deleted
     * Validates: Requirements 1.9
     */
    public function test_property_15_tahun_ajaran_with_dependencies_prevents_deletion(): void
    {
        // Arrange: Create TahunAjaran with multiple Kelompok
        $ta = $this->makeTahunAjaran();
        $guru1 = $this->makeGuruPembimbing(['niy' => 'GURU001']);
        $guru2 = $this->makeGuruPembimbing(['niy' => 'GURU002']);

        Kelompok::factory()->count(3)->create([
            'tahun_ajaran_id' => $ta->id,
            'guru_pembimbing_id' => $guru1->id,
        ]);

        // Assert: Database still has TahunAjaran after failed deletion
        try {
            $this->tahunAjaranService->delete($ta->id);
            $this->fail('Expected exception was not thrown');
        } catch (\Exception $e) {
            $this->assertDatabaseHas('tahun_ajarans', ['id' => $ta->id]);
            $this->assertEquals(3, Kelompok::where('tahun_ajaran_id', $ta->id)->count());
        }
    }

    /**
     * Test: TahunAjaran deletion cascades to Semesters
     * Validates: Requirements 1.8
     */
    public function test_property_15_tahun_ajaran_deletion_cascades_to_semesters(): void
    {
        // Arrange: Create TahunAjaran with multiple Semesters
        $ta = $this->makeTahunAjaran();

        $semesterIds = [];
        $semesterIds[] = $this->makeSemester($ta, ['jenis' => 'Ganjil'])->id;
        $semesterIds[] = $this->makeSemester($ta, [
            'jenis' => 'Genap',
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-06-30',
        ])->id;

        // Act: Delete TahunAjaran
        $this->tahunAjaranService->delete($ta->id);

        // Assert: All Semesters are also deleted
        foreach ($semesterIds as $semesterId) {
            $this->assertDatabaseMissing('semesters', ['id' => $semesterId]);
        }
    }

    /**
     * Test: Empty TahunAjaran (no Semesters) can be deleted
     * Validates: Requirements 1.8
     */
    public function test_property_15_empty_tahun_ajaran_can_be_deleted(): void
    {
        // Arrange: Create TahunAjaran without any Semesters
        $ta = $this->makeTahunAjaran();

        // Act: Delete TahunAjaran
        $result = $this->tahunAjaranService->delete($ta->id);

        // Assert: Successfully deleted
        $this->assertTrue($result);
        $this->assertDatabaseMissing('tahun_ajarans', ['id' => $ta->id]);
    }

    // =========================================================================
    // ADDITIONAL INTEGRATION TESTS
    // =========================================================================

    /**
     * Test: Complete workflow - Create, update, and manage TahunAjaran with Semesters
     */
    public function test_complete_workflow_tahun_ajaran_and_semester_management(): void
    {
        // Create TahunAjaran
        $ta = $this->tahunAjaranService->create([
            'label' => '2024/2025',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2025-06-30',
            'status' => true,
        ]);

        $this->assertEquals(1, TahunAjaran::where('status', true)->count());

        // Create Ganjil Semester
        $ganjil = $this->semesterService->create([
            'tahun_ajaran_id' => $ta->id,
            'jenis' => 'Ganjil',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif' => true,
        ]);

        $this->assertDatabaseHas('semesters', [
            'id' => $ganjil->id,
            'tahun_ajaran_id' => $ta->id,
            'jenis' => 'Ganjil',
        ]);

        // Create Genap Semester
        $genap = $this->semesterService->create([
            'tahun_ajaran_id' => $ta->id,
            'jenis' => 'Genap',
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-06-30',
            'status_aktif' => false,
        ]);

        $this->assertEquals(2, Semester::where('tahun_ajaran_id', $ta->id)->count());

        // Deactivate TahunAjaran - should cascade to Semesters
        $this->tahunAjaranService->update($ta->id, ['status' => false]);

        $this->assertFalse($ganjil->fresh()->status_aktif);
        $this->assertFalse($genap->fresh()->status_aktif);

        // Delete TahunAjaran without dependencies
        $this->tahunAjaranService->delete($ta->id);

        $this->assertDatabaseMissing('tahun_ajarans', ['id' => $ta->id]);
        $this->assertDatabaseMissing('semesters', ['tahun_ajaran_id' => $ta->id]);
    }
}
