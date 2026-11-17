<?php

namespace App\Services;

use App\Models\CapaianJuz;
use App\Models\Juz;
use App\Models\ProgresHafalan;
use App\Models\Setoran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

/**
 * ProgresHafalanService
 *
 * Core service untuk perhitungan progres hafalan siswa.
 * Menghitung juz tuntas berdasarkan 100% coverage ayat dari
 * baseline + setoran "Hafalan Baru" dengan keputusan "Lanjut".
 */
class ProgresHafalanService
{
    /**
     * @var CapaianJuzService
     */
    protected CapaianJuzService $capaianJuzService;

    public function __construct(CapaianJuzService $capaianJuzService)
    {
        $this->capaianJuzService = $capaianJuzService;
    }

    /**
     * Calculate current progress for a student
     *
     * @param int $siswaId
     * @return array [
     *   'total_juz_tuntas' => int,
     *   'juz_berjalan' => int|null,
     *   'juz_berjalan_percentage' => float|null,
     *   'progress_toward_target' => float,
     *   'capaian_juz' => Collection of CapaianJuz
     * ]
     */
    public function calculateProgres(int $siswaId): array
    {
        // Validate siswa
        $siswa = Siswa::findOrFail($siswaId);

        // Get or create ProgresHafalan (lifetime record)
        $progresHafalan = ProgresHafalan::firstOrCreate(
            [
                'siswa_id' => $siswaId,
            ],
            [
                'total_juz_tuntas' => 0,
                'progress_toward_target' => 0,
                'target_juz' => config('tahfidz.target_juz_minimal', 5),
                'status_tahfidz' => 'Belum Lulus',
            ]
        );

        // Get all juz
        $allJuzs = Juz::orderBy('juz_ke')->get();
        $capaianJuzCollection = collect();

        $totalJuzTuntas = 0;
        $juzBerjalan = null;
        $juzBerjalanPercentage = null;

        foreach ($allJuzs as $juz) {
            // Get covered ayat ranges using CapaianJuzService
            $ranges = $this->capaianJuzService->getCoveredAyatRanges($siswaId, $juz->id);
            $mergedRanges = $this->capaianJuzService->unionAyatRanges($ranges);
            $ayatTercakup = $this->capaianJuzService->countAyatFromRanges($mergedRanges);

            // Calculate percentage
            $persentase = ($juz->jumlah_ayat > 0)
                ? round(($ayatTercakup / $juz->jumlah_ayat) * 100, 2)
                : 0;

            // Check if tuntas
            $statusTuntas = $ayatTercakup >= $juz->jumlah_ayat;

            if ($statusTuntas) {
                $totalJuzTuntas++;
            }

            // Identify juz berjalan (first incomplete with coverage > 0)
            if (!$statusTuntas && $ayatTercakup > 0 && $juzBerjalan === null) {
                $juzBerjalan = $juz->juz_ke;
                $juzBerjalanPercentage = $persentase;
            }

            // Determine exact completion date if tuntas
            $tanggalTuntas = null;
            if ($statusTuntas) {
                $tanggalTuntas = $this->capaianJuzService->calculateExactTuntasDate($juz, $ranges) ?? now()->toDateString();
            }

            // Create or update CapaianJuz
            $capaianJuz = CapaianJuz::updateOrCreate(
                [
                    'progres_hafalan_id' => $progresHafalan->id,
                    'juz_id' => $juz->id,
                ],
                [
                    'ayat_tercakup' => $ayatTercakup,
                    'total_ayat' => $juz->jumlah_ayat,
                    'persentase' => $persentase,
                    'status_tuntas' => $statusTuntas,
                    'tanggal_tuntas' => $tanggalTuntas,
                    'detail_ayat_tercakup' => $mergedRanges->toArray(),
                ]
            );

            $capaianJuzCollection->push($capaianJuz);
        }

        // Calculate progress toward target (from config)
        $targetKelulusan = $progresHafalan->target_juz ?? config('tahfidz.target_juz_minimal', 5);
        $progressTowardTarget = ($targetKelulusan > 0)
            ? round(($totalJuzTuntas / $targetKelulusan) * 100, 2)
            : 0;

        // Update ProgresHafalan aggregated fields
        $progresHafalan->update([
            'total_juz_tuntas' => $totalJuzTuntas,
            'juz_berjalan' => $juzBerjalan,
            'juz_berjalan_percentage' => $juzBerjalanPercentage,
            'progress_toward_target' => min($progressTowardTarget, 100),
        ]);

        // Auto-check and update status tahfidz
        $progresHafalan->checkAndUpdateStatusTahfidz();

        return [
            'total_juz_tuntas' => $totalJuzTuntas,
            'juz_berjalan' => $juzBerjalan,
            'juz_berjalan_percentage' => $juzBerjalanPercentage,
            'progress_toward_target' => min($progressTowardTarget, 100),
            'capaian_juz' => $capaianJuzCollection,
        ];
    }

