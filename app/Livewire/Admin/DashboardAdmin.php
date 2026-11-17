<?php

namespace App\Livewire\Admin;

use App\Models\UsulanKenaikanTingkat;
use App\Models\KelulusanProgram;
use App\Models\TahunAjaran;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelompok;
use App\Models\Setoran;
use App\Models\ParameterKonfigurasi;
use App\Enums\StatusUsulan;
use App\Enums\StatusKelulusan;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layouts.app')]
#[Title('Dashboard Admin')]
class DashboardAdmin extends Component
{
    protected $listeners = [
        'usulan-created' => '$refresh',
        'usulan-processed' => '$refresh',
        'usulan-placed' => '$refresh',
        'kelulusan-processed' => '$refresh',
    ];

    /**
     * Master Data KPI
     */
    public function getMasterDataStatisticsProperty(): array
    {
        $totalSiswa = Siswa::where('status_aktif', true)->where('status_akademik', 'Aktif')->count();
        $siswaBelumDitempatkan = Siswa::where('status_aktif', true)
            ->where('status_akademik', 'Aktif')
            ->whereNull('kelompok_id')
            ->count();
        $siswaDitempatkan = $totalSiswa - $siswaBelumDitempatkan;

        // Kelulusan Program Stats
        $siswaLulusTotal = Siswa::where(function ($q) {
            $q->where('status_tahfidz', 'like', '%Lulus%')
              ->orWhereHas('kelulusanProgram', function ($kp) {
                  $kp->where('status_kelulusan', StatusKelulusan::DISETUJUI->value);
              });
        })->count();

        $kelulusanPending = KelulusanProgram::where('status_kelulusan', StatusKelulusan::DIAJUKAN->value)->count();
        $kelulusanTahunIni = KelulusanProgram::where('status_kelulusan', StatusKelulusan::DISETUJUI->value)
            ->whereYear('tanggal_keputusan', now()->year)
            ->count();

        return [
            'total_siswa'             => $totalSiswa,
            'siswa_ditempatkan'       => $siswaDitempatkan,
            'siswa_belum_ditempatkan' => $siswaBelumDitempatkan,
            'persen_penempatan'       => $totalSiswa > 0 ? round(($siswaDitempatkan / $totalSiswa) * 100, 1) : 0,
            'siswa_lulus_total'       => $siswaLulusTotal,
            'kelulusan_pending'       => $kelulusanPending,
            'kelulusan_tahun_ini'     => $kelulusanTahunIni,
            'total_guru'              => Guru::where('status_aktif', true)->count(),
            'total_kelompok'          => Kelompok::count(),
            'kelompok_dasar'          => Kelompok::where('tingkat', 'Dasar')->count(),
            'kelompok_menengah'       => Kelompok::where('tingkat', 'Menengah')->count(),
            'kelompok_atas'           => Kelompok::where('tingkat', 'Atas')->count(),
        ];
    }

    /**
     * Jenjang Breakdown (Siswa, Kelompok, & Target Juz)
     */
    public function getJenjangBreakdownProperty(): array
    {
        $distribution = DB::table('siswas')
            ->join('kelompoks', 'siswas.kelompok_id', '=', 'kelompoks.id')
            ->where('siswas.status_aktif', true)
            ->where('siswas.status_akademik', 'Aktif')
            ->select('kelompoks.tingkat', DB::raw('count(siswas.id) as count'))
            ->groupBy('kelompoks.tingkat')
            ->pluck('count', 'tingkat');

        $dasarSiswa    = (int) $distribution->get('Dasar', 0);
        $menengahSiswa = (int) $distribution->get('Menengah', 0);
        $atasSiswa     = (int) $distribution->get('Atas', 0);
        $totalSiswa    = $dasarSiswa + $menengahSiswa + $atasSiswa;

        $config = ParameterKonfigurasi::first();
        $batas  = $config->batas_tingkat ?? [];

        $dasarJuz    = $batas['Dasar']    ?? [30];
        $menengahJuz = $batas['Menengah'] ?? [29, 28];
        $atasJuz     = $batas['Atas']     ?? [1, 2];

        return [
            'total_siswa'     => $totalSiswa,
            'dasar' => [
                'siswa'        => $dasarSiswa,
                'kelompok'     => Kelompok::where('tingkat', 'Dasar')->count(),
                'persen'       => $totalSiswa > 0 ? round(($dasarSiswa / $totalSiswa) * 100, 1) : 0,
                'juz'          => $dasarJuz,
                'juz_label'    => count($dasarJuz) === 1 ? 'Juz ' . $dasarJuz[0] : 'Juz ' . implode(' & ', $dasarJuz),
                'target_count' => count($dasarJuz),
            ],
            'menengah' => [
                'siswa'        => $menengahSiswa,
                'kelompok'     => Kelompok::where('tingkat', 'Menengah')->count(),
                'persen'       => $totalSiswa > 0 ? round(($menengahSiswa / $totalSiswa) * 100, 1) : 0,
                'juz'          => $menengahJuz,
                'juz_label'    => count($menengahJuz) <= 3 ? 'Juz ' . implode(' & ', $menengahJuz) : 'Juz ' . $menengahJuz[0] . ' – ' . end($menengahJuz),
                'target_count' => count($menengahJuz),
            ],
            'atas' => [
                'siswa'        => $atasSiswa,
                'kelompok'     => Kelompok::where('tingkat', 'Atas')->count(),
                'persen'       => $totalSiswa > 0 ? round(($atasSiswa / $totalSiswa) * 100, 1) : 0,
                'juz'          => $atasJuz,
                'juz_label'    => count($atasJuz) <= 3 ? 'Juz ' . implode(' & ', $atasJuz) : 'Juz ' . $atasJuz[0] . ' – ' . end($atasJuz),
                'target_count' => count($atasJuz),
            ],
            'target_kelulusan' => $config->target_kelulusan ?? 5,
        ];
    }

