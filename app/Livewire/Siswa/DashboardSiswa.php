<?php

namespace App\Livewire\Siswa;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use App\Models\{Siswa, Absensi, Setoran, ProgresHafalan};
use App\Services\{CacheService, QueryOptimizationService};
use App\Enums\StatusKehadiran;
use Carbon\Carbon;
use Illuminate\Support\Facades\{DB, Cache};

/**
 * DashboardSiswa
 *
 * Portal dashboard utama untuk siswa dengan berbagai widget informatif
 *
 * Performance Optimizations:
 * - Cached data with automatic invalidation
 * - Selective column loading
 * - Single optimized query per widget
 * - Minimal relationship loading
 */
#[Layout('siswa.layouts.app')]
#[Title('Dashboard Siswa')]
class DashboardSiswa extends Component
{
    // ─── Properties ──────────────────────────────────────────────────────────

    private CacheService $cacheService;
    private QueryOptimizationService $queryOptimizer;

    // ─── Listeners ───────────────────────────────────────────────────────────

    protected $listeners = [
        'student-placed' => 'handlePlacement',
        'progress-updated' => 'handleProgressUpdate',
    ];

    // ─── Lifecycle ───────────────────────────────────────────────────────────

    public function boot(CacheService $cacheService, QueryOptimizationService $queryOptimizer): void
    {
        $this->cacheService = $cacheService;
        $this->queryOptimizer = $queryOptimizer;
    }

    // ─── Event Handlers ──────────────────────────────────────────────────────

    /**
     * Handle student placement event
     */
    public function handlePlacement(): void
    {
        $this->cacheService->invalidateStudent(auth('siswa')->id());
        $this->resetComputedProperties();
    }

    /**
     * Handle progress update event
     */
    public function handleProgressUpdate(): void
    {
        $this->cacheService->invalidateStudent(auth('siswa')->id());
        $this->resetComputedProperties();
    }

    /**
     * Reset all computed properties for fresh data
     */
    private function resetComputedProperties(): void
    {
        unset(
            $this->statistikKehadiran,
            $this->statistikSetoran,
            $this->progresHafalan,
            $this->kelompokInfo,
            $this->setoranTerbaru,
            $this->chartProgresMingguan
        );
    }

    // ─── Computed Properties ─────────────────────────────────────────────────

    /**
     * Get statistik kehadiran bulan ini
     *
     * Optimized with:
     * - Single DB query with aggregation
     * - Short-term caching (5 minutes)
     */
    #[Computed]
    public function statistikKehadiran(): array
    {
        $siswa = auth('siswa')->user();

        // Single optimized query with aggregation (Cumulative/Lifetime)
        $stats = DB::table('absensis')
            ->select([
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status_kehadiran = 'Hadir' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN status_kehadiran = 'Izin' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN status_kehadiran = 'Sakit' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN status_kehadiran = 'Alfa' THEN 1 ELSE 0 END) as alfa"),
            ])
            ->where('siswa_id', $siswa->id)
            ->first();

        $total = $stats->total ?? 0;
        $hadir = $stats->hadir ?? 0;

