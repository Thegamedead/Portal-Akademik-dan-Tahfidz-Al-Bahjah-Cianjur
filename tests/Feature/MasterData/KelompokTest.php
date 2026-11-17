<?php

namespace Tests\Feature\MasterData;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Kelompok;
use App\Models\RiwayatGuruPembimbingKelompok;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\MasterData\KelompokService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Integration Tests for Kelompok Management
 *
 * **Requirements**: 5.2, 5.3, 5.4, 5.5, 5.9, 5.10, 5.11, 5.13
 *
 * This test suite verifies the following properties:
 * - Property 8: Kelompok nama is unique within the same TahunAjaran
 * - Property 9: guru_pembimbing_id always references a Guru with jabatan = 'Guru Pembimbing Tahfidz'
 * - Default capacity values from config
 * - Property 10: kapasitas_min <= kapasitas_maks for every Kelompok
 * - Guru pembimbing history recording on change
 * - Property 11: jumlah_siswa equals count of Siswa where status_penempatan = 'Ditempatkan' and kelompok_id matches
 * - Property 15: Deletion prevention when students or transactions exist
 *
 * Run with: php artisan test --filter KelompokTest
 */
class KelompokTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected KelompokService $kelompokService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin and authenticate
        $this->admin = Admin::factory()->create();
        $this->actingAs($this->admin, 'admin');

        // Initialize service
        $this->kelompokService = app(KelompokService::class);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Create a TahunAjaran for testing
     */
    private function makeTahunAjaran(array $overrides = []): TahunAjaran
    {
        return TahunAjaran::factory()->create(array_merge([
            'status' => true,
        ], $overrides));
    }

    /**
     * Create a Guru Pembimbing Tahfidz for testing
     */
    private function makePembimbingTahfidz(array $overrides = []): Guru
    {
        return Guru::factory()->create(array_merge([
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'status_aktif' => true,
        ], $overrides));
    }

    /**
     * Create a non-Pembimbing Guru for testing
     */
    private function makeNonPembimbingGuru(array $overrides = []): Guru
    {
        return Guru::factory()->create(array_merge([
            'jabatan' => 'Guru Mapel',
            'status_aktif' => true,
        ], $overrides));
    }

    /**
     * Create valid kelompok data for creation
     */
    private function makeKelompokData(Guru $guru, TahunAjaran $tahunAjaran, array $overrides = []): array
    {
        return array_merge([
            'nama' => 'Kelompok ' . fake()->word(),
            'jenis' => fake()->randomElement(['Banin', 'Banat']),
            'tingkat' => fake()->randomElement(['Dasar', 'Menengah', 'Atas']),
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_min' => 6,
            'kapasitas_maks' => 12,
            'jadwal_hari' => 'Senin',
            'jadwal_jam' => '08:00-10:00',
        ], $overrides);
    }

    /**
     * Create a Kelompok with default or custom attributes
     */
    private function makeKelompok(array $overrides = []): Kelompok
    {
        $guru = $this->makePembimbingTahfidz();
        $tahunAjaran = $this->makeTahunAjaran();

        return Kelompok::factory()->create(array_merge([
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ], $overrides));
    }

    // =========================================================================
    // PROPERTY 8: Kelompok nama is unique within the same TahunAjaran
    // Requirements: 5.2
    // =========================================================================

    /**
     * Test: Kelompok name must be unique within the same tahun ajaran
     * Validates: Requirements 5.2
     */
    public function test_property_8_nama_unique_within_tahun_ajaran(): void
    {
        // Arrange: Create kelompok in tahun ajaran 1
        $tahunAjaran1 = $this->makeTahunAjaran(['label' => '2024/2025']);
        $guru1 = $this->makePembimbingTahfidz();

        $kelompok1Data = $this->makeKelompokData($guru1, $tahunAjaran1, [
            'nama' => 'Kelompok Alpha',
        ]);

        $kelompok1 = $this->kelompokService->create($kelompok1Data);

        // Try to create another kelompok with same name in same tahun ajaran
        $guru2 = $this->makePembimbingTahfidz();
        $kelompok2Data = $this->makeKelompokData($guru2, $tahunAjaran1, [
            'nama' => 'Kelompok Alpha', // Same name, same tahun ajaran
        ]);

        // Act & Assert
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Nama kelompok sudah ada untuk tahun ajaran ini');

        $this->kelompokService->create($kelompok2Data);
    }

    /**
     * Test: Same kelompok name allowed in different tahun ajaran
     * Validates: Requirements 5.2
     */
    public function test_property_8_same_nama_allowed_different_tahun_ajaran(): void
    {
        // Arrange: Create kelompok in tahun ajaran 1
        $tahunAjaran1 = $this->makeTahunAjaran(['label' => '2024/2025']);
        $tahunAjaran2 = $this->makeTahunAjaran(['label' => '2025/2026', 'status' => false]);

        $guru1 = $this->makePembimbingTahfidz();
        $guru2 = $this->makePembimbingTahfidz();

        $kelompok1Data = $this->makeKelompokData($guru1, $tahunAjaran1, [
            'nama' => 'Kelompok Alpha',
        ]);

        $kelompok2Data = $this->makeKelompokData($guru2, $tahunAjaran2, [
            'nama' => 'Kelompok Alpha', // Same name, different tahun ajaran
        ]);

        // Act
        $kelompok1 = $this->kelompokService->create($kelompok1Data);
        $kelompok2 = $this->kelompokService->create($kelompok2Data);

        // Assert: Both created successfully
        $this->assertEquals('Kelompok Alpha', $kelompok1->nama);
        $this->assertEquals('Kelompok Alpha', $kelompok2->nama);
        $this->assertNotEquals($kelompok1->tahun_ajaran_id, $kelompok2->tahun_ajaran_id);
    }

    /**
     * Test: Name uniqueness validation on update
     * Validates: Requirements 5.8
     */
    public function test_property_8_nama_uniqueness_on_update(): void
    {
        // Arrange: Create two kelompok in same tahun ajaran
        $tahunAjaran = $this->makeTahunAjaran();
        $guru1 = $this->makePembimbingTahfidz();
        $guru2 = $this->makePembimbingTahfidz();

        $kelompok1 = $this->makeKelompok([
            'nama' => 'Kelompok Alpha',
            'tahun_ajaran_id' => $tahunAjaran->id,
            'guru_pembimbing_id' => $guru1->id,
        ]);

        $kelompok2 = $this->makeKelompok([
            'nama' => 'Kelompok Beta',
            'tahun_ajaran_id' => $tahunAjaran->id,
            'guru_pembimbing_id' => $guru2->id,
        ]);

        // Act & Assert: Try to update kelompok2 with kelompok1's name
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Nama kelompok sudah ada untuk tahun ajaran ini');

        $this->kelompokService->update($kelompok2->id, ['nama' => 'Kelompok Alpha']);
    }

    // =========================================================================
    // PROPERTY 9: guru_pembimbing_id must reference Guru Pembimbing Tahfidz
    // Requirements: 5.3
    // =========================================================================

    /**
     * Test: Guru pembimbing must have jabatan 'Guru Pembimbing Tahfidz'
     * Validates: Requirements 5.3
     */
    public function test_property_9_guru_pembimbing_must_be_pembimbing_tahfidz(): void
    {
        // Arrange
        $tahunAjaran = $this->makeTahunAjaran();
        $guruMapel = $this->makeNonPembimbingGuru(['jabatan' => 'Guru Mapel']);

        $kelompokData = $this->makeKelompokData($guruMapel, $tahunAjaran);

        // Act & Assert
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Guru yang dipilih harus memiliki jabatan Guru Pembimbing Tahfidz');

        $this->kelompokService->create($kelompokData);
    }

    /**
     * Test: Valid Guru Pembimbing Tahfidz is accepted
     * Validates: Requirements 5.3
     */
    public function test_property_9_valid_pembimbing_tahfidz_accepted(): void
    {
        // Arrange
        $tahunAjaran = $this->makeTahunAjaran();
        $pembimbingTahfidz = $this->makePembimbingTahfidz();

        $kelompokData = $this->makeKelompokData($pembimbingTahfidz, $tahunAjaran, [
            'nama' => 'Kelompok Test Valid',
        ]);

        // Act
        $kelompok = $this->kelompokService->create($kelompokData);

        // Assert
        $this->assertEquals($pembimbingTahfidz->id, $kelompok->guru_pembimbing_id);
        $this->assertEquals('Guru Pembimbing Tahfidz', $kelompok->guruPembimbing->jabatan);
    }

    /**
     * Test: Inactive Guru Pembimbing Tahfidz is rejected
     * Validates: Requirements 5.3
     */
    public function test_property_9_inactive_pembimbing_tahfidz_rejected(): void
    {
        // Arrange
        $tahunAjaran = $this->makeTahunAjaran();
        $inactivePembimbing = $this->makePembimbingTahfidz(['status_aktif' => false]);

        $kelompokData = $this->makeKelompokData($inactivePembimbing, $tahunAjaran);

        // Act & Assert
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Guru yang dipilih tidak ditemukan atau sudah tidak aktif');

        $this->kelompokService->create($kelompokData);
    }

    // =========================================================================
    // DEFAULT CAPACITY VALUES AND PROPERTY 10: kapasitas_min <= kapasitas_maks
    // Requirements: 5.4, 5.5
    // =========================================================================

    /**
     * Test: Default capacity values are set from config
     * Validates: Requirements 5.4
     */
    public function test_default_capacity_values_from_config(): void
    {
        // Arrange
        $tahunAjaran = $this->makeTahunAjaran();
        $guru = $this->makePembimbingTahfidz();

        $kelompokData = $this->makeKelompokData($guru, $tahunAjaran, [
            'nama' => 'Kelompok Default Capacity',
            // Don't set kapasitas_min and kapasitas_maks to test defaults
        ]);
        unset($kelompokData['kapasitas_min']);
        unset($kelompokData['kapasitas_maks']);

        // Act
        $kelompok = $this->kelompokService->create($kelompokData);

        // Assert: Default values should be set (6 min, 12 max)
        $this->assertEquals(6, $kelompok->kapasitas_min);
        $this->assertEquals(12, $kelompok->kapasitas_maks);
    }

    /**
     * Test: Property 10 - kapasitas_min must be <= kapasitas_maks
     * Validates: Requirements 5.5
     */
    public function test_property_10_kapasitas_min_less_than_or_equal_maks(): void
    {
        // Arrange
        $tahunAjaran = $this->makeTahunAjaran();
        $guru = $this->makePembimbingTahfidz();

        $kelompokData = $this->makeKelompokData($guru, $tahunAjaran, [
            'nama' => 'Kelompok Invalid Capacity',
            'kapasitas_min' => 15, // Greater than maks
            'kapasitas_maks' => 10,
        ]);

        // Act & Assert
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Kapasitas minimum tidak boleh lebih besar dari kapasitas maksimum');

        $this->kelompokService->create($kelompokData);
    }

    /**
     * Test: Valid capacity values (min = maks) are accepted
     * Validates: Requirements 5.5
     */
    public function test_property_10_valid_capacity_min_equals_maks(): void
    {
        // Arrange
        $tahunAjaran = $this->makeTahunAjaran();
        $guru = $this->makePembimbingTahfidz();

        $kelompokData = $this->makeKelompokData($guru, $tahunAjaran, [
            'nama' => 'Kelompok Equal Capacity',
            'kapasitas_min' => 10,
            'kapasitas_maks' => 10, // Equal is allowed
        ]);

        // Act
        $kelompok = $this->kelompokService->create($kelompokData);

        // Assert
        $this->assertEquals(10, $kelompok->kapasitas_min);
        $this->assertEquals(10, $kelompok->kapasitas_maks);
    }

    // =========================================================================
    // GURU PEMBIMBING HISTORY RECORDING ON CHANGE
    // Requirements: 5.9
    // =========================================================================

    /**
     * Test: Guru pembimbing history is recorded on change
     * Validates: Requirements 5.9
     */
    public function test_guru_pembimbing_history_recording_on_change(): void
    {
        // Arrange: Create kelompok with initial guru
        $tahunAjaran = $this->makeTahunAjaran();
        $oldGuru = $this->makePembimbingTahfidz(['nama' => 'Old Guru']);
        $newGuru = $this->makePembimbingTahfidz(['nama' => 'New Guru']);

        $kelompok = $this->makeKelompok([
            'nama' => 'Kelompok History Test',
            'guru_pembimbing_id' => $oldGuru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Act: Update guru pembimbing with custom alasan
        $updated = $this->kelompokService->update($kelompok->id, [
            'guru_pembimbing_id' => $newGuru->id,
        ], 'Rotasi tahunan');

        // Assert: History record is created
        $this->assertEquals($newGuru->id, $updated->guru_pembimbing_id);

        $history = RiwayatGuruPembimbingKelompok::where('kelompok_id', $kelompok->id)->first();
        $this->assertNotNull($history);
        $this->assertEquals($kelompok->id, $history->kelompok_id);
        $this->assertEquals($oldGuru->id, $history->guru_dari_id);
        $this->assertEquals($newGuru->id, $history->guru_ke_id);
        $this->assertEquals('Rotasi tahunan', $history->alasan);
        $this->assertNotNull($history->tanggal_efektif);
    }

    /**
     * Test: No history recorded when guru pembimbing unchanged
     * Validates: Requirements 5.9
     */
    public function test_no_history_when_guru_pembimbing_unchanged(): void
    {
        // Arrange: Create kelompok
        $tahunAjaran = $this->makeTahunAjaran();
        $guru = $this->makePembimbingTahfidz();

        $kelompok = $this->makeKelompok([
            'nama' => 'Kelompok No History',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Act: Update other fields, keep same guru
        $this->kelompokService->update($kelompok->id, [
            'jadwal_hari' => 'Selasa', // Change schedule only
        ]);

        // Assert: No history record created
        $historyCount = RiwayatGuruPembimbingKelompok::where('kelompok_id', $kelompok->id)->count();
        $this->assertEquals(0, $historyCount);
    }

    // =========================================================================
    // PROPERTY 11: jumlah_siswa calculation
    // Requirements: 5.13
    // =========================================================================

    /**
     * Test: Member count calculation counts only 'Ditempatkan' students
     * Validates: Requirements 5.13
     */
    public function test_property_11_member_count_calculation(): void
    {
        // Arrange: Create kelompok
        $kelompok = $this->makeKelompok(['nama' => 'Kelompok Count Test']);

        // Create students with different status_penempatan
        $siswaDitempatkan1 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        $siswaDitempatkan2 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        $siswaTerdaftar = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Terdaftar', // Should not be counted
        ]);

        // Create student in different kelompok (should not be counted)
        $otherKelompok = $this->makeKelompok(['nama' => 'Other Kelompok']);
        $siswaOther = Siswa::factory()->create([
            'kelompok_id' => $otherKelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Act: Get member count using service
        $memberCount = $this->kelompokService->getMemberCount($kelompok->id);

        // Assert: Only 'Ditempatkan' students in this kelompok are counted
        $this->assertEquals(2, $memberCount);

        // Verify accessor also works
        $kelompok->refresh();
        $this->assertEquals(2, $kelompok->jumlah_siswa);
    }

    /**
     * Test: Member count is zero when no students placed
     * Validates: Requirements 5.13
     */
    public function test_property_11_member_count_zero_when_no_students(): void
    {
        // Arrange: Create kelompok without any students
        $kelompok = $this->makeKelompok(['nama' => 'Empty Kelompok']);

        // Act
        $memberCount = $this->kelompokService->getMemberCount($kelompok->id);

        // Assert
        $this->assertEquals(0, $memberCount);
        $this->assertEquals(0, $kelompok->jumlah_siswa);
    }

    // =========================================================================
    // PROPERTY 15: Deletion prevention when students or transactions exist
    // Requirements: 5.10, 5.11
    // =========================================================================

    /**
     * Test: Kelompok deletion allowed when no dependencies exist
     * Validates: Requirements 5.10
     */
    public function test_property_15_deletion_allowed_without_dependencies(): void
    {
        // Arrange: Create kelompok without students
        $kelompok = $this->makeKelompok(['nama' => 'Deletable Kelompok']);
        $kelompokId = $kelompok->id;

        // Act
        $result = $this->kelompokService->delete($kelompokId);

        // Assert: Kelompok is deleted
        $this->assertTrue($result);
        $this->assertDatabaseMissing('kelompoks', ['id' => $kelompokId]);
    }

    /**
     * Test: Kelompok deletion blocked when students exist
     * Validates: Requirements 5.11
     */
    public function test_property_15_deletion_blocked_when_students_exist(): void
    {
        // Arrange: Create kelompok with students
        $kelompok = $this->makeKelompok(['nama' => 'Kelompok With Students']);

        // Add students to kelompok
        Siswa::factory()->count(2)->create([
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Tidak dapat menghapus Kelompok ini.*siswa/');

        $this->kelompokService->delete($kelompok->id);
    }

    /**
     * Test: Kelompok with only 'Terdaftar' students can be deleted
     * Validates: Requirements 5.10
     */
    public function test_property_15_kelompok_with_terdaftar_students_can_be_deleted(): void
    {
        // Arrange: Create kelompok with 'Terdaftar' students (not placed)
        $kelompok = $this->makeKelompok(['nama' => 'Kelompok Terdaftar Only']);

        Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Terdaftar', // Not fully placed
        ]);

        $kelompokId = $kelompok->id;

        // Act: Should be deletable since no 'Ditempatkan' students
        $result = $this->kelompokService->delete($kelompokId);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('kelompoks', ['id' => $kelompokId]);
    }

    /**
     * Test: Kelompok deletion after students removed
     * Validates: Requirements 5.10
     */
    public function test_property_15_deletion_after_students_removed(): void
    {
        // Arrange: Create kelompok with students
        $kelompok = $this->makeKelompok(['nama' => 'Kelompok Student Removal']);

        $siswa1 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        $siswa2 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Remove students from kelompok
        $siswa1->update(['kelompok_id' => null, 'status_penempatan' => 'Terdaftar']);
        $siswa2->update(['kelompok_id' => null, 'status_penempatan' => 'Terdaftar']);

        $kelompokId = $kelompok->id;

        // Act: Now should be deletable
        $result = $this->kelompokService->delete($kelompokId);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('kelompoks', ['id' => $kelompokId]);
    }

    // =========================================================================
    // INTEGRATION TESTS - Complete Kelompok Management Flow
    // Requirements: 5.2, 5.3, 5.4, 5.5
    // =========================================================================

    /**
     * Test: Complete kelompok creation with all validations
     * Validates: Requirements 5.2, 5.3, 5.4, 5.5
     */
    public function test_complete_kelompok_creation_success(): void
    {
        // Arrange
        $tahunAjaran = $this->makeTahunAjaran(['label' => '2024/2025']);
        $guru = $this->makePembimbingTahfidz(['nama' => 'Ustadz Ahmad']);

        $kelompokData = [
            'nama' => 'Kelompok Tahfidz Alpha',
            'jenis' => 'Banin',
            'tingkat' => 'Menengah',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_min' => 8,
            'kapasitas_maks' => 15,
            'jadwal_hari' => 'Senin, Rabu, Jumat',
            'jadwal_jam' => '07:30-09:30',
        ];

        // Act
        $kelompok = $this->kelompokService->create($kelompokData);

        // Assert
        $this->assertDatabaseHas('kelompoks', [
            'id' => $kelompok->id,
            'nama' => 'Kelompok Tahfidz Alpha',
            'jenis' => 'Banin',
            'tingkat' => 'Menengah',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_min' => 8,
            'kapasitas_maks' => 15,
        ]);

        // Verify relationships
        $this->assertEquals('Ustadz Ahmad', $kelompok->guruPembimbing->nama);
        $this->assertEquals('2024/2025', $kelompok->tahunAjaran->label);
    }

    /**
     * Test: Status kapasitas calculation
     * Validates: Requirements 5.13
     */
    public function test_status_kapasitas_calculation(): void
    {
        // Arrange: Create kelompok with capacity 3-5
        $kelompok = $this->makeKelompok([
            'nama' => 'Kelompok Status Test',
            'kapasitas_min' => 3,
            'kapasitas_maks' => 5,
        ]);

        // Test 1: Empty kelompok - Tersedia
        $this->assertEquals('Tersedia', $kelompok->status_kapasitas);

        // Test 2: Add 3 students - still Tersedia
        Siswa::factory()->count(3)->create([
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);
        $kelompok->refresh();
        $this->assertEquals('Tersedia', $kelompok->status_kapasitas);

        // Test 3: Add 2 more students (total 5) - Penuh
        Siswa::factory()->count(2)->create([
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);
        $kelompok->refresh();
        $this->assertEquals('Penuh', $kelompok->status_kapasitas);
    }
}
