<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\TesPenempatan;

class TestResultsSubmittedNotification extends Notification
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
            'type' => 'test_results_submitted',
            'tes_id' => $this->tes->id,
            'siswa_nama' => $this->tes->siswa->nama,
            'guru_nama' => $this->tes->guruPenguji->nama,
            'message' => 'Hasil tes penempatan untuk siswa ' . $this->tes->siswa->nama . ' telah disubmit oleh ' . $this->tes->guruPenguji->nama
        ];
    }
}
