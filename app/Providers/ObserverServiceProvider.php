<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\{Setoran, Absensi, ProgresHafalan};
use App\Observers\{SetoranObserver, AbsensiObserver};

/**
 * Register model observers for automatic cache invalidation
 */
class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register observers
        Setoran::observe(SetoranObserver::class);
        Absensi::observe(AbsensiObserver::class);
    }
}