    /**
     * Recalculate progress after new setoran added
     *
     * @param int $setoranId
     * @return void
     */
    public function recalculateAfterSetoran(int $setoranId): void
    {
        // Get setoran with absensi relation
        $setoran = Setoran::with('absensi.kelompok')->findOrFail($setoranId);

        if (!$setoran->absensi) {
            return; // Skip if no absensi
        }

        $siswaId = $setoran->absensi->siswa_id;

        // Recalculate progress (lifetime, no tahun ajaran needed)
        $this->calculateProgres($siswaId);
    }

    /**
     * Get dashboard data for Guru (filtered by kelompok binaan)
     *
     * @param int $guruId
     * @param array $filters [kelompok_id?, tahun_ajaran_id?, sort_by?, sort_direction?, per_page?]
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getDashboardDataForGuru(int $guruId, array $filters = []): LengthAwarePaginator
    {
        // Get kelompok binaan by guru (Cached for 1 hour)
        $kelompokIds = \Illuminate\Support\Facades\Cache::remember("progres:kelompok:guru:{$guruId}", 3600, function () use ($guruId) {
            return DB::table('kelompoks')
                ->where('guru_pembimbing_id', $guruId)
                ->pluck('id');
        });

        if ($kelompokIds->isEmpty()) {
            // Guru tidak punya kelompok binaan, return empty paginator
            return new LengthAwarePaginator(
                [],
                0,
                $filters['per_page'] ?? 15,
                1
            );
        }

        // Build query
        $query = ProgresHafalan::query()
            ->with(['siswa.kelompok', 'capaianJuzs'])
            ->whereHas('siswa', function ($q) use ($kelompokIds) {
                $q->whereIn('kelompok_id', $kelompokIds);
            });

        // Apply kelompok filter if specified
        if (!empty($filters['kelompok_id'])) {
            $query->whereHas('siswa', function ($q) use ($filters) {
                $q->where('kelompok_id', $filters['kelompok_id']);
            });
        }

        // Filter by tahun ajaran (via kelompok, not progres_hafalans)
        $tahunAjaranId = $filters['tahun_ajaran_id'] ?? TahunAjaran::where('status', true)->value('id');
        if ($tahunAjaranId) {
            // Get siswa IDs from kelompok with this tahun_ajaran_id
            $siswaIdsInTahunAjaran = Siswa::whereHas('kelompok', function($q) use ($tahunAjaranId) {
                $q->where('tahun_ajaran_id', $tahunAjaranId);
            })->pluck('id');

            $query->whereIn('siswa_id', $siswaIdsInTahunAjaran);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortDirection = $filters['sort_direction'] ?? 'asc';

        switch ($sortBy) {
            case 'name':
                $query->join('siswas', 'progres_hafalans.siswa_id', '=', 'siswas.id')
                    ->orderBy('siswas.nama', $sortDirection)
                    ->select('progres_hafalans.*');
                break;
            case 'progress_percentage':
                $query->orderBy('progress_toward_target', $sortDirection);
                break;
            case 'juz_count':
                $query->orderBy('total_juz_tuntas', $sortDirection);
                break;
            default:
                $query->orderBy('total_juz_tuntas', 'desc');
        }

        // Paginate
        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Get dashboard data for Admin (global, with advanced filtering)
     *
     * @param array $filters [kelompok_id?, tingkat?, tahun_ajaran_id?, jenis_kelamin?, sort_by?, sort_direction?, per_page?]
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getDashboardDataForAdmin(array $filters = []): LengthAwarePaginator
    {
        // Build query for ALL students (no kelompok restriction)
        $query = ProgresHafalan::query()
            ->with(['siswa.kelompok', 'capaianJuzs'])
            ->join('siswas', 'progres_hafalans.siswa_id', '=', 'siswas.id')
            ->join('kelompoks', 'siswas.kelompok_id', '=', 'kelompoks.id');

        // Filter by tahun ajaran (via kelompok, not progres_hafalans)
        $tahunAjaranId = $filters['tahun_ajaran_id'] ?? TahunAjaran::where('status', true)->value('id');
        if ($tahunAjaranId) {
            $query->where('kelompoks.tahun_ajaran_id', $tahunAjaranId);
        }

        // Apply filters (AND logic for multi-filter combinations)
        if (!empty($filters['kelompok_id'])) {
            $query->where('siswas.kelompok_id', $filters['kelompok_id']);
        }

        if (!empty($filters['tingkat'])) {
            $query->where('kelompoks.tingkat', $filters['tingkat']);
        }

        if (!empty($filters['jenis_kelamin'])) {
            $query->where('siswas.jenis_kelamin', $filters['jenis_kelamin']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortDirection = $filters['sort_direction'] ?? 'asc';

        switch ($sortBy) {
            case 'name':
                $query->orderBy('siswas.nama', $sortDirection);
                break;
            case 'progress_percentage':
                $query->orderBy('progres_hafalans.progress_toward_target', $sortDirection);
                break;
            case 'juz_count':
                $query->orderBy('progres_hafalans.total_juz_tuntas', $sortDirection);
                break;
            default:
                $query->orderBy('progres_hafalans.total_juz_tuntas', 'desc');
        }

        // Select only progres_hafalans columns to avoid ambiguity
        $query->select('progres_hafalans.*');

        // Paginate with max 50 per page for Admin dashboard
        $perPage = min($filters['per_page'] ?? 50, 50);
        return $query->paginate($perPage);
    }

    /**
     * Get aggregate statistics for Admin dashboard
     *
     * Calculates:
     * - Average juz_tuntas per tingkat
     * - Distribution of students by progress range (0-25%, 26-50%, 51-75%, 76-100%)
     * - Completion rate trend (last 6 months)
     *
     * Results are cached with 5-minute TTL for performance
     *
     * @param array $filters [kelompok_id?, tingkat?, tahun_ajaran_id?, jenis_kelamin?]
     * @return array [
     *   'avg_juz_per_tingkat' => array,
     *   'distribution_by_progress' => array,
     *   'completion_trend' => array
     * ]
     */
    public function getAggregateStats(array $filters = []): array
    {
        // Create cache key based on filters
        $cacheKey = 'aggregate_stats_' . md5(json_encode($filters));

        // Cache for 5 minutes (300 seconds)
        return Cache::remember($cacheKey, 300, function () use ($filters) {
            // Get current tahun ajaran (or use filter)
            $tahunAjaranId = $filters['tahun_ajaran_id'] ?? TahunAjaran::where('status', true)->value('id');

            // Build base query with filters
            $baseQuery = ProgresHafalan::query()
                ->join('siswas', 'progres_hafalans.siswa_id', '=', 'siswas.id')
                ->join('kelompoks', 'siswas.kelompok_id', '=', 'kelompoks.id')
                ->where('siswas.status_akademik', 'Aktif')
                ->where('siswas.status_penempatan', 'Ditempatkan');

            // Filter by tahun ajaran via kelompok
            if ($tahunAjaranId) {
                $baseQuery->where('kelompoks.tahun_ajaran_id', $tahunAjaranId);
            }

            // Apply filters
            if (!empty($filters['kelompok_id'])) {
                $baseQuery->where('siswas.kelompok_id', $filters['kelompok_id']);
            }

            if (!empty($filters['tingkat'])) {
                $baseQuery->where('kelompoks.tingkat', $filters['tingkat']);
            }

            if (!empty($filters['jenis_kelamin'])) {
                $baseQuery->where('siswas.jenis_kelamin', $filters['jenis_kelamin']);
            }

            // 1. Calculate average juz_tuntas per tingkat
            $avgJuzPerTingkat = (clone $baseQuery)
                ->select('kelompoks.tingkat', DB::raw('AVG(progres_hafalans.total_juz_tuntas) as avg_juz'))
                ->groupBy('kelompoks.tingkat')
                ->orderBy('kelompoks.tingkat')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->tingkat => round($item->avg_juz, 2)];
                })
                ->toArray();

