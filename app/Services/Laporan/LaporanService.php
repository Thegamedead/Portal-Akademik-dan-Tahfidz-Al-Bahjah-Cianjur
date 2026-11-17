<?php

namespace App\Services\Laporan;

use Illuminate\Support\Facades\Cache;

class LaporanService
{
    /**
     * Invalidate specific report cache.
     * This is a stub created to satisfy LaporanCacheObserver.
     * 
     * @param string $type
     * @return void
     */
    public function invalidateCache(string $type): void
    {
        // For now, if we are using tags (e.g. redis/memcached) we could do:
        // Cache::tags(['laporan', $type])->flush();
        // Since we don't know the exact caching driver/mechanism intended,
        // we leave this safely empty or implement a generic prefix clearer.
        // It prevents the "failed to open stream" error during model saves.
    }
}
