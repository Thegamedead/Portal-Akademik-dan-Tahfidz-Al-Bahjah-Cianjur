<?php

namespace Database\Seeders;

use App\Models\Surah;
use Illuminate\Database\Seeder;

class SurahSeeder extends Seeder
{
    /**
     * Seed data 114 surah Al-Quran.
     *
     * Format: [nomor_surah, nama_latin, nama_arab, jumlah_ayat, tempat_turun, urutan_mushaf]
     *
     * Sumber data: standar Al-Quran Utsmani
     */
    public function run(): void
    {
        $data = [
            // [nomor, nama_latin, nama_arab, jumlah_ayat, tempat_turun, urutan_mushaf]
            [1, 'Al-Fatihah', 'الفاتحة', 7, 'Makkiyah', 1],
            [2, 'Al-Baqarah', 'البقرة', 286, 'Madaniyah', 2],
            [3, 'Ali-Imran', 'آل عمران', 200, 'Madaniyah', 3],
            [4, 'An-Nisa', 'النساء', 176, 'Madaniyah', 4],
            [5, 'Al-Maidah', 'المائدة', 120, 'Madaniyah', 5],
            [6, 'Al-An\'am', 'الأنعام', 165, 'Makkiyah', 6],
            [7, 'Al-A\'raf', 'الأعراف', 206, 'Makkiyah', 7],
            [8, 'Al-Anfal', 'الأنفال', 75, 'Madaniyah', 8],
            [9, 'At-Tawbah', 'التوبة', 129, 'Madaniyah', 9],
            [10, 'Yunus', 'يونس', 109, 'Makkiyah', 10],
            [11, 'Hud', 'هود', 123, 'Makkiyah', 11],
            [12, 'Yusuf', 'يوسف', 111, 'Makkiyah', 12],
            [13, 'Ar-Ra\'d', 'الرعد', 43, 'Madaniyah', 13],
            [14, 'Ibrahim', 'إبراهيم', 52, 'Makkiyah', 14],
            [15, 'Al-Hijr', 'الحجر', 99, 'Makkiyah', 15],
            [16, 'An-Nahl', 'النحل', 128, 'Makkiyah', 16],
            [17, 'Al-Isra', 'الإسراء', 111, 'Makkiyah', 17],
            [18, 'Al-Kahf', 'الكهف', 110, 'Makkiyah', 18],
            [19, 'Maryam', 'مريم', 98, 'Makkiyah', 19],
            [20, 'Ta-Ha', 'طه', 135, 'Makkiyah', 20],
            [21, 'Al-Anbiya', 'الأنبياء', 112, 'Makkiyah', 21],
            [22, 'Al-Hajj', 'الحج', 78, 'Madaniyah', 22],
            [23, 'Al-Muminun', 'المؤمنون', 118, 'Makkiyah', 23],
            [24, 'An-Nur', 'النور', 64, 'Madaniyah', 24],
            [25, 'Al-Furqan', 'الفرقان', 77, 'Makkiyah', 25],
            [26, 'Asy-Syu\'ara', 'الشعراء', 227, 'Makkiyah', 26],
            [27, 'An-Naml', 'النمل', 93, 'Makkiyah', 27],
            [28, 'Al-Qasas', 'القصص', 88, 'Makkiyah', 28],
            [29, 'Al-Ankabut', 'العنكبوت', 69, 'Makkiyah', 29],
            [30, 'Ar-Rum', 'الروم', 60, 'Makkiyah', 30],
            [31, 'Luqman', 'لقمان', 34, 'Makkiyah', 31],
            [32, 'As-Sajdah', 'السجدة', 30, 'Makkiyah', 32],
            [33, 'Al-Ahzab', 'الأحزاب', 73, 'Madaniyah', 33],
            [34, 'Saba', 'سبأ', 54, 'Makkiyah', 34],
            [35, 'Fatir', 'فاطر', 45, 'Makkiyah', 35],
            [36, 'Ya-Sin', 'يس', 83, 'Makkiyah', 36],
            [37, 'As-Saffat', 'الصافات', 182, 'Makkiyah', 37],
            [38, 'Sad', 'ص', 88, 'Makkiyah', 38],
            [39, 'Az-Zumar', 'الزمر', 75, 'Makkiyah', 39],
            [40, 'Ghafir', 'غافر', 85, 'Makkiyah', 40],
            [41, 'Fussilat', 'فصلت', 54, 'Makkiyah', 41],
            [42, 'Asy-Syura', 'الشورى', 53, 'Makkiyah', 42],
            [43, 'Az-Zukhruf', 'الزخرف', 89, 'Makkiyah', 43],
            [44, 'Ad-Dukhan', 'الدخان', 59, 'Makkiyah', 44],
            [45, 'Al-Jatsiyah', 'الجاثية', 37, 'Makkiyah', 45],
            [46, 'Al-Ahqaf', 'الأحقاف', 35, 'Makkiyah', 46],
            [47, 'Muhammad', 'محمد', 38, 'Madaniyah', 47],
            [48, 'Al-Fath', 'الفتح', 29, 'Madaniyah', 48],
            [49, 'Al-Hujurat', 'الحجرات', 18, 'Madaniyah', 49],
            [50, 'Qaf', 'ق', 45, 'Makkiyah', 50],
            [51, 'Adz-Dzariyat', 'الذاريات', 60, 'Makkiyah', 51],
            [52, 'At-Tur', 'الطور', 49, 'Makkiyah', 52],
            [53, 'An-Najm', 'النجم', 62, 'Makkiyah', 53],
            [54, 'Al-Qamar', 'القمر', 55, 'Makkiyah', 54],
            [55, 'Ar-Rahman', 'الرحمن', 78, 'Madaniyah', 55],
            [56, 'Al-Waqi\'ah', 'الواقعة', 96, 'Makkiyah', 56],
            [57, 'Al-Hadid', 'الحديد', 29, 'Madaniyah', 57],
            [58, 'Al-Mujadalah', 'المجادلة', 22, 'Madaniyah', 58],
            [59, 'Al-Hasyr', 'الحشر', 24, 'Madaniyah', 59],
            [60, 'Al-Mumtahanah', 'الممتحنة', 13, 'Madaniyah', 60],
            [61, 'As-Saff', 'الصف', 14, 'Madaniyah', 61],
            [62, 'Al-Jumu\'ah', 'الجمعة', 11, 'Madaniyah', 62],
            [63, 'Al-Munafiqun', 'المنافقون', 11, 'Madaniyah', 63],
            [64, 'At-Taghabun', 'التغابن', 18, 'Madaniyah', 64],
            [65, 'At-Talaq', 'الطلاق', 12, 'Madaniyah', 65],
            [66, 'At-Tahrim', 'التحريم', 12, 'Madaniyah', 66],
            [67, 'Al-Mulk', 'الملك', 30, 'Makkiyah', 67],
            [68, 'Al-Qalam', 'القلم', 52, 'Makkiyah', 68],
            [69, 'Al-Haqqah', 'الحاقة', 52, 'Makkiyah', 69],
            [70, 'Al-Ma\'arij', 'المعارج', 44, 'Makkiyah', 70],
            [71, 'Nuh', 'نوح', 28, 'Makkiyah', 71],
            [72, 'Al-Jinn', 'الجن', 28, 'Makkiyah', 72],
            [73, 'Al-Muzzammil', 'المزمل', 20, 'Makkiyah', 73],
            [74, 'Al-Muddatstsir', 'المدثر', 56, 'Makkiyah', 74],
            [75, 'Al-Qiyamah', 'القيامة', 40, 'Makkiyah', 75],
            [76, 'Al-Insan', 'الإنسان', 31, 'Madaniyah', 76],
            [77, 'Al-Mursalat', 'المرسلات', 50, 'Makkiyah', 77],
            [78, 'An-Naba', 'النبأ', 40, 'Makkiyah', 78],
            [79, 'An-Nazi\'at', 'النازعات', 46, 'Makkiyah', 79],
            [80, 'Abasa', 'عبس', 42, 'Makkiyah', 80],
            [81, 'At-Takwir', 'التكوير', 29, 'Makkiyah', 81],
            [82, 'Al-Infitar', 'الإنفطار', 19, 'Makkiyah', 82],
            [83, 'Al-Mutaffifin', 'المطففين', 36, 'Makkiyah', 83],
            [84, 'Al-Insyiqaq', 'الإنشقاق', 25, 'Makkiyah', 84],
            [85, 'Al-Buruj', 'البروج', 22, 'Makkiyah', 85],
            [86, 'At-Tariq', 'الطارق', 17, 'Makkiyah', 86],
            [87, 'Al-A\'la', 'الأعلى', 19, 'Makkiyah', 87],
            [88, 'Al-Gasyiyah', 'الغاشية', 26, 'Makkiyah', 88],
            [89, 'Al-Fajr', 'الفجر', 30, 'Makkiyah', 89],
            [90, 'Al-Balad', 'البلد', 20, 'Makkiyah', 90],
            [91, 'Asy-Syams', 'الشمس', 15, 'Makkiyah', 91],
            [92, 'Al-Lail', 'الليل', 21, 'Makkiyah', 92],
            [93, 'Ad-Duha', 'الضحى', 11, 'Makkiyah', 93],
            [94, 'Asy-Syarh', 'الشرح', 8, 'Makkiyah', 94],
            [95, 'At-Tin', 'التين', 8, 'Makkiyah', 95],
            [96, 'Al-Alaq', 'العلق', 19, 'Makkiyah', 96],
            [97, 'Al-Qadr', 'القدر', 5, 'Makkiyah', 97],
            [98, 'Al-Bayyinah', 'البينة', 8, 'Madaniyah', 98],
            [99, 'Az-Zalzalah', 'الزلزلة', 8, 'Madaniyah', 99],
            [100, 'Al-Adiyat', 'العاديات', 11, 'Makkiyah', 100],
            [101, 'Al-Qari\'ah', 'القارعة', 11, 'Makkiyah', 101],
            [102, 'At-Takatsur', 'التكاثر', 8, 'Makkiyah', 102],
            [103, 'Al-Asr', 'العصر', 3, 'Makkiyah', 103],
            [104, 'Al-Humazah', 'الهمزة', 9, 'Makkiyah', 104],
            [105, 'Al-Fil', 'الفيل', 5, 'Makkiyah', 105],
            [106, 'Quraisy', 'قريش', 4, 'Makkiyah', 106],
            [107, 'Al-Ma\'un', 'الماعون', 7, 'Makkiyah', 107],
            [108, 'Al-Kautsar', 'الكوثر', 3, 'Makkiyah', 108],
            [109, 'Al-Kafirun', 'الكافرون', 6, 'Makkiyah', 109],
            [110, 'An-Nasr', 'النصر', 3, 'Madaniyah', 110],
            [111, 'Al-Lahab', 'المسد', 5, 'Makkiyah', 111],
            [112, 'Al-Ikhlas', 'الإخلاص', 4, 'Makkiyah', 112],
            [113, 'Al-Falaq', 'الفلق', 5, 'Makkiyah', 113],
            [114, 'An-Nas', 'الناس', 6, 'Makkiyah', 114],
        ];

        foreach ($data as $row) {
            Surah::updateOrCreate(
                ['nomor_surah' => $row[0]],
                [
                    'nama_latin' => $row[1],
                    'nama_arab' => $row[2],
                    'jumlah_ayat' => $row[3],
                    'tempat_turun' => $row[4],
                    'urutan_mushaf' => $row[5],
                ]
            );
        }
    }
}
