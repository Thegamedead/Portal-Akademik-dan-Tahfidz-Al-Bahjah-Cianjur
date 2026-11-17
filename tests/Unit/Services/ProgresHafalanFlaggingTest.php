<?php

namespace Tests\Unit\Services;

use App\Models\ProgresHafalan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\ProgresHafalanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit Tests for ProgresHafalanService Flagging Methods
 *
 * Task 12.3: Implement flagging methods untuk at-risk students
 * Requirements: 6.4, 6.5
 */
class ProgresHafalanFlaggingTest extends TestCase
{
    use RefreshDatabase;

    protected ProgresHafalanService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ProgresHafalanService::class);
    }

    /**
     * Tests for getStudentsApproachingTarget() method
     * Requirement 6.4: Identify students within 1 juz of target
     */

    /** @test */
    public function test_it_identifies_students_within_1_juz_of_target()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        // Student with 4 juz (target is 5, so 5-4 = 1 juz remaining)
        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 4,
            'progress_toward_target' => 80.0,
        ]);

        // Act
        $result = $this->service->getStudentsApproachingTarget();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($siswa1->id, $result->first()->siswa_id);
        $this->assertEquals(4, $result->first()->total_juz_tuntas);
        $this->assertEquals(1, $result->first()->juz_remaining);
    }

    /** @test */
    public function test_it_includes_students_exactly_at_target_minus_one()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        // Student exactly 1 juz away (5-4 = 1)
        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 4,
            'progress_toward_target' => 80.0,
        ]);

        // Act
        $result = $this->service->getStudentsApproachingTarget();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals(1, $result->first()->juz_remaining);
    }

    /** @test */
    public function test_it_excludes_students_already_at_or_above_target()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        // Student who reached target (5 juz)
        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 5,
            'progress_toward_target' => 100.0,
        ]);

        // Act
        $result = $this->service->getStudentsApproachingTarget();

        // Assert
        $this->assertCount(0, $result);
    }

    /** @test */
    public function test_it_excludes_students_more_than_1_juz_away()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        // Student with 3 juz (5-3 = 2 juz remaining, more than 1)
        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 3,
            'progress_toward_target' => 60.0,
        ]);

        // Act
        $result = $this->service->getStudentsApproachingTarget();

        // Assert
        $this->assertCount(0, $result);
    }

    /** @test */
    public function test_it_applies_kelompok_filter()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok1 = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $kelompok2 = \App\Models\Kelompok::factory()->create(['tingkat' => 'Menengah']);

        // Student in kelompok1 (4 juz)
        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompok1->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 4,
            'progress_toward_target' => 80.0,
        ]);

        // Student in kelompok2 (4 juz)
        $siswa2 = Siswa::factory()->create([
            'kelompok_id' => $kelompok2->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 4,
            'progress_toward_target' => 80.0,
        ]);

        // Act - filter by kelompok1
        $result = $this->service->getStudentsApproachingTarget(['kelompok_id' => $kelompok1->id]);

        // Assert - only siswa1 should be returned
        $this->assertCount(1, $result);
        $this->assertEquals($siswa1->id, $result->first()->siswa_id);
    }

    /**
     * Tests for getStudentsBelowProgress() method
     * Requirement 6.5: Identify students below expected progress threshold
     */

    /** @test */
    public function test_it_identifies_students_below_threshold()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        // Student with 40% progress (below 50% threshold)
        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 2,
            'progress_toward_target' => 40.0,
        ]);

        // Act
        $result = $this->service->getStudentsBelowProgress(50.0);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($siswa1->id, $result->first()->siswa_id);
        $this->assertEquals(40.0, $result->first()->progress_toward_target);
    }

    /** @test */
    public function test_it_excludes_students_at_or_above_threshold()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        // Student with exactly 50% progress (threshold is 50%)
        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 2.5,
            'progress_toward_target' => 50.0,
        ]);

        // Act
        $result = $this->service->getStudentsBelowProgress(50.0);

        // Assert
        $this->assertCount(0, $result);
    }

    /** @test */
    public function test_it_validates_threshold_range()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Threshold must be between 0 and 100');

        // Act - threshold above 100
        $this->service->getStudentsBelowProgress(150.0);
    }

    /** @test */
    public function test_it_validates_negative_threshold()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Threshold must be between 0 and 100');

        // Act - negative threshold
        $this->service->getStudentsBelowProgress(-10.0);
    }

    /** @test */
    public function test_it_applies_tingkat_filter_for_below_progress()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompokDasar = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $kelompokMenengah = \App\Models\Kelompok::factory()->create(['tingkat' => 'Menengah']);

        // Student in Dasar with low progress
        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 1,
            'progress_toward_target' => 20.0,
        ]);

        // Student in Menengah with low progress
        $siswa2 = Siswa::factory()->create([
            'kelompok_id' => $kelompokMenengah->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 1,
            'progress_toward_target' => 20.0,
        ]);

        // Act - filter by Dasar tingkat
        $result = $this->service->getStudentsBelowProgress(50.0, ['tingkat' => 'Dasar']);

        // Assert - only siswa1 should be returned
        $this->assertCount(1, $result);
        $this->assertEquals($siswa1->id, $result->first()->siswa_id);
    }

    /** @test */
    public function test_it_sorts_by_progress_ascending()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = \App\Models\Kelompok::factory()->create(['tingkat' => 'Dasar']);

        // Student with 10% progress
        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
            'nama' => 'Ahmad',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 0,
            'progress_toward_target' => 10.0,
        ]);

        // Student with 30% progress
        $siswa2 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
            'nama' => 'Budi',
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 1,
            'progress_toward_target' => 30.0,
        ]);

        // Act
        $result = $this->service->getStudentsBelowProgress(50.0);

        // Assert - should be sorted by progress_toward_target ASC
        $this->assertCount(2, $result);
        $this->assertEquals($siswa1->id, $result->first()->siswa_id);
        $this->assertEquals($siswa2->id, $result->last()->siswa_id);
    }
}
