<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

/**
 * Centralized cache management service
 *
 * Provides consistent caching patterns and automatic invalidation
 */
class CacheService
{
    // Cache TTL constants (in seconds)
    private const TTL_SHORT = 300;      // 5 minutes
    private const TTL_MEDIUM = 1800;    // 30 minutes
    private const TTL_LONG = 3600;      // 1 hour
    private const TTL_VERY_LONG = 21600; // 6 hours

    /**
     * Cache reference data (Juz, Surah, etc.) - rarely changes
     *
     * @param string $key Cache key
     * @param \Closure $callback Data retrieval function
     * @return mixed
     */
    public function rememberReferenceData(string $key, \Closure $callback): mixed
    {
        return Cache::remember($key, self::TTL_VERY_LONG, $callback);
    }

    /**
     * Cache dashboard data - changes frequently
     *
     * @param string $key Cache key
     * @param \Closure $callback Data retrieval function
     * @return mixed
     */
    public function rememberDashboard(string $key, \Closure $callback): mixed
    {
        return Cache::remember($key, self::TTL_SHORT, $callback);
    }

    /**
     * Cache student progress - medium frequency
     *
     * @param int $siswaId Student ID
     * @param \Closure $callback Data retrieval function
     * @return mixed
     */
    public function rememberStudentProgress(int $siswaId, \Closure $callback): mixed
    {
        $key = "student.progress.{$siswaId}";
        return Cache::remember($key, self::TTL_MEDIUM, $callback);
    }

    /**
     * Cache kelompok data
     *
     * @param int $kelompokId Kelompok ID
     * @param string $subkey Subkey for different data types
     * @param \Closure $callback Data retrieval function
     * @return mixed
     */
    public function rememberKelompok(int $kelompokId, string $subkey, \Closure $callback): mixed
    {
        $key = "kelompok.{$kelompokId}.{$subkey}";
        return Cache::remember($key, self::TTL_MEDIUM, $callback);
    }

    /**
     * Invalidate student-related caches
     *
     * @param int $siswaId Student ID
     * @return void
     */
    public function invalidateStudent(int $siswaId): void
    {
        Cache::forget("student.progress.{$siswaId}");
        Cache::forget("student.dashboard.{$siswaId}");
        Cache::forget("student.history.{$siswaId}");
    }

    /**
     * Invalidate kelompok-related caches
     *
     * @param int $kelompokId Kelompok ID
     * @return void
     */
    public function invalidateKelompok(int $kelompokId): void
    {
        $keys = [
            "kelompok.{$kelompokId}.dashboard",
            "kelompok.{$kelompokId}.students",
            "kelompok.{$kelompokId}.progress",
            "kelompok.{$kelompokId}.statistics",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Invalidate all reference data caches
     *
     * @return void
     */
    public function invalidateReferenceData(): void
    {
        Cache::forget('reference.juz.all');
        Cache::forget('reference.surah.all');
        Cache::forget('reference.kelompok.all');
        Cache::forget('reference.tahun_ajaran.active');
    }

    /**
     * Get or cache all Juz data
     *
     * @return Collection
     */
    public function getAllJuz(): Collection
    {
        return $this->rememberReferenceData('reference.juz.all', function () {
            return \App\Models\Juz::orderBy('nomor')->get();
        });
    }

    /**
     * Get or cache all Surah data
     *
     * @return Collection
     */
    public function getAllSurah(): Collection
    {
        return $this->rememberReferenceData('reference.surah.all', function () {
            return \App\Models\Surah::orderBy('nomor')->get();
        });
    }

    /**
     * Get or cache active tahun ajaran
     *
     * @return \App\Models\TahunAjaran|null
     */
    public function getActiveTahunAjaran(): ?\App\Models\TahunAjaran
    {
        return $this->rememberReferenceData('reference.tahun_ajaran.active', function () {
            return \App\Models\TahunAjaran::where('status', true)->first();
        });
    }

    /**
     * Warm up cache with frequently accessed data
     *
     * @return void
     */
    public function warmUp(): void
    {
        // Pre-load reference data
        $this->getAllJuz();
        $this->getAllSurah();
        $this->getActiveTahunAjaran();

        // Pre-load active kelompok list
        $this->rememberReferenceData('reference.kelompok.active', function () {
            $tahunAjaran = $this->getActiveTahunAjaran();
            if (!$tahunAjaran) return collect();

            return \App\Models\Kelompok::where('tahun_ajaran_id', $tahunAjaran->id)
                ->with('guruPembimbing:id,nama')
                ->select('id', 'nama', 'tingkat', 'guru_pembimbing_id')
                ->get();
        });
    }

    /**
     * Clear all application caches
     *
     * @return void
     */
    public function clearAll(): void
    {
        Cache::flush();
    }
}
