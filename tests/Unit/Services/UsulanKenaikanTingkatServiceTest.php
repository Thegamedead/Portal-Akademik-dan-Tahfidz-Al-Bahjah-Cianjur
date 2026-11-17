<?php

namespace Tests\Unit\Services;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelompok;
use App\Models\TahunAjaran;
use App\Services\UsulanKenaikanTingkatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for UsulanKenaikanTingkatService::getAvailableGroups
 *
 * Tests the filtering logic for available groups based on:
 * - tingkat_tujuan matching
 * - jenis_kelamin to jenis mapping
 * - capacity exclusion (full groups)
 */
class UsulanKenaikanTingkatServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UsulanKenaikanTingkatService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(UsulanKenaikanTingkatService::class);
    }

    /**
     * Test getAvailableGroups filters by tingkat correctly
     *
     * @test
     */
    public function test_get_available_groups_filters_by_tingkat()
    {
        // Setup: Create groups with different tingkat
        $tahunAjaran = TahunAjaran::factory()->create();
        $guruPembimbing = Guru::factory()->create();

        $kelompokDasar = Kelompok::factory()->create([
            'nama' => 'Dasar 1',
            'tingkat' => 'Dasar',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        $kelompokMenengah = Kelompok::factory()->create([
            'nama' => 'Menengah 1',
            'tingkat' => 'Menengah',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        $kelompokAtas = Kelompok::factory()->create([
            'nama' => 'Atas 1',
            'tingkat' => 'Atas',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        // Execute: Get available groups for Menengah
        $availableGroups = $this->service->getAvailableGroups('Menengah', 'Laki-laki');

        // Assert: Only Menengah group is returned
        $this->assertCount(1, $availableGroups);
        $this->assertEquals('Menengah 1', $availableGroups->first()->nama);
        $this->assertEquals('Menengah', $availableGroups->first()->tingkat);
    }

    /**
     * Test getAvailableGroups filters by jenis_kelamin correctly
     *
     * @test
     */
    public function test_get_available_groups_filters_by_jenis_kelamin()
    {
        // Setup: Create groups with different jenis
        $tahunAjaran = TahunAjaran::factory()->create();
        $guruPembimbing = Guru::factory()->create();

        $kelompokBanin = Kelompok::factory()->create([
            'nama' => 'Menengah Banin',
            'tingkat' => 'Menengah',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        $kelompokBanat = Kelompok::factory()->create([
            'nama' => 'Menengah Banat',
            'tingkat' => 'Menengah',
            'jenis' => 'Banat',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        // Execute: Get available groups for male students
        $availableGroupsMale = $this->service->getAvailableGroups('Menengah', 'Laki-laki');

        // Assert: Only Banin group is returned for male students
        $this->assertCount(1, $availableGroupsMale);
        $this->assertEquals('Menengah Banin', $availableGroupsMale->first()->nama);
        $this->assertEquals('Banin', $availableGroupsMale->first()->jenis);

        // Execute: Get available groups for female students
        $availableGroupsFemale = $this->service->getAvailableGroups('Menengah', 'Perempuan');

        // Assert: Only Banat group is returned for female students
        $this->assertCount(1, $availableGroupsFemale);
        $this->assertEquals('Menengah Banat', $availableGroupsFemale->first()->nama);
        $this->assertEquals('Banat', $availableGroupsFemale->first()->jenis);
    }

    /**
     * Test getAvailableGroups excludes full groups
     *
     * @test
     */
    public function test_get_available_groups_excludes_full_groups()
    {
        // Setup: Create groups with varying capacity
        $tahunAjaran = TahunAjaran::factory()->create();
        $guruPembimbing = Guru::factory()->create();

        // Group with available capacity
        $kelompokAvailable = Kelompok::factory()->create([
            'nama' => 'Menengah Available',
            'tingkat' => 'Menengah',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        // Add 10 students (< 12)
        Siswa::factory()->count(10)->create([
            'kelompok_id' => $kelompokAvailable->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Group at full capacity
        $kelompokFull = Kelompok::factory()->create([
            'nama' => 'Menengah Full',
            'tingkat' => 'Menengah',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 10,
        ]);

        // Add 10 students (= 10)
        Siswa::factory()->count(10)->create([
            'kelompok_id' => $kelompokFull->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Group over capacity (edge case)
        $kelompokOverFull = Kelompok::factory()->create([
            'nama' => 'Menengah Over Full',
            'tingkat' => 'Menengah',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 8,
        ]);

        // Add 10 students (> 8)
        Siswa::factory()->count(10)->create([
            'kelompok_id' => $kelompokOverFull->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Execute: Get available groups
        $availableGroups = $this->service->getAvailableGroups('Menengah', 'Laki-laki');

        // Assert: Only the available group is returned (not full or overfull)
        $this->assertCount(1, $availableGroups);
        $this->assertEquals('Menengah Available', $availableGroups->first()->nama);

        // Verify the available group has correct count
        $this->assertEquals(10, $availableGroups->first()->siswas->count());
        $this->assertEquals(12, $availableGroups->first()->kapasitas_maks);
    }

    /**
     * Test getAvailableGroups returns collection with required data
     *
     * @test
     */
    public function test_get_available_groups_returns_collection_with_required_data()
    {
        // Setup: Create a group with guru pembimbing
        $tahunAjaran = TahunAjaran::factory()->create();
        $guruPembimbing = Guru::factory()->create([
            'nama' => 'Ustadz Ahmad',
        ]);

        $kelompok = Kelompok::factory()->create([
            'nama' => 'Menengah 1',
            'tingkat' => 'Menengah',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        // Add some students
        Siswa::factory()->count(5)->create([
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Execute: Get available groups
        $availableGroups = $this->service->getAvailableGroups('Menengah', 'Laki-laki');

        // Assert: Collection has required data
        $this->assertCount(1, $availableGroups);

        $group = $availableGroups->first();
        $this->assertNotNull($group->nama); // name
        $this->assertNotNull($group->guruPembimbing); // guru_pembimbing relationship
        $this->assertEquals('Ustadz Ahmad', $group->guruPembimbing->nama);
        $this->assertEquals(5, $group->siswas->count()); // jumlah_siswa (via relationship)
        $this->assertEquals(12, $group->kapasitas_maks); // kapasitas_maks
    }

    /**
     * Test getAvailableGroups returns empty collection when no groups match
     *
     * @test
     */
    public function test_get_available_groups_returns_empty_when_no_match()
    {
        // Setup: Create groups that don't match criteria
        $tahunAjaran = TahunAjaran::factory()->create();
        $guruPembimbing = Guru::factory()->create();

        Kelompok::factory()->create([
            'nama' => 'Dasar Banin',
            'tingkat' => 'Dasar', // Different tingkat
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        // Execute: Get available groups for Menengah
        $availableGroups = $this->service->getAvailableGroups('Menengah', 'Laki-laki');

        // Assert: Empty collection
        $this->assertCount(0, $availableGroups);
    }

    /**
     * Test placeStudent successfully places student with atomic transaction
     *
     * @test
     */
    public function test_place_student_successfully_updates_all_state()
    {
        // Setup: Create approved usulan with student and target group
        $tahunAjaran = TahunAjaran::factory()->create();
        $guruPembimbing = Guru::factory()->create();
        $guruPengusul = Guru::factory()->create();
        $admin = \App\Models\Admin::factory()->create();

        // Create source and destination groups
        $kelompokAsal = Kelompok::factory()->create([
            'nama' => 'Dasar 1',
            'tingkat' => 'Dasar',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        $kelompokTujuan = Kelompok::factory()->create([
            'nama' => 'Menengah 1',
            'tingkat' => 'Menengah',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        // Create student in source group
        $siswa = Siswa::factory()->create([
            'kelompok_id' => $kelompokAsal->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Create approved usulan
        $usulan = \App\Models\UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa->id,
            'guru_pengusul_id' => $guruPengusul->id,
            'tingkat_asal' => 'Dasar',
            'tingkat_tujuan' => 'Menengah',
            'kelompok_asal_id' => $kelompokAsal->id,
            'kelompok_tujuan_id' => null,
            'status_usulan' => \App\Enums\StatusUsulan::DISETUJUI,
            'alasan' => 'Test alasan',
            'tanggal_usul' => now(),
            'admin_verifikator_id' => $admin->id,
            'tanggal_keputusan' => now(),
        ]);

        // Execute: Place student
        $result = $this->service->placeStudent($usulan->id, $kelompokTujuan->id);

        // Assert: Usulan updated correctly
        $this->assertEquals(\App\Enums\StatusUsulan::DITEMPATKAN, $result->status_usulan);
        $this->assertEquals($kelompokTujuan->id, $result->kelompok_tujuan_id);
        $this->assertNotNull($result->tanggal_penempatan);

        // Assert: Siswa moved to new group
        $siswa->refresh();
        $this->assertEquals($kelompokTujuan->id, $siswa->kelompok_id);

        // Assert: Riwayat_Kelompok created
        $this->assertDatabaseHas('riwayat_kelompoks', [
            'siswa_id' => $siswa->id,
            'kelompok_dari_id' => $kelompokAsal->id,
            'kelompok_ke_id' => $kelompokTujuan->id,
            'alasan' => 'Kenaikan Tingkat',
            'disetujui_oleh' => $admin->id,
        ]);
    }

    /**
     * Test placeStudent throws exception when status is not "Disetujui"
     *
     * @test
     */
    public function test_place_student_throws_exception_for_invalid_status()
    {
        // Setup: Create usulan with status "Diajukan" (not approved yet)
        $tahunAjaran = TahunAjaran::factory()->create();
        $guruPembimbing = Guru::factory()->create();
        $guruPengusul = Guru::factory()->create();

        $kelompokAsal = Kelompok::factory()->create([
            'nama' => 'Dasar 1',
            'tingkat' => 'Dasar',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        $kelompokTujuan = Kelompok::factory()->create([
            'nama' => 'Menengah 1',
            'tingkat' => 'Menengah',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        $siswa = Siswa::factory()->create([
            'kelompok_id' => $kelompokAsal->id,
        ]);

        $usulan = \App\Models\UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa->id,
            'guru_pengusul_id' => $guruPengusul->id,
            'tingkat_asal' => 'Dasar',
            'tingkat_tujuan' => 'Menengah',
            'kelompok_asal_id' => $kelompokAsal->id,
            'status_usulan' => \App\Enums\StatusUsulan::DIAJUKAN, // Wrong status
            'alasan' => 'Test alasan',
            'tanggal_usul' => now(),
        ]);

        // Assert: Exception thrown
        $this->expectException(\App\Exceptions\InvalidStatusTransitionException::class);

        // Execute: Attempt to place student
        $this->service->placeStudent($usulan->id, $kelompokTujuan->id);
    }

    /**
     * Test placeStudent throws exception when target group is full
     *
     * @test
     */
    public function test_place_student_throws_exception_when_group_full()
    {
        // Setup: Create approved usulan with full target group
        $tahunAjaran = TahunAjaran::factory()->create();
        $guruPembimbing = Guru::factory()->create();
        $guruPengusul = Guru::factory()->create();
        $admin = \App\Models\Admin::factory()->create();

        $kelompokAsal = Kelompok::factory()->create([
            'nama' => 'Dasar 1',
            'tingkat' => 'Dasar',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        $kelompokTujuan = Kelompok::factory()->create([
            'nama' => 'Menengah 1',
            'tingkat' => 'Menengah',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 10, // Small capacity
        ]);

        // Fill target group to capacity
        Siswa::factory()->count(10)->create([
            'kelompok_id' => $kelompokTujuan->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Create student to be placed
        $siswa = Siswa::factory()->create([
            'kelompok_id' => $kelompokAsal->id,
        ]);

        // Create approved usulan
        $usulan = \App\Models\UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa->id,
            'guru_pengusul_id' => $guruPengusul->id,
            'tingkat_asal' => 'Dasar',
            'tingkat_tujuan' => 'Menengah',
            'kelompok_asal_id' => $kelompokAsal->id,
            'status_usulan' => \App\Enums\StatusUsulan::DISETUJUI,
            'alasan' => 'Test alasan',
            'tanggal_usul' => now(),
            'admin_verifikator_id' => $admin->id,
            'tanggal_keputusan' => now(),
        ]);

        // Assert: Exception thrown
        $this->expectException(\App\Exceptions\GroupCapacityExceededException::class);

        // Execute: Attempt to place student to full group
        $this->service->placeStudent($usulan->id, $kelompokTujuan->id);
    }

    /**
     * Test placeStudent rollback on transaction failure
     *
     * @test
     */
    public function test_place_student_rollback_on_failure()
    {
        // Setup: Create approved usulan
        $tahunAjaran = TahunAjaran::factory()->create();
        $guruPembimbing = Guru::factory()->create();
        $guruPengusul = Guru::factory()->create();
        $admin = \App\Models\Admin::factory()->create();

        $kelompokAsal = Kelompok::factory()->create([
            'nama' => 'Dasar 1',
            'tingkat' => 'Dasar',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        $siswa = Siswa::factory()->create([
            'kelompok_id' => $kelompokAsal->id,
        ]);

        $usulan = \App\Models\UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa->id,
            'guru_pengusul_id' => $guruPengusul->id,
            'tingkat_asal' => 'Dasar',
            'tingkat_tujuan' => 'Menengah',
            'kelompok_asal_id' => $kelompokAsal->id,
            'status_usulan' => \App\Enums\StatusUsulan::DISETUJUI,
            'alasan' => 'Test alasan',
            'tanggal_usul' => now(),
            'admin_verifikator_id' => $admin->id,
            'tanggal_keputusan' => now(),
        ]);

        // Assert: Exception thrown for non-existent group
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        // Execute: Attempt to place student to non-existent group
        $this->service->placeStudent($usulan->id, 99999);

        // Assert: Original data unchanged (transaction rollback)
        $usulan->refresh();
        $this->assertEquals(\App\Enums\StatusUsulan::DISETUJUI, $usulan->status_usulan);
        $this->assertNull($usulan->kelompok_tujuan_id);
        $this->assertNull($usulan->tanggal_penempatan);

        $siswa->refresh();
        $this->assertEquals($kelompokAsal->id, $siswa->kelompok_id);
    }

    /**
     * Test concurrent placements to same group with only one slot remaining
     *
     * This test simulates a race condition where two admins attempt to place
     * students to the same group simultaneously when only one slot is available.
     * One placement should succeed, the other should throw GroupCapacityExceededException.
     *
     * @test
     */
    public function test_concurrent_placements_to_same_group_one_slot_remaining()
    {
        // Setup: Create target group with only one slot remaining
        $tahunAjaran = TahunAjaran::factory()->create();
        $guruPembimbing = Guru::factory()->create();
        $guruPengusul = Guru::factory()->create();
        $admin = \App\Models\Admin::factory()->create();

        $kelompokAsal = Kelompok::factory()->create([
            'nama' => 'Dasar 1',
            'tingkat' => 'Dasar',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        $kelompokTujuan = Kelompok::factory()->create([
            'nama' => 'Menengah 1',
            'tingkat' => 'Menengah',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_maks' => 10, // Capacity = 10
        ]);

        // Fill group to capacity - 1 (9 students, 1 slot remaining)
        Siswa::factory()->count(9)->create([
            'kelompok_id' => $kelompokTujuan->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Create two students to be placed
        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompokAsal->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        $siswa2 = Siswa::factory()->create([
            'kelompok_id' => $kelompokAsal->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Create two approved usulan
        $usulan1 = \App\Models\UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa1->id,
            'guru_pengusul_id' => $guruPengusul->id,
            'tingkat_asal' => 'Dasar',
            'tingkat_tujuan' => 'Menengah',
            'kelompok_asal_id' => $kelompokAsal->id,
            'status_usulan' => \App\Enums\StatusUsulan::DISETUJUI,
            'alasan' => 'Test alasan 1',
            'tanggal_usul' => now(),
            'admin_verifikator_id' => $admin->id,
            'tanggal_keputusan' => now(),
        ]);

        $usulan2 = \App\Models\UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa2->id,
            'guru_pengusul_id' => $guruPengusul->id,
            'tingkat_asal' => 'Dasar',
            'tingkat_tujuan' => 'Menengah',
            'kelompok_asal_id' => $kelompokAsal->id,
            'status_usulan' => \App\Enums\StatusUsulan::DISETUJUI,
            'alasan' => 'Test alasan 2',
            'tanggal_usul' => now(),
            'admin_verifikator_id' => $admin->id,
            'tanggal_keputusan' => now(),
        ]);

        // Execute: Place first student (should succeed)
        $result1 = $this->service->placeStudent($usulan1->id, $kelompokTujuan->id);

        // Assert: First placement succeeded
        $this->assertEquals(\App\Enums\StatusUsulan::DITEMPATKAN, $result1->status_usulan);
        $this->assertEquals($kelompokTujuan->id, $result1->kelompok_tujuan_id);

        $siswa1->refresh();
        $this->assertEquals($kelompokTujuan->id, $siswa1->kelompok_id);

        // Verify group is now at capacity (10 students)
        $this->assertEquals(10, $kelompokTujuan->siswas()->count());

        // Execute: Attempt to place second student (should fail with exception)
        $exceptionThrown = false;
        try {
            $this->service->placeStudent($usulan2->id, $kelompokTujuan->id);
        } catch (\App\Exceptions\GroupCapacityExceededException $e) {
            $exceptionThrown = true;
            $this->assertStringContainsString('Menengah 1', $e->getMessage());
        }

        // Assert: Exception was thrown
        $this->assertTrue($exceptionThrown, 'Expected GroupCapacityExceededException to be thrown');

        // Verify second placement did not modify any state (transaction rollback)
        $usulan2->refresh();
        $this->assertEquals(\App\Enums\StatusUsulan::DISETUJUI, $usulan2->status_usulan);
        $this->assertNull($usulan2->kelompok_tujuan_id);
        $this->assertNull($usulan2->tanggal_penempatan);

        $siswa2->refresh();
        $this->assertEquals($kelompokAsal->id, $siswa2->kelompok_id);

        // Verify group still has exactly 10 students (not 11)
        $this->assertEquals(10, $kelompokTujuan->siswas()->count());
    }
}
