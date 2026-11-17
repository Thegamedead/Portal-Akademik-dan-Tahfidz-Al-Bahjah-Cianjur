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
 * Test getDashboardDataForGuru method
 *
 * Requirements: 5.1, 5.2, 5.3, 5.4
 */
class GuruDashboardTest extends TestCase
{
    use RefreshDatabase;

    private ProgresHafalanService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProgresHafalanService();
    }

    public function test_it_returns_empty_paginator_when_guru_has_no_kelompok()
    {
        // Arrange: Guru tanpa kelompok binaan
        $guru = Guru::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);

        // Act
        $result = $this->service->getDashboardDataForGuru($guru->id);

        // Assert
        $this->assertCount(0, $result->items());
        $this->assertEquals(0, $result->total());
    }

    public function test_it_returns_only_siswa_in_kelompok_binaan()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $guru = Guru::factory()->create();
        $guruLain = Guru::factory()->create(); // Buat guru lain yang valid

        // Kelompok binaan guru
        $kelompok1 = Kelompok::factory()->create(['guru_pembimbing_id' => $guru->id]);
        $siswa1 = Siswa::factory()->create(['kelompok_id' => $kelompok1->id]);
        $progres1 = ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 5,
        ]);

        // Kelompok guru lain
        $kelompok2 = Kelompok::factory()->create(['guru_pembimbing_id' => $guruLain->id]);
        $siswa2 = Siswa::factory()->create(['kelompok_id' => $kelompok2->id]);
        $progres2 = ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 10,
        ]);

        // Act
        $result = $this->service->getDashboardDataForGuru($guru->id);

        // Assert
        $this->assertCount(1, $result->items());
        $this->assertEquals($progres1->id, $result->items()[0]->id);
        $this->assertEquals(5, $result->items()[0]->total_juz_tuntas);
    }

    public function test_it_filters_by_kelompok_id_when_specified()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $guru = Guru::factory()->create();

        // 2 kelompok binaan guru
        $kelompok1 = Kelompok::factory()->create(['guru_pembimbing_id' => $guru->id]);
        $kelompok2 = Kelompok::factory()->create(['guru_pembimbing_id' => $guru->id]);

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

        // Act: Filter by kelompok1
        $result = $this->service->getDashboardDataForGuru($guru->id, [
            'kelompok_id' => $kelompok1->id,
        ]);

        // Assert: Only siswa from kelompok1
        $this->assertCount(1, $result->items());
        $this->assertEquals($siswa1->id, $result->items()[0]->siswa_id);
    }

    public function test_it_sorts_by_name_ascending()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $guru = Guru::factory()->create();
        $kelompok = Kelompok::factory()->create(['guru_pembimbing_id' => $guru->id]);

        $siswaZ = Siswa::factory()->create(['kelompok_id' => $kelompok->id, 'nama' => 'Zaki']);
        $siswaA = Siswa::factory()->create(['kelompok_id' => $kelompok->id, 'nama' => 'Ahmad']);
        $siswaM = Siswa::factory()->create(['kelompok_id' => $kelompok->id, 'nama' => 'Muhammad']);

        ProgresHafalan::factory()->create(['siswa_id' => $siswaZ->id, 'tahun_ajaran_id' => $tahunAjaran->id]);
        ProgresHafalan::factory()->create(['siswa_id' => $siswaA->id, 'tahun_ajaran_id' => $tahunAjaran->id]);
        ProgresHafalan::factory()->create(['siswa_id' => $siswaM->id, 'tahun_ajaran_id' => $tahunAjaran->id]);

        // Act
        $result = $this->service->getDashboardDataForGuru($guru->id, [
            'sort_by' => 'name',
            'sort_direction' => 'asc',
        ]);

        // Assert
        $this->assertEquals('Ahmad', $result->items()[0]->siswa->nama);
        $this->assertEquals('Muhammad', $result->items()[1]->siswa->nama);
        $this->assertEquals('Zaki', $result->items()[2]->siswa->nama);
    }

    public function test_it_sorts_by_progress_percentage_descending()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $guru = Guru::factory()->create();
        $kelompok = Kelompok::factory()->create(['guru_pembimbing_id' => $guru->id]);

        $siswa1 = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $siswa2 = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $siswa3 = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'progress_toward_target' => 40,
        ]);
        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'progress_toward_target' => 80,
        ]);
        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa3->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'progress_toward_target' => 60,
        ]);

        // Act
        $result = $this->service->getDashboardDataForGuru($guru->id, [
            'sort_by' => 'progress_percentage',
            'sort_direction' => 'desc',
        ]);

        // Assert
        $this->assertEquals(80, $result->items()[0]->progress_toward_target);
        $this->assertEquals(60, $result->items()[1]->progress_toward_target);
        $this->assertEquals(40, $result->items()[2]->progress_toward_target);
    }

    public function test_it_sorts_by_juz_count_descending()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $guru = Guru::factory()->create();
        $kelompok = Kelompok::factory()->create(['guru_pembimbing_id' => $guru->id]);

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
            'total_juz_tuntas' => 8,
        ]);
        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa3->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 5,
        ]);

        // Act
        $result = $this->service->getDashboardDataForGuru($guru->id, [
            'sort_by' => 'juz_count',
            'sort_direction' => 'desc',
        ]);

        // Assert
        $this->assertEquals(8, $result->items()[0]->total_juz_tuntas);
        $this->assertEquals(5, $result->items()[1]->total_juz_tuntas);
        $this->assertEquals(3, $result->items()[2]->total_juz_tuntas);
    }

    public function test_it_paginates_results_with_custom_per_page()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $guru = Guru::factory()->create();
        $kelompok = Kelompok::factory()->create(['guru_pembimbing_id' => $guru->id]);

        // Create 25 siswa
        for ($i = 1; $i <= 25; $i++) {
            $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
            ProgresHafalan::factory()->create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
            ]);
        }

        // Act: Request 10 per page
        $result = $this->service->getDashboardDataForGuru($guru->id, ['per_page' => 10]);

        // Assert
        $this->assertCount(10, $result->items());
        $this->assertEquals(25, $result->total());
        $this->assertEquals(3, $result->lastPage());
    }
}
