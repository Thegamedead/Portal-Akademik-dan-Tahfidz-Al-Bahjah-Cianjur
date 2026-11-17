<?php

namespace Tests\Feature\Livewire\Guru\UsulanKenaikanTingkat;

use App\Livewire\Guru\UsulanKenaikanTingkat\IndexUsulan;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelompok;
use App\Models\TahunAjaran;
use App\Models\UsulanKenaikanTingkat;
use App\Enums\StatusUsulan;
use App\Enums\TingkatKelompok;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Feature tests for IndexUsulan Livewire Component (Guru Namespace)
 *
 * Tests the display and interaction of promotion proposal list including:
 * - Table rendering with correct columns
 * - Filtering by scopeByGuru (only proposals by logged-in guru)
 * - Status badge colors (Zamrud, Delima, Biru Slate)
 * - Viewing catatan_keputusan for rejected proposals
 *
 * Task 10.1: Implement IndexUsulan component (Guru namespace)
 * Requirements: 10.1, 10.2, 10.4, 10.5
 */
class IndexUsulanTest extends TestCase
{
    use RefreshDatabase;

    protected Guru $guru;
    protected Guru $otherGuru;
    protected Kelompok $kelompok;
    protected TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();

        // Create tahun ajaran
        $this->tahunAjaran = TahunAjaran::factory()->create([
            'status' => 'Aktif',
        ]);

        // Create guru and authenticate
        $this->guru = Guru::factory()->create();
        $this->otherGuru = Guru::factory()->create();

        // Create kelompok
        $this->kelompok = Kelompok::factory()->create([
            'nama' => 'Dasar 1',
            'tingkat' => TingkatKelompok::DASAR,
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $this->guru->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);
    }

    /**
     * Test: Component renders successfully for authenticated guru
     *
     * Validates: Requirements 10.1
     */
    public function test_component_renders_for_authenticated_guru(): void
    {
        $this->actingAs($this->guru, 'guru');

        Livewire::test(IndexUsulan::class)
            ->assertStatus(200)
            ->assertSee('Usulan Kenaikan Tingkat')
            ->assertSee('Daftar usulan kenaikan tingkat yang telah Anda ajukan');
    }

    /**
     * Test: Table displays all required columns
     *
     * Validates: Requirements 10.4
     */
    public function test_table_displays_required_columns(): void
    {
        $this->actingAs($this->guru, 'guru');

        Livewire::test(IndexUsulan::class)
            ->assertSee('Tanggal Usul')
            ->assertSee('Nama Siswa')
            ->assertSee('NIS')
            ->assertSee('Tingkat Tujuan')
            ->assertSee('Status Usulan')
            ->assertSee('Tanggal Keputusan')
            ->assertSee('Aksi');
    }

