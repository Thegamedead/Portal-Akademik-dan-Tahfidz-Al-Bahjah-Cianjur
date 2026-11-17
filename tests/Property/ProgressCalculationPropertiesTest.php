<?php

/**
 * Property-Based Tests for Progress Calculation Logic
 *
 * Tests Properties 3, 4, 5, 6, 7, 8 from design.md
 */

namespace Tests\Property;

use App\Models\Juz;
use App\Models\ProgresHafalan;
use App\Models\Absensi;
use App\Models\Setoran;
use App\Models\Siswa;
use App\Models\Surah;
use App\Models\TahunAjaran;
use App\Services\ProgresHafalanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressCalculationPropertiesTest extends TestCase
{
    use RefreshDatabase;

    protected ProgresHafalanService $progresService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->progresService = app(ProgresHafalanService::class);
    }

    /**
     * Property 3: Setoran Contribution Rule
     *
     * For any setoran entry, it SHALL contribute to student progress if and only if
     * `jenis_setoran = 'Hafalan Baru'` AND `keputusan = 'Lanjut'`.
     * Ulang (review) SHALL NEVER contribute regardless of keputusan value.
     *
     * Feature: progres-hafalan-otomatis, Property 3: Setoran Contribution Rule
     * Validates: Requirements 2.1, 2.2, 2.3
     *
     * @test
     * @group property-based
     */
    public function only_hafalan_baru_with_lanjut_contributes_to_progress()
    {
        // Setup
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $juz = Juz::factory()->create(['juz_ke' => 1, 'jumlah_ayat' => 148]);
        $surah = Surah::factory()->create(['jumlah_ayat' => 148]);
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Test scenarios
        $scenarios = [
            ['jenis' => 'Hafalan Baru', 'keputusan' => 'Lanjut', 'should_contribute' => true],
            ['jenis' => 'Hafalan Baru', 'keputusan' => 'Ulang', 'should_contribute' => false],
            ['jenis' => 'Ulang', 'keputusan' => 'Lanjut', 'should_contribute' => false],
            ['jenis' => 'Ulang', 'keputusan' => 'Ulang', 'should_contribute' => false],
        ];

        foreach ($scenarios as $scenario) {
            // Clean previous data
            Setoran::query()->delete();
            ProgresHafalan::query()->delete();

            // Create setoran
            Setoran::factory()->create([
                'absensi_id' => $absensi->id,
                'juz_id' => $juz->id,
                'surah_id' => $surah->id,
                'ayat_mulai' => 1,
                'ayat_selesai' => 10,
                'jenis_setoran' => $scenario['jenis'],
                'keputusan' => $scenario['keputusan'],
            ]);

            // Calculate progress
            $progress = $this->progresService->calculateProgres($siswa->id, $tahunAjaran->id);
            $ranges = $this->progresService->getCoveredAyatRanges($siswa->id, $juz->id, $tahunAjaran->id);

            // Assert
            if ($scenario['should_contribute']) {
                $this->assertGreaterThan(0, $ranges->count(),
                    "Scenario {$scenario['jenis']}/{$scenario['keputusan']} should contribute");
            } else {
                $this->assertEquals(0, $ranges->count(),
                    "Scenario {$scenario['jenis']}/{$scenario['keputusan']} should NOT contribute");
            }
        }

        $this->assertTrue(true, 'Property 3: Setoran Contribution Rule holds');
    }

    /**
     * Property 4: Progress Set Union
     *
     * For any student's baseline and qualifying setoran entries, total progress
     * SHALL equal the union set of all covered ayat, with each unique ayat counted
     * exactly once regardless of overlapping ranges.
     *
     * Feature: progres-hafalan-otomatis, Property 4: Progress Set Union
     * Validates: Requirements 2.4, 2.5, 9.3
     *
     * @test
     * @group property-based
     */
    public function progress_equals_union_of_baseline_and_qualifying_setoran()
    {
        // Setup
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $juz = Juz::factory()->create(['juz_ke' => 1, 'jumlah_ayat' => 148, 'awal_ayat' => 1, 'akhir_ayat' => 148]);
        $surah = Surah::factory()->create(['jumlah_ayat' => 148]);
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Create overlapping setoran: [1-50], [40-80], [75-100]
        // Union should be: [1-100] = 100 ayat
        Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 50,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 40,
            'ayat_selesai' => 80,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 75,
            'ayat_selesai' => 100,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        // Calculate progress
        $progress = $this->progresService->calculateProgres($siswa->id, $tahunAjaran->id);
        $capaianJuz = $progress['capaian_juz']->firstWhere('juz_id', $juz->id);

        // Assert: Union result is 100 ayat (not 50 + 41 + 26 = 117)
        $this->assertEquals(100, $capaianJuz->ayat_tercakup,
            'Overlapping ranges should be merged into union');

        $this->assertTrue(true, 'Property 4: Progress Set Union holds');
    }

    /**
     * Property 5: Juz Completion Threshold
     *
     * For any juz, it SHALL be marked as `status_tuntas = true` if and only if
     * 100% of its ayat are covered. Any missing ayat SHALL keep `status_tuntas = false`.
     *
     * Feature: progres-hafalan-otomatis, Property 5: Juz Completion Threshold
     * Validates: Requirements 3.2, 3.3, 3.5
     *
     * @test
     * @group property-based
     */
    public function juz_marked_tuntas_only_when_100_percent_covered()
    {
        // Setup
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $juz = Juz::factory()->create(['juz_ke' => 1, 'jumlah_ayat' => 100, 'awal_ayat' => 1, 'akhir_ayat' => 100]);
        $surah = Surah::factory()->create(['jumlah_ayat' => 100]);
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Test scenarios: 99%, 100%, 101% (impossible but test boundary)
        $coverageScenarios = [
            ['ayat_selesai' => 99, 'expected_tuntas' => false],
            ['ayat_selesai' => 100, 'expected_tuntas' => true],
        ];

        foreach ($coverageScenarios as $scenario) {
            // Clean previous data
            Setoran::query()->delete();
            ProgresHafalan::query()->delete();

            // Create setoran covering ayat 1 to scenario value
            Setoran::factory()->create([
                'absensi_id' => $absensi->id,
                'juz_id' => $juz->id,
                'surah_id' => $surah->id,
                'ayat_mulai' => 1,
                'ayat_selesai' => $scenario['ayat_selesai'],
                'jenis_setoran' => 'Hafalan Baru',
                'keputusan' => 'Lanjut',
            ]);

            // Calculate
            $progress = $this->progresService->calculateProgres($siswa->id, $tahunAjaran->id);
            $capaianJuz = $progress['capaian_juz']->firstWhere('juz_id', $juz->id);

            // Assert
            $this->assertEquals($scenario['expected_tuntas'], $capaianJuz->status_tuntas,
                "Coverage {$scenario['ayat_selesai']}/100 should have status_tuntas = {$scenario['expected_tuntas']}");
        }

        $this->assertTrue(true, 'Property 5: Juz Completion Threshold holds');
    }

    /**
     * Placeholder for additional calculation properties
     *
     * TODO: Add when PBT library is configured:
     * - Property 6: Juz Completion Reactivity
     * - Property 7: Dashboard Progress Accuracy
     * - Property 8: Progress-to-Target Calculation
     * Each with 100 random iterations
     */
}
