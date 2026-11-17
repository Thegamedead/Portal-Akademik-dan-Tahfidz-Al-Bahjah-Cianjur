<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

/**
 * Service for applying consistent query optimizations
 *
 * Provides eager loading presets and query optimization patterns
 */
class QueryOptimizationService
{
    /**
     * Get eager loading presets for common scenarios
     *
     * @return array<string, array>
     */
    private function getPresets(): array
    {
        return [
            'siswa.full' => [
                'kelompok:id,nama,tingkat,guru_pembimbing_id',
                'kelompok.guruPembimbing:id,nama',
                'kelas:id,nama,tingkat',
                'tahunAjaran:id,tahun_mulai,tahun_selesai',
                'progresHafalan:id,siswa_id,total_juz_tuntas,target_juz,status_tahfidz',
            ],

            'siswa.dashboard' => [
                'kelompok:id,nama,tingkat',
                'progresHafalan:id,siswa_id,total_juz_tuntas,target_juz',
            ],

            'siswa.list' => [
                'kelompok:id,nama',
                'kelas:id,nama',
            ],

            'kelompok.dashboard' => [
                'guruPembimbing:id,nama,nip',
                'tahunAjaran:id,label,tanggal_mulai,tanggal_selesai',
            ],

            'absensi.detail' => [
                'kelompok:id,nama',
                'siswa:id,nama,nis',
                'guruPengisi:id,nama',
                'setorans.surah:id,nama_latin,nomor_surah',
                'setorans.juz:id,juz_ke',
            ],

            'setoran.detail' => [
                'absensi:id,tanggal,kelompok_id,siswa_id',
                'absensi.siswa:id,nama,nis',
                'absensi.kelompok:id,nama',
                'juz:id,juz_ke',
                'surah:id,nama_latin,nomor_surah,jumlah_ayat',
                'saranSurah:id,nama_latin',
                'saranJuz:id,juz_ke',
            ],

            'progres.calculation' => [
                'siswa:id,nama,nis',
                'progresPerJuzs:id,progres_hafalan_id,juz_id,persentase,ayat_tercakup',
                'progresPerJuzs.juz:id,juz_ke',
            ],

        ];
    }

    /**
     * Apply eager loading preset to query
     *
     * @param Builder $query
     * @param string $preset Preset name from getPresets()
     * @return Builder
     */
    public function applyPreset(Builder $query, string $preset): Builder
    {
        $presets = $this->getPresets();

        if (!isset($presets[$preset])) {
            throw new \InvalidArgumentException("Unknown preset: {$preset}");
        }

        return $query->with($presets[$preset]);
    }

    /**
     * Optimize query for pagination with counting
     *
     * @param Builder $query
     * @return Builder
     */
    public function optimizeForPagination(Builder $query): Builder
    {
        // Select only needed columns to reduce memory usage
        // Note: Ensure id is always selected for relationships
        return $query->select([
            $query->getModel()->getTable() . '.*'
        ]);
    }

    /**
     * Apply selective column loading for list views
     *
     * @param Builder $query
     * @param array<string> $columns Columns to select
     * @return Builder
     */
    public function selectiveLoad(Builder $query, array $columns): Builder
    {
        // Always include id for relationships
        if (!in_array('id', $columns)) {
            array_unshift($columns, 'id');
        }

        return $query->select($columns);
    }

    /**
     * Optimize subquery to avoid N+1
     *
     * @param Builder $query
     * @param string $relation Relationship name
     * @param \Closure $subquery Subquery callback
     * @return Builder
     */
    public function withCountOptimized(Builder $query, string $relation, ?\Closure $subquery = null): Builder
    {
        if ($subquery) {
            return $query->withCount([$relation => $subquery]);
        }

        return $query->withCount($relation);
    }

    /**
     * Load relationship only if not already loaded
     *
     * @param mixed $model Model or collection
     * @param string|array $relations
     * @return mixed
     */
    public function lazyLoadIfNeeded($model, string|array $relations): mixed
    {
        if ($model instanceof \Illuminate\Database\Eloquent\Collection) {
            return $model->loadMissing($relations);
        }

        if ($model instanceof \Illuminate\Database\Eloquent\Model) {
            return $model->loadMissing($relations);
        }

        return $model;
    }

    /**
     * Chunk large queries for memory efficiency
     *
     * @param Builder $query
     * @param int $chunkSize
     * @param \Closure $callback
     * @return void
     */
    public function chunkProcess(Builder $query, int $chunkSize, \Closure $callback): void
    {
        $query->chunk($chunkSize, function ($items) use ($callback) {
            foreach ($items as $item) {
                $callback($item);
            }
        });
    }

    /**
     * Get optimal chunk size based on available memory
     *
     * @return int
     */
    public function getOptimalChunkSize(): int
    {
        $memoryLimit = $this->getMemoryLimitInBytes();
        $availableMemory = $memoryLimit - memory_get_usage(true);

        // Conservative estimate: 1KB per record
        $estimatedRecordSize = 1024;

        // Use 50% of available memory
        $optimalSize = (int) (($availableMemory * 0.5) / $estimatedRecordSize);

        // Clamp between 100 and 1000
        return max(100, min(1000, $optimalSize));
    }

    /**
     * Get PHP memory limit in bytes
     *
     * @return int
     */
    private function getMemoryLimitInBytes(): int
    {
        $memoryLimit = ini_get('memory_limit');

        if ($memoryLimit === '-1') {
            return PHP_INT_MAX; // No limit
        }

        $unit = strtolower(substr($memoryLimit, -1));
        $value = (int) $memoryLimit;

        return match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => $value,
        };
    }

    /**
     * Apply date range filter with index optimization
     *
     * @param Builder $query
     * @param string $column Date column name
     * @param \Carbon\Carbon $start Start date
     * @param \Carbon\Carbon $end End date
     * @return Builder
     */
    public function applyDateRange(
        Builder $query,
        string $column,
        \Carbon\Carbon $start,
        \Carbon\Carbon $end
    ): Builder {
        // Use whereBetween for better index usage
        return $query->whereBetween($column, [
            $start->startOfDay(),
            $end->endOfDay()
        ]);
    }
}
