<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\MasterData\SekolahService;
use App\Models\Sekolah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

class SekolahServiceTest extends TestCase
{
    use RefreshDatabase;

    private SekolahService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SekolahService();
    }

    /** @test */
    public function it_can_create_singleton_sekolah()
    {
        $data = [
            'nama' => 'SMP Islam Al-Azhar',
            'npsn' => '12345678',
            'alamat_jalan' => 'Jl. Raya No. 123',
            'alamat_provinsi' => 'Jawa Barat',
        ];

        $sekolah = $this->service->createOrUpdateSingleton($data);

        $this->assertInstanceOf(Sekolah::class, $sekolah);
        $this->assertEquals('SMP Islam Al-Azhar', $sekolah->nama);
        $this->assertDatabaseCount('sekolahs', 1);
    }

    /** @test */
    public function it_can_update_existing_singleton_sekolah()
    {
        // Create first
        $data1 = [
            'nama' => 'SMP Islam Al-Azhar',
            'npsn' => '12345678',
        ];
        $this->service->createOrUpdateSingleton($data1);

        // Update
        $data2 = [
            'nama' => 'SMP Islam Al-Azhar Updated',
            'npsn' => '87654321',
        ];
        $sekolah = $this->service->createOrUpdateSingleton($data2);

        $this->assertEquals('SMP Islam Al-Azhar Updated', $sekolah->nama);
        $this->assertEquals('87654321', $sekolah->npsn);
        $this->assertDatabaseCount('sekolahs', 1); // Still only 1 record
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->expectException(ValidationException::class);

        $this->service->createOrUpdateSingleton([
            // Missing required 'nama'
            'npsn' => '12345678',
        ]);
    }

    /** @test */
    public function it_can_get_singleton_sekolah()
    {
        Sekolah::create([
            'nama' => 'SMP Islam Al-Azhar',
            'npsn' => '12345678',
        ]);

        $sekolah = $this->service->getSingleton();

        $this->assertInstanceOf(Sekolah::class, $sekolah);
        $this->assertEquals('SMP Islam Al-Azhar', $sekolah->nama);
    }

    /** @test */
    public function it_returns_null_when_no_sekolah_exists()
    {
        $sekolah = $this->service->getSingleton();

        $this->assertNull($sekolah);
    }
}
