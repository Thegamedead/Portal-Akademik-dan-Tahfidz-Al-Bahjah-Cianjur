<?php

namespace Database\Seeders;

use App\Models\Juz;
use App\Models\Surah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SurahsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Data: [nomor_surah, nama_latin, nama_arab, jumlah_ayat, tempat_turun, urutan_mushaf]
        $surahs = [
            [1,  'Al-Fatihah',      'الفاتحة',   7,   'Makkiyah',  1],
            [2,  'Al-Baqarah',      'البقرة',    286,  'Madaniyah', 2],
            [3,  'Ali Imran',       'آل عمران',  200,  'Madaniyah', 3],
            [4,  'An-Nisa',        'النساء',    176,  'Madaniyah', 4],
            [5,  'Al-Maidah',      'المائدة',   120,  'Madaniyah', 5],
            [6,  'Al-Anam',        'الأنعام',   165,  'Makkiyah',  6],
            [7,  'Al-Araf',        'الأعراف',   206,  'Makkiyah',  7],
            [8,  'Al-Anfal',       'الأنفال',   75,   'Madaniyah', 8],
            [9,  'At-Tawbah',      'التوبة',    129,  'Madaniyah', 9],
            [10, 'Yunus',          'يونس',      109,  'Makkiyah',  10],
            [11, 'Hud',            'هود',       123,  'Makkiyah',  11],
            [12, 'Yusuf',          'يوسف',      111,  'Makkiyah',  12],
            [13, 'Ar-Rad',        'الرعد',     43,   'Madaniyah', 13],
            [14, 'Ibrahim',        'إبراهيم',   52,   'Makkiyah',  14],
            [15, 'Al-Hijr',        'الحجر',     99,   'Makkiyah',  15],
            [16, 'An-Nahl',        'النحل',     128,  'Makkiyah',  16],
            [17, 'Al-Isra',        'الإسراء',   111,  'Makkiyah',  17],
            [18, 'Al-Kahf',        'الكهف',     110,  'Makkiyah',  18],
            [19, 'Maryam',         'مريم',      98,   'Makkiyah',  19],
            [20, 'Ta-Ha',          'طه',        135,  'Makkiyah',  20],
            [21, 'Al-Anbiya',      'الأنبياء',  112,  'Makkiyah',  21],
            [22, 'Al-Hajj',        'الحج',      78,   'Madaniyah', 22],
            [23, 'Al-Muminun',     'المؤمنون',  118,  'Makkiyah',  23],
            [24, 'An-Nur',         'النور',     64,   'Madaniyah', 24],
            [25, 'Al-Furqan',      'الفرقان',   77,   'Makkiyah',  25],
            [26, 'Asy-Syuara',     'الشعراء',   227,  'Makkiyah',  26],
            [27, 'An-Naml',        'النمل',     93,   'Makkiyah',  27],
            [28, 'Al-Qasas',       'القصص',     88,   'Makkiyah',  28],
            [29, 'Al-Ankabut',     'العنكبوت',  69,   'Makkiyah',  29],
            [30, 'Ar-Rum',         'الروم',     60,   'Makkiyah',  30],
            [31, 'Luqman',         'لقمان',     34,   'Makkiyah',  31],
            [32, 'As-Sajdah',      'السجدة',    30,   'Makkiyah',  32],
            [33, 'Al-Ahzab',       'الأحزاب',   73,   'Madaniyah', 33],
            [34, 'Saba',           'سبأ',       54,   'Makkiyah',  34],
            [35, 'Fatir',          'فاطر',      45,   'Makkiyah',  35],
            [36, 'Ya-Sin',         'يس',        83,   'Makkiyah',  36],
            [37, 'As-Saffat',      'الصافات',   182,  'Makkiyah',  37],
            [38, 'Sad',            'ص',         88,   'Makkiyah',  38],
            [39, 'Az-Zumar',       'الزمر',     75,   'Makkiyah',  39],
            [40, 'Gafir',          'غافر',      85,   'Makkiyah',  40],
            [41, 'Fussilat',       'فصلت',      54,   'Makkiyah',  41],
            [42, 'Asy-Syura',      'الشورى',    53,   'Makkiyah',  42],
            [43, 'Az-Zukhruf',     'الزخرف',    89,   'Makkiyah',  43],
            [44, 'Ad-Dukhan',      'الدخان',    59,   'Makkiyah',  44],
            [45, 'Al-Jasiyah',     'الجاثية',   37,   'Makkiyah',  45],
            [46, 'Al-Ahqaf',       'الأحقاف',   35,   'Makkiyah',  46],
            [47, 'Muhammad',       'محمد',      38,   'Madaniyah', 47],
            [48, 'Al-Fath',        'الفتح',     29,   'Madaniyah', 48],
            [49, 'Al-Hujurat',     'الحجرات',   18,   'Madaniyah', 49],
            [50, 'Qaf',            'ق',         45,   'Makkiyah',  50],
            [51, 'Az-Zariyat',     'الذاريات',  60,   'Makkiyah',  51],
            [52, 'At-Tur',         'الطور',     49,   'Makkiyah',  52],
            [53, 'An-Najm',        'النجم',     62,   'Makkiyah',  53],
            [54, 'Al-Qamar',       'القمر',     55,   'Makkiyah',  54],
            [55, 'Ar-Rahman',      'الرحمن',    78,   'Madaniyah', 55],
            [56, 'Al-Waqiah',      'الواقعة',   96,   'Makkiyah',  56],
            [57, 'Al-Hadid',       'الحديد',    29,   'Madaniyah', 57],
            [58, 'Al-Mujadilah',   'المجادلة',  22,   'Madaniyah', 58],
            [59, 'Al-Hasyr',       'الحشر',     24,   'Madaniyah', 59],
            [60, 'Al-Mumtahanah',  'الممتحنة',  13,   'Madaniyah', 60],
            [61, 'As-Saf',         'الصف',      14,   'Madaniyah', 61],
            [62, 'Al-Jumuah',      'الجمعة',    11,   'Madaniyah', 62],
            [63, 'Al-Munafiqun',   'المنافقون', 11,   'Madaniyah', 63],
            [64, 'At-Tagabun',     'التغابن',   18,   'Madaniyah', 64],
            [65, 'At-Talaq',       'الطلاق',    12,   'Madaniyah', 65],
            [66, 'At-Tahrim',      'التحريم',   12,   'Madaniyah', 66],
            [67, 'Al-Mulk',        'الملك',     30,   'Makkiyah',  67],
            [68, 'Al-Qalam',       'القلم',     52,   'Makkiyah',  68],
            [69, 'Al-Haqqah',      'الحاقة',    52,   'Makkiyah',  69],
            [70, 'Al-Maarij',      'المعارج',   44,   'Makkiyah',  70],
            [71, 'Nuh',            'نوح',       28,   'Makkiyah',  71],
            [72, 'Al-Jinn',        'الجن',      28,   'Makkiyah',  72],
            [73, 'Al-Muzzammil',   'المزمل',    20,   'Makkiyah',  73],
            [74, 'Al-Muddassir',   'المدثر',    56,   'Makkiyah',  74],
            [75, 'Al-Qiyamah',     'القيامة',   40,   'Makkiyah',  75],
            [76, 'Al-Insan',       'الإنسان',   31,   'Madaniyah', 76],
            [77, 'Al-Mursalat',    'المرسلات',  50,   'Makkiyah',  77],
            [78, 'An-Naba',        'النبأ',     40,   'Makkiyah',  78],
            [79, 'An-Naziat',      'النازعات',  46,   'Makkiyah',  79],
            [80, 'Abasa',          'عبس',       42,   'Makkiyah',  80],
            [81, 'At-Takwir',      'التكوير',   29,   'Makkiyah',  81],
            [82, 'Al-Infitar',     'الانفطار',  19,   'Makkiyah',  82],
            [83, 'Al-Mutaffifin',  'المطففين',  36,   'Makkiyah',  83],
            [84, 'Al-Insyiqaq',    'الانشقاق',  25,   'Makkiyah',  84],
            [85, 'Al-Buruj',       'البروج',    22,   'Makkiyah',  85],
            [86, 'At-Tariq',       'الطارق',    17,   'Makkiyah',  86],
            [87, 'Al-Ala',         'الأعلى',    19,   'Makkiyah',  87],
            [88, 'Al-Gasyiyah',    'الغاشية',   26,   'Makkiyah',  88],
            [89, 'Al-Fajr',        'الفجر',     30,   'Makkiyah',  89],
            [90, 'Al-Balad',       'البلد',     20,   'Makkiyah',  90],
            [91, 'Asy-Syams',      'الشمس',     15,   'Makkiyah',  91],
            [92, 'Al-Lail',        'الليل',     21,   'Makkiyah',  92],
            [93, 'Ad-Duha',        'الضحى',     11,   'Makkiyah',  93],
            [94, 'Al-Insyirah',    'الشرح',     8,    'Makkiyah',  94],
            [95, 'At-Tin',         'التين',     8,    'Makkiyah',  95],
            [96, 'Al-Alaq',        'العلق',     19,   'Makkiyah',  96],
            [97, 'Al-Qadr',        'القدر',     5,    'Makkiyah',  97],
            [98, 'Al-Bayyinah',    'البينة',    8,    'Madaniyah', 98],
            [99, 'Az-Zalzalah',    'الزلزلة',   8,    'Madaniyah', 99],
            [100,'Al-Adiyat',      'العاديات',  11,   'Makkiyah',  100],
            [101,'Al-Qariah',      'القارعة',   11,   'Makkiyah',  101],
            [102,'At-Takasur',     'التكاثر',   8,    'Makkiyah',  102],
            [103,'Al-Asr',         'العصر',     3,    'Makkiyah',  103],
            [104,'Al-Humazah',     'الهمزة',    9,    'Makkiyah',  104],
            [105,'Al-Fil',         'الفيل',     5,    'Makkiyah',  105],
            [106,'Quraisy',        'قريش',      4,    'Makkiyah',  106],
            [107,'Al-Maun',        'الماعون',   7,    'Makkiyah',  107],
            [108,'Al-Kausar',      'الكوثر',    3,    'Makkiyah',  108],
            [109,'Al-Kafirun',     'الكافرون',  6,    'Makkiyah',  109],
            [110,'An-Nasr',        'النصر',     3,    'Madaniyah', 110],
            [111,'Al-Masad',       'المسد',     5,    'Makkiyah',  111],
            [112,'Al-Ikhlas',      'الإخلاص',   4,    'Makkiyah',  112],
            [113,'Al-Falaq',       'الفلق',     5,    'Makkiyah',  113],
            [114,'An-Nas',         'الناس',     6,    'Makkiyah',  114],
        ];

        foreach ($surahs as $row) {
            Surah::updateOrCreate(
                ['nomor_surah' => $row[0]],
                [
                    'nama_latin'    => $row[1],
                    'nama_arab'     => $row[2],
                    'jumlah_ayat'   => $row[3],
                    'tempat_turun'  => $row[4],
                    'urutan_mushaf' => $row[5],
                ]
            );
        }

        // Now seed the juz_surah pivot table
        // Each entry: [juz_ke, nomor_surah, ayat_mulai, ayat_selesai]
        $juzSurahData = [
            // Juz 1: Al-Fatihah 1:1-7, Al-Baqarah 2:1-141
            [1, 1,   1,   7],
            [1, 2,   1, 141],
            // Juz 2: Al-Baqarah 2:142-252
            [2, 2, 142, 252],
            // Juz 3: Al-Baqarah 2:253-286, Ali Imran 3:1-92
            [3, 2, 253, 286],
            [3, 3,   1,  92],
            // Juz 4: Ali Imran 3:93-200, An-Nisa 4:1-23
            [4, 3,  93, 200],
            [4, 4,   1,  23],
            // Juz 5: An-Nisa 4:24-147
            [5, 4,  24, 147],
            // Juz 6: An-Nisa 4:148-176, Al-Maidah 5:1-81
            [6, 4, 148, 176],
            [6, 5,   1,  81],
            // Juz 7: Al-Maidah 5:82-120, Al-Anam 6:1-110
            [7, 5,  82, 120],
            [7, 6,   1, 110],
            // Juz 8: Al-Anam 6:111-165, Al-Araf 7:1-87
            [8, 6, 111, 165],
            [8, 7,   1,  87],
            // Juz 9: Al-Araf 7:88-206, Al-Anfal 8:1-40
            [9, 7,  88, 206],
            [9, 8,   1,  40],
            // Juz 10: Al-Anfal 8:41-75, At-Tawbah 9:1-92
            [10, 8,  41,  75],
            [10, 9,   1,  92],
            // Juz 11: At-Tawbah 9:93-129, Yunus 10:1-109, Hud 11:1-5
            [11, 9,  93, 129],
            [11, 10,  1, 109],
            [11, 11,  1,   5],
            // Juz 12: Hud 11:6-123, Yusuf 12:1-52
            [12, 11,  6, 123],
            [12, 12,  1,  52],
            // Juz 13: Yusuf 12:53-111, Ar-Rad 13:1-43, Ibrahim 14:1-52
            [13, 12, 53, 111],
            [13, 13,  1,  43],
            [13, 14,  1,  52],
            // Juz 14: Al-Hijr 15:1-99, An-Nahl 16:1-128
            [14, 15,  1,  99],
            [14, 16,  1, 128],
            // Juz 15: Al-Isra 17:1-111, Al-Kahf 18:1-74
            [15, 17,  1, 111],
            [15, 18,  1,  74],
            // Juz 16: Al-Kahf 18:75-110, Maryam 19:1-98, Ta-Ha 20:1-135
            [16, 18, 75, 110],
            [16, 19,  1,  98],
            [16, 20,  1, 135],
            // Juz 17: Al-Anbiya 21:1-112, Al-Hajj 22:1-78
            [17, 21,  1, 112],
            [17, 22,  1,  78],
            // Juz 18: Al-Muminun 23:1-118, An-Nur 24:1-64, Al-Furqan 25:1-20
            [18, 23,  1, 118],
            [18, 24,  1,  64],
            [18, 25,  1,  20],
            // Juz 19: Al-Furqan 25:21-77, Asy-Syuara 26:1-227, An-Naml 27:1-55
            [19, 25, 21,  77],
            [19, 26,  1, 227],
            [19, 27,  1,  55],
            // Juz 20: An-Naml 27:56-93, Al-Qasas 28:1-88, Al-Ankabut 29:1-45
            [20, 27, 56,  93],
            [20, 28,  1,  88],
            [20, 29,  1,  45],
            // Juz 21: Al-Ankabut 29:46-69, Ar-Rum 30:1-60, Luqman 31:1-34, As-Sajdah 32:1-30, Al-Ahzab 33:1-30
            [21, 29, 46,  69],
            [21, 30,  1,  60],
            [21, 31,  1,  34],
            [21, 32,  1,  30],
            [21, 33,  1,  30],
            // Juz 22: Al-Ahzab 33:31-73, Saba 34:1-54, Fatir 35:1-45, Ya-Sin 36:1-27
            [22, 33, 31,  73],
            [22, 34,  1,  54],
            [22, 35,  1,  45],
            [22, 36,  1,  27],
            // Juz 23: Ya-Sin 36:28-83, As-Saffat 37:1-182, Sad 38:1-88, Az-Zumar 39:1-31
            [23, 36, 28,  83],
            [23, 37,  1, 182],
            [23, 38,  1,  88],
            [23, 39,  1,  31],
            // Juz 24: Az-Zumar 39:32-75, Gafir 40:1-85, Fussilat 41:1-46
            [24, 39, 32,  75],
            [24, 40,  1,  85],
            [24, 41,  1,  46],
            // Juz 25: Fussilat 41:47-54, Asy-Syura 42:1-53, Az-Zukhruf 43:1-89, Ad-Dukhan 44:1-59, Al-Jasiyah 45:1-37
            [25, 41, 47,  54],
            [25, 42,  1,  53],
            [25, 43,  1,  89],
            [25, 44,  1,  59],
            [25, 45,  1,  37],
            // Juz 26: Al-Ahqaf 46:1-35, Muhammad 47:1-38, Al-Fath 48:1-29, Al-Hujurat 49:1-18, Qaf 50:1-45, Az-Zariyat 51:1-30
            [26, 46,  1,  35],
            [26, 47,  1,  38],
            [26, 48,  1,  29],
            [26, 49,  1,  18],
            [26, 50,  1,  45],
            [26, 51,  1,  30],
            // Juz 27: Az-Zariyat 51:31-60, At-Tur 52:1-49, An-Najm 53:1-62, Al-Qamar 54:1-55, Ar-Rahman 55:1-78, Al-Waqiah 56:1-96, Al-Hadid 57:1-29
            [27, 51, 31,  60],
            [27, 52,  1,  49],
            [27, 53,  1,  62],
            [27, 54,  1,  55],
            [27, 55,  1,  78],
            [27, 56,  1,  96],
            [27, 57,  1,  29],
            // Juz 28: Al-Mujadilah 58, Al-Hasyr 59, Al-Mumtahanah 60, As-Saf 61, Al-Jumuah 62, Al-Munafiqun 63, At-Tagabun 64, At-Talaq 65, At-Tahrim 66
            [28, 58,  1,  22],
            [28, 59,  1,  24],
            [28, 60,  1,  13],
            [28, 61,  1,  14],
            [28, 62,  1,  11],
            [28, 63,  1,  11],
            [28, 64,  1,  18],
            [28, 65,  1,  12],
            [28, 66,  1,  12],
            // Juz 29: Al-Mulk 67 - Al-Mursalat 77
            [29, 67,  1,  30],
            [29, 68,  1,  52],
            [29, 69,  1,  52],
            [29, 70,  1,  44],
            [29, 71,  1,  28],
            [29, 72,  1,  28],
            [29, 73,  1,  20],
            [29, 74,  1,  56],
            [29, 75,  1,  40],
            [29, 76,  1,  31],
            [29, 77,  1,  50],
            // Juz 30: An-Naba 78 - An-Nas 114
            [30, 78,  1,  40],
            [30, 79,  1,  46],
            [30, 80,  1,  42],
            [30, 81,  1,  29],
            [30, 82,  1,  19],
            [30, 83,  1,  36],
            [30, 84,  1,  25],
            [30, 85,  1,  22],
            [30, 86,  1,  17],
            [30, 87,  1,  19],
            [30, 88,  1,  26],
            [30, 89,  1,  30],
            [30, 90,  1,  20],
            [30, 91,  1,  15],
            [30, 92,  1,  21],
            [30, 93,  1,  11],
            [30, 94,  1,   8],
            [30, 95,  1,   8],
            [30, 96,  1,  19],
            [30, 97,  1,   5],
            [30, 98,  1,   8],
            [30, 99,  1,   8],
            [30, 100, 1,  11],
            [30, 101, 1,  11],
            [30, 102, 1,   8],
            [30, 103, 1,   3],
            [30, 104, 1,   9],
            [30, 105, 1,   5],
            [30, 106, 1,   4],
            [30, 107, 1,   7],
            [30, 108, 1,   3],
            [30, 109, 1,   6],
            [30, 110, 1,   3],
            [30, 111, 1,   5],
            [30, 112, 1,   4],
            [30, 113, 1,   5],
            [30, 114, 1,   6],
        ];

        foreach ($juzSurahData as $row) {
            $juz   = Juz::where('juz_ke', $row[0])->first();
            $surah = Surah::where('nomor_surah', $row[1])->first();

            if ($juz && $surah) {
                DB::table('juz_surah')->updateOrInsert(
                    ['juz_id' => $juz->id, 'surah_id' => $surah->id],
                    ['ayat_mulai' => $row[2], 'ayat_selesai' => $row[3], 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
