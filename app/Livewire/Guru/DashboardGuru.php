<?php

namespace App\Livewire\Guru;

use App\Models\Siswa;
use App\Models\Kelompok;
use App\Models\Setoran;
use App\Models\Absensi;
use App\Models\TahunAjaran;
use Livewire\Component;

class DashboardGuru extends Component
{
    /**
     * Get data statistik untuk guru yang login
     */
    public function getDashboardDataProperty(): array
    {
        $guru = auth('guru')->user();
        $currentTahunAjaran = TahunAjaran::where('status', true)->first();

        if (!$guru || !$currentTahunAjaran) {
            return [
                'kelompok_dibimbing' => 0,
                'total_siswa_bimbingan' => 0,
                'setoran_bulan_ini' => 0,
                'absensi_bulan_ini' => 0,
                'siswa_per_tingkat' => ['Dasar' => 0, 'Menengah' => 0, 'Atas' => 0],
                'kelompok_list' => [],
                'recent_activity' => []
            ];
        }

        // Kelompok yang dibimbing guru
        $kelompokDibimbing = Kelompok::where('guru_pembimbing_id', $guru->id)
            ->where('tahun_ajaran_id', $currentTahunAjaran->id)
            ->withCount(['siswas' => function ($query) {
                $query->where('status_aktif', true)
                      ->where('status_akademik', 'Aktif');
            }])
            ->get();

        $kelompokIds = $kelompokDibimbing->pluck('id');

        // Total siswa bimbingan
        $totalSiswaBimbingan = Siswa::whereIn('kelompok_id', $kelompokIds)
            ->where('status_aktif', true)
            ->where('status_akademik', 'Aktif')
            ->count();

        // Setoran bulan ini
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $setoranBulanIni = Setoran::whereHas('absensi', function ($query) use ($kelompokIds, $startOfMonth, $endOfMonth) {
            $query->whereIn('kelompok_id', $kelompokIds)
                  ->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
        })->count();

        // Absensi bulan ini
        $absensiBulanIni = Absensi::whereIn('kelompok_id', $kelompokIds)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->count();

        // Distribusi siswa per tingkat
        $siswaPerTingkat = $kelompokDibimbing->groupBy('tingkat')
            ->map(function ($items) {
                return $items->sum('siswas_count');
            })
            ->toArray();

        // Ensure all tingkat exist
        $siswaPerTingkat = array_merge(
            ['Dasar' => 0, 'Menengah' => 0, 'Atas' => 0],
            $siswaPerTingkat
        );

        // Kelompok list dengan detail
        $kelompokList = $kelompokDibimbing->map(function ($kelompok) {
            return [
                'id' => $kelompok->id,
                'nama' => $kelompok->nama,
                'tingkat' => $kelompok->tingkat instanceof \BackedEnum ? $kelompok->tingkat->value : (string) $kelompok->tingkat,
                'jumlah_siswa' => $kelompok->siswas_count,
                'kapasitas' => $kelompok->kapasitas_maks
            ];
        });

        // Recent activity (setoran terbaru dari kelompok guru)
        $recentActivity = Setoran::with(['absensi.siswa', 'absensi.kelompok', 'juz', 'surah'])
            ->whereHas('absensi', function ($query) use ($kelompokIds) {
                $query->whereIn('kelompok_id', $kelompokIds);
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($setoran) {
                return [
                    'tanggal' => $setoran->absensi->tanggal,
                    'siswa_nama' => $setoran->absensi->siswa->nama ?? 'Unknown',
                    'kelompok_nama' => $setoran->absensi->kelompok->nama ?? 'Unknown',
                    'juz' => $setoran->juz ? $setoran->juz->juz_ke : $setoran->juz_id,
                    'surah' => $setoran->surah ? $setoran->surah->nama_latin : $setoran->surah_id,
                    'keputusan' => $setoran->keputusan instanceof \App\Enums\Keputusan ? $setoran->keputusan->value : $setoran->keputusan,
                    'predikat' => $setoran->predikat instanceof \App\Enums\SkorKomponen ? $setoran->predikat->value : $setoran->predikat,
                    'jenis_setoran' => $setoran->jenis_setoran instanceof \App\Enums\JenisSetoran ? $setoran->jenis_setoran->label() : $setoran->jenis_setoran,
                ];
            });

        return [
            'kelompok_dibimbing' => $kelompokDibimbing->count(),
            'total_siswa_bimbingan' => $totalSiswaBimbingan,
            'setoran_bulan_ini' => $setoranBulanIni,
            'absensi_bulan_ini' => $absensiBulanIni,
            'siswa_per_tingkat' => $siswaPerTingkat,
            'kelompok_list' => $kelompokList,
            'recent_activity' => $recentActivity
        ];
    }

    public function render()
    {
        return view('livewire.guru.dashboard-guru', [
            'dashboardData' => $this->dashboardData
        ])->layout('guru.layouts.app', ['title' => 'Dashboard Guru']);
    }
}
