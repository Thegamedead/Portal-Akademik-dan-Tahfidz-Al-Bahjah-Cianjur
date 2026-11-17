<?php

namespace Tests\Feature\MasterData;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Kelompok;
use App\Models\TahunAjaran;
use App\Services\MasterData\GuruService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Integration Tests for Guru Registration and Management
 *
 * **Requirements**: 4.4, 4.5, 4.6, 4.11, 4.12, 4.13
 *
 * This test suite verifies the following properties:
 * - Property 6: NIY is globally unique across all Guru records
 * - Jabatan enum validation
 * - Property 7: program_studi is required when pendidikan_terakhir >= D1
 * - Property 14: Soft delete when status_aktif = false
 * - Property 15: Deletion prevention when dependencies exist
 *
 * Run with: php artisan test --filter GuruTest
 */
class GuruTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected GuruService $guruService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin and authenticate
        $this->admin = Admin::factory()->create();
        $this->actingAs($this->admin, 'admin');

        // Initialize service
        $this->guruService = app(GuruService::class);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Create a Guru with default or custom attributes
     */
    private function makeGuru(array $overrides = []): Guru
    {
        return Guru::factory()->create($overrides);
    }

    /**
     * Create valid guru data for registration
     */
    private function makeGuruData(array $overrides = []): array
    {
        return array_merge([
            'niy' => fake()->unique()->numerify('GR####'),
            'nama' => fake()->name(),
            'jenis_kelamin' => fake()->randomElement(['Laki-laki', 'Perempuan']),
            'tanggal_lahir' => fake()->dateTimeBetween('-60 years', '-25 years')->format('Y-m-d'),
            'tempat_lahir' => fake()->city(),
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'status_pernikahan' => fake()->randomElement(['Menikah', 'Belum Menikah', 'Cerai']),
            'pendidikan_terakhir' => 'SMA',
            'tahun_lulus' => '2010',
            'jabatan' => 'Guru Mapel',
            'no_telepon' => '081234567890',
            'alamat_jalan' => fake()->streetAddress(),
            'alamat_desa_kelurahan' => fake()->city(),
            'alamat_kecamatan' => fake()->city(),
            'alamat_kabupaten_kota' => fake()->city(),
            'alamat_provinsi' => fake()->state(),
            'alamat_kode_pos' => '12345',
        ], $overrides);
    }

    /**
     * Create a TahunAjaran for testing
     */
    private function makeTahunAjaran(array $overrides = []): TahunAjaran
    {
        return TahunAjaran::factory()->create(array_merge([
            'status' => true,
        ], $overrides));
    }

    // =========================================================================
    // PROPERTY 6: NIY is globally unique across all Guru records
    // Requirements: 4.4
    // =========================================================================

    /**
     * Test: NIY must be globally unique
     * Validates: Requirements 4.4
     */
    public function test_property_6_niy_globally_unique(): void
    {
        // Arrange: Create guru with NIY
        $guru1 = $this->makeGuru(['niy' => 'GR2024001']);

        $guru2Data = $this->makeGuruData(['niy' => 'GR2024001']); // Duplicate NIY

        // Act & Assert
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('NIY sudah terdaftar dalam sistem');

        $this->guruService->register($guru2Data);
    }

    /**
     * Test: NIY uniqueness is validated on update
     * Validates: Requirements 4.10
     */
    public function test_property_6_niy_uniqueness_on_update(): void
    {
        // Arrange: Create two gurus
        $guru1 = $this->makeGuru(['niy' => 'GR2024002']);
        $guru2 = $this->makeGuru(['niy' => 'GR2024003']);

        // Act & Assert: Try to update guru2 with guru1's NIY
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('NIY sudah terdaftar dalam sistem');

        $this->guruService->update($guru2->id, ['niy' => 'GR2024002']);
    }

    /**
     * Test: Guru can update their own NIY to same value
     * Validates: Requirements 4.10
     */
    public function test_property_6_guru_can_keep_same_niy_on_update(): void
    {
        // Arrange
        $guru = $this->makeGuru(['niy' => 'GR2024004', 'nama' => 'Original Name']);

        // Act: Update with same NIY but different nama
        $updated = $this->guruService->update($guru->id, [
            'niy' => 'GR2024004',
            'nama' => 'Updated Name',
        ]);

        // Assert
        $this->assertEquals('GR2024004', $updated->niy);
        $this->assertEquals('Updated Name', $updated->nama);
    }

    // =========================================================================
    // JABATAN ENUM VALIDATION
    // Requirements: 4.5
    // =========================================================================

    /**
     * Test: Valid jabatan values are accepted
     * Validates: Requirements 4.5
     */
    public function test_jabatan_enum_valid_values(): void
    {
        $validJabatans = [
            'Guru Pembimbing Tahfidz',
            'Guru Mapel',
            'Kepala Sekolah',
            'Tendik'
        ];

        foreach ($validJabatans as $jabatan) {
            // Arrange
            $guruData = $this->makeGuruData([
                'niy' => fake()->unique()->numerify('GR####'),
                'jabatan' => $jabatan,
            ]);

            // Act
            $guru = $this->guruService->register($guruData);

            // Assert
            $this->assertEquals($jabatan, $guru->jabatan);
            $this->assertDatabaseHas('gurus', [
                'id' => $guru->id,
                'jabatan' => $jabatan,
            ]);
        }
    }

    /**
     * Test: Invalid jabatan value is rejected
     * Validates: Requirements 4.5
     */
    public function test_jabatan_enum_invalid_value(): void
    {
        // Arrange
        $guruData = $this->makeGuruData([
            'jabatan' => 'Invalid Jabatan', // Invalid enum value
        ]);

        // Act & Assert
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Jabatan tidak valid');

        $this->guruService->register($guruData);
    }

    // =========================================================================
    // PROPERTY 7: program_studi requirement for pendidikan >= D1
    // Requirements: 4.3, 4.6
    // =========================================================================

    /**
     * Test: program_studi is required for pendidikan D1 and above
     * Validates: Requirements 4.3, 4.6
     */
    public function test_property_7_program_studi_required_for_d1_and_above(): void
    {
        $pendidikanRequiringProgramStudi = ['D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];

        foreach ($pendidikanRequiringProgramStudi as $pendidikan) {
            // Arrange
            $guruData = $this->makeGuruData([
                'niy' => fake()->unique()->numerify('GR####'),
                'pendidikan_terakhir' => $pendidikan,
                'program_studi' => null, // Missing program_studi
            ]);

            // Act & Assert
            try {
                $this->guruService->register($guruData);
                $this->fail("Expected ValidationException for pendidikan $pendidikan without program_studi");
            } catch (ValidationException $e) {
                $this->assertStringContainsString('Program studi wajib diisi untuk pendidikan D1 ke atas', $e->getMessage());
            }
        }
    }

    /**
     * Test: program_studi is provided for D1 and above - registration succeeds
     * Validates: Requirements 4.3, 4.6
     */
    public function test_property_7_program_studi_provided_for_d1_and_above(): void
    {
        $pendidikanRequiringProgramStudi = ['D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];

        foreach ($pendidikanRequiringProgramStudi as $pendidikan) {
            // Arrange
            $guruData = $this->makeGuruData([
                'niy' => fake()->unique()->numerify('GR####'),
                'pendidikan_terakhir' => $pendidikan,
                'program_studi' => 'Pendidikan Agama Islam',
            ]);

            // Act
            $guru = $this->guruService->register($guruData);

            // Assert
            $this->assertEquals($pendidikan, $guru->pendidikan_terakhir);
            $this->assertEquals('Pendidikan Agama Islam', $guru->program_studi);
            $this->assertDatabaseHas('gurus', [
                'id' => $guru->id,
                'pendidikan_terakhir' => $pendidikan,
                'program_studi' => 'Pendidikan Agama Islam',
            ]);
        }
    }

    /**
     * Test: program_studi is not required for pendidikan below D1
     * Validates: Requirements 4.3, 4.6
     */
    public function test_property_7_program_studi_not_required_below_d1(): void
    {
        $pendidikanNotRequiringProgramStudi = ['SD', 'SMP', 'SMA'];

        foreach ($pendidikanNotRequiringProgramStudi as $pendidikan) {
            // Arrange
            $guruData = $this->makeGuruData([
                'niy' => fake()->unique()->numerify('GR####'),
                'pendidikan_terakhir' => $pendidikan,
                'program_studi' => null, // Not required
            ]);

            // Act
            $guru = $this->guruService->register($guruData);

            // Assert
            $this->assertEquals($pendidikan, $guru->pendidikan_terakhir);
            $this->assertNull($guru->program_studi);
            $this->assertDatabaseHas('gurus', [
                'id' => $guru->id,
                'pendidikan_terakhir' => $pendidikan,
                'program_studi' => null,
            ]);
        }
    }

    // =========================================================================
    // PROPERTY 14: Soft delete when status_aktif = false
    // Requirements: 4.11
    // =========================================================================

    /**
     * Test: Soft delete when status_aktif changes to false
     * Validates: Requirements 4.11
     */
    public function test_property_14_soft_delete_when_status_aktif_false(): void
    {
        // Arrange
        $guru = $this->makeGuru([
            'status_aktif' => true,
        ]);

        // Act
        $updated = $this->guruService->updateStatus($guru->id, false);

        // Assert: deleted_at is not null after soft delete
        $this->assertNotNull($updated->deleted_at);
        $this->assertFalse($updated->status_aktif);

        // Verify in database
        $this->assertSoftDeleted('gurus', ['id' => $guru->id]);
    }

    /**
     * Test: No soft delete when status_aktif remains true
     * Validates: Requirements 4.11
     */
    public function test_property_14_no_soft_delete_when_status_aktif_true(): void
    {
        // Arrange
        $guru = $this->makeGuru([
            'status_aktif' => true,
        ]);

        // Act
        $updated = $this->guruService->updateStatus($guru->id, true);

        // Assert
        $this->assertNull($updated->deleted_at);
        $this->assertTrue($updated->status_aktif);
    }

    /**
     * Test: Reactivating guru removes soft delete
     * Validates: Requirements 4.11
     */
    public function test_property_14_reactivate_removes_soft_delete(): void
    {
        // Arrange: Create guru then soft delete it
        $guru = $this->makeGuru([
            'status_aktif' => true,
        ]);

        // First soft delete the guru
        $this->guruService->updateStatus($guru->id, false);

        // Verify soft delete occurred
        $softDeleted = Guru::withTrashed()->find($guru->id);
        $this->assertNotNull($softDeleted->deleted_at);
        $this->assertFalse($softDeleted->status_aktif);

        // Act: Restore using the restore method
        $restored = $this->guruService->restore($guru->id);

        // Assert
        $this->assertNull($restored->deleted_at);
        $this->assertTrue($restored->status_aktif);
    }

    // =========================================================================
    // PROPERTY 15: Deletion prevention when dependencies exist
    // Requirements: 4.12, 4.13
    // =========================================================================

    /**
     * Test: Guru deletion allowed when no dependencies exist
     * Validates: Requirements 4.12
     */
    public function test_property_15_deletion_allowed_without_dependencies(): void
    {
        // Arrange: Create guru without any kelompok
        $guru = $this->makeGuru();
        $guruId = $guru->id;

        // Act
        $result = $this->guruService->delete($guruId);

        // Assert: Guru is deleted permanently
        $this->assertTrue($result);
        $this->assertDatabaseMissing('gurus', ['id' => $guruId]);
    }

    /**
     * Test: Guru deletion blocked when mentoring kelompok
     * Validates: Requirements 4.13
     */
    public function test_property_15_deletion_blocked_when_mentoring_kelompok(): void
    {
        // Arrange: Create guru and kelompok
        $guru = $this->makeGuru([
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'status_aktif' => true,
        ]);

        $tahunAjaran = $this->makeTahunAjaran();

        $kelompok = Kelompok::factory()->create([
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Tidak dapat menghapus Guru ini.*kelompok/');

        $this->guruService->delete($guru->id);
    }

    /**
     * Test: Guru without kelompok can be deleted
     * Validates: Requirements 4.12
     */
    public function test_property_15_guru_without_kelompok_can_be_deleted(): void
    {
        // Arrange: Create guru without kelompok
        $guru = $this->makeGuru([
            'jabatan' => 'Guru Mapel', // Not mentoring any kelompok
            'status_aktif' => true,
        ]);

        $guruId = $guru->id;

        // Act
        $result = $this->guruService->delete($guruId);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('gurus', ['id' => $guruId]);
    }

    /**
     * Test: Former pembimbing can be deleted after kelompok reassignment
     * Validates: Requirements 4.12
     */
    public function test_property_15_former_pembimbing_can_be_deleted_after_reassignment(): void
    {
        // Arrange: Create two gurus and kelompok
        $oldGuru = $this->makeGuru([
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'status_aktif' => true,
        ]);

        $newGuru = $this->makeGuru([
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'status_aktif' => true,
        ]);

        $tahunAjaran = $this->makeTahunAjaran();

        $kelompok = Kelompok::factory()->create([
            'guru_pembimbing_id' => $oldGuru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Reassign kelompok to new guru
        $kelompok->update(['guru_pembimbing_id' => $newGuru->id]);

        $oldGuruId = $oldGuru->id;

        // Act: Try to delete old guru (should succeed now)
        $result = $this->guruService->delete($oldGuruId);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('gurus', ['id' => $oldGuruId]);

        // Verify kelompok is still assigned to new guru
        $this->assertDatabaseHas('kelompoks', [
            'id' => $kelompok->id,
            'guru_pembimbing_id' => $newGuru->id,
        ]);
    }

    // =========================================================================
    // INTEGRATION TESTS - Registration Flow
    // Requirements: 4.4, 4.5, 4.6
    // =========================================================================

    /**
     * Test: Complete guru registration with valid data
     * Validates: Requirements 4.4, 4.5, 4.6
     */
    public function test_complete_guru_registration_success(): void
    {
        // Arrange
        $guruData = $this->makeGuruData([
            'niy' => 'GR2024100',
            'nama' => 'Dr. Ahmad Fauzan, M.Pd.I',
            'pendidikan_terakhir' => 'S2',
            'program_studi' => 'Pendidikan Agama Islam',
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'email' => 'ahmad.fauzan@example.com',
        ]);

        // Act
        $guru = $this->guruService->register($guruData);

        // Assert
        $this->assertDatabaseHas('gurus', [
            'id' => $guru->id,
            'niy' => 'GR2024100',
            'nama' => 'Dr. Ahmad Fauzan, M.Pd.I',
            'pendidikan_terakhir' => 'S2',
            'program_studi' => 'Pendidikan Agama Islam',
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'email' => 'ahmad.fauzan@example.com',
            'status_aktif' => true,
        ]);

        // Verify default status
        $this->assertTrue($guru->status_aktif);
        $this->assertNull($guru->deleted_at);
    }

    /**
     * Test: Guru registration with minimal required data
     * Validates: Requirements 4.4, 4.5
     */
    public function test_guru_registration_minimal_data(): void
    {
        // Arrange: Only required fields
        $guruData = [
            'niy' => 'GR2024101',
            'nama' => 'Siti Aminah',
            'jenis_kelamin' => 'Perempuan',
            'tanggal_lahir' => '1985-05-15',
            'tempat_lahir' => 'Jakarta',
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'status_pernikahan' => 'Menikah',
            'pendidikan_terakhir' => 'SMA', // Below D1, no program_studi required
            'tahun_lulus' => '2005',
            'jabatan' => 'Tendik',
            'no_telepon' => '081234567890',
            'alamat_jalan' => 'Jl. Merdeka No. 1',
            'alamat_desa_kelurahan' => 'Menteng',
            'alamat_kecamatan' => 'Menteng',
            'alamat_kabupaten_kota' => 'Jakarta Pusat',
            'alamat_provinsi' => 'DKI Jakarta',
            'alamat_kode_pos' => '10310',
        ];

        // Act
        $guru = $this->guruService->register($guruData);

        // Assert
        $this->assertDatabaseHas('gurus', [
            'id' => $guru->id,
            'niy' => 'GR2024101',
            'nama' => 'Siti Aminah',
            'jabatan' => 'Tendik',
        ]);

        $this->assertTrue($guru->status_aktif);
        $this->assertNull($guru->program_studi); // Not required for SMA
    }
}
