<?php

namespace Tests\Feature\MasterData;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\GuruTugasTambahan;
use App\Models\Kelompok;
use App\Models\OrangTuaWali;
use App\Models\ReferensiQuran;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\MasterData\GuruService;
use App\Services\MasterData\KelompokService;
use App\Services\MasterData\SemesterService;
use App\Services\MasterData\SiswaService;
use App\Services\MasterData\TahunAjaranService;
use Database\Seeders\JuzSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Correctness Properties Test Suite
 *
 * Verifies all invariants documented in the design document for the
 * Master Data Tahfidz module. Each test method maps to a named property.
 *
 * Run with: php artisan test --filter CorrectnessPropertiesTest
 */
class CorrectnessPropertiesTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
        $this->actingAs($this->admin, 'admin');
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function makeGuruPembimbing(array $overrides = []): Guru
    {
        return Guru::factory()->create(array_merge([
            'jabatan'    => 'Guru Pembimbing Tahfidz',
            'status_aktif' => true,
        ], $overrides));
    }

    private function makeTahunAjaran(array $overrides = []): TahunAjaran
    {
        return TahunAjaran::factory()->create(array_merge([
            'tanggal_mulai'  => '2024-07-01',
            'tanggal_selesai'=> '2025-06-30',
            'status'         => false,
        ], $overrides));
    }

    private function makeKelompok(TahunAjaran $ta, Guru $guru, array $overrides = []): Kelompok
    {
        return Kelompok::factory()->create(array_merge([
            'tahun_ajaran_id'    => $ta->id,
            'guru_pembimbing_id' => $guru->id,
        ], $overrides));
    }

    private function orangTuaData(string $jenis, string $suffix = ''): array
    {
        return [
            'jenis'        => $jenis,
            'nama'         => "Orang Tua {$jenis}{$suffix}",
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir'=> '1978-01-01',
            'pendidikan'   => 'S1',
            'pekerjaan'    => 'Wiraswasta',
            'penghasilan'  => '5000000',
            'no_telepon'   => '0812' . rand(10000000, 99999999),
        ];
    }

    private function minimalSiswaData(string $nis, ?string $nisn = null): array
    {
        return array_filter([
            'nis'             => $nis,
            'nisn'            => $nisn,
            'nama'            => 'Siswa ' . $nis,
            'jenis_kelamin'   => 'Laki-laki',
            'tempat_lahir'    => 'Jakarta',
            'kewarganegaraan' => 'Indonesia',
            'agama'           => 'Islam',
            'tanggal_lahir'   => '2010-01-01',
            'status_masuk'    => 'Baru',
            'tanggal_masuk'   => '2024-07-01',
        ], fn($v) => $v !== null);
    }

    // =========================================================================
    // Property 1 – At most one TahunAjaran has status = true at any time
    // Validates: Requirements 1.2
    // =========================================================================

    /** Validates: Requirements 1.2 */
    public function test_property_1_single_active_tahun_ajaran_on_create(): void
    {
        $service = app(TahunAjaranService::class);

        $service->create([
            'label'          => '2023/2024',
            'tanggal_mulai'  => '2023-07-01',
            'tanggal_selesai'=> '2024-06-30',
            'status'         => true,
        ]);

        $this->assertEquals(1, TahunAjaran::where('status', true)->count());
    }

    /** Validates: Requirements 1.2 */
    public function test_property_1_creating_second_active_deactivates_first(): void
    {
        $service = app(TahunAjaranService::class);

        $first = $service->create([
            'label'          => '2023/2024',
            'tanggal_mulai'  => '2023-07-01',
            'tanggal_selesai'=> '2024-06-30',
            'status'         => true,
        ]);

        $second = $service->create([
            'label'          => '2024/2025',
            'tanggal_mulai'  => '2024-07-01',
            'tanggal_selesai'=> '2025-06-30',
            'status'         => true,
        ]);

        $this->assertEquals(1, TahunAjaran::where('status', true)->count());
        $this->assertFalse($first->fresh()->status);
        $this->assertTrue($second->fresh()->status);
    }

    /** Validates: Requirements 1.2 */
    public function test_property_1_updating_to_active_deactivates_all_others(): void
    {
        $service = app(TahunAjaranService::class);

        $ta1 = TahunAjaran::factory()->create(['status' => true]);
        $ta2 = TahunAjaran::factory()->create(['status' => false]);
        $ta3 = TahunAjaran::factory()->create(['status' => false]);

        $service->update($ta2->id, ['status' => true]);

        $this->assertEquals(1, TahunAjaran::where('status', true)->count());
        $this->assertFalse($ta1->fresh()->status);
        $this->assertTrue($ta2->fresh()->status);
        $this->assertFalse($ta3->fresh()->status);
    }

    // =========================================================================
    // Property 2 – Semester dates are within parent TahunAjaran date range
    // Validates: Requirements 1.4
    // =========================================================================

    /** Validates: Requirements 1.4 */
    public function test_property_2_semester_start_before_tahun_ajaran_rejected(): void
    {
        $ta = $this->makeTahunAjaran();
        $this->expectException(ValidationException::class);

        app(SemesterService::class)->create([
            'tahun_ajaran_id' => $ta->id,
            'jenis'           => 'Ganjil',
            'tanggal_mulai'   => '2024-06-30', // 1 day before TA start
            'tanggal_selesai' => '2024-12-31',
            'status_aktif'    => false,
        ]);
    }

    /** Validates: Requirements 1.4 */
    public function test_property_2_semester_end_after_tahun_ajaran_rejected(): void
    {
        $ta = $this->makeTahunAjaran();
        $this->expectException(ValidationException::class);

        app(SemesterService::class)->create([
            'tahun_ajaran_id' => $ta->id,
            'jenis'           => 'Genap',
            'tanggal_mulai'   => '2025-01-01',
            'tanggal_selesai' => '2025-07-01', // 1 day after TA end
            'status_aktif'    => false,
        ]);
    }

    /** Validates: Requirements 1.4 */
    public function test_property_2_valid_semester_dates_within_range_accepted(): void
    {
        $ta       = $this->makeTahunAjaran();
        $semester = app(SemesterService::class)->create([
            'tahun_ajaran_id' => $ta->id,
            'jenis'           => 'Ganjil',
            'tanggal_mulai'   => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif'    => false,
        ]);

        $this->assertDatabaseHas('semesters', ['id' => $semester->id]);
        $this->assertTrue($semester->tanggal_mulai >= $ta->tanggal_mulai);
        $this->assertTrue($semester->tanggal_selesai <= $ta->tanggal_selesai);
    }

    // =========================================================================
    // Property 3 – No two Semesters share the same jenis within one TahunAjaran
    // Validates: Requirements 1.5
    // =========================================================================

    /** Validates: Requirements 1.5 */
    public function test_property_3_db_constraint_prevents_duplicate_jenis_in_same_tahun_ajaran(): void
    {
        $ta = $this->makeTahunAjaran();

        Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'jenis'           => 'Ganjil',
            'tanggal_mulai'   => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif'    => false,
        ]);

        $this->expectException(QueryException::class);

        Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'jenis'           => 'Ganjil', // duplicate
            'tanggal_mulai'   => '2024-07-15',
            'tanggal_selesai' => '2024-12-30',
            'status_aktif'    => false,
        ]);
    }

    /** Validates: Requirements 1.5 */
    public function test_property_3_service_rejects_duplicate_jenis_per_tahun_ajaran(): void
    {
        $service = app(SemesterService::class);
        $ta      = $this->makeTahunAjaran();

        $service->create([
            'tahun_ajaran_id' => $ta->id,
            'jenis'           => 'Ganjil',
            'tanggal_mulai'   => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif'    => false,
        ]);

        $this->expectException(ValidationException::class);

        $service->create([
            'tahun_ajaran_id' => $ta->id,
            'jenis'           => 'Ganjil',
            'tanggal_mulai'   => '2024-07-15',
            'tanggal_selesai' => '2024-12-30',
            'status_aktif'    => false,
        ]);
    }

    /** Validates: Requirements 1.5 */
    public function test_property_3_different_jenis_allowed_in_same_tahun_ajaran(): void
    {
        $ta = $this->makeTahunAjaran();

        $ganjil = Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'jenis'           => 'Ganjil',
            'tanggal_mulai'   => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif'    => false,
        ]);

        $genap = Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'jenis'           => 'Genap',
            'tanggal_mulai'   => '2025-01-01',
            'tanggal_selesai' => '2025-06-30',
            'status_aktif'    => false,
        ]);

        $this->assertEquals(2, Semester::where('tahun_ajaran_id', $ta->id)->count());
        $this->assertDatabaseHas('semesters', ['id' => $ganjil->id]);
        $this->assertDatabaseHas('semesters', ['id' => $genap->id]);
    }

    // =========================================================================
    // Property 4 – NIS globally unique; NISN globally unique when not null
    // Validates: Requirements 2.3, 2.4, 2.10
    // =========================================================================

    /** Validates: Requirements 2.3, 2.10 */
    public function test_property_4_nis_must_be_globally_unique(): void
    {
        $service = app(SiswaService::class);
        $service->register($this->minimalSiswaData('NIS_001'), [$this->orangTuaData('Ayah')]);

        $this->expectException(ValidationException::class);
        $service->register($this->minimalSiswaData('NIS_001'), [$this->orangTuaData('Ibu')]);
    }

    /** Validates: Requirements 2.4, 2.10 */
    public function test_property_4_nisn_must_be_globally_unique_when_provided(): void
    {
        $service = app(SiswaService::class);
        $service->register($this->minimalSiswaData('NIS_002', 'NISN_123'), [$this->orangTuaData('Ayah')]);

        $this->expectException(ValidationException::class);
        $service->register($this->minimalSiswaData('NIS_003', 'NISN_123'), [$this->orangTuaData('Ibu')]);
    }

    /** Validates: Requirements 2.4 */
    public function test_property_4_null_nisn_is_not_a_duplicate(): void
    {
        Siswa::factory()->create(['nis' => 'NIS_A01', 'nisn' => null]);
        Siswa::factory()->create(['nis' => 'NIS_A02', 'nisn' => null]);

        $this->assertEquals(2, Siswa::whereNull('nisn')->count());
    }

    // =========================================================================
    // Property 5 – Each Siswa has 1-3 OrangTuaWali with unique jenis per Siswa
    // Validates: Requirements 3.2, 3.3
    // =========================================================================

    /** Validates: Requirements 3.3 */
    public function test_property_5_db_constraint_prevents_duplicate_jenis_per_siswa(): void
    {
        $siswa = Siswa::factory()->create();

        OrangTuaWali::create([
            'siswa_id'     => $siswa->id,
            'jenis'        => 'Ayah',
            'nama'         => 'Bapak Test',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir'=> '1975-01-01',
            'pendidikan'   => 'S1',
            'pekerjaan'    => 'Wiraswasta',
            'penghasilan'  => '5000000',
            'no_telepon'   => '081234567890',
        ]);

        $this->expectException(QueryException::class);

        OrangTuaWali::create([
            'siswa_id'     => $siswa->id,
            'jenis'        => 'Ayah', // duplicate
            'nama'         => 'Paman Test',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir'=> '1978-06-15',
            'pendidikan'   => 'SMA',
            'pekerjaan'    => 'Buruh',
            'penghasilan'  => '2500000',
            'no_telepon'   => '081298765432',
        ]);
    }

    /** Validates: Requirements 3.2, 3.3 */
    public function test_property_5_siswa_can_have_up_to_three_unique_jenis(): void
    {
        $siswa = Siswa::factory()->create();

        foreach (['Ayah', 'Ibu', 'Wali'] as $jenis) {
            OrangTuaWali::create([
                'siswa_id'     => $siswa->id,
                'jenis'        => $jenis,
                'nama'         => "Orang Tua {$jenis}",
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir'=> '1975-01-01',
                'pendidikan'   => 'S1',
                'pekerjaan'    => 'Karyawan',
                'penghasilan'  => '5000000',
                'no_telepon'   => '0812' . rand(10000000, 99999999),
            ]);
        }

        $jenisList = OrangTuaWali::where('siswa_id', $siswa->id)->pluck('jenis')->toArray();
        $this->assertCount(3, $jenisList);
        $this->assertEquals(3, count(array_unique($jenisList)),
            'All jenis values must be unique per siswa');
    }

    /** Validates: Requirements 3.2 */
    public function test_property_5_same_jenis_allowed_for_different_siswas(): void
    {
        $siswa1 = Siswa::factory()->create();
        $siswa2 = Siswa::factory()->create();

        OrangTuaWali::create([
            'siswa_id'     => $siswa1->id,
            'jenis'        => 'Ayah',
            'nama'         => 'Bapak Satu',
            'tempat_lahir' => 'Solo',
            'tanggal_lahir'=> '1972-03-01',
            'pendidikan'   => 'SMA',
            'pekerjaan'    => 'Pedagang',
            'penghasilan'  => '3000000',
            'no_telepon'   => '081111111111',
        ]);

        OrangTuaWali::create([
            'siswa_id'     => $siswa2->id,
            'jenis'        => 'Ayah',
            'nama'         => 'Bapak Dua',
            'tempat_lahir' => 'Yogya',
            'tanggal_lahir'=> '1975-07-20',
            'pendidikan'   => 'D3',
            'pekerjaan'    => 'Swasta',
            'penghasilan'  => '4000000',
            'no_telepon'   => '082222222222',
        ]);

        $this->assertEquals(1, OrangTuaWali::where('siswa_id', $siswa1->id)->count());
        $this->assertEquals(1, OrangTuaWali::where('siswa_id', $siswa2->id)->count());
    }

    // =========================================================================
    // Property 6 – NIY is globally unique across all Guru records
    // Validates: Requirements 4.4, 4.10
    // =========================================================================

    /** Validates: Requirements 4.4 */
    public function test_property_6_niy_must_be_globally_unique_on_register(): void
    {
        $service = app(GuruService::class);
        $base    = [
            'jenis_kelamin'      => 'Laki-laki',
            'tanggal_lahir'      => '1985-06-15',
            'tempat_lahir'       => 'Jakarta',
            'kewarganegaraan'    => 'WNI',
            'agama'              => 'Islam',
            'status_pernikahan'  => 'Menikah',
            'pendidikan_terakhir'=> 'SMA',
            'tahun_lulus'        => 2004,
            'jabatan'            => 'Guru Mapel',
            'no_telepon'         => '081234567890',
            'status_aktif'       => true,
        ];

        $service->register(array_merge($base, ['niy' => 'NIY_SAME_001', 'nama' => 'Guru A']));

        $this->expectException(ValidationException::class);
        $service->register(array_merge($base, ['niy' => 'NIY_SAME_001', 'nama' => 'Guru B']));
    }

    /** Validates: Requirements 4.10 */
    public function test_property_6_niy_must_remain_unique_on_update(): void
    {
        $guru1 = Guru::factory()->create(['niy' => 'NIY_AAA']);
        $guru2 = Guru::factory()->create(['niy' => 'NIY_BBB']);

        $this->expectException(ValidationException::class);
        app(GuruService::class)->update($guru2->id, ['niy' => 'NIY_AAA']);
    }

    // =========================================================================
    // Property 7 – program_studi is not null when pendidikan_terakhir >= D1
    // Validates: Requirements 4.3, 4.6
    // =========================================================================

    /** Validates: Requirements 4.6 */
    public function test_property_7_program_studi_required_for_d1_and_above(): void
    {
        $service  = app(GuruService::class);
        $requires = ['D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];

        foreach ($requires as $pendidikan) {
            $caught = false;
            try {
                $service->register([
                    'niy'                => 'NIY_P_' . $pendidikan,
                    'nama'               => "Guru {$pendidikan}",
                    'jenis_kelamin'      => 'Laki-laki',
                    'tanggal_lahir'      => '1985-01-01',
                    'tempat_lahir'       => 'Jakarta',
                    'kewarganegaraan'    => 'WNI',
                    'agama'              => 'Islam',
                    'status_pernikahan'  => 'Menikah',
                    'pendidikan_terakhir'=> $pendidikan,
                    'program_studi'      => null,
                    'tahun_lulus'        => 2010,
                    'jabatan'            => 'Guru Mapel',
                    'no_telepon'         => '0812' . rand(10000000, 99999999),
                    'status_aktif'       => true,
                ]);
            } catch (ValidationException $e) {
                $caught = true;
                $this->assertArrayHasKey('program_studi', $e->errors(),
                    "Should error on program_studi for pendidikan: {$pendidikan}");
            }
            $this->assertTrue($caught,
                "ValidationException expected for pendidikan: {$pendidikan}");
        }
    }

    /** Validates: Requirements 4.3 */
    public function test_property_7_program_studi_not_required_below_d1(): void
    {
        $guru = app(GuruService::class)->register([
            'niy'                => 'NIY_SMA_TEST',
            'nama'               => 'Ustadzah SMA',
            'jenis_kelamin'      => 'Perempuan',
            'tanggal_lahir'      => '1988-04-10',
            'tempat_lahir'       => 'Bandung',
            'kewarganegaraan'    => 'WNI',
            'agama'              => 'Islam',
            'status_pernikahan'  => 'Menikah',
            'pendidikan_terakhir'=> 'SMA',
            'program_studi'      => null,
            'tahun_lulus'        => 2006,
            'jabatan'            => 'Tendik',
            'no_telepon'         => '085000000001',
            'status_aktif'       => true,
        ]);

        $this->assertDatabaseHas('gurus', ['id' => $guru->id]);
        $this->assertNull($guru->program_studi);
    }

    // =========================================================================
    // Property 8 – Kelompok nama unique within the same TahunAjaran
    // Validates: Requirements 5.2, 5.8
    // =========================================================================

    /** Validates: Requirements 5.2 */
    public function test_property_8_db_constraint_prevents_duplicate_nama_in_same_tahun_ajaran(): void
    {
        $ta   = $this->makeTahunAjaran();
        $guru = $this->makeGuruPembimbing();

        Kelompok::create([
            'nama'               => 'Kelompok Al-Fatihah',
            'jenis'              => 'Banin',
            'tingkat'            => 'Dasar',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id'    => $ta->id,
            'kapasitas_min'      => 6,
            'kapasitas_maks'     => 12,
        ]);

        $this->expectException(QueryException::class);

        Kelompok::create([
            'nama'               => 'Kelompok Al-Fatihah', // duplicate
            'jenis'              => 'Banat',
            'tingkat'            => 'Menengah',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id'    => $ta->id,
            'kapasitas_min'      => 6,
            'kapasitas_maks'     => 12,
        ]);
    }

    /** Validates: Requirements 5.2 */
    public function test_property_8_service_rejects_duplicate_nama_in_same_tahun_ajaran(): void
    {
        $service = app(KelompokService::class);
        $ta      = $this->makeTahunAjaran();
        $guru    = $this->makeGuruPembimbing();

        $service->create([
            'nama'               => 'Kelompok Iqra',
            'jenis'              => 'Banin',
            'tingkat'            => 'Dasar',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id'    => $ta->id,
        ]);

        $this->expectException(ValidationException::class);

        $service->create([
            'nama'               => 'Kelompok Iqra',
            'jenis'              => 'Banat',
            'tingkat'            => 'Menengah',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id'    => $ta->id,
        ]);
    }

    /** Validates: Requirements 5.8 */
    public function test_property_8_same_nama_allowed_in_different_tahun_ajaran(): void
    {
        $service = app(KelompokService::class);
        $ta1     = $this->makeTahunAjaran(['label' => '2023/2024', 'tanggal_mulai' => '2023-07-01', 'tanggal_selesai' => '2024-06-30']);
        $ta2     = $this->makeTahunAjaran(['label' => '2024/2025']);
        $guru    = $this->makeGuruPembimbing();

        $k1 = $service->create([
            'nama'               => 'Kelompok Iqra',
            'jenis'              => 'Banin',
            'tingkat'            => 'Dasar',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id'    => $ta1->id,
        ]);

        $k2 = $service->create([
            'nama'               => 'Kelompok Iqra',
            'jenis'              => 'Banin',
            'tingkat'            => 'Dasar',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id'    => $ta2->id,
        ]);

        $this->assertDatabaseHas('kelompoks', ['id' => $k1->id]);
        $this->assertDatabaseHas('kelompoks', ['id' => $k2->id]);
    }

    // =========================================================================
    // Property 9 – guru_pembimbing_id always references a Guru with
    //              jabatan = 'Guru Pembimbing Tahfidz'
    // Validates: Requirements 5.3
    // =========================================================================

    /** Validates: Requirements 5.3 */
    public function test_property_9_kelompok_rejects_guru_with_wrong_jabatan(): void
    {
        $service   = app(KelompokService::class);
        $ta        = $this->makeTahunAjaran();
        $guruMapel = Guru::factory()->create(['jabatan' => 'Guru Mapel', 'status_aktif' => true]);

        $this->expectException(ValidationException::class);

        $service->create([
            'nama'               => 'Kelompok Test',
            'jenis'              => 'Banin',
            'tingkat'            => 'Dasar',
            'guru_pembimbing_id' => $guruMapel->id,
            'tahun_ajaran_id'    => $ta->id,
        ]);
    }

    /** Validates: Requirements 5.3 */
    public function test_property_9_kelompok_accepts_guru_pembimbing_tahfidz(): void
    {
        $service = app(KelompokService::class);
        $ta      = $this->makeTahunAjaran();
        $guru    = $this->makeGuruPembimbing();

        $kelompok = $service->create([
            'nama'               => 'Kelompok Valid',
            'jenis'              => 'Banin',
            'tingkat'            => 'Dasar',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id'    => $ta->id,
        ]);

        $kelompok->load('guruPembimbing');
        $this->assertEquals('Guru Pembimbing Tahfidz', $kelompok->guruPembimbing->jabatan);
    }

    // =========================================================================
    // Property 10 – kapasitas_min <= kapasitas_maks for every Kelompok
    // Validates: Requirements 5.5
    // =========================================================================

    /** Validates: Requirements 5.5 */
    public function test_property_10_service_rejects_kapasitas_min_greater_than_maks(): void
    {
        $service = app(KelompokService::class);
        $ta      = $this->makeTahunAjaran();
        $guru    = $this->makeGuruPembimbing();

        $this->expectException(ValidationException::class);

        $service->create([
            'nama'               => 'Kelompok Kapasitas',
            'jenis'              => 'Banin',
            'tingkat'            => 'Dasar',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id'    => $ta->id,
            'kapasitas_min'      => 15,
            'kapasitas_maks'     => 10,
        ]);
    }

    /** Validates: Requirements 5.5 */
    public function test_property_10_kapasitas_min_equal_to_maks_is_valid(): void
    {
        $service  = app(KelompokService::class);
        $ta       = $this->makeTahunAjaran();
        $guru     = $this->makeGuruPembimbing();

        $kelompok = $service->create([
            'nama'               => 'Kelompok Equal Cap',
            'jenis'              => 'Banin',
            'tingkat'            => 'Menengah',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id'    => $ta->id,
            'kapasitas_min'      => 10,
            'kapasitas_maks'     => 10,
        ]);

        $this->assertTrue($kelompok->kapasitas_min <= $kelompok->kapasitas_maks);
    }

    /** Validates: Requirements 5.5 */
    public function test_property_10_default_capacities_satisfy_invariant(): void
    {
        $service  = app(KelompokService::class);
        $ta       = $this->makeTahunAjaran();
        $guru     = $this->makeGuruPembimbing();

        $kelompok = $service->create([
            'nama'               => 'Kelompok Default',
            'jenis'              => 'Banat',
            'tingkat'            => 'Atas',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id'    => $ta->id,
        ]);

        $this->assertLessThanOrEqual($kelompok->kapasitas_maks, $kelompok->kapasitas_min);
    }

    // =========================================================================
    // Property 11 – jumlah_siswa equals count of Ditempatkan students
    // Validates: Requirements 5.13
    // =========================================================================

    /** Validates: Requirements 5.13 */
    public function test_property_11_jumlah_siswa_matches_placed_students_count(): void
    {
        $ta       = $this->makeTahunAjaran();
        $guru     = $this->makeGuruPembimbing();
        $kelompok = $this->makeKelompok($ta, $guru);

        Siswa::factory()->count(3)->create([
            'kelompok_id'       => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        Siswa::factory()->count(2)->create([
            'kelompok_id'       => $kelompok->id,
            'status_penempatan' => 'Terdaftar',
        ]);

        $kelompok->refresh();

        $dbCount = Siswa::where('kelompok_id', $kelompok->id)
            ->where('status_penempatan', 'Ditempatkan')
            ->count();

        $this->assertEquals(3, $dbCount);
        $this->assertEquals($dbCount, $kelompok->jumlah_siswa,
            'jumlah_siswa accessor must equal count of Ditempatkan students');
    }

    /** Validates: Requirements 5.13 */
    public function test_property_11_jumlah_siswa_is_zero_when_no_placed_students(): void
    {
        $ta       = $this->makeTahunAjaran();
        $guru     = $this->makeGuruPembimbing();
        $kelompok = $this->makeKelompok($ta, $guru);

        $this->assertEquals(0, $kelompok->jumlah_siswa);
    }

    // =========================================================================
    // Property 12 – ReferensiQuran has exactly 30 records, unique juz_ke 1-30
    // Validates: Requirements 6.2, 6.3
    // =========================================================================

    /** Validates: Requirements 6.2, 6.3 */
    public function test_property_12_juz_seeder_creates_exactly_30_records(): void
    {
        $this->seed(JuzSeeder::class);
        $this->assertEquals(30, ReferensiQuran::count());
    }

    /** Validates: Requirements 6.3 */
    public function test_property_12_juz_ke_values_are_unique_and_span_1_to_30(): void
    {
        $this->seed(JuzSeeder::class);

        $juzValues = ReferensiQuran::orderBy('juz_ke')->pluck('juz_ke')->toArray();

        $this->assertCount(30, $juzValues);
        $this->assertCount(30, array_unique($juzValues), 'All juz_ke must be unique');
        $this->assertEquals(range(1, 30), $juzValues,
            'juz_ke must cover exactly 1 through 30');
    }

    /** Validates: Requirements 6.3 */
    public function test_property_12_db_constraint_prevents_duplicate_juz_ke(): void
    {
        $this->seed(JuzSeeder::class);

        $this->expectException(QueryException::class);

        ReferensiQuran::create([
            'juz_ke'         => 1,
            'urutan_hafalan' => 1,
            'awal_surah'     => 1,
            'awal_ayat'      => 1,
            'akhir_surah'    => 2,
            'akhir_ayat'     => 141,
            'jumlah_ayat'    => 148,
        ]);
    }

    // =========================================================================
    // Property 13 – All Semesters have status_aktif = false when TahunAjaran is
    //               deactivated
    // Validates: Requirements 1.7
    // =========================================================================

    /** Validates: Requirements 1.7 */
    public function test_property_13_deactivating_tahun_ajaran_deactivates_all_its_semesters(): void
    {
        $service = app(TahunAjaranService::class);
        $ta      = $this->makeTahunAjaran(['status' => true]);

        $ganjil = Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'jenis'           => 'Ganjil',
            'tanggal_mulai'   => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif'    => true,
        ]);

        $genap = Semester::create([
            'tahun_ajaran_id' => $ta->id,
            'jenis'           => 'Genap',
            'tanggal_mulai'   => '2025-01-01',
            'tanggal_selesai' => '2025-06-30',
            'status_aktif'    => true,
        ]);

        $service->update($ta->id, ['status' => false]);

        $this->assertFalse($ganjil->fresh()->status_aktif,
            'Ganjil semester must be deactivated');
        $this->assertFalse($genap->fresh()->status_aktif,
            'Genap semester must be deactivated');
    }

    /** Validates: Requirements 1.7 */
    public function test_property_13_other_tahun_ajaran_semesters_unaffected(): void
    {
        $service = app(TahunAjaranService::class);

        $ta1 = $this->makeTahunAjaran(['label' => '2023/2024', 'status' => true, 'tanggal_mulai' => '2023-07-01', 'tanggal_selesai' => '2024-06-30']);
        $ta2 = $this->makeTahunAjaran(['label' => '2024/2025', 'status' => false]);

        $semTA1 = Semester::create([
            'tahun_ajaran_id' => $ta1->id,
            'jenis'           => 'Ganjil',
            'tanggal_mulai'   => '2023-07-01',
            'tanggal_selesai' => '2023-12-31',
            'status_aktif'    => true,
        ]);

        $semTA2 = Semester::create([
            'tahun_ajaran_id' => $ta2->id,
            'jenis'           => 'Ganjil',
            'tanggal_mulai'   => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
            'status_aktif'    => true,
        ]);

        $service->update($ta1->id, ['status' => false]);

        $this->assertFalse($semTA1->fresh()->status_aktif);
        $this->assertTrue($semTA2->fresh()->status_aktif, 'TA2 semester must not be affected');
    }

    // =========================================================================
    // Property 14 – deleted_at NOT NULL after soft delete; NULL after restore
    // Validates: Requirements 2.11, 4.11, 9.8, 9.9
    // =========================================================================

    /** Validates: Requirements 2.11, 9.8 */
    public function test_property_14_siswa_deleted_at_set_after_soft_delete(): void
    {
        $service = app(SiswaService::class);

        foreach (['Lulus SMP', 'Pindah Sekolah', 'Keluar'] as $status) {
            $siswa = Siswa::factory()->create(['status_akademik' => 'Aktif']);
            $service->updateStatus($siswa->id, $status);

            $fresh = Siswa::withTrashed()->find($siswa->id);
            $this->assertNotNull($fresh->deleted_at,
                "deleted_at must be set after status: {$status}");
        }
    }

    /** Validates: Requirements 9.9 */
    public function test_property_14_siswa_deleted_at_null_after_restore(): void
    {
        $service = app(SiswaService::class);
        $siswa   = Siswa::factory()->create(['status_akademik' => 'Aktif']);

        $service->updateStatus($siswa->id, 'Lulus SMP');
        $this->assertNotNull(Siswa::withTrashed()->find($siswa->id)->deleted_at);

        $restored = $service->restore($siswa->id);
        $this->assertNull($restored->deleted_at);
    }

    /** Validates: Requirements 4.11, 9.8 */
    public function test_property_14_guru_deleted_at_set_when_deactivated(): void
    {
        $service = app(GuruService::class);
        $guru    = Guru::factory()->create(['status_aktif' => true]);

        $service->updateStatus($guru->id, false);

        $fresh = Guru::withTrashed()->find($guru->id);
        $this->assertNotNull($fresh->deleted_at,
            'deleted_at must be set when guru is deactivated');
    }

    /** Validates: Requirements 9.9 */
    public function test_property_14_guru_deleted_at_null_after_restore(): void
    {
        $service = app(GuruService::class);
        $guru    = Guru::factory()->create(['status_aktif' => true]);

        $service->updateStatus($guru->id, false);
        $this->assertNotNull(Guru::withTrashed()->find($guru->id)->deleted_at);

        $restored = $service->restore($guru->id);
        $this->assertNull($restored->deleted_at);
    }

    // =========================================================================
    // Property 15 – Deletion blocked when dependent records exist
    // Validates: Requirements 1.8, 1.9, 2.12, 2.13, 4.12, 4.13, 5.10, 5.11, 9.6
    // =========================================================================

    /** Validates: Requirements 1.8, 1.9 */
    public function test_property_15_tahun_ajaran_deletion_blocked_by_kelompok(): void
    {
        $ta   = $this->makeTahunAjaran();
        $guru = $this->makeGuruPembimbing();
        Kelompok::factory()->create([
            'tahun_ajaran_id'    => $ta->id,
            'guru_pembimbing_id' => $guru->id,
        ]);

        $this->expectException(\Exception::class);
        app(TahunAjaranService::class)->delete($ta->id);
    }

    /** Validates: Requirements 1.8 */
    public function test_property_15_tahun_ajaran_deletion_allowed_without_kelompok(): void
    {
        $ta       = $this->makeTahunAjaran();
        $semester = Semester::factory()->create(['tahun_ajaran_id' => $ta->id]);

        $result = app(TahunAjaranService::class)->delete($ta->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('tahun_ajarans', ['id' => $ta->id]);
        $this->assertDatabaseMissing('semesters', ['id' => $semester->id]);
    }

    /** Validates: Requirements 2.12, 2.13 */
    public function test_property_15_siswa_deletion_blocked_when_in_kelompok(): void
    {
        $ta       = $this->makeTahunAjaran();
        $guru     = $this->makeGuruPembimbing();
        $kelompok = $this->makeKelompok($ta, $guru);
        $siswa    = Siswa::factory()->create([
            'kelompok_id'       => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        $this->expectException(\Exception::class);
        app(SiswaService::class)->delete($siswa->id);
    }

    /** Validates: Requirements 2.12 */
    public function test_property_15_siswa_deletion_allowed_without_dependencies(): void
    {
        $siswa    = Siswa::factory()->create(['kelompok_id' => null]);
        $orangTua = OrangTuaWali::factory()->create(['siswa_id' => $siswa->id]);

        $result = app(SiswaService::class)->delete($siswa->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('siswas', ['id' => $siswa->id]);
        $this->assertDatabaseMissing('orang_tua_walis', ['id' => $orangTua->id]);
    }

    /** Validates: Requirements 4.12, 4.13 */
    public function test_property_15_guru_deletion_blocked_by_kelompok_bimbingan(): void
    {
        $ta   = $this->makeTahunAjaran();
        $guru = $this->makeGuruPembimbing();
        Kelompok::factory()->create([
            'tahun_ajaran_id'    => $ta->id,
            'guru_pembimbing_id' => $guru->id,
        ]);

        $this->expectException(\Exception::class);
        app(GuruService::class)->delete($guru->id);
    }

    /** Validates: Requirements 4.12 */
    public function test_property_15_guru_deletion_allowed_without_kelompok(): void
    {
        $guru          = Guru::factory()->create(['jabatan' => 'Guru Mapel', 'status_aktif' => true]);
        $tugasTambahan = GuruTugasTambahan::factory()->create(['guru_id' => $guru->id]);

        $result = app(GuruService::class)->delete($guru->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('gurus', ['id' => $guru->id]);
        $this->assertDatabaseMissing('guru_tugas_tambahans', ['id' => $tugasTambahan->id]);
    }

    /** Validates: Requirements 5.10, 5.11 */
    public function test_property_15_kelompok_deletion_blocked_by_placed_siswas(): void
    {
        $ta       = $this->makeTahunAjaran();
        $guru     = $this->makeGuruPembimbing();
        $kelompok = $this->makeKelompok($ta, $guru);

        Siswa::factory()->create([
            'kelompok_id'       => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        $this->expectException(\Exception::class);
        app(KelompokService::class)->delete($kelompok->id);
    }

    /** Validates: Requirements 5.10 */
    public function test_property_15_kelompok_deletion_allowed_without_siswas(): void
    {
        $ta       = $this->makeTahunAjaran();
        $guru     = $this->makeGuruPembimbing();
        $kelompok = $this->makeKelompok($ta, $guru);

        $result = app(KelompokService::class)->delete($kelompok->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('kelompoks', ['id' => $kelompok->id]);
    }
}
