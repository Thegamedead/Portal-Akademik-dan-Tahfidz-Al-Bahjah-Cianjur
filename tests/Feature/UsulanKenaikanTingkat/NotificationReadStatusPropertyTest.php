<?php

namespace Tests\Feature\UsulanKenaikanTingkat;

use App\Enums\StatusUsulan;
use App\Livewire\NotificationPanel;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\UsulanKenaikanTingkat;
use App\Notifications\KenaikanTingkatPlacementNotification;
use App\Notifications\UsulanKenaikanTingkatDecisionNotification;
use Eris\Generators;
use Eris\TestTrait;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * 7.4 — NotificationReadStatusPropertyTest
 *
 * Property 26: Accessing notification marks it read
 *
 * For any notification (teacher or student), when accessed by the intended
 * recipient, the system SHALL mark it as read.
 *
 * This property test verifies that:
 * 1. Unread notification changes to read status when accessed
 * 2. Read status persists after access
 * 3. Multiple accesses don't cause errors
 * 4. Only the intended recipient can mark as read
 *
 * **Validates: Requirements 8.4, 9.4**
 */
class NotificationReadStatusPropertyTest extends TestCase
{
    use RefreshDatabase;
    use TestTrait;

    /**
     * Property: Teacher notifications — accessing marks as read
     *
     * Strategy: Generate random approved/rejected decisions, create notifications
     * for guru, verify that calling markAsRead sets read_at timestamp
     */
    public function test_property_guru_notification_marks_as_read_on_access(): void
    {
        $this->limitTo(100)->forAll(
            // Decision type: approved or rejected
            Generators::elements('approved', 'rejected'),
            // Random seed for unique data generation
            Generators::choose(1, 999999)
        )->then(function (string $decisionType, int $seed) {
            // Create unique guru for this iteration
            $guru = Guru::factory()->create([
                'niy' => 'G' . str_pad((string) $seed, 9, '0', STR_PAD_LEFT),
                'status_aktif' => true,
            ]);

            // Create an usulan with appropriate status
            $usulan = UsulanKenaikanTingkat::factory()->create([
                'guru_pengusul_id' => $guru->id,
                'status_usulan' => $decisionType === 'approved'
                    ? StatusUsulan::DISETUJUI
                    : StatusUsulan::DITOLAK,
            ]);

            // Create notification for guru
            $guru->notify(new UsulanKenaikanTingkatDecisionNotification($usulan, $decisionType));

            // Verify notification is initially unread
            $notification = $guru->notifications()->first();
            $this->assertNotNull($notification, "Notification should exist for guru (seed={$seed})");
            $this->assertNull(
                $notification->read_at,
                "Notification should be unread initially (seed={$seed})"
            );

            // Act as guru and mark notification as read
            $this->actingAs($guru, 'guru');

            Livewire::test(NotificationPanel::class)
                ->call('markAsRead', $notification->id);

            // Verify notification is now marked as read
            $notification->refresh();
            $this->assertNotNull(
                $notification->read_at,
                "Notification should be marked as read after access (seed={$seed})"
            );

            // Verify read_at is a recent timestamp (within last 5 seconds)
            $this->assertTrue(
                $notification->read_at->diffInSeconds(now()) < 5,
                "Read timestamp should be recent (seed={$seed})"
            );
        });
    }

    /**
     * Property: Student notifications — accessing marks as read
     *
     * Strategy: Generate random placement notifications for students,
     * verify that accessing marks them as read
     */
    public function test_property_siswa_notification_marks_as_read_on_access(): void
    {
        $this->limitTo(100)->forAll(
            // Random seed for unique data generation
            Generators::choose(1, 999999)
        )->then(function (int $seed) {
            // Create unique siswa for this iteration
            $siswa = Siswa::factory()->create([
                'nis' => 'S' . str_pad((string) $seed, 9, '0', STR_PAD_LEFT),
                'status_aktif' => true,
            ]);

            // Create a placed usulan
            $usulan = UsulanKenaikanTingkat::factory()->ditempatkan()->create([
                'siswa_id' => $siswa->id,
            ]);

            // Create notification for siswa
            $siswa->notify(new KenaikanTingkatPlacementNotification($usulan));

            // Verify notification is initially unread
            $notification = $siswa->notifications()->first();
            $this->assertNotNull($notification, "Notification should exist for siswa (seed={$seed})");
            $this->assertNull(
                $notification->read_at,
                "Notification should be unread initially (seed={$seed})"
            );

            // Act as siswa and mark notification as read
            $this->actingAs($siswa, 'siswa');

            Livewire::test(NotificationPanel::class)
                ->call('markAsRead', $notification->id);

            // Verify notification is now marked as read
            $notification->refresh();
            $this->assertNotNull(
                $notification->read_at,
                "Notification should be marked as read after access (seed={$seed})"
            );

            // Verify read_at is a recent timestamp (within last 5 seconds)
            $this->assertTrue(
                $notification->read_at->diffInSeconds(now()) < 5,
                "Read timestamp should be recent (seed={$seed})"
            );
        });
    }

