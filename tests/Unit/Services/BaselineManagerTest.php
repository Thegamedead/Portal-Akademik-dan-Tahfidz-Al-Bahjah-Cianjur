<?php

namespace Tests\Unit\Services;

use App\Models\Admin;
use App\Models\Juz;
use App\Models\ProgresHafalan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\BaselineManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Unit Tests for BaselineManager Service
 *
 * Task 10: Checkpoint verification
 */
class BaselineManagerTest extends TestCase
{
    use RefreshDatabase;

    protected BaselineManager $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(BaselineManager::class);
    }

    public function test_it_can_store_baseline_from_placement_test()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $juzs = Juz::factory()->count(3)->create();

        $baselineData = [
            'juzs_covered' => $juzs->pluck('id')->toArray(),
            'surahs_covered' => 'Al-Fatihah, Al-Baqarah',
            'catatan_kualitas' => 'Hafalan lancar',
            'rekomendasi_tingkat' => 'Menengah',
        ];

        // Act
        $result = $this->service->storeBaseline($siswa->id, $tahunAjaran->id, $baselineData);

        // Assert
        $this->assertInstanceOf(ProgresHafalan::class, $result);
        $this->assertEquals($siswa->id, $result->siswa_id);
        $this->assertEquals($tahunAjaran->id, $result->tahun_ajaran_id);
        $this->assertEquals($baselineData['juzs_covered'], $result->baseline_juzs);
        $this->assertEquals(3, $result->total_juz_tuntas);

        $this->assertDatabaseHas('progres_hafalans', [
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'baseline_surahs' => 'Al-Fatihah, Al-Baqarah',
        ]);
    }

    public function test_it_validates_juz_ids_exist_in_referensi_quran()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();

        $baselineData = [
            'juzs_covered' => [999, 1000], // Invalid IDs
            'surahs_covered' => 'Test',
        ];

        // Act & Assert
        $this->expectException(ValidationException::class);
        $this->service->storeBaseline($siswa->id, $tahunAjaran->id, $baselineData);
    }

    public function test_it_can_update_baseline_by_admin()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $admin = Admin::factory()->create();
        $juzs = Juz::factory()->count(5)->create();

        $initial = $this->service->storeBaseline($siswa->id, $tahunAjaran->id, [
            'juzs_covered' => $juzs->take(2)->pluck('id')->toArray(),
            'surahs_covered' => 'Initial',
        ]);

        $modifiedData = [
            'juzs_covered' => $juzs->take(4)->pluck('id')->toArray(),
            'surahs_covered' => 'Modified',
            'catatan_kualitas' => 'Updated quality notes',
        ];

        // Act
        $result = $this->service->updateBaseline($initial->id, $modifiedData, $admin->id);

        // Assert
        $this->assertEquals($admin->id, $result->baseline_modified_by);
        $this->assertNotNull($result->baseline_modified_at);
        $this->assertEquals($modifiedData['juzs_covered'], $result->baseline_juzs);
        $this->assertEquals('Modified', $result->baseline_surahs);
    }

    public function test_it_can_get_baseline_for_student()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $juzs = Juz::factory()->count(2)->create();

        $this->service->storeBaseline($siswa->id, $tahunAjaran->id, [
            'juzs_covered' => $juzs->pluck('id')->toArray(),
            'surahs_covered' => 'Test surahs',
        ]);

        // Act
        $result = $this->service->getBaseline($siswa->id, $tahunAjaran->id);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($siswa->id, $result->siswa_id);
        $this->assertCount(2, $result->baseline_juzs);
    }

    public function test_it_returns_null_when_no_baseline_exists()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();

        // Act
        $result = $this->service->getBaseline($siswa->id, $tahunAjaran->id);

        // Assert
        $this->assertNull($result);
    }

    public function test_it_can_get_baseline_audit_log()
    {
        // Arrange
        $siswa = Siswa::factory()->create();
        $tahunAjaran = TahunAjaran::factory()->create();
        $admin = Admin::factory()->create();
        $juzs = Juz::factory()->count(3)->create();

        $progresHafalan = $this->service->storeBaseline($siswa->id, $tahunAjaran->id, [
            'juzs_covered' => $juzs->take(1)->pluck('id')->toArray(),
        ]);

        $this->service->updateBaseline($progresHafalan->id, [
            'juzs_covered' => $juzs->take(2)->pluck('id')->toArray(),
        ], $admin->id);

        // Act
        $auditLog = $this->service->getBaselineAuditLog($progresHafalan->id);

        // Assert
        $this->assertNotNull($auditLog);
        $this->assertGreaterThan(0, $auditLog->count());
    }
}
