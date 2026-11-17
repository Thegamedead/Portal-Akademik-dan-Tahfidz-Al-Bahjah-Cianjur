<?php

namespace App\Observers;

use App\Models\Setoran;
use App\Services\CacheService;

/**
 * Observer for Setoran model
 *
 * Automatically invalidates related caches when setoran changes
 */
class SetoranObserver
{
    public function __construct(
        private CacheService $cacheService
    ) {}

    /**
     * Handle the Setoran "created" event.
     */
    public function created(Setoran $setoran): void
    {
        $this->invalidateRelatedCaches($setoran);
    }

    /**
     * Handle the Setoran "updated" event.
     */
    public function updated(Setoran $setoran): void
    {
        $this->invalidateRelatedCaches($setoran);
    }

    /**
     * Handle the Setoran "deleted" event.
     */
    public function deleted(Setoran $setoran): void
    {
        $this->invalidateRelatedCaches($setoran);
    }

    /**
     * Invalidate all caches related to this setoran
     */
    private function invalidateRelatedCaches(Setoran $setoran): void
    {
        // Load absensi if not loaded
        if (!$setoran->relationLoaded('absensi')) {
            $setoran->load('absensi:id,siswa_id,kelompok_id');
        }

        $absensi = $setoran->absensi;
        if (!$absensi) return;

        // Invalidate student progress cache
        if ($absensi->siswa_id) {
            $this->cacheService->invalidateStudent($absensi->siswa_id);
        }

        // Invalidate kelompok statistics cache
        if ($absensi->kelompok_id) {
            $this->cacheService->invalidateKelompok($absensi->kelompok_id);
        }
    }
}
