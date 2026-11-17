<?php

namespace App\Livewire\Siswa\Progres;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Services\ProgresHafalanService;

/**
 * DashboardProgresSiswa
 *
 * Livewire component untuk dashboard progres hafalan Siswa/Ortu.
 * Menampilkan pencapaian siswa login, termasuk target kelulusan
 * dan riwayat setoran terbaru.
 *
 * Requirements: 7.1, 7.2, 7.5, 11.1, 11.2
 */
#[Layout('siswa.layouts.app')]
#[Title('Dashboard Siswa')]
class DashboardProgresSiswa extends Component
{
    /** @var array Data dashboard dari service */
    public array $dashboardData = [];

    protected ProgresHafalanService $progresService;

    /**
     * Boot component dengan service injection
     */
    public function boot(ProgresHafalanService $progresService): void
    {
        $this->progresService = $progresService;
    }

    /**
     * Mount: Get data for logged in student
     */
    public function mount(): void
    {
        $siswa = auth()->guard('siswa')->user();

        if ($siswa) {
            $this->dashboardData = $this->progresService->getDashboardDataForSiswa($siswa->id);
        }
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.siswa.progres.dashboard-progres-siswa');
    }
}
