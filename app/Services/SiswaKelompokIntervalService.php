<?php

namespace App\Services;

use App\Models\Kelompok;
use App\Models\Siswa;

class SiswaKelompokIntervalService
{
    /**
     * Get valid student date ranges for a given group and month.
     * Returns an array:
     * [
     *    'validSiswaIds' => [...],
     *    'siswaDateRanges' => [ siswa_id => [ 'Y-m-d' => true, ... ] ]
     * ]
     */
    public function getValidSiswaForBulan(Kelompok $kelompok, string $bulanTahun): array
    {
        [$year, $month] = explode('-', $bulanTahun);
        
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();

        // Cari semua siswa yang berpotensi ada di kelompok ini pada bulan tersebut
        // 1. Siswa yang saati ini aktif dan ada di kelompok ini
        // 2. Siswa yang punya riwayat di kelompok ini
        $potentialSiswas = Siswa::with(['riwayatKelompoks' => function ($q) use ($kelompok) {
            $q->where('kelompok_dari_id', $kelompok->id)
              ->orWhere('kelompok_ke_id', $kelompok->id)
              ->orderBy('tanggal', 'asc');
        }])
        ->where(function($q) use ($kelompok) {
            $q->where('kelompok_id', $kelompok->id)
              ->orWhereHas('riwayatKelompoks', function($sq) use ($kelompok) {
                  $sq->where('kelompok_dari_id', $kelompok->id)
                     ->orWhere('kelompok_ke_id', $kelompok->id);
              });
        })
        ->get();

        $validSiswaIds = [];
        $siswaDateRanges = [];

        foreach ($potentialSiswas as $siswa) {
            $intervals = [];
            $sortedHistory = $siswa->riwayatKelompoks->sortBy('tanggal')->values();
            $currentJoin = null;

            if ($sortedHistory->isEmpty()) {
                // Tidak ada history pindah. Artinya dari dulu sampai sekarang dia ada di kelompok ini
                if ($siswa->kelompok_id == $kelompok->id) {
                    $intervals[] = [
                        'join' => \Carbon\Carbon::create(2000, 1, 1), 
                        'leaveExclusive' => \Carbon\Carbon::create(2100, 1, 1)
                    ];
                }
            } else {
                foreach ($sortedHistory as $record) {
                    if ($record->kelompok_ke_id == $kelompok->id) {
                        // Siswa masuk ke kelompok ini pada tanggal ini (inclusive)
                        $currentJoin = \Carbon\Carbon::parse($record->tanggal)->startOfDay();
                    } elseif ($record->kelompok_dari_id == $kelompok->id) {
                        // Siswa keluar dari kelompok ini (exclusive: hari terakhir valid adalah 1 hari sebelum)
                        $leaveExclusive = \Carbon\Carbon::parse($record->tanggal)->startOfDay();
                        $join = $currentJoin ?? \Carbon\Carbon::create(2000, 1, 1);
                        if ($leaveExclusive->gt($join)) {
                            $intervals[] = ['join' => $join, 'leaveExclusive' => $leaveExclusive];
                        }
                        $currentJoin = null;
                    }
                }
                
                // Jika masih dalam status 'join' (belum pernah keluar), panjangkan hingga masa depan
                if ($currentJoin !== null) {
                    $intervals[] = ['join' => $currentJoin, 'leaveExclusive' => \Carbon\Carbon::create(2100, 1, 1)];
                } elseif (empty($intervals) && $siswa->kelompok_id == $kelompok->id) {
                    // Ada history tapi bukan tentang join eksplisit, namun saat ini aktif di kelompok ini
                    $intervals[] = ['join' => \Carbon\Carbon::create(2000, 1, 1), 'leaveExclusive' => \Carbon\Carbon::create(2100, 1, 1)];
                }
            }

            $intersects = false;
            $validDates = [];
            foreach ($intervals as $interval) {
                $joinStart = $interval['join']->copy()->startOfDay();
                $leaveExcl = $interval['leaveExclusive']->copy()->startOfDay();

                // Apakah interval tumpang tindih dengan bulan yang dilihat?
                if ($joinStart->lt($endDate) && $leaveExcl->gt($startDate)) {
                    $intersects = true;
                    $p = $startDate->copy();
                    while ($p->lte($endDate)) {
                        // Valid jika: p >= join (inclusive) AND p < leaveExclusive (exclusive)
                        if ($p->gte($joinStart) && $p->lt($leaveExcl)) {
                            $validDates[$p->format('Y-m-d')] = true;
                        }
                        $p->addDay();
                    }
                }
            }

            if ($intersects) {
                if ($siswa->status_akademik === 'Aktif' || \Carbon\Carbon::parse($siswa->tanggal_keluar ?? now())->startOfDay()->gte($startDate)) {
                    $validSiswaIds[] = $siswa->id;
                    $siswaDateRanges[$siswa->id] = $validDates;
                }
            }
        }

        return [
            'validSiswaIds' => $validSiswaIds,
            'siswaDateRanges' => $siswaDateRanges
        ];
    }

    /**
     * Get valid student IDs for a given group and specific date.
     * Returns an array of siswa_ids.
     */
    public function getValidSiswaForTanggal(Kelompok $kelompok, string $tanggal): array
    {
        $targetDate = \Carbon\Carbon::parse($tanggal)->startOfDay();

        // Get all history involving this group
        $groupHistories = \App\Models\RiwayatKelompok::where('kelompok_dari_id', $kelompok->id)
            ->orWhere('kelompok_ke_id', $kelompok->id)
            ->get();

        $historiesBySiswa = $groupHistories->groupBy('siswa_id');

        $currentSiswaIds = $kelompok->siswas()->pluck('id')->toArray();
        $pastSiswaIds = $groupHistories->pluck('siswa_id')->unique()->toArray();
        $allIds = array_unique(array_merge($currentSiswaIds, $pastSiswaIds));

        $siswas = Siswa::whereIn('id', $allIds)->get();
        $validSiswaIds = [];

        foreach ($siswas as $siswa) {
            $history = $historiesBySiswa->get($siswa->id) ?? collect();
            $sortedHistory = $history->sortBy('tanggal')->values();

            $inGroup = false;

            if ($sortedHistory->isEmpty()) {
                if ($siswa->kelompok_id == $kelompok->id) {
                    $inGroup = true;
                }
            } else {
                $currentJoin = null;
                foreach ($sortedHistory as $record) {
                    if ($record->kelompok_ke_id == $kelompok->id) {
                        $currentJoin = \Carbon\Carbon::parse($record->tanggal)->startOfDay();
                    } elseif ($record->kelompok_dari_id == $kelompok->id) {
                        $leaveExcl = \Carbon\Carbon::parse($record->tanggal)->startOfDay();
                        $join = $currentJoin ?? \Carbon\Carbon::create(2000, 1, 1);
                        if ($targetDate->gte($join) && $targetDate->lt($leaveExcl)) {
                            $inGroup = true;
                            break;
                        }
                        $currentJoin = null;
                    }
                }
                // If joined but never left
                if (!$inGroup && $currentJoin !== null && $targetDate->gte($currentJoin)) {
                    $inGroup = true;
                }
            }

            if ($inGroup) {
                if ($siswa->status_akademik === 'Aktif' || \Carbon\Carbon::parse($siswa->tanggal_keluar ?? now())->startOfDay()->gte($targetDate)) {
                    $validSiswaIds[] = $siswa->id;
                }
            }
        }

        return $validSiswaIds;
    }
}
