<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelompok;
use App\Models\UsulanKenaikanTingkat;
use App\Models\ProgresHafalan;
use App\Models\ParameterKonfigurasi;
use App\Enums\StatusUsulan;
use App\Enums\TingkatKelompok;
use App\Services\UsulanKenaikanTingkatService;
use App\Notifications\UsulanKenaikanTingkatDecisionNotification;
use App\Notifications\KenaikanTingkatPlacementNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

/**
 * Test notification read status handling
 *
 * Validates: Requirements 8.4, 9.4
 * Property 26: Accessing notification marks it read
 */
class NotificationReadStatusTest extends TestCase
{
    use RefreshDatabase;

    protected UsulanKenaikanTingkatService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(UsulanKenaikanTingkatService::class);

        // Create parameter configuration
        ParameterKonfigurasi::factory()->create([
            'batas_tingkat' => [
                'dasar' => 0,
                'menengah' => 10,
                'atas' => 20,
            ],
        ]);
    }

    /**
     * Test: Guru receives notification on usulan approval
     * Validates: Requirement 8.1, 8.2
     */
    public function test_guru_receives_notification_on_usulan_approval()
    {
        Notification::fake();

        // Create test data
        $guru = Guru::factory()->create();
        $kelompok = Kelompok::factory()->create(['tingkat' => TingkatKelompok::DASAR]);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        // Create progres hafalan
        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'total_juz_tuntas' => 10,
        ]);

        // Create usulan
        $usulan = $this->service->createUsulan(
            $siswa->id,
            $guru->id,
            TingkatKelompok::MENENGAH->value,
            'Test approval'
        );

        // Approve usulan
        $admin = \App\Models\Admin::factory()->create();
        $this->service->approveUsulan($usulan->id, $admin->id, 'Test catatan');

        // Assert notification was sent to guru
        Notification::assertSentTo(
            $guru,
            UsulanKenaikanTingkatDecisionNotification::class,
            function ($notification, $channels) use ($usulan) {
                return $notification->usulan->id === $usulan->id
                    && $notification->decision === 'approved';
            }
        );
    }

    /**
     * Test: Guru receives notification on usulan rejection
     * Validates: Requirement 8.1, 8.2
     */
    public function test_guru_receives_notification_on_usulan_rejection()
    {
        Notification::fake();

        // Create test data
        $guru = Guru::factory()->create();
        $kelompok = Kelompok::factory()->create(['tingkat' => TingkatKelompok::DASAR]);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'total_juz_tuntas' => 10,
        ]);

        $usulan = $this->service->createUsulan(
            $siswa->id,
            $guru->id,
            TingkatKelompok::MENENGAH->value,
            'Test rejection'
        );

        // Reject usulan
        $admin = \App\Models\Admin::factory()->create();
        $this->service->rejectUsulan($usulan->id, $admin->id, 'Not ready yet');

        // Assert notification was sent to guru
        Notification::assertSentTo(
            $guru,
            UsulanKenaikanTingkatDecisionNotification::class,
            function ($notification, $channels) use ($usulan) {
                return $notification->usulan->id === $usulan->id
                    && $notification->decision === 'rejected';
            }
        );
    }

    /**
     * Test: Siswa receives notification on placement
     * Validates: Requirement 9.1, 9.2
     */
    public function test_siswa_receives_notification_on_placement()
    {
        Notification::fake();

        // Create test data
        $guru = Guru::factory()->create();
        $kelompokAsal = Kelompok::factory()->create(['tingkat' => TingkatKelompok::DASAR]);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompokAsal->id]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'total_juz_tuntas' => 10,
        ]);

        // Create target group
        $kelompokTujuan = Kelompok::factory()->create([
            'tingkat' => TingkatKelompok::MENENGAH,
            'jenis' => $siswa->jenis_kelamin === 'Laki-laki' ? 'Banin' : 'Banat',
            'kapasitas_maks' => 12,
        ]);

        $usulan = $this->service->createUsulan(
            $siswa->id,
            $guru->id,
            TingkatKelompok::MENENGAH->value,
            'Test placement'
        );

        // Approve and place
        $admin = \App\Models\Admin::factory()->create();
        $this->service->approveUsulan($usulan->id, $admin->id);
        $this->service->placeStudent($usulan->id, $kelompokTujuan->id);

        // Assert notification was sent to siswa
        Notification::assertSentTo(
            $siswa,
            KenaikanTingkatPlacementNotification::class,
            function ($notification, $channels) use ($usulan) {
                return $notification->usulan->id === $usulan->id;
            }
        );
    }

    /**
     * Test: Notification is marked as read when accessed
     * Validates: Requirements 8.4, 9.4
     * Property 26: Accessing notification marks it read
     */
    public function test_notification_is_marked_as_read_when_accessed()
    {
        // Create test data
        $guru = Guru::factory()->create();
        $kelompok = Kelompok::factory()->create(['tingkat' => TingkatKelompok::DASAR]);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'total_juz_tuntas' => 10,
        ]);

        $usulan = $this->service->createUsulan(
            $siswa->id,
            $guru->id,
            TingkatKelompok::MENENGAH->value,
            'Test read status'
        );

        // Approve usulan (sends notification to guru)
        $admin = \App\Models\Admin::factory()->create();
        $this->service->approveUsulan($usulan->id, $admin->id);

        // Verify notification exists and is unread
        $guru->refresh();
        $this->assertEquals(1, $guru->unreadNotifications->count());

        $notification = $guru->unreadNotifications->first();
        $this->assertNull($notification->read_at);

        // Mark as read
        $notification->markAsRead();

        // Verify notification is now read
        $guru->refresh();
        $this->assertEquals(0, $guru->unreadNotifications->count());
        $this->assertEquals(1, $guru->readNotifications->count());

        $readNotification = $guru->readNotifications->first();
        $this->assertNotNull($readNotification->read_at);
    }

    /**
     * Test: Notification contains required context for guru
     * Validates: Requirement 8.2
     * Property 25: Notifications contain required context
     */
    public function test_guru_notification_contains_required_context()
    {
        // Create test data
        $guru = Guru::factory()->create();
        $kelompok = Kelompok::factory()->create(['tingkat' => TingkatKelompok::DASAR]);
        $siswa = Siswa::factory()->create([
            'nama' => 'Test Siswa',
            'kelompok_id' => $kelompok->id,
        ]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'total_juz_tuntas' => 10,
        ]);

        $usulan = $this->service->createUsulan(
            $siswa->id,
            $guru->id,
            TingkatKelompok::MENENGAH->value,
            'Test context'
        );

        // Approve with catatan
        $admin = \App\Models\Admin::factory()->create();
        $this->service->approveUsulan($usulan->id, $admin->id, 'Bagus sekali');

        // Get notification
        $guru->refresh();
        $notification = $guru->notifications->first();
        $data = $notification->data;

        // Assert required fields are present
        $this->assertArrayHasKey('siswa_nama', $data);
        $this->assertEquals('Test Siswa', $data['siswa_nama']);
        $this->assertArrayHasKey('decision', $data);
        $this->assertEquals('approved', $data['decision']);
        $this->assertArrayHasKey('catatan_keputusan', $data);
        $this->assertEquals('Bagus sekali', $data['catatan_keputusan']);
        $this->assertArrayHasKey('tingkat_tujuan', $data);
    }

    /**
     * Test: Notification contains required context for siswa
     * Validates: Requirement 9.2
     * Property 25: Notifications contain required context
     */
    public function test_siswa_notification_contains_required_context()
    {
        // Create test data
        $guru = Guru::factory()->create();
        $kelompokAsal = Kelompok::factory()->create(['tingkat' => TingkatKelompok::DASAR]);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompokAsal->id]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'total_juz_tuntas' => 10,
        ]);

        $guruPembimbing = Guru::factory()->create(['nama' => 'Ustadz Test']);
        $kelompokTujuan = Kelompok::factory()->create([
            'nama' => 'Kelompok Menengah A',
            'tingkat' => TingkatKelompok::MENENGAH,
            'jenis' => $siswa->jenis_kelamin === 'Laki-laki' ? 'Banin' : 'Banat',
            'guru_pembimbing_id' => $guruPembimbing->id,
            'kapasitas_maks' => 12,
        ]);

        $usulan = $this->service->createUsulan(
            $siswa->id,
            $guru->id,
            TingkatKelompok::MENENGAH->value,
            'Test context'
        );

        // Approve and place
        $admin = \App\Models\Admin::factory()->create();
        $this->service->approveUsulan($usulan->id, $admin->id);
        $this->service->placeStudent($usulan->id, $kelompokTujuan->id);

        // Get notification
        $siswa->refresh();
        $notification = $siswa->notifications->first();
        $data = $notification->data;

        // Assert required fields are present (Requirement 9.2)
        $this->assertArrayHasKey('tingkat_baru', $data);
        $this->assertEquals(TingkatKelompok::MENENGAH->value, $data['tingkat_baru']);
        $this->assertArrayHasKey('kelompok_nama', $data);
        $this->assertEquals('Kelompok Menengah A', $data['kelompok_nama']);
        $this->assertArrayHasKey('guru_pembimbing', $data);
        $this->assertEquals('Ustadz Test', $data['guru_pembimbing']);
        $this->assertArrayHasKey('tanggal_penempatan', $data);
    }

    /**
     * Test: Notifications are retained for current academic year
     * Validates: Requirements 8.5, 9.5
     */
    public function test_notifications_retained_for_current_academic_year()
    {
        // Create test data
        $guru = Guru::factory()->create();
        $kelompok = Kelompok::factory()->create(['tingkat' => TingkatKelompok::DASAR]);
        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        ProgresHafalan::factory()->create([
            'siswa_id' => $siswa->id,
            'total_juz_tuntas' => 10,
        ]);

        $usulan = $this->service->createUsulan(
            $siswa->id,
            $guru->id,
            TingkatKelompok::MENENGAH->value,
            'Test retention'
        );

        // Approve usulan
        $admin = \App\Models\Admin::factory()->create();
        $this->service->approveUsulan($usulan->id, $admin->id);

        // Verify notification exists
        $guru->refresh();
        $this->assertEquals(1, $guru->notifications->count());

        // Simulate old notification from previous academic year
        $oldNotification = $guru->notifications()->first();
        $oldNotification->created_at = now()->subYear(2);
        $oldNotification->save();

        // Create new notification for current year
        $usulan2 = $this->service->createUsulan(
            $siswa->id,
            $guru->id,
            TingkatKelompok::ATAS->value,
            'Test current year'
        );

        ProgresHafalan::where('siswa_id', $siswa->id)
            ->update(['total_juz_tuntas' => 20]);

        $this->service->approveUsulan($usulan2->id, $admin->id);

        // Get current academic year start
        $academicYearStart = now()->month >= 7
            ? now()->year . '-07-01'
            : (now()->year - 1) . '-07-01';

        // Count notifications from current year
        $guru->refresh();
        $currentYearNotifications = $guru->notifications()
            ->where('created_at', '>=', $academicYearStart)
            ->count();

        // Should only have 1 notification from current year
        $this->assertEquals(1, $currentYearNotifications);
    }
}