    /**
     * Usulan Kenaikan Tingkat & Kelulusan Statistics
     */
    public function getUsulanStatisticsProperty(): array
    {
        $counts = UsulanKenaikanTingkat::select('status_usulan', DB::raw('count(*) as count'))
            ->groupBy('status_usulan')
            ->pluck('count', 'status_usulan');

        $diajukan    = (int) $counts->get(StatusUsulan::DIAJUKAN->value, 0);
        $disetujui   = (int) $counts->get(StatusUsulan::DISETUJUI->value, 0);
        $ditempatkan = (int) $counts->get(StatusUsulan::DITEMPATKAN->value, 0);
        $ditolak     = (int) $counts->get(StatusUsulan::DITOLAK->value, 0);

        return [
            'diajukan'    => $diajukan,
            'disetujui'   => $disetujui,
            'ditempatkan' => $ditempatkan,
            'ditolak'     => $ditolak,
            'total'       => $diajukan + $disetujui + $ditempatkan + $ditolak,
        ];
    }

    /**
     * Setoran Activity Stats
     */
    public function getActivityStatisticsProperty(): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth   = now()->endOfMonth();
        $startOfWeek  = now()->startOfWeek();
        $endOfWeek    = now()->endOfWeek();

        $setoranBulanIni = Setoran::whereHas('absensi', function ($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('tanggal', [$startOfMonth, $endOfMonth]);
        })->count();

        $setoranMingguIni = Setoran::whereHas('absensi', function ($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('tanggal', [$startOfWeek, $endOfWeek]);
        })->count();

        $setoranHariIni = Setoran::whereHas('absensi', function ($query) {
            $query->whereDate('tanggal', today());
        })->count();

        return [
            'setoran_bulan_ini'  => $setoranBulanIni,
            'setoran_minggu_ini' => $setoranMingguIni,
            'setoran_hari_ini'   => $setoranHariIni,
        ];
    }

    /**
     * Recent Activities (Latest Setoran & Pending Usulan)
     */
    public function getLatestActivitiesProperty(): array
    {
        $latestSetorans = Setoran::with(['absensi.siswa', 'absensi.kelompok.guruPembimbing', 'surah', 'juz'])
            ->latest('id')
            ->take(5)
            ->get();

        $pendingKelulusans = KelulusanProgram::with(['siswa', 'kelompok.guruPembimbing', 'guruPengusul'])
            ->where('status_kelulusan', StatusKelulusan::DIAJUKAN->value)
            ->latest('id')
            ->take(5)
            ->get();

        return [
            'setorans'   => $latestSetorans,
            'kelulusans' => $pendingKelulusans,
        ];
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.dashboard-admin', [
            'masterData'        => $this->masterDataStatistics,
            'jenjang'           => $this->jenjangBreakdown,
            'usulanStats'       => $this->usulanStatistics,
            'activityStats'     => $this->activityStatistics,
            'latestActivities'  => $this->latestActivities,
            'activeTahunAjaran' => TahunAjaran::where('status', true)->first(),
        ]);
    }
}
