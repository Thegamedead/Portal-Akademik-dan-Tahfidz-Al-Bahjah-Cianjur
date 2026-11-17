<?php

namespace Tests\Unit\Services;

use App\Models\CapaianJuz;
use App\Models\ProgresHafalan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\ProgresHafalanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Integration test for getProgressTimeline method
 *
 * This test validates the implementation without requiring factories
 * by creating minimal data manually.
 */
class GetProgressTimelineIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected ProgresHafalanService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ProgresHafalanService::class);
    }

    /** @test */
    public function test_get_progress_timeline_returns_completed_juz_in_chronological_order()
    {
        // Arrange: Create minimal required data
        $siswa = Siswa::create([
            'nama' => 'Test Student',
            'nisn' => '1234567890',
            'nis' => '12345',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2010-01-01',
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        $tahunAjaran = TahunAjaran::create([
            'tahun_mulai' => 2024,
            'tahun_selesai' => 2025,
            'jenis_semester' => 'Ganjil',
            'nama' => '2024/2025 Ganjil',
            'status' => true,
        ]);

        $progresHafalan = ProgresHafalan::create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 3,
            'progress_toward_target' => 60.0,
        ]);

        // Create completed juz in non-chronological order
        // Note: We don't use juz factories, just link to juz_id directly
        CapaianJuz::create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => 2,
            'ayat_tercakup' => 100,
            'total_ayat' => 100,
            'persentase' => 100.00,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-02-15',
            'detail_ayat_tercakup' => [],
        ]);

        CapaianJuz::create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => 1,
            'ayat_tercakup' => 100,
            'total_ayat' => 100,
            'persentase' => 100.00,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-01-10',
            'detail_ayat_tercakup' => [],
        ]);

        CapaianJuz::create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => 3,
            'ayat_tercakup' => 100,
            'total_ayat' => 100,
            'persentase' => 100.00,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-03-20',
            'detail_ayat_tercakup' => [],
        ]);

        // Act: Get timeline
        $timeline = $this->service->getProgressTimeline($siswa->id, $tahunAjaran->id);

        // Assert: Should return in chronological order
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $timeline);
        $this->assertCount(3, $timeline);

        // Verify chronological ordering
        $this->assertEquals('2024-01-10', $timeline[0]['tanggal_tuntas']);
        $this->assertEquals('2024-02-15', $timeline[1]['tanggal_tuntas']);
        $this->assertEquals('2024-03-20', $timeline[2]['tanggal_tuntas']);

        // Verify data structure
        $this->assertArrayHasKey('juz_id', $timeline[0]);
        $this->assertArrayHasKey('tanggal_tuntas', $timeline[0]);
        $this->assertEquals(1, $timeline[0]['juz_id']);
        $this->assertEquals(2, $timeline[1]['juz_id']);
        $this->assertEquals(3, $timeline[2]['juz_id']);
    }

    /** @test */
    public function test_get_progress_timeline_filters_by_date_range()
    {
        // Arrange
        $siswa = Siswa::create([
            'nama' => 'Test Student',
            'nisn' => '1234567890',
            'nis' => '12345',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2010-01-01',
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        $tahunAjaran = TahunAjaran::create([
            'tahun_mulai' => 2024,
            'tahun_selesai' => 2025,
            'jenis_semester' => 'Ganjil',
            'nama' => '2024/2025 Ganjil',
            'status' => true,
        ]);

        $progresHafalan = ProgresHafalan::create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 3,
            'progress_toward_target' => 60.0,
        ]);

        CapaianJuz::create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => 1,
            'ayat_tercakup' => 100,
            'total_ayat' => 100,
            'persentase' => 100.00,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-01-10',
            'detail_ayat_tercakup' => [],
        ]);

        CapaianJuz::create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => 2,
            'ayat_tercakup' => 100,
            'total_ayat' => 100,
            'persentase' => 100.00,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-02-15',
            'detail_ayat_tercakup' => [],
        ]);

        CapaianJuz::create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => 3,
            'ayat_tercakup' => 100,
            'total_ayat' => 100,
            'persentase' => 100.00,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-03-20',
            'detail_ayat_tercakup' => [],
        ]);

        // Act: Filter from Feb 1 onwards
        $timeline = $this->service->getProgressTimeline($siswa->id, $tahunAjaran->id, [
            'date_range' => ['start' => '2024-02-01']
        ]);

        // Assert: Should only include Feb and Mar completions
        $this->assertCount(2, $timeline);
        $this->assertEquals(2, $timeline[0]['juz_id']);
        $this->assertEquals(3, $timeline[1]['juz_id']);
    }

    /** @test */
    public function test_get_progress_timeline_excludes_incomplete_juz()
    {
        // Arrange
        $siswa = Siswa::create([
            'nama' => 'Test Student',
            'nisn' => '1234567890',
            'nis' => '12345',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2010-01-01',
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        $tahunAjaran = TahunAjaran::create([
            'tahun_mulai' => 2024,
            'tahun_selesai' => 2025,
            'jenis_semester' => 'Ganjil',
            'nama' => '2024/2025 Ganjil',
            'status' => true,
        ]);

        $progresHafalan = ProgresHafalan::create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 1,
            'progress_toward_target' => 20.0,
        ]);

        // Complete juz
        CapaianJuz::create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => 1,
            'ayat_tercakup' => 100,
            'total_ayat' => 100,
            'persentase' => 100.00,
            'status_tuntas' => true,
            'tanggal_tuntas' => '2024-01-10',
            'detail_ayat_tercakup' => [],
        ]);

        // Incomplete juz (should be excluded from timeline)
        CapaianJuz::create([
            'progres_hafalan_id' => $progresHafalan->id,
            'juz_id' => 2,
            'ayat_tercakup' => 50,
            'total_ayat' => 100,
            'persentase' => 50.00,
            'status_tuntas' => false,
            'tanggal_tuntas' => null,
            'detail_ayat_tercakup' => [],
        ]);

        // Act
        $timeline = $this->service->getProgressTimeline($siswa->id, $tahunAjaran->id);

        // Assert: Should only include complete juz
        $this->assertCount(1, $timeline);
        $this->assertEquals(1, $timeline[0]['juz_id']);
    }
}
