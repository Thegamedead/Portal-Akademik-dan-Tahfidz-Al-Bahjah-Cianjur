<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\MasterData\KelasService;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

class KelasServiceTest extends TestCase
{
    use RefreshDatabase;

    private KelasService $service;
    private TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KelasService();

        // Create tahun ajaran for FK
        $this->tahunAjaran = TahunAjaran::create([
            'label' => '2026/2027',
            'tahun_mulai' => 2026,
            'tahun_selesai' => 2027,
            'status' => true,
        ]);
    }

    /** @test */
    public function it_can_create_kelas()
    {
        $data = [
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'tingkat' => '7',
            'rombel' => 'A',
            'kapasitas' => 30,
        ];

        $kelas = $this->service->create($data);

        $this->assertInstanceOf(Kelas::class, $kelas);
        $this->assertEquals('7', $kelas->tingkat);
        $this->assertEquals('A', $kelas->rombel);
        $this->assertEquals('7A', $kelas->nama_lengkap);
    }

    /** @test */
    public function it_prevents_duplicate_kelas()
    {
        $data = [
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'tingkat' => '7',
            'rombel' => 'A',
            'kapasitas' => 30,
        ];

        $this->service->create($data);

        $this->expectException(ValidationException::class);
        $this->service->create($data); // Duplicate
    }

    /** @test */
    public function it_can_update_kelas()
    {
        $kelas = Kelas::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'tingkat' => '7',
            'rombel' => 'A',
            'kapasitas' => 30,
        ]);

        $updated = $this->service->update($kelas->id, [
            'kapasitas' => 35,
        ]);

        $this->assertEquals(35, $updated->kapasitas);
    }

    /** @test */
    public function it_can_delete_kelas()
    {
        $kelas = Kelas::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'tingkat' => '7',
            'rombel' => 'A',
            'kapasitas' => 30,
        ]);

        $this->service->delete($kelas->id);

        $this->assertDatabaseMissing('kelas', ['id' => $kelas->id]);
    }
}
