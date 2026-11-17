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
use App\Models\ReferensiQuran;
use App\Services\MasterData\GuruService;
use App\Services\MasterData\KelompokService;
use App\Services\MasterData\SiswaService;
use App\Services\MasterData\TahunAjaranService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimestampManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    // ========================================================================
    // CREATED_AT TIMESTAMP TESTS
    // ========================================================================

    public function test_it_sets_created_at_when_tahun_ajaran_is_created()
    {
        $this->actingAs($this->admin, 'admin');

        $tahunAjaran = TahunAjaran::create([
            'label' => '2024/2025',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2025-06-30',
            'status' => true,
        ]);

        $this->assertNotNull($tahunAjaran->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $tahunAjaran->created_at);
    }

    public function test_it_sets_created_at_when_semester_is_created()
    {
        $this->actingAs($this->admin, 'admin');

        $tahunAjaran = TahunAjaran::factory()->create();

        $semester = Semester::create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jenis' => 'Ganjil',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif' => true,
        ]);

        $this->assertNotNull($semester->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $semester->created_at);
    }

    public function test_it_sets_created_at_when_siswa_is_created()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create();

        $this->assertNotNull($siswa->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $siswa->created_at);
    }

    public function test_it_sets_created_at_when_orang_tua_wali_is_created()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create();

        $orangTua = OrangTuaWali::create([
            'siswa_id' => $siswa->id,
            'jenis' => 'Ayah',
            'nama' => 'Budi Santoso',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1980-01-01',
            'pendidikan' => 'S1',
            'pekerjaan' => 'PNS',
            'penghasilan' => '5000000',
            'no_telepon' => '081234567890',
        ]);

        $this->assertNotNull($orangTua->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $orangTua->created_at);
    }

    public function test_it_sets_created_at_when_guru_is_created()
    {
        $this->actingAs($this->admin, 'admin');

        $guru = Guru::factory()->create();

        $this->assertNotNull($guru->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $guru->created_at);
    }

    public function test_it_sets_created_at_when_guru_tugas_tambahan_is_created()
    {
        $this->actingAs($this->admin, 'admin');

        $guru = Guru::factory()->create();

        $tugasTambahan = GuruTugasTambahan::create([
            'guru_id' => $guru->id,
            'jenis_tugas' => 'Wali Kelas',
        ]);

        $this->assertNotNull($tugasTambahan->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $tugasTambahan->created_at);
    }

    public function test_it_sets_created_at_when_kelompok_is_created()
    {
        $this->actingAs($this->admin, 'admin');

        $tahunAjaran = TahunAjaran::factory()->create();
        $guru = Guru::factory()->create(['jabatan' => 'Guru Pembimbing Tahfidz', 'status_aktif' => true]);

        $kelompok = Kelompok::create([
            'nama' => 'Kelompok A',
            'jenis' => 'Banin',
            'tingkat' => 'Dasar',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_min' => 6,
            'kapasitas_maks' => 12,
        ]);

        $this->assertNotNull($kelompok->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $kelompok->created_at);
    }

    // ========================================================================
    // UPDATED_AT TIMESTAMP TESTS
    // ========================================================================

    public function test_it_sets_updated_at_when_record_is_created()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create();

        $this->assertNotNull($siswa->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $siswa->updated_at);

        // On creation, created_at and updated_at should be the same
        $this->assertEquals($siswa->created_at->timestamp, $siswa->updated_at->timestamp);
    }

    public function test_it_updates_updated_at_when_tahun_ajaran_is_modified()
    {
        $this->actingAs($this->admin, 'admin');

        $tahunAjaran = TahunAjaran::factory()->create();
        $originalUpdatedAt = $tahunAjaran->updated_at;

        sleep(1); // Ensure different timestamp

        $tahunAjaran->label = '2025/2026';
        $tahunAjaran->save();

        $this->assertNotEquals($originalUpdatedAt->timestamp, $tahunAjaran->updated_at->timestamp);
        $this->assertTrue($tahunAjaran->updated_at->gt($originalUpdatedAt));
    }

    public function test_it_updates_updated_at_when_siswa_is_modified()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create(['nama' => 'Original Name']);
        $originalUpdatedAt = $siswa->updated_at;

        sleep(1);

        $siswa->nama = 'Updated Name';
        $siswa->save();

        $this->assertNotEquals($originalUpdatedAt->timestamp, $siswa->updated_at->timestamp);
        $this->assertTrue($siswa->updated_at->gt($originalUpdatedAt));
    }

    public function test_it_updates_updated_at_when_guru_is_modified()
    {
        $this->actingAs($this->admin, 'admin');

        $guru = Guru::factory()->create(['nama' => 'Original Name']);
        $originalUpdatedAt = $guru->updated_at;

        sleep(1);

        $guru->nama = 'Updated Name';
        $guru->save();

        $this->assertNotEquals($originalUpdatedAt->timestamp, $guru->updated_at->timestamp);
        $this->assertTrue($guru->updated_at->gt($originalUpdatedAt));
    }

    public function test_it_updates_updated_at_when_kelompok_is_modified()
    {
        $this->actingAs($this->admin, 'admin');

        $tahunAjaran = TahunAjaran::factory()->create();
        $guru = Guru::factory()->create(['jabatan' => 'Guru Pembimbing Tahfidz']);
        $kelompok = Kelompok::factory()->create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'guru_pembimbing_id' => $guru->id,
            'nama' => 'Original Name',
        ]);

        $originalUpdatedAt = $kelompok->updated_at;

        sleep(1);

        $kelompok->nama = 'Updated Name';
        $kelompok->save();

        $this->assertNotEquals($originalUpdatedAt->timestamp, $kelompok->updated_at->timestamp);
        $this->assertTrue($kelompok->updated_at->gt($originalUpdatedAt));
    }

    public function test_it_does_not_update_updated_at_when_no_changes_made()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create(['nama' => 'Test Name']);
        $originalUpdatedAt = $siswa->updated_at;

        sleep(1);

        // Save without changes
        $siswa->save();

        // updated_at should remain the same (Laravel's behavior)
        $this->assertEquals($originalUpdatedAt->timestamp, $siswa->updated_at->timestamp);
    }

    // ========================================================================
    // CREATED_AT IMMUTABILITY TESTS
    // ========================================================================

    public function test_it_does_not_change_created_at_when_record_is_updated()
    {
        $this->actingAs($this->admin, 'admin');

        $guru = Guru::factory()->create(['nama' => 'Original Name']);
        $originalCreatedAt = $guru->created_at;

        sleep(1);

        $guru->nama = 'Updated Name';
        $guru->save();

        // created_at should never change
        $this->assertEquals($originalCreatedAt->timestamp, $guru->created_at->timestamp);
    }

    public function test_it_preserves_created_at_across_multiple_updates()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create(['nama' => 'Name 1']);
        $originalCreatedAt = $siswa->created_at;

        sleep(1);
        $siswa->nama = 'Name 2';
        $siswa->save();

        sleep(1);
        $siswa->nama = 'Name 3';
        $siswa->save();

        sleep(1);
        $siswa->nama = 'Name 4';
        $siswa->save();

        // created_at should still be the original
        $this->assertEquals($originalCreatedAt->timestamp, $siswa->created_at->timestamp);
    }

    // ========================================================================
    // SERVICE LAYER TIMESTAMP TESTS
    // ========================================================================

    public function test_it_manages_timestamps_correctly_through_siswa_service()
    {
        $this->actingAs($this->admin, 'admin');

        $siswaService = app(SiswaService::class);

        $siswa = $siswaService->register([
            'nis' => '12345',
            'nisn' => '1234567890',
            'nama' => 'Test Student',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2010-01-01',
            'tempat_lahir' => 'Jakarta',
            'kewarganegaraan' => 'WNI',
            'agama' => 'Islam',
            'status_masuk' => 'Baru',
            'tanggal_masuk' => now()->format('Y-m-d'),
        ], [
            [
                'jenis' => 'Ayah',
                'nama' => 'Parent Name',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1980-01-01',
                'pendidikan' => 'S1',
                'pekerjaan' => 'PNS',
                'penghasilan' => '5000000',
                'no_telepon' => '081234567890',
            ]
        ]);

        $this->assertNotNull($siswa->created_at);
        $this->assertNotNull($siswa->updated_at);
    }

    public function test_it_manages_timestamps_correctly_through_guru_service()
    {
        $this->actingAs($this->admin, 'admin');

        $guruService = app(GuruService::class);

        $guru = $guruService->register([
            'niy' => '12345',
            'nama' => 'Test Teacher',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '1985-01-01',
            'tempat_lahir' => 'Jakarta',
            'kewarganegaraan' => 'WNI',
            'agama' => 'Islam',
            'status_pernikahan' => 'Menikah',
            'pendidikan_terakhir' => 'S1',
            'program_studi' => 'Pendidikan Agama Islam',
            'tahun_lulus' => '2008',
            'jabatan' => 'Guru Mapel',
            'no_telepon' => '081234567890',
        ]);

        $this->assertNotNull($guru->created_at);
        $this->assertNotNull($guru->updated_at);
    }

    public function test_it_manages_timestamps_correctly_through_kelompok_service()
    {
        $this->actingAs($this->admin, 'admin');

        $tahunAjaran = TahunAjaran::factory()->create();
        $guru = Guru::factory()->create(['jabatan' => 'Guru Pembimbing Tahfidz', 'status_aktif' => true]);

        $kelompokService = app(KelompokService::class);

        $kelompok = $kelompokService->create([
            'nama' => 'Kelompok Test',
            'jenis' => 'Banin',
            'tingkat' => 'Dasar',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'kapasitas_min' => 6,
            'kapasitas_maks' => 12,
        ]);

        $this->assertNotNull($kelompok->created_at);
        $this->assertNotNull($kelompok->updated_at);
    }

    // ========================================================================
    // MODELS WITHOUT TIMESTAMPS TESTS
    // ========================================================================

    public function test_it_does_not_set_timestamps_for_referensi_quran()
    {
        $this->actingAs($this->admin, 'admin');

        // ReferensiQuran has $timestamps = false
        $referensi = ReferensiQuran::create([
            'juz_ke' => 1,
            'urutan_hafalan' => 1,
            'awal_surah' => 'Al-Fatihah',
            'awal_ayat' => 1,
            'akhir_surah' => 'Al-Baqarah',
            'akhir_ayat' => 141,
            'jumlah_ayat' => 148,
        ]);

        // Should not have timestamps
        $this->assertObjectNotHasProperty('created_at', $referensi);
        $this->assertObjectNotHasProperty('updated_at', $referensi);
    }

    // ========================================================================
    // REQUIREMENT 9.10 COMPLIANCE TEST
    // ========================================================================

    public function test_it_satisfies_requirement_9_10_for_all_master_data()
    {
        $this->actingAs($this->admin, 'admin');

        // Test all master data entities
        $tahunAjaran = TahunAjaran::factory()->create();
        $semester = Semester::factory()->create(['tahun_ajaran_id' => $tahunAjaran->id]);
        $siswa = Siswa::factory()->create();
        $orangTua = OrangTuaWali::factory()->create(['siswa_id' => $siswa->id]);
        $guru = Guru::factory()->create();
        $tugasTambahan = GuruTugasTambahan::factory()->create(['guru_id' => $guru->id]);
        $kelompok = Kelompok::factory()->create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'guru_pembimbing_id' => $guru->id,
        ]);

        // All should have created_at and updated_at
        $entities = [
            $tahunAjaran,
            $semester,
            $siswa,
            $orangTua,
            $guru,
            $tugasTambahan,
            $kelompok,
        ];

        foreach ($entities as $entity) {
            $this->assertNotNull($entity->created_at, get_class($entity) . ' should have created_at');
            $this->assertNotNull($entity->updated_at, get_class($entity) . ' should have updated_at');
        }
    }

    // ========================================================================
    // TIMESTAMP FORMAT TESTS
    // ========================================================================

    public function test_it_stores_timestamps_in_correct_format()
    {
        $this->actingAs($this->admin, 'admin');

        $siswa = Siswa::factory()->create();

        // Timestamps should be Carbon instances
        $this->assertInstanceOf(\Carbon\Carbon::class, $siswa->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $siswa->updated_at);

        // Should be able to format timestamps
        $formattedCreated = $siswa->created_at->format('Y-m-d H:i:s');
        $formattedUpdated = $siswa->updated_at->format('Y-m-d H:i:s');

        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $formattedCreated);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $formattedUpdated);
    }
}
