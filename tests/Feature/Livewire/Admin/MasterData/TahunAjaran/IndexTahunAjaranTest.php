<?php

namespace Tests\Feature\Livewire\Admin\MasterData\TahunAjaran;

use App\Livewire\Admin\MasterData\TahunAjaran\IndexTahunAjaran;
use App\Models\Admin;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Kelompok;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndexTahunAjaranTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate an admin user
        $this->admin = Admin::factory()->create();
        $this->actingAs($this->admin, 'admin');
    }

    public function test_it_can_render_the_component(): void
    {
        Livewire::test(IndexTahunAjaran::class)
            ->assertStatus(200);
    }

    public function test_it_displays_list_of_tahun_ajarans_with_semester_count(): void
    {
        $tahunAjaran1 = TahunAjaran::factory()->create([
            'label' => '2023/2024',
            'status' => true,
        ]);

        $tahunAjaran2 = TahunAjaran::factory()->create([
            'label' => '2024/2025',
            'status' => false,
        ]);

        // Create semesters for tahun ajaran 1
        Semester::factory()->count(2)->create(['tahun_ajaran_id' => $tahunAjaran1->id]);

        // Create semester for tahun ajaran 2
        Semester::factory()->create(['tahun_ajaran_id' => $tahunAjaran2->id]);

        Livewire::test(IndexTahunAjaran::class)
            ->assertSee('2023/2024')
            ->assertSee('2024/2025')
            ->assertSee('2'); // semester count for tahunAjaran1
    }

    public function test_it_can_open_confirmation_modal_for_delete(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        Livewire::test(IndexTahunAjaran::class)
            ->call('bukaModalKonfirmasi', $tahunAjaran->id)
            ->assertSet('showModalKonfirmasi', true)
            ->assertSet('tahunAjaranIdHapus', $tahunAjaran->id)
            ->assertNotNull('tahunAjaranHapus');
    }

    public function test_it_can_close_confirmation_modal(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        Livewire::test(IndexTahunAjaran::class)
            ->call('bukaModalKonfirmasi', $tahunAjaran->id)
            ->assertSet('showModalKonfirmasi', true)
            ->call('tutupModalKonfirmasi')
            ->assertSet('showModalKonfirmasi', false)
            ->assertSet('tahunAjaranIdHapus', null)
            ->assertNull('tahunAjaranHapus');
    }

    public function test_it_can_delete_tahun_ajaran_without_dependencies(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        Semester::factory()->count(2)->create(['tahun_ajaran_id' => $tahunAjaran->id]);

        $this->assertDatabaseHas('tahun_ajarans', ['id' => $tahunAjaran->id]);
        $this->assertDatabaseCount('semesters', 2);

        Livewire::test(IndexTahunAjaran::class)
            ->call('bukaModalKonfirmasi', $tahunAjaran->id)
            ->call('delete')
            ->assertDispatched('toast');

        $this->assertDatabaseMissing('tahun_ajarans', ['id' => $tahunAjaran->id]);
        $this->assertDatabaseCount('semesters', 0); // Cascade deleted
    }

    public function test_it_cannot_delete_tahun_ajaran_with_kelompok_dependency(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        // Create a kelompok related to this tahun ajaran
        Kelompok::factory()->create(['tahun_ajaran_id' => $tahunAjaran->id]);

        $this->assertDatabaseHas('tahun_ajarans', ['id' => $tahunAjaran->id]);

        Livewire::test(IndexTahunAjaran::class)
            ->call('bukaModalKonfirmasi', $tahunAjaran->id)
            ->call('delete')
            ->assertDispatched('toast');

        // Should still exist because deletion was prevented
        $this->assertDatabaseHas('tahun_ajarans', ['id' => $tahunAjaran->id]);
    }

    public function test_it_orders_tahun_ajarans_by_tanggal_mulai_descending(): void
    {
        $tahunAjaran1 = TahunAjaran::factory()->create([
            'label' => '2022/2023',
            'tanggal_mulai' => '2022-07-01',
        ]);

        $tahunAjaran2 = TahunAjaran::factory()->create([
            'label' => '2023/2024',
            'tanggal_mulai' => '2023-07-01',
        ]);

        $tahunAjaran3 = TahunAjaran::factory()->create([
            'label' => '2024/2025',
            'tanggal_mulai' => '2024-07-01',
        ]);

        $component = Livewire::test(IndexTahunAjaran::class);

        $tahunAjarans = $component->viewData('tahunAjarans');

        // The first item should be the most recent (2024/2025)
        $this->assertEquals('2024/2025', $tahunAjarans->first()->label);
        // The last item should be the oldest (2022/2023)
        $this->assertEquals('2022/2023', $tahunAjarans->last()->label);
    }
}