            // 2. Calculate distribution of students by progress range
            $distribution = (clone $baseQuery)
                ->select(
                    DB::raw('
                        CASE
                            WHEN progres_hafalans.progress_toward_target >= 0 AND progres_hafalans.progress_toward_target <= 25 THEN "0-25%"
                            WHEN progres_hafalans.progress_toward_target > 25 AND progres_hafalans.progress_toward_target <= 50 THEN "26-50%"
                            WHEN progres_hafalans.progress_toward_target > 50 AND progres_hafalans.progress_toward_target <= 75 THEN "51-75%"
                            WHEN progres_hafalans.progress_toward_target > 75 AND progres_hafalans.progress_toward_target <= 100 THEN "76-100%"
                            ELSE "Unknown"
                        END as progress_range
                    '),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('progress_range')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->progress_range => $item->count];
                })
                ->toArray();

            // Ensure all ranges are present (even if count is 0)
            $distributionTemplate = [
                '0-25%' => 0,
                '26-50%' => 0,
                '51-75%' => 0,
                '76-100%' => 0,
            ];
            $distribution = array_merge($distributionTemplate, $distribution);
            unset($distribution['Unknown']); // Remove unknown if present

            // 3. Calculate completion rate trend (last 6 months)
            // This shows how many juz were completed each month for the last 6 months
            $completionTrend = [];
            $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();

