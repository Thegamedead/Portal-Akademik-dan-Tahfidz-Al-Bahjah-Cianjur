<?php

namespace Tests\Feature\Livewire\Guru;

use App\Livewire\Guru\IndexKelompokBinaan;
use App\Models\Guru;
use App\Models\Kelompok;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IndexKelompokBinaanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the component renders successfully for authenticated guru
     */
    public function test_component_renders_for_authenticated_guru(): void
    {
        $guru = Guru::factory()->create([
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'status_aktif' => true,
        ]);

        $this->actingAs($guru, 'guru');

        Livewire::test(IndexKelompokBinaan::class)
            ->assertStatus(200)
            ->assertSee('Kelompok Binaan');
    }

    /**
     * Test that component displays only kelompok assigned to the authenticated guru
     */
    public function test_displays_only_guru_own_kelompok(): void
    {
        // Create two guru
        $guru1 = Guru::factory()->create([
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'status_aktif' => true,
        ]);
        $guru2 = Guru::factory()->create([
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'status_aktif' => true,
        ]);

        $tahunAjaran = TahunAjaran::factory()->create(['status' => true]);

        // Create kelompok for guru1
        $kelompokGuru1 = Kelompok::factory()->create([
            'nama' => 'Kelompok A',
            'guru_pembimbing_id' => $guru1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Create kelompok for guru2
        $kelompokGuru2 = Kelompok::factory()->create([
            'nama' => 'Kelompok B',
            'guru_pembimbing_id' => $guru2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Login as guru1
        $this->actingAs($guru1, 'guru');

        Livewire::test(IndexKelompokBinaan::class)
            ->assertSee('Kelompok A')
            ->assertDontSee('Kelompok B');
    }

    /**
     * Test that component displays empty state when guru has no kelompok
     */
    public function test_displays_empty_state_when_no_kelompok(): void
    {
        $guru = Guru::factory()->create([
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'status_aktif' => true,
        ]);

        $this->actingAs($guru, 'guru');

        Livewire::test(IndexKelompokBinaan::class)
            ->assertSee('Belum Ada Kelompok Binaan')
            ->assertSee('Anda belum ditugaskan untuk membimbing kelompok tahfidz manapun');
    }

    /**
     * Test that unauthenticated users cannot access the component
     */
    public function test_unauthenticated_users_cannot_access(): void
    {
        $this->get(route('guru.kelompok-binaan'))
            ->assertRedirect(route('guru.login'));
    }

    /**
     * Test that the route is accessible for authenticated guru
     */
    public function test_route_is_accessible_for_authenticated_guru(): void
    {
        $guru = Guru::factory()->create([
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'status_aktif' => true,
        ]);

        $this->actingAs($guru, 'guru')
            ->get(route('guru.kelompok-binaan'))
            ->assertStatus(200)
            ->assertSeeLivewire(IndexKelompokBinaan::class);
    }
}
