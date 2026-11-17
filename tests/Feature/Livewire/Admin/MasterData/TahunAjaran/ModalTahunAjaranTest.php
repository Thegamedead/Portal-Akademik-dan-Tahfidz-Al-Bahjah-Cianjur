<?php

namespace Tests\Feature\Livewire\Admin\MasterData\TahunAjaran;

use App\Livewire\Admin\MasterData\TahunAjaran\ModalTahunAjaran;
use App\Models\Admin;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ModalTahunAjaranTest extends TestCase
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
        Livewire::test(ModalTahunAjaran::class)
            ->assertStatus(200);
    }

    public function test_modal_is_hidden_by_default(): void
    {
        Livewire::test(ModalTahunAjaran::class)
            ->assertSet('showModal', false)
            ->assertDontSee('Tambah Tahun Ajaran');
    }

    public function test_it_can_open_modal_for_create_mode(): void
    {
        Livewire::test(ModalTahunAjaran::class)
            ->call('openModal')
            ->assertSet('showModal', true)
            ->assertSet('tahunAjaranId', null)
            ->assertSee('Tambah Tahun Ajaran');
    }

    public function test_it_can_open_modal_for_edit_mode(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create([
            'label' => '2024/2025',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2025-06-30',
            'status' => true,
        ]);

        Livewire::test(ModalTahunAjaran::class)
            ->call('openModal', $tahunAjaran->id)
            ->assertSet('showModal', true)
            ->assertSet('tahunAjaranId', $tahunAjaran->id)
            ->assertSet('label', '2024/2025')
            ->assertSet('tanggal_mulai', '2024-07-01')
            ->assertSet('tanggal_selesai', '2025-06-30')
            ->assertSet('status', true)
            ->assertSee('Edit Tahun Ajaran');
    }

    public function test_it_can_close_modal_and_reset_form(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        Livewire::test(ModalTahunAjaran::class)
            ->call('openModal', $tahunAjaran->id)
            ->assertSet('showModal', true)
            ->assertSet('tahunAjaranId', $tahunAjaran->id)
            ->call('tutupModal')
            ->assertSet('showModal', false)
            ->assertSet('tahunAjaranId', null)
            ->assertSet('label', '')
            ->assertSet('tanggal_mulai', '')
            ->assertSet('tanggal_selesai', '')
            ->assertSet('status', false);
    }

    public function test_it_can_create_tahun_ajaran(): void
    {
        Livewire::test(ModalTahunAjaran::class)
            ->call('openModal')
            ->set('label', '2024/2025')
            ->set('tanggal_mulai', '2024-07-01')
            ->set('tanggal_selesai', '2025-06-30')
            ->set('status', true)
            ->call('save')
            ->assertDispatched('tahunAjaranSaved')
            ->assertDispatched('toast')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('tahun_ajarans', [
            'label' => '2024/2025',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2025-06-30',
            'status' => true,
        ]);
    }

    public function test_it_can_update_tahun_ajaran(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create([
            'label' => '2023/2024',
            'tanggal_mulai' => '2023-07-01',
            'tanggal_selesai' => '2024-06-30',
            'status' => false,
        ]);

        Livewire::test(ModalTahunAjaran::class)
            ->call('openModal', $tahunAjaran->id)
            ->set('label', '2024/2025')
            ->set('tanggal_mulai', '2024-07-01')
            ->set('tanggal_selesai', '2025-06-30')
            ->set('status', true)
            ->call('save')
            ->assertDispatched('tahunAjaranSaved')
            ->assertDispatched('toast')
            ->assertSet('showModal', false);

        $this->assertDatabaseHas('tahun_ajarans', [
            'id' => $tahunAjaran->id,
            'label' => '2024/2025',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2025-06-30',
            'status' => true,
        ]);
    }

    public function test_label_is_required(): void
    {
        Livewire::test(ModalTahunAjaran::class)
            ->call('openModal')
            ->set('label', '')
            ->set('tanggal_mulai', '2024-07-01')
            ->set('tanggal_selesai', '2025-06-30')
            ->call('save')
            ->assertHasErrors(['label' => 'required']);
    }

    public function test_label_max_length_is_20_characters(): void
    {
        Livewire::test(ModalTahunAjaran::class)
            ->call('openModal')
            ->set('label', str_repeat('a', 21))
            ->set('tanggal_mulai', '2024-07-01')
            ->set('tanggal_selesai', '2025-06-30')
            ->call('save')
            ->assertHasErrors(['label' => 'max']);
    }

    public function test_tanggal_mulai_is_required(): void
    {
        Livewire::test(ModalTahunAjaran::class)
            ->call('openModal')
            ->set('label', '2024/2025')
            ->set('tanggal_mulai', '')
            ->set('tanggal_selesai', '2025-06-30')
            ->call('save')
            ->assertHasErrors(['tanggal_mulai' => 'required']);
    }

    public function test_tanggal_selesai_is_required(): void
    {
        Livewire::test(ModalTahunAjaran::class)
            ->call('openModal')
            ->set('label', '2024/2025')
            ->set('tanggal_mulai', '2024-07-01')
            ->set('tanggal_selesai', '')
            ->call('save')
            ->assertHasErrors(['tanggal_selesai' => 'required']);
    }

    public function test_tanggal_selesai_must_be_after_tanggal_mulai(): void
    {
        Livewire::test(ModalTahunAjaran::class)
            ->call('openModal')
            ->set('label', '2024/2025')
            ->set('tanggal_mulai', '2025-06-30')
            ->set('tanggal_selesai', '2024-07-01')
            ->call('save')
            ->assertHasErrors(['tanggal_selesai' => 'after']);
    }

    public function test_creating_active_tahun_ajaran_deactivates_others(): void
    {
        // Create two existing active tahun ajarans
        $tahunAjaran1 = TahunAjaran::factory()->create(['status' => true]);
        $tahunAjaran2 = TahunAjaran::factory()->create(['status' => true]);

        // Create a new active tahun ajaran
        Livewire::test(ModalTahunAjaran::class)
            ->call('openModal')
            ->set('label', '2024/2025')
            ->set('tanggal_mulai', '2024-07-01')
            ->set('tanggal_selesai', '2025-06-30')
            ->set('status', true)
            ->call('save');

        // Only the new one should be active
        $this->assertDatabaseHas('tahun_ajarans', [
            'id' => $tahunAjaran1->id,
            'status' => false,
        ]);

        $this->assertDatabaseHas('tahun_ajarans', [
            'id' => $tahunAjaran2->id,
            'status' => false,
        ]);

        $this->assertDatabaseHas('tahun_ajarans', [
            'label' => '2024/2025',
            'status' => true,
        ]);
    }

    public function test_updating_to_active_deactivates_other_tahun_ajarans(): void
    {
        $tahunAjaran1 = TahunAjaran::factory()->create(['status' => true]);
        $tahunAjaran2 = TahunAjaran::factory()->create(['status' => false]);

        // Update tahunAjaran2 to active
        Livewire::test(ModalTahunAjaran::class)
            ->call('openModal', $tahunAjaran2->id)
            ->set('status', true)
            ->call('save');

        // tahunAjaran1 should now be inactive
        $this->assertDatabaseHas('tahun_ajarans', [
            'id' => $tahunAjaran1->id,
            'status' => false,
        ]);

        // tahunAjaran2 should be active
        $this->assertDatabaseHas('tahun_ajarans', [
            'id' => $tahunAjaran2->id,
            'status' => true,
        ]);
    }

    public function test_it_dispatches_correct_event_on_create_success(): void
    {
        Livewire::test(ModalTahunAjaran::class)
            ->call('openModal')
            ->set('label', '2024/2025')
            ->set('tanggal_mulai', '2024-07-01')
            ->set('tanggal_selesai', '2025-06-30')
            ->call('save')
            ->assertDispatched('tahunAjaranSaved');
    }

    public function test_it_dispatches_correct_event_on_update_success(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        Livewire::test(ModalTahunAjaran::class)
            ->call('openModal', $tahunAjaran->id)
            ->set('label', 'Updated Label')
            ->call('save')
            ->assertDispatched('tahunAjaranSaved');
    }
}
