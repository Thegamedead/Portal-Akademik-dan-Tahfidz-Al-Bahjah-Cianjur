<?php

namespace Tests\Unit\Services;

use App\Models\Siswa;
use App\Models\Kelompok;
use App\Models\RiwayatKelompok;
use App\Enums\StatusAkademik;
use App\Enums\StatusPenempatan;
use App\Services\Handlers\TahfidzGroupHandler;
use App\DataTransferObjects\TahfidzClearResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Unit Tests for TahfidzGroupHandler
 *
 * Tests Tahfidz group clearing logic for year-end rollover:
 * - Clearing kelompok_id for active students
 * - Updating status_penempatan to "Belum Ditempatkan"
 * - Creating RiwayatKelompok audit trail records
 */
class TahfidzGroupHandlerTest extends TestCase
{
    use RefreshDatabase;

    protected TahfidzGroupHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = app(TahfidzGroupHandler::class);
    }

    public function test_it_clears_kelompok_assignments_for_active_students()
    {
        // Arrange
        $kelompok = Kelompok::factory()->create();
        $adminId = 1;

        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
            'tanggal_penempatan' => Carbon::now()->subMonths(3),
        ]);

        $siswa2 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
            'tanggal_penempatan' => Carbon::now()->subMonths(2),
        ]);

        // Act
        $result = $this->handler->clearGroups($adminId);

        // Assert
        $this->assertInstanceOf(TahfidzClearResult::class, $result);
        $this->assertEquals(2, $result->clearedCount);

        $siswa1->refresh();
        $siswa2->refresh();

        $this->assertNull($siswa1->kelompok_id);
        $this->assertNull($siswa2->kelompok_id);
        $this->assertEquals(StatusPenempatan::TERDAFTAR->value, $siswa1->status_penempatan);
        $this->assertEquals(StatusPenempatan::TERDAFTAR->value, $siswa2->status_penempatan);
        $this->assertNull($siswa1->tanggal_penempatan);
        $this->assertNull($siswa2->tanggal_penempatan);
    }

    public function test_it_creates_riwayat_kelompok_records_for_cleared_students()
    {
        // Arrange
        $kelompok = Kelompok::factory()->create();
        $adminId = 1;

        $siswa = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
        ]);

        // Act
        $result = $this->handler->clearGroups($adminId);

        // Assert
        $this->assertEquals(1, $result->riwayatRecordsCreated);

        $riwayat = RiwayatKelompok::where('siswa_id', $siswa->id)->first();

        $this->assertNotNull($riwayat);
        $this->assertEquals($siswa->id, $riwayat->siswa_id);
        $this->assertEquals($kelompok->id, $riwayat->kelompok_dari_id);
        $this->assertNull($riwayat->kelompok_ke_id); // Cleared, not moved
        $this->assertEquals('Rollover Tahun Ajaran', $riwayat->alasan);
        $this->assertEquals($adminId, $riwayat->disetujui_oleh);
        $this->assertTrue(Carbon::parse($riwayat->tanggal)->isToday());
    }

    public function test_it_does_not_clear_graduated_students()
    {
        // Arrange
        $kelompok = Kelompok::factory()->create();
        $adminId = 1;

        $siswaActive = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
        ]);

        $siswaLulus = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => StatusAkademik::LULUS_SMP->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
        ]);

        // Act
        $result = $this->handler->clearGroups($adminId);

        // Assert
        $this->assertEquals(1, $result->clearedCount);

        $siswaActive->refresh();
        $siswaLulus->refresh();

        $this->assertNull($siswaActive->kelompok_id); // Cleared
        $this->assertEquals($kelompok->id, $siswaLulus->kelompok_id); // Preserved
        $this->assertEquals(StatusPenempatan::DITEMPATKAN->value, $siswaLulus->status_penempatan);
    }

    public function test_it_does_not_clear_students_with_no_kelompok()
    {
        // Arrange
        $adminId = 1;

        $siswa = Siswa::factory()->create([
            'kelompok_id' => null,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::TERDAFTAR->value,
        ]);

        // Act
        $result = $this->handler->clearGroups($adminId);

        // Assert
        $this->assertEquals(0, $result->clearedCount);
        $this->assertEquals(0, $result->riwayatRecordsCreated);

        $siswa->refresh();

        $this->assertNull($siswa->kelompok_id);
        $this->assertEquals(StatusPenempatan::TERDAFTAR->value, $siswa->status_penempatan);
    }

    public function test_it_only_clears_active_students()
    {
        // Arrange
        $kelompok = Kelompok::factory()->create();
        $adminId = 1;

        $siswaActive = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
        ]);

        $siswaPindah = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => StatusAkademik::PINDAH_SEKOLAH->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
        ]);

        $siswaKeluar = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => StatusAkademik::KELUAR->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
        ]);

        // Act
        $result = $this->handler->clearGroups($adminId);

        // Assert
        $this->assertEquals(1, $result->clearedCount);

        $siswaActive->refresh();
        $siswaPindah->refresh();
        $siswaKeluar->refresh();

        $this->assertNull($siswaActive->kelompok_id); // Cleared
        $this->assertEquals($kelompok->id, $siswaPindah->kelompok_id); // Preserved
        $this->assertEquals($kelompok->id, $siswaKeluar->kelompok_id); // Preserved
    }

    public function test_it_returns_cleared_student_ids()
    {
        // Arrange
        $kelompok = Kelompok::factory()->create();
        $adminId = 1;

        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
        ]);

        $siswa2 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
        ]);

        // Act
        $result = $this->handler->clearGroups($adminId);

        // Assert
        $this->assertContains($siswa1->id, $result->clearedStudentIds);
        $this->assertContains($siswa2->id, $result->clearedStudentIds);
        $this->assertCount(2, $result->clearedStudentIds);
    }

    public function test_it_returns_zero_counts_when_no_students_have_kelompok()
    {
        // Arrange
        $adminId = 1;

        Siswa::factory()->create([
            'kelompok_id' => null,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::TERDAFTAR->value,
        ]);

        // Act
        $result = $this->handler->clearGroups($adminId);

        // Assert
        $this->assertEquals(0, $result->clearedCount);
        $this->assertEquals(0, $result->riwayatRecordsCreated);
        $this->assertEmpty($result->clearedStudentIds);
        $this->assertFalse($result->hasAffectedStudents());
    }

    public function test_it_handles_multiple_kelompok_types()
    {
        // Arrange
        $kelompokDasar = Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $kelompokMenengah = Kelompok::factory()->create(['tingkat' => 'Menengah']);
        $kelompokAtas = Kelompok::factory()->create(['tingkat' => 'Atas']);
        $adminId = 1;

        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompokDasar->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
        ]);

        $siswa2 = Siswa::factory()->create([
            'kelompok_id' => $kelompokMenengah->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
        ]);

        $siswa3 = Siswa::factory()->create([
            'kelompok_id' => $kelompokAtas->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
        ]);

        // Act
        $result = $this->handler->clearGroups($adminId);

        // Assert
        $this->assertEquals(3, $result->clearedCount);
        $this->assertEquals(3, $result->riwayatRecordsCreated);

        $siswa1->refresh();
        $siswa2->refresh();
        $siswa3->refresh();

        $this->assertNull($siswa1->kelompok_id);
        $this->assertNull($siswa2->kelompok_id);
        $this->assertNull($siswa3->kelompok_id);
    }

    public function test_audit_trail_is_complete()
    {
        // Arrange
        $kelompok = Kelompok::factory()->create();
        $adminId = 1;

        Siswa::factory()->count(5)->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
        ]);

        // Act
        $result = $this->handler->clearGroups($adminId);

        // Assert
        $this->assertEquals(5, $result->clearedCount);
        $this->assertEquals(5, $result->riwayatRecordsCreated);
        $this->assertTrue($result->isAuditTrailComplete());

        $riwayatCount = RiwayatKelompok::where('alasan', 'Rollover Tahun Ajaran')->count();
        $this->assertEquals(5, $riwayatCount);
    }

    public function test_it_processes_large_datasets_efficiently()
    {
        // Arrange
        $kelompok = Kelompok::factory()->create();
        $adminId = 1;
        $studentCount = 100;

        Siswa::factory()->count($studentCount)->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
        ]);

        // Act
        $startTime = microtime(true);
        $result = $this->handler->clearGroups($adminId);
        $executionTime = microtime(true) - $startTime;

        // Assert
        $this->assertEquals($studentCount, $result->clearedCount);
        $this->assertEquals($studentCount, $result->riwayatRecordsCreated);
        $this->assertLessThan(5.0, $executionTime, 'Processing should complete within 5 seconds');

        // Verify all students were cleared
        $remainingAssignments = Siswa::where('status_akademik', StatusAkademik::AKTIF->value)
            ->whereNotNull('kelompok_id')
            ->count();

        $this->assertEquals(0, $remainingAssignments);
    }

    public function test_it_preserves_tanggal_penempatan_before_clearing()
    {
        // Arrange
        $kelompok = Kelompok::factory()->create();
        $adminId = 1;
        $originalDate = Carbon::now()->subMonths(6);

        $siswa = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
            'tanggal_penempatan' => $originalDate,
        ]);

        // Act
        $result = $this->handler->clearGroups($adminId);

        // Assert
        $siswa->refresh();

        // tanggal_penempatan should be cleared
        $this->assertNull($siswa->tanggal_penempatan);

        // But RiwayatKelompok should record the clearing event
        $riwayat = RiwayatKelompok::where('siswa_id', $siswa->id)->first();
        $this->assertNotNull($riwayat);
        $this->assertEquals($kelompok->id, $riwayat->kelompok_dari_id);
    }
}