    /**
     * Property: Read status persists after multiple accesses
     *
     * Strategy: Mark notification as read, access multiple times,
     * verify read_at timestamp doesn't change
     */
    public function test_property_read_status_persists_across_multiple_accesses(): void
    {
        $this->limitTo(100)->forAll(
            // Number of repeated accesses (2-5)
            Generators::choose(2, 5),
            // Random seed
            Generators::choose(1, 999999)
        )->then(function (int $accessCount, int $seed) {
            $guru = Guru::factory()->create([
                'niy' => 'G' . str_pad((string) $seed, 9, '0', STR_PAD_LEFT),
                'status_aktif' => true,
            ]);

            $usulan = UsulanKenaikanTingkat::factory()->create([
                'guru_pengusul_id' => $guru->id,
                'status_usulan' => StatusUsulan::DISETUJUI,
            ]);

            $guru->notify(new UsulanKenaikanTingkatDecisionNotification($usulan, 'approved'));

            $this->actingAs($guru, 'guru');
            $notification = $guru->notifications()->first();

            // First access - mark as read
            Livewire::test(NotificationPanel::class)
                ->call('markAsRead', $notification->id);

            $notification->refresh();
            $firstReadAt = $notification->read_at;

            $this->assertNotNull($firstReadAt, "Notification should be read (seed={$seed})");

            // Wait a tiny moment to ensure timestamp would change if updated
            usleep(10000); // 10ms

            // Multiple subsequent accesses
            for ($i = 1; $i < $accessCount; $i++) {
                Livewire::test(NotificationPanel::class)
                    ->call('markAsRead', $notification->id);

                $notification->refresh();

                // Verify read_at timestamp hasn't changed
                $this->assertEquals(
                    $firstReadAt->timestamp,
                    $notification->read_at->timestamp,
                    "Read timestamp should remain unchanged after access #{$i} (seed={$seed})"
                );
            }
        });
    }

    /**
     * Property: Only intended recipient can mark notification as read
     *
     * Strategy: Create notification for guru A, attempt to mark as read
     * from guru B, verify it remains unread
     */
    public function test_property_only_intended_recipient_can_mark_as_read(): void
    {
        $this->limitTo(100)->forAll(
            // Random seeds for two different users
            Generators::choose(1, 999999),
            Generators::choose(1, 999999)
        )->then(function (int $seed1, int $seed2) {
            // Ensure seeds are different
            if ($seed1 === $seed2) {
                $seed2 = ($seed2 % 999999) + 1;
            }

            // Create two gurus
            $guruA = Guru::factory()->create([
                'niy' => 'GA' . str_pad((string) $seed1, 8, '0', STR_PAD_LEFT),
                'status_aktif' => true,
            ]);

            $guruB = Guru::factory()->create([
                'niy' => 'GB' . str_pad((string) $seed2, 8, '0', STR_PAD_LEFT),
                'status_aktif' => true,
            ]);

            // Create usulan and notification for guruA
            $usulan = UsulanKenaikanTingkat::factory()->create([
                'guru_pengusul_id' => $guruA->id,
                'status_usulan' => StatusUsulan::DISETUJUI,
            ]);

            $guruA->notify(new UsulanKenaikanTingkatDecisionNotification($usulan, 'approved'));

            $notification = $guruA->notifications()->first();
            $this->assertNotNull($notification, "Notification should exist for guruA");

            // Act as guruB and attempt to mark guruA's notification as read
            $this->actingAs($guruB, 'guru');

            Livewire::test(NotificationPanel::class)
                ->call('markAsRead', $notification->id);

            // Verify notification remains unread (guruB cannot mark guruA's notification)
            $notification->refresh();
            $this->assertNull(
                $notification->read_at,
                "Notification should remain unread when accessed by wrong user (seed1={$seed1}, seed2={$seed2})"
            );

            // Now act as guruA and verify they CAN mark it as read
            $this->actingAs($guruA, 'guru');

            Livewire::test(NotificationPanel::class)
                ->call('markAsRead', $notification->id);

            $notification->refresh();
            $this->assertNotNull(
                $notification->read_at,
                "Notification should be marked as read when accessed by correct user (seed1={$seed1})"
            );
        });
    }

