<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Sekolah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Admin\MasterData\Sekolah\ProfilSekolah;

class ProfilSekolahTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();
    }

    /** @test */
    public function admin_can_access_profil_sekolah_page()
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.master-data.profil-sekolah'))
            ->assertOk();
    }

    /** @test */
    public function admin_can_create_profil_sekolah()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(ProfilSekolah::class)
            ->set('nama', 'SMP Islam Al-Azhar')
            ->set('npsn', '12345678')
            ->set('alamat_jalan', 'Jl. Raya No. 123')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('sekolahs', [
            'nama' => 'SMP Islam Al-Azhar',
            'npsn' => '12345678',
        ]);
    }

    /** @test */
    public function admin_can_update_profil_sekolah()
    {
        Sekolah::create([
            'nama' => 'SMP Islam Al-Azhar',
            'npsn' => '12345678',
        ]);

        Livewire::actingAs($this->admin, 'admin')
            ->test(ProfilSekolah::class)
            ->set('nama', 'SMP Islam Al-Azhar Updated')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('sekolahs', [
            'nama' => 'SMP Islam Al-Azhar Updated',
        ]);
    }

    /** @test */
    public function validation_errors_are_shown()
    {
        Livewire::actingAs($this->admin, 'admin')
            ->test(ProfilSekolah::class)
            ->set('nama', '') // Required field
            ->call('save')
            ->assertHasErrors(['nama']);
    }
}
