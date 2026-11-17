<?php

namespace App\Services\MasterData;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Builder;

class SiswaQueryService
{
    /**
     * Build query for Siswa listing with filters and search
     *
     * @param array $filters
     * @return Builder
     */
    public function getFilteredSiswas(array $filters): Builder
    {
        $query = Siswa::with([
            'kelompok',
            'kelas',
            'tahunAjaran', // ✅ Eager load tahun ajaran
            'kelulusanProgram', // ✅ Eager load kelulusan program
            'progresHafalan', // ✅ Eager load progres hafalan
            'usulanKenaikanTingkats' => function($q) {
                $q->latest();
            }
        ])->where('status_aktif', true);

        // Apply search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        // Apply filters
        if (!empty($filters['status_akademik'])) {
            $query->where('status_akademik', $filters['status_akademik']);
        }

        if (!empty($filters['status_tahfidz'])) {
            if ($filters['status_tahfidz'] === 'Lulus Program Tahfidz' || $filters['status_tahfidz'] === 'Lulus') {
                $query->where(function($q) {
                    $q->whereIn('status_tahfidz', ['Lulus Program Tahfidz', 'Lulus']);
                });
            } elseif ($filters['status_tahfidz'] === 'Sedang Berjalan' || $filters['status_tahfidz'] === 'Sedang Berlangsung') {
                $query->where(function($q) {
                    $q->whereNotNull('kelompok_id')
                      ->where('status_penempatan', 'Ditempatkan')
                      ->whereNotIn('status_tahfidz', ['Lulus Program Tahfidz', 'Lulus']);
                });
            } else {
                $query->where('status_tahfidz', $filters['status_tahfidz']);
            }
        }

        if (!empty($filters['status_penempatan'])) {
            $query->where('status_penempatan', $filters['status_penempatan']);
        }

        if (!empty($filters['jenis_kelamin'])) {
            $query->where('jenis_kelamin', $filters['jenis_kelamin']);
        }

        if (!empty($filters['kelas_id'])) {
            $query->where('kelas_id', $filters['kelas_id']);
        }

        // Order by ID descending (newest first)
        $query->orderBy('id', 'desc');

        return $query;
    }
}
