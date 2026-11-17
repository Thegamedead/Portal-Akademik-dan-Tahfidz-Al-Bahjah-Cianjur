<?php

namespace App\Services;

use App\Models\{Juz, Surah};
use Illuminate\Support\Facades\Cache;

/**
 * Service untuk caching data referensi yang jarang berubah
 *
 * Requirements: 20.1, 23.2
 */
class ReferensiCacheService
{
    /**
     * Cache duration: 24 jam
     */
    protected const CACHE_DURATION = 86400; // 24 hours in seconds

    /**
     * Get all juzs dengan caching
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getJuzs()
    {
        return Juz::orderBy('juz_ke')->get();
    }

    /**
     * Get all surahs dengan relationships juzs (for filtering)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getSurahs()
    {
        return Surah::with('juzs')->orderBy('nomor_surah')->get();
    }

    /**
     * Get kelompok binaan per guru dengan caching
     * Cache menggunakan tag 'kelompok-binaan:{guru_id}' untuk invalidation
     *
     * @param int $guruId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getKelompokBinaan(int $guruId)
    {
        $cacheKey = "kelompok-binaan:{$guruId}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($guruId) {
            $guru = \App\Models\Guru::findOrFail($guruId);
            return $guru->kelompoksBimbingan()->get(['id', 'nama']);
        });
    }

    /**
     * Invalidate (clear) cache untuk juzs & surahs
     * Dipanggil ketika data referensi berubah (jarang terjadi)
     *
     * @return void
     */
    public static function clearReferensiCache(): void
    {
        Cache::forget('referensi:juzs');
        Cache::forget('referensi:surahs');
    }

    /**
     * Invalidate (clear) cache kelompok binaan untuk guru tertentu
     * Dipanggil ketika kelompok berubah (guru_pembimbing_id diupdate, dll)
     *
     * @param int $guruId
     * @return void
     */
    public static function clearKelompokBinaanCache(int $guruId): void
    {
        Cache::forget("kelompok-binaan:{$guruId}");
    }

    /**
     * Clear all kelompok binaan cache untuk semua guru
     * Dipanggil ketika ada perubahan massal pada kelompok
     *
     * @return void
     */
    public static function clearAllKelompokBinaanCache(): void
    {
        // Laravel Cache tidak support wildcard delete tanpa tag driver (Redis/Memcached)
        // Untuk file/database cache driver, kita bisa clear by pattern manually
        // Atau gunakan Cache::flush() untuk clear semua (tidak recommended untuk production)

        // Alternatif: Track guru IDs yang punya cache dan clear satu per satu
        // Ini implementation sederhana untuk MVP
        $guruIds = \App\Models\Guru::pluck('id');
        foreach ($guruIds as $guruId) {
            self::clearKelompokBinaanCache($guruId);
        }
    }
}
