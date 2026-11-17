<?php

namespace Tests\Feature\UsulanKenaikanTingkat;

use App\Models\Kelompok;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\UsulanKenaikanTingkatService;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 6.2 — GroupFilteringPropertyTest
 *
 * Property tests for group filtering in usulan kenaikan tingkat placement.
 *
 * These tests verify that the getAvailableGroups method correctly filters
 * kelompok records based on tingkat, jenis_kelamin, and capacity constraints.
 *
 * Properties tested:
 * - Property 18: Available groups filtered by tingkat_tujuan
 * - Property 20: Full groups excluded from available list
 * - Property 36: New student placement filters by rekomendasi_tingkat
 *
 * **Validates: Requirements 6.1, 6.3, 12.3**
 */
class GroupFilteringPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    protected UsulanKenaikanTingkatService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(UsulanKenaikanTingkatService::class);
    }

    /**
     * Property 18: Available groups filtered by tingkat_tujuan
     *
     * For any tingkat_tujuan value, the system SHALL return only Kelompok
     * records where kelompok.tingkat matches that level.
     *
     * Strategy: For each tingkat level (Dasar/Menengah/Atas), create groups
     * at all levels, then verify that getAvailableGroups returns only groups
     * matching the requested tingkat_tujuan.
     *
     * @test
     */
    public function test_property_18_available_groups_filtered_by_tingkat_tujuan(): void
    {
        $this->limitTo(30)->forAll(
            // Generate tingkat combinations
            Generators::elements('Dasar', 'Menengah', 'Atas'),
            Generators::elements('Laki-laki', 'Perempuan'),
            // Random capacity values
            Generators::choose(8, 15)
        )->then(function (string $targetTingkat, string $jenisKelamin, int $kapasitas) {
            $tahunAjaran = TahunAjaran::factory()->create();
            $jenisKelompok = $jenisKelamin === 'Laki-laki' ? 'Banin' : 'Banat';

            // Setup: Create groups at all tingkat levels with same jenis
            $kelompokDasar = Kelompok::factory()->create([
                'tingkat' => 'Dasar',
                'jenis' => $jenisKelompok,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => $kapasitas,
            ]);

            $kelompokMenengah = Kelompok::factory()->create([
                'tingkat' => 'Menengah',
                'jenis' => $jenisKelompok,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => $kapasitas,
            ]);

            $kelompokAtas = Kelompok::factory()->create([
                'tingkat' => 'Atas',
                'jenis' => $jenisKelompok,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => $kapasitas,
            ]);

            // Execute: Get available groups for target tingkat
            $availableGroups = $this->service->getAvailableGroups(
                $targetTingkat,
                $jenisKelamin
            );

            // Assert: All returned groups have matching tingkat
            $this->assertGreaterThan(
                0,
                $availableGroups->count(),
                "Should return at least one group for tingkat {$targetTingkat}"
            );

            foreach ($availableGroups as $kelompok) {
                $this->assertEquals(
                    $targetTingkat,
                    $kelompok->tingkat,
                    "All returned groups should have tingkat = {$targetTingkat}"
                );
            }

            // Assert: Groups from other tingkat levels are NOT included
            $returnedIds = $availableGroups->pluck('id')->toArray();

            switch ($targetTingkat) {
                case 'Dasar':
                    $this->assertContains($kelompokDasar->id, $returnedIds);
                    $this->assertNotContains($kelompokMenengah->id, $returnedIds);
                    $this->assertNotContains($kelompokAtas->id, $returnedIds);
                    break;
                case 'Menengah':
                    $this->assertNotContains($kelompokDasar->id, $returnedIds);
                    $this->assertContains($kelompokMenengah->id, $returnedIds);
                    $this->assertNotContains($kelompokAtas->id, $returnedIds);
                    break;
                case 'Atas':
                    $this->assertNotContains($kelompokDasar->id, $returnedIds);
                    $this->assertNotContains($kelompokMenengah->id, $returnedIds);
                    $this->assertContains($kelompokAtas->id, $returnedIds);
                    break;
            }
        });
    }

    /**
     * Property 20: Full groups excluded from available list
     *
     * For any group where jumlah_siswa >= kapasitas_maks, the system SHALL
     * exclude it from the available groups list.
     *
     * Strategy: Generate various capacity scenarios (exactly full, overfull),
     * create groups with students matching those capacities, then verify that
     * full groups are excluded from available groups list.
     *
     * @test
     */
    public function test_property_20_full_groups_excluded_from_available_list(): void
    {
        $this->limitTo(30)->forAll(
            // Capacity configurations: [max_capacity, actual_students]
            Generators::choose(5, 15),  // kapasitas_maks
            Generators::choose(0, 20)   // jumlah_siswa (can be over capacity)
        )->then(function (int $kapasitasMaks, int $jumlahSiswa) {
            $tahunAjaran = TahunAjaran::factory()->create();

            // Setup: Create a group with specified capacity
            $kelompokTest = Kelompok::factory()->create([
                'tingkat' => 'Menengah',
                'jenis' => 'Banin',
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => $kapasitasMaks,
            ]);

            // Add students to the group
            Siswa::factory()->count($jumlahSiswa)->create([
                'kelompok_id' => $kelompokTest->id,
                'status_penempatan' => 'Ditempatkan',
                'jenis_kelamin' => 'Laki-laki',
            ]);

            // Create a control group with available capacity
            $kelompokAvailable = Kelompok::factory()->create([
                'tingkat' => 'Menengah',
                'jenis' => 'Banin',
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => 12,
            ]);

            // Add some students but keep it available (< capacity)
            Siswa::factory()->count(5)->create([
                'kelompok_id' => $kelompokAvailable->id,
                'status_penempatan' => 'Ditempatkan',
                'jenis_kelamin' => 'Laki-laki',
            ]);

            // Execute: Get available groups
            $availableGroups = $this->service->getAvailableGroups(
                'Menengah',
                'Laki-laki'
            );

            $returnedIds = $availableGroups->pluck('id')->toArray();

            // Assert: Behavior depends on whether test group is full
            if ($jumlahSiswa >= $kapasitasMaks) {
                // Group is full or overfull — should be EXCLUDED
                $this->assertNotContains(
                    $kelompokTest->id,
                    $returnedIds,
                    "Full group (jumlah_siswa={$jumlahSiswa}, kapasitas_maks={$kapasitasMaks}) should be excluded"
                );
            } else {
                // Group has available capacity — should be INCLUDED
                $this->assertContains(
                    $kelompokTest->id,
                    $returnedIds,
                    "Available group (jumlah_siswa={$jumlahSiswa}, kapasitas_maks={$kapasitasMaks}) should be included"
                );
            }

            // Assert: Control group (with capacity) is always included
            $this->assertContains(
                $kelompokAvailable->id,
                $returnedIds,
                "Available control group should always be included"
            );

            // Assert: All returned groups satisfy capacity constraint
            foreach ($availableGroups as $kelompok) {
                $actualJumlahSiswa = $kelompok->siswas->count();
                $this->assertLessThan(
                    $kelompok->kapasitas_maks,
                    $actualJumlahSiswa,
                    "All returned groups should have jumlah_siswa < kapasitas_maks"
                );
            }
        });
    }

    /**
     * Property 36: New student placement filters by rekomendasi_tingkat
     *
     * For any Siswa_Baru with rekomendasi_tingkat from tes penempatan, the
     * system SHALL filter available Kelompok to only those matching that tingkat.
     *
     * This property verifies the unified placement flow — the same
     * getAvailableGroups method is used for both promoted students and new
     * students, with rekomendasi_tingkat from placement test serving as the
     * filtering criteria.
     *
     * Strategy: Generate various rekomendasi_tingkat values, create groups at
     * all levels, then verify that only groups matching rekomendasi_tingkat
     * are returned.
     *
     * @test
     */
    public function test_property_36_new_student_placement_filters_by_rekomendasi_tingkat(): void
    {
        $this->limitTo(30)->forAll(
            // Rekomendasi tingkat from placement test
            Generators::elements('Dasar', 'Menengah', 'Atas'),
            // Student gender
            Generators::elements('Laki-laki', 'Perempuan'),
            // Random capacity
            Generators::choose(8, 15)
        )->then(function (string $rekomendasiTingkat, string $jenisKelamin, int $kapasitas) {
            $tahunAjaran = TahunAjaran::factory()->create();
            $jenisKelompok = $jenisKelamin === 'Laki-laki' ? 'Banin' : 'Banat';

            // Setup: Create groups at all tingkat levels with matching jenis
            $kelompokDasar = Kelompok::factory()->create([
                'tingkat' => 'Dasar',
                'jenis' => $jenisKelompok,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => $kapasitas,
            ]);

            $kelompokMenengah = Kelompok::factory()->create([
                'tingkat' => 'Menengah',
                'jenis' => $jenisKelompok,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => $kapasitas,
            ]);

            $kelompokAtas = Kelompok::factory()->create([
                'tingkat' => 'Atas',
                'jenis' => $jenisKelompok,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => $kapasitas,
            ]);

            // Execute: Get available groups using rekomendasi_tingkat
            // (This uses the SAME method as promoted students — unified flow)
            $availableGroups = $this->service->getAvailableGroups(
                $rekomendasiTingkat,
                $jenisKelamin
            );

            // Assert: Should return at least one group
            $this->assertGreaterThan(
                0,
                $availableGroups->count(),
                "Should return at least one group for rekomendasi tingkat {$rekomendasiTingkat}"
            );

            // Assert: All returned groups match rekomendasi_tingkat
            foreach ($availableGroups as $kelompok) {
                $this->assertEquals(
                    $rekomendasiTingkat,
                    $kelompok->tingkat,
                    "All groups should match rekomendasi tingkat = {$rekomendasiTingkat}"
                );

                // Also verify jenis matches jenis_kelamin
                $this->assertEquals(
                    $jenisKelompok,
                    $kelompok->jenis,
                    "All groups should have jenis matching student's jenis_kelamin"
                );
            }

            // Assert: Groups from other tingkat levels are excluded
            $returnedIds = $availableGroups->pluck('id')->toArray();

            switch ($rekomendasiTingkat) {
                case 'Dasar':
                    $this->assertContains($kelompokDasar->id, $returnedIds);
                    $this->assertNotContains($kelompokMenengah->id, $returnedIds);
                    $this->assertNotContains($kelompokAtas->id, $returnedIds);
                    break;
                case 'Menengah':
                    $this->assertNotContains($kelompokDasar->id, $returnedIds);
                    $this->assertContains($kelompokMenengah->id, $returnedIds);
                    $this->assertNotContains($kelompokAtas->id, $returnedIds);
                    break;
                case 'Atas':
                    $this->assertNotContains($kelompokDasar->id, $returnedIds);
                    $this->assertNotContains($kelompokMenengah->id, $returnedIds);
                    $this->assertContains($kelompokAtas->id, $returnedIds);
                    break;
            }
        });
    }

    /**
     * Additional Property: Gender matching in available groups
     *
     * For any student with jenis_kelamin, the system SHALL return only groups
     * where kelompok.jenis matches the student's gender (Laki-laki → Banin,
     * Perempuan → Banat).
     *
     * This validates the implicit requirement from the design that groups must
     * match student gender for placement eligibility.
     *
     * @test
     */
    public function test_additional_property_gender_matching(): void
    {
        $this->limitTo(20)->forAll(
            Generators::elements('Laki-laki', 'Perempuan'),
            Generators::choose(6, 12)
        )->then(function (string $jenisKelamin, int $kapasitas) {
            $tahunAjaran = TahunAjaran::factory()->create();

            $expectedJenis = $jenisKelamin === 'Laki-laki' ? 'Banin' : 'Banat';
            $wrongJenis = $jenisKelamin === 'Laki-laki' ? 'Banat' : 'Banin';

            // Setup: Create groups with both jenis at same tingkat
            $kelompokMatchingGender = Kelompok::factory()->create([
                'tingkat' => 'Menengah',
                'jenis' => $expectedJenis,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => $kapasitas,
            ]);

            $kelompokWrongGender = Kelompok::factory()->create([
                'tingkat' => 'Menengah',
                'jenis' => $wrongJenis,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => $kapasitas,
            ]);

            // Execute: Get available groups for student's gender
            $availableGroups = $this->service->getAvailableGroups(
                'Menengah',
                $jenisKelamin
            );

            // Assert: Only matching gender groups are returned
            $returnedIds = $availableGroups->pluck('id')->toArray();

            $this->assertContains(
                $kelompokMatchingGender->id,
                $returnedIds,
                "Group with jenis {$expectedJenis} should be included for {$jenisKelamin}"
            );

            $this->assertNotContains(
                $kelompokWrongGender->id,
                $returnedIds,
                "Group with jenis {$wrongJenis} should be excluded for {$jenisKelamin}"
            );

            // Assert: All returned groups have matching jenis
            foreach ($availableGroups as $kelompok) {
                $this->assertEquals(
                    $expectedJenis,
                    $kelompok->jenis,
                    "All returned groups should have jenis = {$expectedJenis}"
                );
            }
        });
    }
}
