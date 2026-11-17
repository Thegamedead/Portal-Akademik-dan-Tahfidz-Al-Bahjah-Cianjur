<?php

namespace App\Notifications;

use App\Models\UsulanKenaikanTingkat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notification sent to Siswa when they are placed to a new group after approval
 *
 * Validates: Requirements 9.1, 9.2
 */
class KenaikanTingkatPlacementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public UsulanKenaikanTingkat $usulan,
    ) {}

    /**
     * Get the notification's delivery channels
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database storage
     *
     * Validates: Requirement 9.2 - notification SHALL include new Tingkat_Kelompok,
     * new Kelompok name, and effective date
     */
    public function toArray(object $notifiable): array
    {
        return [
            'usulan_id' => $this->usulan->id,
            'kelompok_tujuan_id' => $this->usulan->kelompok_tujuan_id,
            'tingkat_tujuan' => $this->usulan->tingkat_tujuan->value,
            'kelompok_name' => $this->usulan->kelompokTujuan->nama,
            'guru_pembimbing_name' => $this->usulan->kelompokTujuan->guruPembimbing->nama,
            'tanggal_penempatan' => $this->usulan->tanggal_penempatan?->toISOString(),
        ];
    }
}
