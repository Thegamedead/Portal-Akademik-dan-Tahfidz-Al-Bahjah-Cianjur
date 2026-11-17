<?php

namespace Tests\Unit\Services;

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

/**
 * Unit Tests for ProgresHafalanService
 *
 * Task 10: Checkpoint verification - calculation, set union, juz tuntas
 */
class ProgresHafalanServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProgresHafalanService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ProgresHafalanService::class);
    }

    /** @test */
    public function test_it_can_get_covered_ayat_ranges_from_setoran()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $juz = Juz::factory()->create(['juz_ke' => 1]);
        $surah = Surah::factory()->create();
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
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
        $ranges = $this->service->getCoveredAyatRanges($siswa->id, $juz->id, $tahunAjaran->id);

        // Assert
        $this->assertCount(1, $ranges);
        $this->assertEquals(1, $ranges->first()['ayat_mulai']);
        $this->assertEquals(10, $ranges->first()['ayat_selesai']);
    }

    /** @test */
    public function test_it_only_includes_hafalan_baru_with_lanjut_in_covered_ranges()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $juz = Juz::factory()->create();
        $surah = Surah::factory()->create();
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Create non-qualifying setoran
        Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 10,
            'jenis_setoran' => 'Ulang', // Should not contribute
            'keputusan' => 'Lanjut',
        ]);

        // Act
        $ranges = $this->service->getCoveredAyatRanges($siswa->id, $juz->id, $tahunAjaran->id);

        // Assert
        $this->assertCount(0, $ranges, 'Ulang (review) should not contribute');
    }

    /** @test */
    public function test_it_can_calculate_juz_tuntas_with_100_percent_coverage()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $juz = Juz::factory()->create(['juz_ke' => 1, 'jumlah_ayat' => 50, 'awal_ayat' => 1, 'akhir_ayat' => 50]);
        $surah = Surah::factory()->create(['jumlah_ayat' => 50]);
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Create setoran covering all 50 ayat
        Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 50,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        // Act
        $isTuntas = $this->service->calculateJuzTuntas($siswa->id, $juz->id, $tahunAjaran->id);

        // Assert
        $this->assertTrue($isTuntas, '100% coverage should mark juz as tuntas');
    }

    /** @test */
    public function test_it_does_not_mark_juz_tuntas_with_partial_coverage()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $juz = Juz::factory()->create(['juz_ke' => 1, 'jumlah_ayat' => 100, 'awal_ayat' => 1, 'akhir_ayat' => 100]);
        $surah = Surah::factory()->create(['jumlah_ayat' => 100]);
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Create setoran covering only 99 ayat
        Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 99,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        // Act
        $isTuntas = $this->service->calculateJuzTuntas($siswa->id, $juz->id, $tahunAjaran->id);

        // Assert
        $this->assertFalse($isTuntas, '99% coverage should NOT mark juz as tuntas');
    }

    /** @test */
    public function test_it_can_calculate_complete_progress_for_student()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $juz = Juz::factory()->create(['juz_ke' => 1, 'jumlah_ayat' => 100, 'awal_ayat' => 1, 'akhir_ayat' => 100]);
        $surah = Surah::factory()->create(['jumlah_ayat' => 100]);
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Complete 1 juz, partial on another
        Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 100,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        // Act
        $progress = $this->service->calculateProgres($siswa->id, $tahunAjaran->id);

        // Assert
        $this->assertIsArray($progress);
        $this->assertArrayHasKey('total_juz_tuntas', $progress);
        $this->assertArrayHasKey('juz_berjalan', $progress);
        $this->assertArrayHasKey('juz_berjalan_percentage', $progress);
        $this->assertArrayHasKey('progress_toward_target', $progress);
        $this->assertArrayHasKey('capaian_juz', $progress);

        $this->assertEquals(1, $progress['total_juz_tuntas']);
    }

    /** @test */
    public function test_it_merges_overlapping_ayat_ranges_using_set_union()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $juz = Juz::factory()->create(['juz_ke' => 1, 'jumlah_ayat' => 100, 'awal_ayat' => 1, 'akhir_ayat' => 100]);
        $surah = Surah::factory()->create(['jumlah_ayat' => 100]);
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Create overlapping ranges: [1-50], [40-70]
        // Union should be [1-70] = 70 ayat
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
            'ayat_selesai' => 70,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        // Act
        $progress = $this->service->calculateProgres($siswa->id, $tahunAjaran->id);
        $capaianJuz = $progress['capaian_juz']->firstWhere('juz_id', $juz->id);

        // Assert
        $this->assertEquals(70, $capaianJuz->ayat_tercakup,
            'Overlapping ranges [1-50] and [40-70] should merge to [1-70] = 70 ayat');
    }

    /** @test */
    public function test_it_can_recalculate_after_new_setoran()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $juz = Juz::factory()->create(['jumlah_ayat' => 100]);
        $surah = Surah::factory()->create();
        $absensi = Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        $setoran = Setoran::factory()->create([
            'absensi_id' => $absensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 10,
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        // Act
        $this->service->recalculateAfterSetoran($setoran->id);

        // Assert
        $progresHafalan = ProgresHafalan::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->first();

        $this->assertNotNull($progresHafalan);
    }

    /** @test */
    public function test_it_can_calculate_aggregate_statistics_without_filters()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompokDasar = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $kelompokMenengah = \App\Models\Kelompok::factory()->create(['tingkat' => 'Menengah']);

        // Create students with different progress levels
        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);
        $siswa2 = Siswa::factory()->create([
            'kelompok_id' => $kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);
        $siswa3 = Siswa::factory()->create([
            'kelompok_id' => $kelompokMenengah->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Create ProgresHafalan records with varying progress
        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 2,
            'progress_toward_target' => 40.0, // 2/5 * 100 = 40% (26-50% range)
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 1,
            'progress_toward_target' => 20.0, // 1/5 * 100 = 20% (0-25% range)
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa3->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 4,
            'progress_toward_target' => 80.0, // 4/5 * 100 = 80% (76-100% range)
        ]);

        // Act
        $stats = $this->service->getAggregateStats();

        // Assert
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('avg_juz_per_tingkat', $stats);
        $this->assertArrayHasKey('distribution_by_progress', $stats);
        $this->assertArrayHasKey('completion_trend', $stats);

        // Check average juz per tingkat
        $this->assertArrayHasKey('Dasar', $stats['avg_juz_per_tingkat']);
        $this->assertArrayHasKey('Menengah', $stats['avg_juz_per_tingkat']);
        $this->assertEquals(1.5, $stats['avg_juz_per_tingkat']['Dasar']); // (2 + 1) / 2
        $this->assertEquals(4.0, $stats['avg_juz_per_tingkat']['Menengah']); // 4 / 1

        // Check distribution by progress range
        $this->assertEquals(1, $stats['distribution_by_progress']['0-25%']);
        $this->assertEquals(1, $stats['distribution_by_progress']['26-50%']);
        $this->assertEquals(0, $stats['distribution_by_progress']['51-75%']);
        $this->assertEquals(1, $stats['distribution_by_progress']['76-100%']);
    }

    /** @test */
    public function test_it_filters_aggregate_stats_by_tingkat()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompokDasar = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $kelompokMenengah = \App\Models\Kelompok::factory()->create(['tingkat' => 'Menengah']);

        $siswaDasar = Siswa::factory()->create([
            'kelompok_id' => $kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);
        $siswaMenengah = Siswa::factory()->create([
            'kelompok_id' => $kelompokMenengah->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswaDasar->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 2,
            'progress_toward_target' => 40.0,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswaMenengah->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 8,
            'progress_toward_target' => 100.0,
        ]);

        // Act - filter by Dasar only
        $stats = $this->service->getAggregateStats(['tingkat' => 'Dasar']);

        // Assert
        $this->assertArrayHasKey('Dasar', $stats['avg_juz_per_tingkat']);
        $this->assertArrayNotHasKey('Menengah', $stats['avg_juz_per_tingkat']);
        $this->assertEquals(1, $stats['distribution_by_progress']['26-50%']); // Only Dasar student
        $this->assertEquals(0, $stats['distribution_by_progress']['76-100%']); // Menengah filtered out
    }

    /** @test */
    public function test_it_filters_aggregate_stats_by_kelompok()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok1 = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $kelompok2 = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompok1->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);
        $siswa2 = Siswa::factory()->create([
            'kelompok_id' => $kelompok2->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 3,
            'progress_toward_target' => 60.0,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 1,
            'progress_toward_target' => 20.0,
        ]);

        // Act - filter by kelompok1 only
        $stats = $this->service->getAggregateStats(['kelompok_id' => $kelompok1->id]);

        // Assert
        $this->assertEquals(3.0, $stats['avg_juz_per_tingkat']['Dasar']); // Only siswa1
        $this->assertEquals(1, $stats['distribution_by_progress']['51-75%']); // Only siswa1
        $this->assertEquals(0, $stats['distribution_by_progress']['0-25%']); // siswa2 filtered out
    }

    /** @test */
    public function test_it_filters_aggregate_stats_by_jenis_kelamin()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        $siswaLaki = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'jenis_kelamin' => 'Laki-laki',
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);
        $siswaPerempuan = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'jenis_kelamin' => 'Perempuan',
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswaLaki->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 5,
            'progress_toward_target' => 100.0,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswaPerempuan->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 2,
            'progress_toward_target' => 40.0,
        ]);

        // Act - filter by Laki-laki only
        $stats = $this->service->getAggregateStats(['jenis_kelamin' => 'Laki-laki']);

        // Assert
        $this->assertEquals(5.0, $stats['avg_juz_per_tingkat']['Dasar']); // Only siswaLaki
        $this->assertEquals(1, $stats['distribution_by_progress']['76-100%']); // Only siswaLaki
        $this->assertEquals(0, $stats['distribution_by_progress']['26-50%']); // siswaPerempuan filtered out
    }

    /** @test */
    public function test_it_ensures_all_progress_ranges_are_present_in_distribution()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        $siswa = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 1,
            'progress_toward_target' => 20.0,
        ]);

        // Act
        $stats = $this->service->getAggregateStats();

        // Assert - all 4 ranges should exist
        $this->assertArrayHasKey('0-25%', $stats['distribution_by_progress']);
        $this->assertArrayHasKey('26-50%', $stats['distribution_by_progress']);
        $this->assertArrayHasKey('51-75%', $stats['distribution_by_progress']);
        $this->assertArrayHasKey('76-100%', $stats['distribution_by_progress']);

        // Check counts
        $this->assertEquals(1, $stats['distribution_by_progress']['0-25%']);
        $this->assertEquals(0, $stats['distribution_by_progress']['26-50%']);
        $this->assertEquals(0, $stats['distribution_by_progress']['51-75%']);
        $this->assertEquals(0, $stats['distribution_by_progress']['76-100%']);
    }

    /** @test */
    public function test_it_calculates_completion_trend_for_last_6_months()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $siswa = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        $progresHafalan = ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 2,
            'progress_toward_target' => 40.0,
        ]);

        $juz1 = Juz::factory()->create(['juz_ke' => 1]);
        $juz2 = Juz::factory()->create(['juz_ke' => 2]);

        // Create CapaianJuz with tanggal_tuntas in different months
        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz1->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => now()->subMonths(2)->toDateString(),
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz2->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => now()->subMonth()->toDateString(),
        ]);

        // Act
        $stats = $this->service->getAggregateStats();

        // Assert
        $this->assertArrayHasKey('completion_trend', $stats);
        $this->assertCount(6, $stats['completion_trend']); // Should have 6 months

        // Check that months have correct format (YYYY-MM)
        foreach ($stats['completion_trend'] as $month => $count) {
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}$/', $month);
        }
    }

    /** @test */
    public function test_it_caches_aggregate_stats_for_5_minutes()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $siswa = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 3,
            'progress_toward_target' => 60.0,
        ]);

        // Act - first call (should hit database and cache)
        $stats1 = $this->service->getAggregateStats();

        // Update data
        ProgresHafalan::where('siswa_id', $siswa->id)->update(['total_juz_tuntas' => 5]);

        // Second call (should return cached data)
        $stats2 = $this->service->getAggregateStats();

        // Assert - should still show old data from cache
        $this->assertEquals($stats1['avg_juz_per_tingkat']['Dasar'], $stats2['avg_juz_per_tingkat']['Dasar']);
        $this->assertEquals(3.0, $stats2['avg_juz_per_tingkat']['Dasar']); // Old cached value

        // Clear cache and call again
        \Illuminate\Support\Facades\Cache::flush();
        $stats3 = $this->service->getAggregateStats();

        // Now should show updated data
        $this->assertEquals(5.0, $stats3['avg_juz_per_tingkat']['Dasar']); // New value
    }

    /** @test */
    public function test_it_only_includes_active_and_placed_students_in_aggregate_stats()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        // Active + Ditempatkan - should be included
        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Lulus SMP - should be excluded
        $siswa2 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => 'Lulus SMP',
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Terdaftar (not placed) - should be excluded
        $siswa3 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Terdaftar',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 2,
            'progress_toward_target' => 40.0,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 5,
            'progress_toward_target' => 100.0,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa3->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 3,
            'progress_toward_target' => 60.0,
        ]);

        // Act
        $stats = $this->service->getAggregateStats();

        // Assert - only siswa1 should be included
        $this->assertEquals(2.0, $stats['avg_juz_per_tingkat']['Dasar']);
        $this->assertEquals(1, $stats['distribution_by_progress']['26-50%']);
        $this->assertEquals(0, $stats['distribution_by_progress']['76-100%']); // siswa2 excluded
        $this->assertEquals(0, $stats['distribution_by_progress']['51-75%']); // siswa3 excluded
    }

    /**
     * Tests for getDashboardDataForAdmin method (Task 12.1)
     * Requirements: 6.1, 6.2
     */

    /** @test */
    public function test_admin_dashboard_returns_all_students_without_kelompok_restriction()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok1 = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $kelompok2 = \App\Models\Kelompok::factory()->create(['tingkat' => 'Menengah']);

        $siswa1 = Siswa::factory()->create(['kelompok_id' => $kelompok1->id]);
        $siswa2 = Siswa::factory()->create(['kelompok_id' => $kelompok2->id]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 2,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 3,
        ]);

        // Act
        $result = $this->service->getDashboardDataForAdmin();

        // Assert
        $this->assertEquals(2, $result->total());
        $this->assertCount(2, $result->items());
    }

    /** @test */
    public function test_admin_dashboard_filters_by_kelompok_id()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok1 = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $kelompok2 = \App\Models\Kelompok::factory()->create(['tingkat' => 'Menengah']);

        $siswa1 = Siswa::factory()->create(['kelompok_id' => $kelompok1->id]);
        $siswa2 = Siswa::factory()->create(['kelompok_id' => $kelompok2->id]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Act - filter by kelompok1
        $result = $this->service->getDashboardDataForAdmin(['kelompok_id' => $kelompok1->id]);

        // Assert
        $this->assertEquals(1, $result->total());
        $this->assertEquals($siswa1->id, $result->items()[0]->siswa_id);
    }

    /** @test */
    public function test_admin_dashboard_filters_by_tingkat()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompokDasar = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $kelompokMenengah = \App\Models\Kelompok::factory()->create(['tingkat' => 'Menengah']);

        $siswaDasar = Siswa::factory()->create(['kelompok_id' => $kelompokDasar->id]);
        $siswaMenengah = Siswa::factory()->create(['kelompok_id' => $kelompokMenengah->id]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswaDasar->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswaMenengah->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Act - filter by Dasar
        $result = $this->service->getDashboardDataForAdmin(['tingkat' => 'Dasar']);

        // Assert
        $this->assertEquals(1, $result->total());
        $this->assertEquals($siswaDasar->id, $result->items()[0]->siswa_id);
    }

    /** @test */
    public function test_admin_dashboard_filters_by_jenis_kelamin()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        $siswaLaki = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'jenis_kelamin' => 'Laki-laki',
        ]);
        $siswaPerempuan = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'jenis_kelamin' => 'Perempuan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswaLaki->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswaPerempuan->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Act - filter by Laki-laki
        $result = $this->service->getDashboardDataForAdmin(['jenis_kelamin' => 'Laki-laki']);

        // Assert
        $this->assertEquals(1, $result->total());
        $this->assertEquals($siswaLaki->id, $result->items()[0]->siswa_id);
    }

    /** @test */
    public function test_admin_dashboard_supports_multi_filter_combinations_with_and_logic()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompokDasarBanin = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $kelompokDasarBanat = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $kelompokMenengahBanin = \App\Models\Kelompok::factory()->create(['tingkat' => 'Menengah']);

        // Dasar + Laki-laki
        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompokDasarBanin->id,
            'jenis_kelamin' => 'Laki-laki',
        ]);

        // Dasar + Perempuan
        $siswa2 = Siswa::factory()->create([
            'kelompok_id' => $kelompokDasarBanat->id,
            'jenis_kelamin' => 'Perempuan',
        ]);

        // Menengah + Laki-laki
        $siswa3 = Siswa::factory()->create([
            'kelompok_id' => $kelompokMenengahBanin->id,
            'jenis_kelamin' => 'Laki-laki',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa3->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Act - filter by Dasar AND Laki-laki
        $result = $this->service->getDashboardDataForAdmin([
            'tingkat' => 'Dasar',
            'jenis_kelamin' => 'Laki-laki',
        ]);

        // Assert - should only return siswa1
        $this->assertEquals(1, $result->total());
        $this->assertEquals($siswa1->id, $result->items()[0]->siswa_id);
    }

    /** @test */
    public function test_admin_dashboard_returns_paginated_collection_with_max_50_per_page()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        // Create 60 students
        for ($i = 0; $i < 60; $i++) {
            $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
            ProgresHafalan::factory()->create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
            ]);
        }

        // Act - request 100 per page (should be capped at 50)
        $result = $this->service->getDashboardDataForAdmin(['per_page' => 100]);

        // Assert
        $this->assertEquals(60, $result->total());
        $this->assertEquals(50, $result->perPage()); // Capped at 50
        $this->assertCount(50, $result->items()); // First page has 50 items
    }

    /** @test */
    public function test_admin_dashboard_sorts_by_name()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        $siswaB = Siswa::factory()->create(['kelompok_id' => $kelompok->id, 'nama' => 'Budi']);
        $siswaA = Siswa::factory()->create(['kelompok_id' => $kelompok->id, 'nama' => 'Ahmad']);
        $siswaC = Siswa::factory()->create(['kelompok_id' => $kelompok->id, 'nama' => 'Citra']);

        ProgresHafalan::factory()->create(['siswa_id' => $siswaB->id, 'tahun_ajaran_id' => $tahunAjaran->id]);
        ProgresHafalan::factory()->create(['siswa_id' => $siswaA->id, 'tahun_ajaran_id' => $tahunAjaran->id]);
        ProgresHafalan::factory()->create(['siswa_id' => $siswaC->id, 'tahun_ajaran_id' => $tahunAjaran->id]);

        // Act - sort by name ascending
        $result = $this->service->getDashboardDataForAdmin(['sort_by' => 'name', 'sort_direction' => 'asc']);

        // Assert
        $items = $result->items();
        $this->assertEquals($siswaA->id, $items[0]->siswa_id);
        $this->assertEquals($siswaB->id, $items[1]->siswa_id);
        $this->assertEquals($siswaC->id, $items[2]->siswa_id);
    }

    /** @test */
    public function test_admin_dashboard_sorts_by_progress_percentage()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        $siswa1 = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $siswa2 = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $siswa3 = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'progress_toward_target' => 60.0,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'progress_toward_target' => 20.0,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa3->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'progress_toward_target' => 90.0,
        ]);

        // Act - sort by progress_percentage descending
        $result = $this->service->getDashboardDataForAdmin([
            'sort_by' => 'progress_percentage',
            'sort_direction' => 'desc',
        ]);

        // Assert
        $items = $result->items();
        $this->assertEquals(90.0, $items[0]->progress_toward_target);
        $this->assertEquals(60.0, $items[1]->progress_toward_target);
        $this->assertEquals(20.0, $items[2]->progress_toward_target);
    }

    /** @test */
    public function test_admin_dashboard_sorts_by_juz_count()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        $siswa1 = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $siswa2 = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $siswa3 = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 3,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 1,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa3->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 5,
        ]);

        // Act - sort by juz_count ascending
        $result = $this->service->getDashboardDataForAdmin([
            'sort_by' => 'juz_count',
            'sort_direction' => 'asc',
        ]);

        // Assert
        $items = $result->items();
        $this->assertEquals(1, $items[0]->total_juz_tuntas);
        $this->assertEquals(3, $items[1]->total_juz_tuntas);
        $this->assertEquals(5, $items[2]->total_juz_tuntas);
    }

    /** @test */
    public function test_admin_dashboard_filters_by_tahun_ajaran()
    {
        // Arrange
        $tahunAjaran2023 = TahunAjaran::factory()->create(['status' => false]);
        $tahunAjaran2024 = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran2023->id,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran2024->id,
        ]);

        // Act - filter by 2023
        $result = $this->service->getDashboardDataForAdmin(['tahun_ajaran_id' => $tahunAjaran2023->id]);

        // Assert
        $this->assertEquals(1, $result->total());
        $this->assertEquals($tahunAjaran2023->id, $result->items()[0]->tahun_ajaran_id);
    }

    /**
     * Tests for getDashboardDataForSiswa method (Task 13.1)
     * Requirements: 7.1, 7.2, 7.5
     */

    /** @test */
    public function test_siswa_dashboard_returns_complete_data_structure()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        // Create progres hafalan
        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 2,
            'juz_berjalan' => 3,
            'juz_berjalan_percentage' => 45.5,
            'progress_toward_target' => 40.0,
        ]);

        // Act
        $result = $this->service->getDashboardDataForSiswa($siswa->id);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('siswa', $result);
        $this->assertArrayHasKey('progres', $result);
        $this->assertArrayHasKey('capaian_juzs', $result);
        $this->assertArrayHasKey('recent_setoran', $result);

        // Check siswa
        $this->assertEquals($siswa->id, $result['siswa']->id);

        // Check progres structure
        $this->assertArrayHasKey('total_juz_tuntas', $result['progres']);
        $this->assertArrayHasKey('juz_berjalan', $result['progres']);
        $this->assertArrayHasKey('juz_berjalan_percentage', $result['progres']);
        $this->assertArrayHasKey('progress_toward_target', $result['progres']);
        $this->assertArrayHasKey('target_kelulusan', $result['progres']);

        // Check progres values
        $this->assertEquals(2, $result['progres']['total_juz_tuntas']);
        $this->assertEquals(3, $result['progres']['juz_berjalan']);
        $this->assertEquals(45.5, $result['progres']['juz_berjalan_percentage']);
        $this->assertEquals(40.0, $result['progres']['progress_toward_target']);
        $this->assertEquals(5, $result['progres']['target_kelulusan']);
    }

    /** @test */
    public function test_siswa_dashboard_shows_recent_setoran_history()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $juz = Juz::factory()->create(['juz_ke' => 1]);
        $surah = Surah::factory()->create(['nama_latin' => 'Al-Baqarah']);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Create 12 setoran entries (should only show last 10)
        for ($i = 0; $i < 12; $i++) {
            $absensi = \App\Models\Absensi::factory()->create([
                'siswa_id' => $siswa->id,
                'kelompok_id' => $kelompok->id,
                'tanggal' => now()->subDays(12 - $i),
                'status_kehadiran' => 'Hadir',
            ]);

            Setoran::factory()->create([
                'absensi_id' => $absensi->id,
                'juz_id' => $juz->id,
                'surah_id' => $surah->id,
                'ayat_mulai' => 1 + ($i * 5),
                'ayat_selesai' => 5 + ($i * 5),
                'jenis_setoran' => 'Hafalan Baru',
                'keputusan' => 'Lanjut',
            ]);
        }

        // Act
        $result = $this->service->getDashboardDataForSiswa($siswa->id);

        // Assert - should only show last 10
        $this->assertCount(10, $result['recent_setoran']);

        // Check recent setoran structure
        $firstSetoran = $result['recent_setoran']->first();
        $this->assertArrayHasKey('id', $firstSetoran);
        $this->assertArrayHasKey('tanggal', $firstSetoran);
        $this->assertArrayHasKey('jenis_setoran', $firstSetoran);
        $this->assertArrayHasKey('juz', $firstSetoran);
        $this->assertArrayHasKey('surah', $firstSetoran);
        $this->assertArrayHasKey('ayat_range', $firstSetoran);
        $this->assertArrayHasKey('predikat', $firstSetoran);
        $this->assertArrayHasKey('keputusan', $firstSetoran);

        // Check values
        $this->assertEquals(1, $firstSetoran['juz']);
        $this->assertEquals('Al-Baqarah', $firstSetoran['surah']);
        $this->assertStringContainsString('Ayat', $firstSetoran['ayat_range']);
    }

    /** @test */
    public function test_siswa_dashboard_eager_loads_capaian_juzs_with_juz_relationship()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        $juz1 = Juz::factory()->create(['juz_ke' => 1, 'jumlah_ayat' => 100]);
        $juz2 = Juz::factory()->create(['juz_ke' => 2, 'jumlah_ayat' => 100]);

        $progresHafalan = ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 1,
        ]);

        // Create capaian juz records
        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz1->id,
            'ayat_tercakup' => 100,
            'total_ayat' => 100,
            'persentase' => 100.0,
            'status_tuntas' => true,
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz2->id,
            'ayat_tercakup' => 50,
            'total_ayat' => 100,
            'persentase' => 50.0,
            'status_tuntas' => false,
        ]);

        // Act
        $result = $this->service->getDashboardDataForSiswa($siswa->id);

        // Assert
        $this->assertGreaterThanOrEqual(2, $result['capaian_juzs']->count());

        // Verify juz relationship is loaded
        $firstCapaian = $result['capaian_juzs']->firstWhere('juz_id', $juz1->id);
        $this->assertNotNull($firstCapaian);
        $this->assertTrue($firstCapaian->relationLoaded('juz'));
        $this->assertEquals(1, $firstCapaian->juz->juz_ke);
    }

    /** @test */
    public function test_siswa_dashboard_returns_empty_data_when_no_active_tahun_ajaran()
    {
        // Arrange
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        // No active tahun ajaran exists

        // Act
        $result = $this->service->getDashboardDataForSiswa($siswa->id);

        // Assert
        $this->assertEquals(0, $result['progres']['total_juz_tuntas']);
        $this->assertNull($result['progres']['juz_berjalan']);
        $this->assertNull($result['progres']['juz_berjalan_percentage']);
        $this->assertEquals(0, $result['progres']['progress_toward_target']);
        $this->assertEquals(5, $result['progres']['target_kelulusan']);
        $this->assertEmpty($result['capaian_juzs']);
        $this->assertEmpty($result['recent_setoran']);
    }

    /** @test */
    public function test_siswa_dashboard_only_queries_authenticated_siswa_data()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        $siswa1 = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $siswa2 = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 2,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 5,
        ]);

        // Act - request dashboard for siswa1
        $result = $this->service->getDashboardDataForSiswa($siswa1->id);

        // Assert - should only return siswa1's data
        $this->assertEquals($siswa1->id, $result['siswa']->id);
        $this->assertEquals(2, $result['progres']['total_juz_tuntas']);
    }

    /**
     * Tests for getProgressTimeline method (Task 14.1)
     * Requirements: 11.1, 11.4, 11.5
     */

    /** @test */
    public function test_it_returns_empty_collection_when_no_progres_hafalan_exists()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();

        // Act
        $timeline = $this->service->getProgressTimeline($siswa->id, $tahunAjaran->id);

        // Assert
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $timeline);
        $this->assertCount(0, $timeline);
    }

    /** @test */
    public function test_it_returns_completed_juz_with_tanggal_tuntas_ordered_asc()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $progresHafalan = ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 3,
        ]);

        $juz1 = Juz::factory()->create(['juz_ke' => 1]);
        $juz2 = Juz::factory()->create(['juz_ke' => 2]);
        $juz3 = Juz::factory()->create(['juz_ke' => 3]);

        // Create completions in non-chronological order
        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz2->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-02-15',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz1->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-01-10',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz3->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-03-20',
        ]);

        // Act
        $timeline = $this->service->getProgressTimeline($siswa->id, $tahunAjaran->id);

        // Assert
        $this->assertCount(3, $timeline);

        // Verify chronological order (ASC)
        $this->assertEquals('2024-01-10', $timeline[0]['tanggal_tuntas']);
        $this->assertEquals('2024-02-15', $timeline[1]['tanggal_tuntas']);
        $this->assertEquals('2024-03-20', $timeline[2]['tanggal_tuntas']);

        // Verify data structure
        $this->assertArrayHasKey('juz_id', $timeline[0]);
        $this->assertArrayHasKey('juz_ke', $timeline[0]);
        $this->assertArrayHasKey('tanggal_tuntas', $timeline[0]);

        $this->assertEquals($juz1->id, $timeline[0]['juz_id']);
        $this->assertEquals(1, $timeline[0]['juz_ke']);
    }

    /** @test */
    public function test_it_excludes_incomplete_juz_from_timeline()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $progresHafalan = ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        $juz1 = Juz::factory()->create(['juz_ke' => 1]);
        $juz2 = Juz::factory()->create(['juz_ke' => 2]);

        // Complete juz
        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz1->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-01-10',
        ]);

        // Incomplete juz (should be excluded)
        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz2->id,
            'status_tuntas' => false,
            'tanggal_tuntas' => null,
        ]);

        // Act
        $timeline = $this->service->getProgressTimeline($siswa->id, $tahunAjaran->id);

        // Assert
        $this->assertCount(1, $timeline);
        $this->assertEquals($juz1->id, $timeline[0]['juz_id']);
    }

    /** @test */
    public function test_it_filters_timeline_by_date_range_start()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $progresHafalan = ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        $juz1 = Juz::factory()->create(['juz_ke' => 1]);
        $juz2 = Juz::factory()->create(['juz_ke' => 2]);
        $juz3 = Juz::factory()->create(['juz_ke' => 3]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz1->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-01-10',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz2->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-02-15',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz3->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-03-20',
        ]);

        // Act - filter from Feb 1 onwards
        $timeline = $this->service->getProgressTimeline($siswa->id, $tahunAjaran->id, [
            'date_range' => ['start' => '2024-02-01']
        ]);

        // Assert - should only include juz2 and juz3
        $this->assertCount(2, $timeline);
        $this->assertEquals($juz2->id, $timeline[0]['juz_id']);
        $this->assertEquals($juz3->id, $timeline[1]['juz_id']);
    }

    /** @test */
    public function test_it_filters_timeline_by_date_range_end()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $progresHafalan = ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        $juz1 = Juz::factory()->create(['juz_ke' => 1]);
        $juz2 = Juz::factory()->create(['juz_ke' => 2]);
        $juz3 = Juz::factory()->create(['juz_ke' => 3]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz1->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-01-10',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz2->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-02-15',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz3->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-03-20',
        ]);

        // Act - filter up to Feb 28
        $timeline = $this->service->getProgressTimeline($siswa->id, $tahunAjaran->id, [
            'date_range' => ['end' => '2024-02-28']
        ]);

        // Assert - should only include juz1 and juz2
        $this->assertCount(2, $timeline);
        $this->assertEquals($juz1->id, $timeline[0]['juz_id']);
        $this->assertEquals($juz2->id, $timeline[1]['juz_id']);
    }

    /** @test */
    public function test_it_filters_timeline_by_date_range_start_and_end()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $progresHafalan = ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        $juz1 = Juz::factory()->create(['juz_ke' => 1]);
        $juz2 = Juz::factory()->create(['juz_ke' => 2]);
        $juz3 = Juz::factory()->create(['juz_ke' => 3]);
        $juz4 = Juz::factory()->create(['juz_ke' => 4]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz1->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-01-10',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz2->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-02-15',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz3->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-03-20',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz4->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-04-25',
        ]);

        // Act - filter Feb 1 to Mar 31
        $timeline = $this->service->getProgressTimeline($siswa->id, $tahunAjaran->id, [
            'date_range' => [
                'start' => '2024-02-01',
                'end' => '2024-03-31'
            ]
        ]);

        // Assert - should only include juz2 and juz3
        $this->assertCount(2, $timeline);
        $this->assertEquals($juz2->id, $timeline[0]['juz_id']);
        $this->assertEquals($juz3->id, $timeline[1]['juz_id']);
    }

    /** @test */
    public function test_it_filters_timeline_by_juz_range_min()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $progresHafalan = ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        $juz1 = Juz::factory()->create(['juz_ke' => 1]);
        $juz2 = Juz::factory()->create(['juz_ke' => 2]);
        $juz3 = Juz::factory()->create(['juz_ke' => 3]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz1->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-01-10',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz2->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-02-15',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz3->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-03-20',
        ]);

        // Act - filter juz 2 and above
        $timeline = $this->service->getProgressTimeline($siswa->id, $tahunAjaran->id, [
            'juz_range' => ['min' => 2]
        ]);

        // Assert - should only include juz2 and juz3
        $this->assertCount(2, $timeline);
        $this->assertEquals(2, $timeline[0]['juz_ke']);
        $this->assertEquals(3, $timeline[1]['juz_ke']);
    }

    /** @test */
    public function test_it_filters_timeline_by_juz_range_max()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $progresHafalan = ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        $juz1 = Juz::factory()->create(['juz_ke' => 1]);
        $juz2 = Juz::factory()->create(['juz_ke' => 2]);
        $juz3 = Juz::factory()->create(['juz_ke' => 3]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz1->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-01-10',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz2->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-02-15',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz3->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-03-20',
        ]);

        // Act - filter juz up to 2
        $timeline = $this->service->getProgressTimeline($siswa->id, $tahunAjaran->id, [
            'juz_range' => ['max' => 2]
        ]);

        // Assert - should only include juz1 and juz2
        $this->assertCount(2, $timeline);
        $this->assertEquals(1, $timeline[0]['juz_ke']);
        $this->assertEquals(2, $timeline[1]['juz_ke']);
    }

    /** @test */
    public function test_it_filters_timeline_by_juz_range_min_and_max()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $progresHafalan = ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        $juz1 = Juz::factory()->create(['juz_ke' => 1]);
        $juz2 = Juz::factory()->create(['juz_ke' => 2]);
        $juz3 = Juz::factory()->create(['juz_ke' => 3]);
        $juz4 = Juz::factory()->create(['juz_ke' => 4]);
        $juz5 = Juz::factory()->create(['juz_ke' => 5]);

        foreach ([$juz1, $juz2, $juz3, $juz4, $juz5] as $index => $juz) {
            \App\Models\CapaianJuz::factory()->create([
                'progres_hafalan_id' => $progresHafalan->id,
                'juz_id' => $juz->id,
                'status_tuntas' => true,
                'tanggal_tuntas' => now()->subDays(5 - $index)->toDateString(),
            ]);
        }

        // Act - filter juz 2 to 4
        $timeline = $this->service->getProgressTimeline($siswa->id, $tahunAjaran->id, [
            'juz_range' => ['min' => 2, 'max' => 4]
        ]);

        // Assert - should only include juz2, juz3, juz4
        $this->assertCount(3, $timeline);
        $this->assertEquals(2, $timeline[0]['juz_ke']);
        $this->assertEquals(3, $timeline[1]['juz_ke']);
        $this->assertEquals(4, $timeline[2]['juz_ke']);
    }

    /** @test */
    public function test_it_combines_date_range_and_juz_range_filters()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $progresHafalan = ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        $juz1 = Juz::factory()->create(['juz_ke' => 1]);
        $juz2 = Juz::factory()->create(['juz_ke' => 2]);
        $juz3 = Juz::factory()->create(['juz_ke' => 3]);
        $juz4 = Juz::factory()->create(['juz_ke' => 4]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz1->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-01-10',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz2->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-02-15',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz3->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-03-20',
        ]);

        \App\Models\CapaianJuz::factory()->create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => $juz4->id,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-04-25',
        ]);

        // Act - filter juz 2-3 AND dates Feb-Mar
        $timeline = $this->service->getProgressTimeline($siswa->id, $tahunAjaran->id, [
            'date_range' => [
                'start' => '2024-02-01',
                'end' => '2024-03-31'
            ],
            'juz_range' => ['min' => 2, 'max' => 3]
        ]);

        // Assert - should only include juz2 and juz3 (both filters applied)
        $this->assertCount(2, $timeline);
        $this->assertEquals(2, $timeline[0]['juz_ke']);
        $this->assertEquals('2024-02-15', $timeline[0]['tanggal_tuntas']);
        $this->assertEquals(3, $timeline[1]['juz_ke']);
        $this->assertEquals('2024-03-20', $timeline[1]['tanggal_tuntas']);
    }

    /**
     * Tests for getAverageAyatPerWeek method (Task 14.2)
     * Requirements: 11.2
     */

    /** @test */
    public function test_it_can_calculate_average_ayat_per_week_for_last_month()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $kelompok = \App\Models\Kelompok::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);

        $juz = Juz::create(['juz_ke' => 1, 'urutan_hafalan' => 1]);
        $surah = Surah::create(['nama_latin' => 'Al-Fatihah', 'nama_arab' => 'الفاتحة', 'jumlah_ayat' => 7, 'nomor_surah' => 1]);

        // Create 3 setoran in last month
        for ($i = 0; $i < 3; $i++) {
            $absensi = \App\Models\Absensi::factory()->create([
                'siswa_id' => $siswa->id,
                'kelompok_id' => $kelompok->id,
                'tanggal' => now()->subDays(10 + $i),
            ]);

            Setoran::factory()->create([
                'absensi_id' => $absensi->id,
                'juz_id' => $juz->id,
                'surah_id' => $surah->id,
                'ayat_mulai' => 1,
                'ayat_selesai' => 10, // 10 ayat each
                'jenis_setoran' => 'Hafalan Baru',
                'keputusan' => 'Lanjut',
            ]);
        }

        // Act
        $average = $this->service->getAverageAyatPerWeek($siswa->id, 'last_month');

        // Assert
        // 3 setoran × 10 ayat = 30 ayat total
        // Last month = 4 weeks
        // 30 / 4 = 7.5 ayat per week
        $this->assertEquals(7.5, $average);
    }

    /** @test */
    public function test_it_can_calculate_average_ayat_per_week_for_last_semester()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $kelompok = \App\Models\Kelompok::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);

        $juz = Juz::create(['juz_ke' => 1, 'urutan_hafalan' => 1]);
        $surah = Surah::create(['nama_latin' => 'Al-Fatihah', 'nama_arab' => 'الفاتحة', 'jumlah_ayat' => 7, 'nomor_surah' => 1]);

        // Create setoran spread over last semester (6 months)
        for ($i = 0; $i < 5; $i++) {
            $absensi = \App\Models\Absensi::factory()->create([
                'siswa_id' => $siswa->id,
                'kelompok_id' => $kelompok->id,
                'tanggal' => now()->subMonths($i)->subDays(5),
            ]);

            Setoran::factory()->create([
                'absensi_id' => $absensi->id,
                'juz_id' => $juz->id,
                'surah_id' => $surah->id,
                'ayat_mulai' => 1,
                'ayat_selesai' => 20, // 20 ayat each
                'jenis_setoran' => 'Hafalan Baru',
                'keputusan' => 'Lanjut',
            ]);
        }

        // Act
        $average = $this->service->getAverageAyatPerWeek($siswa->id, 'last_semester');

        // Assert
        // 5 setoran × 20 ayat = 100 ayat total
        // Last semester = 26 weeks
        // 100 / 26 = 3.85 ayat per week (rounded to 2 decimals)
        $this->assertEquals(3.85, $average);
    }

    /** @test */
    public function test_it_can_calculate_average_ayat_per_week_for_all_time()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $juz = Juz::factory()->create();
        $surah = Surah::factory()->create();

        // Create first setoran 10 weeks ago
        $firstAbsensi = \App\Models\Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tanggal' => now()->subWeeks(10),
        ]);

        Setoran::factory()->create([
            'absensi_id' => $firstAbsensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 15, // 15 ayat
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        // Create more setoran
        for ($i = 0; $i < 4; $i++) {
            $absensi = \App\Models\Absensi::factory()->create([
                'siswa_id' => $siswa->id,
                'tanggal' => now()->subWeeks(8 - $i * 2),
            ]);

            Setoran::factory()->create([
                'absensi_id' => $absensi->id,
                'juz_id' => $juz->id,
                'surah_id' => $surah->id,
                'ayat_mulai' => 1,
                'ayat_selesai' => 10, // 10 ayat each
                'jenis_setoran' => 'Hafalan Baru',
                'keputusan' => 'Lanjut',
            ]);
        }

        // Act
        $average = $this->service->getAverageAyatPerWeek($siswa->id, 'all_time');

        // Assert
        // Total ayat: 15 + (4 × 10) = 55 ayat
        // Period: 10 weeks (from first setoran to now)
        // 55 / 10 = 5.5 ayat per week
        $this->assertEquals(5.5, $average);
    }

    /** @test */
    public function test_it_only_counts_qualifying_setoran_hafalan_baru_lanjut()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $juz = Juz::factory()->create();
        $surah = Surah::factory()->create();

        // Qualifying setoran (Hafalan Baru + Lanjut)
        $absensi1 = \App\Models\Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tanggal' => now()->subDays(15),
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi1->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 10, // 10 ayat
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        // Non-qualifying: Ulang (should not count)
        $absensi2 = \App\Models\Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tanggal' => now()->subDays(10),
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi2->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 20, // 20 ayat (should not count)
            'jenis_setoran' => 'Ulang',
            'keputusan' => 'Lanjut',
        ]);

        // Non-qualifying: Hafalan Baru + Ulang (should not count)
        $absensi3 = \App\Models\Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tanggal' => now()->subDays(5),
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi3->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 15, // 15 ayat (should not count)
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Ulang',
        ]);

        // Act
        $average = $this->service->getAverageAyatPerWeek($siswa->id, 'last_month');

        // Assert
        // Only 10 ayat from first setoran should count
        // 10 / 4 weeks = 2.5 ayat per week
        $this->assertEquals(2.5, $average);
    }

    /** @test */
    public function test_it_returns_zero_when_no_qualifying_setoran_exists()
    {
        // Arrange
        $siswa = Siswa::factory()->create();

        // Act
        $average = $this->service->getAverageAyatPerWeek($siswa->id, 'all_time');

        // Assert
        $this->assertEquals(0.0, $average);
    }

    /** @test */
    public function test_it_throws_exception_for_invalid_period()
    {
        // Arrange
        $siswa = Siswa::factory()->create();

        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid period. Must be one of: last_month, last_semester, all_time');

        // Act
        $this->service->getAverageAyatPerWeek($siswa->id, 'invalid_period');
    }

    /** @test */
    public function test_it_filters_setoran_by_period_correctly()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $juz = Juz::factory()->create();
        $surah = Surah::factory()->create();

        // Old setoran (8 months ago - outside last_semester)
        $oldAbsensi = \App\Models\Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tanggal' => now()->subMonths(8),
        ]);

        Setoran::factory()->create([
            'absensi_id' => $oldAbsensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 50, // 50 ayat (should not count in last_semester)
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        // Recent setoran (2 months ago - within last_semester)
        $recentAbsensi = \App\Models\Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tanggal' => now()->subMonths(2),
        ]);

        Setoran::factory()->create([
            'absensi_id' => $recentAbsensi->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 1,
            'ayat_selesai' => 20, // 20 ayat
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        // Act
        $average = $this->service->getAverageAyatPerWeek($siswa->id, 'last_semester');

        // Assert
        // Only 20 ayat from recent setoran should count
        // 20 / 26 weeks ≈ 0.77 ayat per week
        $this->assertEquals(0.77, $average);
    }

    /** @test */
    public function test_it_correctly_counts_ayat_from_ranges()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $juz = Juz::factory()->create();
        $surah = Surah::factory()->create();

        $absensi1 = \App\Models\Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tanggal' => now()->subDays(20),
        ]);

        // Setoran with different range sizes
        Setoran::factory()->create([
            'absensi_id' => $absensi1->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 5,
            'ayat_selesai' => 15, // 11 ayat (15 - 5 + 1)
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        $absensi2 = \App\Models\Absensi::factory()->create([
            'siswa_id' => $siswa->id,
            'tanggal' => now()->subDays(15),
        ]);

        Setoran::factory()->create([
            'absensi_id' => $absensi2->id,
            'juz_id' => $juz->id,
            'surah_id' => $surah->id,
            'ayat_mulai' => 20,
            'ayat_selesai' => 25, // 6 ayat (25 - 20 + 1)
            'jenis_setoran' => 'Hafalan Baru',
            'keputusan' => 'Lanjut',
        ]);

        // Act
        $average = $this->service->getAverageAyatPerWeek($siswa->id, 'last_month');

        // Assert
        // Total: 11 + 6 = 17 ayat
        // 17 / 4 weeks = 4.25 ayat per week
        $this->assertEquals(4.25, $average);
    }
}
