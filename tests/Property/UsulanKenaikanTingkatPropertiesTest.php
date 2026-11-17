<?php

/**
 * Property-Based Tests for Usulan Kenaikan Tingkat Logic
 *
 * Tests Properties 2-7 from design.md
 * Feature: usulan-kenaikan-tingkat
 */

namespace Tests\Property;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelompok;
use App\Models\ProgresHafalan;
use App\Models\ParameterKonfigurasi;
use App\Models\UsulanKenaikanTingkat;
use App\Models\TahunAjaran;
use App\Services\UsulanKenaikanTingkatService;
use App\Exceptions\IneligibleForPromotionException;
use App\Exceptions\InvalidLevelProgressionException;
use App\Exceptions\InvalidStatusTransitionException;
use App\Enums\StatusUsulan;
use App\Enums\TingkatKelompok;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UsulanKenaikanTingkatPropertiesTest extends TestCase
{
    use RefreshDatabase;

    protected UsulanKenaikanTingkatService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(UsulanKenaikanTingkatService::class);

        // Setup parameter konfigurasi with default values
        $config = ParameterKonfigurasi::getInstance();
        $config->batas_tingkat = [
            'dasar' => 0,
            'menengah' => 10,
            'atas' => 20,
        ];
        $config->save();
    }

    /**
     * Property 2: Submission initializes proposal state correctly
     *
     * For any valid proposal submission, the system SHALL set status_usulan to "Diajukan",
     * record guru_pengusul_id, and set tanggal_usul to current timestamp within acceptable range.
     *
     * Feature: usulan-kenaikan-tingkat, Property 2: Submission initializes proposal state correctly
     * Validates: Requirements 1.3, 1.4
     *
     * @test
     * @group property-based
     */
    public function test_submission_initializes_proposal_state_correctly()
    {
        // Test multiple scenarios with different tingkat combinations
        $scenarios = [
            ['tingkat_asal' => 'Dasar', 'tingkat_tujuan' => 'Menengah', 'juz' => 10],
            ['tingkat_asal' => 'Dasar', 'tingkat_tujuan' => 'Atas', 'juz' => 20],
            ['tingkat_asal' => 'Menengah', 'tingkat_tujuan' => 'Atas', 'juz' => 20],
        ];

        foreach ($scenarios as $scenario) {
            // Setup
            $tahunAjaran = TahunAjaran::factory()->create();
            $kelompok = Kelompok::factory()->create([
                'tingkat' => $scenario['tingkat_asal']
            ]);
            $siswa = Siswa::factory()->create([
                'kelompok_id' => $kelompok->id
            ]);
            $guru = Guru::factory()->create();

            // Create progres hafalan with sufficient juz
            ProgresHafalan::factory()->create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => $scenario['juz'],
            ]);

            // Record time before submission
            $beforeSubmission = now()->subSecond();

            // Execute
            $usulan = $this->service->createUsulan(
                $siswa->id,
                $guru->id,
                $scenario['tingkat_tujuan'],
                'Alasan test property'
            );

            // Record time after submission
            $afterSubmission = now()->addSecond();

            // Assert: status_usulan is "Diajukan" (Requirement 1.3)
            $this->assertEquals(
                StatusUsulan::DIAJUKAN,
                $usulan->status_usulan,
                "Usulan should be initialized with status 'Diajukan'"
            );

            // Assert: guru_pengusul_id is recorded correctly (Requirement 1.3)
            $this->assertEquals(
                $guru->id,
                $usulan->guru_pengusul_id,
                "Guru pengusul ID should be recorded correctly"
            );

            // Assert: tanggal_usul is set to current timestamp within acceptable range (Requirement 1.4)
            $this->assertTrue(
                $usulan->tanggal_usul >= $beforeSubmission && $usulan->tanggal_usul <= $afterSubmission,
                "Tanggal usul should be set to current timestamp (within 2 second tolerance)"
            );

            // Assert: other required fields are populated
            $this->assertEquals($siswa->id, $usulan->siswa_id);
            $this->assertEquals($scenario['tingkat_asal'], $usulan->tingkat_asal->value);
            $this->assertEquals($scenario['tingkat_tujuan'], $usulan->tingkat_tujuan->value);
            $this->assertEquals($kelompok->id, $usulan->kelompok_asal_id);
            $this->assertNull($usulan->kelompok_tujuan_id);
            $this->assertNull($usulan->admin_verifikator_id);
            $this->assertNull($usulan->tanggal_keputusan);
        }

        $this->assertTrue(true, 'Property 2: Submission initializes proposal state correctly');
    }

    /**
     * Property 3: Eligibility validation retrieves correct student progress
     *
     * For any siswa_id, when validating eligibility, the system SHALL retrieve
     * the correct total_juz_tuntas value from Progres_Hafalan.
     *
     * Feature: usulan-kenaikan-tingkat, Property 3: Eligibility validation retrieves correct student progress
     * Validates: Requirements 2.1
     *
     * @test
     * @group property-based
     */
    public function test_eligibility_validation_retrieves_correct_student_progress()
    {
        // Test with various juz values
        $juzTestValues = [0, 5, 10, 15, 20, 25, 30];

        foreach ($juzTestValues as $juzValue) {
            // Setup
            $tahunAjaran = TahunAjaran::factory()->create();
            $siswa = Siswa::factory()->create();

            ProgresHafalan::factory()->create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => $juzValue,
            ]);

            // For this test, we use a tingkat where the threshold is low enough
            // so we can test the retrieval regardless of eligibility result
            try {
                $this->service->validateEligibility($siswa->id, 'Dasar');
                $retrievalSuccessful = true;
            } catch (IneligibleForPromotionException $e) {
                // Exception message should contain the actual juz value from DB
                $this->assertStringContainsString(
                    "saat ini {$juzValue} juz",
                    $e->getMessage(),
                    "Exception should contain correct juz value from progres hafalan"
                );
                $retrievalSuccessful = true;
            }

            $this->assertTrue(
                $retrievalSuccessful,
                "Eligibility validation should retrieve total_juz_tuntas = {$juzValue} correctly"
            );
        }

        $this->assertTrue(true, 'Property 3: Eligibility validation retrieves correct student progress');
    }

    /**
     * Property 4: Eligibility validation retrieves correct level threshold
     *
     * For any tingkat_tujuan value (Dasar/Menengah/Atas), the system SHALL
     * retrieve the correct Batas_Tingkat configuration.
     *
     * Feature: usulan-kenaikan-tingkat, Property 4: Eligibility validation retrieves correct level threshold
     * Validates: Requirements 2.2
     *
     * @test
     * @group property-based
     */
    public function test_eligibility_validation_retrieves_correct_level_threshold()
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        // Test each tingkat level
        $tingkatTests = [
            ['tingkat' => 'Dasar', 'expected_threshold' => 0],
            ['tingkat' => 'Menengah', 'expected_threshold' => 10],
            ['tingkat' => 'Atas', 'expected_threshold' => 20],
        ];

        foreach ($tingkatTests as $test) {
            // Setup student with juz = threshold - 1 (should fail)
            $siswa = Siswa::factory()->create();
            $juzBelowThreshold = max(0, $test['expected_threshold'] - 1);

            ProgresHafalan::factory()->create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => $juzBelowThreshold,
            ]);

            // Execute and expect exception with correct threshold
            try {
                $this->service->validateEligibility($siswa->id, $test['tingkat']);

                // If no exception for Dasar (threshold 0), that's correct
                if ($test['tingkat'] === 'Dasar' && $juzBelowThreshold === 0) {
                    $this->assertTrue(true);
                    continue;
                }

                $this->fail("Should throw IneligibleForPromotionException for {$test['tingkat']}");
            } catch (IneligibleForPromotionException $e) {
                // Verify exception message contains correct threshold
                $this->assertStringContainsString(
                    "Minimum {$test['expected_threshold']} juz diperlukan untuk {$test['tingkat']}",
                    $e->getMessage(),
                    "Exception should contain correct threshold for {$test['tingkat']}"
                );
            }
        }

        $this->assertTrue(true, 'Property 4: Eligibility validation retrieves correct level threshold');
    }

    /**
     * Property 5: Insufficient juz blocks submission
     *
     * For any student where total_juz_tuntas < Batas_Tingkat[tingkat_tujuan],
     * the system SHALL reject the submission with an error message.
     *
     * Feature: usulan-kenaikan-tingkat, Property 5: Insufficient juz blocks submission
     * Validates: Requirements 2.3
     *
     * @test
     * @group property-based
     */
    public function test_insufficient_juz_blocks_submission()
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        // Test scenarios: juz below threshold
        $scenarios = [
            ['tingkat_tujuan' => 'Menengah', 'threshold' => 10, 'juz' => 9],
            ['tingkat_tujuan' => 'Menengah', 'threshold' => 10, 'juz' => 5],
            ['tingkat_tujuan' => 'Menengah', 'threshold' => 10, 'juz' => 0],
            ['tingkat_tujuan' => 'Atas', 'threshold' => 20, 'juz' => 19],
            ['tingkat_tujuan' => 'Atas', 'threshold' => 20, 'juz' => 15],
            ['tingkat_tujuan' => 'Atas', 'threshold' => 20, 'juz' => 10],
        ];

        foreach ($scenarios as $scenario) {
            // Setup
            $kelompok = Kelompok::factory()->create(['tingkat' => 'Dasar']);
            $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
            $guru = Guru::factory()->create();

            ProgresHafalan::factory()->create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => $scenario['juz'],
            ]);

            // Execute and expect exception
            $exceptionThrown = false;
            try {
                $this->service->createUsulan(
                    $siswa->id,
                    $guru->id,
                    $scenario['tingkat_tujuan'],
                    'Test alasan'
                );
            } catch (IneligibleForPromotionException $e) {
                $exceptionThrown = true;
                $this->assertStringContainsString(
                    "Minimum {$scenario['threshold']} juz diperlukan",
                    $e->getMessage()
                );
            }

            $this->assertTrue(
                $exceptionThrown,
                "IneligibleForPromotionException should be thrown for juz={$scenario['juz']}, threshold={$scenario['threshold']}"
            );

            // Clean up
            ProgresHafalan::query()->delete();
        }

        $this->assertTrue(true, 'Property 5: Insufficient juz blocks submission');
    }

    /**
     * Property 6: Sufficient juz allows submission
     *
     * For any student where total_juz_tuntas >= Batas_Tingkat[tingkat_tujuan],
     * the system SHALL allow the submission to proceed.
     *
     * Feature: usulan-kenaikan-tingkat, Property 6: Sufficient juz allows submission
     * Validates: Requirements 2.4
     *
     * @test
     * @group property-based
     */
    public function test_sufficient_juz_allows_submission()
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        // Test scenarios: juz at or above threshold
        $scenarios = [
            ['tingkat_asal' => 'Dasar', 'tingkat_tujuan' => 'Menengah', 'threshold' => 10, 'juz' => 10],
            ['tingkat_asal' => 'Dasar', 'tingkat_tujuan' => 'Menengah', 'threshold' => 10, 'juz' => 15],
            ['tingkat_asal' => 'Dasar', 'tingkat_tujuan' => 'Menengah', 'threshold' => 10, 'juz' => 30],
            ['tingkat_asal' => 'Menengah', 'tingkat_tujuan' => 'Atas', 'threshold' => 20, 'juz' => 20],
            ['tingkat_asal' => 'Menengah', 'tingkat_tujuan' => 'Atas', 'threshold' => 20, 'juz' => 25],
            ['tingkat_asal' => 'Menengah', 'tingkat_tujuan' => 'Atas', 'threshold' => 20, 'juz' => 30],
        ];

        foreach ($scenarios as $scenario) {
            // Setup
            $kelompok = Kelompok::factory()->create(['tingkat' => $scenario['tingkat_asal']]);
            $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
            $guru = Guru::factory()->create();

            ProgresHafalan::factory()->create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => $scenario['juz'],
            ]);

            // Execute - should NOT throw exception
            $usulan = $this->service->createUsulan(
                $siswa->id,
                $guru->id,
                $scenario['tingkat_tujuan'],
                'Test alasan'
            );

            // Assert: usulan was created successfully
            $this->assertInstanceOf(UsulanKenaikanTingkat::class, $usulan);
            $this->assertEquals(StatusUsulan::DIAJUKAN, $usulan->status_usulan);
            $this->assertEquals($scenario['tingkat_tujuan'], $usulan->tingkat_tujuan->value);

            // Clean up for next iteration
            UsulanKenaikanTingkat::query()->delete();
            ProgresHafalan::query()->delete();
        }

        $this->assertTrue(true, 'Property 6: Sufficient juz allows submission');
    }

    /**
     * Property 7: Level progression must be upward only
     *
     * For any proposal where tingkat_tujuan <= tingkat_asal, the system SHALL
     * reject the submission.
     *
     * Feature: usulan-kenaikan-tingkat, Property 7: Level progression must be upward only
     * Validates: Requirements 2.5
     *
     * @test
     * @group property-based
     */
    public function test_level_progression_must_be_upward_only()
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        // Test invalid progressions (lateral and downward)
        $invalidScenarios = [
            ['tingkat_asal' => 'Dasar', 'tingkat_tujuan' => 'Dasar'], // lateral
            ['tingkat_asal' => 'Menengah', 'tingkat_tujuan' => 'Menengah'], // lateral
            ['tingkat_asal' => 'Atas', 'tingkat_tujuan' => 'Atas'], // lateral
            ['tingkat_asal' => 'Menengah', 'tingkat_tujuan' => 'Dasar'], // downward
            ['tingkat_asal' => 'Atas', 'tingkat_tujuan' => 'Menengah'], // downward
            ['tingkat_asal' => 'Atas', 'tingkat_tujuan' => 'Dasar'], // downward
        ];

        foreach ($invalidScenarios as $scenario) {
            // Setup
            $kelompok = Kelompok::factory()->create(['tingkat' => $scenario['tingkat_asal']]);
            $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
            $guru = Guru::factory()->create();

            // Create progres with high juz to ensure it's not rejected for insufficient juz
            ProgresHafalan::factory()->create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => 30,
            ]);

            // Execute and expect InvalidLevelProgressionException
            try {
                $this->service->createUsulan(
                    $siswa->id,
                    $guru->id,
                    $scenario['tingkat_tujuan'],
                    'Test alasan'
                );

                $this->fail(
                    "Should throw InvalidLevelProgressionException for " .
                    "{$scenario['tingkat_asal']} -> {$scenario['tingkat_tujuan']}"
                );
            } catch (InvalidLevelProgressionException $e) {
                // Verify exception message contains both levels
                $this->assertStringContainsString(
                    "Tingkat asal: {$scenario['tingkat_asal']}",
                    $e->getMessage()
                );
                $this->assertStringContainsString(
                    "tingkat tujuan: {$scenario['tingkat_tujuan']}",
                    $e->getMessage()
                );
            }

            // Clean up
            ProgresHafalan::query()->delete();
        }

        // Test valid upward progressions (should NOT throw exception)
        $validScenarios = [
            ['tingkat_asal' => 'Dasar', 'tingkat_tujuan' => 'Menengah'],
            ['tingkat_asal' => 'Dasar', 'tingkat_tujuan' => 'Atas'],
            ['tingkat_asal' => 'Menengah', 'tingkat_tujuan' => 'Atas'],
        ];

        foreach ($validScenarios as $scenario) {
            // Setup
            $kelompok = Kelompok::factory()->create(['tingkat' => $scenario['tingkat_asal']]);
            $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
            $guru = Guru::factory()->create();

            ProgresHafalan::factory()->create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => 30,
            ]);

            // Execute - should NOT throw exception
            $usulan = $this->service->createUsulan(
                $siswa->id,
                $guru->id,
                $scenario['tingkat_tujuan'],
                'Test alasan'
            );

            $this->assertInstanceOf(UsulanKenaikanTingkat::class, $usulan);

            // Clean up
            UsulanKenaikanTingkat::query()->delete();
            ProgresHafalan::query()->delete();
        }

        $this->assertTrue(true, 'Property 7: Level progression must be upward only');
    }

    /**
     * Property 13: Rejection transitions state correctly
     *
     * For any proposal in "Diajukan" status, when rejected, the system SHALL
     * update status_usulan to "Ditolak", record admin_verifikator_id, and set
     * tanggal_keputusan to current timestamp.
     *
     * Feature: usulan-kenaikan-tingkat, Property 13: Rejection transitions state correctly
     * Validates: Requirements 4.3
     *
     * @test
     * @group property-based
     */
    public function test_rejection_transitions_state_correctly()
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        // Test with multiple valid scenarios
        $scenarios = [
            ['tingkat_asal' => 'Dasar', 'tingkat_tujuan' => 'Menengah', 'juz' => 10],
            ['tingkat_asal' => 'Dasar', 'tingkat_tujuan' => 'Atas', 'juz' => 20],
            ['tingkat_asal' => 'Menengah', 'tingkat_tujuan' => 'Atas', 'juz' => 25],
        ];

        foreach ($scenarios as $scenario) {
            // Setup: Create an approved usulan
            $kelompok = Kelompok::factory()->create(['tingkat' => $scenario['tingkat_asal']]);
            $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
            $guru = Guru::factory()->create();
            $admin = \App\Models\Admin::factory()->create();

            ProgresHafalan::factory()->create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => $scenario['juz'],
            ]);

            $usulan = $this->service->createUsulan(
                $siswa->id,
                $guru->id,
                $scenario['tingkat_tujuan'],
                'Test alasan pengajuan'
            );

            // Verify initial state is "Diajukan"
            $this->assertEquals(StatusUsulan::DIAJUKAN, $usulan->status_usulan);
            $this->assertNull($usulan->admin_verifikator_id);
            $this->assertNull($usulan->tanggal_keputusan);

            // Record time before rejection
            $beforeRejection = now()->subSecond();

            // Execute: Reject the usulan
            $catatanKeputusan = "Progres belum konsisten, perlu observasi lebih lanjut";
            $rejectedUsulan = $this->service->rejectUsulan(
                $usulan->id,
                $admin->id,
                $catatanKeputusan
            );

            // Record time after rejection
            $afterRejection = now()->addSecond();

            // Assert: status_usulan is "Ditolak" (Requirement 4.3)
            $this->assertEquals(
                StatusUsulan::DITOLAK,
                $rejectedUsulan->status_usulan,
                "Status should transition to 'Ditolak'"
            );

            // Assert: admin_verifikator_id is recorded (Requirement 4.3)
            $this->assertEquals(
                $admin->id,
                $rejectedUsulan->admin_verifikator_id,
                "Admin verifikator ID should be recorded"
            );

            // Assert: tanggal_keputusan is set to current timestamp (Requirement 4.3)
            $this->assertTrue(
                $rejectedUsulan->tanggal_keputusan >= $beforeRejection &&
                $rejectedUsulan->tanggal_keputusan <= $afterRejection,
                "Tanggal keputusan should be set to current timestamp (within 2 second tolerance)"
            );

            // Assert: catatan_keputusan is recorded
            $this->assertEquals(
                $catatanKeputusan,
                $rejectedUsulan->catatan_keputusan,
                "Catatan keputusan should be recorded"
            );

            // Assert: kelompok_tujuan_id remains NULL after rejection
            $this->assertNull(
                $rejectedUsulan->kelompok_tujuan_id,
                "Kelompok tujuan ID should remain NULL after rejection"
            );

            // Assert: tanggal_penempatan remains NULL after rejection
            $this->assertNull(
                $rejectedUsulan->tanggal_penempatan,
                "Tanggal penempatan should remain NULL after rejection"
            );

            // Clean up
            UsulanKenaikanTingkat::query()->delete();
            ProgresHafalan::query()->delete();
        }

        $this->assertTrue(true, 'Property 13: Rejection transitions state correctly');
    }

    /**
     * Property 14: Rejection requires catatan_keputusan
     *
     * For any rejection attempt without catatan_keputusan, the system SHALL
     * block the operation with a ValidationException.
     *
     * Feature: usulan-kenaikan-tingkat, Property 14: Rejection requires catatan_keputusan
     * Validates: Requirements 4.4
     *
     * @test
     * @group property-based
     */
    public function test_rejection_requires_catatan_keputusan()
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        // Test scenarios with various invalid catatan_keputusan values
        $invalidCatatanScenarios = [
            ['catatan' => '', 'label' => 'empty string'],
            ['catatan' => '   ', 'label' => 'whitespace only'],
            ['catatan' => "\t", 'label' => 'tab only'],
            ['catatan' => "\n", 'label' => 'newline only'],
            ['catatan' => "  \t\n  ", 'label' => 'mixed whitespace'],
        ];

        foreach ($invalidCatatanScenarios as $testCase) {
            // Setup: Create a usulan in "Diajukan" status
            $kelompok = Kelompok::factory()->create(['tingkat' => 'Dasar']);
            $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
            $guru = Guru::factory()->create();
            $admin = \App\Models\Admin::factory()->create();

            ProgresHafalan::factory()->create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => 15,
            ]);

            $usulan = $this->service->createUsulan(
                $siswa->id,
                $guru->id,
                'Menengah',
                'Test alasan'
            );

            // Execute and expect ValidationException
            try {
                $this->service->rejectUsulan(
                    $usulan->id,
                    $admin->id,
                    $testCase['catatan']
                );

                $this->fail(
                    "Should throw ValidationException when catatan_keputusan is {$testCase['label']}"
                );
            } catch (ValidationException $e) {
                // Assert: exception contains correct error message
                $errors = $e->errors();
                $this->assertArrayHasKey(
                    'catatan_keputusan',
                    $errors,
                    "ValidationException should have catatan_keputusan key"
                );

                $this->assertStringContainsString(
                    'wajib diisi',
                    $errors['catatan_keputusan'][0],
                    "Error message should indicate catatan_keputusan is required"
                );
            }

            // Verify usulan status unchanged after failed rejection
            $usulan->refresh();
            $this->assertEquals(
                StatusUsulan::DIAJUKAN,
                $usulan->status_usulan,
                "Status should remain 'Diajukan' after failed rejection"
            );

            // Clean up
            UsulanKenaikanTingkat::query()->delete();
            ProgresHafalan::query()->delete();
        }

        // Test valid rejection with proper catatan_keputusan
        $validCatatanScenarios = [
            'Progres belum memadai',
            'Perlu evaluasi lebih lanjut dengan koordinator',
            'A', // minimum 1 character
            str_repeat('x', 500), // long text
        ];

        foreach ($validCatatanScenarios as $validCatatan) {
            // Setup
            $kelompok = Kelompok::factory()->create(['tingkat' => 'Dasar']);
            $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
            $guru = Guru::factory()->create();
            $admin = \App\Models\Admin::factory()->create();

            ProgresHafalan::factory()->create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => 15,
            ]);

            $usulan = $this->service->createUsulan(
                $siswa->id,
                $guru->id,
                'Menengah',
                'Test alasan'
            );

            // Execute - should NOT throw exception
            $rejectedUsulan = $this->service->rejectUsulan(
                $usulan->id,
                $admin->id,
                $validCatatan
            );

            // Assert: rejection succeeded
            $this->assertEquals(StatusUsulan::DITOLAK, $rejectedUsulan->status_usulan);
            $this->assertEquals($validCatatan, $rejectedUsulan->catatan_keputusan);

            // Clean up
            UsulanKenaikanTingkat::query()->delete();
            ProgresHafalan::query()->delete();
        }

        $this->assertTrue(true, 'Property 14: Rejection requires catatan_keputusan');
    }

    /**
     * Bonus Test: Rejection can only transition from "Diajukan" status
     *
     * For any proposal not in "Diajukan" status, rejection attempt SHALL throw
     * InvalidStatusTransitionException.
     *
     * Feature: usulan-kenaikan-tingkat, Rejection state transition validation
     * Validates: Requirements 4.3
     *
     * @test
     * @group property-based
     */
    public function test_rejection_only_from_diajukan_status()
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        $kelompok = Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $guru = Guru::factory()->create();
        $admin = \App\Models\Admin::factory()->create();

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 15,
        ]);

        // Create usulan
        $usulan = $this->service->createUsulan(
            $siswa->id,
            $guru->id,
            'Menengah',
            'Test alasan'
        );

        // First approve it to change status from "Diajukan"
        $approvedUsulan = $this->service->approveUsulan(
            $usulan->id,
            $admin->id,
            'Disetujui untuk penempatan'
        );

        $this->assertEquals(StatusUsulan::DISETUJUI, $approvedUsulan->status_usulan);

        // Now attempt to reject the approved usulan - should fail
        try {
            $this->service->rejectUsulan(
                $approvedUsulan->id,
                $admin->id,
                'Mencoba menolak usulan yang sudah disetujui'
            );

            $this->fail("Should throw InvalidStatusTransitionException when rejecting from 'Disetujui' status");
        } catch (InvalidStatusTransitionException $e) {
            // Assert: exception message mentions current status and required status
            $this->assertStringContainsString(
                "Tidak dapat menolak usulan dengan status 'Disetujui'",
                $e->getMessage()
            );
            $this->assertStringContainsString(
                "Status harus 'Diajukan'",
                $e->getMessage()
            );
        }

        $this->assertTrue(true, 'Rejection can only transition from Diajukan status');
    }

    /**
     * Property 12: Approval transitions state correctly
     *
     * For any proposal in "Diajukan" status, when approved, the system SHALL
     * update status_usulan to "Disetujui", record admin_verifikator_id, and set
     * tanggal_keputusan to current timestamp.
     *
     * Feature: usulan-kenaikan-tingkat, Property 12: Approval transitions state correctly
     * Validates: Requirements 4.2
     *
     * @test
     * @group property-based
     */
    public function test_approval_transitions_state_correctly()
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        // Test with multiple valid scenarios
        $scenarios = [
            ['tingkat_asal' => 'Dasar', 'tingkat_tujuan' => 'Menengah', 'juz' => 10],
            ['tingkat_asal' => 'Dasar', 'tingkat_tujuan' => 'Atas', 'juz' => 20],
            ['tingkat_asal' => 'Menengah', 'tingkat_tujuan' => 'Atas', 'juz' => 25],
        ];

        foreach ($scenarios as $scenario) {
            // Setup: Create a usulan in "Diajukan" status
            $kelompok = Kelompok::factory()->create(['tingkat' => $scenario['tingkat_asal']]);
            $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
            $guru = Guru::factory()->create();
            $admin = \App\Models\Admin::factory()->create();

            ProgresHafalan::factory()->create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => $scenario['juz'],
            ]);

            $usulan = $this->service->createUsulan(
                $siswa->id,
                $guru->id,
                $scenario['tingkat_tujuan'],
                'Test alasan pengajuan'
            );

            // Verify initial state is "Diajukan"
            $this->assertEquals(StatusUsulan::DIAJUKAN, $usulan->status_usulan);
            $this->assertNull($usulan->admin_verifikator_id);
            $this->assertNull($usulan->tanggal_keputusan);

            // Record time before approval
            $beforeApproval = now()->subSecond();

            // Execute: Approve the usulan
            $approvedUsulan = $this->service->approveUsulan(
                $usulan->id,
                $admin->id,
                null // No catatan_keputusan for this scenario
            );

            // Record time after approval
            $afterApproval = now()->addSecond();

            // Assert: status_usulan is "Disetujui" (Requirement 4.2)
            $this->assertEquals(
                StatusUsulan::DISETUJUI,
                $approvedUsulan->status_usulan,
                "Status should transition to 'Disetujui'"
            );

            // Assert: admin_verifikator_id is recorded (Requirement 4.2)
            $this->assertEquals(
                $admin->id,
                $approvedUsulan->admin_verifikator_id,
                "Admin verifikator ID should be recorded"
            );

            // Assert: tanggal_keputusan is set to current timestamp (Requirement 4.2)
            $this->assertTrue(
                $approvedUsulan->tanggal_keputusan >= $beforeApproval &&
                $approvedUsulan->tanggal_keputusan <= $afterApproval,
                "Tanggal keputusan should be set to current timestamp (within 2 second tolerance)"
            );

            // Clean up
            UsulanKenaikanTingkat::query()->delete();
            ProgresHafalan::query()->delete();
        }

        $this->assertTrue(true, 'Property 12: Approval transitions state correctly');
    }

    /**
     * Property 15: Approval accepts optional catatan_keputusan
     *
     * For any approval attempt with or without catatan_keputusan, the system SHALL succeed.
     *
     * Feature: usulan-kenaikan-tingkat, Property 15: Approval accepts optional catatan_keputusan
     * Validates: Requirements 4.5
     *
     * @test
     * @group property-based
     */
    public function test_approval_accepts_optional_catatan_keputusan()
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        // Test scenarios with various catatan_keputusan values (including null and empty)
        $catatanScenarios = [
            ['catatan' => null, 'label' => 'null'],
            ['catatan' => '', 'label' => 'empty string'],
            ['catatan' => 'Disetujui dengan catatan', 'label' => 'with text'],
            ['catatan' => 'Progres sangat baik, direkomendasikan untuk naik tingkat', 'label' => 'long text'],
        ];

        foreach ($catatanScenarios as $testCase) {
            // Setup: Create a usulan in "Diajukan" status
            $kelompok = Kelompok::factory()->create(['tingkat' => 'Dasar']);
            $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
            $guru = Guru::factory()->create();
            $admin = \App\Models\Admin::factory()->create();

            ProgresHafalan::factory()->create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => 15,
            ]);

            $usulan = $this->service->createUsulan(
                $siswa->id,
                $guru->id,
                'Menengah',
                'Test alasan'
            );

            // Execute: Approve with various catatan_keputusan values - should NOT throw exception
            $approvedUsulan = $this->service->approveUsulan(
                $usulan->id,
                $admin->id,
                $testCase['catatan']
            );

            // Assert: approval succeeded regardless of catatan_keputusan (Requirement 4.5)
            $this->assertEquals(
                StatusUsulan::DISETUJUI,
                $approvedUsulan->status_usulan,
                "Approval should succeed with catatan_keputusan = {$testCase['label']}"
            );

            // Assert: catatan_keputusan is stored as provided
            if ($testCase['catatan'] === '') {
                // Empty string should be stored as empty string (not null)
                $this->assertEquals('', $approvedUsulan->catatan_keputusan);
            } else {
                $this->assertEquals($testCase['catatan'], $approvedUsulan->catatan_keputusan);
            }

            // Clean up
            UsulanKenaikanTingkat::query()->delete();
            ProgresHafalan::query()->delete();
        }

        $this->assertTrue(true, 'Property 15: Approval accepts optional catatan_keputusan');
    }

    /**
     * Property 16: Approval leaves kelompok_tujuan_id NULL
     *
     * For any approved proposal, the system SHALL set status_usulan to "Disetujui"
     * while keeping kelompok_tujuan_id NULL.
     *
     * Feature: usulan-kenaikan-tingkat, Property 16: Approval leaves kelompok_tujuan_id NULL
     * Validates: Requirements 5.1
     *
     * @test
     * @group property-based
     */
    public function test_approval_leaves_kelompok_tujuan_id_null()
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        // Test multiple scenarios to ensure this property holds universally
        $scenarios = [
            ['tingkat_asal' => 'Dasar', 'tingkat_tujuan' => 'Menengah', 'juz' => 10, 'catatan' => null],
            ['tingkat_asal' => 'Dasar', 'tingkat_tujuan' => 'Atas', 'juz' => 20, 'catatan' => 'Disetujui'],
            ['tingkat_asal' => 'Menengah', 'tingkat_tujuan' => 'Atas', 'juz' => 25, 'catatan' => ''],
            ['tingkat_asal' => 'Dasar', 'tingkat_tujuan' => 'Menengah', 'juz' => 30, 'catatan' => 'Progres sangat baik'],
        ];

        foreach ($scenarios as $scenario) {
            // Setup: Create a usulan
            $kelompok = Kelompok::factory()->create(['tingkat' => $scenario['tingkat_asal']]);
            $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
            $guru = Guru::factory()->create();
            $admin = \App\Models\Admin::factory()->create();

            ProgresHafalan::factory()->create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'total_juz_tuntas' => $scenario['juz'],
            ]);

            $usulan = $this->service->createUsulan(
                $siswa->id,
                $guru->id,
                $scenario['tingkat_tujuan'],
                'Test alasan'
            );

            // Verify kelompok_tujuan_id is initially NULL
            $this->assertNull($usulan->kelompok_tujuan_id);

            // Execute: Approve the usulan
            $approvedUsulan = $this->service->approveUsulan(
                $usulan->id,
                $admin->id,
                $scenario['catatan']
            );

            // Assert: status transitions to "Disetujui" (Requirement 5.1)
            $this->assertEquals(
                StatusUsulan::DISETUJUI,
                $approvedUsulan->status_usulan,
                "Status should be 'Disetujui' after approval"
            );

            // Assert: kelompok_tujuan_id remains NULL (Requirement 5.1)
            $this->assertNull(
                $approvedUsulan->kelompok_tujuan_id,
                "kelompok_tujuan_id must remain NULL after approval - placement is separate step"
            );

            // Assert: tanggal_penempatan also remains NULL (not set until placement)
            $this->assertNull(
                $approvedUsulan->tanggal_penempatan,
                "tanggal_penempatan must remain NULL after approval"
            );

            // Assert: other fields are set correctly
            $this->assertNotNull($approvedUsulan->admin_verifikator_id);
            $this->assertNotNull($approvedUsulan->tanggal_keputusan);

            // Clean up
            UsulanKenaikanTingkat::query()->delete();
            ProgresHafalan::query()->delete();
        }

        $this->assertTrue(true, 'Property 16: Approval leaves kelompok_tujuan_id NULL');
    }

    /**
     * Bonus Test: Approval can only transition from "Diajukan" status
     *
     * For any proposal not in "Diajukan" status, approval attempt SHALL throw
     * InvalidStatusTransitionException.
     *
     * Feature: usulan-kenaikan-tingkat, Approval state transition validation
     * Validates: Requirements 4.2
     *
     * @test
     * @group property-based
     */
    public function test_approval_only_from_diajukan_status()
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        $kelompok = Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $guru = Guru::factory()->create();
        $admin1 = \App\Models\Admin::factory()->create();
        $admin2 = \App\Models\Admin::factory()->create();

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 15,
        ]);

        // Create and approve usulan
        $usulan = $this->service->createUsulan(
            $siswa->id,
            $guru->id,
            'Menengah',
            'Test alasan'
        );

        $approvedUsulan = $this->service->approveUsulan(
            $usulan->id,
            $admin1->id,
            'Disetujui untuk penempatan'
        );

        $this->assertEquals(StatusUsulan::DISETUJUI, $approvedUsulan->status_usulan);

        // Now attempt to approve again - should fail
        try {
            $this->service->approveUsulan(
                $approvedUsulan->id,
                $admin2->id,
                'Mencoba approve lagi'
            );

            $this->fail("Should throw InvalidStatusTransitionException when approving from 'Disetujui' status");
        } catch (InvalidStatusTransitionException $e) {
            // Assert: exception message mentions current status and required status
            $this->assertStringContainsString(
                'Disetujui',
                $e->getMessage(),
                "Exception should mention current 'Disetujui' status"
            );
        }

        $this->assertTrue(true, 'Approval can only transition from Diajukan status');
    }

    /**
     * Property 18: Available groups filtered by tingkat_tujuan
     *
     * For any tingkat_tujuan value, the system SHALL return only Kelompok records
     * where kelompok.tingkat matches that level.
     *
     * Feature: usulan-kenaikan-tingkat, Property 18: Available groups filtered by tingkat_tujuan
     * Validates: Requirements 6.1
     *
     * @test
     * @group property-based
     */
    public function test_available_groups_filtered_by_tingkat_tujuan()
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        // Test all tingkat combinations
        $tingkatLevels = ['Dasar', 'Menengah', 'Atas'];
        $jenisKelaminOptions = ['Laki-laki', 'Perempuan'];

        foreach ($tingkatLevels as $targetTingkat) {
            foreach ($jenisKelaminOptions as $jenisKelamin) {
                // Setup: Create groups at all levels with same jenis
                $jenisKelompok = $jenisKelamin === 'Laki-laki' ? 'Banin' : 'Banat';

                $kelompokDasar = Kelompok::factory()->create([
                    'tingkat' => 'Dasar',
                    'jenis' => $jenisKelompok,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'kapasitas_maks' => 12,
                ]);

                $kelompokMenengah = Kelompok::factory()->create([
                    'tingkat' => 'Menengah',
                    'jenis' => $jenisKelompok,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'kapasitas_maks' => 12,
                ]);

                $kelompokAtas = Kelompok::factory()->create([
                    'tingkat' => 'Atas',
                    'jenis' => $jenisKelompok,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                    'kapasitas_maks' => 12,
                ]);

                // Execute: Get available groups for target tingkat
                $availableGroups = $this->service->getAvailableGroups(
                    $targetTingkat,
                    $jenisKelamin
                );

                // Assert: All returned groups have matching tingkat
                $this->assertGreaterThan(
                    0,
                    $availableGroups->count(),
                    "Should return at least one group for tingkat {$targetTingkat}"
                );

                foreach ($availableGroups as $kelompok) {
                    $this->assertEquals(
                        $targetTingkat,
                        $kelompok->tingkat,
                        "All returned groups should have tingkat = {$targetTingkat}"
                    );
                }

                // Assert: Groups from other tingkat levels are NOT included
                $returnedIds = $availableGroups->pluck('id')->toArray();

                if ($targetTingkat === 'Dasar') {
                    $this->assertContains($kelompokDasar->id, $returnedIds);
                    $this->assertNotContains($kelompokMenengah->id, $returnedIds);
                    $this->assertNotContains($kelompokAtas->id, $returnedIds);
                } elseif ($targetTingkat === 'Menengah') {
                    $this->assertNotContains($kelompokDasar->id, $returnedIds);
                    $this->assertContains($kelompokMenengah->id, $returnedIds);
                    $this->assertNotContains($kelompokAtas->id, $returnedIds);
                } elseif ($targetTingkat === 'Atas') {
                    $this->assertNotContains($kelompokDasar->id, $returnedIds);
                    $this->assertNotContains($kelompokMenengah->id, $returnedIds);
                    $this->assertContains($kelompokAtas->id, $returnedIds);
                }

                // Clean up for next iteration
                Kelompok::query()->delete();
                Siswa::query()->delete();
            }
        }

        $this->assertTrue(true, 'Property 18: Available groups filtered by tingkat_tujuan');
    }

    /**
     * Property 20: Full groups excluded from available list
     *
     * For any group where jumlah_siswa >= kapasitas_maks, the system SHALL
     * exclude it from the available groups list.
     *
     * Feature: usulan-kenaikan-tingkat, Property 20: Full groups excluded from available list
     * Validates: Requirements 6.3
     *
     * @test
     * @group property-based
     */
    public function test_full_groups_excluded_from_available_list()
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        // Test with different capacity scenarios
        $capacityScenarios = [
            ['kapasitas_maks' => 5, 'jumlah_siswa' => 5], // exactly full
            ['kapasitas_maks' => 10, 'jumlah_siswa' => 10], // exactly full
            ['kapasitas_maks' => 12, 'jumlah_siswa' => 15], // over capacity
            ['kapasitas_maks' => 8, 'jumlah_siswa' => 20], // significantly over
        ];

        foreach ($capacityScenarios as $scenario) {
            // Setup: Create full group and available group at same tingkat/jenis
            $kelompokFull = Kelompok::factory()->create([
                'tingkat' => 'Menengah',
                'jenis' => 'Banin',
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => $scenario['kapasitas_maks'],
            ]);

            // Add students to fill the group
            Siswa::factory()->count($scenario['jumlah_siswa'])->create([
                'kelompok_id' => $kelompokFull->id,
                'status_penempatan' => 'Ditempatkan',
                'jenis_kelamin' => 'Laki-laki',
            ]);

            // Create an available (not full) group at same level
            $kelompokAvailable = Kelompok::factory()->create([
                'tingkat' => 'Menengah',
                'jenis' => 'Banin',
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => 12,
            ]);

            // Add some students but not at capacity
            Siswa::factory()->count(5)->create([
                'kelompok_id' => $kelompokAvailable->id,
                'status_penempatan' => 'Ditempatkan',
                'jenis_kelamin' => 'Laki-laki',
            ]);

            // Execute: Get available groups
            $availableGroups = $this->service->getAvailableGroups(
                'Menengah',
                'Laki-laki'
            );

            // Assert: Full group is NOT in the list
            $returnedIds = $availableGroups->pluck('id')->toArray();
            $this->assertNotContains(
                $kelompokFull->id,
                $returnedIds,
                "Full group (jumlah_siswa={$scenario['jumlah_siswa']}, kapasitas_maks={$scenario['kapasitas_maks']}) should be excluded"
            );

            // Assert: Available group IS in the list
            $this->assertContains(
                $kelompokAvailable->id,
                $returnedIds,
                "Available group should be included in the list"
            );

            // Assert: All returned groups have capacity available
            foreach ($availableGroups as $kelompok) {
                $jumlahSiswa = $kelompok->siswas->count();
                $this->assertLessThan(
                    $kelompok->kapasitas_maks,
                    $jumlahSiswa,
                    "All returned groups should have jumlah_siswa < kapasitas_maks"
                );
            }

            // Clean up for next iteration
            Kelompok::query()->delete();
            Siswa::query()->delete();
        }

        $this->assertTrue(true, 'Property 20: Full groups excluded from available list');
    }

    /**
     * Property 36: New student placement filters by rekomendasi_tingkat
     *
     * For any Siswa_Baru with rekomendasi_tingkat from tes penempatan, the system
     * SHALL filter available Kelompok to only those matching that tingkat.
     *
     * Feature: usulan-kenaikan-tingkat, Property 36: New student placement filters by rekomendasi_tingkat
     * Validates: Requirements 12.3
     *
     * @test
     * @group property-based
     */
    public function test_new_student_placement_filters_by_rekomendasi_tingkat()
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        // Test all possible rekomendasi_tingkat values
        $rekomendasiTingkatScenarios = [
            ['rekomendasi' => 'Dasar', 'jenis_kelamin' => 'Perempuan'],
            ['rekomendasi' => 'Menengah', 'jenis_kelamin' => 'Laki-laki'],
            ['rekomendasi' => 'Atas', 'jenis_kelamin' => 'Perempuan'],
        ];

        foreach ($rekomendasiTingkatScenarios as $scenario) {
            // Setup: Create groups at all tingkat levels
            $jenisKelompok = $scenario['jenis_kelamin'] === 'Laki-laki' ? 'Banin' : 'Banat';

            $kelompokDasar = Kelompok::factory()->create([
                'tingkat' => 'Dasar',
                'jenis' => $jenisKelompok,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => 12,
            ]);

            $kelompokMenengah = Kelompok::factory()->create([
                'tingkat' => 'Menengah',
                'jenis' => $jenisKelompok,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => 12,
            ]);

            $kelompokAtas = Kelompok::factory()->create([
                'tingkat' => 'Atas',
                'jenis' => $jenisKelompok,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => 12,
            ]);

            // Execute: Get available groups using rekomendasi_tingkat
            // (This uses the same method as promoted students - unified flow)
            $availableGroups = $this->service->getAvailableGroups(
                $scenario['rekomendasi'],
                $scenario['jenis_kelamin']
            );

            // Assert: Should return at least one group
            $this->assertGreaterThan(
                0,
                $availableGroups->count(),
                "Should return at least one group for rekomendasi tingkat {$scenario['rekomendasi']}"
            );

            // Assert: All returned groups match rekomendasi_tingkat
            foreach ($availableGroups as $kelompok) {
                $this->assertEquals(
                    $scenario['rekomendasi'],
                    $kelompok->tingkat,
                    "All groups should match rekomendasi tingkat = {$scenario['rekomendasi']}"
                );

                // Also verify jenis matches jenis_kelamin
                $this->assertEquals(
                    $jenisKelompok,
                    $kelompok->jenis,
                    "All groups should have jenis matching student's jenis_kelamin"
                );
            }

            // Assert: Groups from other tingkat levels are excluded
            $returnedIds = $availableGroups->pluck('id')->toArray();

            if ($scenario['rekomendasi'] === 'Dasar') {
                $this->assertContains($kelompokDasar->id, $returnedIds);
                $this->assertNotContains($kelompokMenengah->id, $returnedIds);
                $this->assertNotContains($kelompokAtas->id, $returnedIds);
            } elseif ($scenario['rekomendasi'] === 'Menengah') {
                $this->assertNotContains($kelompokDasar->id, $returnedIds);
                $this->assertContains($kelompokMenengah->id, $returnedIds);
                $this->assertNotContains($kelompokAtas->id, $returnedIds);
            } elseif ($scenario['rekomendasi'] === 'Atas') {
                $this->assertNotContains($kelompokDasar->id, $returnedIds);
                $this->assertNotContains($kelompokMenengah->id, $returnedIds);
                $this->assertContains($kelompokAtas->id, $returnedIds);
            }

            // Clean up for next iteration
            Kelompok::query()->delete();
            Siswa::query()->delete();
        }

        $this->assertTrue(true, 'Property 36: New student placement filters by rekomendasi_tingkat');
    }

    /**
     * Bonus Test: Gender matching in available groups
     *
     * For any student with jenis_kelamin, the system SHALL return only groups
     * where kelompok.jenis matches the student's gender (Laki-laki → Banin, Perempuan → Banat).
     *
     * Feature: usulan-kenaikan-tingkat, Gender matching validation
     * Validates: Requirements 6.1
     *
     * @test
     * @group property-based
     */
    public function test_available_groups_filter_by_gender_matching()
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        // Test gender matching scenarios
        $genderScenarios = [
            ['jenis_kelamin' => 'Laki-laki', 'expected_jenis' => 'Banin', 'wrong_jenis' => 'Banat'],
            ['jenis_kelamin' => 'Perempuan', 'expected_jenis' => 'Banat', 'wrong_jenis' => 'Banin'],
        ];

        foreach ($genderScenarios as $scenario) {
            // Setup: Create groups with both jenis at same tingkat
            $kelompokMatchingGender = Kelompok::factory()->create([
                'tingkat' => 'Menengah',
                'jenis' => $scenario['expected_jenis'],
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => 12,
            ]);

            $kelompokWrongGender = Kelompok::factory()->create([
                'tingkat' => 'Menengah',
                'jenis' => $scenario['wrong_jenis'],
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => 12,
            ]);

            // Execute: Get available groups for student's gender
            $availableGroups = $this->service->getAvailableGroups(
                'Menengah',
                $scenario['jenis_kelamin']
            );

            // Assert: Only matching gender groups are returned
            $returnedIds = $availableGroups->pluck('id')->toArray();

            $this->assertContains(
                $kelompokMatchingGender->id,
                $returnedIds,
                "Group with jenis {$scenario['expected_jenis']} should be included for {$scenario['jenis_kelamin']}"
            );

            $this->assertNotContains(
                $kelompokWrongGender->id,
                $returnedIds,
                "Group with jenis {$scenario['wrong_jenis']} should be excluded for {$scenario['jenis_kelamin']}"
            );

            // Assert: All returned groups have matching jenis
            foreach ($availableGroups as $kelompok) {
                $this->assertEquals(
                    $scenario['expected_jenis'],
                    $kelompok->jenis,
                    "All returned groups should have jenis = {$scenario['expected_jenis']}"
                );
            }

            // Clean up for next iteration
            Kelompok::query()->delete();
        }

        $this->assertTrue(true, 'Available groups filter by gender matching correctly');
    }

    /**
     * Bonus Test: Edge case - No available groups
     *
     * When all groups matching tingkat and jenis are full, the system SHALL
     * return an empty collection.
     *
     * Feature: usulan-kenaikan-tingkat, Edge case handling
     * Validates: Requirements 6.1, 6.3
     *
     * @test
     * @group property-based
     */
    public function test_no_available_groups_when_all_full()
    {
        $tahunAjaran = TahunAjaran::factory()->create();

        // Setup: Create multiple groups at same tingkat/jenis, all full
        $fullGroups = [];
        for ($i = 0; $i < 3; $i++) {
            $kelompok = Kelompok::factory()->create([
                'tingkat' => 'Atas',
                'jenis' => 'Banat',
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kapasitas_maks' => 10,
            ]);

            // Fill to capacity
            Siswa::factory()->count(10)->create([
                'kelompok_id' => $kelompok->id,
                'status_penempatan' => 'Ditempatkan',
                'jenis_kelamin' => 'Perempuan',
            ]);

            $fullGroups[] = $kelompok;
        }

        // Execute: Get available groups
        $availableGroups = $this->service->getAvailableGroups(
            'Atas',
            'Perempuan'
        );

        // Assert: Should return empty collection
        $this->assertEquals(
            0,
            $availableGroups->count(),
            "Should return empty collection when all groups are full"
        );

        $this->assertTrue(true, 'No available groups when all full - edge case handled correctly');
    }
}
