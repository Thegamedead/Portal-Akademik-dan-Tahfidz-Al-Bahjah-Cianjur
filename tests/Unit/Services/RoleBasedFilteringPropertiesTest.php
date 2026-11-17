<?php

namespace Tests\Unit\Services;

use App\Models\Guru;
use App\Models\Kelompok;
use App\Models\ProgresHafalan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\ProgresHafalanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Property-Based Tests for Role-Based Filtering
 *
 * Task 15.1: Test role-based filtering properties
 * - Property 9: Role-Based Data Filtering (Guru)
 * - Property 10: Kelompok Filtering
 * - Property 12: Admin Filter Combinations
 */
class RoleBasedFilteringPropertiesTest extends TestCase
{
    use RefreshDatabase;

    private ProgresHafalanService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProgresHafalanService();
    }

    /**
     * Property 9: Role-Based Data Filtering (Guru)
     *
     * For any Guru, dashboard SHALL return progress data only for students in Kelompok
     * where guru_pembimbing_id equals the authenticated guru's ID.
     *
     * Universal property: If a student is returned in guru dashboard, then that student's
     * kelompok MUST have guru_pembimbing_id matching the guru's ID.
     *
     * // Feature: progres-hafalan-otomatis, Property 9: Role-Based Data Filtering (Guru)
     *
     * @test
     */
    public function test_property_guru_can_only_see_students_in_supervised_kelompok()
    {
        // Run property test with 100 iterations
        for ($iteration = 1; $iteration <= 100; $iteration++) {
            // Clean database for fresh iteration using DB facade
            \Illuminate\Support\Facades\DB::table('progres_hafalans')->delete();
            \Illuminate\Support\Facades\DB::table('siswas')->delete();
            \Illuminate\Support\Facades\DB::table('kelompoks')->delete();
            \Illuminate\Support\Facades\DB::table('gurus')->delete();
            \Illuminate\Support\Facades\DB::table('tahun_ajarans')->delete();

            // Generate random test data
            $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);

            // Create random number of gurus (2-5)
            $guruCount = rand(2, 5);
            $gurus = Guru::factory()->count($guruCount)->create();

            // Create random number of kelompoks (3-10), each assigned to random guru
            $kelompokCount = rand(3, 10);
            $kelompoks = collect();
            for ($i = 0; $i < $kelompokCount; $i++) {
                $kelompoks->push(Kelompok::factory()->create([
                    'guru_pembimbing_id' => $gurus->random()->id,
                ]));
            }

            // Create random number of students (5-20), each assigned to random kelompok
            $siswaCount = rand(5, 20);
            for ($i = 0; $i < $siswaCount; $i++) {
                $siswa = Siswa::factory()->create([
                    'kelompok_id' => $kelompoks->random()->id,
                ]);
                ProgresHafalan::factory()->create([
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'total_juz_tuntas' => rand(0, 15),
                    'progress_toward_target' => rand(0, 100),
                ]);
            }

            // Test each guru
            foreach ($gurus as $guru) {
                // Act: Get dashboard data for this guru (with high per_page to get all students)
                $result = $this->service->getDashboardDataForGuru($guru->id, ['per_page' => 1000]);

                // Assert: All returned students must be in kelompok supervised by this guru
                foreach ($result->items() as $progres) {
                    $siswa = Siswa::find($progres->siswa_id);
                    $kelompok = Kelompok::find($siswa->kelompok_id);

                    $this->assertEquals(
                        $guru->id,
                        $kelompok->guru_pembimbing_id,
                        "Iteration {$iteration}: Guru {$guru->id} dashboard returned student {$siswa->id} from kelompok {$kelompok->id} which is not supervised by this guru (supervised by guru {$kelompok->guru_pembimbing_id})"
                    );
                }

                // Assert: All students in guru's kelompok WITH ProgresHafalan records should be returned
                $expectedKelompokIds = Kelompok::where('guru_pembimbing_id', $guru->id)->pluck('id');
                $expectedSiswaIds = ProgresHafalan::where('tahun_ajaran_id', $tahunAjaran->id)
                    ->whereHas('siswa', function ($q) use ($expectedKelompokIds) {
                        $q->whereIn('kelompok_id', $expectedKelompokIds);
                    })
                    ->pluck('siswa_id')
                    ->sort()
                    ->values();
                $actualSiswaIds = $result->pluck('siswa_id')->sort()->values();

                $this->assertEquals(
                    $expectedSiswaIds->toArray(),
                    $actualSiswaIds->toArray(),
                    "Iteration {$iteration}: Guru {$guru->id} should see all students with ProgresHafalan in their supervised kelompok"
                );
            }
        }
    }

    /**
     * Property 10: Kelompok Filtering
     *
     * For any Guru with multiple Kelompok, applying kelompok_id filter SHALL return
     * only students where siswa.kelompok_id matches the filter value.
     *
     * Universal property: If kelompok_id filter is applied, then all returned students
     * MUST have kelompok_id exactly matching the filter value.
     *
     * // Feature: progres-hafalan-otomatis, Property 10: Kelompok Filtering
     *
     * @test
     */
    public function test_property_kelompok_filter_returns_only_matching_students()
    {
        // Run property test with 100 iterations
        for ($iteration = 1; $iteration <= 100; $iteration++) {
            // Clean database for fresh iteration using DB facade
            \Illuminate\Support\Facades\DB::table('progres_hafalans')->delete();
            \Illuminate\Support\Facades\DB::table('siswas')->delete();
            \Illuminate\Support\Facades\DB::table('kelompoks')->delete();
            \Illuminate\Support\Facades\DB::table('gurus')->delete();
            \Illuminate\Support\Facades\DB::table('tahun_ajarans')->delete();

            // Generate random test data
            $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
            $guru = Guru::factory()->create();

            // Create random number of kelompoks for this guru (2-6)
            $kelompokCount = rand(2, 6);
            $kelompoks = collect();
            for ($i = 0; $i < $kelompokCount; $i++) {
                $kelompoks->push(Kelompok::factory()->create([
                    'guru_pembimbing_id' => $guru->id,
                    'tingkat' => fake()->randomElement(['Dasar', 'Menengah', 'Atas']),
                ]));
            }

            // Create random students distributed across kelompoks
            $siswaPerKelompok = rand(2, 8);
            foreach ($kelompoks as $kelompok) {
                for ($i = 0; $i < $siswaPerKelompok; $i++) {
                    $siswa = Siswa::factory()->create([
                        'kelompok_id' => $kelompok->id,
                    ]);
                    ProgresHafalan::factory()->create([
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                        'total_juz_tuntas' => rand(0, 20),
                        'progress_toward_target' => rand(0, 100),
                    ]);
                }
            }

            // Test filtering by each kelompok
            foreach ($kelompoks as $targetKelompok) {
                // Act: Apply kelompok filter (with high per_page to get all students)
                $result = $this->service->getDashboardDataForGuru($guru->id, [
                    'kelompok_id' => $targetKelompok->id,
                    'per_page' => 1000,
                ]);

                // Assert: All returned students must belong to the filtered kelompok
                foreach ($result->items() as $progres) {
                    $siswa = Siswa::find($progres->siswa_id);

                    $this->assertEquals(
                        $targetKelompok->id,
                        $siswa->kelompok_id,
                        "Iteration {$iteration}: Kelompok filter {$targetKelompok->id} returned student {$siswa->id} from kelompok {$siswa->kelompok_id}"
                    );
                }

                // Assert: All students in target kelompok should be returned
                $expectedCount = Siswa::where('kelompok_id', $targetKelompok->id)->count();
                $this->assertEquals(
                    $expectedCount,
                    $result->total(),
                    "Iteration {$iteration}: Kelompok filter {$targetKelompok->id} should return exactly {$expectedCount} students"
                );

                // Assert: No students from other kelompoks should be returned
                $returnedKelompokIds = $result->pluck('siswa.kelompok_id')->unique();
                $this->assertCount(
                    1,
                    $returnedKelompokIds,
                    "Iteration {$iteration}: Kelompok filter should only return students from one kelompok"
                );
                $this->assertEquals(
                    $targetKelompok->id,
                    $returnedKelompokIds->first(),
                    "Iteration {$iteration}: The single kelompok_id must match the filter"
                );
            }
        }
    }

    /**
     * Property 12: Admin Filter Combinations
     *
     * For any combination of filters (kelompok_id, tingkat, tahun_ajaran_id, jenis_kelamin),
     * admin dashboard SHALL return only students matching ALL specified criteria (AND logic).
     *
     * Universal property: If multiple filters are applied, then every returned student
     * MUST satisfy ALL filter conditions simultaneously.
     *
     * // Feature: progres-hafalan-otomatis, Property 12: Admin Filter Combinations
     *
     * @test
     */
    public function test_property_admin_filters_use_and_logic_for_combinations()
    {
        // Run property test with 100 iterations
        for ($iteration = 1; $iteration <= 100; $iteration++) {
            // Clean database for fresh iteration using DB facade
            \Illuminate\Support\Facades\DB::table('progres_hafalans')->delete();
            \Illuminate\Support\Facades\DB::table('siswas')->delete();
            \Illuminate\Support\Facades\DB::table('kelompoks')->delete();
            \Illuminate\Support\Facades\DB::table('gurus')->delete();
            \Illuminate\Support\Facades\DB::table('tahun_ajarans')->delete();

            // Generate random test data
            $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);

            // Create diverse set of kelompoks with different tingkat
            $tingkatOptions = ['Dasar', 'Menengah', 'Atas'];
            $kelompoks = collect();
            foreach ($tingkatOptions as $tingkat) {
                for ($i = 0; $i < rand(2, 4); $i++) {
                    $kelompoks->push(Kelompok::factory()->create([
                        'tingkat' => $tingkat,
                    ]));
                }
            }

            // Create diverse set of students with different jenis_kelamin
            $jenisKelaminOptions = ['Laki-laki', 'Perempuan'];
            foreach ($kelompoks as $kelompok) {
                $siswaCount = rand(3, 8);
                for ($i = 0; $i < $siswaCount; $i++) {
                    $siswa = Siswa::factory()->create([
                        'kelompok_id' => $kelompok->id,
                        'jenis_kelamin' => fake()->randomElement($jenisKelaminOptions),
                    ]);
                    ProgresHafalan::factory()->create([
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                        'total_juz_tuntas' => rand(0, 25),
                        'progress_toward_target' => rand(0, 100),
                    ]);
                }
            }

            // Generate random filter combinations (1-4 filters)
            $filterCombinationsToTest = [
                // Single filters
                ['tingkat' => fake()->randomElement($tingkatOptions)],
                ['jenis_kelamin' => fake()->randomElement($jenisKelaminOptions)],
                ['kelompok_id' => $kelompoks->random()->id],

                // Two filter combinations
                [
                    'tingkat' => fake()->randomElement($tingkatOptions),
                    'jenis_kelamin' => fake()->randomElement($jenisKelaminOptions),
                ],
                [
                    'kelompok_id' => $kelompoks->random()->id,
                    'jenis_kelamin' => fake()->randomElement($jenisKelaminOptions),
                ],

                // Three filter combination
                [
                    'tingkat' => fake()->randomElement($tingkatOptions),
                    'jenis_kelamin' => fake()->randomElement($jenisKelaminOptions),
                    'tahun_ajaran_id' => $tahunAjaran->id,
                ],

                // All four filters
                [
                    'kelompok_id' => $kelompoks->random()->id,
                    'tingkat' => fake()->randomElement($tingkatOptions),
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'jenis_kelamin' => fake()->randomElement($jenisKelaminOptions),
                ],
            ];

            // Test each filter combination
            foreach ($filterCombinationsToTest as $filterIndex => $filters) {
                // Add per_page to ensure we get all students
                $filters['per_page'] = 1000;

                // Act: Apply filter combination
                $result = $this->service->getDashboardDataForAdmin($filters);

                // Assert: Every returned student must satisfy ALL filter conditions
                foreach ($result->items() as $progres) {
                    $siswa = Siswa::with('kelompok')->find($progres->siswa_id);

                    // Check kelompok_id filter (if specified)
                    if (isset($filters['kelompok_id'])) {
                        $this->assertEquals(
                            $filters['kelompok_id'],
                            $siswa->kelompok_id,
                            "Iteration {$iteration}, Filter combo {$filterIndex}: Student {$siswa->id} has kelompok_id {$siswa->kelompok_id} but filter requires {$filters['kelompok_id']}"
                        );
                    }

                    // Check tingkat filter (if specified)
                    if (isset($filters['tingkat'])) {
                        $this->assertEquals(
                            $filters['tingkat'],
                            $siswa->kelompok->tingkat,
                            "Iteration {$iteration}, Filter combo {$filterIndex}: Student {$siswa->id} has tingkat {$siswa->kelompok->tingkat} but filter requires {$filters['tingkat']}"
                        );
                    }

                    // Check jenis_kelamin filter (if specified)
                    if (isset($filters['jenis_kelamin'])) {
                        $this->assertEquals(
                            $filters['jenis_kelamin'],
                            $siswa->jenis_kelamin,
                            "Iteration {$iteration}, Filter combo {$filterIndex}: Student {$siswa->id} has jenis_kelamin {$siswa->jenis_kelamin} but filter requires {$filters['jenis_kelamin']}"
                        );
                    }

                    // Check tahun_ajaran_id filter (if specified)
                    if (isset($filters['tahun_ajaran_id'])) {
                        $this->assertEquals(
                            $filters['tahun_ajaran_id'],
                            $progres->tahun_ajaran_id,
                            "Iteration {$iteration}, Filter combo {$filterIndex}: Progress record has tahun_ajaran_id {$progres->tahun_ajaran_id} but filter requires {$filters['tahun_ajaran_id']}"
                        );
                    }
                }

                // Assert: Verify AND logic by checking against manual query
                $expectedQuery = ProgresHafalan::query()
                    ->join('siswas', 'progres_hafalans.siswa_id', '=', 'siswas.id')
                    ->join('kelompoks', 'siswas.kelompok_id', '=', 'kelompoks.id')
                    ->where('progres_hafalans.tahun_ajaran_id', $filters['tahun_ajaran_id'] ?? $tahunAjaran->id);

                if (isset($filters['kelompok_id'])) {
                    $expectedQuery->where('siswas.kelompok_id', $filters['kelompok_id']);
                }
                if (isset($filters['tingkat'])) {
                    $expectedQuery->where('kelompoks.tingkat', $filters['tingkat']);
                }
                if (isset($filters['jenis_kelamin'])) {
                    $expectedQuery->where('siswas.jenis_kelamin', $filters['jenis_kelamin']);
                }

                $expectedCount = $expectedQuery->count();
                $actualCount = $result->total();

                $this->assertEquals(
                    $expectedCount,
                    $actualCount,
                    "Iteration {$iteration}, Filter combo {$filterIndex}: Expected {$expectedCount} students but got {$actualCount}. Filter AND logic may be incorrect."
                );
            }
        }
    }

    /**
     * Additional property: Verify no cross-contamination between gurus
     *
     * For any two different Gurus, the set of students returned for Guru A
     * should have NO intersection with students returned for Guru B,
     * UNLESS they share a kelompok (which should not happen in valid data).
     *
     * // Feature: progres-hafalan-otomatis, Property 9 (supplementary)
     *
     * @test
     */
    public function test_property_different_gurus_see_disjoint_student_sets()
    {
        // Run property test with 50 iterations (fewer since it's more expensive)
        for ($iteration = 1; $iteration <= 50; $iteration++) {
            // Clean database for fresh iteration using DB facade
            \Illuminate\Support\Facades\DB::table('progres_hafalans')->delete();
            \Illuminate\Support\Facades\DB::table('siswas')->delete();
            \Illuminate\Support\Facades\DB::table('kelompoks')->delete();
            \Illuminate\Support\Facades\DB::table('gurus')->delete();
            \Illuminate\Support\Facades\DB::table('tahun_ajarans')->delete();

            // Generate test data
            $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);

            // Create exactly 2 gurus
            $guru1 = Guru::factory()->create();
            $guru2 = Guru::factory()->create();

            // Create kelompoks, ensuring no overlap (each guru has exclusive kelompoks)
            $kelompoksGuru1 = collect();
            $kelompoksGuru2 = collect();

            for ($i = 0; $i < rand(2, 4); $i++) {
                $kelompoksGuru1->push(Kelompok::factory()->create([
                    'guru_pembimbing_id' => $guru1->id,
                ]));
            }

            for ($i = 0; $i < rand(2, 4); $i++) {
                $kelompoksGuru2->push(Kelompok::factory()->create([
                    'guru_pembimbing_id' => $guru2->id,
                ]));
            }

            // Create students for each guru's kelompoks
            foreach ($kelompoksGuru1 as $kelompok) {
                for ($i = 0; $i < rand(2, 5); $i++) {
                    $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
                    ProgresHafalan::factory()->create([
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                    ]);
                }
            }

            foreach ($kelompoksGuru2 as $kelompok) {
                for ($i = 0; $i < rand(2, 5); $i++) {
                    $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
                    ProgresHafalan::factory()->create([
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran_id' => $tahunAjaran->id,
                    ]);
                }
            }

            // Act: Get dashboard data for both gurus (with high per_page to get all students)
            $resultGuru1 = $this->service->getDashboardDataForGuru($guru1->id, ['per_page' => 1000]);
            $resultGuru2 = $this->service->getDashboardDataForGuru($guru2->id, ['per_page' => 1000]);

            // Extract student IDs
            $siswaIdsGuru1 = $resultGuru1->pluck('siswa_id')->toArray();
            $siswaIdsGuru2 = $resultGuru2->pluck('siswa_id')->toArray();

            // Assert: No intersection between the two sets
            $intersection = array_intersect($siswaIdsGuru1, $siswaIdsGuru2);

            $this->assertEmpty(
                $intersection,
                "Iteration {$iteration}: Guru {$guru1->id} and Guru {$guru2->id} have overlapping students: " . implode(', ', $intersection)
            );

            // Assert: Union of both sets should equal total students WITH ProgresHafalan records
            $totalStudentsWithProgres = ProgresHafalan::where('tahun_ajaran_id', $tahunAjaran->id)
                ->whereHas('siswa', function ($q) use ($kelompoksGuru1, $kelompoksGuru2) {
                    $q->whereIn('kelompok_id', $kelompoksGuru1->pluck('id')->merge($kelompoksGuru2->pluck('id')));
                })
                ->count();
            $unionCount = count(array_unique(array_merge($siswaIdsGuru1, $siswaIdsGuru2)));

            $this->assertEquals(
                $totalStudentsWithProgres,
                $unionCount,
                "Iteration {$iteration}: Union of both guru dashboards should equal total students with ProgresHafalan"
            );
        }
    }

    /**
     * Additional property: Empty result set for valid filters
     *
     * For any valid filter combination that matches zero students,
     * the system SHALL return an empty result set without errors.
     *
     * // Feature: progres-hafalan-otomatis, Property 12 (edge case)
     *
     * @test
     */
    public function test_property_admin_filters_handle_empty_results_gracefully()
    {
        // Run property test with 50 iterations
        for ($iteration = 1; $iteration <= 50; $iteration++) {
            // Clean database for fresh iteration using DB facade
            \Illuminate\Support\Facades\DB::table('progres_hafalans')->delete();
            \Illuminate\Support\Facades\DB::table('siswas')->delete();
            \Illuminate\Support\Facades\DB::table('kelompoks')->delete();
            \Illuminate\Support\Facades\DB::table('gurus')->delete();
            \Illuminate\Support\Facades\DB::table('tahun_ajarans')->delete();

            // Generate test data with specific characteristics
            $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
            $kelompok = Kelompok::factory()->create(['tingkat' => 'Dasar']);

            // Create only Laki-laki students
            for ($i = 0; $i < rand(3, 8); $i++) {
                $siswa = Siswa::factory()->create([
                    'kelompok_id' => $kelompok->id,
                    'jenis_kelamin' => 'Laki-laki', // Only males
                ]);
                ProgresHafalan::factory()->create([
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                ]);
            }

            // Act: Apply filter that should return empty (filter for Perempuan)
            $result = $this->service->getDashboardDataForAdmin([
                'tingkat' => 'Dasar',
                'jenis_kelamin' => 'Perempuan', // Should match zero students
            ]);

            // Assert: Should return empty result without errors
            $this->assertCount(
                0,
                $result->items(),
                "Iteration {$iteration}: Filter for non-existent combination should return empty array"
            );

            $this->assertEquals(
                0,
                $result->total(),
                "Iteration {$iteration}: Total should be 0 for empty result"
            );

            // Assert: Paginator structure should still be valid
            $this->assertIsInt($result->currentPage());
            $this->assertIsInt($result->lastPage());
            $this->assertEquals(1, $result->currentPage());
            $this->assertEquals(1, $result->lastPage());
        }
    }
}
