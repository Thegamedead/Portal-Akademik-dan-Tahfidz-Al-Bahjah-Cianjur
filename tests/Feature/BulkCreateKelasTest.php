<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Admin\MasterData\Kelas\BulkCreateKelas;

class BulkCreateKelasTest extends TestCase
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
    public function admin_can_access_bulk_create_page()
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.kelas.bulk-create'))
            ->assertOk();
    }

    /** @test */
    public function admin_can_bulk_create_kelas()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(BulkCreateKelas::class)
            ->set('tahun_ajaran_id', $this->tahunAjaran->id)
            ->set('tingkat', '7')
            ->set('jumlah_rombel', 3)
            ->set('kapasitas', 30)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.master-data.kelas.index'));

        $this->assertDatabaseCount('kelas', 3);
        $this->assertDatabaseHas('kelas', ['rombel' => 'A']);
        $this->assertDatabaseHas('kelas', ['rombel' => 'B']);
        $this->assertDatabaseHas('kelas', ['rombel' => 'C']);
    }

    /** @test */
    public function preview_updates_when_parameters_change()
    {
        $component = Livewire::actingAs($this->admin, 'admin')
            ->test(BulkCreateKelas::class)
            ->set('tingkat', '7')
            ->set('jumlah_rombel', 2);

        $this->assertCount(2, $component->get('preview'));
        $this->assertEquals('7A', $component->get('preview')[0]['nama_lengkap']);
        $this->assertEquals('7B', $component->get('preview')[1]['nama_lengkap']);
    }
}
