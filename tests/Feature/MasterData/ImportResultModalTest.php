<?php

namespace Tests\Feature\MasterData;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Admin;
use Livewire\Livewire;
use App\Livewire\Admin\MasterData\Siswa\ImportSiswa;
use App\Livewire\Admin\MasterData\Guru\ImportGuru;

/**
 * Test: Import Result Modal Component
 *
 * Task 17.3: Implement import result modal
 * Requirements: 10.8, 10.9
 *
 * Verifies that the reusable import result modal displays correctly
 * with summary statistics, error details, and action buttons.
 */
class ImportResultModalTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create();
    }

    /** @test */
    public function import_siswa_shows_result_modal_on_success()
    {
        $this->actingAs($this->admin, 'admin');

        $component = Livewire::test(ImportSiswa::class)
            ->set('showResultModal', true)
            ->set('importResult', [
                'successCount' => 5,
                'errorCount' => 0,
                'errors' => []
            ]);

        // Modal should be shown
        $component->assertSee('Hasil Import Data Siswa');
        $component->assertSee('Total Baris');
        $component->assertSee('5'); // success count
        $component->assertSee('Berhasil');
        $component->assertSee('Tingkat Keberhasilan');
        $component->assertSee('100%'); // success rate
    }

    /** @test */
    public function import_siswa_shows_error_details_in_table_format()
    {
        $this->actingAs($this->admin, 'admin');

        $component = Livewire::test(ImportSiswa::class)
            ->set('showResultModal', true)
            ->set('importResult', [
                'successCount' => 3,
                'errorCount' => 2,
                'errors' => [
                    [
                        'row' => 5,
                        'data' => ['nis' => '12345', 'nama' => 'Ahmad'],
                        'message' => 'NIS sudah terdaftar'
                    ],
                    [
                        'row' => 8,
                        'data' => ['nis' => '12346', 'nama' => 'Budi'],
                        'message' => 'NISN tidak valid'
                    ]
                ]
            ]);

        // Should show error table headers
        $component->assertSee('Detail Error');
        $component->assertSee('Baris');
        $component->assertSee('NIS');
        $component->assertSee('Nama');
        $component->assertSee('Pesan Error');

        // Should show error details
        $component->assertSee('12345');
        $component->assertSee('Ahmad');
        $component->assertSee('NIS sudah terdaftar');
        $component->assertSee('12346');
        $component->assertSee('Budi');
        $component->assertSee('NISN tidak valid');

        // Should show download button
        $component->assertSee('Download Laporan Error');
    }

    /** @test */
    public function import_guru_shows_result_modal_with_correct_labels()
    {
        $this->actingAs($this->admin, 'admin');

        $component = Livewire::test(ImportGuru::class)
            ->set('showResultModal', true)
            ->set('importResult', [
                'successCount' => 4,
                'errorCount' => 1,
                'errors' => [
                    [
                        'row' => 3,
                        'data' => ['niy' => 'G001', 'nama' => 'Pak Ahmad'],
                        'message' => 'NIY sudah terdaftar'
                    ]
                ]
            ]);

        // Should show guru-specific labels
        $component->assertSee('Hasil Import Data Guru');
        $component->assertSee('NIY'); // not NIS
        $component->assertSee('G001');
        $component->assertSee('Pak Ahmad');
        $component->assertSee('Lihat Daftar Guru');
    }

    /** @test */
    public function modal_shows_success_message_when_no_errors()
    {
        $this->actingAs($this->admin, 'admin');

        $component = Livewire::test(ImportSiswa::class)
            ->set('showResultModal', true)
            ->set('importResult', [
                'successCount' => 10,
                'errorCount' => 0,
                'errors' => []
            ]);

        // Should show success message
        $component->assertSee('Import Berhasil!');
        $component->assertSee('Semua data Siswa berhasil diimport ke sistem tanpa error');

        // Should NOT show error section
        $component->assertDontSee('Detail Error');
        $component->assertDontSee('Download Laporan Error');
    }

    /** @test */
    public function modal_calculates_success_rate_correctly()
    {
        $this->actingAs($this->admin, 'admin');

        $component = Livewire::test(ImportSiswa::class)
            ->set('showResultModal', true)
            ->set('importResult', [
                'successCount' => 7,
                'errorCount' => 3,
                'errors' => []
            ]);

        // 7 success out of 10 total = 70%
        $component->assertSee('10'); // total
        $component->assertSee('7'); // success
        $component->assertSee('3'); // error
        $component->assertSee('70%'); // success rate
    }

    /** @test */
    public function modal_can_be_closed()
    {
        $this->actingAs($this->admin, 'admin');

        $component = Livewire::test(ImportSiswa::class)
            ->set('showResultModal', true)
            ->set('importResult', [
                'successCount' => 5,
                'errorCount' => 0,
                'errors' => []
            ])
            ->call('closeResultModal');

        // Modal should be closed
        $component->assertSet('showResultModal', false);
    }

    /** @test */
    public function import_lagi_button_resets_component()
    {
        $this->actingAs($this->admin, 'admin');

        $component = Livewire::test(ImportSiswa::class)
            ->set('showResultModal', true)
            ->set('importResult', [
                'successCount' => 5,
                'errorCount' => 0,
                'errors' => []
            ])
            ->call('importLagi');

        // Should reset state
        $component->assertSet('showResultModal', false);
        $component->assertSet('importResult', null);
        $component->assertSet('file', null);
    }

    /** @test */
    public function modal_shows_action_buttons_correctly()
    {
        $this->actingAs($this->admin, 'admin');

        $component = Livewire::test(ImportSiswa::class)
            ->set('showResultModal', true)
            ->set('importResult', [
                'successCount' => 5,
                'errorCount' => 2,
                'errors' => []
            ]);

        // Should show all action buttons
        $component->assertSee('Lihat Daftar Siswa'); // Link to index
        $component->assertSee('Import Lagi'); // Reset button
        $component->assertSee('Tutup'); // Close button
    }

    /** @test */
    public function modal_does_not_show_index_link_when_no_success()
    {
        $this->actingAs($this->admin, 'admin');

        $component = Livewire::test(ImportSiswa::class)
            ->set('showResultModal', true)
            ->set('importResult', [
                'successCount' => 0,
                'errorCount' => 5,
                'errors' => []
            ]);

        // Should NOT show link to index when no successful imports
        $component->assertDontSee('Lihat Daftar Siswa');

        // But should still show other buttons
        $component->assertSee('Import Lagi');
        $component->assertSee('Tutup');
    }
}
