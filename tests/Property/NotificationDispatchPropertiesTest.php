<?php

/**
 * Property-Based Tests for Notification Dispatch
 *
 * Tests Properties 24, 25, 27 from design.md
 * Feature: usulan-kenaikan-tingkat
 */

namespace Tests\Property;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Admin;
use App\Models\Kelompok;
use App\Models\ProgresHafalan;
use App\Models\ParameterKonfigurasi;
use App\Models\UsulanKenaikanTingkat;
use App\Models\TahunAjaran;
use App\Services\UsulanKenaikanTingkatService;
use App\Enums\StatusUsulan;
use App\Enums\TingkatKelompok;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationDispatchPropertiesTest extends TestCase
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
     * Property 24: Decision triggers notification to teacher
     *
     * For any approval or rejection decision, the system SHALL dispatch an in-app
     * notification to the Guru_Pengusul.
     *
     * Feature: usulan-kenaikan-tingkat, Property 24: Decision triggers notification to teacher
     * Validates: Requirements 8.1
     *
     * @test
     * @group property-based
     */
    public function test_decision_triggers_notification_to_teacher()
    {
        // Fake notifications to prevent actual dispatch
        Notification::fake();

        $tahunAjaran = TahunAjaran::factory()->create();

        // Test both approval and rejection scenarios
        $decisionScenarios = [
            [
                'decision_type' => 'approved',
                'tingkat_asal' => 'Dasar',
                'tingkat_tujuan' => 'Menengah',
                'juz' => 10,
                'catatan' => 'Siswa menunjukkan progres yang sangat baik',
            ],
            [
                'decision_type' => 'rejected',
                'tingkat_asal' => 'Dasar',
                'tingkat_tujuan' => 'Menengah',
                'juz' => 10,
                'catatan' => 'Perlu evaluasi lebih lanjut',
            ],
            [
                'decision_type' => 'approved',
                'tingkat_asal' => 'Menengah',
                'tingkat_tujuan' => 'Atas',
                'juz' => 20,
                'catatan' => null, // approval without catatan
            ],
            [
                'decision_type' => 'rejected',
                'tingkat_asal' => 'Menengah',
                'tingkat_tujuan' => 'Atas',
                'juz' => 20,
                'catatan' => 'Progres belum konsisten',
            ],
        ];

        foreach ($decisionScenarios as $scenario) {
            // Setup: Create usulan in "Diajukan" status
            $kelompok = Kelompok::factory()->create(['tingkat' => $scenario['tingkat_asal']]);
            $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
            $guru = Guru::factory()->create();
            $admin = Admin::factory()->create();

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

            // Execute: Process decision (approve or reject)
            if ($scenario['decision_type'] === 'approved') {
                $this->service->approveUsulan(
                    $usulan->id,
                    $admin->id,
                    $scenario['catatan']
                );
            } else {
                $this->service->rejectUsulan(
                    $usulan->id,
                    $admin->id,
                    $scenario['catatan']
                );
            }

            // Assert: Notification was dispatched to guru_pengusul (Requirement 8.1)
            Notification::assertSentTo(
                $guru,
                \App\Notifications\UsulanKenaikanTingkatDecisionNotification::class,
                function ($notification, $channels, $notifiable) use ($usulan, $scenario) {
                    // Verify notification was sent to correct guru
                    $this->assertEquals($usulan->guru_pengusul_id, $notifiable->id);

                    // Verify notification contains correct usulan
                    $this->assertEquals($usulan->id, $notification->usulan->id);

                    // Verify notification contains correct decision type
                    $this->assertEquals($scenario['decision_type'], $notification->decision);

                    return true;
                }
            );

            // Clean up for next iteration
            Notification::fake(); // Reset notification assertions
            UsulanKenaikanTingkat::query()->delete();
            ProgresHafalan::query()->delete();
        }

        $this->assertTrue(true, 'Property 24: Decision triggers notification to teacher');
    }

    /**
     * Property 25: Notifications contain required context
     *
     * For any notification (teacher or student), the notification SHALL include all
     * contextually relevant data (student name, decision/group details, dates, catatan if present).
     *
     * Feature: usulan-kenaikan-tingkat, Property 25: Notifications contain required context
     * Validates: Requirements 8.2, 9.2
     *
     * @test
     * @group property-based
     */
    public function test_notifications_contain_required_context()
    {
        // Fake notifications to prevent actual dispatch
        Notification::fake();

        $tahunAjaran = TahunAjaran::factory()->create();

        // === Test 1: Teacher notification on approval (with catatan) ===
        $kelompok = Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $siswa = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'nama' => 'Ahmad Fauzi'
        ]);
        $guru = Guru::factory()->create();
        $admin = Admin::factory()->create();

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 10,
        ]);

        $usulan1 = $this->service->createUsulan(
            $siswa->id,
            $guru->id,
            'Menengah',
            'Test alasan'
        );

        $catatanApproval = 'Disetujui dengan catatan: terus tingkatkan kualitas hafalan';
        $this->service->approveUsulan($usulan1->id, $admin->id, $catatanApproval);

        // Assert: Teacher notification contains all required context (Requirement 8.2)
        Notification::assertSentTo(
            $guru,
            \App\Notifications\UsulanKenaikanTingkatDecisionNotification::class,
            function ($notification) use ($siswa, $catatanApproval) {
                $data = $notification->toArray($notification->usulan->guruPengusul);

                // Required: student name
                $this->assertArrayHasKey('siswa_nama', $data);
                $this->assertEquals('Ahmad Fauzi', $data['siswa_nama']);

                // Required: decision
                $this->assertArrayHasKey('decision', $data);
                $this->assertEquals('approved', $data['decision']);
                $this->assertArrayHasKey('decision_label', $data);
                $this->assertEquals('Disetujui', $data['decision_label']);

                // Required: catatan_keputusan (if provided)
                $this->assertArrayHasKey('catatan_keputusan', $data);
                $this->assertEquals($catatanApproval, $data['catatan_keputusan']);

                // Required: tanggal_keputusan
                $this->assertArrayHasKey('tanggal_keputusan', $data);
                $this->assertNotNull($data['tanggal_keputusan']);

                // Required: tingkat_tujuan
                $this->assertArrayHasKey('tingkat_tujuan', $data);

                return true;
            }
        );

        // Clean up
        UsulanKenaikanTingkat::query()->delete();
        ProgresHafalan::query()->delete();
        Notification::fake();

        // === Test 2: Teacher notification on rejection (with catatan) ===
        $siswa2 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'nama' => 'Fatimah Zahra'
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 10,
        ]);

        $usulan2 = $this->service->createUsulan(
            $siswa2->id,
            $guru->id,
            'Menengah',
            'Test alasan'
        );

        $catatanRejection = 'Progres belum memadai, perlu evaluasi lebih lanjut';
        $this->service->rejectUsulan($usulan2->id, $admin->id, $catatanRejection);

        // Assert: Rejection notification contains all required context
        Notification::assertSentTo(
            $guru,
            \App\Notifications\UsulanKenaikanTingkatDecisionNotification::class,
            function ($notification) use ($catatanRejection) {
                $data = $notification->toArray($notification->usulan->guruPengusul);

                // Verify all required fields for rejection
                $this->assertArrayHasKey('siswa_nama', $data);
                $this->assertEquals('Fatimah Zahra', $data['siswa_nama']);
                $this->assertArrayHasKey('decision', $data);
                $this->assertEquals('rejected', $data['decision']);
                $this->assertArrayHasKey('decision_label', $data);
                $this->assertEquals('Ditolak', $data['decision_label']);
                $this->assertArrayHasKey('catatan_keputusan', $data);
                $this->assertEquals($catatanRejection, $data['catatan_keputusan']);
                $this->assertArrayHasKey('tanggal_keputusan', $data);
                $this->assertNotNull($data['tanggal_keputusan']);

                return true;
            }
        );

        // Clean up
        UsulanKenaikanTingkat::query()->delete();
        ProgresHafalan::query()->delete();
        Notification::fake();

        // === Test 3: Student notification on placement (Requirement 9.2) ===
        $guruPembimbing = Guru::factory()->create([
            'nama' => 'Ustadz Abdullah'
        ]);

        $kelompokTujuan = Kelompok::factory()->create([
            'tingkat' => 'Menengah',
            'nama' => 'Kelompok Menengah A',
            'kapasitas_maks' => 12,
            'guru_pembimbing_id' => $guruPembimbing->id,
        ]);

        $siswa3 = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'nama' => 'Umar bin Khattab'
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa3->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 12,
        ]);

        $usulan3 = $this->service->createUsulan(
            $siswa3->id,
            $guru->id,
            'Menengah',
            'Test alasan'
        );

        // Approve first
        $this->service->approveUsulan($usulan3->id, $admin->id, null);

        // Reset notification fake to clear approval notification
        Notification::fake();

        // Place student to target group
        $this->service->placeStudent($usulan3->id, $kelompokTujuan->id);

        // Assert: Student notification contains all required context (Requirement 9.2)
        Notification::assertSentTo(
            $siswa3,
            \App\Notifications\KenaikanTingkatPlacementNotification::class,
            function ($notification) use ($kelompokTujuan, $guruPembimbing) {
                $data = $notification->toArray($notification->usulan->siswa);

                // Required: new Tingkat_Kelompok
                $this->assertArrayHasKey('tingkat_baru', $data);
                $this->assertInstanceOf(TingkatKelompok::class, $data['tingkat_baru']);
                $this->assertEquals('Menengah', $data['tingkat_baru']->value);

                // Required: new Kelompok name
                $this->assertArrayHasKey('kelompok_nama', $data);
                $this->assertEquals('Kelompok Menengah A', $data['kelompok_nama']);

                // Required: kelompok_id
                $this->assertArrayHasKey('kelompok_id', $data);
                $this->assertEquals($kelompokTujuan->id, $data['kelompok_id']);

                // Required: guru_pembimbing name
                $this->assertArrayHasKey('guru_pembimbing', $data);
                $this->assertEquals('Ustadz Abdullah', $data['guru_pembimbing']);

                // Required: effective date (tanggal_penempatan)
                $this->assertArrayHasKey('tanggal_penempatan', $data);
                $this->assertNotNull($data['tanggal_penempatan']);

                return true;
            }
        );

        $this->assertTrue(true, 'Property 25: Notifications contain required context');
    }

    /**
     * Property 27: Placement triggers notification to student
     *
     * For any usulan transitioning to status "Ditempatkan", the system SHALL
     * dispatch an in-app notification to the Siswa account.
     *
     * Feature: usulan-kenaikan-tingkat, Property 27: Placement triggers notification to student
     * Validates: Requirements 9.1
     *
     * @test
     * @group property-based
     */
    public function test_placement_triggers_notification_to_student()
    {
        // Fake notifications to prevent actual dispatch
        Notification::fake();

        $tahunAjaran = TahunAjaran::factory()->create();

        // Test multiple placement scenarios
        $placementScenarios = [
            [
                'tingkat_asal' => 'Dasar',
                'tingkat_tujuan' => 'Menengah',
                'kelompok_tujuan_nama' => 'Kelompok Menengah A',
                'juz' => 10,
            ],
            [
                'tingkat_asal' => 'Dasar',
                'tingkat_tujuan' => 'Atas',
                'kelompok_tujuan_nama' => 'Kelompok Atas B',
                'juz' => 20,
            ],
            [
                'tingkat_asal' => 'Menengah',
                'tingkat_tujuan' => 'Atas',
                'kelompok_tujuan_nama' => 'Kelompok Atas C',
                'juz' => 22,
            ],
        ];

        foreach ($placementScenarios as $scenario) {
            // Setup: Create usulan and approve it
            $kelompokAsal = Kelompok::factory()->create(['tingkat' => $scenario['tingkat_asal']]);
            $kelompokTujuan = Kelompok::factory()->create([
                'tingkat' => $scenario['tingkat_tujuan'],
                'nama' => $scenario['kelompok_tujuan_nama'],
                'kapasitas_maks' => 12,
            ]);

            $siswa = Siswa::factory()->create(['kelompok_id' => $kelompokAsal->id]);
            $guru = Guru::factory()->create();
            $admin = Admin::factory()->create();

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

            // Approve the usulan first
            $this->service->approveUsulan($usulan->id, $admin->id, null);

            // Reset notification fake to clear approval notification
            Notification::fake();

            // Execute: Place student to target group
            $placedUsulan = $this->service->placeStudent($usulan->id, $kelompokTujuan->id);

            // Assert: Usulan status is "Ditempatkan"
            $this->assertEquals(
                StatusUsulan::DITEMPATKAN,
                $placedUsulan->status_usulan,
                "Usulan should transition to 'Ditempatkan' status"
            );

            // Assert: Notification was dispatched to siswa (Requirement 9.1)
            Notification::assertSentTo(
                $siswa,
                \App\Notifications\KenaikanTingkatPlacementNotification::class,
                function ($notification, $channels, $notifiable) use ($usulan, $siswa) {
                    // Verify notification was sent to correct siswa
                    $this->assertEquals($siswa->id, $notifiable->id);

                    // Verify notification contains correct usulan
                    $this->assertEquals($usulan->id, $notification->usulan->id);

                    // Verify notification uses database channel
                    $this->assertContains('database', $channels);

                    return true;
                }
            );

            // Clean up for next iteration
            Notification::fake();
            UsulanKenaikanTingkat::query()->delete();
            ProgresHafalan::query()->delete();
        }

        $this->assertTrue(true, 'Property 27: Placement triggers notification to student');
    }

    /**
     * Bonus Test: Notification is NOT sent if approval/rejection fails
     *
     * If an approval or rejection operation fails (e.g., invalid status transition),
     * no notification should be dispatched.
     *
     * Feature: usulan-kenaikan-tingkat, Notification dispatch failure handling
     *
     * @test
     * @group property-based
     */
    public function test_notification_not_sent_on_failed_decision()
    {
        // Fake notifications to prevent actual dispatch
        Notification::fake();

        $tahunAjaran = TahunAjaran::factory()->create();
        $kelompok = Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $guru = Guru::factory()->create();
        $admin = Admin::factory()->create();

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 10,
        ]);

        $usulan = $this->service->createUsulan(
            $siswa->id,
            $guru->id,
            'Menengah',
            'Test alasan'
        );

        // Approve the usulan first
        $this->service->approveUsulan($usulan->id, $admin->id, null);

        // Reset notification fake
        Notification::fake();

        // Attempt to approve again (invalid status transition) - should fail
        try {
            $this->service->approveUsulan($usulan->id, $admin->id, 'Attempt double approval');
            $this->fail('Should throw InvalidStatusTransitionException');
        } catch (\App\Exceptions\InvalidStatusTransitionException $e) {
            // Expected exception
        }

        // Assert: No notification was sent after failed approval
        Notification::assertNothingSent();

        $this->assertTrue(true, 'Bonus test: Notification not sent on failed decision');
    }

    /**
     * Bonus Test: Notification is NOT sent if placement fails
     *
     * If a placement operation fails (e.g., capacity exceeded), no notification
     * should be dispatched to the student.
     *
     * Feature: usulan-kenaikan-tingkat, Notification dispatch failure handling
     *
     * @test
     * @group property-based
     */
    public function test_notification_not_sent_on_failed_placement()
    {
        // Fake notifications to prevent actual dispatch
        Notification::fake();

        $tahunAjaran = TahunAjaran::factory()->create();
        $kelompok = Kelompok::factory()->create(['tingkat' => 'Dasar']);
        $kelompokTujuan = Kelompok::factory()->create([
            'tingkat' => 'Menengah',
            'kapasitas_maks' => 10,
        ]);

        // Fill the kelompok to capacity by creating 10 students
        Siswa::factory()->count(10)->create(['kelompok_id' => $kelompokTujuan->id]);

        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);
        $guru = Guru::factory()->create();
        $admin = Admin::factory()->create();

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'total_juz_tuntas' => 10,
        ]);

        $usulan = $this->service->createUsulan(
            $siswa->id,
            $guru->id,
            'Menengah',
            'Test alasan'
        );

        // Approve the usulan
        $this->service->approveUsulan($usulan->id, $admin->id, null);

        // Reset notification fake
        Notification::fake();

        // Attempt to place to full group - should fail
        try {
            $this->service->placeStudent($usulan->id, $kelompokTujuan->id);
            $this->fail('Should throw GroupCapacityExceededException');
        } catch (\App\Exceptions\GroupCapacityExceededException $e) {
            // Expected exception
        }

        // Assert: No notification was sent after failed placement
        Notification::assertNothingSent();

        $this->assertTrue(true, 'Bonus test: Notification not sent on failed placement');
    }
}
