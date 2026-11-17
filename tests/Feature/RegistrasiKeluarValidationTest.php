<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Siswa;
use Livewire\Livewire;
use App\Livewire\Admin\MasterData\Siswa\IndexSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class RegistrasiKeluarValidationTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create([
            'username' => 'admin',
            'nama' => 'Admin Test',
        ]);

        $this->siswa = Siswa::factory()->create([
            'nis' => '12345',
            'nama' => 'Siswa Test',
            'status_akademik' => 'Aktif',
            'status_aktif' => true,
            'tanggal_masuk' => '2020-01-01',
        ]);
    }

    #[Test]
    public function it_requires_jenis_keluar_field()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', '')
            ->set('tanggalKeluar', '2024-01-01')
            ->call('submitRegistrasiKeluar')
            ->assertHasErrors(['jenisKeluar' => 'required']);
    }

    #[Test]
    public function it_validates_jenis_keluar_is_valid_enum()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', 'Invalid Value')
            ->set('tanggalKeluar', '2024-01-01')
            ->call('submitRegistrasiKeluar')
            ->assertHasErrors('jenisKeluar');
    }

    #[Test]
    public function it_requires_tanggal_keluar_field()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', 'Lulus SMP')
            ->set('tanggalKeluar', '')
            ->call('submitRegistrasiKeluar')
            ->assertHasErrors(['tanggalKeluar' => 'required']);
    }

    #[Test]
    public function it_validates_tanggal_keluar_must_be_date()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', 'Lulus SMP')
            ->set('tanggalKeluar', 'not-a-date')
            ->call('submitRegistrasiKeluar')
            ->assertHasErrors(['tanggalKeluar' => 'date']);
    }

    #[Test]
    public function it_validates_tanggal_keluar_cannot_be_before_tanggal_masuk()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', 'Lulus SMP')
            ->set('tanggalKeluar', '2019-12-31')
            ->call('submitRegistrasiKeluar')
            ->assertHasErrors('tanggalKeluar');
    }

    #[Test]
    public function it_allows_tanggal_keluar_equal_to_tanggal_masuk()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', 'Lulus SMP')
            ->set('tanggalKeluar', '2020-01-01')
            ->call('submitRegistrasiKeluar')
            ->assertHasNoErrors('tanggalKeluar')
            ->assertSet('showModalKonfirmasiKeluar', true);
    }

    #[Test]
    public function it_allows_tanggal_keluar_after_tanggal_masuk()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', 'Lulus SMP')
            ->set('tanggalKeluar', '2024-01-01')
            ->call('submitRegistrasiKeluar')
            ->assertHasNoErrors('tanggalKeluar')
            ->assertSet('showModalKonfirmasiKeluar', true);
    }

    #[Test]
    public function it_requires_tujuan_sekolah_when_jenis_keluar_is_pindah_sekolah()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', 'Pindah Sekolah')
            ->set('tanggalKeluar', '2024-01-01')
            ->set('tujuanSekolah', '')
            ->call('submitRegistrasiKeluar')
            ->assertHasErrors('tujuanSekolah');
    }

    #[Test]
    public function it_does_not_require_tujuan_sekolah_when_jenis_keluar_is_not_pindah_sekolah()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', 'Lulus SMP')
            ->set('tanggalKeluar', '2024-01-01')
            ->set('tujuanSekolah', '')
            ->call('submitRegistrasiKeluar')
            ->assertHasNoErrors('tujuanSekolah')
            ->assertSet('showModalKonfirmasiKeluar', true);
    }

    #[Test]
    public function it_requires_keterangan_keluar_when_jenis_keluar_is_meninggal()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', 'Meninggal')
            ->set('tanggalKeluar', '2024-01-01')
            ->set('keteranganKeluar', '')
            ->call('submitRegistrasiKeluar')
            ->assertHasErrors('keteranganKeluar');
    }

    #[Test]
    public function it_requires_keterangan_keluar_when_jenis_keluar_is_keluar()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', 'Keluar')
            ->set('tanggalKeluar', '2024-01-01')
            ->set('keteranganKeluar', '')
            ->call('submitRegistrasiKeluar')
            ->assertHasErrors('keteranganKeluar');
    }

    #[Test]
    public function it_does_not_require_keterangan_keluar_when_jenis_keluar_is_lulus_smp()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', 'Lulus SMP')
            ->set('tanggalKeluar', '2024-01-01')
            ->set('keteranganKeluar', '')
            ->call('submitRegistrasiKeluar')
            ->assertHasNoErrors('keteranganKeluar')
            ->assertSet('showModalKonfirmasiKeluar', true);
    }

    #[Test]
    public function it_opens_confirmation_modal_when_validation_passes()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', 'Lulus SMP')
            ->set('tanggalKeluar', '2024-01-01')
            ->call('submitRegistrasiKeluar')
            ->assertHasNoErrors()
            ->assertSet('showModalKonfirmasiKeluar', true);
    }

    #[Test]
    public function it_validates_pindah_sekolah_with_tujuan_sekolah_successfully()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', 'Pindah Sekolah')
            ->set('tanggalKeluar', '2024-01-01')
            ->set('tujuanSekolah', 'SMP Negeri 1')
            ->call('submitRegistrasiKeluar')
            ->assertHasNoErrors()
            ->assertSet('showModalKonfirmasiKeluar', true);
    }

    #[Test]
    public function it_validates_meninggal_with_keterangan_successfully()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', 'Meninggal')
            ->set('tanggalKeluar', '2024-01-01')
            ->set('keteranganKeluar', 'Sakit')
            ->call('submitRegistrasiKeluar')
            ->assertHasNoErrors()
            ->assertSet('showModalKonfirmasiKeluar', true);
    }

    #[Test]
    public function it_validates_keluar_with_keterangan_successfully()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('selectedIds', [$this->siswa->id])
            ->call('bukaModalRegistrasiKeluar')
            ->set('jenisKeluar', 'Keluar')
            ->set('tanggalKeluar', '2024-01-01')
            ->set('keteranganKeluar', 'Alasan pribadi')
            ->call('submitRegistrasiKeluar')
            ->assertHasNoErrors()
            ->assertSet('showModalKonfirmasiKeluar', true);
    }
}
