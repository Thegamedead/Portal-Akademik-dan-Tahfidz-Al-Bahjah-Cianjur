<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Admin\MasterData\Siswa\IndexSiswa;

class SiswaKelasIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private TahunAjaran $tahunAjaran;
    private Kelas $kelas;

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

        $this->kelas = Kelas::create([
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'tingkat' => '7',
            'rombel' => 'A',
            'kapasitas' => 30,
        ]);
    }

    /** @test */
    public function siswa_index_shows_kelas_column()
    {
        $siswa = Siswa::factory()->create([
            'kelas_id' => $this->kelas->id,
        ]);

        $component = Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class);

        // Check that kelasList is passed to view
        $this->assertNotNull($component->get('kelasList'));
    }

    /** @test */
    public function siswa_can_be_filtered_by_kelas()
    {
        $siswa1 = Siswa::factory()->create(['kelas_id' => $this->kelas->id]);
        $siswa2 = Siswa::factory()->create(['kelas_id' => null]);

        $component = Livewire::actingAs($this->admin, 'admin')
            ->test(IndexSiswa::class)
            ->set('filterKelas', $this->kelas->id);

        // Should only show siswa1
        $siswas = $component->get('siswas');
        $this->assertCount(1, $siswas);
        $this->assertEquals($siswa1->id, $siswas->first()->id);
    }

    /** @test */
    public function kelas_shows_assigned_siswa_count()
    {
        Siswa::factory()->count(3)->create(['kelas_id' => $this->kelas->id]);

        $this->kelas->refresh();

        $this->assertEquals(3, $this->kelas->jumlah_siswa);
    }

    /** @test */
    public function kelas_capacity_warning_shows_correctly()
    {
        // Create 25 siswa (83.3% of capacity 30)
        Siswa::factory()->count(25)->create(['kelas_id' => $this->kelas->id]);

        $this->kelas->refresh();

        $percentage = ($this->kelas->jumlah_siswa / $this->kelas->kapasitas);

        $this->assertGreaterThan(0.8, $percentage); // Should show emas-pudar badge
    }
}
