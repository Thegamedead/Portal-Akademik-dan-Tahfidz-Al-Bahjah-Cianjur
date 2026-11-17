<?php

namespace App\Notifications;

use App\Models\UsulanKenaikanTingkat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * PlacementCancelledNotification
 *
 * Notifies student when their placement has been cancelled and they
 * have been returned to their original kelompok.
 */
class PlacementCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected UsulanKenaikanTingkat $usulan;

    /**
     * Create a new notification instance.
     */
    public function __construct(UsulanKenaikanTingkat $usulan)
    {
        $this->usulan = $usulan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Penempatan Dibatalkan',
            'message' => "Penempatan Anda ke Tingkat {$this->usulan->tingkat_tujuan->value} telah dibatalkan oleh admin. Anda dikembalikan ke kelompok asal: {$this->usulan->kelompokAsal->nama}.",
            'usulan_id' => $this->usulan->id,
            'type' => 'placement_cancelled',
            'kelompok_asal' => $this->usulan->kelompokAsal->nama,
            'tingkat_asal' => $this->usulan->tingkat_asal->value,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
