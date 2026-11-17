<?php

namespace App\Livewire\Admin\Absensi;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\{Kelompok, Absensi, Setoran};
use Carbon\Carbon;

#[Layout('admin.layouts.app')]
#[Title('Absensi dan Setoran Hafalan')]
class IndexAbsensi extends Component
{
    public $kelompoks;
    public $absensiStatus = [];
    public $statsPerKelompok = [];
    public $globalStats = [];

    /**
     * Mount component
     */
    public function mount(): void
    {
        $this->kelompoks = Kelompok::with(['guruPembimbing', 'siswas' => function ($query) {
            $query->where('status_akademik', 'Aktif');
        }])
        ->orderBy('nama')
        ->get();

        $this->checkAbsensiStatus();
        $this->loadStats();
    }

    /**
     * Check if absensi filled today for each kelompok
     */
    protected function checkAbsensiStatus(): void
    {
        $today = today();
        foreach ($this->kelompoks as $kelompok) {
            $this->absensiStatus[$kelompok->id] = Absensi::where('kelompok_id', $kelompok->id)
                ->whereDate('tanggal', $today)
                ->exists();
        }
    }

    /**
     * Load per-kelompok and global stats for today and current month
     */
    protected function loadStats(): void
    {
        $today      = today()->format('Y-m-d');
        $monthStart = now()->startOfMonth()->format('Y-m-d');
        $monthEnd   = now()->endOfMonth()->format('Y-m-d');

        // Today's absensi by kelompok + status
        $todayAbsensi = Absensi::selectRaw('kelompok_id, status_kehadiran, COUNT(*) as total')
            ->whereDate('tanggal', $today)
            ->groupBy('kelompok_id', 'status_kehadiran')
            ->get()
            ->groupBy('kelompok_id');

        // Today's setoran count per kelompok
        $todaySetoran = Setoran::selectRaw('absensis.kelompok_id, COUNT(setorans.id) as total')
            ->join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
            ->whereDate('absensis.tanggal', $today)
            ->groupBy('absensis.kelompok_id')
            ->pluck('total', 'absensis.kelompok_id');

        // Sessions (distinct days with absensi) this month per kelompok
        $monthSessions = Absensi::selectRaw('kelompok_id, COUNT(DISTINCT tanggal) as total')
            ->whereBetween('tanggal', [$monthStart, $monthEnd])
            ->groupBy('kelompok_id')
            ->pluck('total', 'kelompok_id');

        foreach ($this->kelompoks as $kelompok) {
            $kid        = $kelompok->id;
            $statusRows = $todayAbsensi->get($kid, collect());

            $hadir = (int) ($statusRows->firstWhere('status_kehadiran', 'Hadir')?->total ?? 0);
            $izin  = (int) ($statusRows->firstWhere('status_kehadiran', 'Izin')?->total  ?? 0);
            $sakit = (int) ($statusRows->firstWhere('status_kehadiran', 'Sakit')?->total ?? 0);
            $alfa  = (int) ($statusRows->firstWhere('status_kehadiran', 'Alfa')?->total  ?? 0);

            $this->statsPerKelompok[$kid] = [
                'hadir'            => $hadir,
                'izin'             => $izin,
                'sakit'            => $sakit,
                'alfa'             => $alfa,
                'setoran_hari_ini' => (int) $todaySetoran->get($kid, 0),
                'sesi_bulan_ini'   => (int) $monthSessions->get($kid, 0),
            ];
        }

        // Global summary card
        $this->globalStats = [
            'total_kelompok'         => $this->kelompoks->count(),
            'sudah_input'            => collect($this->absensiStatus)->filter()->count(),
            'belum_input'            => collect($this->absensiStatus)->reject(fn($v) => $v)->count(),
            'total_setoran_hari_ini' => (int) $todaySetoran->sum(),
        ];
    }

    /**
     * Get total active students in kelompok
     */
    public function getTotalSiswaAktif(Kelompok $kelompok): int
    {
        return $kelompok->siswas->count();
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.admin.absensi.index-absensi');
    }
}
