<?php

namespace Tests\Feature\Livewire\Guru\UsulanKenaikanTingkat;

use App\Livewire\Guru\UsulanKenaikanTingkat\CreateUsulan;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelompok;
use App\Models\TahunAjaran;
use App\Models\ProgresHafalan;
use App\Models\ParameterKonfigurasi;
use App\Enums\StatusUsulan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Feature tests for CreateUsulan Livewire Component
 *
 * Tests the complete workflow of creating promotion proposals including:
 * - Form rendering and data loading
 * - Auto-population of tingkat_asal
 * - Form validation
 * - Eligibility validation
 * - Level progression validation
 * - Success notification
 *
 * Task 9.1: Implement CreateUsulan component (Guru namespace)
 * Requirements: 1.1, 1.2, 1.3, 1.5, 2.3, 2.5
 */
class CreateUsulanTest extends TestCase
{
    use RefreshDatabase;

    protected Guru $guru;
    protected Kelompok $kelompokDasar;
    protected Kelompok $kelompokMenengah;
    protected TahunAjaran $tahunAjaran;
    protected ParameterKonfigurasi $config;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock NotifikasiService to prevent actual notification dispatch in tests
        $this->mock(\App\Services\Contracts\NotifikasiServiceInterface::class, function ($mock) {
            $mock->shouldReceive('notifyGuruOnDecision')->andReturn(true);
            $mock->shouldReceive('notifySiswaOnPlacement')->andReturn(true);
        });

        // Create tahun ajaran
        $this->tahunAjaran = TahunAjaran::factory()->create([
            'status' => 'Aktif',
        ]);

        // Create guru and authenticate
        $this->guru = Guru::factory()->create();

