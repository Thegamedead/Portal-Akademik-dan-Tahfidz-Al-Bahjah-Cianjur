<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait for searchable student queries
 *
 * Provides consistent search functionality across components
 */
trait HasSearchableStudents
{
    /**
     * Apply search filter to student query
     *
     * @param Builder $query
     * @param string|null $search
     * @return Builder
     */
    protected function applyStudentSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('nis', 'like', "%{$search}%")
              ->orWhere('nisn', 'like', "%{$search}%")
              ->orWhere('nama', 'like', "%{$search}%");
        });
    }

    /**
     * Apply status akademik filter
     *
     * @param Builder $query
     * @param string|null $status
     * @return Builder
     */
    protected function applyStatusAkademikFilter(Builder $query, ?string $status): Builder
    {
        if (!$status) {
            return $query;
        }

        return $query->where('status_akademik', $status);
    }

    /**
     * Apply status penempatan filter
     *
     * @param Builder $query
     * @param string|null $status
     * @return Builder
     */
    protected function applyStatusPenempatanFilter(Builder $query, ?string $status): Builder
    {
        if (!$status) {
            return $query;
        }

        return $query->where('status_penempatan', $status);
    }

    /**
     * Apply kelompok filter
     *
     * @param Builder $query
     * @param int|null $kelompokId
     * @return Builder
     */
    protected function applyKelompokFilter(Builder $query, ?int $kelompokId): Builder
    {
        if (!$kelompokId) {
            return $query;
        }

        return $query->where('kelompok_id', $kelompokId);
    }
}
