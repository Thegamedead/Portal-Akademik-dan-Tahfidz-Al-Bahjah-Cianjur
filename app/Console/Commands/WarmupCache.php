<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheService;

/**
 * Warm up application cache with frequently accessed data
 */
class WarmupCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:warmup
                            {--clear : Clear cache before warming up}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pre-load frequently accessed data into cache';

    /**
     * Execute the console command.
     */
    public function handle(CacheService $cacheService): int
    {
        if ($this->option('clear')) {
            $this->info('Clearing existing cache...');
            $cacheService->clearAll();
        }

        $this->info('Warming up cache...');

        $bar = $this->output->createProgressBar(4);
        $bar->start();

        // Warm up reference data
        $cacheService->warmUp();
        $bar->advance();

        // Pre-load active semester
        \Illuminate\Support\Facades\Cache::remember('reference.semester.active', 3600, function () {
            return \App\Models\Semester::with('tahunAjaran')
                ->where('status_aktif', true)
                ->first();
        });
        $bar->advance();

        // Pre-load parameter konfigurasi
        \Illuminate\Support\Facades\Cache::remember('reference.parameter.config', 3600, function () {
            return \App\Models\ParameterKonfigurasi::first();
        });
        $bar->advance();

        // Pre-load sekolah data
        \Illuminate\Support\Facades\Cache::remember('reference.sekolah', 3600, function () {
            return \App\Models\Sekolah::first();
        });
        $bar->advance();

        $bar->finish();
        $this->newLine(2);
        $this->info('✅ Cache warmed up successfully!');

        return Command::SUCCESS;
    }
}
