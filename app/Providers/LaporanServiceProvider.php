<?php

namespace App\Providers;

use App\Models\Absensi;
use App\Models\Setoran;
use App\Models\ProgresHafalan;
use App\Models\UsulanKenaikanTingkat;
use App\Observers\LaporanCacheObserver;
use Illuminate\Support\ServiceProvider;

/**
 * Registers the LaporanCacheObserver on relevant models to ensure
 * report caches are invalidated when underlying data changes.
 * Supports Requirement 12 (Cache invalidation events).
 */
class LaporanServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Absensi::observe(LaporanCacheObserver::class);
        Setoran::observe(LaporanCacheObserver::class);
        ProgresHafalan::observe(LaporanCacheObserver::class);
        UsulanKenaikanTingkat::observe(LaporanCacheObserver::class);
    }

    public function register(): void
    {
        //
    }
}