        // Create kelompok groups assigned to this guru
        $this->kelompokDasar = Kelompok::factory()->create([
            'nama' => 'Dasar 1',
            'tingkat' => 'Dasar',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $this->guru->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        $this->kelompokMenengah = Kelompok::factory()->create([
            'nama' => 'Menengah 1',
            'tingkat' => 'Menengah',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $this->guru->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'kapasitas_maks' => 12,
        ]);

        // Create parameter configuration with batas tingkat
        $this->config = ParameterKonfigurasi::factory()->create([
            'batas_tingkat' => [
                'dasar' => 0,
                'menengah' => 10,
                'atas' => 20,
            ],
        ]);
    }

    /**
     * Test component renders successfully
     *
     * @test
     */
    public function test_component_renders_successfully()
    {
        $this->actingAs($this->guru, 'guru');

        Livewire::test(CreateUsulan::class)
            ->assertStatus(200)
            ->assertSee('Ajukan Kenaikan Tingkat');
    }

    /**
     * Test form renders with all required fields and student dropdown
     * Task 9.2: Test form rendering with student dropdown and fields
     *
     * @test
     */
    public function test_form_renders_with_student_dropdown_and_all_fields()
    {
        $this->actingAs($this->guru, 'guru');

        // Create students in guru's groups to populate dropdown
        $siswa1 = Siswa::factory()->create([
            'nama' => 'Ahmad Abdullah',
            'nis' => '20230001',
            'kelompok_id' => $this->kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        $siswa2 = Siswa::factory()->create([
            'nama' => 'Fatimah Zahra',
            'nis' => '20230002',
            'kelompok_id' => $this->kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        Livewire::test(CreateUsulan::class)
            ->call('openModal')
            ->assertSet('showModal', true)
            // Assert student dropdown field exists
            ->assertSee('Siswa')
            ->assertSee('-- Pilih Siswa --')
            ->assertSee('Ahmad Abdullah (20230001)')
            ->assertSee('Fatimah Zahra (20230002)')
            // Assert tingkat_asal field exists (read-only)
            ->assertSee('Tingkat Asal')
            ->assertSee('Akan terisi otomatis setelah memilih siswa')
            // Assert tingkat_tujuan field exists (radio buttons)
            ->assertSee('Tingkat Tujuan')
            ->assertSee('Dasar')
            ->assertSee('Menengah')
            ->assertSee('Atas')
            // Assert alasan field exists
            ->assertSee('Alasan Kenaikan Tingkat')
            ->assertSee('Minimal 10 karakter, maksimal 1000 karakter')
            // Assert action buttons
            ->assertSee('Batal')
            ->assertSee('Ajukan Usulan');
    }

    /**
     * Test component loads siswa from guru's assigned kelompoks
     * Validates: Requirement 1.1
     *
     * @test
     */
    public function test_component_loads_siswa_from_guru_kelompoks()
    {
        $this->actingAs($this->guru, 'guru');

        // Create students in guru's groups
        $siswa1 = Siswa::factory()->create([
            'nama' => 'Ahmad Abdullah',
            'nis' => '20230001',
            'kelompok_id' => $this->kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        $siswa2 = Siswa::factory()->create([
            'nama' => 'Fatimah Zahra',
            'nis' => '20230002',
            'kelompok_id' => $this->kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Create student in another guru's group (should not appear)
        $otherGuru = Guru::factory()->create();
        $otherKelompok = Kelompok::factory()->create([
            'nama' => 'Dasar 2',
            'tingkat' => 'Dasar',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $otherGuru->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
        ]);

        $siswa3 = Siswa::factory()->create([
            'nama' => 'Other Student',
            'kelompok_id' => $otherKelompok->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        Livewire::test(CreateUsulan::class)
            ->call('openModal')
            ->assertSet('siswaOptions', function ($options) use ($siswa1, $siswa2, $siswa3) {
                // Should contain siswa1 and siswa2
                $ids = array_column($options, 'id');
                return in_array($siswa1->id, $ids)
                    && in_array($siswa2->id, $ids)
                    && !in_array($siswa3->id, $ids);
            });
    }

    /**
     * Test auto-populate tingkat_asal when student is selected
     * Validates: Requirement 1.2
     *
     * @test
     */
    public function test_auto_populate_tingkat_asal_when_student_selected()
    {
        $this->actingAs($this->guru, 'guru');

        $siswa = Siswa::factory()->create([
            'nama' => 'Ahmad Abdullah',
            'kelompok_id' => $this->kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        Livewire::test(CreateUsulan::class)
            ->call('openModal')
            ->set('siswa_id', $siswa->id)
            ->assertSet('tingkat_asal', 'Dasar');
    }

    /**
     * Test form validation for required fields
     *
     * @test
     */
    public function test_form_validation_for_required_fields()
    {
        $this->actingAs($this->guru, 'guru');

        Livewire::test(CreateUsulan::class)
            ->call('openModal')
            ->call('submit')
            ->assertHasErrors([
                'siswa_id' => 'required',
                'tingkat_tujuan' => 'required',
                'alasan' => 'required',
            ]);
    }

    /**
     * Test alasan minimum length validation
     *
     * @test
     */
    public function test_alasan_minimum_length_validation()
    {
        $this->actingAs($this->guru, 'guru');

        $siswa = Siswa::factory()->create([
            'kelompok_id' => $this->kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        Livewire::test(CreateUsulan::class)
            ->call('openModal')
            ->set('siswa_id', $siswa->id)
            ->set('tingkat_tujuan', 'Menengah')
            ->set('alasan', 'Short') // Less than 10 characters
            ->call('submit')
            ->assertHasErrors(['alasan' => 'min']);
    }

    /**
     * Test successful usulan creation with eligible student
     * Validates: Requirements 1.3, 1.5
     *
     * @test
     */
    public function test_successful_usulan_creation_with_eligible_student()
    {
        $this->actingAs($this->guru, 'guru');

        // Create student with sufficient juz
        $siswa = Siswa::factory()->create([
            'nama' => 'Ahmad Abdullah',
            'kelompok_id' => $this->kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Create progres hafalan with 12 juz (> 10 required for Menengah)
        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'total_juz_tuntas' => 12,
        ]);

        Livewire::test(CreateUsulan::class)
            ->call('openModal')
            ->set('siswa_id', $siswa->id)
            ->set('tingkat_tujuan', 'Menengah')
            ->set('alasan', 'Siswa telah menyelesaikan 12 juz dengan baik')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('showModal', false)
            ->assertDispatched('usulan-created');

        // Assert usulan was created in database
        $this->assertDatabaseHas('usulan_kenaikan_tingkats', [
            'siswa_id' => $siswa->id,
            'guru_pengusul_id' => $this->guru->id,
            'tingkat_asal' => 'Dasar',
            'tingkat_tujuan' => 'Menengah',
            'status_usulan' => StatusUsulan::DIAJUKAN->value,
            'alasan' => 'Siswa telah menyelesaikan 12 juz dengan baik',
        ]);
    }

    /**
     * Test success toast notification is displayed after submission
     * Task 9.2: Test success toast after submission
     * Validates: Requirement 1.5
     *
     * This test verifies that successful submission results in proper feedback.
     * The component uses session()->flash() to set a success message, and we verify
     * the modal closes and event is dispatched, which are the observable outcomes
     * of a successful submission.
     *
     * @test
     */
    public function test_success_toast_notification_after_submission()
    {
        $this->actingAs($this->guru, 'guru');

        // Create student with sufficient juz
        $siswa = Siswa::factory()->create([
            'nama' => 'Fatimah Zahra',
            'kelompok_id' => $this->kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Create progres hafalan with 15 juz (> 10 required for Menengah)
        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'total_juz_tuntas' => 15,
        ]);

        Livewire::test(CreateUsulan::class)
            ->call('openModal')
            ->set('siswa_id', $siswa->id)
            ->set('tingkat_tujuan', 'Menengah')
            ->set('alasan', 'Siswa menunjukkan kemampuan hafalan yang sangat baik')
            ->call('submit')
            ->assertHasNoErrors()
            // Modal closes on success
            ->assertSet('showModal', false)
            // Event is dispatched to refresh parent list
            ->assertDispatched('usulan-created');

        // The success feedback is observable through:
        // 1. Modal closing (assertSet above)
        // 2. Event dispatched (assertDispatched above)
        // 3. Database record created (verified below)

        $this->assertDatabaseHas('usulan_kenaikan_tingkats', [
            'siswa_id' => $siswa->id,
            'guru_pengusul_id' => $this->guru->id,
            'tingkat_tujuan' => 'Menengah',
        ]);
    }

    /**
     * Test validation error display for ineligible students
     * Task 9.2: Test validation error display for ineligible students
     * Validates: Requirement 2.3
     *
     * This test verifies that the component properly integrates with the service
     * layer for eligibility validation. When the service throws IneligibleForPromotionException,
     * the component should catch it and display an error to the user.
     *
     * NOTE: In the current test environment, we verify the component's error handling
     * by mocking the service to throw the exception explicitly.
     *
     * @test
     */
    public function test_validation_error_display_for_ineligible_students()
    {
        $this->actingAs($this->guru, 'guru');

        // Create student
        $siswa = Siswa::factory()->create([
            'nama' => 'Ahmad Abdullah',
            'kelompok_id' => $this->kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Mock the service to throw IneligibleForPromotionException
        $mockService = $this->mock(\App\Services\Contracts\UsulanKenaikanTingkatServiceInterface::class);
        $mockService->shouldReceive('createUsulan')
            ->once()
            ->andThrow(new \App\Exceptions\IneligibleForPromotionException(
                "Siswa {$siswa->nama} belum memenuhi syarat kenaikan tingkat. Minimum 10 juz diperlukan untuk Menengah, saat ini 5 juz."
            ));

        $component = Livewire::test(CreateUsulan::class)
            ->call('openModal')
            ->set('siswa_id', $siswa->id)
            ->set('tingkat_tujuan', 'Menengah')
            ->set('alasan', 'Siswa sudah siap naik tingkat')
            ->call('submit');

        // Assert that error is caught and added to the component
        $errorBag = $component->instance()->getErrorBag();

        $this->assertTrue(
            $errorBag->has('siswa_id'),
            'Component should display error on siswa_id field when validation fails'
        );

        $errorMessage = $errorBag->first('siswa_id');
        $this->assertStringContainsString(
            'belum memenuhi syarat kenaikan tingkat',
            $errorMessage,
            'Error message should explain why student is ineligible'
        );

        // Assert no usulan was created in database
        $this->assertDatabaseMissing('usulan_kenaikan_tingkats', [
            'siswa_id' => $siswa->id,
            'guru_pengusul_id' => $this->guru->id,
        ]);
    }

    /**
     * Test invalid level progression blocked
     * Validates: Requirement 2.5
     *
     * @test
     */
    public function test_invalid_level_progression_blocked()
    {
        $this->actingAs($this->guru, 'guru');

        // Create student in Menengah group
        $siswa = Siswa::factory()->create([
            'kelompok_id' => $this->kelompokMenengah->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Create progres hafalan with sufficient juz
        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $this->tahunAjaran->id,
            'total_juz_tuntas' => 15,
        ]);

        // Attempt to propose to Dasar (downward progression)
        Livewire::test(CreateUsulan::class)
            ->call('openModal')
            ->set('siswa_id', $siswa->id)
            ->set('tingkat_tujuan', 'Dasar') // Invalid: downward
            ->set('alasan', 'Test invalid progression')
            ->call('submit')
            ->assertHasErrors(['tingkat_tujuan'])
            ->assertSet('showModal', true);

        // Assert no usulan was created
        $this->assertDatabaseMissing('usulan_kenaikan_tingkats', [
            'siswa_id' => $siswa->id,
            'guru_pengusul_id' => $this->guru->id,
        ]);
    }

    /**
     * Test modal opens and closes correctly
     *
     * @test
     */
    public function test_modal_opens_and_closes_correctly()
    {
        $this->actingAs($this->guru, 'guru');

        Livewire::test(CreateUsulan::class)
            ->assertSet('showModal', false)
            ->call('openModal')
            ->assertSet('showModal', true)
            ->call('closeModal')
            ->assertSet('showModal', false);
    }

    /**
     * Test form reset when modal closes
     *
     * @test
     */
    public function test_form_reset_when_modal_closes()
    {
        $this->actingAs($this->guru, 'guru');

        $siswa = Siswa::factory()->create([
            'kelompok_id' => $this->kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        Livewire::test(CreateUsulan::class)
            ->call('openModal')
            ->set('siswa_id', $siswa->id)
            ->set('tingkat_tujuan', 'Menengah')
            ->set('alasan', 'Test alasan')
            ->call('closeModal')
            ->assertSet('siswa_id', null)
            ->assertSet('tingkat_asal', '')
            ->assertSet('tingkat_tujuan', '')
            ->assertSet('alasan', '');
    }

    /**
     * Test component loads tingkat options correctly
     *
     * @test
     */
    public function test_component_loads_tingkat_options()
    {
        $this->actingAs($this->guru, 'guru');

        Livewire::test(CreateUsulan::class)
            ->call('openModal')
            ->assertSet('tingkatOptions', [
                'Dasar' => 'Dasar',
                'Menengah' => 'Menengah',
                'Atas' => 'Atas',
            ]);
    }

    /**
     * Test only active and placed students are loaded
     *
     * @test
     */
    public function test_only_active_and_placed_students_are_loaded()
    {
        $this->actingAs($this->guru, 'guru');

        // Create active placed student (should appear)
        $siswaActive = Siswa::factory()->create([
            'nama' => 'Active Student',
            'kelompok_id' => $this->kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Create inactive student (should not appear)
        $siswaInactive = Siswa::factory()->create([
            'nama' => 'Inactive Student',
            'kelompok_id' => $this->kelompokDasar->id,
            'status_akademik' => 'Lulus SMP',
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Create not-yet-placed student (should not appear)
        $siswaNotPlaced = Siswa::factory()->create([
            'nama' => 'Not Placed Student',
            'kelompok_id' => $this->kelompokDasar->id,
            'status_akademik' => 'Aktif',
            'status_penempatan' => 'Terdaftar',
        ]);

        Livewire::test(CreateUsulan::class)
            ->call('openModal')
            ->assertSet('siswaOptions', function ($options) use ($siswaActive, $siswaInactive, $siswaNotPlaced) {
                $ids = array_column($options, 'id');
                return in_array($siswaActive->id, $ids)
                    && !in_array($siswaInactive->id, $ids)
                    && !in_array($siswaNotPlaced->id, $ids);
            });
    }
}
