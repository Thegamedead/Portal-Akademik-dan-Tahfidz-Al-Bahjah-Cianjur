<?php

namespace Tests\Unit\Services;

use App\Models\Absensi;
use App\Models\Juz;
use App\Models\Kelompok;
use App\Models\ProgresHafalan;
use App\Models\Setoran;
use App\Models\Siswa;
use App\Models\Surah;
use App\Models\TahunAjaran;
use App\Services\ProgresHafalanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit Tests for getDashboardDataForSiswa method
 *
 * Task 13.1: Implement getDashboardDataForSiswa() method in ProgresHafalanService
 */
class GetDashboardDataForSiswaTest extends TestCase
{
    use RefreshDatabase;

    protected ProgresHafalanService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ProgresHafalanService::class);
    }

    public function test_getDashboardDataForSiswa_returns_complete_dashboard_data_for_authenticated_siswa(): void
    {
        // Arrange: Create siswa with kelompok and tahun ajaran
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create();
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        // Create juz and surah
        $juz1 = Juz::factory()->create(['juz_ke' => 1, 'jumlah_ayat' => 148]);
        $juz2 = Juz::factory()->create(['juz_ke' => 2, 'jumlah_ayat' => 111]);
        $surah = Surah::factory()->create(['jumlah_ayat' => 148]);

        // Create some absensi and setoran records for recent history
        for ($i = 0; $i < 5; $i++) {
            $absensi = Absensi::factory()->create([
                'siswa_id' => $siswa->id,
                'kelompok_id' => $kelompok->id,
                'tanggal' => now()->subDays($i),
                'status_kehadiran' => 'Hadir',
            ]);

            Setoran::factory()->create([
                'absensi_id' => $absensi->id,
                'juz_id' => $juz2->id,
                'surah_id' => $surah->id,
                'ayat_mulai' => 1 + ($i * 10),
                'ayat_selesai' => 10 + ($i * 10),
                'jenis_setoran' => 'Hafalan Baru',
                'keputusan' => 'Lanjut',
                'predikat' => 'Lancar',
            ]);
        }

        // Act: Call getDashboardDataForSiswa
        $result = $this->service->getDashboardDataForSiswa($siswa->id);

        // Assert: Verify structure
        $this->assertArrayHasKey('siswa', $result);
        $this->assertArrayHasKey('progres', $result);
        $this->assertArrayHasKey('capaian_juzs', $result);
        $this->assertArrayHasKey('recent_setoran', $result);

        // Verify siswa data
        $this->assertEquals($siswa->id, $result['siswa']->id);

        // Verify progres data
        $this->assertArrayHasKey('total_juz_tuntas', $result['progres']);
        $this->assertArrayHasKey('target_kelulusan', $result['progres']);
        $this->assertEquals(5, $result['progres']['target_kelulusan']);

        // Verify recent setoran (should have 5 entries)
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result['recent_setoran']);
        $this->assertCount(5, $result['recent_setoran']);

        // Verify capaian_juzs is loaded
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result['capaian_juzs']);
    }

    public function test_getDashboardDataForSiswa_returns_empty_data_when_no_active_tahun_ajaran(): void
    {
        // Arrange: Create siswa without active tahun ajaran
        $kelompok = Kelompok::factory()->create();
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        // Act: Call getDashboardDataForSiswa
        $result = $this->service->getDashboardDataForSiswa($siswa->id);

        // Assert: Verify empty data structure
        $this->assertEquals(0, $result['progres']['total_juz_tuntas']);
        $this->assertNull($result['progres']['juz_berjalan']);
        $this->assertTrue($result['capaian_juzs']->isEmpty());
        $this->assertTrue($result['recent_setoran']->isEmpty());
    }

    public function test_getDashboardDataForSiswa_returns_last_10_setoran_entries(): void
    {
        // Arrange: Create siswa with more than 10 setoran
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create();
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        $juz = Juz::factory()->create(['juz_ke' => 1, 'jumlah_ayat' => 148]);
        $surah = Surah::factory()->create(['jumlah_ayat' => 148]);

        // Create 15 setoran records
        for ($i = 0; $i < 15; $i++) {
            $absensi = Absensi::factory()->create([
                'siswa_id' => $siswa->id,
                'kelompok_id' => $kelompok->id,
                'tanggal' => now()->subDays(14 - $i),
                'status_kehadiran' => 'Hadir',
            ]);

            Setoran::factory()->create([
                'absensi_id' => $absensi->id,
                'juz_id' => $juz->id,
                'surah_id' => $surah->id,
                'ayat_mulai' => 1,
                'ayat_selesai' => 10,
                'jenis_setoran' => 'Hafalan Baru',
                'keputusan' => 'Lanjut',
                'predikat' => 'Lancar',
            ]);
        }

        // Act: Call getDashboardDataForSiswa
        $result = $this->service->getDashboardDataForSiswa($siswa->id);

        // Assert: Should return only last 10 entries
        $this->assertCount(10, $result['recent_setoran']);
    }
}
