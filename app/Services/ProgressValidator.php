<?php

namespace App\Services;

use App\Models\CapaianJuz;
use App\Models\Juz;
use App\Models\ProgresHafalan;
use App\Models\Setoran;
use App\Models\Surah;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * ProgressValidator Service
 *
 * Validates integrity of progress data including referential integrity,
 * gap detection, overlap detection, and upper bound checks.
 */
class ProgressValidator
{
    /**
     * Validate progress data integrity for a student
     *
     * @param int $siswaId
     * @param int $tahunAjaranId
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validateIntegrity(int $siswaId, int $tahunAjaranId): array
    {
        $errors = [];

        // Run all validations
        $referensiErrors = $this->validateReferensi($siswaId, $tahunAjaranId);
        $totalAyatError = $this->verifyTotalAyat($siswaId, $tahunAjaranId);

        if (!empty($referensiErrors)) {
            $errors[] = 'Referential integrity violations: ' . implode(', ', $referensiErrors);
        }

        if ($totalAyatError) {
            $errors[] = $totalAyatError;
        }

        // Note: Gaps and overlaps are informational, not blocking
        // We report them but don't mark as invalid

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Validate referential integrity (juz/surah/ayat exists in Referensi Quran)
     *
     * @param int $siswaId
     * @param int $tahunAjaranId
     * @return array Error messages
     */
    public function validateReferensi(int $siswaId, int $tahunAjaranId): array
    {
        $errors = [];

        // Get all setoran for this siswa/tahun ajaran
        $setoran = Setoran::join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
            ->join('kelompoks', 'absensis.kelompok_id', '=', 'kelompoks.id')
            ->where('absensis.siswa_id', $siswaId)
            ->where('kelompoks.tahun_ajaran_id', $tahunAjaranId)
            ->select('setorans.*')
            ->get();

        // Check juz_id exists
        $juzIds = $setoran->pluck('juz_id')->unique();
        $existingJuzIds = Juz::whereIn('id', $juzIds)->pluck('id');
        $invalidJuzIds = $juzIds->diff($existingJuzIds);

        if ($invalidJuzIds->isNotEmpty()) {
            $errors[] = 'Invalid juz_id: ' . $invalidJuzIds->implode(', ');
        }

        // Check surah_id exists
        $surahIds = $setoran->pluck('surah_id')->unique();
        $existingSurahIds = Surah::whereIn('id', $surahIds)->pluck('id');
        $invalidSurahIds = $surahIds->diff($existingSurahIds);

        if ($invalidSurahIds->isNotEmpty()) {
            $errors[] = 'Invalid surah_id: ' . $invalidSurahIds->implode(', ');
        }

        // Check ayat ranges are within surah bounds
        foreach ($setoran as $item) {
            $surah = Surah::find($item->surah_id);
            if ($surah) {
                if ($item->ayat_mulai < 1 || $item->ayat_mulai > $surah->jumlah_ayat) {
                    $errors[] = "Setoran #{$item->id}: ayat_mulai {$item->ayat_mulai} out of range for surah {$surah->nama}";
                }
                if ($item->ayat_selesai < 1 || $item->ayat_selesai > $surah->jumlah_ayat) {
                    $errors[] = "Setoran #{$item->id}: ayat_selesai {$item->ayat_selesai} out of range for surah {$surah->nama}";
                }
                if ($item->ayat_mulai > $item->ayat_selesai) {
                    $errors[] = "Setoran #{$item->id}: ayat_mulai > ayat_selesai (invalid range)";
                }
            }
        }

        return $errors;
    }

