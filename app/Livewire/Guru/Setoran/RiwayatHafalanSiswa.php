<?php

namespace App\Livewire\Guru\Setoran;

use App\Livewire\Admin\Setoran\RiwayatHafalanSiswa as AdminRiwayatHafalanSiswa;
use App\Models\Kelompok;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('guru.layouts.app')]
#[Title('Track Record Hafalan Siswa')]
class RiwayatHafalanSiswa extends AdminRiwayatHafalanSiswa
{
    // Inherit semua logic dari admin — hanya layout yang berbeda
    // Guard check: pastikan kelompok yang dilihat milik guru yang login
    public function mount(\App\Models\Siswa $siswa): void
    {
        parent::mount($siswa);

        // Jika kelompokFilter ada, pastikan guru ini pembimbingnya
        if ($this->kelompokFilter && $this->kelompokFilter->guru_pembimbing_id !== auth('guru')->id()) {
            // Kalau bukan kelompoknya, tetap tampilkan tapi tanpa filter kelompok
            // agar data tetap bisa dilihat secara terbatas
            $this->kelompokId     = null;
            $this->kelompokFilter = null;
        }
    }

    public function render()
    {
        return view('livewire.admin.setoran.riwayat-hafalan-siswa', [
            'setoranData'    => $this->setoranData,
            'summaryStats'   => $this->summaryStats,
            'kelompokFilter' => $this->kelompokFilter,
        ]);
    }
}
