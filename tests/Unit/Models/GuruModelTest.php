<?php

namespace Tests\Unit\Models;

use App\Models\Guru;
use App\Models\GuruTugasTambahan;
use App\Models\Kelompok;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_soft_deletes_trait()
    {
        $this->assertTrue(
            in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(Guru::class)),
            'Guru model should use SoftDeletes trait'
        );
    }

    /** @test */
    public function it_has_correct_guard()
    {
        $guru = new Guru();
        $this->assertEquals('guru', $guru->guard, 'Guru model should have guard set to "guru"');
    }

    /** @test */
    public function it_hides_tanggal_lahir()
    {
        $guru = new Guru();
        $this->assertContains('tanggal_lahir', $guru->getHidden(), 'tanggal_lahir should be in hidden array');
    }

    /** @test */
    public function it_has_all_master_data_fields_in_fillable()
    {
        $guru = new Guru();
        $fillable = $guru->getFillable();

        $requiredFields = [
            // Basic fields
            'niy', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir',
            // Master data fields
            'kewarganegaraan', 'agama', 'status_pernikahan',
            'pendidikan_terakhir', 'program_studi', 'tahun_lulus', 'jabatan',
            'no_telepon', 'email', 'foto',
            // Address fields with alamat_ prefix
            'alamat_jalan', 'alamat_dusun', 'alamat_rt', 'alamat_rw',
            'alamat_desa_kelurahan', 'alamat_kecamatan', 'alamat_kabupaten_kota',
            'alamat_provinsi', 'alamat_kode_pos',
            // Status fields
            'status_aktif', 'reset_oleh', 'reset_pada',
        ];

        foreach ($requiredFields as $field) {
            $this->assertContains(
                $field,
                $fillable,
                "Field '{$field}' should be in fillable array"
            );
        }
    }

    /** @test */
    public function it_has_tugas_tambahans_relationship()
    {
        $guru = Guru::factory()->create();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $guru->tugasTambahans()
        );
    }

    /** @test */
    public function it_has_kelompoks_bimbingan_relationship()
    {
        $guru = Guru::factory()->create();

        $relation = $guru->kelompoksBimbingan();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            $relation
        );

        // Verify it uses correct foreign key
        $this->assertEquals('guru_pembimbing_id', $relation->getForeignKeyName());
    }

    /** @test */
    public function scope_pembimbing_tahfidz_filters_correctly()
    {
        // Create guru with different jabatan
        Guru::factory()->create(['jabatan' => 'Guru Mapel', 'niy' => 'NIY001']);
        Guru::factory()->create(['jabatan' => 'Guru Pembimbing Tahfidz', 'niy' => 'NIY002']);
        Guru::factory()->create(['jabatan' => 'Kepala Sekolah', 'niy' => 'NIY003']);
        Guru::factory()->create(['jabatan' => 'Guru Pembimbing Tahfidz', 'niy' => 'NIY004']);

        $pembimbingTahfidz = Guru::pembimbingTahfidz()->get();

        $this->assertEquals(2, $pembimbingTahfidz->count());
        $this->assertTrue(
            $pembimbingTahfidz->every(fn($g) => $g->jabatan === 'Guru Pembimbing Tahfidz')
        );
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $guru = new Guru();
        $casts = $guru->getCasts();

        $this->assertEquals('boolean', $casts['status_aktif'] ?? null);
        $this->assertEquals('date', $casts['tanggal_lahir'] ?? null);
        $this->assertEquals('datetime', $casts['reset_pada'] ?? null);
    }

    /** @test */
    public function it_can_create_guru_with_all_master_data_fields()
    {
        $guruData = [
            'niy' => 'NIY123456',
            'nama' => 'Test Guru',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '1990-05-15',
            'tempat_lahir' => 'Jakarta',
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'status_pernikahan' => 'Menikah',
            'pendidikan_terakhir' => 'S1',
            'program_studi' => 'Pendidikan Agama Islam',
            'tahun_lulus' => '2012',
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'no_telepon' => '081234567890',
            'email' => 'test@example.com',
            'alamat_jalan' => 'Jl. Test No. 123',
            'alamat_dusun' => 'Dusun Test',
            'alamat_rt' => '001',
            'alamat_rw' => '002',
            'alamat_desa_kelurahan' => 'Kelurahan Test',
            'alamat_kecamatan' => 'Kecamatan Test',
            'alamat_kabupaten_kota' => 'Kota Test',
            'alamat_provinsi' => 'Provinsi Test',
            'alamat_kode_pos' => '12345',
            'status_aktif' => true,
        ];

        $guru = Guru::create($guruData);

        $this->assertDatabaseHas('gurus', [
            'niy' => 'NIY123456',
            'nama' => 'Test Guru',
            'jabatan' => 'Guru Pembimbing Tahfidz',
        ]);

        $this->assertEquals('Test Guru', $guru->nama);
        $this->assertEquals('Guru Pembimbing Tahfidz', $guru->jabatan);
    }
}
