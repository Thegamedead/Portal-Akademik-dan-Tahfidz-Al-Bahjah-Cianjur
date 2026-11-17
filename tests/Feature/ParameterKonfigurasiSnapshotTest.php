<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\ParameterKonfigurasi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TranskripCapaian;
use App\Models\ProgresHafalan;
use App\Models\Kelompok;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class ParameterKonfigurasiSnapshotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed necessary data
        $this->seed(\Database\Seeders\JuzSeeder::class);
    }

    public function test_it_creates_snapshot_when_target_kelulusan_changes()
    {
        // Create test data
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create(['tahun_ajaran_id' => $tahunAjaran->id]);
        $siswa = Siswa::factory()->create([
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
            'kelompok_id' => $kelompok->id,
        ]);

        // Create progres hafalan for the student
        $progresHafalan = ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 3,
        ]);

        // Get initial snapshot count
        $initialSnapshotCount = TranskripCapaian::count();

        // Update target_kelulusan
        $parameterKonfigurasi = ParameterKonfigurasi::getInstance();
        $oldValue = $parameterKonfigurasi->target_kelulusan;
        $parameterKonfigurasi->update([
            'target_kelulusan' => 10,
        ]);

        // Assert snapshot was created
        $this->assertEquals($initialSnapshotCount + 1, TranskripCapaian::count());

        // Assert snapshot has correct reason and data
        $snapshot = TranskripCapaian::where('siswa_id', $siswa->id)->latest()->first();
        $this->assertNotNull($snapshot);
        $this->assertEquals('target_kelulusan_change', $snapshot->reason);
        $this->assertIsInt($snapshot->total_juz_tuntas_snapshot);
        $this->assertEquals($oldValue, $snapshot->target_kelulusan_at_snapshot);
    }

    public function test_it_creates_snapshot_when_batas_tingkat_changes()
    {
        // Create test data
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create(['tahun_ajaran_id' => $tahunAjaran->id]);
        $siswa = Siswa::factory()->create([
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
            'kelompok_id' => $kelompok->id,
        ]);

        // Create progres hafalan for the student
        $progresHafalan = ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 5,
        ]);

        // Get initial snapshot count
        $initialSnapshotCount = TranskripCapaian::count();

        // Update batas_tingkat
        $parameterKonfigurasi = ParameterKonfigurasi::getInstance();
        $oldValue = $parameterKonfigurasi->batas_tingkat;
        $parameterKonfigurasi->update([
            'batas_tingkat' => [
                'Dasar' => ['juz_mulai' => 1, 'juz_selesai' => 8],
                'Menengah' => ['juz_mulai' => 9, 'juz_selesai' => 18],
                'Atas' => ['juz_mulai' => 19, 'juz_selesai' => 30],
            ],
        ]);

        // Assert snapshot was created
        $this->assertEquals($initialSnapshotCount + 1, TranskripCapaian::count());

        // Assert snapshot has correct reason and data
        $snapshot = TranskripCapaian::where('siswa_id', $siswa->id)->latest()->first();
        $this->assertNotNull($snapshot);
        $this->assertEquals('batas_tingkat_change', $snapshot->reason);
        $this->assertIsInt($snapshot->total_juz_tuntas_snapshot);
        $this->assertEquals($oldValue, $snapshot->batas_tingkat_at_snapshot);
    }

    public function test_it_creates_snapshot_for_multiple_active_students()
    {
        // Create test data
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create(['tahun_ajaran_id' => $tahunAjaran->id]);

        $siswa1 = Siswa::factory()->create([
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
            'kelompok_id' => $kelompok->id,
        ]);
        $siswa2 = Siswa::factory()->create([
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
            'kelompok_id' => $kelompok->id,
        ]);
        $siswa3 = Siswa::factory()->create([
            'status_akademik' => 'Lulus SMP', // Not active
            'status_penempatan' => 'Ditempatkan',
            'kelompok_id' => $kelompok->id,
        ]);

        // Create progres hafalan for students
        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 2,
        ]);
        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 4,
        ]);
        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa3->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 6,
        ]);

        // Get initial snapshot count
        $initialSnapshotCount = TranskripCapaian::count();

        // Update target_kelulusan
        $parameterKonfigurasi = ParameterKonfigurasi::getInstance();
        $parameterKonfigurasi->update([
            'target_kelulusan' => 8,
        ]);

        // Assert snapshots were created only for active students
        $this->assertEquals($initialSnapshotCount + 2, TranskripCapaian::count());

        // Assert snapshot for siswa1
        $snapshot1 = TranskripCapaian::where('siswa_id', $siswa1->id)->latest()->first();
        $this->assertNotNull($snapshot1);
        $this->assertEquals('target_kelulusan_change', $snapshot1->reason);

        // Assert snapshot for siswa2
        $snapshot2 = TranskripCapaian::where('siswa_id', $siswa2->id)->latest()->first();
        $this->assertNotNull($snapshot2);
        $this->assertEquals('target_kelulusan_change', $snapshot2->reason);

        // Assert NO snapshot for siswa3 (not active)
        $snapshot3 = TranskripCapaian::where('siswa_id', $siswa3->id)->count();
        $this->assertEquals(0, $snapshot3);
    }

    public function test_it_does_not_create_snapshot_when_other_parameters_change()
    {
        // Create test data
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create(['tahun_ajaran_id' => $tahunAjaran->id]);
        $siswa = Siswa::factory()->create([
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
            'kelompok_id' => $kelompok->id,
        ]);

        // Create progres hafalan for the student
        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 3,
        ]);

        // Get initial snapshot count
        $initialSnapshotCount = TranskripCapaian::count();

        // Update other parameters (not target_kelulusan or batas_tingkat)
        $parameterKonfigurasi = ParameterKonfigurasi::getInstance();
        $parameterKonfigurasi->update([
            'kapasitas_kelompok_min' => 8,
            'kapasitas_kelompok_max' => 15,
            'ambang_alfa_berturut' => 5,
        ]);

        // Assert NO snapshot was created
        $this->assertEquals($initialSnapshotCount, TranskripCapaian::count());
    }

    public function test_it_logs_snapshot_creation_to_activity_log()
    {
        // Create test data
        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);
        $kelompok = Kelompok::factory()->create(['tahun_ajaran_id' => $tahunAjaran->id]);
        $siswa = Siswa::factory()->create([
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
            'kelompok_id' => $kelompok->id,
        ]);

        // Create progres hafalan for the student
        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 3,
        ]);

        // Capture log messages
        Log::shouldReceive('info')
            ->with('Parameter target_kelulusan changing', \Mockery::type('array'))
            ->once();

        Log::shouldReceive('info')
            ->with('Snapshot created before target_kelulusan change', \Mockery::type('array'))
            ->once();

        // Update target_kelulusan
        $parameterKonfigurasi = ParameterKonfigurasi::getInstance();
        $parameterKonfigurasi->update([
            'target_kelulusan' => 7,
        ]);
    }
}
