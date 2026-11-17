<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Admin\MasterData\Kelas\IndexKelas;

class KelasManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();

        $this->tahunAjaran = TahunAjaran::create([
            'label' => '2026/2027',
            'tahun_mulai' => 2026,
            'tahun_selesai' => 2027,
            'status' => true,
        ]);
    }

    /** @test */
    public function admin_can_access_kelas_index_page()
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.kelas.index'))
            ->assertOk();
    }

    /** @test */
    public function admin_can_create_kelas()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexKelas::class)
            ->call('openModal')
            ->set('tahun_ajaran_id', $this->tahunAjaran->id)
            ->set('tingkat', '7')
            ->set('rombel', 'A')
            ->set('kapasitas', 30)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('kelas', [
            'tingkat' => '7',
            'rombel' => 'A',
        ]);
    }

    /** @test */
    public function admin_can_edit_kelas()
    {
        $kelas = Kelas::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'tingkat' => '7',
            'rombel' => 'A',
            'kapasitas' => 30,
        ]);

        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexKelas::class)
            ->call('openModal', $kelas->id)
            ->set('kapasitas', 35)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('kelas', [
            'id' => $kelas->id,
            'kapasitas' => 35,
        ]);
    }

    /** @test */
    public function admin_can_delete_kelas()
    {
        $kelas = Kelas::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'tingkat' => '7',
            'rombel' => 'A',
            'kapasitas' => 30,
        ]);

        Livewire::actingAs($this->admin, 'admin')
            ->test(IndexKelas::class)
            ->call('confirmDelete', $kelas->id)
            ->call('deleteKelas')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('kelas', ['id' => $kelas->id]);
    }
}
