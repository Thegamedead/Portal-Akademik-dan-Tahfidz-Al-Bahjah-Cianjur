<?php

namespace App\Observers;

use App\Services\Laporan\LaporanService;

/**
 * Observes Absensi & Setoran model changes and invalidates related report caches.
 * This supports Requirement 12: Cache invalidation events.
 */
class LaporanCacheObserver
{
    public function __construct(protected LaporanService $laporanService) {}

    public function created($model): void
    {
        $this->invalidateForModel($model);
    }

    public function updated($model): void
    {
        $this->invalidateForModel($model);
    }

    public function deleted($model): void
    {
        $this->invalidateForModel($model);
    }

    protected function invalidateForModel($model): void
    {
        $class = class_basename($model);

        match ($class) {
            'Absensi'  => $this->invalidate(['absensi', 'rekap-kelompok']),
            'Setoran'  => $this->invalidate(['setoran', 'rekap-kelompok']),
            'UsulanKenaikanTingkat' => $this->invalidate(['kenaikan-tingkat']),
            'ProgresHafalan' => $this->invalidate(['progres-hafalan', 'rekap-kelompok', 'kelulusan', 'belum-tuntas']),
            default    => null,
        };
    }

    protected function invalidate(array $types): void
    {
        foreach ($types as $type) {
            $this->laporanService->invalidateCache($type);
        }
    }
}