        return [
            'total' => $total,
            'hadir' => $hadir,
            'izin' => $stats->izin ?? 0,
            'sakit' => $stats->sakit ?? 0,
            'alfa' => $stats->alfa ?? 0,
            'persentase_hadir' => $total > 0 ? round(($hadir / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get statistik setoran bulan ini
     *
     * Optimized with:
     * - Single query with JOIN and aggregation
     * - Short-term caching
     */
    #[Computed]
    public function statistikSetoran(): array
    {
        $siswa = auth('siswa')->user();

        // Optimized query with aggregation (Cumulative/Lifetime)
        $stats = DB::table('setorans')
            ->join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
            ->select([
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN setorans.keputusan = 'Lanjut' THEN 1 ELSE 0 END) as lanjut"),
                DB::raw("SUM(CASE WHEN setorans.keputusan = 'Ulang' THEN 1 ELSE 0 END) as ulang"),
                DB::raw("AVG(CASE
                    WHEN setorans.predikat = 'Lancar' THEN 3
                    WHEN setorans.predikat = 'Kurang Lancar' THEN 2
                    WHEN setorans.predikat = 'Tidak Lancar' THEN 1
                    ELSE 0
                END) as rata_rata_nilai"),
            ])
            ->where('absensis.siswa_id', $siswa->id)
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'lanjut' => $stats->lanjut ?? 0,
            'ulang' => $stats->ulang ?? 0,
            'rata_rata_predikat' => $this->convertPredikatLabel($stats->rata_rata_nilai ?? 0),
        ];
    }

    /**
     * Get data progres hafalan
     *
     * Optimized with:
     * - Selective column loading
     * - Medium-term caching (30 minutes)
     */
    #[Computed]
    public function progresHafalan(): ?array
    {
        $siswa = auth('siswa')->user();

        $record = ProgresHafalan::where('siswa_id', $siswa->id)
            ->select(['id', 'siswa_id', 'total_juz_tuntas', 'target_juz', 'status_tahfidz'])
            ->first();

        if (!$record) {
            return null;
        }

        $config = \App\Models\ParameterKonfigurasi::getInstance();
        $targetKelulusan = $config ? $config->target_kelulusan : $record->target_juz;

        return [
            'id'               => $record->id,
            'siswa_id'         => $record->siswa_id,
            'total_juz_tuntas' => $record->total_juz_tuntas,
            'target_juz'       => $targetKelulusan,
            'status_tahfidz'   => $record->status_tahfidz,
        ];
    }

    /**
     * Get informasi kelompok saat ini
     *
     * Optimized with:
     * - Selective column loading
     * - Preload with count
     * - Medium-term caching
     */
    #[Computed]
    public function kelompokInfo(): ?array
    {
        $siswa = auth('siswa')->user();

        if (!$siswa->kelompok_id) {
            return null;
        }

        $kelompok = \App\Models\Kelompok::where('id', $siswa->kelompok_id)
            ->select(['id', 'nama', 'tingkat', 'guru_pembimbing_id', 'tahun_ajaran_id', 'kapasitas_maks'])
            ->with([
                'guruPembimbing:id,nama',
                'tahunAjaran:id,label,tanggal_mulai,tanggal_selesai'
            ])
            ->withCount(['siswas' => function ($query) {
                $query->where('status_akademik', 'Aktif')
                      ->where('status_penempatan', 'Ditempatkan');
            }])
            ->first();

        if (!$kelompok) {
            return null;
        }

        return [
            'nama' => $kelompok->nama,
            'tingkat' => $kelompok->tingkat instanceof \BackedEnum ? $kelompok->tingkat->value : (string)($kelompok->tingkat ?? ''),
            'guru_pembimbing' => $kelompok->guruPembimbing?->nama ?? 'Belum ada',
            'jumlah_siswa' => $kelompok->siswas_count,
            'kapasitas_maks' => $kelompok->kapasitas_maks,
        ];
    }

    /**
     * Get riwayat setoran terbaru (5 terakhir)
     *
     * Optimized with:
     * - Selective column loading
     * - Optimized eager loading preset
     * - Short-term caching
     */
    #[Computed]
    public function setoranTerbaru(): array
    {
        $siswa = auth('siswa')->user();
        if (!$siswa) {
            return [];
        }

        $results = Setoran::query()
            ->whereHas('absensi', function ($q) use ($siswa) {
                $q->where('siswa_id', $siswa->id);
            })
            ->with([
                'absensi:id,siswa_id,tanggal',
                'surah:id,nama_latin,nomor_surah',
                'juz:id,juz_ke',
            ])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return $results->toArray();
    }

    /**
     * Get data chart progres hafalan (7 hari terakhir)
     *
     * Optimized with:
     * - Single query with grouping
     * - Date range index utilization
     * - Short-term caching
     */
    #[Computed]
    public function chartProgresMingguan(): array
    {
        $siswa = auth('siswa')->user();
        $sevenDaysAgo = now()->subDays(6)->startOfDay();

        // Optimized single query with grouping
        $results = DB::table('setorans')
            ->join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
            ->select([
                DB::raw('DATE(absensis.tanggal) as date'),
                DB::raw('COUNT(*) as count')
            ])
            ->where('absensis.siswa_id', $siswa->id)
            ->whereBetween('absensis.tanggal', [$sevenDaysAgo, now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        // Prepare 7 days array
        $days = [];
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $days[] = $results->get($dateStr, 0);
            $labels[] = $date->isoFormat('ddd');
        }

        return [
            'labels' => $labels,
            'data' => $days,
        ];
    }

    // ─── Helper Methods ──────────────────────────────────────────────────────

    /**
     * Convert average predikat value to label
     *
     * @param float $rataRata Average value
     * @return string Label
     */
    private function convertPredikatLabel(float $rataRata): string
    {
        if ($rataRata == 0) return '-';
        if ($rataRata >= 2.5) return 'Lancar';
        if ($rataRata >= 1.5) return 'Kurang Lancar';
        return 'Tidak Lancar';
    }

    // ─── Render ──────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        return view('siswa.dashboard-siswa');
    }
}