            // Get all CapaianJuz records for the filtered students in the last 6 months
            $filteredSiswaIds = (clone $baseQuery)
                ->pluck('progres_hafalans.siswa_id')
                ->unique()
                ->toArray();

            if (!empty($filteredSiswaIds)) {
                $capaianJuzRecords = CapaianJuz::join('progres_hafalans', 'capaian_juzs.progres_hafalan_id', '=', 'progres_hafalans.id')
                    ->whereIn('progres_hafalans.siswa_id', $filteredSiswaIds)
                    ->where('capaian_juzs.status_tuntas', true)
                    ->whereNotNull('capaian_juzs.tanggal_tuntas')
                    ->where('capaian_juzs.tanggal_tuntas', '>=', $sixMonthsAgo)
                    ->select('capaian_juzs.tanggal_tuntas')
                    ->get();

                // Group by month in PHP (database-agnostic)
                $trendData = $capaianJuzRecords
                    ->groupBy(function ($item) {
                        return Carbon::parse($item->tanggal_tuntas)->format('Y-m');
                    })
                    ->map(function ($items) {
                        return $items->count();
                    })
                    ->toArray();

                // Generate all 6 months (even if no completions)
                for ($i = 0; $i < 6; $i++) {
                    $month = Carbon::now()->subMonths(5 - $i)->format('Y-m');
                    $completionTrend[$month] = $trendData[$month] ?? 0;
                }
            } else {
                // No students, return empty trend
                for ($i = 0; $i < 6; $i++) {
                    $month = Carbon::now()->subMonths(5 - $i)->format('Y-m');
                    $completionTrend[$month] = 0;
                }
            }

