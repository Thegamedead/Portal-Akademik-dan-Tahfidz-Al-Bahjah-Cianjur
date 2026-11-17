<?php

namespace Tests\Unit\Services;

use App\Models\Juz;
use App\Models\Absensi;
use App\Models\Kelompok;
use App\Models\Setoran;
use App\Models\Siswa;
use App\Models\Surah;
use App\Models\TahunAjaran;
use App\Services\ProgressValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit Tests for ProgressValidator Service
 *
 * Task 10: Checkpoint verification - all validation methods
 */
class ProgressValidatorTest extends TestCase
{
    use RefreshDatabase;

    protected ProgressValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = app(ProgressValidator::class);
    }

    /** @test */
    public function it_can_validate_integrity_returns_valid_for_correct_data()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create();
        $kelompok = Kelompok::factory()->create(['tahun_ajaran_id' => $tahunAjaran->id]);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $juz = Juz::factory()->create();
        $surah = Surah::factory()->create(['jumlah_ayat' => 50]);
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'kelompok_id' => $kelompok->id,
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 10,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        // Act
        $result = $this->validator->validateIntegrity($siswa->id, $tahunAjaran->id);

        // Assert
        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }

    /** @test */
    public function it_can_detect_invalid_referensi_quran_references()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create();
        $kelompok = Kelompok::factory()->create(['tahun_ajaran_id' => $tahunAjaran->id]);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $juz = Juz::factory()->create();
        $surah = Surah::factory()->create(['jumlah_ayat' => 50]);
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'kelompok_id' => $kelompok->id,
        ]);

        // Create setoran with invalid ayat range
        Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 100, // Exceeds surah jumlah_ayat (50)
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        // Act
        $errors = $this->validator->validateReferensi($siswa->id, $tahunAjaran->id);

        // Assert
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('out of range', implode(', ', $errors));
    }

    /** @test */
    public function it_can_detect_gaps_in_ayat_sequence()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create();
        $kelompok = Kelompok::factory()->create(['tahun_ajaran_id' => $tahunAjaran->id]);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $juz = Juz::factory()->create([
            'jumlah_ayat' => 100,
            'awal_surah' => 1,
            'awal_ayat' => 1,
            'akhir_surah' => 1,
            'akhir_ayat' => 100,
        ]);
        $surah = Surah::factory()->create(['id' => 1, 'jumlah_ayat' => 100]);
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'kelompok_id' => $kelompok->id,
        ]);

        // Create coverage with gap: [1-30], [50-100]
        // Gap: [31-49]
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
            'ayat_selesai' => 100,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        // Act
        $gaps = $this->validator->detectGaps($siswa->id, $juz->id, $tahunAjaran->id);

        // Assert
        $this->assertNotEmpty($gaps);
        $this->assertCount(1, $gaps);
        $this->assertEquals(31, $gaps[0]['ayat_mulai']);
        $this->assertEquals(49, $gaps[0]['ayat_selesai']);
    }

    /** @test */
    public function it_can_detect_overlapping_ayat_ranges()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create();
        $kelompok = Kelompok::factory()->create(['tahun_ajaran_id' => $tahunAjaran->id]);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $juz = Juz::factory()->create();
        $surah = Surah::factory()->create(['jumlah_ayat' => 100]);
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'kelompok_id' => $kelompok->id,
        ]);

        // Create overlapping setoran: [1-50], [40-80]
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

        // Act
        $overlaps = $this->validator->detectOverlaps($siswa->id, $juz->id, $tahunAjaran->id);

        // Assert
        $this->assertNotEmpty($overlaps);
        $this->assertCount(1, $overlaps);
        $overlap = $overlaps->first();
        $this->assertEquals(40, $overlap['overlap_range']['ayat_mulai']);
        $this->assertEquals(50, $overlap['overlap_range']['ayat_selesai']);
    }

    /** @test */
    public function it_can_verify_total_ayat_does_not_exceed_quran_total()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        Juz::factory()->count(3)->create(['jumlah_ayat' => 100]);

        // Act
        $error = $this->validator->verifyTotalAyat($siswa->id, $tahunAjaran->id);

        // Assert
        $this->assertNull($error, 'Valid data should not have total ayat error');
    }
}
