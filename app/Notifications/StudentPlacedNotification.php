<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\TesPenempatan;

class StudentPlacedNotification extends Notification
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
            'type' => 'student_placed',
            'tes_id' => $this->tes->id,
            'siswa_nama' => $this->tes->siswa->nama,
            'kelompok_nama' => $this->tes->finalKelompok ? $this->tes->finalKelompok->nama : 'Tanpa Kelompok',
            'message' => 'Anda telah ditempatkan di kelompok ' . ($this->tes->finalKelompok ? $this->tes->finalKelompok->nama : 'baru') . ' berdasarkan hasil tes penempatan.'
        ];
    }
}