            return [
                'avg_juz_per_tingkat' => $avgJuzPerTingkat,
                'distribution_by_progress' => $distribution,
                'completion_trend' => $completionTrend,
            ];
        });
    }

    /**
     * Get students approaching target kelulusan (within 1 juz of target)
     *
     * Identifies students who are close to completing their target kelulusan.
     * A student is considered "approaching target" when:
     * (target_kelulusan - total_juz_tuntas) <= 1
     *
     * @param array $filters [kelompok_id?, tingkat?, tahun_ajaran_id?, jenis_kelamin?]
     * @return Collection [
     *   'siswa_id' => int,
     *   'nama' => string,
     *   'kelompok' => string,
     *   'total_juz_tuntas' => int,
     *   'juz_remaining' => int,
     *   'progress_toward_target' => float
     * ]
     */
    public function getStudentsApproachingTarget(array $filters = []): Collection
    {
        // Get target kelulusan from configuration (default 5 juz for MVP)
        $targetKelulusan = config('tahfidz.target_juz_minimal', 5);

        // Get current tahun ajaran (or use filter)
        $tahunAjaranId = $filters['tahun_ajaran_id'] ?? TahunAjaran::where('status', true)->value('id');

        // Build query
        $query = ProgresHafalan::query()
            ->join('siswas', 'progres_hafalans.siswa_id', '=', 'siswas.id')
            ->join('kelompoks', 'siswas.kelompok_id', '=', 'kelompoks.id')
            ->where('siswas.status_akademik', 'Aktif')
            ->where('siswas.status_penempatan', 'Ditempatkan')
            // Filter: (target_kelulusan - total_juz_tuntas) <= 1 AND total_juz_tuntas < target_kelulusan
            ->whereRaw('? - progres_hafalans.total_juz_tuntas <= 1', [$targetKelulusan])
            ->where('progres_hafalans.total_juz_tuntas', '<', $targetKelulusan);

        // Filter by tahun ajaran via kelompok
        if ($tahunAjaranId) {
            $query->where('kelompoks.tahun_ajaran_id', $tahunAjaranId);
        }

        // Apply additional filters
        if (!empty($filters['kelompok_id'])) {
            $query->where('siswas.kelompok_id', $filters['kelompok_id']);
        }

        if (!empty($filters['tingkat'])) {
            $query->where('kelompoks.tingkat', $filters['tingkat']);
        }

        if (!empty($filters['jenis_kelamin'])) {
            $query->where('siswas.jenis_kelamin', $filters['jenis_kelamin']);
        }

        // Select and calculate remaining juz
        $results = $query->select(
            'siswas.id as siswa_id',
            'siswas.nama',
            'kelompoks.nama as kelompok',
            'progres_hafalans.total_juz_tuntas',
            'progres_hafalans.progress_toward_target',
            DB::raw("($targetKelulusan - progres_hafalans.total_juz_tuntas) as juz_remaining")
        )
            ->orderBy('juz_remaining', 'asc')
            ->orderBy('siswas.nama', 'asc')
            ->get();

        return $results;
    }

    /**
     * Get students below expected progress threshold
     *
     * Identifies students whose progress is significantly below expected level.
     * A student is flagged when: (total_juz_tuntas / target_kelulusan) * 100 < threshold
     *
     * @param float $threshold Percentage threshold (e.g., 40 means below 40% of target)
     * @param array $filters [kelompok_id?, tingkat?, tahun_ajaran_id?, jenis_kelamin?]
     * @return Collection [
     *   'siswa_id' => int,
     *   'nama' => string,
     *   'kelompok' => string,
     *   'tingkat' => string,
     *   'total_juz_tuntas' => int,
     *   'progress_toward_target' => float
     * ]
     */
    public function getStudentsBelowProgress(float $threshold, array $filters = []): Collection
    {
        // Validate threshold (must be between 0 and 100)
        if ($threshold < 0 || $threshold > 100) {
            throw new \InvalidArgumentException("Threshold must be between 0 and 100");
        }

        // Get current tahun ajaran (or use filter)
        $tahunAjaranId = $filters['tahun_ajaran_id'] ?? TahunAjaran::where('status', true)->value('id');

        // Build query
        $query = ProgresHafalan::query()
            ->join('siswas', 'progres_hafalans.siswa_id', '=', 'siswas.id')
            ->join('kelompoks', 'siswas.kelompok_id', '=', 'kelompoks.id')
            ->where('siswas.status_akademik', 'Aktif')
            ->where('siswas.status_penempatan', 'Ditempatkan')
            // Filter: progress_toward_target < threshold
            ->where('progres_hafalans.progress_toward_target', '<', $threshold);

        // Filter by tahun ajaran via kelompok
        if ($tahunAjaranId) {
            $query->where('kelompoks.tahun_ajaran_id', $tahunAjaranId);
        }

        // Apply additional filters
        if (!empty($filters['kelompok_id'])) {
            $query->where('siswas.kelompok_id', $filters['kelompok_id']);
        }

        if (!empty($filters['tingkat'])) {
            $query->where('kelompoks.tingkat', $filters['tingkat']);
        }

        if (!empty($filters['jenis_kelamin'])) {
            $query->where('siswas.jenis_kelamin', $filters['jenis_kelamin']);
        }

        // Select fields
        $results = $query->select(
            'siswas.id as siswa_id',
            'siswas.nama',
            'kelompoks.nama as kelompok',
            'kelompoks.tingkat',
            'progres_hafalans.total_juz_tuntas',
            'progres_hafalans.progress_toward_target'
        )
            ->orderBy('progres_hafalans.progress_toward_target', 'asc')
            ->orderBy('siswas.nama', 'asc')
            ->get();

        return $results;
    }

    /**
     * Get dashboard data for Siswa/Orang Tua
     *
     * Displays authenticated student's progress including:
     * - Total juz tuntas count
     * - Juz berjalan with percentage
     * - Progress toward target kelulusan
     * - Detailed capaian juz (30 segments for visual indicator)
     * - Recent setoran history (last 10 entries)
     *
     * @param int $siswaId
     * @return array [
     *   'siswa' => Siswa,
     *   'progres' => [
     *     'total_juz_tuntas' => int,
     *     'juz_berjalan' => int|null,
     *     'juz_berjalan_percentage' => float|null,
     *     'progress_toward_target' => float,
     *     'target_kelulusan' => int
     *   ],
     *   'capaian_juzs' => Collection (30 juz with status for visual indicator),
     *   'recent_setoran' => Collection (last 10 entries with details)
     * ]
     */
    public function getDashboardDataForSiswa(int $siswaId): array
    {
        // Validate siswa exists
        $siswa = Siswa::with('kelompok')->findOrFail($siswaId);

        $cacheKey = "progres:siswa:{$siswaId}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 1800, function () use ($siswa, $siswaId) {
            // Get or calculate current progress (lifetime)
            $progressData = $this->calculateProgres($siswaId);

            // Get target kelulusan from DB config (source of truth)
            $config          = \App\Models\ParameterKonfigurasi::getInstance();
            $targetKelulusan = $config->target_kelulusan ?? 3;

            // Get flat list of target juz numbers from batas_tingkat (e.g. [28, 29, 30])
            $batasTingkat = $config->batas_tingkat ?? [];
            $dasarList    = is_array($batasTingkat['Dasar'] ?? null) ? $batasTingkat['Dasar'] : [];
            $menengahList = is_array($batasTingkat['Menengah'] ?? null) ? $batasTingkat['Menengah'] : [];
            $atasList     = is_array($batasTingkat['Atas'] ?? null) ? $batasTingkat['Atas'] : [];
            $targetJuzs   = array_values(array_unique(array_merge($dasarList, $menengahList, $atasList)));
            sort($targetJuzs);

            // Get progres hafalan record with eager loaded capaian juzs
            $progresHafalan = ProgresHafalan::with(['capaianJuzs.juz' => function ($query) {
                $query->orderBy('juz_ke');
            }])
                ->where('siswa_id', $siswaId)
                ->first();

            // Get capaian juzs (30 segments for visual indicator)
            // Convert to plain array to avoid serialization issues with cache
            $capaianJuzsRaw = $progresHafalan ? $progresHafalan->capaianJuzs : collect();
            $capaianJuzs = $capaianJuzsRaw->map(function ($item) {
                $juzKe = $item->juz ? $item->juz->juz_ke : null;
                if (!$juzKe) return null;

                if ($item->status_tuntas) {
                    $status = 'tuntas';
                } elseif ($item->persentase > 0) {
                    $status = 'berjalan';
                } else {
                    $status = 'belum';
                }

                return [
                    'juz'    => (int) $juzKe,
                    'status' => $status,
                    'persen' => (float) $item->persentase,
                ];
            })->filter()->values()->toArray();

            // Get recent setoran history (last 10 entries)
            $recentSetoran = [];
            if ($siswa->kelompok_id) {
                $recentSetoran = Setoran::join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
                    ->where('absensis.siswa_id', $siswaId)
                    ->where('absensis.kelompok_id', $siswa->kelompok_id)
                    ->with(['juz', 'surah'])
                    ->select('setorans.*', 'absensis.tanggal')
                    ->orderBy('absensis.tanggal', 'desc')
                    ->orderBy('setorans.created_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function ($setoran) {
                        return [
                            'id'           => $setoran->id,
                            'tanggal'      => $setoran->tanggal ? Carbon::parse($setoran->tanggal)->format('Y-m-d') : null,
                            'jenis_setoran'=> $setoran->jenis_setoran instanceof \BackedEnum ? $setoran->jenis_setoran->value : $setoran->jenis_setoran,
                            'juz'          => $setoran->juz ? $setoran->juz->juz_ke : null,
                            'surah'        => $setoran->surah ? $setoran->surah->nama_latin : null,
                            'ayat_range'   => "Ayat {$setoran->ayat_mulai}-{$setoran->ayat_selesai}",
                            'predikat'     => $setoran->predikat instanceof \BackedEnum ? $setoran->predikat->value : $setoran->predikat,
                            'keputusan'    => $setoran->keputusan instanceof \BackedEnum ? $setoran->keputusan->value : $setoran->keputusan,
                        ];
                    })->toArray();
            }

            return [
                'siswa_id'       => $siswa->id,
                'progres'        => [
                    'total_juz_tuntas'        => $progressData['total_juz_tuntas'],
                    'juz_berjalan'            => $progressData['juz_berjalan'],
                    'juz_berjalan_percentage' => $progressData['juz_berjalan_percentage'],
                    'progress_toward_target'  => $progressData['progress_toward_target'],
                    'target_kelulusan'        => $targetKelulusan,
                ],
                'target_juzs'    => $targetJuzs,      // e.g. [28, 29, 30]
                'capaian_juzs'   => $capaianJuzs,
                'recent_setoran' => $recentSetoran,
            ];
        });
    }

    /**
     * Get progress timeline (juz completion dates over time)
     *
     * Displays chronological list of completed juz for a student, showing
     * when each juz was marked as tuntas. Useful for viewing learning velocity
     * and identifying completion patterns.
     *
     * Supports filtering by:
     * - date_range: ['start' => date, 'end' => date]
     * - juz_range: ['min' => int, 'max' => int]
     *
     * @param int $siswaId
     * @param int $tahunAjaranId
     * @param array $filters [date_range?, juz_range?]
     * @return Collection [
     *   'juz_id' => int,
     *   'juz_ke' => int,
     *   'tanggal_tuntas' => date
     * ]
     */
    public function getProgressTimeline(int $siswaId, array $filters = []): Collection
    {
        // Get progres hafalan for this student (lifetime)
        $progresHafalan = ProgresHafalan::where('siswa_id', $siswaId)
            ->first();

        if (!$progresHafalan) {
            return collect();
        }

        // Query capaian_juzs with status_tuntas = true
        $query = CapaianJuz::query()
            ->where('progres_hafalan_id', $progresHafalan->id)
            ->where('status_tuntas', true)
            ->whereNotNull('tanggal_tuntas');

        // Apply date_range filter if provided
        if (!empty($filters['date_range'])) {
            if (!empty($filters['date_range']['start'])) {
                $query->where('tanggal_tuntas', '>=', $filters['date_range']['start']);
            }
            if (!empty($filters['date_range']['end'])) {
                $query->where('tanggal_tuntas', '<=', $filters['date_range']['end']);
            }
        }

        // Apply juz_range filter if provided
        if (!empty($filters['juz_range'])) {
            $query->whereHas('juz', function ($q) use ($filters) {
                if (!empty($filters['juz_range']['min'])) {
                    $q->where('juz_ke', '>=', $filters['juz_range']['min']);
                }
                if (!empty($filters['juz_range']['max'])) {
                    $q->where('juz_ke', '<=', $filters['juz_range']['max']);
                }
            });
        }

        // Eager load juz relation and order by tanggal_tuntas ASC
        $results = $query->with('juz')
            ->orderBy('tanggal_tuntas', 'asc')
            ->get()
            ->map(function ($capaian) {
                return [
                    'juz_id' => $capaian->juz_id,
                    'juz_ke' => $capaian->juz ? $capaian->juz->juz_ke : null,
                    'tanggal_tuntas' => $capaian->tanggal_tuntas->format('Y-m-d'),
                ];
            });

        return $results;
    }

    /**
     * Calculate average ayat per week for learning velocity tracking
     *
     * Calculates the rate of memorization by dividing total ayat covered
     * by qualifying setoran (Hafalan Baru + Lanjut) by the number of weeks
     * in the specified period.
     *
     * Supported periods:
     * - 'last_month': Last 30 days
     * - 'last_semester': Last 6 months
     * - 'all_time': All qualifying setoran for the student
     *
     * @param int $siswaId
     * @param string $period 'last_month'|'last_semester'|'all_time'
     * @return float Average ayat per week (rounded to 2 decimal places)
     */
    public function getAverageAyatPerWeek(int $siswaId, string $period): float
    {
        // Validate period parameter
        $validPeriods = ['last_month', 'last_semester', 'all_time'];
        if (!in_array($period, $validPeriods)) {
            throw new \InvalidArgumentException("Invalid period. Must be one of: " . implode(', ', $validPeriods));
        }

        // Calculate start date based on period
        $startDate = null;
        $weeksInPeriod = 0;

        switch ($period) {
            case 'last_month':
                $startDate = Carbon::now()->subDays(30);
                $weeksInPeriod = 4; // Approximately 4 weeks in a month
                break;
            case 'last_semester':
                $startDate = Carbon::now()->subMonths(6);
                $weeksInPeriod = 26; // Approximately 26 weeks in 6 months
                break;
            case 'all_time':
                // No start date filter for all_time
                // Calculate weeks from first setoran to now
                $firstSetoran = Setoran::join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
                    ->where('absensis.siswa_id', $siswaId)
                    ->where('setorans.jenis_setoran', 'Hafalan Baru')
                    ->where('setorans.keputusan', 'Lanjut')
                    ->orderBy('absensis.tanggal', 'asc')
                    ->select('absensis.tanggal')
                    ->first();

                if (!$firstSetoran) {
                    // No qualifying setoran found, return 0
                    return 0.0;
                }

                $startDate = Carbon::parse($firstSetoran->tanggal);
                $daysSinceFirst = $startDate->diffInDays(Carbon::now());
                $weeksInPeriod = max(1, ceil($daysSinceFirst / 7)); // Minimum 1 week to avoid division by zero
                break;
        }

        // Build query for qualifying setoran in the period
        $query = Setoran::join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
            ->where('absensis.siswa_id', $siswaId)
            ->where('setorans.jenis_setoran', 'Hafalan Baru')
            ->where('setorans.keputusan', 'Lanjut');

        // Apply date filter if start date is set
        if ($startDate) {
            $query->where('absensis.tanggal', '>=', $startDate);
        }

        // Get all qualifying setoran in the period
        $setoranRecords = $query->select(
            'setorans.ayat_mulai',
            'setorans.ayat_selesai'
        )->get();

        // Calculate total ayat covered
        $totalAyat = 0;
        foreach ($setoranRecords as $setoran) {
            $ayatCount = ($setoran->ayat_selesai - $setoran->ayat_mulai + 1);
            $totalAyat += $ayatCount;
        }

        // Calculate average ayat per week
        if ($weeksInPeriod > 0) {
            $averageAyatPerWeek = $totalAyat / $weeksInPeriod;
            return round($averageAyatPerWeek, 2);
        }

        return 0.0;
    }
}
