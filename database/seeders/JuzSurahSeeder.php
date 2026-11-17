<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Juz;
use App\Models\Surah;

class JuzSurahSeeder extends Seeder
{
    /**
     * Seed mapping antara juz dan surah.
     *
     * Data diekstrak dari file referensi SDD/JuzSeeder.php
     * Format: [juz_ke, surah_awal, ayat_awal_surah_awal, surah_akhir, ayat_akhir_surah_akhir]
     *
     * Sumber data: standar pembagian juz Al-Quran (Utsmani/Rasm Madinah).
     */
    public function run(): void
    {
        // Data mapping juz -> surah dengan ayat mulai dan selesai
        $mappings = [
            // [juz_ke, surah_awal, ayat_awal, surah_akhir, ayat_akhir]
            [1, 1, 1, 2, 141],      // Juz 1: Al-Fatihah 1:1 – Al-Baqarah 2:141
            [2, 2, 142, 2, 252],    // Juz 2: Al-Baqarah 2:142 – Al-Baqarah 2:252
            [3, 2, 253, 3, 92],     // Juz 3: Al-Baqarah 2:253 – Ali-Imran 3:92
            [4, 3, 93, 4, 23],      // Juz 4: Ali-Imran 3:93 – An-Nisa 4:23
            [5, 4, 24, 4, 147],     // Juz 5: An-Nisa 4:24 – An-Nisa 4:147
            [6, 4, 148, 5, 81],     // Juz 6: An-Nisa 4:148 – Al-Maidah 5:81
            [7, 5, 82, 6, 110],     // Juz 7: Al-Maidah 5:82 – Al-An'am 6:110
            [8, 6, 111, 7, 87],     // Juz 8: Al-An'am 6:111 – Al-A'raf 7:87
            [9, 7, 88, 8, 40],      // Juz 9: Al-A'raf 7:88 – Al-Anfal 8:40
            [10, 8, 41, 9, 92],     // Juz 10: Al-Anfal 8:41 – At-Tawbah 9:92
            [11, 9, 93, 11, 5],     // Juz 11: At-Tawbah 9:93 – Hud 11:5
            [12, 11, 6, 12, 52],    // Juz 12: Hud 11:6 – Yusuf 12:52
            [13, 12, 53, 14, 52],   // Juz 13: Yusuf 12:53 – Ibrahim 14:52
            [14, 15, 1, 16, 128],   // Juz 14: Al-Hijr 15:1 – An-Nahl 16:128
            [15, 17, 1, 18, 74],    // Juz 15: Al-Isra 17:1 – Al-Kahf 18:74
            [16, 18, 75, 20, 135],  // Juz 16: Al-Kahf 18:75 – Ta-Ha 20:135
            [17, 21, 1, 22, 78],    // Juz 17: Al-Anbiya 21:1 – Al-Hajj 22:78
            [18, 23, 1, 25, 20],    // Juz 18: Al-Muminun 23:1 – Al-Furqan 25:20
            [19, 25, 21, 27, 55],   // Juz 19: Al-Furqan 25:21 – An-Naml 27:55
            [20, 27, 56, 29, 45],   // Juz 20: An-Naml 27:56 – Al-Ankabut 29:45
            [21, 29, 46, 33, 30],   // Juz 21: Al-Ankabut 29:46 – Al-Ahzab 33:30
            [22, 33, 31, 36, 27],   // Juz 22: Al-Ahzab 33:31 – Ya-Sin 36:27
            [23, 36, 28, 39, 31],   // Juz 23: Ya-Sin 36:28 – Az-Zumar 39:31
            [24, 39, 32, 41, 46],   // Juz 24: Az-Zumar 39:32 – Fussilat 41:46
            [25, 41, 47, 45, 37],   // Juz 25: Fussilat 41:47 – Al-Jathiyah 45:37
            [26, 46, 1, 51, 30],    // Juz 26: Al-Ahqaf 46:1 – Adh-Dhariyat 51:30
            [27, 51, 31, 57, 29],   // Juz 27: Adh-Dhariyat 51:31 – Al-Hadid 57:29
            [28, 58, 1, 66, 12],    // Juz 28: Al-Mujadila 58:1 – At-Tahrim 66:12
            [29, 67, 1, 77, 50],    // Juz 29: Al-Mulk 67:1 – Al-Mursalat 77:50
            [30, 78, 1, 114, 6],    // Juz 30: An-Naba 78:1 – An-Nas 114:6
        ];

        foreach ($mappings as $mapping) {
            [$juzKe, $surahAwal, $ayatAwal, $surahAkhir, $ayatAkhir] = $mapping;

            // Get juz by juz_ke
            $juz = Juz::where('juz_ke', $juzKe)->first();
            if (!$juz) {
                continue;
            }

            // Loop through all surahs in this juz range
            for ($surahNum = $surahAwal; $surahNum <= $surahAkhir; $surahNum++) {
                $surah = Surah::where('nomor_surah', $surahNum)->first();
                if (!$surah) {
                    continue;
                }

                // Determine ayat range for this specific surah within the juz
                if ($surahNum == $surahAwal && $surahNum == $surahAkhir) {
                    // Same surah for start and end
                    $ayatMulai = $ayatAwal;
                    $ayatSelesai = $ayatAkhir;
                } elseif ($surahNum == $surahAwal) {
                    // First surah in juz
                    $ayatMulai = $ayatAwal;
                    $ayatSelesai = $surah->jumlah_ayat;
                } elseif ($surahNum == $surahAkhir) {
                    // Last surah in juz
                    $ayatMulai = 1;
                    $ayatSelesai = $ayatAkhir;
                } else {
                    // Middle surah(s) - complete surah
                    $ayatMulai = 1;
                    $ayatSelesai = $surah->jumlah_ayat;
                }

                // Insert or update pivot record
                DB::table('juz_surah')->updateOrInsert(
                    [
                        'juz_id' => $juz->id,
                        'surah_id' => $surah->id,
                    ],
                    [
                        'ayat_mulai' => $ayatMulai,
                        'ayat_selesai' => $ayatSelesai,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
