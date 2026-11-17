<?php

namespace App\Notifications;

use App\Models\UsulanKenaikanTingkat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notification sent to Guru when Admin approves or rejects their usulan
 *
 * Validates: Requirements 8.1, 8.2
 */
class UsulanKenaikanTingkatDecisionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public UsulanKenaikanTingkat $usulan,
        public string $decision, // 'approved', 'rejected', or 'placement_cancelled'
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
     * Validates: Requirement 8.2 - notification SHALL include student name,
     * decision, and catatan_keputusan if provided
     */
    public function toArray(object $notifiable): array
    {
        $label = 'Ditolak';
        if ($this->decision === 'approved') {
            $label = 'Disetujui';
        } elseif ($this->decision === 'placement_cancelled') {
            $label = 'Penempatan Dibatalkan';
        } elseif ($this->decision === 'cancelled') {
            $label = 'Persetujuan Dibatalkan';
        }

        return [
            'usulan_id' => $this->usulan->id,
            'siswa_name' => $this->usulan->siswa->nama,
            'decision_type' => $this->decision,
            'decision_label' => $label,
            'catatan_keputusan' => $this->usulan->catatan_keputusan,
            'tanggal_keputusan' => $this->usulan->tanggal_keputusan?->toISOString(),
            'tingkat_tujuan' => $this->usulan->tingkat_tujuan->value,
        ];
    }
}
