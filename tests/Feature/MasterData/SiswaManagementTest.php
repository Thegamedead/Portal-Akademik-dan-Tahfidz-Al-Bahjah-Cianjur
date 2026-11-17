<?php

namespace Tests\Feature\MasterData;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Kelompok;
use App\Models\OrangTuaWali;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\MasterData\OrangTuaWaliService;
use App\Services\MasterData\SiswaService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Integration Tests for Siswa Registration and Management
 *
 * **Requirements**: 2.3, 2.4, 2.5, 2.8, 2.9, 2.11, 2.12, 2.13, 3.3
 *
 * This test suite verifies the following properties:
 * - Property 5: Siswa registration with 1-3 OrangTuaWali entries with unique jenis per Siswa
 * - Property 4: NIS and NISN uniqueness validation
 * - Conditional validation: status_masuk Pindahan requires asal_sekolah and alamat_sekolah_asal
 * - Property 14: Soft delete when status changes to Lulus/Pindah/Keluar
 * - Property 15: Deletion prevention when transaction data exists
 * - Search and filter functionality (NIS, NISN, nama, status fields, jenis_kelamin)
 *
 * Run with: php artisan test --filter SiswaManagementTest
 */
class SiswaManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected SiswaService $siswaService;
    protected OrangTuaWaliService $orangTuaWaliService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin and authenticate
        $this->admin = Admin::factory()->create();
        $this->actingAs($this->admin, 'admin');

        // Initialize services
        $this->siswaService = app(SiswaService::class);
        $this->orangTuaWaliService = app(OrangTuaWaliService::class);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Create a Siswa with default or custom attributes
     */
    private function makeSiswa(array $overrides = []): Siswa
    {
        return Siswa::factory()->create($overrides);
    }

    /**
     * Create OrangTuaWali data array for registration
     */
    private function makeOrangTuaWaliData(int $count = 1, array $overrides = []): array
    {
        $jenisOptions = ['Ayah', 'Ibu', 'Wali'];
        $data = [];

        for ($i = 0; $i < $count; $i++) {
            $data[] = array_merge([
                'jenis' => $jenisOptions[$i] ?? 'Wali',
                'nama' => fake()->name(),
                'tempat_lahir' => fake()->city(),
                'tanggal_lahir' => fake()->dateTimeBetween('-60 years', '-30 years')->format('Y-m-d'),
                'pendidikan' => 'SMA',
                'pekerjaan' => 'Wiraswasta',
                'penghasilan' => 5000000,
                'no_telepon' => '081234567890',
                'email' => fake()->optional(0.5)->safeEmail(),
            ], $overrides);
        }

        return $data;
    }

    /**
     * Create a Kelompok for testing placement
     */
    private function makeKelompok(array $overrides = []): Kelompok
    {
        $guru = Guru::factory()->create([
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'status_aktif' => true,
        ]);

        $tahunAjaran = TahunAjaran::factory()->create([
            'status' => true,
        ]);

        return Kelompok::factory()->create(array_merge([
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ], $overrides));
    }

    // =========================================================================
    // PROPERTY 5: Siswa registration with 1-3 OrangTuaWali entries
    // Requirements: 2.3, 3.3
    // =========================================================================

    /**
     * Test: Siswa can be registered with 1 OrangTuaWali
     * Validates: Requirements 2.3, 3.3
     */
    public function test_property_5_siswa_registration_with_one_orang_tua_wali(): void
    {
        // Arrange
        $siswaData = [
            'nis' => '2024001',
            'nisn' => '0012345678',
            'nama' => 'Ahmad Fauzan',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2010-05-15',
            'tempat_lahir' => 'Jakarta',
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'status_masuk' => 'Baru',
            'tanggal_masuk' => '2024-07-01',
        ];

        $orangTuaData = $this->makeOrangTuaWaliData(1);

        // Act
        $siswa = $this->siswaService->register($siswaData, $orangTuaData);

        // Assert
        $this->assertDatabaseHas('siswas', [
            'id' => $siswa->id,
            'nis' => '2024001',
            'nama' => 'Ahmad Fauzan',
        ]);

        $this->assertEquals(1, OrangTuaWali::where('siswa_id', $siswa->id)->count());
        $this->assertDatabaseHas('orang_tua_walis', [
            'siswa_id' => $siswa->id,
            'jenis' => 'Ayah',
        ]);
    }

    /**
     * Test: Siswa can be registered with 3 OrangTuaWali entries
     * Validates: Requirements 2.3, 3.3
     */
    public function test_property_5_siswa_registration_with_three_orang_tua_wali(): void
    {
        // Arrange
        $siswaData = [
            'nis' => '2024002',
            'nisn' => '0023456789',
            'nama' => 'Siti Aminah',
            'jenis_kelamin' => 'Perempuan',
            'tanggal_lahir' => '2011-08-20',
            'tempat_lahir' => 'Bandung',
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'status_masuk' => 'Baru',
            'tanggal_masuk' => '2024-07-01',
        ];

        $orangTuaData = $this->makeOrangTuaWaliData(3);

        // Act
        $siswa = $this->siswaService->register($siswaData, $orangTuaData);

        // Assert
        $this->assertEquals(3, OrangTuaWali::where('siswa_id', $siswa->id)->count());

        $this->assertDatabaseHas('orang_tua_walis', [
            'siswa_id' => $siswa->id,
            'jenis' => 'Ayah',
        ]);

        $this->assertDatabaseHas('orang_tua_walis', [
            'siswa_id' => $siswa->id,
            'jenis' => 'Ibu',
        ]);

        $this->assertDatabaseHas('orang_tua_walis', [
            'siswa_id' => $siswa->id,
            'jenis' => 'Wali',
        ]);
    }

    /**
     * Test: Each Siswa has 1-3 OrangTuaWali with unique jenis per Siswa
     * Validates: Requirements 3.3
     */
    public function test_property_5_unique_jenis_per_siswa(): void
    {
        // Arrange
        $siswaData = [
            'nis' => '2024003',
            'nisn' => '0034567890',
            'nama' => 'Budi Santoso',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2010-03-10',
            'tempat_lahir' => 'Surabaya',
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'status_masuk' => 'Baru',
            'tanggal_masuk' => '2024-07-01',
        ];

        $orangTuaData = [
            [
                'jenis' => 'Ayah',
                'nama' => 'Santoso',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1980-01-01',
                'pendidikan' => 'S1',
                'pekerjaan' => 'PNS',
                'penghasilan' => 8000000,
                'no_telepon' => '081234567890',
            ],
            [
                'jenis' => 'Ayah', // Duplicate jenis
                'nama' => 'Another Ayah',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1985-01-01',
                'pendidikan' => 'SMA',
                'pekerjaan' => 'Wiraswasta',
                'penghasilan' => 5000000,
                'no_telepon' => '082345678901',
            ],
        ];

        // Act & Assert
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Jenis orang tua/wali tidak boleh duplikat');

        $this->siswaService->register($siswaData, $orangTuaData);
    }

    /**
     * Test: OrangTuaWali jenis uniqueness is enforced per Siswa only
     * Validates: Requirements 3.3
     */
    public function test_property_5_jenis_uniqueness_per_siswa_not_global(): void
    {
        // Arrange: Create first siswa with Ayah
        $siswa1Data = [
            'nis' => '2024004',
            'nama' => 'Siswa One',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2010-01-01',
            'tempat_lahir' => 'Jakarta',
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'status_masuk' => 'Baru',
            'tanggal_masuk' => '2024-07-01',
        ];

        $orangTua1Data = [
            ['jenis' => 'Ayah', 'nama' => 'Ayah One', 'tempat_lahir' => 'Jakarta',
             'tanggal_lahir' => '1980-01-01', 'pendidikan' => 'SMA', 'pekerjaan' => 'PNS',
             'penghasilan' => 5000000, 'no_telepon' => '081111111111'],
        ];

        // Create second siswa with same jenis (Ayah)
        $siswa2Data = [
            'nis' => '2024005',
            'nama' => 'Siswa Two',
            'jenis_kelamin' => 'Perempuan',
            'tanggal_lahir' => '2011-02-02',
            'tempat_lahir' => 'Bandung',
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'status_masuk' => 'Baru',
            'tanggal_masuk' => '2024-07-01',
        ];

        $orangTua2Data = [
            ['jenis' => 'Ayah', 'nama' => 'Ayah Two', 'tempat_lahir' => 'Bandung',
             'tanggal_lahir' => '1982-02-02', 'pendidikan' => 'S1', 'pekerjaan' => 'Dokter',
             'penghasilan' => 15000000, 'no_telepon' => '082222222222'],
        ];

        // Act
        $siswa1 = $this->siswaService->register($siswa1Data, $orangTua1Data);
        $siswa2 = $this->siswaService->register($siswa2Data, $orangTua2Data);

        // Assert: Both siswa can have Ayah
        $this->assertDatabaseHas('orang_tua_walis', [
            'siswa_id' => $siswa1->id,
            'jenis' => 'Ayah',
        ]);

        $this->assertDatabaseHas('orang_tua_walis', [
            'siswa_id' => $siswa2->id,
            'jenis' => 'Ayah',
        ]);
    }

    // =========================================================================
    // PROPERTY 4: NIS and NISN uniqueness validation
    // Requirements: 2.3, 2.4
    // =========================================================================

    /**
     * Test: NIS must be globally unique
     * Validates: Requirements 2.3
     */
    public function test_property_4_nis_globally_unique(): void
    {
        // Arrange: Create siswa with NIS
        $siswa1 = $this->makeSiswa(['nis' => '2024100']);

        $siswa2Data = [
            'nis' => '2024100', // Duplicate NIS
            'nama' => 'Another Student',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2011-05-05',
            'tempat_lahir' => 'Jakarta',
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'status_masuk' => 'Baru',
            'tanggal_masuk' => '2024-07-01',
        ];

        $orangTuaData = $this->makeOrangTuaWaliData(1);

        // Act & Assert
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('NIS sudah terdaftar dalam sistem');

        $this->siswaService->register($siswa2Data, $orangTuaData);
    }

    /**
     * Test: NISN must be globally unique when not null
     * Validates: Requirements 2.4
     */
    public function test_property_4_nisn_globally_unique_when_not_null(): void
    {
        // Arrange: Create siswa with NISN
        $siswa1 = $this->makeSiswa(['nisn' => '0098765432']);

        $siswa2Data = [
            'nis' => '2024101',
            'nisn' => '0098765432', // Duplicate NISN
            'nama' => 'Another Student',
            'jenis_kelamin' => 'Perempuan',
            'tanggal_lahir' => '2012-06-06',
            'tempat_lahir' => 'Bandung',
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'status_masuk' => 'Baru',
            'tanggal_masuk' => '2024-07-01',
        ];

        $orangTuaData = $this->makeOrangTuaWaliData(1);

        // Act & Assert
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('NISN sudah terdaftar dalam sistem');

        $this->siswaService->register($siswa2Data, $orangTuaData);
    }

    /**
     * Test: Multiple siswa can have null NISN
     * Validates: Requirements 2.4
     */
    public function test_property_4_multiple_null_nisn_allowed(): void
    {
        // Arrange
        $siswa1Data = [
            'nis' => '2024102',
            'nisn' => null,
            'nama' => 'Student One',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2010-07-07',
            'tempat_lahir' => 'Jakarta',
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'status_masuk' => 'Baru',
            'tanggal_masuk' => '2024-07-01',
        ];

        $siswa2Data = [
            'nis' => '2024103',
            'nisn' => null,
            'nama' => 'Student Two',
            'jenis_kelamin' => 'Perempuan',
            'tanggal_lahir' => '2011-08-08',
            'tempat_lahir' => 'Bandung',
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'status_masuk' => 'Baru',
            'tanggal_masuk' => '2024-07-01',
        ];

        $orangTuaData = $this->makeOrangTuaWaliData(1);

        // Act
        $siswa1 = $this->siswaService->register($siswa1Data, $orangTuaData);
        $siswa2 = $this->siswaService->register($siswa2Data, $orangTuaData);

        // Assert: Both created successfully
        $this->assertDatabaseHas('siswas', ['id' => $siswa1->id, 'nisn' => null]);
        $this->assertDatabaseHas('siswas', ['id' => $siswa2->id, 'nisn' => null]);
    }

    /**
     * Test: NIS uniqueness is validated on update
     * Validates: Requirements 2.10
     */
    public function test_property_4_nis_uniqueness_on_update(): void
    {
        // Arrange: Create two siswa
        $siswa1 = $this->makeSiswa(['nis' => '2024104']);
        $siswa2 = $this->makeSiswa(['nis' => '2024105']);

        // Act & Assert: Try to update siswa2 with siswa1's NIS
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('NIS sudah terdaftar dalam sistem');

        $this->siswaService->update($siswa2->id, ['nis' => '2024104']);
    }

    /**
     * Test: Siswa can update their own NIS to same value
     * Validates: Requirements 2.10
     */
    public function test_property_4_siswa_can_keep_same_nis_on_update(): void
    {
        // Arrange
        $siswa = $this->makeSiswa(['nis' => '2024106', 'nama' => 'Original Name']);

        // Act: Update with same NIS but different nama
        $updated = $this->siswaService->update($siswa->id, [
            'nis' => '2024106',
            'nama' => 'Updated Name',
        ]);

        // Assert
        $this->assertEquals('2024106', $updated->nis);
        $this->assertEquals('Updated Name', $updated->nama);
    }

    // =========================================================================
    // CONDITIONAL VALIDATION: status_masuk Pindahan requires asal_sekolah
    // Requirements: 2.5
    // =========================================================================

    /**
     * Test: status_masuk Pindahan requires asal_sekolah and alamat_sekolah_asal
     * Validates: Requirements 2.5
     */
    public function test_conditional_validation_pindahan_requires_asal_sekolah(): void
    {
        // Arrange
        $siswaData = [
            'nis' => '2024107',
            'nama' => 'Transfer Student',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2010-09-09',
            'tempat_lahir' => 'Jakarta',
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'status_masuk' => 'Pindahan',
            'tanggal_masuk' => '2024-07-01',
            // Missing asal_sekolah and alamat_sekolah_asal
        ];

        $orangTuaData = $this->makeOrangTuaWaliData(1);

        // Act & Assert
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Asal sekolah wajib diisi untuk siswa pindahan');

        $this->siswaService->register($siswaData, $orangTuaData);
    }

    /**
     * Test: status_masuk Pindahan with asal_sekolah is accepted
     * Validates: Requirements 2.5
     */
    public function test_conditional_validation_pindahan_with_asal_sekolah_accepted(): void
    {
        // Arrange
        $siswaData = [
            'nis' => '2024108',
            'nama' => 'Transfer Student Valid',
            'jenis_kelamin' => 'Perempuan',
            'tanggal_lahir' => '2011-10-10',
            'tempat_lahir' => 'Bandung',
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'status_masuk' => 'Pindahan',
            'tanggal_masuk' => '2024-07-01',
            'asal_sekolah' => 'SMP Negeri 1 Jakarta',
            'alamat_sekolah_asal' => 'Jl. Sudirman No. 1, Jakarta',
        ];

        $orangTuaData = $this->makeOrangTuaWaliData(1);

        // Act
        $siswa = $this->siswaService->register($siswaData, $orangTuaData);

        // Assert
        $this->assertDatabaseHas('siswas', [
            'id' => $siswa->id,
            'status_masuk' => 'Pindahan',
            'asal_sekolah' => 'SMP Negeri 1 Jakarta',
        ]);
    }

    /**
     * Test: status_masuk Baru does not require asal_sekolah
     * Validates: Requirements 2.5
     */
    public function test_conditional_validation_baru_does_not_require_asal_sekolah(): void
    {
        // Arrange
        $siswaData = [
            'nis' => '2024109',
            'nama' => 'New Student',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2010-11-11',
            'tempat_lahir' => 'Jakarta',
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'status_masuk' => 'Baru',
            'tanggal_masuk' => '2024-07-01',
            // No asal_sekolah needed
        ];

        $orangTuaData = $this->makeOrangTuaWaliData(1);

        // Act
        $siswa = $this->siswaService->register($siswaData, $orangTuaData);

        // Assert
        $this->assertDatabaseHas('siswas', [
            'id' => $siswa->id,
            'status_masuk' => 'Baru',
        ]);

        $this->assertNull($siswa->asal_sekolah);
    }

    // =========================================================================
    // PROPERTY 14: Soft delete when status changes to Lulus/Pindah/Keluar
    // Requirements: 2.11
    // =========================================================================

    /**
     * Test: Soft delete when status_akademik changes to Lulus SMP
     * Validates: Requirements 2.11
     */
    public function test_property_14_soft_delete_on_lulus_smp(): void
    {
        // Arrange
        $siswa = $this->makeSiswa([
            'status_akademik' => 'Aktif',
            'status_aktif' => true,
        ]);

        // Act
        $updated = $this->siswaService->updateStatus($siswa->id, 'Lulus SMP');

        // Assert: deleted_at is not null after soft delete
        $this->assertNotNull($updated->deleted_at);
        $this->assertEquals('Lulus SMP', $updated->status_akademik);
        $this->assertFalse($updated->status_aktif);

        // Verify in database
        $this->assertSoftDeleted('siswas', ['id' => $siswa->id]);
    }

    /**
     * Test: Soft delete when status_akademik changes to Pindah Sekolah
     * Validates: Requirements 2.11
     */
    public function test_property_14_soft_delete_on_pindah_sekolah(): void
    {
        // Arrange
        $siswa = $this->makeSiswa([
            'status_akademik' => 'Aktif',
            'status_aktif' => true,
        ]);

        // Act
        $updated = $this->siswaService->updateStatus($siswa->id, 'Pindah Sekolah');

        // Assert
        $this->assertNotNull($updated->deleted_at);
        $this->assertEquals('Pindah Sekolah', $updated->status_akademik);
        $this->assertFalse($updated->status_aktif);
        $this->assertSoftDeleted('siswas', ['id' => $siswa->id]);
    }

    /**
     * Test: Soft delete when status_akademik changes to Keluar
     * Validates: Requirements 2.11
     */
    public function test_property_14_soft_delete_on_keluar(): void
    {
        // Arrange
        $siswa = $this->makeSiswa([
            'status_akademik' => 'Aktif',
            'status_aktif' => true,
        ]);

        // Act
        $updated = $this->siswaService->updateStatus($siswa->id, 'Keluar');

        // Assert
        $this->assertNotNull($updated->deleted_at);
        $this->assertEquals('Keluar', $updated->status_akademik);
        $this->assertFalse($updated->status_aktif);
        $this->assertSoftDeleted('siswas', ['id' => $siswa->id]);
    }

    /**
     * Test: No soft delete when status_akademik remains Aktif
     * Validates: Requirements 2.11
     */
    public function test_property_14_no_soft_delete_when_aktif(): void
    {
        // Arrange
        $siswa = $this->makeSiswa([
            'status_akademik' => 'Aktif',
            'status_aktif' => true,
        ]);

        // Act
        $updated = $this->siswaService->updateStatus($siswa->id, 'Aktif');

        // Assert
        $this->assertNull($updated->deleted_at);
        $this->assertEquals('Aktif', $updated->status_akademik);
        $this->assertTrue($updated->status_aktif);
    }

    // =========================================================================
    // PROPERTY 15: Deletion prevention when transaction data exists
    // Requirements: 2.12, 2.13
    // =========================================================================

    /**
     * Test: Siswa deletion allowed when no dependencies exist
     * Validates: Requirements 2.12
     */
    public function test_property_15_deletion_allowed_without_dependencies(): void
    {
        // Arrange: Create siswa with orang tua wali only
        $siswa = $this->makeSiswa();
        OrangTuaWali::factory()->count(2)->create(['siswa_id' => $siswa->id]);

        $siswaId = $siswa->id;

        // Act
        $result = $this->siswaService->delete($siswaId);

        // Assert: Siswa and related OrangTuaWali are deleted permanently
        $this->assertTrue($result);
        $this->assertDatabaseMissing('siswas', ['id' => $siswaId]);
        $this->assertEquals(0, OrangTuaWali::where('siswa_id', $siswaId)->count());
    }

    /**
     * Test: Siswa deletion blocked when placed in kelompok
     * Validates: Requirements 2.13
     */
    public function test_property_15_deletion_blocked_when_in_kelompok(): void
    {
        // Arrange: Create siswa placed in kelompok
        $kelompok = $this->makeKelompok();
        $siswa = $this->makeSiswa([
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Tidak dapat menghapus Siswa ini.*kelompok/');

        $this->siswaService->delete($siswa->id);
    }

    /**
     * Test: Siswa without kelompok can be deleted
     * Validates: Requirements 2.12
     */
    public function test_property_15_siswa_without_kelompok_can_be_deleted(): void
    {
        // Arrange: Create siswa with status Terdaftar (not placed)
        $siswa = $this->makeSiswa([
            'kelompok_id' => null,
            'status_penempatan' => 'Terdaftar',
        ]);

        $orangTuaWali = OrangTuaWali::factory()->create(['siswa_id' => $siswa->id]);

        $siswaId = $siswa->id;
        $orangTuaWaliId = $orangTuaWali->id;

        // Act
        $result = $this->siswaService->delete($siswaId);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('siswas', ['id' => $siswaId]);
        $this->assertDatabaseMissing('orang_tua_walis', ['id' => $orangTuaWaliId]);
    }

    /**
     * Test: Siswa deletion cascades to OrangTuaWali
     * Validates: Requirements 3.9
     */
    public function test_property_15_deletion_cascades_to_orang_tua_wali(): void
    {
        // Arrange
        $siswa = $this->makeSiswa();

        $orangTuaIds = [];
        $orangTuaIds[] = OrangTuaWali::factory()->create([
            'siswa_id' => $siswa->id,
            'jenis' => 'Ayah',
        ])->id;

        $orangTuaIds[] = OrangTuaWali::factory()->create([
            'siswa_id' => $siswa->id,
            'jenis' => 'Ibu',
        ])->id;

        $orangTuaIds[] = OrangTuaWali::factory()->create([
            'siswa_id' => $siswa->id,
            'jenis' => 'Wali',
        ])->id;

        // Act
        $this->siswaService->delete($siswa->id);

        // Assert: All OrangTuaWali records are deleted
        foreach ($orangTuaIds as $orangTuaId) {
            $this->assertDatabaseMissing('orang_tua_walis', ['id' => $orangTuaId]);
        }
    }

    // =========================================================================
    // SEARCH AND FILTER FUNCTIONALITY
    // Requirements: 2.8, 2.9
    // =========================================================================

    /**
     * Test: Search by NIS
     * Validates: Requirements 2.9
     */
    public function test_search_by_nis(): void
    {
        // Arrange
        $siswa1 = $this->makeSiswa(['nis' => '2024201', 'nama' => 'Student Alpha']);
        $siswa2 = $this->makeSiswa(['nis' => '2024202', 'nama' => 'Student Beta']);
        $siswa3 = $this->makeSiswa(['nis' => '2024203', 'nama' => 'Student Gamma']);

        // Act
        $results = $this->siswaService->getAllWithFilters(['search' => '2024201'])->get();

        // Assert
        $this->assertCount(1, $results);
        $this->assertEquals('2024201', $results->first()->nis);
        $this->assertEquals('Student Alpha', $results->first()->nama);
    }

    /**
     * Test: Search by NISN
     * Validates: Requirements 2.9
     */
    public function test_search_by_nisn(): void
    {
        // Arrange
        $siswa1 = $this->makeSiswa(['nisn' => '1234567890', 'nama' => 'Student One']);
        $siswa2 = $this->makeSiswa(['nisn' => '0987654321', 'nama' => 'Student Two']);

        // Act
        $results = $this->siswaService->getAllWithFilters(['search' => '1234567890'])->get();

        // Assert
        $this->assertCount(1, $results);
        $this->assertEquals('1234567890', $results->first()->nisn);
    }

    /**
     * Test: Search by nama (partial match)
     * Validates: Requirements 2.9
     */
    public function test_search_by_nama_partial(): void
    {
        // Arrange
        $siswa1 = $this->makeSiswa(['nama' => 'Ahmad Fauzan']);
        $siswa2 = $this->makeSiswa(['nama' => 'Ahmad Zaki']);
        $siswa3 = $this->makeSiswa(['nama' => 'Siti Aminah']);

        // Act: Search for "Ahmad"
        $results = $this->siswaService->getAllWithFilters(['search' => 'Ahmad'])->get();

        // Assert: Both Ahmad records found
        $this->assertCount(2, $results);
        $this->assertTrue($results->contains('nama', 'Ahmad Fauzan'));
        $this->assertTrue($results->contains('nama', 'Ahmad Zaki'));
    }

    /**
     * Test: Filter by status_akademik
     * Validates: Requirements 2.8
     */
    public function test_filter_by_status_akademik(): void
    {
        // Arrange
        $siswaAktif1 = $this->makeSiswa(['status_akademik' => 'Aktif']);
        $siswaAktif2 = $this->makeSiswa(['status_akademik' => 'Aktif']);
        $siswaLulus = $this->makeSiswa(['status_akademik' => 'Lulus SMP']);

        // Act
        $results = $this->siswaService->getAllWithFilters(['status_akademik' => 'Aktif'])->get();

        // Assert
        $this->assertCount(2, $results);
        $this->assertTrue($results->contains('id', $siswaAktif1->id));
        $this->assertTrue($results->contains('id', $siswaAktif2->id));
        $this->assertFalse($results->contains('id', $siswaLulus->id));
    }

    /**
     * Test: Filter by status_tahfidz
     * Validates: Requirements 2.8
     */
    public function test_filter_by_status_tahfidz(): void
    {
        // Arrange
        $siswaBerjalan1 = $this->makeSiswa(['status_tahfidz' => 'Sedang Berjalan']);
        $siswaBerjalan2 = $this->makeSiswa(['status_tahfidz' => 'Sedang Berjalan']);
        $siswaLulus = $this->makeSiswa(['status_tahfidz' => 'Lulus Program Tahfidz']);

        // Act
        $results = $this->siswaService->getAllWithFilters(['status_tahfidz' => 'Sedang Berjalan'])->get();

        // Assert
        $this->assertCount(2, $results);
        $this->assertTrue($results->contains('id', $siswaBerjalan1->id));
        $this->assertTrue($results->contains('id', $siswaBerjalan2->id));
        $this->assertFalse($results->contains('id', $siswaLulus->id));
    }

    /**
     * Test: Filter by status_penempatan
     * Validates: Requirements 2.8
     */
    public function test_filter_by_status_penempatan(): void
    {
        // Arrange
        $siswaTerdaftar1 = $this->makeSiswa(['status_penempatan' => 'Terdaftar']);
        $siswaTerdaftar2 = $this->makeSiswa(['status_penempatan' => 'Terdaftar']);
        $siswaDitempatkan = $this->makeSiswa(['status_penempatan' => 'Ditempatkan']);

        // Act
        $results = $this->siswaService->getAllWithFilters(['status_penempatan' => 'Terdaftar'])->get();

        // Assert
        $this->assertCount(2, $results);
        $this->assertTrue($results->contains('id', $siswaTerdaftar1->id));
        $this->assertTrue($results->contains('id', $siswaTerdaftar2->id));
        $this->assertFalse($results->contains('id', $siswaDitempatkan->id));
    }

    /**
     * Test: Filter by jenis_kelamin
     * Validates: Requirements 2.8
     */
    public function test_filter_by_jenis_kelamin(): void
    {
        // Arrange
        $siswaLakiLaki1 = $this->makeSiswa(['jenis_kelamin' => 'Laki-laki']);
        $siswaLakiLaki2 = $this->makeSiswa(['jenis_kelamin' => 'Laki-laki']);
        $siswaPerempuan = $this->makeSiswa(['jenis_kelamin' => 'Perempuan']);

        // Act
        $results = $this->siswaService->getAllWithFilters(['jenis_kelamin' => 'Laki-laki'])->get();

        // Assert
        $this->assertCount(2, $results);
        $this->assertTrue($results->contains('id', $siswaLakiLaki1->id));
        $this->assertTrue($results->contains('id', $siswaLakiLaki2->id));
        $this->assertFalse($results->contains('id', $siswaPerempuan->id));
    }

    /**
     * Test: Combined search and filter
     * Validates: Requirements 2.8, 2.9
     */
    public function test_combined_search_and_filter(): void
    {
        // Arrange
        $siswa1 = $this->makeSiswa([
            'nama' => 'Ahmad Fauzan',
            'status_akademik' => 'Aktif',
            'jenis_kelamin' => 'Laki-laki',
        ]);

        $siswa2 = $this->makeSiswa([
            'nama' => 'Ahmad Zaki',
            'status_akademik' => 'Lulus SMP',
            'jenis_kelamin' => 'Laki-laki',
        ]);

        $siswa3 = $this->makeSiswa([
            'nama' => 'Siti Aminah',
            'status_akademik' => 'Aktif',
            'jenis_kelamin' => 'Perempuan',
        ]);

        // Act: Search "Ahmad" AND filter by status_akademik=Aktif
        $results = $this->siswaService->getAllWithFilters([
            'search' => 'Ahmad',
            'status_akademik' => 'Aktif',
        ])->get();

        // Assert: Only siswa1 matches (Ahmad in name + Aktif status)
        $this->assertCount(1, $results);
        $this->assertEquals($siswa1->id, $results->first()->id);
    }
}

