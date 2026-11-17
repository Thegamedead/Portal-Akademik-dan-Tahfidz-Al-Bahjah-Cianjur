<?php

namespace App\Services;

use App\Models\Setoran;
use App\Models\Absensi;
use App\Models\Surah;
use App\Enums\{SkorKomponen, Keputusan};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class SetoranService
{
    /**
     * Save multiple setoran entries for a student in one session
     *
     * Req 3.1, 3.3, 7.1, 7.2, 9.1: Multi-entry support, predikat calculation, saran ayat generation
     *
     * @param int $absensiId
     * @param array $setoranEntries Array of setoran data, each containing: jenis_setoran, juz_id, surah_id, ayat_mulai, ayat_selesai, skor_tajwid, skor_kelancaran, skor_makhraj, skor_fashahah, keputusan, catatan (optional)
     * @return Collection Collection of saved Setoran models
     */
    public function saveMultipleSetorans(int $absensiId, array $setoranEntries): Collection
    {
        $savedSetorans = collect();

        DB::transaction(function () use ($absensiId, $setoranEntries, &$savedSetorans) {
            foreach ($setoranEntries as $entryData) {
                // Calculate predikat from 4 component scores (Req 7.1)
                $predikat = Setoran::calculatePredikat(
                    SkorKomponen::from($entryData['skor_tajwid']),
                    SkorKomponen::from($entryData['skor_kelancaran']),
                    SkorKomponen::from($entryData['skor_makhraj']),
                    SkorKomponen::from($entryData['skor_fashahah'])
                );

                // Create setoran record
                $setoran = new Setoran([
                    'absensi_id' => $absensiId,
                    'jenis_setoran' => $entryData['jenis_setoran'],
                    'juz_id' => $entryData['juz_id'],
                    'surah_id' => $entryData['surah_id'],
                    'ayat_mulai' => $entryData['ayat_mulai'],
                    'ayat_selesai' => $entryData['ayat_selesai'],
                    'skor_tajwid' => $entryData['skor_tajwid'],
                    'skor_kelancaran' => $entryData['skor_kelancaran'],
                    'skor_makhraj' => $entryData['skor_makhraj'],
                    'skor_fashahah' => $entryData['skor_fashahah'],
                    'predikat' => $predikat->value,
                    'keputusan' => $entryData['keputusan'],
                    'catatan' => $entryData['catatan'] ?? null,
                ]);

                // Generate saran ayat for next session (Req 9.1)
                $setoran->generateSaranAyat();

                // Save the setoran
                $setoran->save();

                $savedSetorans->push($setoran);
            }
        });

        return $savedSetorans;
    }

    /**
     * Get last saran ayat from student's previous setoran
     *
     * Req 9.3: Pre-populate form with suggestion from last session
     *
     * @param int $siswaId
     * @return array|null Array with keys: saran_juz_id, saran_surah_id, saran_ayat_mulai, saran_ayat_selesai, or null if no previous setoran
     */
    public function getLastSaranAyat(int $siswaId, ?int $kelompokId = null): ?array
    {
        $query = Setoran::whereHas('absensi', function ($q) use ($siswaId, $kelompokId) {
            $q->where('siswa_id', $siswaId);

            // Filter by kelompok if provided (to get suggestion only from current kelompok)
            if ($kelompokId !== null) {
                $q->where('kelompok_id', $kelompokId);
            }
        })
        ->whereNotNull('saran_juz_id')
        ->orderBy('created_at', 'desc');

        $lastSetoran = $query->first();

        if (!$lastSetoran) {
            return null;
        }

        return [
            'saran_juz_id'     => $lastSetoran->saran_juz_id,
            'saran_surah_id'   => $lastSetoran->saran_surah_id,
            'saran_ayat_mulai' => $lastSetoran->saran_ayat_mulai,
            'saran_ayat_selesai' => $lastSetoran->saran_ayat_selesai,
            // Flag: apakah saran ini berasal dari keputusan Ulang?
            'is_mengulang'     => $lastSetoran->keputusan === Keputusan::Ulang,
        ];
    }

    /**
     * Validate ayat range
     *
     * Req 4.3, 4.4, 20.1: ayat_selesai >= ayat_mulai, not exceed surah's total ayat
     *
     * @param int $ayatMulai
     * @param int $ayatSelesai
     * @param int $surahId
     * @return bool True if valid, false otherwise
     */
    public function validateAyatRange(int $ayatMulai, int $ayatSelesai, int $surahId): bool
    {
        // Check ayat_selesai >= ayat_mulai
        if ($ayatSelesai < $ayatMulai) {
            return false;
        }

        // Check not exceed surah's total ayat
        $surah = Surah::find($surahId);
        if (!$surah) {
            return false;
        }

        if ($ayatMulai < 1 || $ayatSelesai > $surah->jumlah_ayat) {
            return false;
        }

        return true;
    }

    /**
     * Validate ayat range berdasarkan batas juz yang sebenarnya
     *
     * Method ini memvalidasi apakah range ayat yang dipilih VALID untuk juz tertentu.
     * Contoh: Juz 28 dimulai dari Surah 58:1 dan berakhir di Surah 66:12
     * - Jika pilih Juz 28 + Surah 58, ayat mulai minimal = 1, ayat akhir maksimal = 22
     * - Jika pilih Juz 28 + Surah 66, ayat mulai minimal = 1, ayat akhir maksimal = 12 (batas juz)
     *
     * @param int $juzId
     * @param int $surahId
     * @param int $ayatMulai
     * @param int $ayatSelesai
     * @return array ['valid' => bool, 'message' => string, 'allowed_range' => ['min' => int, 'max' => int]]
     */
    public function validateAyatRangeInJuz(int $juzId, int $surahId, int $ayatMulai, int $ayatSelesai): array
    {
        $juz = \App\Models\Juz::find($juzId);
        $surah = Surah::find($surahId);

        if (!$juz || !$surah) {
            return [
                'valid' => false,
                'message' => 'Juz atau Surah tidak ditemukan',
                'allowed_range' => ['min' => 1, 'max' => 1]
            ];
        }

        // Cek apakah surah ada di juz ini
        if (!$this->validateSurahInJuz($surahId, $juzId)) {
            return [
                'valid' => false,
                'message' => "Surah {$surah->nama_latin} tidak termasuk dalam Juz {$juz->juz_ke}",
                'allowed_range' => ['min' => 1, 'max' => 1]
            ];
        }

        // Tentukan batas ayat yang valid untuk surah ini dalam juz
        $minAyat = 1;
        $maxAyat = $surah->jumlah_ayat;

        // Jika surah ini adalah surah AWAL juz, maka ayat mulai dibatasi oleh awal_ayat juz
        if ($surah->urutan_mushaf == $juz->awal_surah) {
            $minAyat = $juz->awal_ayat;
        }

        // Jika surah ini adalah surah AKHIR juz, maka ayat akhir dibatasi oleh akhir_ayat juz
        if ($surah->urutan_mushaf == $juz->akhir_surah) {
            $maxAyat = $juz->akhir_ayat;
        }

        // Validasi range
        if ($ayatMulai < $minAyat || $ayatSelesai > $maxAyat) {
            return [
                'valid' => false,
                'message' => "Untuk Juz {$juz->juz_ke} - Surah {$surah->nama_latin}, range ayat yang valid adalah {$minAyat}-{$maxAyat}",
                'allowed_range' => ['min' => $minAyat, 'max' => $maxAyat]
            ];
        }

        if ($ayatSelesai < $ayatMulai) {
            return [
                'valid' => false,
                'message' => 'Ayat akhir tidak boleh lebih kecil dari ayat awal',
                'allowed_range' => ['min' => $minAyat, 'max' => $maxAyat]
            ];
        }

        return [
            'valid' => true,
            'message' => 'Range ayat valid',
            'allowed_range' => ['min' => $minAyat, 'max' => $maxAyat]
        ];
    }

    /**
     * Get allowed ayat range untuk surah tertentu dalam juz tertentu
     * Helper method untuk frontend (menampilkan hint ke user)
     *
     * @param int $juzId
     * @param int $surahId
     * @return array ['min' => int, 'max' => int, 'hint' => string]
     */
    public function getAllowedAyatRange(int $juzId, int $surahId): array
    {
        $juz = \App\Models\Juz::find($juzId);
        $surah = Surah::find($surahId);

        if (!$juz || !$surah) {
            return ['min' => 1, 'max' => 1, 'hint' => 'Data tidak ditemukan'];
        }

        $minAyat = 1;
        $maxAyat = $surah->jumlah_ayat;

        // Surah awal juz
        if ($surah->urutan_mushaf == $juz->awal_surah) {
            $minAyat = $juz->awal_ayat;
        }

        // Surah akhir juz
        if ($surah->urutan_mushaf == $juz->akhir_surah) {
            $maxAyat = $juz->akhir_ayat;
        }

        $hint = "Range valid untuk Juz {$juz->juz_ke}: ayat {$minAyat}-{$maxAyat}";

        return [
            'min' => $minAyat,
            'max' => $maxAyat,
            'hint' => $hint
        ];
    }

    /**
     * Validate that surah belongs to selected juz
     *
     * Req 20.2: Surah must belong to juz
     *
     * @param int $surahId
     * @param int $juzId
     * @return bool True if surah belongs to juz, false otherwise
     */
    public function validateSurahInJuz(int $surahId, int $juzId): bool
    {
        $surah = Surah::find($surahId);
        if (!$surah) {
            return false;
        }

        // Check if surah is in this juz via pivot table
        return $surah->juzs()->where('juzs.id', $juzId)->exists();
    }

    /**
     * Validate that absensi status is Hadir before allowing setoran
     *
     * Req 15.1, 15.2: Cannot insert setoran if status != Hadir
     *
     * @param int $absensiId
     * @return bool True if Hadir, false otherwise
     */
    public function validateAbsensiHadir(int $absensiId): bool
    {
        $absensi = Absensi::find($absensiId);
        if (!$absensi) {
            return false;
        }

        return $absensi->status_kehadiran->value === 'Hadir';
    }

    /**
     * Check if student has completed all target hafalan for their kelompok
     * This checks if all juz in the kelompok's target have been completed (status_tuntas = 1 in CapaianJuz)
     *
     * @param int $siswaId
     * @param int $kelompokId
     * @return bool True if all target hafalan completed, false otherwise
     */
    public function hasCompletedAllTargetHafalan(int $siswaId, int $kelompokId): bool
    {
        $kelompok = \App\Models\Kelompok::find($kelompokId);
        if (!$kelompok) {
            return false;
        }

        $config = \App\Models\ParameterKonfigurasi::first();
        if (!$config || empty($config->batas_tingkat)) {
            return false;
        }

        $tingkat = $kelompok->tingkat;
        $tingkatValue = $tingkat instanceof \BackedEnum ? $tingkat->value : (string) $tingkat;
        $batasTingkat = $config->batas_tingkat[$tingkatValue] ?? [];

        // Get target juz for this tingkat
        $juzTargets = [];
        if (is_array($batasTingkat) && isset($batasTingkat['juz_mulai'])) {
            // Old format
            $juzMulai = (int) ($batasTingkat['juz_mulai'] ?? 1);
            $juzSelesai = (int) ($batasTingkat['juz_selesai'] ?? 30);
            $juzTargets = range($juzMulai, $juzSelesai);
        } elseif (is_array($batasTingkat) && !empty($batasTingkat)) {
            // New format: array of juz numbers
            $juzTargets = array_map('intval', array_values($batasTingkat));
        }

        if (empty($juzTargets)) {
            return false;
        }

        // Get all juzs in target
        $targetJuzIds = \App\Models\Juz::whereIn('juz_ke', $juzTargets)->pluck('id')->toArray();
        if (empty($targetJuzIds)) {
            return false;
        }

        // Check CapaianJuz instead of completed setorans
        // Get progres hafalan for this siswa
        $progresHafalan = \App\Models\ProgresHafalan::where('siswa_id', $siswaId)->first();
        if (!$progresHafalan) {
            return false;
        }

        // Check how many target juz are marked as status_tuntas = 1
        $completedJuzCount = \App\Models\CapaianJuz::where('progres_hafalan_id', $progresHafalan->id)
            ->whereIn('juz_id', $targetJuzIds)
            ->where('status_tuntas', 1)
            ->count();

        // All target juz must be completed
        return $completedJuzCount === count($targetJuzIds);
    }

    /**
     * Check if the specific ayat range in a surah has already been completed ("Lanjut") in a past "Hafalan Baru" setoran.
     *
     * @param int $siswaId
     * @param int $surahId
     * @param int $ayatMulai
     * @param int $ayatSelesai
     * @return bool True if it has been completed (overlaps), false otherwise.
     */
    public function hasCompletedHafalanBaru(int $siswaId, int $surahId, int $ayatMulai, int $ayatSelesai): bool
    {
        // Find all "Hafalan Baru" and "Lanjut" setorans for this student and this surah
        $pastSetorans = Setoran::whereHas('absensi', function ($query) use ($siswaId) {
                $query->where('siswa_id', $siswaId);
            })
            ->where('surah_id', $surahId)
            ->where('jenis_setoran', \App\Enums\JenisSetoran::HafalanBaru->value)
            ->where('keputusan', \App\Enums\Keputusan::Lanjut->value)
            ->get();

        // Check for any overlap
        foreach ($pastSetorans as $setoran) {
            // Overlap condition: Max(start1, start2) <= Min(end1, end2)
            $overlapStart = max($ayatMulai, $setoran->ayat_mulai);
            $overlapEnd = min($ayatSelesai, $setoran->ayat_selesai);

            if ($overlapStart <= $overlapEnd) {
                return true; // Found an overlap with already completed hafalan
            }
        }

        return false;
    }
}
