<?php

namespace Tests\Feature\Livewire\Admin;

use Tests\TestCase;
use App\Livewire\Admin\DashboardAdmin;
use App\Models\Admin;
use App\Models\TahunAjaran;
use App\Models\Kelompok;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\UsulanKenaikanTingkat;
use App\Enums\StatusUsulan;
use App\Enums\TingkatKelompok;
use App\Enums\JenisKelompok;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

/**
 * Test suite for DashboardAdmin component
 *
 * Validates:
 * - Task 16: Dashboard widget for admin proposal statistics
 * - Requirements: 11.1, 11.2, 11.3, 11.4, 11.5
 */
class DashboardAdminTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected TahunAjaran $tahunAjaran;
    protected Kelompok $kelompok;
    protected Guru $guru;
    protected Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test admin
        $this->admin = Admin::factory()->create([
            'username' => 'admin_test',
        ]);

        // Create active tahun ajaran
        $this->tahunAjaran = TahunAjaran::factory()->create([
            'label' => '2024/2025',
            'status' => true,
        ]);

        // Create guru
        $this->guru = Guru::factory()->create();

        // Create kelompok for current tahun ajaran
        $this->kelompok = Kelompok::factory()->create([
            'nama' => 'Kelompok Dasar A',
            'tingkat' => TingkatKelompok::DASAR,
            'jenis' => JenisKelompok::BANIN,
            'guru_pembimbing_id' => $this->guru->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
        ]);

        // Create siswa
        $this->siswa = Siswa::factory()->create([
            'kelompok_id' => $this->kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);
    }

    /**
     * Test: Component renders successfully for authenticated admin
     *
     * Validates: Basic rendering requirement
     */
    public function test_component_renders_successfully(): void
    {
        $this->actingAs($this->admin, 'admin');

        Livewire::test(DashboardAdmin::class)
            ->assertStatus(200)
            ->assertSee('Selamat datang')
            ->assertSee('Statistik Usulan Kenaikan Tingkat');
    }

    /**
     * Test: Component displays correct statistics for current tahun_ajaran
     *
     * Validates: Requirements 11.1, 11.2
     */
    public function test_displays_correct_statistics_for_current_tahun_ajaran(): void
    {
        $this->actingAs($this->admin, 'admin');

        // Create proposals with different statuses
        UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $this->siswa->id,
            'guru_pengusul_id' => $this->guru->id,
            'kelompok_asal_id' => $this->kelompok->id,
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'status_usulan' => StatusUsulan::DIAJUKAN,
        ]);

        UsulanKenaikanTingkat::factory()->count(2)->create([
            'siswa_id' => $this->siswa->id,
            'guru_pengusul_id' => $this->guru->id,
            'kelompok_asal_id' => $this->kelompok->id,
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'status_usulan' => StatusUsulan::DISETUJUI,
        ]);

        UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $this->siswa->id,
            'guru_pengusul_id' => $this->guru->id,
            'kelompok_asal_id' => $this->kelompok->id,
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'status_usulan' => StatusUsulan::DITEMPATKAN,
        ]);

        UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $this->siswa->id,
            'guru_pengusul_id' => $this->guru->id,
            'kelompok_asal_id' => $this->kelompok->id,
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'status_usulan' => StatusUsulan::DITOLAK,
        ]);

        Livewire::test(DashboardAdmin::class)
            ->assertStatus(200)
            ->assertSee('1') // Diajukan count
            ->assertSee('2') // Disetujui count
            ->assertSee('5'); // Total count
    }

    /**
     * Test: Statistics exclude proposals from inactive tahun_ajaran
     *
     * Validates: Requirement 11.2 (filter by current tahun_ajaran)
     */
    public function test_statistics_exclude_inactive_tahun_ajaran(): void
    {
        $this->actingAs($this->admin, 'admin');

        // Create inactive tahun ajaran with kelompok
        $inactiveTahunAjaran = TahunAjaran::factory()->create([
            'label' => '2023/2024',
            'status' => false,
        ]);

        $inactiveKelompok = Kelompok::factory()->create([
            'tahun_ajaran_id' => $inactiveTahunAjaran->id,
            'guru_pembimbing_id' => $this->guru->id,
        ]);

        $inactiveSiswa = Siswa::factory()->create([
            'kelompok_id' => $inactiveKelompok->id,
        ]);

        // Create proposal for inactive tahun ajaran
        UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $inactiveSiswa->id,
            'guru_pengusul_id' => $this->guru->id,
            'kelompok_asal_id' => $inactiveKelompok->id,
            'status_usulan' => StatusUsulan::DIAJUKAN,
        ]);

        // Create proposal for active tahun ajaran
        UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $this->siswa->id,
            'guru_pengusul_id' => $this->guru->id,
            'kelompok_asal_id' => $this->kelompok->id,
            'status_usulan' => StatusUsulan::DIAJUKAN,
        ]);

        $component = Livewire::test(DashboardAdmin::class);

        $statistics = $component->get('usulanStatistics');

        // Should only count the proposal from active tahun ajaran
        $this->assertEquals(1, $statistics['diajukan']);
        $this->assertEquals(1, $statistics['total']);
    }

    /**
     * Test: Clicking status count navigates to IndexUsulan with filter
     *
     * Validates: Requirement 11.4
     */
    public function test_clicking_status_navigates_with_filter(): void
    {
        $this->actingAs($this->admin, 'admin');

        Livewire::test(DashboardAdmin::class)
            ->call('viewUsulanByStatus', 'diajukan')
            ->assertRedirect(route('admin.usulan-kenaikan-tingkat.index', ['filterStatus' => 'diajukan']));

        Livewire::test(DashboardAdmin::class)
            ->call('viewUsulanByStatus', 'disetujui')
            ->assertRedirect(route('admin.usulan-kenaikan-tingkat.index', ['filterStatus' => 'disetujui']));
    }

    /**
     * Test: Component refreshes on usulan-created event
     *
     * Validates: Requirement 11.5 (real-time updates)
     */
    public function test_component_refreshes_on_usulan_created_event(): void
    {
        $this->actingAs($this->admin, 'admin');

        $component = Livewire::test(DashboardAdmin::class);

        // Verify initial count is 0
        $statistics = $component->get('usulanStatistics');
        $this->assertEquals(0, $statistics['total']);

        // Create a proposal
        UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $this->siswa->id,
            'guru_pengusul_id' => $this->guru->id,
            'kelompok_asal_id' => $this->kelompok->id,
            'status_usulan' => StatusUsulan::DIAJUKAN,
        ]);

        // Dispatch event
        $component->dispatch('usulan-created');

        // Statistics should be refreshed
        $statistics = $component->get('usulanStatistics');
        $this->assertEquals(1, $statistics['diajukan']);
        $this->assertEquals(1, $statistics['total']);
    }

    /**
     * Test: Component refreshes on usulan-processed event
     *
     * Validates: Requirement 11.5 (real-time updates)
     */
    public function test_component_refreshes_on_usulan_processed_event(): void
    {
        $this->actingAs($this->admin, 'admin');

        // Create an initial proposal
        $usulan = UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $this->siswa->id,
            'guru_pengusul_id' => $this->guru->id,
            'kelompok_asal_id' => $this->kelompok->id,
            'status_usulan' => StatusUsulan::DIAJUKAN,
        ]);

        $component = Livewire::test(DashboardAdmin::class);

        // Verify initial counts
        $statistics = $component->get('usulanStatistics');
        $this->assertEquals(1, $statistics['diajukan']);
        $this->assertEquals(0, $statistics['disetujui']);

        // Update proposal status
        $usulan->update(['status_usulan' => StatusUsulan::DISETUJUI]);

        // Dispatch event
        $component->dispatch('usulan-processed');

        // Statistics should be refreshed
        $statistics = $component->get('usulanStatistics');
        $this->assertEquals(0, $statistics['diajukan']);
        $this->assertEquals(1, $statistics['disetujui']);
    }

    /**
     * Test: Diajukan status uses Emas Pudar color
     *
     * Validates: Requirement 11.3 (highlight pending items)
     */
    public function test_diajukan_status_uses_emas_pudar_color(): void
    {
        $this->actingAs($this->admin, 'admin');

        $component = Livewire::test(DashboardAdmin::class);
        $color = $component->call('getStatusColor', 'diajukan')->get('__method_return');

        $this->assertEquals('#C9A227', $color['bg']);
        $this->assertEquals('#FFFFFF', $color['text']);
    }

    /**
     * Test: Returns zero counts when no active tahun_ajaran
     *
     * Validates: Requirement 11.2 (graceful handling)
     */
    public function test_returns_zero_counts_when_no_active_tahun_ajaran(): void
    {
        $this->actingAs($this->admin, 'admin');

        // Deactivate the tahun ajaran
        $this->tahunAjaran->update(['status' => false]);

        $component = Livewire::test(DashboardAdmin::class);
        $statistics = $component->get('usulanStatistics');

        $this->assertEquals(0, $statistics['diajukan']);
        $this->assertEquals(0, $statistics['disetujui']);
        $this->assertEquals(0, $statistics['ditempatkan']);
        $this->assertEquals(0, $statistics['ditolak']);
        $this->assertEquals(0, $statistics['total']);
    }
}

