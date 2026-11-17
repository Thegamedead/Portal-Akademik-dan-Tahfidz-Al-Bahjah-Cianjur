<?php

namespace Tests\Unit\Services;

use App\Models\Absensi;
use App\Models\Juz;
use App\Models\Kelompok;
use App\Models\Setoran;
use App\Models\Siswa;
use App\Models\Surah;
use App\Models\TahunAjaran;
use App\Services\ProgresHafalanService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit Tests for getAverageAyatPerWeek method
 *
 * Task 14.2: Implement learning velocity calculation
 * Requirement 11.2: Calculate average ayat per week for configurable time periods
 */
class GetAverageAyatPerWeekTest extends TestCase
{
    use RefreshDatabase;

    protected ProgresHafalanService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ProgresHafalanService::class);
    }

    public function test_calculates_average_ayat_per_week_for_last_month_period(): void
    {
        // Arrange: Create siswa with setoran in the last 30 days
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create();
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $juz = Juz::create(['juz_ke' => 1, 'urutan_hafalan' => 1, 'jumlah_ayat' => 148]);
        $surah = Surah::create([
            'nama_latin' => 'Al-Fatihah',
            'nama_arab' => 'الفاتحة',
            'jumlah_ayat' => 148,
            'nomor_surah' => 1,
            'tempat_turun' => 'Makkiyah',
            'urutan_mushaf' => 1,
        ]);

        // Create 4 setoran in the last 30 days, each covering 10 ayat
        // Expected: 40 ayat / 4 weeks = 10 ayat per week
        for ($i = 0; $i < 4; $i++) {
            $absensi = Absensi::factory()->create([
                'siswa_id' => $siswa->id,
                'kelompok_id' => $kelompok->id,
                'tanggal' => Carbon::now()->subDays(5 + ($i * 5)),
                'status_kehadiran' => 'Hadir',
            ]);

            Setoran::factory()->create([
                'absensi_id' => $absensi->id,
                'juz_id' => $juz->id,  // Override factory default
                'surah_id' => $surah->id,
                'ayat_mulai' => 1 + ($i * 10),
                'ayat_selesai' => 10 + ($i * 10),
                'jenis_setoran' => 'Hafalan Baru',
                'keputusan' => 'Lanjut',
                'predikat' => 'Lancar',
            ]);
        }

        // Act: Calculate average ayat per week for last_month
        $result = $this->service->getAverageAyatPerWeek($siswa->id, 'last_month');

        // Assert: Should be 40 ayat / 4 weeks = 10.0
        $this->assertEquals(10.0, $result);
    }

    public function test_calculates_average_ayat_per_week_for_last_semester_period(): void
    {
        // Arrange: Create siswa with setoran spread over 6 months
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create();
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $juz = Juz::create(['juz_ke' => 1, 'urutan_hafalan' => 1, 'jumlah_ayat' => 148]);
        $surah = Surah::create(['nama_latin' => 'Al-Fatihah', 'nama_arab' => 'الفاتحة', 'jumlah_ayat' => 148, 'nomor_surah' => 1, 'tempat_turun' => 'Makkiyah', 'urutan_mushaf' => 1]);

        // Create 10 setoran over 6 months, each covering 15 ayat
        // Expected: 150 ayat / 26 weeks = 5.77 ayat per week
        for ($i = 0; $i < 10; $i++) {
            $absensi = Absensi::factory()->create([
                'siswa_id' => $siswa->id,
                'kelompok_id' => $kelompok->id,
                'tanggal' => Carbon::now()->subDays(180 - ($i * 15)),
                'status_kehadiran' => 'Hadir',
            ]);

            Setoran::factory()->create([
                'absensi_id' => $absensi->id,
                'juz_id' => $juz->id,
                'surah_id' => $surah->id,
                'ayat_mulai' => 1,
                'ayat_selesai' => 15,
                'jenis_setoran' => 'Hafalan Baru',
                'keputusan' => 'Lanjut',
                'predikat' => 'Lancar',
            ]);
        }

        // Act: Calculate average ayat per week for last_semester
        $result = $this->service->getAverageAyatPerWeek($siswa->id, 'last_semester');

        // Assert: Should be 150 ayat / 26 weeks ≈ 5.77
        $this->assertEquals(5.77, $result);
    }

    public function test_calculates_average_ayat_per_week_for_all_time_period(): void
    {
        // Arrange: Create siswa with setoran over a longer period
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create();
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $juz = Juz::create(['juz_ke' => 1, 'urutan_hafalan' => 1, 'jumlah_ayat' => 148]);
        $surah = Surah::create(['nama_latin' => 'Al-Fatihah', 'nama_arab' => 'الفاتحة', 'jumlah_ayat' => 148, 'nomor_surah' => 1, 'tempat_turun' => 'Makkiyah', 'urutan_mushaf' => 1]);

        // Create first setoran 100 days ago (14 weeks)
        $firstDate = Carbon::now()->subDays(100);
        $absensi1 = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'kelompok_id' => $kelompok->id,
            'tanggal' => $firstDate,
            'status_kehadiran' => 'Hadir',
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi1->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 50,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
            'predikat' => 'Lancar',
        ]);

        // Create second setoran recently
        $absensi2 = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'kelompok_id' => $kelompok->id,
            'tanggal' => Carbon::now()->subDays(5),
            'status_kehadiran' => 'Hadir',
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi2->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 51,
            'ayat_selesai' => 100,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
            'predikat' => 'Lancar',
        ]);

        // Act: Calculate average ayat per week for all_time
        $result = $this->service->getAverageAyatPerWeek($siswa->id, 'all_time');

        // Assert: Should be 100 ayat / ~15 weeks ≈ 6.67
        // Calculation: 100 days = ceil(100/7) = 15 weeks
        // 100 ayat / 15 weeks = 6.67 ayat/week
        $this->assertGreaterThan(6.0, $result);
        $this->assertLessThan(7.0, $result);
    }

    public function test_only_counts_qualifying_setoran_hafalan_baru_with_lanjut(): void
    {
        // Arrange: Create siswa with mixed setoran types
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create();
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $juz = Juz::create(['juz_ke' => 1, 'urutan_hafalan' => 1, 'jumlah_ayat' => 148]);
        $surah = Surah::create(['nama_latin' => 'Al-Fatihah', 'nama_arab' => 'الفاتحة', 'jumlah_ayat' => 148, 'nomor_surah' => 1, 'tempat_turun' => 'Makkiyah', 'urutan_mushaf' => 1]);

        // Create qualifying setoran (Hafalan Baru + Lanjut): 10 ayat
        $absensi1 = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'kelompok_id' => $kelompok->id,
            'tanggal' => Carbon::now()->subDays(10),
            'status_kehadiran' => 'Hadir',
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi1->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 10,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
            'predikat' => 'Lancar',
        ]);

        // Create non-qualifying setoran (Ulang): should NOT count
        $absensi2 = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'kelompok_id' => $kelompok->id,
            'tanggal' => Carbon::now()->subDays(8),
            'status_kehadiran' => 'Hadir',
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi2->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 11,
            'ayat_selesai' => 30,
            'jenis_setoran' => 'Ulang',
            'keputusan' => 'Lanjut',
            'predikat' => 'Lancar',
        ]);

        // Create non-qualifying setoran (Hafalan Baru + Ulang): should NOT count
        $absensi3 = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'kelompok_id' => $kelompok->id,
            'tanggal' => Carbon::now()->subDays(5),
            'status_kehadiran' => 'Hadir',
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi3->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 31,
            'ayat_selesai' => 50,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Ulang',
            'predikat' => 'Kurang Lancar',
        ]);

        // Act: Calculate average ayat per week for last_month
        $result = $this->service->getAverageAyatPerWeek($siswa->id, 'last_month');

        // Assert: Should only count 10 ayat from first setoran
        // 10 ayat / 4 weeks = 2.5 ayat per week
        $this->assertEquals(2.5, $result);
    }

    public function test_returns_zero_when_no_qualifying_setoran_found(): void
    {
        // Arrange: Create siswa without any setoran
        $kelompok = Kelompok::factory()->create();
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        // Act: Calculate average ayat per week
        $result = $this->service->getAverageAyatPerWeek($siswa->id, 'last_month');

        // Assert: Should return 0.0
        $this->assertEquals(0.0, $result);
    }

    public function test_throws_exception_for_invalid_period(): void
    {
        // Arrange: Create siswa
        $kelompok = Kelompok::factory()->create();
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        // Assert: Should throw InvalidArgumentException
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid period');

        // Act: Call with invalid period
        $this->service->getAverageAyatPerWeek($siswa->id, 'invalid_period');
    }

    public function test_excludes_setoran_outside_period_for_last_month(): void
    {
        // Arrange: Create siswa with setoran both inside and outside last_month
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create();
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $juz = Juz::create(['juz_ke' => 1, 'urutan_hafalan' => 1, 'jumlah_ayat' => 148]);
        $surah = Surah::create(['nama_latin' => 'Al-Fatihah', 'nama_arab' => 'الفاتحة', 'jumlah_ayat' => 148, 'nomor_surah' => 1, 'tempat_turun' => 'Makkiyah', 'urutan_mushaf' => 1]);

        // Create setoran within last 30 days: 20 ayat
        $absensi1 = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'kelompok_id' => $kelompok->id,
            'tanggal' => Carbon::now()->subDays(15),
            'status_kehadiran' => 'Hadir',
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi1->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 20,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
            'predikat' => 'Lancar',
        ]);

        // Create setoran outside last 30 days: should NOT count
        $absensi2 = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'kelompok_id' => $kelompok->id,
            'tanggal' => Carbon::now()->subDays(40),
            'status_kehadiran' => 'Hadir',
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi2->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 21,
            'ayat_selesai' => 50,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
            'predikat' => 'Lancar',
        ]);

        // Act: Calculate average ayat per week for last_month
        $result = $this->service->getAverageAyatPerWeek($siswa->id, 'last_month');

        // Assert: Should only count 20 ayat from setoran within period
        // 20 ayat / 4 weeks = 5.0 ayat per week
        $this->assertEquals(5.0, $result);
    }

    public function test_calculates_correct_weeks_for_all_time_with_minimum_one_week(): void
    {
        // Arrange: Create siswa with setoran only 2 days ago (less than a week)
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create();
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $juz = Juz::create(['juz_ke' => 1, 'urutan_hafalan' => 1, 'jumlah_ayat' => 148]);
        $surah = Surah::create(['nama_latin' => 'Al-Fatihah', 'nama_arab' => 'الفاتحة', 'jumlah_ayat' => 148, 'nomor_surah' => 1, 'tempat_turun' => 'Makkiyah', 'urutan_mushaf' => 1]);

        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'kelompok_id' => $kelompok->id,
            'tanggal' => Carbon::now()->subDays(2),
            'status_kehadiran' => 'Hadir',
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 10,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
            'predikat' => 'Lancar',
        ]);

        // Act: Calculate average ayat per week for all_time
        $result = $this->service->getAverageAyatPerWeek($siswa->id, 'all_time');

        // Assert: Should use minimum 1 week to avoid division by zero
        // 10 ayat / 1 week = 10.0 ayat per week
        $this->assertEquals(10.0, $result);
    }
}
