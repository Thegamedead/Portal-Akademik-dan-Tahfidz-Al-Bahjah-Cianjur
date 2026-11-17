<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\MasterData\KelasService;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

class KelasServiceBulkCreateTest extends TestCase
{
    use RefreshDatabase;

    private KelasService $service;
    private TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KelasService();

        $this->tahunAjaran = TahunAjaran::create([
            'label' => '2026/2027',
            'tahun_mulai' => 2026,
            'tahun_selesai' => 2027,
            'status' => true,
        ]);
    }

    /** @test */
    public function it_can_bulk_create_multiple_kelas()
    {
        $data = [
            [
                'tahun_ajaran_id' => $this->tahunAjaran->id,
                'tingkat' => '7',
                'rombel' => 'A',
                'kapasitas' => 30,
            ],
            [
                'tahun_ajaran_id' => $this->tahunAjaran->id,
                'tingkat' => '7',
                'rombel' => 'B',
                'kapasitas' => 30,
            ],
            [
                'tahun_ajaran_id' => $this->tahunAjaran->id,
                'tingkat' => '7',
                'rombel' => 'C',
                'kapasitas' => 30,
            ],
        ];

        $result = $this->service->bulkCreate($data);

        $this->assertCount(3, $result);
        $this->assertDatabaseCount('kelas', 3);
    }

    /** @test */
    public function it_rolls_back_on_duplicate_in_bulk_create()
    {
        // Create existing kelas
        Kelas::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'tingkat' => '7',
            'rombel' => 'B',
            'kapasitas' => 30,
        ]);

        $data = [
            [
                'tahun_ajaran_id' => $this->tahunAjaran->id,
                'tingkat' => '7',
                'rombel' => 'A',
                'kapasitas' => 30,
            ],
            [
                'tahun_ajaran_id' => $this->tahunAjaran->id,
                'tingkat' => '7',
                'rombel' => 'B', // Duplicate!
                'kapasitas' => 30,
            ],
        ];

        $this->expectException(ValidationException::class);
        $this->service->bulkCreate($data);

        // Should still only have 1 record (7B), not 2
        $this->assertDatabaseCount('kelas', 1);
    }

    /** @test */
    public function it_validates_all_items_in_bulk_create()
    {
        $data = [
            [
                'tahun_ajaran_id' => $this->tahunAjaran->id,
                'tingkat' => '7',
                'rombel' => 'A',
                'kapasitas' => 30,
            ],
            [
                // Missing required tingkat
                'tahun_ajaran_id' => $this->tahunAjaran->id,
                'rombel' => 'B',
                'kapasitas' => 30,
            ],
        ];

        $this->expectException(ValidationException::class);
        $this->service->bulkCreate($data);

        $this->assertDatabaseCount('kelas', 0);
    }
}
