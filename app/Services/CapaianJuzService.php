<?php

namespace App\Services;

use App\Models\Juz;
use App\Models\ProgresHafalan;
use App\Models\Setoran;
use Illuminate\Support\Collection;

/**
 * CapaianJuzService
 *
 * Mengurus logika spesifik terkait pencapaian ayat dan perhitungan 
 * apakah sebuah Juz sudah tuntas dihafal berdasarkan rentang ayat yang disetorkan.
 */
class CapaianJuzService
{
    /**
     * Get covered ayat ranges for a juz from baseline and qualifying setoran
     *
     * @param int $siswaId
     * @param int $juzId
     * @return Collection Array of [surah_id, ayat_mulai, ayat_selesai]
     */
    public function getCoveredAyatRanges(int $siswaId, int $juzId): Collection
    {
        $ranges = collect();

        // Get juz info
        $juz = Juz::findOrFail($juzId);

        // Get ProgresHafalan to check baseline
        $progresHafalan = ProgresHafalan::where('siswa_id', $siswaId)->first();

        // Add baseline ranges if juz is in baseline
        if ($progresHafalan && $progresHafalan->baseline_juzs) {
            if (in_array($juzId, $progresHafalan->baseline_juzs)) {
                // Baseline covers full juz
                $ranges->push([
                    'surah_id' => $juz->awal_surah,
                    'ayat_mulai' => $juz->awal_ayat,
                    'ayat_selesai' => $juz->akhir_ayat,
                    'tanggal' => $progresHafalan->created_at ? $progresHafalan->created_at->toDateString() : '2000-01-01',
                ]);
            }
        }

        // Get qualifying setoran (any jenis with Lanjut decision) for this juz
        $setoran = Setoran::join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
            ->where('absensis.siswa_id', $siswaId)
            ->where('setorans.juz_id', $juzId)
            ->where('setorans.keputusan', 'Lanjut') // Lanjut from any jenis
            ->select('setorans.surah_id', 'setorans.ayat_mulai', 'setorans.ayat_selesai', 'absensis.tanggal', 'setorans.created_at')
            ->orderBy('absensis.tanggal', 'asc')
            ->orderBy('setorans.created_at', 'asc')
            ->get();

        foreach ($setoran as $item) {
            $ranges->push([
                'surah_id' => $item->surah_id,
                'ayat_mulai' => $item->ayat_mulai,
                'ayat_selesai' => $item->ayat_selesai,
                'tanggal' => $item->tanggal,
            ]);
        }

        return $ranges;
    }

    /**
     * Merge overlapping ayat ranges using set union algorithm
     *
     * @param Collection $ranges Array of [surah_id, ayat_mulai, ayat_selesai]
     * @return Collection Merged ranges
     */
    public function unionAyatRanges(Collection $ranges): Collection
    {
        if ($ranges->isEmpty()) {
            return collect();
        }

        // Group by surah_id first
        $grouped = $ranges->groupBy('surah_id');
        $merged = collect();

        foreach ($grouped as $surahId => $surahRanges) {
            // Sort by ayat_mulai
            $sorted = $surahRanges->sortBy('ayat_mulai')->values();

            $current = $sorted->first();

            for ($i = 1; $i < $sorted->count(); $i++) {
                $next = $sorted[$i];

                // Check if ranges overlap or adjacent
                if ($next['ayat_mulai'] <= $current['ayat_selesai'] + 1) {
                    // Merge: extend current range
                    $current['ayat_selesai'] = max($current['ayat_selesai'], $next['ayat_selesai']);
                } else {
                    // No overlap: save current and move to next
                    $merged->push($current);
                    $current = $next;
                }
            }

            // Push last range
            $merged->push($current);
        }

        return $merged;
    }

    /**
     * Count unique ayat from merged ranges
     *
     * @param Collection $mergedRanges
     * @return int
     */
    public function countAyatFromRanges(Collection $mergedRanges): int
    {
        $count = 0;

        foreach ($mergedRanges as $range) {
            $count += ($range['ayat_selesai'] - $range['ayat_mulai'] + 1);
        }

        return $count;
    }

    /**
     * Calculate if a juz is complete (100% ayat covered)
     *
     * @param int $siswaId
     * @param int $juzId
     * @return bool
     */
    public function calculateJuzTuntas(int $siswaId, int $juzId): bool
    {
        $juz = Juz::findOrFail($juzId);
        $ranges = $this->getCoveredAyatRanges($siswaId, $juzId);
        $mergedRanges = $this->unionAyatRanges($ranges);
        $ayatTercakup = $this->countAyatFromRanges($mergedRanges);
        
        return $ayatTercakup >= $juz->jumlah_ayat;
    }

    /**
     * Calculate the exact date a juz reached 100% completion
     */
    public function calculateExactTuntasDate(Juz $juz, Collection $ranges): ?string
    {
        if ($ranges->isEmpty()) return null;
        
        $sortedRanges = $ranges->sortBy('tanggal')->values();
        $tempRanges = collect();
        
        foreach ($sortedRanges as $range) {
            $tempRanges->push($range);
            $merged = $this->unionAyatRanges($tempRanges);
            if ($this->countAyatFromRanges($merged) >= $juz->jumlah_ayat) {
                return $range['tanggal'] ?? null;
            }
        }
        
        return $sortedRanges->last()['tanggal'] ?? null;
    }
}
