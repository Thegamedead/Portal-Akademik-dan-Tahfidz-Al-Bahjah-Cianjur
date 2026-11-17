<?php

namespace App\Observers;

use App\Models\Absensi;
use App\Services\CacheService;

/**
 * Observer for Absensi model
 *
 * Automatically invalidates related caches when absensi changes
 */
class AbsensiObserver
{
    public function __construct(
        private CacheService $cacheService
    ) {}

    /**
     * Handle the Absensi "created" event.
     */
    public function created(Absensi $absensi): void
    {
        $this->invalidateRelatedCaches($absensi);
    }

    /**
     * Handle the Absensi "updated" event.
     */
    public function updated(Absensi $absensi): void
    {
        $this->invalidateRelatedCaches($absensi);
    }

    /**
     * Handle the Absensi "deleted" event.
     */
    public function deleted(Absensi $absensi): void
    {
        $this->invalidateRelatedCaches($absensi);
    }

    /**
     * Invalidate all caches related to this absensi
     */
    private function invalidateRelatedCaches(Absensi $absensi): void
    {
        // Invalidate student cache
        if ($absensi->siswa_id) {
            $this->cacheService->invalidateStudent($absensi->siswa_id);
        }

        // Invalidate kelompok cache
        if ($absensi->kelompok_id) {
            $this->cacheService->invalidateKelompok($absensi->kelompok_id);
        }
    }
}