    /**
     * Property: Unread count decreases when notification is marked as read
     *
     * Strategy: Create multiple notifications, mark one as read,
     * verify unread count decreases by 1
     */
    public function test_property_unread_count_decreases_when_marked_as_read(): void
    {
        $this->limitTo(100)->forAll(
            // Number of notifications to create (2-5)
            Generators::choose(2, 5),
            // Random seed
            Generators::choose(1, 999999)
        )->then(function (int $notificationCount, int $seed) {
            $guru = Guru::factory()->create([
                'niy' => 'G' . str_pad((string) $seed, 9, '0', STR_PAD_LEFT),
                'status_aktif' => true,
            ]);

            // Create multiple notifications
            for ($i = 0; $i < $notificationCount; $i++) {
                $usulan = UsulanKenaikanTingkat::factory()->create([
                    'guru_pengusul_id' => $guru->id,
                    'status_usulan' => StatusUsulan::DISETUJUI,
                ]);

                $guru->notify(new UsulanKenaikanTingkatDecisionNotification($usulan, 'approved'));
            }

            $this->actingAs($guru, 'guru');

            // Verify initial unread count
            $initialUnreadCount = $guru->unreadNotifications()->count();
            $this->assertEquals(
                $notificationCount,
                $initialUnreadCount,
                "Initial unread count should match created notifications (seed={$seed})"
            );

            // Mark first notification as read
            $firstNotification = $guru->notifications()->first();

            Livewire::test(NotificationPanel::class)
                ->call('markAsRead', $firstNotification->id);

            // Verify unread count decreased by 1
            $newUnreadCount = $guru->unreadNotifications()->count();
            $this->assertEquals(
                $initialUnreadCount - 1,
                $newUnreadCount,
                "Unread count should decrease by 1 after marking one as read (seed={$seed})"
            );
        });
    }

    /**
     * Property: markAllAsRead marks all notifications as read
     *
     * Strategy: Create multiple notifications, call markAllAsRead,
     * verify all have read_at timestamp
     */
    public function test_property_mark_all_as_read_marks_all_notifications(): void
    {
        $this->limitTo(100)->forAll(
            // Number of notifications to create (1-10)
            Generators::choose(1, 10),
            // Random seed
            Generators::choose(1, 999999)
        )->then(function (int $notificationCount, int $seed) {
            $guru = Guru::factory()->create([
                'niy' => 'G' . str_pad((string) $seed, 9, '0', STR_PAD_LEFT),
                'status_aktif' => true,
            ]);

            // Create multiple notifications
            for ($i = 0; $i < $notificationCount; $i++) {
                $usulan = UsulanKenaikanTingkat::factory()->create([
                    'guru_pengusul_id' => $guru->id,
                    'status_usulan' => StatusUsulan::DISETUJUI,
                ]);

                $guru->notify(new UsulanKenaikanTingkatDecisionNotification($usulan, 'approved'));
            }

            $this->actingAs($guru, 'guru');

            // Verify all notifications are unread
            $unreadCount = $guru->unreadNotifications()->count();
            $this->assertEquals(
                $notificationCount,
                $unreadCount,
                "All notifications should be unread initially (seed={$seed})"
            );

            // Call markAllAsRead
            Livewire::test(NotificationPanel::class)
                ->call('markAllAsRead');

            // Verify all notifications are now read
            $remainingUnread = $guru->unreadNotifications()->count();
            $this->assertEquals(
                0,
                $remainingUnread,
                "All notifications should be marked as read (seed={$seed})"
            );

            // Verify all notifications have read_at timestamp
            $allNotifications = $guru->notifications()->get();
            foreach ($allNotifications as $notification) {
                $this->assertNotNull(
                    $notification->read_at,
                    "Each notification should have read_at timestamp (seed={$seed})"
                );
            }
        });
    }
}
