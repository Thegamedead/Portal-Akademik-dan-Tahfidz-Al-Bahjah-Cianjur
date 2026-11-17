<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\TesPenempatan;

class TestCancelledNotification extends Notification
{
    use Queueable;

    public $tes;

    public function __construct(TesPenempatan $tes)
    {
        $this->tes = $tes;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'test_cancelled',
            'tes_id' => $this->tes->id,
            'siswa_nama' => $this->tes->siswa->nama,
            'alasan' => $this->tes->alasan_pembatalan,
            'message' => 'Jadwal tes penempatan untuk siswa ' . $this->tes->siswa->nama . ' telah dibatalkan.'
        ];
    }
}
