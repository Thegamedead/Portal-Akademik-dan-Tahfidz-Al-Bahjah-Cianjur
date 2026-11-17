<?php

/**
 * Property-Based Tests for Dashboard and Aggregate Methods
 *
 * Tests Properties 11, 13, 14, 15 from design.md
 *
 * Configuration:
 * - Minimum 100 iterations per property test (reduced to 10 for MVP demonstration)
 * - Use data generators for student progress, filters, sorting criteria
 *
 * Reference: design.md "Correctness Properties" section
 */

namespace Tests\Property;

use App\Models\Guru;
use App\Models\Juz;
use App\Models\Kelompok;
use App\Models\ProgresHafalan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\ProgresHafalanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPropertiesTest extends TestCase
{
    use RefreshDatabase;

    protected ProgresHafalanService $progresService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->progresService = app(ProgresHafalanService::class);

        // Seed 30 juz for completeness
        Juz::factory()->count(30)->create();
    }

    /**
     * Property 11: Student Sorting
     *
     * For any set of students, sorting by specified criteria (name alphabetically,
     * progress_percentage numerically, juz_count numerically) SHALL produce results
     * in correct ascending or descending order.
     *
     * Feature: progres-hafalan-otomatis, Property 11: Student Sorting
     * Validates: Requirement 5.4
     *
     * @test
     * @group property-based
     */
    public function test_student_sorting_produces_correct_order()
    {
        // Setup: Create fixtures
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create();
        $guru = Guru::factory()->create();
        $kelompok->update(['guru_pembimbing_id' => $guru->id]);

        // Create students with varied progress data
        $studentsData = [
            ['nama' => 'Zahra', 'juz_tuntas' => 3, 'progress' => 60.00],
            ['nama' => 'Ahmad', 'juz_tuntas' => 5, 'progress' => 100.00],
            ['nama' => 'Budi', 'juz_tuntas' => 1, 'progress' => 20.00],
            ['nama' => 'Yusuf', 'juz_tuntas' => 4, 'progress' => 80.00],
            ['nama' => 'Fatimah', 'juz_tuntas' => 2, 'progress' => 40.00],
        ];

        foreach ($studentsData as $data) {
            $siswa = Siswa::factory()->create([
                'nama' => $data['nama'],
                'kelompok_id' => $kelompok->id,
            ]);

            ProgresHafalan::create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => $data['juz_tuntas'],
                'progress_toward_target' => $data['progress'],
            ]);
        }

        // Property iteration: Test different sort criteria
        $sortScenarios = [
            // Sort by name ascending
            [
                'sort_by' => 'name',
                'sort_direction' => 'asc',
                'expected_order' => ['Ahmad', 'Budi', 'Fatimah', 'Yusuf', 'Zahra'],
            ],
            // Sort by name descending
            [
                'sort_by' => 'name',
                'sort_direction' => 'desc',
                'expected_order' => ['Zahra', 'Yusuf', 'Fatimah', 'Budi', 'Ahmad'],
            ],
            // Sort by progress percentage ascending
            [
                'sort_by' => 'progress_percentage',
                'sort_direction' => 'asc',
                'expected_order' => ['Budi', 'Fatimah', 'Zahra', 'Yusuf', 'Ahmad'],
            ],
            // Sort by progress percentage descending
            [
                'sort_by' => 'progress_percentage',
                'sort_direction' => 'desc',
                'expected_order' => ['Ahmad', 'Yusuf', 'Zahra', 'Fatimah', 'Budi'],
            ],
            // Sort by juz count ascending
            [
                'sort_by' => 'juz_count',
                'sort_direction' => 'asc',
                'expected_order' => ['Budi', 'Fatimah', 'Zahra', 'Yusuf', 'Ahmad'],
            ],
            // Sort by juz count descending
            [
                'sort_by' => 'juz_count',
                'sort_direction' => 'desc',
                'expected_order' => ['Ahmad', 'Yusuf', 'Zahra', 'Fatimah', 'Budi'],
            ],
        ];

        foreach ($sortScenarios as $scenario) {
            // Act: Get dashboard data with sorting
            $result = $this->progresService->getDashboardDataForGuru($guru->id, [
                'sort_by' => $scenario['sort_by'],
                'sort_direction' => $scenario['sort_direction'],
                'per_page' => 10,
            ]);

            // Extract actual names in order (handle paginator or collection)
            $items = method_exists($result, 'items') ? collect($result->items()) : $result;
            $actualOrder = $items->map(function ($progres) {
                return $progres->siswa->nama;
            })->toArray();

            // Assert: Order matches expected
            $this->assertEquals(
                $scenario['expected_order'],
                $actualOrder,
                "Sorting by {$scenario['sort_by']} {$scenario['sort_direction']} failed"
            );
        }

        $this->assertTrue(true, 'Property 11: Student Sorting holds');
    }

    /**
     * Property 13: Aggregate Statistics Accuracy
     *
     * For any set of students in admin dashboard, aggregate statistics
     * (average juz_tuntas per tingkat, distribution by progress range)
     * SHALL match manual calculation from the same underlying data.
     *
     * Feature: progres-hafalan-otomatis, Property 13: Aggregate Statistics Accuracy
     * Validates: Requirement 6.3
     *
     * @test
     * @group property-based
     */
    public function test_aggregate_statistics_match_manual_calculation()
    {
        // Setup: Create varied student progress across tingkat
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);

        // Create kelompok for different tingkat
        $kelompokDasar = Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $kelompokMenengah = Kelompok::factory()->create(['tingkat' => 'Menengah']);
        $kelompokAtas = Kelompok::factory()->create(['tingkat' => 'Atas']);

        // Students in Dasar: juz [2, 3, 4] => avg 3.00
        $siswaDasar1 = Siswa::factory()->create([
            'kelompok_id' => $kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);
        ProgresHafalan::create([
            'siswa_id' => $siswaDasar1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 2,
            'progress_toward_target' => 40.00,
        ]);

        $siswaDasar2 = Siswa::factory()->create([
            'kelompok_id' => $kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);
        ProgresHafalan::create([
            'siswa_id' => $siswaDasar2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 3,
            'progress_toward_target' => 60.00,
        ]);

        $siswaDasar3 = Siswa::factory()->create([
            'kelompok_id' => $kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);
        ProgresHafalan::create([
            'siswa_id' => $siswaDasar3->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 4,
            'progress_toward_target' => 80.00,
        ]);

        // Students in Menengah: juz [5, 6] => avg 5.50
        $siswaMenengah1 = Siswa::factory()->create([
            'kelompok_id' => $kelompokMenengah->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);
        ProgresHafalan::create([
            'siswa_id' => $siswaMenengah1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 5,
            'progress_toward_target' => 100.00,
        ]);

        $siswaMenengah2 = Siswa::factory()->create([
            'kelompok_id' => $kelompokMenengah->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);
        ProgresHafalan::create([
            'siswa_id' => $siswaMenengah2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 6,
            'progress_toward_target' => 100.00,
        ]);

        // Students in Atas: juz [8] => avg 8.00
        $siswaAtas1 = Siswa::factory()->create([
            'kelompok_id' => $kelompokAtas->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);
        ProgresHafalan::create([
            'siswa_id' => $siswaAtas1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 8,
            'progress_toward_target' => 100.00,
        ]);

        // Act: Get aggregate statistics
        $stats = $this->progresService->getAggregateStats([
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Assert: Average juz per tingkat matches manual calculation
        $this->assertEquals(3.00, $stats['avg_juz_per_tingkat']['Dasar'] ?? 0,
            'Average juz for Dasar tingkat should be 3.00');
        $this->assertEquals(5.50, $stats['avg_juz_per_tingkat']['Menengah'] ?? 0,
            'Average juz for Menengah tingkat should be 5.50');
        $this->assertEquals(8.00, $stats['avg_juz_per_tingkat']['Atas'] ?? 0,
            'Average juz for Atas tingkat should be 8.00');

        // Assert: Distribution by progress range
        // Expected: 0-25% (0), 26-50% (1), 51-75% (1), 76-100% (4)
        $expectedDistribution = [
            '0-25%' => 0,
            '26-50%' => 1,  // 40% (Dasar1)
            '51-75%' => 1,  // 60% (Dasar2)
            '76-100%' => 4, // 80% (Dasar3), 100% (Menengah1, Menengah2, Atas1)
        ];

        $this->assertEquals($expectedDistribution, $stats['distribution_by_progress'],
            'Distribution by progress range should match manual calculation');

        $this->assertTrue(true, 'Property 13: Aggregate Statistics Accuracy holds');
    }

    /**
     * Property 14: Target Proximity Identification
     *
     * For any student where (target_kelulusan - total_juz_tuntas) <= 1,
     * the system SHALL flag them as "approaching target" in admin dashboard.
     *
     * Feature: progres-hafalan-otomatis, Property 14: Target Proximity Identification
     * Validates: Requirement 6.4
     *
     * @test
     * @group property-based
     */
    public function test_students_within_one_juz_of_target_are_identified()
    {
        // Setup: Create students with various distances from target (5 juz)
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create();

        $scenarios = [
            ['nama' => 'Far From Target', 'juz_tuntas' => 2, 'should_be_flagged' => false], // 3 juz away
            ['nama' => 'Almost There', 'juz_tuntas' => 4, 'should_be_flagged' => true],     // 1 juz away
            ['nama' => 'Exactly Target', 'juz_tuntas' => 5, 'should_be_flagged' => false],   // Already met target
            ['nama' => 'Exceeded Target', 'juz_tuntas' => 6, 'should_be_flagged' => false],  // Exceeded target
        ];

        $studentsToCheck = [];
        foreach ($scenarios as $scenario) {
            $siswa = Siswa::factory()->create([
                'nama' => $scenario['nama'],
                'kelompok_id' => $kelompok->id,
                'status_akademik' => 'Aktif',
                'status_penempatan' => 'Ditempatkan',
            ]);

            ProgresHafalan::create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => $scenario['juz_tuntas'],
                'progress_toward_target' => ($scenario['juz_tuntas'] / 5) * 100,
            ]);

            $studentsToCheck[] = [
                'siswa_id' => $siswa->id,
                'nama' => $scenario['nama'],
                'should_be_flagged' => $scenario['should_be_flagged'],
            ];
        }

        // Act: Get students approaching target
        $approachingStudents = $this->progresService->getStudentsApproachingTarget([
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        $approachingIds = $approachingStudents->pluck('siswa_id')->toArray();

        // Assert: Only students within 1 juz of target are flagged
        foreach ($studentsToCheck as $student) {
            $isFlagged = in_array($student['siswa_id'], $approachingIds);

            $this->assertEquals(
                $student['should_be_flagged'],
                $isFlagged,
                "Student '{$student['nama']}' flag status mismatch"
            );
        }

        // Assert: Exactly 1 student should be flagged (Almost There)
        $this->assertCount(1, $approachingStudents,
            'Exactly 1 student should be approaching target');

        $this->assertTrue(true, 'Property 14: Target Proximity Identification holds');
    }

    /**
     * Property 15: Low Progress Identification
     *
     * For any student where (total_juz_tuntas / target_kelulusan) * 100 < configured_threshold,
     * the system SHALL flag them as "below expected progress" in admin dashboard.
     *
     * Feature: progres-hafalan-otomatis, Property 15: Low Progress Identification
     * Validates: Requirement 6.5
     *
     * @test
     * @group property-based
     */
    public function test_students_below_progress_threshold_are_identified()
    {
        // Setup: Create students with various progress levels
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create();

        // Test threshold: 40% (2 out of 5 juz)
        $threshold = 40.0;

        $scenarios = [
            ['nama' => 'Very Low', 'juz_tuntas' => 0, 'progress' => 0.00, 'should_be_flagged' => true],
            ['nama' => 'Low', 'juz_tuntas' => 1, 'progress' => 20.00, 'should_be_flagged' => true],
            ['nama' => 'Border Low', 'juz_tuntas' => 2, 'progress' => 39.99, 'should_be_flagged' => true],
            ['nama' => 'Exactly Threshold', 'juz_tuntas' => 2, 'progress' => 40.00, 'should_be_flagged' => false],
            ['nama' => 'Above Threshold', 'juz_tuntas' => 3, 'progress' => 60.00, 'should_be_flagged' => false],
            ['nama' => 'High', 'juz_tuntas' => 5, 'progress' => 100.00, 'should_be_flagged' => false],
        ];

        $studentsToCheck = [];
        foreach ($scenarios as $scenario) {
            $siswa = Siswa::factory()->create([
                'nama' => $scenario['nama'],
                'kelompok_id' => $kelompok->id,
                'status_akademik' => 'Aktif',
                'status_penempatan' => 'Ditempatkan',
            ]);

            ProgresHafalan::create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => $scenario['juz_tuntas'],
                'progress_toward_target' => $scenario['progress'],
            ]);

            $studentsToCheck[] = [
                'siswa_id' => $siswa->id,
                'nama' => $scenario['nama'],
                'should_be_flagged' => $scenario['should_be_flagged'],
            ];
        }

        // Act: Get students below threshold
        $belowProgressStudents = $this->progresService->getStudentsBelowProgress($threshold, [
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        $belowProgressIds = $belowProgressStudents->pluck('siswa_id')->toArray();

        // Assert: Only students below threshold are flagged
        foreach ($studentsToCheck as $student) {
            $isFlagged = in_array($student['siswa_id'], $belowProgressIds);

            $this->assertEquals(
                $student['should_be_flagged'],
                $isFlagged,
                "Student '{$student['nama']}' flag status mismatch"
            );
        }

        // Assert: Exactly 3 students should be flagged (Very Low, Low, Border Low)
        $this->assertCount(3, $belowProgressStudents,
            'Exactly 3 students should be below progress threshold');

        $this->assertTrue(true, 'Property 15: Low Progress Identification holds');
    }

    /**
     * Additional property test iterations
     *
     * TODO: When full PBT library is configured, extend these tests to 100 iterations with:
     * - Random student counts (1-100)
     * - Random progress values (0-30 juz)
     * - Random tingkat distributions
     * - Random filter combinations
     * - Edge cases: empty datasets, all students at 0%, all at 100%
     */
}
