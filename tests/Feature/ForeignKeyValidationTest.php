<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Kelompok;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\MasterData\KelompokService;
use App\Services\MasterData\SemesterService;
use App\Services\MasterData\SiswaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ForeignKeyValidationTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected TahunAjaran $tahunAjaran;
    protected Guru $guruPembimbing;
    protected Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin for authentication
        $this->admin = Admin::factory()->create();

        // Create tahun ajaran
        $this->tahunAjaran = TahunAjaran::factory()->create([
            'label' => '2024/2025',
            'status' => true,
        ]);

        // Create guru pembimbing tahfidz
        $this->guruPembimbing = Guru::factory()->create([
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'status_aktif' => true,
        ]);

        // Create siswa
        $this->siswa = Siswa::factory()->create([
            'status_akademik' => 'Aktif',
        ]);
    }

    /** @test */
    public function it_validates_guru_pembimbing_exists_when_creating_kelompok()
    {
        $this->actingAs($this->admin, 'admin');

        $kelompokService = app(KelompokService::class);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Guru yang dipilih tidak ditemukan');

        $kelompokService->create([
            'nama' => 'Kelompok Test',
            'jenis' => 'Banin',
            'tingkat' => 'Dasar',
            'guru_pembimbing_id' => 99999, // Non-existent ID
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'kapasitas_min' => 6,
            'kapasitas_maks' => 12,
        ]);
    }

    /** @test */
    public function it_validates_guru_pembimbing_has_correct_jabatan()
    {
        $this->actingAs($this->admin, 'admin');

        // Create guru with different jabatan
        $guruMapel = Guru::factory()->create([
            'jabatan' => 'Guru Mapel',
            'status_aktif' => true,
        ]);

        $kelompokService = app(KelompokService::class);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Guru yang dipilih harus memiliki jabatan Guru Pembimbing Tahfidz');

        $kelompokService->create([
            'nama' => 'Kelompok Test',
            'jenis' => 'Banin',
            'tingkat' => 'Dasar',
            'guru_pembimbing_id' => $guruMapel->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'kapasitas_min' => 6,
            'kapasitas_maks' => 12,
        ]);
    }

    /** @test */
    public function it_validates_guru_pembimbing_is_active()
    {
        $this->actingAs($this->admin, 'admin');

        // Create inactive guru pembimbing
        $guruInactive = Guru::factory()->create([
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'status_aktif' => false,
        ]);

        $kelompokService = app(KelompokService::class);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Guru yang dipilih sudah tidak aktif');

        $kelompokService->create([
            'nama' => 'Kelompok Test',
            'jenis' => 'Banin',
            'tingkat' => 'Dasar',
            'guru_pembimbing_id' => $guruInactive->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'kapasitas_min' => 6,
            'kapasitas_maks' => 12,
        ]);
    }

    /** @test */
    public function it_validates_tahun_ajaran_exists_when_creating_kelompok()
    {
        $this->actingAs($this->admin, 'admin');

        $kelompokService = app(KelompokService::class);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Tahun ajaran yang dipilih tidak ditemukan');

        $kelompokService->create([
            'nama' => 'Kelompok Test',
            'jenis' => 'Banin',
            'tingkat' => 'Dasar',
            'guru_pembimbing_id' => $this->guruPembimbing->id,
            'tahun_ajaran_id' => 99999, // Non-existent ID
            'kapasitas_min' => 6,
            'kapasitas_maks' => 12,
        ]);
    }

    /** @test */
    public function it_validates_tahun_ajaran_exists_when_creating_semester()
    {
        $this->actingAs($this->admin, 'admin');

        $semesterService = app(SemesterService::class);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Tahun ajaran yang dipilih tidak ditemukan');

        $semesterService->create([
            'tahun_ajaran_id' => 99999, // Non-existent ID
            'jenis' => 'Ganjil',
            'tanggal_mulai' => '2024-08-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif' => true,
        ]);
    }

    /** @test */
    public function it_validates_siswa_exists_when_assigning_to_kelompok()
    {
        $this->actingAs($this->admin, 'admin');

        // Create kelompok
        $kelompok = Kelompok::factory()->create([
            'guru_pembimbing_id' => $this->guruPembimbing->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
        ]);

        $siswaService = app(SiswaService::class);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Siswa yang dipilih tidak ditemukan');

        $siswaService->assignToKelompok(99999, $kelompok->id); // Non-existent siswa ID
    }

    /** @test */
    public function it_validates_kelompok_exists_when_assigning_siswa()
    {
        $this->actingAs($this->admin, 'admin');

        $siswaService = app(SiswaService::class);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Kelompok yang dipilih tidak ditemukan');

        $siswaService->assignToKelompok($this->siswa->id, 99999); // Non-existent kelompok ID
    }

    /** @test */
    public function it_validates_kelompok_capacity_when_assigning_siswa()
    {
        $this->actingAs($this->admin, 'admin');

        // Create kelompok with capacity 1
        $kelompok = Kelompok::factory()->create([
            'guru_pembimbing_id' => $this->guruPembimbing->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'kapasitas_maks' => 1,
        ]);

        // Create and assign first siswa to fill capacity
        $siswa1 = Siswa::factory()->create(['status_akademik' => 'Aktif']);
        $siswa2 = Siswa::factory()->create(['status_akademik' => 'Aktif']);

        $siswaService = app(SiswaService::class);

        // First assignment should succeed
        $siswaService->assignToKelompok($siswa1->id, $kelompok->id);

        // Second assignment should fail due to capacity
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Kelompok sudah penuh');

        $siswaService->assignToKelompok($siswa2->id, $kelompok->id);
    }

    /** @test */
    public function it_validates_siswa_is_active_when_assigning_to_kelompok()
    {
        $this->actingAs($this->admin, 'admin');

        // Create inactive siswa
        $siswaInactive = Siswa::factory()->create([
            'status_akademik' => 'Lulus SMP',
        ]);

        // Create kelompok
        $kelompok = Kelompok::factory()->create([
            'guru_pembimbing_id' => $this->guruPembimbing->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
        ]);

        $siswaService = app(SiswaService::class);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Siswa yang dipilih tidak aktif atau sudah lulus/pindah/keluar');

        $siswaService->assignToKelompok($siswaInactive->id, $kelompok->id);
    }

    /** @test */
    public function it_successfully_creates_kelompok_with_valid_foreign_keys()
    {
        $this->actingAs($this->admin, 'admin');

        $kelompokService = app(KelompokService::class);

        $kelompok = $kelompokService->create([
            'nama' => 'Kelompok Valid',
            'jenis' => 'Banin',
            'tingkat' => 'Dasar',
            'guru_pembimbing_id' => $this->guruPembimbing->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'kapasitas_min' => 6,
            'kapasitas_maks' => 12,
        ]);

        $this->assertDatabaseHas('kelompoks', [
            'id' => $kelompok->id,
            'nama' => 'Kelompok Valid',
            'guru_pembimbing_id' => $this->guruPembimbing->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
        ]);
    }

    /** @test */
    public function it_successfully_assigns_siswa_to_kelompok_with_valid_references()
    {
        $this->actingAs($this->admin, 'admin');

        // Create kelompok
        $kelompok = Kelompok::factory()->create([
            'guru_pembimbing_id' => $this->guruPembimbing->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
        ]);

        $siswaService = app(SiswaService::class);

        $updatedSiswa = $siswaService->assignToKelompok($this->siswa->id, $kelompok->id);

        $this->assertEquals($kelompok->id, $updatedSiswa->kelompok_id);
        $this->assertEquals('Ditempatkan', $updatedSiswa->status_penempatan);
    }
}
