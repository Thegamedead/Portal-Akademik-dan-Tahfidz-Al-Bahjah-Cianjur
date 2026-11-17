<?php

namespace Database\Seeders;

use App\Models\ReferensiQuran;
use Illuminate\Database\Seeder;

class JuzSeeder extends Seeder
{
    /**
     * Seed data 30 juz Al-Quran.
     *
     * Kolom: [juz_ke, urutan_hafalan, awal_surah, awal_ayat, akhir_surah, akhir_ayat, jumlah_ayat]
     *
     * urutan_hafalan default = sama dengan juz_ke (urutan standar 1–30).
     * Admin dapat mengubah urutan_hafalan melalui fitur lanjutan.
     *
     * Sumber data: standar pembagian juz Al-Quran (Utsmani/Rasm Madinah).
     */
    public function run(): void
    {
        $data = [
            // [juz_ke, urutan_hafalan, awal_surah, awal_ayat, akhir_surah, akhir_ayat, jumlah_ayat]
            // Data dikoreksi berdasarkan perhitungan akurat dari Mushaf Utsmani
            // Total: 6,236 ayat (sesuai standar Al-Quran)
            [1,  1,   1,   1,   2, 141, 148],  // Al-Fatihah 1:1 – Al-Baqarah 2:141
            [2,  2,   2, 142,   2, 252, 111],  // Al-Baqarah 2:142 – Al-Baqarah 2:252
            [3,  3,   2, 253,   3,  92, 126],  // Al-Baqarah 2:253 – Ali-Imran 3:92
            [4,  4,   3,  93,   4,  23, 131],  // Ali-Imran 3:93 – An-Nisa 4:23 ✅ FIXED
            [5,  5,   4,  24,   4, 147, 124],  // An-Nisa 4:24 – An-Nisa 4:147
            [6,  6,   4, 148,   5,  82, 111],  // An-Nisa 4:148 – Al-Maidah 5:82 ✅ FIXED
            [7,  7,   5,  83,   6, 110, 148],  // Al-Maidah 5:83 – Al-An'am 6:110 ✅ FIXED
            [8,  8,   6, 111,   7,  87, 142],  // Al-An'am 6:111 – Al-A'raf 7:87
            [9,  9,   7,  88,   8,  40, 159],  // Al-A'raf 7:88 – Al-Anfal 8:40
            [10, 10,  8,  41,   9,  92, 127],  // Al-Anfal 8:41 – At-Tawbah 9:92 ✅ FIXED
            [11, 11,  9,  93,  11,   5, 151],  // At-Tawbah 9:93 – Hud 11:5 ✅ FIXED
            [12, 12, 11,   6,  12,  52, 170],  // Hud 11:6 – Yusuf 12:52
            [13, 13, 12,  53,  14,  52, 154],  // Yusuf 12:53 – Ibrahim 14:52
            [14, 14, 15,   1,  16, 128, 227],  // Al-Hijr 15:1 – An-Nahl 16:128
            [15, 15, 17,   1,  18,  74, 185],  // Al-Isra 17:1 – Al-Kahf 18:74
            [16, 16, 18,  75,  20, 135, 269],  // Al-Kahf 18:75 – Ta-Ha 20:135 ✅ FIXED
            [17, 17, 21,   1,  22,  78, 190],  // Al-Anbiya 21:1 – Al-Hajj 22:78 ✅ FIXED
            [18, 18, 23,   1,  25,  20, 202],  // Al-Muminun 23:1 – Al-Furqan 25:20
            [19, 19, 25,  21,  27,  55, 339],  // Al-Furqan 25:21 – An-Naml 27:55 ✅ FIXED
            [20, 20, 27,  56,  29,  45, 171],  // An-Naml 27:56 – Al-Ankabut 29:45 ✅ FIXED
            [21, 21, 29,  46,  33,  30, 178],  // Al-Ankabut 29:46 – Al-Ahzab 33:30 ✅ FIXED
            [22, 22, 33,  31,  36,  27, 169],  // Al-Ahzab 33:31 – Ya-Sin 36:27 ✅ FIXED
            [23, 23, 36,  28,  39,  31, 357],  // Ya-Sin 36:28 – Az-Zumar 39:31
            [24, 24, 39,  32,  41,  46, 175],  // Az-Zumar 39:32 – Fussilat 41:46 ✅ FIXED
            [25, 25, 41,  47,  45,  37, 246],  // Fussilat 41:47 – Al-Jathiyah 45:37
            [26, 26, 46,   1,  51,  30, 195],  // Al-Ahqaf 46:1 – Adh-Dhariyat 51:30 ✅ FIXED
            [27, 27, 51,  31,  57,  29, 399],  // Adh-Dhariyat 51:31 – Al-Hadid 57:29 ✅ FIXED
            [28, 28, 58,   1,  66,  12, 137],  // Al-Mujadila 58:1 – At-Tahrim 66:12 ✅ FIXED
            [29, 29, 67,   1,  77,  50, 431],  // Al-Mulk 67:1 – Al-Mursalat 77:50
            [30, 30, 78,   1, 114,   6, 564],  // An-Naba 78:1 – An-Nas 114:6
        ];

        foreach ($data as $row) {
            ReferensiQuran::updateOrCreate(
                ['juz_ke' => $row[0]],
                [
                    'urutan_hafalan' => $row[1],
                    'awal_surah'     => $row[2],
                    'awal_ayat'      => $row[3],
                    'akhir_surah'    => $row[4],
                    'akhir_ayat'     => $row[5],
                    'jumlah_ayat'    => $row[6],
                ]
            );
        }
    }
}