    /**
     * Detect gaps in ayat sequence within a juz
     *
     * @param int $siswaId
     * @param int $juzId
     * @param int $tahunAjaranId
     * @return array Array of gap ranges ['ayat_mulai' => int, 'ayat_selesai' => int, 'surah_id' => int]
     */
    public function detectGaps(int $siswaId, int $juzId, int $tahunAjaranId): array
    {
        $juz = Juz::findOrFail($juzId);

        // Get covered ayat ranges (using ProgresHafalanService logic)
        $progresService = app(ProgresHafalanService::class);
        $ranges = $progresService->getCoveredAyatRanges($siswaId, $juzId, $tahunAjaranId);

        if ($ranges->isEmpty()) {
            // Entire juz is a gap
            return [[
                'surah_id' => $juz->awal_surah,
                'ayat_mulai' => $juz->awal_ayat,
                'ayat_selesai' => $juz->akhir_ayat,
            ]];
        }

        // Build bitmap of covered ayat (simplified approach)
        // Group by surah_id, merge ranges, find gaps
        $grouped = $ranges->groupBy('surah_id');
        $gaps = [];

        foreach ($grouped as $surahId => $surahRanges) {
            $sorted = $surahRanges->sortBy('ayat_mulai')->values();

            // Check gap from beginning
            $firstRange = $sorted->first();
            if ($surahId == $juz->awal_surah && $firstRange['ayat_mulai'] > $juz->awal_ayat) {
                $gaps[] = [
                    'surah_id' => $surahId,
                    'ayat_mulai' => $juz->awal_ayat,
                    'ayat_selesai' => $firstRange['ayat_mulai'] - 1,
                ];
            }

            // Check gaps between ranges
            for ($i = 0; $i < $sorted->count() - 1; $i++) {
                $current = $sorted[$i];
                $next = $sorted[$i + 1];

                if ($current['ayat_selesai'] + 1 < $next['ayat_mulai']) {
                    $gaps[] = [
                        'surah_id' => $surahId,
                        'ayat_mulai' => $current['ayat_selesai'] + 1,
                        'ayat_selesai' => $next['ayat_mulai'] - 1,
                    ];
                }
            }

            // Check gap to end
            $lastRange = $sorted->last();
            if ($surahId == $juz->akhir_surah && $lastRange['ayat_selesai'] < $juz->akhir_ayat) {
                $gaps[] = [
                    'surah_id' => $surahId,
                    'ayat_mulai' => $lastRange['ayat_selesai'] + 1,
                    'ayat_selesai' => $juz->akhir_ayat,
                ];
            }
        }

        return $gaps;
    }

    /**
     * Detect overlapping ayat ranges in setoran
     *
     * @param int $siswaId
     * @param int $juzId
     * @param int $tahunAjaranId
     * @return Collection Collection of overlapping setoran entries
     */
    public function detectOverlaps(int $siswaId, int $juzId, int $tahunAjaranId): Collection
    {
        // Get all qualifying setoran for this juz
        $setoran = Setoran::join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
            ->join('kelompoks', 'absensis.kelompok_id', '=', 'kelompoks.id')
            ->where('absensis.siswa_id', $siswaId)
            ->where('kelompoks.tahun_ajaran_id', $tahunAjaranId)
            ->where('setorans.juz_id', $juzId)
            ->where('setorans.jenis_setoran', 'Hafalan Baru')
            ->where('setorans.keputusan', 'Lanjut')
            ->select('setorans.*')
            ->orderBy('setorans.surah_id')
            ->orderBy('setorans.ayat_mulai')
            ->get();

        $overlaps = collect();

        // Check each pair for overlap
        for ($i = 0; $i < $setoran->count(); $i++) {
            for ($j = $i + 1; $j < $setoran->count(); $j++) {
                $first = $setoran[$i];
                $second = $setoran[$j];

                // Only check if same surah
                if ($first->surah_id !== $second->surah_id) {
                    continue;
                }

                // Check if ranges overlap
                $overlaps_condition = (
                    $first->ayat_mulai <= $second->ayat_selesai &&
                    $second->ayat_mulai <= $first->ayat_selesai
                );

                if ($overlaps_condition) {
                    $overlaps->push([
                        'setoran_1' => [
                            'id' => $first->id,
                            'surah_id' => $first->surah_id,
                            'ayat_range' => "{$first->ayat_mulai}-{$first->ayat_selesai}",
                        ],
                        'setoran_2' => [
                            'id' => $second->id,
                            'surah_id' => $second->surah_id,
                            'ayat_range' => "{$second->ayat_mulai}-{$second->ayat_selesai}",
                        ],
                        'overlap_range' => [
                            'ayat_mulai' => max($first->ayat_mulai, $second->ayat_mulai),
                            'ayat_selesai' => min($first->ayat_selesai, $second->ayat_selesai),
                        ],
                    ]);
                }
            }
        }

        return $overlaps;
    }

    /**
     * Verify total covered ayat does not exceed Referensi Quran
     *
     * @param int $siswaId
     * @param int $tahunAjaranId
     * @return string|null Error message, or null if valid
     */
    public function verifyTotalAyat(int $siswaId, int $tahunAjaranId): ?string
    {
        // Get total ayat across all 30 juz from Referensi Quran
        $totalAyatQuran = Juz::sum('jumlah_ayat');

        // REFACTOR 2026-08-11: Progres hafalan sekarang lifetime (tanpa tahun_ajaran_id)
        // Get total covered ayat for siswa (from CapaianJuz)
        $progresHafalan = ProgresHafalan::where('siswa_id', $siswaId)->first();

        if (!$progresHafalan) {
            return null; // No progress data, nothing to validate
        }

        $totalCoveredAyat = CapaianJuz::where('progres_hafalan_id', $progresHafalan->id)
            ->sum('ayat_tercakup');

        if ($totalCoveredAyat > $totalAyatQuran) {
            return "Total covered ayat ($totalCoveredAyat) exceeds Quran total ($totalAyatQuran)";
        }

        return null;
    }
}
