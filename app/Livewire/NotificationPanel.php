<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Notifications\DatabaseNotification;

/**
 * Notification Panel Component
 *
 * Displays notifications for authenticated user (Guru or Siswa)
 * and allows marking them as read when accessed.
 *
 * Validates: Requirements 8.3, 8.4, 9.3, 9.4
 */
class NotificationPanel extends Component
{
    public $notifications;
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadNotifications();
    }

    /**
     * Load notifications for the authenticated user
     *
     * Only loads notifications from the current academic year
     * Validates: Requirements 8.5, 9.5 - retain history for current academic year
     */
    public function loadNotifications()
    {
        $user = auth()->user();

        if (!$user) {
            $this->notifications = collect([]);
            $this->unreadCount = 0;
            return;
        }

        // Get current academic year start date from active tahun ajaran
        $activeTahunAjaran = \App\Models\TahunAjaran::where('status', true)->first();

        if ($activeTahunAjaran) {
            $academicYearStart = $activeTahunAjaran->tanggal_mulai;
        } else {
            // Fallback: if no active tahun ajaran, use July as academic year start
            $academicYearStart = now()->month >= 7
                ? now()->year . '-07-01'
                : (now()->year - 1) . '-07-01';
        }

        // Load notifications from current academic year
        $this->notifications = $user->notifications()
            ->where('created_at', '>=', $academicYearStart)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->unreadCount = $user->unreadNotifications()
            ->where('created_at', '>=', $academicYearStart)
            ->count();
    }

    /**
     * Mark a notification as read when accessed
     *
     * Validates: Requirements 8.4, 9.4 - mark as read when accessed
     */
    public function markAsRead($notificationId)
    {
        $user = auth()->user();

        if (!$user) {
            return;
        }

        $notification = $user->notifications()->find($notificationId);

        if ($notification && $notification->unread()) {
            $notification->markAsRead();
            $this->loadNotifications(); // Refresh the list

            $this->dispatch('notification-read', ['id' => $notificationId]);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = auth()->user();

        if (!$user) {
            return;
        }

        $user->unreadNotifications->markAsRead();
        $this->loadNotifications();

        $this->dispatch('all-notifications-read');
    }

    public function render()
    {
        return view('livewire.notification-panel');
    }
}
