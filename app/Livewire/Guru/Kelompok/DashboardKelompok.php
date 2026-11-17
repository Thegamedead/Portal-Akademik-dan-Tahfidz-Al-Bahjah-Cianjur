<?php

namespace App\Livewire\Guru\Kelompok;

use App\Livewire\Admin\Kelompok\DashboardKelompok as AdminDashboardKelompok;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Kelompok;

#[Layout('guru.layouts.app')]
#[Title('Setoran Hafalan Kelompok')]
class DashboardKelompok extends AdminDashboardKelompok
{
    public function mount(Kelompok $kelompok): void
    {
        // Pastikan kelompok ini milik guru yang login
        if ($kelompok->guru_pembimbing_id !== auth('guru')->id()) {
            abort(403, 'Anda tidak memiliki akses ke kelompok ini.');
        }

        parent::mount($kelompok);
    }
}
