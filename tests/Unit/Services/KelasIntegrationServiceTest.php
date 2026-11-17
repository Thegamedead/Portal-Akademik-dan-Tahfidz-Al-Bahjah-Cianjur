<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\MasterData\KelasIntegrationService;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KelasIntegrationServiceTest extends TestCase
{
    use RefreshDatabase;

    private KelasIntegrationService $service;
    private Kelas $kelas;
    private Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KelasIntegrationService();

        $tahunAjaran = TahunAjaran::create([
            'label' => '2026/2027',
            'tahun_mulai' => 2026,
            'tahun_selesai' => 2027,
            'status' => true,
        ]);

        $this->kelas = Kelas::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'tingkat' => '7',
            'rombel' => 'A',
            'kapasitas' => 30,
        ]);

        $this->siswa = Siswa::create([
            'nis' => '12345',
            'nama' => 'Test Siswa',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2010-01-01',
            'tempat_lahir' => 'Jakarta',
            'kewarganegaraan' => 'WNI',
            'agama' => 'Islam',
            'status_masuk' => 'Baru',
            'tanggal_masuk' => now(),
        ]);
    }

    /** @test */
    public function it_can_assign_siswa_to_kelas()
    {
        $result = $this->service->assignSiswaToKelas($this->siswa->id, $this->kelas->id);

        $this->assertTrue($result);
        $this->assertEquals($this->kelas->id, $this->siswa->fresh()->kelas_id);
    }

    /** @test */
    public function it_can_remove_siswa_from_kelas()
    {
        // Assign first
        $this->siswa->update(['kelas_id' => $this->kelas->id]);

        $result = $this->service->removeSiswaFromKelas($this->siswa->id);

        $this->assertTrue($result);
        $this->assertNull($this->siswa->fresh()->kelas_id);
    }

    /** @test */
    public function it_can_get_available_capacity()
    {
        // Kelas capacity = 30, no siswa yet
        $capacity = $this->service->getAvailableCapacity($this->kelas->id);

        $this->assertEquals(30, $capacity);

        // Add 2 siswa
        $this->siswa->update(['kelas_id' => $this->kelas->id]);

        $siswa2 = Siswa::create([
            'nis' => '12346',
            'nama' => 'Test Siswa 2',
            'jenis_kelamin' => 'Perempuan',
            'tanggal_lahir' => '2010-01-02',
            'tempat_lahir' => 'Bandung',
            'kewarganegaraan' => 'WNI',
            'agama' => 'Islam',
            'status_masuk' => 'Baru',
            'tanggal_masuk' => now(),
            'kelas_id' => $this->kelas->id,
        ]);

        $capacity = $this->service->getAvailableCapacity($this->kelas->id);

        $this->assertEquals(28, $capacity); // 30 - 2
    }
}
