<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\TranskripCapaian;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * TranskripService
 *
 * Service untuk snapshot transkrip capaian hafalan siswa.
 * Menyimpan snapshot untuk history non-retroaktif saat parameter sistem berubah.
 */
class TranskripService
{
    /**
     * ProgresHafalanService instance untuk menghitung progres
     *
     * @var ProgresHafalanService
     */
    protected ProgresHafalanService $progresService;

    /**
     * Constructor
     *
     * @param ProgresHafalanService $progresService
     */
    public function __construct(ProgresHafalanService $progresService)
    {
        $this->progresService = $progresService;
    }

    /**
     * Create snapshot transcript for students before parameter change
     *
     * REFACTOR 2026-08-11: Progres hafalan sekarang lifetime
     * Filter siswa by tahun_ajaran via kelompok, tapi transkrip disimpan tanpa tahun_ajaran_id
     *
     * @param string $reason 'target_kelulusan_change'|'batas_tingkat_change'|'manual_admin'
     * @param array $affectedSiswaIds If empty, snapshot all active students
     * @param int|null $tahunAjaranId Optional: untuk filter siswa by kelompok tahun ajaran
     * @return int Number of snapshots created
     */
    public function createSnapshot(string $reason, array $affectedSiswaIds = [], ?int $tahunAjaranId = null): int
    {
        // Validate reason
        if (!in_array($reason, ['target_kelulusan_change', 'batas_tingkat_change', 'manual_admin', 'lulus_tahfidz', 'lulus_akademik'])) {
            throw new \InvalidArgumentException("Invalid reason: {$reason}");
        }

        // Get current system parameters
        $targetKelulusan = $this->getCurrentTargetKelulusan();
        $batasTingkat = $this->getCurrentBatasTingkat();

        // Determine which students to snapshot
        $siswaQuery = Siswa::query()
            ->where('status_akademik', 'Aktif')
            ->where('status_penempatan', 'Ditempatkan');

        // Optional: filter by tahun_ajaran_id via kelompok
        if ($tahunAjaranId) {
            $siswaQuery->whereHas('kelompok', function($q) use ($tahunAjaranId) {
                $q->where('tahun_ajaran_id', $tahunAjaranId);
            });
        }

        // Filter by affected siswa IDs if provided
        if (!empty($affectedSiswaIds)) {
            $siswaQuery->whereIn('id', $affectedSiswaIds);
        }

        $siswaIds = $siswaQuery->pluck('id');

        if ($siswaIds->isEmpty()) {
            // No students to snapshot
            return 0;
        }

        $snapshotCount = 0;

        // Wrap in transaction
        DB::beginTransaction();

        try {
            foreach ($siswaIds as $siswaId) {
                // Calculate current progress (tanpa tahunAjaranId)
                $progressData = $this->progresService->calculateProgres($siswaId);

                // Create snapshot record (tanpa tahun_ajaran_id)
                TranskripCapaian::create([
                    'siswa_id' => $siswaId,
                    'tanggal_generate' => now()->toDateString(),
                    'reason' => $reason,
                    'generated_by' => null, // Automated snapshot (no admin)
                    'total_juz_tuntas_snapshot' => $progressData['total_juz_tuntas'],
                    'detail_capaian_snapshot' => [
                        'total_juz_tuntas' => $progressData['total_juz_tuntas'],
                        'juz_berjalan' => $progressData['juz_berjalan'],
                        'juz_berjalan_percentage' => $progressData['juz_berjalan_percentage'],
                        'progress_toward_target' => $progressData['progress_toward_target'],
                        'capaian_juz' => $progressData['capaian_juz']->map(function ($capaian) {
                            return [
                                'juz_id' => $capaian->juz_id,
                                'ayat_tercakup' => $capaian->ayat_tercakup,
                                'total_ayat' => $capaian->total_ayat,
                                'persentase' => $capaian->persentase,
                                'status_tuntas' => $capaian->status_tuntas,
                                'tanggal_tuntas' => $capaian->tanggal_tuntas,
                                'detail_ayat_tercakup' => $capaian->detail_ayat_tercakup,
                            ];
                        })->toArray(),
                    ],
                    'target_kelulusan_at_snapshot' => $targetKelulusan,
                    'batas_tingkat_at_snapshot' => $batasTingkat,
                ]);

                $snapshotCount++;
            }

            DB::commit();

            return $snapshotCount;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create snapshot for specific student (Admin manual trigger)
     *
     * REFACTOR 2026-08-11: Hapus tahunAjaranId (progres lifetime)
     *
     * @param int $siswaId
     * @param string $reason
     * @param int|null $adminId
     * @return TranskripCapaian
     */
    public function createSnapshotForSiswa(int $siswaId, string $reason, ?int $adminId = null): TranskripCapaian
    {
        // Validate siswa exists
        $siswa = Siswa::findOrFail($siswaId);

        // Calculate current progress (tanpa tahunAjaranId)
        $progressData = $this->progresService->calculateProgres($siswaId);

        // Get current system parameters
        $targetKelulusan = $this->getCurrentTargetKelulusan();
        $batasTingkat = $this->getCurrentBatasTingkat();

        // Create snapshot record (tanpa tahun_ajaran_id)
        $snapshot = TranskripCapaian::create([
            'siswa_id' => $siswaId,
            'tanggal_generate' => now()->toDateString(),
            'reason' => $reason,
            'generated_by' => $adminId,
            'total_juz_tuntas_snapshot' => $progressData['total_juz_tuntas'],
            'detail_capaian_snapshot' => [
                'total_juz_tuntas' => $progressData['total_juz_tuntas'],
                'juz_berjalan' => $progressData['juz_berjalan'],
                'juz_berjalan_percentage' => $progressData['juz_berjalan_percentage'],
                'progress_toward_target' => $progressData['progress_toward_target'],
                'capaian_juz' => $progressData['capaian_juz']->map(function ($capaian) {
                    return [
                        'juz_id' => $capaian->juz_id,
                        'ayat_tercakup' => $capaian->ayat_tercakup,
                        'total_ayat' => $capaian->total_ayat,
                        'persentase' => $capaian->persentase,
                        'status_tuntas' => $capaian->status_tuntas,
                        'tanggal_tuntas' => $capaian->tanggal_tuntas,
                        'detail_ayat_tercakup' => $capaian->detail_ayat_tercakup,
                    ];
                })->toArray(),
            ],
            'target_kelulusan_at_snapshot' => $targetKelulusan,
            'batas_tingkat_at_snapshot' => $batasTingkat,
        ]);

        return $snapshot;
    }

    /**
     * Retrieve historical transcript data
     *
     * @param int $siswaId
     * @param Carbon $asOfDate
     * @return array|null Progress data as of date, or null if no snapshot exists
     */
    public function retrieveHistorical(int $siswaId, Carbon $asOfDate): ?array
    {
        // Find the most recent snapshot before or on the specified date
        $snapshot = TranskripCapaian::where('siswa_id', $siswaId)
            ->where('tanggal_generate', '<=', $asOfDate->toDateString())
            ->orderBy('tanggal_generate', 'desc')
            ->first();

        if (!$snapshot) {
            return null;
        }

        return $snapshot->detail_capaian_snapshot;
    }

    /**
     * Get latest transcript for a student
     *
     * REFACTOR 2026-08-11: Hapus tahunAjaranId parameter
     *
     * @param int $siswaId
     * @return TranskripCapaian|null
     */
    public function getLatestTranscript(int $siswaId): ?TranskripCapaian
    {
        return TranskripCapaian::where('siswa_id', $siswaId)
            ->orderBy('tanggal_generate', 'desc')
            ->first();
    }

    /**
     * Auto-generate transkrip saat siswa lulus tahfidz
     *
     * NEW METHOD 2026-08-11
     *
     * @param int $siswaId
     * @param int|null $generatedBy
     * @return TranskripCapaian|null
     */
    public function autoGenerateOnLulusTahfidz(int $siswaId, ?int $generatedBy = null): ?TranskripCapaian
    {
        $siswa = Siswa::find($siswaId);

        if ($siswa && $siswa->isLulusTahfidz()) {
            return $this->createSnapshotForSiswa($siswaId, 'lulus_tahfidz', $generatedBy);
        }

        return null;
    }

    /**
     * Auto-generate transkrip saat siswa lulus akademik
     *
     * NEW METHOD 2026-08-11
     *
     * @param int $siswaId
     * @param int|null $generatedBy
     * @return TranskripCapaian|null
     */
    public function autoGenerateOnLulusAkademik(int $siswaId, ?int $generatedBy = null): ?TranskripCapaian
    {
        $siswa = Siswa::find($siswaId);

        if ($siswa && $siswa->isLulusAkademik()) {
            return $this->createSnapshotForSiswa($siswaId, 'lulus_akademik', $generatedBy);
        }

        return null;
    }

    /**
     * Get current target kelulusan from configuration
     *
     * @return int
     */
    private function getCurrentTargetKelulusan(): int
    {
        return \App\Models\ParameterKonfigurasi::getInstance()->target_kelulusan;
    }

    /**
     * Get current batas tingkat from configuration
     *
     * @return array
     */
    private function getCurrentBatasTingkat(): array
    {
        return \App\Models\ParameterKonfigurasi::getInstance()->batas_tingkat;
    }
}
