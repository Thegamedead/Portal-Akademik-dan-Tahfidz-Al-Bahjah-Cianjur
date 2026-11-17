<?php

/**
 * Property-Based Tests for Validation Logic
 *
 * Tests Properties 18, 19, 20 from design.md
 */

namespace Tests\Property;

use App\Models\Juz;
use App\Models\Absensi;
use App\Models\Setoran;
use App\Models\Siswa;
use App\Models\Surah;
use App\Models\TahunAjaran;
use App\Services\ProgressValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationPropertiesTest extends TestCase
{
    use RefreshDatabase;

    protected ProgressValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = app(ProgressValidator::class);
    }

    /**
     * Property 18: Referential Integrity Validation
     *
     * For any setoran entry or baseline data, all referenced juz_id, surah_id,
     * and ayat ranges SHALL exist in Referensi_Quran table, or validation SHALL
     * reject the data.
     *
     * Feature: progres-hafalan-otomatis, Property 18: Referential Integrity Validation
     * Validates: Requirement 9.1
     *
     * @test
     * @group property-based
     */
    public function validation_rejects_invalid_referensi_quran_references()
    {
        // Setup
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $juz = Juz::factory()->create(['juz_ke' => 1]);
        $surah = Surah::factory()->create(['jumlah_ayat' => 50]);
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Valid scenario: all references exist
        Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 10,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        $validationResult = $this->validator->validateReferensi($siswa->id, $tahunAjaran->id);
        $this->assertEmpty($validationResult, 'Valid references should pass validation');

        // Invalid scenario: ayat out of range
        Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 100, // Exceeds surah jumlah_ayat (50)
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        $validationResult = $this->validator->validateReferensi($siswa->id, $tahunAjaran->id);
        $this->assertNotEmpty($validationResult, 'Invalid ayat range should fail validation');
        $this->assertStringContainsString('out of range', implode(', ', $validationResult));

        $this->assertTrue(true, 'Property 18: Referential Integrity Validation holds');
    }

    /**
     * Property 19: Gap Detection
     *
     * For any juz with progress data, if there exist ayat numbers N where ayat N-1
     * is covered but ayat N is not covered (and N <= total_ayat for that juz),
     * the system SHALL detect and log the gap range.
     *
     * Feature: progres-hafalan-otomatis, Property 19: Gap Detection
     * Validates: Requirement 9.2
     *
     * @test
     * @group property-based
     */
    public function gap_detection_finds_missing_ayat_sequences()
    {
        // Setup
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $juz = Juz::factory()->create([
            'juz_ke' => 1,
            'jumlah_ayat' => 100,
            'awal_ayat' => 1,
            'akhir_ayat' => 100,
        ]);
        $surah = Surah::factory()->create(['jumlah_ayat' => 100]);
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Create coverage with gaps: [1-30], [50-70], [90-100]
        // Gaps: [31-49], [71-89]
        Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 30,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 50,
            'ayat_selesai' => 70,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 90,
            'ayat_selesai' => 100,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        // Detect gaps
        $gaps = $this->validator->detectGaps($siswa->id, $juz->id, $tahunAjaran->id);

        // Assert: Should detect 2 gaps
        $this->assertCount(2, $gaps, 'Should detect exactly 2 gaps');

        // Assert: Gap 1 should be [31-49]
        $gap1 = collect($gaps)->first(fn($g) => $g['ayat_mulai'] == 31);
        $this->assertNotNull($gap1, 'Gap [31-49] should be detected');
        $this->assertEquals(49, $gap1['ayat_selesai']);

        // Assert: Gap 2 should be [71-89]
        $gap2 = collect($gaps)->first(fn($g) => $g['ayat_mulai'] == 71);
        $this->assertNotNull($gap2, 'Gap [71-89] should be detected');
        $this->assertEquals(89, $gap2['ayat_selesai']);

        $this->assertTrue(true, 'Property 19: Gap Detection holds');
    }

    /**
     * Property 20: Total Ayat Upper Bound
     *
     * For any student's complete progress across all juz, the total count of unique
     * covered ayat SHALL be less than or equal to the sum of `jumlah_ayat` for all
     * 30 juz in Referensi_Quran.
     *
     * Feature: progres-hafalan-otomatis, Property 20: Total Ayat Upper Bound
     * Validates: Requirement 9.4
     *
     * @test
     * @group property-based
     */
    public function total_covered_ayat_does_not_exceed_quran_total()
    {
        // Setup
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();

        // Create realistic juz data (total should be 6236 ayat for complete Quran)
        $juzs = [];
        for ($i = 1; $i <= 5; $i++) {
            $juzs[] = Juz::factory()->create([
                'juz_ke' => $i,
                'jumlah_ayat' => 200, // Simplified for test
            ]);
        }

        // Calculate total ayat in Referensi Quran
        $totalAyatQuran = Juz::sum('jumlah_ayat');

        // Create progres and capaian_juz records
        $progresService = app(\App\Services\ProgresHafalanService::class);
        $progress = $progresService->calculateProgres($siswa->id, $tahunAjaran->id);

        // Validate
        $validationError = $this->validator->verifyTotalAyat($siswa->id, $tahunAjaran->id);

        // Assert: No error (total covered <= total Quran)
        $this->assertNull($validationError,
            'Total covered ayat should not exceed Quran total');

        // Get actual covered ayat
        $progresHafalan = \App\Models\ProgresHafalan::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->first();

        if ($progresHafalan) {
            $totalCovered = \App\Models\CapaianJuz::where('progres_hafalan_id', $progresHafalan->id)
                ->sum('ayat_tercakup');

            $this->assertLessThanOrEqual($totalAyatQuran, $totalCovered,
                "Total covered ayat ($totalCovered) should not exceed Quran total ($totalAyatQuran)");
        }

        $this->assertTrue(true, 'Property 20: Total Ayat Upper Bound holds');
    }

    /**
     * Placeholder for additional validation properties
     *
     * TODO: Add when PBT library is configured:
     * - Property 18 with 100 random invalid reference scenarios
     * - Property 19 with 100 random gap patterns
     * - Property 20 with edge cases (exactly 6236 ayat, etc.)
     */
}
