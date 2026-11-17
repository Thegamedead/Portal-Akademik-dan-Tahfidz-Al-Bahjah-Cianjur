<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Absensi, Setoran, Juz, Surah};
use App\Enums\{JenisSetoran, SkorKomponen, Keputusan, StatusKehadiran};

/**
 * Seeder untuk sample data setoran (demo/testing purposes)
 *
 * Generates realistic setoran data dengan:
 * - Multi-entry per absensi (1-3 entries)
 * - Berbagai kombinasi predikat (Lancar, Kurang Lancar, Tidak Lancar)
 * - Keputusan (Lanjut/Ulang) independen dari predikat
 * - Saran ayat berikutnya yang logis
 *
 * Idempotent: dapat dijalankan ulang (clear existing setorans untuk absensi yang di-seed)
 */
class SetoranDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get absensi dengan status Hadir dari 7 hari terakhir
        $absensis = Absensi::with(['siswa', 'kelompok'])
            ->where('status_kehadiran', StatusKehadiran::Hadir)
            ->whereDate('tanggal', '>=', now()->subDays(6))
            ->get();

        if ($absensis->isEmpty()) {
            $this->command->warn('Tidak ada absensi dengan status Hadir ditemukan. Jalankan AbsensiDemoSeeder terlebih dahulu.');
            return;
        }

        // Get juzs dan surahs untuk generate setoran realistis
        $juzs = Juz::with('surahs')->get();
        if ($juzs->isEmpty()) {
            $this->command->warn('Tidak ada data juz ditemukan. Jalankan JuzSeeder terlebih dahulu.');
            return;
        }

        $totalSetoran = 0;

        foreach ($absensis as $absensi) {
            // Clear existing setorans untuk absensi ini (idempotent)
            Setoran::where('absensi_id', $absensi->id)->delete();

            // Random number of entries (1-3)
            $numEntries = rand(1, 3);

            // Start from random juz and surah
            $currentJuz = $juzs->random();
            $currentSurah = $currentJuz->surahs->random();
            $currentAyat = rand(1, 10); // Start from early ayat

            for ($i = 0; $i < $numEntries; $i++) {
                // Generate random skor components
                $skorTajwid = $this->randomSkor();
                $skorKelancaran = $this->randomSkor();
                $skorMakhraj = $this->randomSkor();
                $skorFashahah = $this->randomSkor();

                // Calculate predikat (min of 4 components)
                $minSkor = min([
                    $skorTajwid->value(),
                    $skorKelancaran->value(),
                    $skorMakhraj->value(),
                    $skorFashahah->value(),
                ]);
                $predikat = SkorKomponen::fromMinValue($minSkor);

                // Keputusan independen dari predikat (60% Lanjut, 40% Ulang)
                $keputusan = rand(1, 100) <= 60 ? Keputusan::Lanjut : Keputusan::Ulang;

                // Jenis setoran (70% Hafalan Baru, 30% Ulang)
                $jenisSetoran = rand(1, 100) <= 70 ? JenisSetoran::HafalanBaru : JenisSetoran::Ulang;

                // Ayat range (1-5 ayat per setoran)
                $ayatMulai = $currentAyat;
                $ayatSelesai = min($ayatMulai + rand(0, 4), $currentSurah->jumlah_ayat);

                // Generate saran ayat berikutnya
                if ($keputusan === Keputusan::Ulang) {
                    // Ulang: saran ayat sama
                    $saranAyatMulai = $ayatMulai;
                    $saranAyatSelesai = $ayatSelesai;
                    $saranSurahId = $currentSurah->id;
                    $saranJuzId = $currentJuz->id;
                } else {
                    // Lanjut: ayat berikutnya
                    if ($ayatSelesai < $currentSurah->jumlah_ayat) {
                        // Lanjut di surah yang sama
                        $saranAyatMulai = $ayatSelesai + 1;
                        $saranAyatSelesai = min($saranAyatMulai + 4, $currentSurah->jumlah_ayat);
                        $saranSurahId = $currentSurah->id;
                        $saranJuzId = $currentJuz->id;
                    } else {
                        // Lanjut ke surah berikutnya
                        $nextSurah = Surah::where('nomor', '>', $currentSurah->nomor)
                            ->orderBy('nomor')
                            ->first();

                        if ($nextSurah) {
                            $saranAyatMulai = 1;
                            $saranAyatSelesai = min(5, $nextSurah->jumlah_ayat);
                            $saranSurahId = $nextSurah->id;
                            $saranJuzId = $nextSurah->juzs->first()->id ?? $currentJuz->id;
                        } else {
                            // Sudah sampai akhir Al-Qur'an
                            $saranAyatMulai = null;
                            $saranAyatSelesai = null;
                            $saranSurahId = null;
                            $saranJuzId = null;
                        }
                    }
                }

                // Create setoran
                Setoran::create([
                    'absensi_id' => $absensi->id,
                    'jenis_setoran' => $jenisSetoran,
                    'juz_id' => $currentJuz->id,
                    'surah_id' => $currentSurah->id,
                    'ayat_mulai' => $ayatMulai,
                    'ayat_selesai' => $ayatSelesai,
                    'skor_tajwid' => $skorTajwid,
                    'skor_kelancaran' => $skorKelancaran,
                    'skor_makhraj' => $skorMakhraj,
                    'skor_fashahah' => $skorFashahah,
                    'predikat' => $predikat,
                    'keputusan' => $keputusan,
                    'catatan' => $this->randomCatatan($predikat),
                    'saran_ayat_mulai' => $saranAyatMulai,
                    'saran_ayat_selesai' => $saranAyatSelesai,
                    'saran_surah_id' => $saranSurahId,
                    'saran_juz_id' => $saranJuzId,
                ]);

                $totalSetoran++;

                // Update current position untuk entry berikutnya
                if ($keputusan === Keputusan::Lanjut && $saranSurahId) {
                    $currentAyat = $saranAyatMulai;
                    if ($saranSurahId !== $currentSurah->id) {
                        $currentSurah = Surah::find($saranSurahId);
                        $currentJuz = $currentSurah->juzs->first() ?? $currentJuz;
                    }
                }
            }
        }

        $this->command->info("✅ Berhasil generate {$totalSetoran} record setoran demo untuk {$absensis->count()} absensi.");
    }

    /**
     * Generate random skor dengan distribusi realistis
     * 60% Lancar, 30% Kurang Lancar, 10% Tidak Lancar
     */
    protected function randomSkor(): SkorKomponen
    {
        $random = rand(1, 100);
        if ($random <= 60) {
            return SkorKomponen::Lancar;
        } elseif ($random <= 90) {
            return SkorKomponen::KurangLancar;
        } else {
            return SkorKomponen::TidakLancar;
        }
    }

    /**
     * Generate random catatan sesuai predikat
     */
    protected function randomCatatan(SkorKomponen $predikat): ?string
    {
        $catatanLancar = [
            'Alhamdulillah, hafalan sangat baik',
            'Bacaan lancar dan fasih',
            'Pertahankan kualitas hafalan',
            null, // 25% tanpa catatan
        ];

        $catatanKurang = [
            'Perlu lebih banyak pengulangan',
            'Perbaiki tajwid di beberapa ayat',
            'Tingkatkan kelancaran bacaan',
        ];

        $catatanTidak = [
            'Perlu bimbingan intensif',
            'Perbaiki makhraj huruf',
            'Hafalan perlu diulang dari awal',
        ];

        return match ($predikat) {
            SkorKomponen::Lancar => $catatanLancar[array_rand($catatanLancar)],
            SkorKomponen::KurangLancar => $catatanKurang[array_rand($catatanKurang)],
            SkorKomponen::TidakLancar => $catatanTidak[array_rand($catatanTidak)],
        };
    }
}
