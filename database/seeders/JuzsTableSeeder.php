<?php

namespace Database\Seeders;

use App\Models\Juz;
use Illuminate\Database\Seeder;

class JuzsTableSeeder extends Seeder
{
    /**
     * Seed data 30 juz ke tabel 'juzs'.
     * Kolom: juz_ke, urutan_hafalan, jumlah_ayat, awal_surah, awal_ayat, akhir_surah, akhir_ayat
     */
    public function run(): void
    {
        $data = [
            // [juz_ke, jumlah_ayat, awal_surah, awal_ayat, akhir_surah, akhir_ayat]
            // Data dikoreksi berdasarkan perhitungan akurat dari Mushaf Utsmani
            // Total: 6,236 ayat (sesuai standar Al-Quran)
            [ 1, 148,   1,   1,   2, 141],
            [ 2, 111,   2, 142,   2, 252],
            [ 3, 126,   2, 253,   3,  92],
            [ 4, 131,   3,  93,   4,  23],
            [ 5, 124,   4,  24,   4, 147],
            [ 6, 111,   4, 148,   5,  82],
            [ 7, 148,   5,  83,   6, 110],
            [ 8, 142,   6, 111,   7,  87],
            [ 9, 159,   7,  88,   8,  40],
            [10, 127,   8,  41,   9,  92],
            [11, 151,   9,  93,  11,   5],
            [12, 170,  11,   6,  12,  52],
            [13, 154,  12,  53,  14,  52],
            [14, 227,  15,   1,  16, 128],
            [15, 185,  17,   1,  18,  74],
            [16, 269,  18,  75,  20, 135],
            [17, 190,  21,   1,  22,  78],
            [18, 202,  23,   1,  25,  20],
            [19, 339,  25,  21,  27,  55],
            [20, 171,  27,  56,  29,  45],
            [21, 178,  29,  46,  33,  30],
            [22, 169,  33,  31,  36,  27],
            [23, 357,  36,  28,  39,  31],
            [24, 175,  39,  32,  41,  46],
            [25, 246,  41,  47,  45,  37],
            [26, 195,  46,   1,  51,  30],
            [27, 399,  51,  31,  57,  29],
            [28, 137,  58,   1,  66,  12],
            [29, 431,  67,   1,  77,  50],
            [30, 564,  78,   1, 114,   6],
        ];

        foreach ($data as $row) {
            Juz::updateOrCreate(
                ['juz_ke' => $row[0]],
                [
                    'urutan_hafalan' => $row[0],
                    'jumlah_ayat'   => $row[1],
                    'awal_surah'    => $row[2],
                    'awal_ayat'     => $row[3],
                    'akhir_surah'   => $row[4],
                    'akhir_ayat'    => $row[5],
                ]
            );
        }
    }
}
