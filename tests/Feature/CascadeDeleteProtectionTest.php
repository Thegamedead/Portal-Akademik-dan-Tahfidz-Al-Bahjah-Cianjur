<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\GuruTugasTambahan;
use App\Models\Kelompok;
use App\Models\OrangTuaWali;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\MasterData\GuruService;
use App\Services\MasterData\KelompokService;
use App\Services\MasterData\SiswaService;
use App\Services\MasterData\TahunAjaranService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CascadeDeleteProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    /** @test */
    public function it_prevents_deleting_tahun_ajaran_with_kelompok()
    {
        $this->actingAs($this->admin, 'admin');

        $tahunAjaran = TahunAjaran::factory()->create();
        $guru = Guru::factory()->create(['jabatan' => 'Guru Pembimbing Tahfidz', 'status_aktif' => true]);

        // Create kelompok linked to tahun ajaran
        Kelompok::factory()->create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'guru_pembimbing_id' => $guru->id,
        ]);

        $tahunAjaranService = app(TahunAjaranService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tidak dapat menghapus Tahun Ajaran ini karena masih memiliki data terkait');

        $tahunAjaranService->delete($tahunAjaran->id);
    }

    /** @test */
    public function it_allows_deleting_tahun_ajaran_without_dependencies()
    {
        $this->actingAs($this->admin, 'admin');

        $tahunAjaran = TahunAjaran::factory()->create();
        $semester = Semester::factory()->create(['tahun_ajaran_id' => $tahunAjaran->id]);

        $tahunAjaranService = app(TahunAjaranService::class);

        $result = $tahunAjaranService->delete($tahunAjaran->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('tahun_ajarans', ['id' => $tahunAjaran->id]);
        $this->assertDatabaseMissing('semesters', ['id' => $semester->id]); // Cascade deleted
    }

    /** @test */
    public function it_prevents_deleting_kelompok_with_siswa()
    {
        $this->actingAs($this->admin, 'admin');

        $tahunAjaran = TahunAjaran::factory()->create();
        $guru = Guru::factory()->create(['jabatan' => 'Guru Pembimbing Tahfidz', 'status_aktif' => true]);
        $kelompok = Kelompok::factory()->create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'guru_pembimbing_id' => $guru->id,
        ]);

        // Create siswa assigned to kelompok
        Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        $kelompokService = app(KelompokService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tidak dapat menghapus Kelompok ini karena masih memiliki data terkait');

        $kelompokService->delete($kelompok->id);
    }

    /** @test */
    public function it_allows_deleting_kelompok_without_siswa()
    {
        $this->actingAs($this->admin, 'admin');

        $tahunAjaran = TahunAjaran::factory()->create();
        $guru = Guru::factory()->create(['jabatan' => 'Guru Pembimbing Tahfidz', 'status_aktif' => true]);
        $kelompok = Kelompok::factory()->create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'guru_pembimbing_id' => $guru->id,
        ]);

        $kelompokService = app(KelompokService::class);

        $result = $kelompokService->delete($kelompok->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('kelompoks', ['id' => $kelompok->id]);
    }

    /** @test */
    public function it_prevents_deleting_guru_with_kelompok()
    {
        $this->actingAs($this->admin, 'admin');

        $tahunAjaran = TahunAjaran::factory()->create();
        $guru = Guru::factory()->create(['jabatan' => 'Guru Pembimbing Tahfidz', 'status_aktif' => true]);

        // Create kelompok with this guru as pembimbing
        Kelompok::factory()->create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'guru_pembimbing_id' => $guru->id,
        ]);

        $guruService = app(GuruService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tidak dapat menghapus Guru ini karena masih memiliki data terkait');

        $guruService->delete($guru->id);
    }

    /** @test */
    public function it_allows_deleting_guru_without_dependencies()
    {
        $this->actingAs($this->admin, 'admin');

        $guru = Guru::factory()->create(['jabatan' => 'Guru Mapel', 'status_aktif' => true]);
        $tugasTambahan = GuruTugasTambahan::factory()->create(['guru_id' => $guru->id]);

        $guruService = app(GuruService::class);

        $result = $guruService->delete($guru->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('gurus', ['id' => $guru->id]);
        $this->assertDatabaseMissing('guru_tugas_tambahans', ['id' => $tugasTambahan->id]); // Cascade deleted
    }

    /** @test */
    public function it_prevents_deleting_siswa_with_kelompok_assignment()
    {
        $this->actingAs($this->admin, 'admin');

        $tahunAjaran = TahunAjaran::factory()->create();
        $guru = Guru::factory()->create(['jabatan' => 'Guru Pembimbing Tahfidz', 'status_aktif' => true]);
        $kelompok = Kelompok::factory()->create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'guru_pembimbing_id' => $guru->id,
        ]);

        $siswa = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        $siswaService = app(SiswaService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tidak dapat menghapus Siswa ini karena masih terdaftar dalam kelompok');

        $siswaService->delete($siswa->id);
    }

    /** @test */
    public function it_allows_deleting_siswa_without_dependencies()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create(['kelompok_id' => null]);
        $orangTua = OrangTuaWali::factory()->create(['siswa_id' => $siswa->id]);

        $siswaService = app(SiswaService::class);

        $result = $siswaService->delete($siswa->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('siswas', ['id' => $siswa->id]);
        $this->assertDatabaseMissing('orang_tua_walis', ['id' => $orangTua->id]); // Cascade deleted
    }

    /** @test */
    public function it_cascade_deletes_semesters_when_deleting_tahun_ajaran()
    {
        $this->actingAs($this->admin, 'admin');

        $tahunAjaran = TahunAjaran::factory()->create();
        $semester1 = Semester::factory()->create(['tahun_ajaran_id' => $tahunAjaran->id, 'jenis' => 'Ganjil']);
        $semester2 = Semester::factory()->create(['tahun_ajaran_id' => $tahunAjaran->id, 'jenis' => 'Genap']);

        $tahunAjaranService = app(TahunAjaranService::class);

        $tahunAjaranService->delete($tahunAjaran->id);

        $this->assertDatabaseMissing('semesters', ['id' => $semester1->id]);
        $this->assertDatabaseMissing('semesters', ['id' => $semester2->id]);
    }

    /** @test */
    public function it_cascade_deletes_orang_tua_wali_when_deleting_siswa()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create(['kelompok_id' => null]);
        $ayah = OrangTuaWali::factory()->create(['siswa_id' => $siswa->id, 'jenis' => 'Ayah']);
        $ibu = OrangTuaWali::factory()->create(['siswa_id' => $siswa->id, 'jenis' => 'Ibu']);

        $siswaService = app(SiswaService::class);

        $siswaService->delete($siswa->id);

        $this->assertDatabaseMissing('orang_tua_walis', ['id' => $ayah->id]);
        $this->assertDatabaseMissing('orang_tua_walis', ['id' => $ibu->id]);
    }

    /** @test */
    public function it_provides_detailed_error_message_with_dependency_count()
    {
        $this->actingAs($this->admin, 'admin');

        $tahunAjaran = TahunAjaran::factory()->create();
        $guru = Guru::factory()->create(['jabatan' => 'Guru Pembimbing Tahfidz', 'status_aktif' => true]);

        // Create multiple kelompok
        Kelompok::factory()->count(3)->create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'guru_pembimbing_id' => $guru->id,
        ]);

        // Create semesters
        Semester::factory()->count(2)->create(['tahun_ajaran_id' => $tahunAjaran->id]);

        $tahunAjaranService = app(TahunAjaranService::class);

        try {
            $tahunAjaranService->delete($tahunAjaran->id);
            $this->fail('Expected exception was not thrown');
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $this->assertStringContainsString('3 kelompok', $message);
            $this->assertStringContainsString('2 semester', $message);
        }
    }
}
