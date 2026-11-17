<?php

namespace App\Services\TesPenempatan;

use App\Models\Surah;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ReferensiQuranValidator
{
    /**
     * Checks if the given surah belongs to the given juz based on juz_surah pivot table.
     */
    public function validateJuzSurahRelation(int $juzId, int $surahId): bool
    {
        return DB::table('juz_surah')
            ->where('juz_id', $juzId)
            ->where('surah_id', $surahId)
            ->exists();
    }

    /**
     * Validates if the ayat range is within the surah's total ayats and logically correct.
     * Throws InvalidArgumentException if invalid.
     */
    public function validateAyatRange(int $surahId, int $ayatMulai, int $ayatSelesai): void
    {
        if ($ayatMulai > $ayatSelesai) {
            throw new InvalidArgumentException("Ayat mulai ($ayatMulai) tidak boleh lebih besar dari ayat selesai ($ayatSelesai).");
        }

        if ($ayatMulai < 1) {
            throw new InvalidArgumentException("Ayat mulai harus minimal 1.");
        }

        $surah = Surah::find($surahId);
        
        if (!$surah) {
            throw new InvalidArgumentException("Surah ID $surahId tidak ditemukan.");
        }

        if ($ayatSelesai > $surah->jumlah_ayat) {
            throw new InvalidArgumentException("Ayat selesai ($ayatSelesai) melebihi jumlah ayat maksimal pada surah {$surah->nama_latin} ({$surah->jumlah_ayat}).");
        }
    }

    /**
     * Returns the maximum valid ayat count for a given surah.
     */
    public function getValidAyatRange(int $surahId): int
    {
        $surah = Surah::find($surahId);
        return $surah ? $surah->jumlah_ayat : 0;
    }
}
