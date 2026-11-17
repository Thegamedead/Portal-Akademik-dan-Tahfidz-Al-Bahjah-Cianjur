<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\TesPenempatan;

class GuruAppointedNotification extends Notification
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
            'type' => 'guru_appointed',
            'tes_id' => $this->tes->id,
            'siswa_nama' => $this->tes->siswa->nama,
            'tanggal_tes' => $this->tes->tanggal_tes->format('Y-m-d'),
            'waktu_tes' => $this->tes->waktu_tes ? $this->tes->waktu_tes->format('H:i') : null,
            'message' => 'Anda dijadwalkan untuk menguji tes penempatan siswa ' . $this->tes->siswa->nama . ' pada ' . $this->tes->tanggal_tes->format('d M Y')
        ];
    }
}