    /**
     * Test: Component only displays proposals submitted by logged-in guru
     *
     * Validates: Requirements 10.1, 10.2 (scopeByGuru)
     */
    public function test_component_filters_proposals_by_logged_in_guru(): void
    {
        $this->actingAs($this->guru, 'guru');

        // Create siswa for this guru's kelompok
        $siswa1 = Siswa::factory()->create([
            'nama' => 'Ahmad Test',
            'nis' => '12345',
            'kelompok_id' => $this->kelompok->id,
        ]);

        $siswa2 = Siswa::factory()->create([
            'nama' => 'Budi Test',
            'nis' => '12346',
            'kelompok_id' => $this->kelompok->id,
        ]);

        // Create usulan by this guru
        $usulanByThisGuru = UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa1->id,
            'guru_pengusul_id' => $this->guru->id,
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'kelompok_asal_id' => $this->kelompok->id,
            'status_usulan' => StatusUsulan::DIAJUKAN,
            'alasan' => 'Test alasan',
            'tanggal_usul' => now(),
        ]);

        // Create usulan by another guru (should NOT be visible)
        $usulanByOtherGuru = UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa2->id,
            'guru_pengusul_id' => $this->otherGuru->id,
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'kelompok_asal_id' => $this->kelompok->id,
            'status_usulan' => StatusUsulan::DIAJUKAN,
            'alasan' => 'Test alasan lain',
            'tanggal_usul' => now(),
        ]);

        Livewire::test(IndexUsulan::class)
            ->assertSee('Ahmad Test')  // Should see this guru's proposal
            ->assertDontSee('Budi Test');  // Should NOT see other guru's proposal
    }

    /**
     * Test: Proposal data is displayed correctly in table
     *
     * Validates: Requirements 10.4
     */
    public function test_proposal_data_displayed_correctly(): void
    {
        $this->actingAs($this->guru, 'guru');

        $siswa = Siswa::factory()->create([
            'nama' => 'Ahmad Test',
            'nis' => '12345',
            'kelompok_id' => $this->kelompok->id,
        ]);

        $usulan = UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa->id,
            'guru_pengusul_id' => $this->guru->id,
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'kelompok_asal_id' => $this->kelompok->id,
            'status_usulan' => StatusUsulan::DIAJUKAN,
            'alasan' => 'Test alasan',
            'tanggal_usul' => now()->setDate(2024, 1, 15),
        ]);

        Livewire::test(IndexUsulan::class)
            ->assertSee('Ahmad Test')  // nama_siswa
            ->assertSee('12345')  // NIS
            ->assertSee('Menengah')  // tingkat_tujuan
            ->assertSee('Diajukan')  // status_usulan
            ->assertSee('15/01/2024');  // tanggal_usul
    }

    /**
     * Test: Status badges display with correct colors
     *
     * Validates: Requirement 10.4 (Zamrud, Delima, Biru Slate)
     */
    public function test_status_badges_display_with_correct_colors(): void
    {
        $this->actingAs($this->guru, 'guru');

        $siswa = Siswa::factory()->create([
            'kelompok_id' => $this->kelompok->id,
        ]);

        // Test DIAJUKAN status (Biru Slate)
        $usulanDiajukan = UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa->id,
            'guru_pengusul_id' => $this->guru->id,
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'kelompok_asal_id' => $this->kelompok->id,
            'status_usulan' => StatusUsulan::DIAJUKAN,
            'alasan' => 'Test',
            'tanggal_usul' => now(),
        ]);

        $component = Livewire::test(IndexUsulan::class);
        $badgeColor = $component->instance()->getStatusBadgeColor(StatusUsulan::DIAJUKAN);

        $this->assertEquals('#5B7A8C', $badgeColor['bg']); // Biru Slate
        $this->assertEquals('#FFFFFF', $badgeColor['text']);

        // Test DISETUJUI status (Zamrud)
        $badgeColor = $component->instance()->getStatusBadgeColor(StatusUsulan::DISETUJUI);
        $this->assertEquals('#1B5E4F', $badgeColor['bg']); // Zamrud

        // Test DITEMPATKAN status (Zamrud)
        $badgeColor = $component->instance()->getStatusBadgeColor(StatusUsulan::DITEMPATKAN);
        $this->assertEquals('#1B5E4F', $badgeColor['bg']); // Zamrud

        // Test DITOLAK status (Delima)
        $badgeColor = $component->instance()->getStatusBadgeColor(StatusUsulan::DITOLAK);
        $this->assertEquals('#9B3A3A', $badgeColor['bg']); // Delima
    }

    /**
     * Test: View catatan button appears only for rejected proposals
     *
     * Validates: Requirement 10.5
     */
    public function test_view_catatan_button_appears_only_for_rejected_proposals(): void
    {
        $this->actingAs($this->guru, 'guru');

        $siswa1 = Siswa::factory()->create([
            'nama' => 'Siswa Diajukan',
            'kelompok_id' => $this->kelompok->id,
        ]);

        $siswa2 = Siswa::factory()->create([
            'nama' => 'Siswa Ditolak',
            'kelompok_id' => $this->kelompok->id,
        ]);

        // Usulan with DIAJUKAN status (no catatan button)
        $usulanDiajukan = UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa1->id,
            'guru_pengusul_id' => $this->guru->id,
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'kelompok_asal_id' => $this->kelompok->id,
            'status_usulan' => StatusUsulan::DIAJUKAN,
            'alasan' => 'Test',
            'tanggal_usul' => now(),
        ]);

        // Usulan with DITOLAK status (should show catatan button)
        $usulanDitolak = UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa2->id,
            'guru_pengusul_id' => $this->guru->id,
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'kelompok_asal_id' => $this->kelompok->id,
            'status_usulan' => StatusUsulan::DITOLAK,
            'alasan' => 'Test',
            'tanggal_usul' => now(),
            'catatan_keputusan' => 'Ditolak karena belum memenuhi syarat',
            'tanggal_keputusan' => now(),
        ]);

        Livewire::test(IndexUsulan::class)
            ->assertSee('Lihat Catatan'); // Button for rejected proposal
    }

    /**
     * Test: Viewing catatan_keputusan modal for rejected proposal
     *
     * Validates: Requirement 10.5
     */
    public function test_viewing_catatan_keputusan_modal(): void
    {
        $this->actingAs($this->guru, 'guru');

        $siswa = Siswa::factory()->create([
            'nama' => 'Ahmad Test',
            'kelompok_id' => $this->kelompok->id,
        ]);

        $usulan = UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa->id,
            'guru_pengusul_id' => $this->guru->id,
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'kelompok_asal_id' => $this->kelompok->id,
            'status_usulan' => StatusUsulan::DITOLAK,
            'alasan' => 'Test',
            'tanggal_usul' => now(),
            'catatan_keputusan' => 'Ditolak karena belum memenuhi syarat minimal hafalan',
            'tanggal_keputusan' => now(),
        ]);

        Livewire::test(IndexUsulan::class)
            ->call('viewCatatan', $usulan->id)
            ->assertSet('showCatatanModal', true)
            ->assertSet('selectedCatatan', 'Ditolak karena belum memenuhi syarat minimal hafalan')
            ->assertSet('selectedSiswaName', 'Ahmad Test');
    }

    /**
     * Test: Closing catatan modal resets state
     *
     * Validates: Requirement 10.5
     */
    public function test_closing_catatan_modal_resets_state(): void
    {
        $this->actingAs($this->guru, 'guru');

        $siswa = Siswa::factory()->create([
            'nama' => 'Ahmad Test',
            'kelompok_id' => $this->kelompok->id,
        ]);

        $usulan = UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa->id,
            'guru_pengusul_id' => $this->guru->id,
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'kelompok_asal_id' => $this->kelompok->id,
            'status_usulan' => StatusUsulan::DITOLAK,
            'alasan' => 'Test',
            'tanggal_usul' => now(),
            'catatan_keputusan' => 'Ditolak karena belum memenuhi syarat',
            'tanggal_keputusan' => now(),
        ]);

        Livewire::test(IndexUsulan::class)
            ->call('viewCatatan', $usulan->id)
            ->assertSet('showCatatanModal', true)
            ->call('closeCatatanModal')
            ->assertSet('showCatatanModal', false)
            ->assertSet('selectedCatatan', null)
            ->assertSet('selectedSiswaName', null);
    }

    /**
     * Test: Cannot view catatan for proposals from other gurus
     *
     * Validates: Requirement 10.2 (security - scopeByGuru)
     */
    public function test_cannot_view_catatan_for_other_guru_proposals(): void
    {
        $this->actingAs($this->guru, 'guru');

        $siswa = Siswa::factory()->create([
            'kelompok_id' => $this->kelompok->id,
        ]);

        // Usulan by another guru
        $usulan = UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa->id,
            'guru_pengusul_id' => $this->otherGuru->id,  // Different guru
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'kelompok_asal_id' => $this->kelompok->id,
            'status_usulan' => StatusUsulan::DITOLAK,
            'alasan' => 'Test',
            'tanggal_usul' => now(),
            'catatan_keputusan' => 'Ditolak',
            'tanggal_keputusan' => now(),
        ]);

        Livewire::test(IndexUsulan::class)
            ->call('viewCatatan', $usulan->id)
            ->assertSet('showCatatanModal', false)  // Modal should NOT open
            ->assertSee('Usulan tidak ditemukan');  // Should show error message
    }

    /**
     * Test: Empty state displayed when no proposals exist
     *
     * Validates: Requirements 10.1
     */
    public function test_empty_state_displayed_when_no_proposals(): void
    {
        $this->actingAs($this->guru, 'guru');

        Livewire::test(IndexUsulan::class)
            ->assertSee('Belum ada usulan kenaikan tingkat', false)
            ->assertSee('Ajukan Kenaikan Tingkat', false);
    }

    /**
     * Test: Pagination works correctly
     *
     * Validates: Requirements 10.1
     */
    public function test_pagination_works_correctly(): void
    {
        $this->actingAs($this->guru, 'guru');

        // Create 15 proposals (more than 10 per page)
        $siswa = Siswa::factory()->count(15)->create([
            'kelompok_id' => $this->kelompok->id,
        ]);

        foreach ($siswa as $s) {
            UsulanKenaikanTingkat::factory()->create([
                'siswa_id' => $s->id,
                'guru_pengusul_id' => $this->guru->id,
                'tingkat_asal' => TingkatKelompok::DASAR,
                'tingkat_tujuan' => TingkatKelompok::MENENGAH,
                'kelompok_asal_id' => $this->kelompok->id,
                'status_usulan' => StatusUsulan::DIAJUKAN,
                'alasan' => 'Test',
                'tanggal_usul' => now(),
            ]);
        }

        $component = Livewire::test(IndexUsulan::class);

        // Should show pagination
        $proposals = $component->instance()->proposals;
        $this->assertEquals(10, $proposals->perPage());
        $this->assertEquals(15, $proposals->total());
        $this->assertEquals(2, $proposals->lastPage());
    }

    /**
     * Test: Status filter via query parameter works correctly
     *
     * Validates: Requirement 10.3 (Dashboard widget links with status filter)
     */
    public function test_status_filter_via_query_parameter(): void
    {
        $this->actingAs($this->guru, 'guru');

        $siswa1 = Siswa::factory()->create([
            'nama' => 'Siswa Diajukan',
            'kelompok_id' => $this->kelompok->id,
        ]);

        $siswa2 = Siswa::factory()->create([
            'nama' => 'Siswa Disetujui',
            'kelompok_id' => $this->kelompok->id,
        ]);

        $siswa3 = Siswa::factory()->create([
            'nama' => 'Siswa Ditolak',
            'kelompok_id' => $this->kelompok->id,
        ]);

        // Create proposals with different statuses
        UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa1->id,
            'guru_pengusul_id' => $this->guru->id,
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'kelompok_asal_id' => $this->kelompok->id,
            'status_usulan' => StatusUsulan::DIAJUKAN,
            'alasan' => 'Test',
            'tanggal_usul' => now(),
        ]);

        UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa2->id,
            'guru_pengusul_id' => $this->guru->id,
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'kelompok_asal_id' => $this->kelompok->id,
            'status_usulan' => StatusUsulan::DISETUJUI,
            'alasan' => 'Test',
            'tanggal_usul' => now(),
            'tanggal_keputusan' => now(),
        ]);

        UsulanKenaikanTingkat::factory()->create([
            'siswa_id' => $siswa3->id,
            'guru_pengusul_id' => $this->guru->id,
            'tingkat_asal' => TingkatKelompok::DASAR,
            'tingkat_tujuan' => TingkatKelompok::MENENGAH,
            'kelompok_asal_id' => $this->kelompok->id,
            'status_usulan' => StatusUsulan::DITOLAK,
            'alasan' => 'Test',
            'tanggal_usul' => now(),
            'tanggal_keputusan' => now(),
            'catatan_keputusan' => 'Belum memenuhi syarat',
        ]);

        // Test filter by 'diajukan' status
        Livewire::test(IndexUsulan::class)
            ->set('status', 'diajukan')
            ->assertSee('Siswa Diajukan')
            ->assertDontSee('Siswa Disetujui')
            ->assertDontSee('Siswa Ditolak');

        // Test filter by 'disetujui' status
        Livewire::test(IndexUsulan::class)
            ->set('status', 'disetujui')
            ->assertSee('Siswa Disetujui')
            ->assertDontSee('Siswa Diajukan')
            ->assertDontSee('Siswa Ditolak');

        // Test filter by 'ditolak' status
        Livewire::test(IndexUsulan::class)
            ->set('status', 'ditolak')
            ->assertSee('Siswa Ditolak')
            ->assertDontSee('Siswa Diajukan')
            ->assertDontSee('Siswa Disetujui');

        // Test no filter shows all
        Livewire::test(IndexUsulan::class)
            ->assertSee('Siswa Diajukan')
            ->assertSee('Siswa Disetujui')
            ->assertSee('Siswa Ditolak');
    }
}
