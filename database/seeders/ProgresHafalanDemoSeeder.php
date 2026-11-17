<?php

namespace Database\Seeders;

use App\Models\CapaianJuz;
use App\Models\Juz;
use App\Models\ProgresHafalan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class ProgresHafalanDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Generate realistic baseline and progress data for demo purposes.
     * Creates varied scenarios: siswa without baseline, with partial baseline, with complete juz.
     */
    public function run(): void
    {
        // Get active siswa and current tahun ajaran
        // Priority: aktif + ditempatkan, fallback to just aktif
        $siswas = Siswa::where('status_akademik', 'Aktif')
            ->where('status_penempatan', 'Ditempatkan')
            ->limit(30)
            ->get();

        if ($siswas->isEmpty()) {
            // Fallback: use any active siswa
            $siswas = Siswa::where('status_akademik', 'Aktif')
                ->limit(30)
                ->get();

            if ($siswas->isEmpty()) {
                $this->command->warn('Tidak ada siswa aktif. Jalankan SiswaDemoSeeder terlebih dahulu.');
                return;
            }

            $this->command->info('Using active siswa (not yet placed in kelompok)');
        }

        $tahunAjaran = TahunAjaran::where('status', true)->first();

        if (!$tahunAjaran) {
            // Fallback: use any tahun ajaran
            $tahunAjaran = TahunAjaran::first();

            if (!$tahunAjaran) {
                $this->command->warn('Tidak ada tahun ajaran. Jalankan SemesterSeeder terlebih dahulu.');
                return;
            }

            $this->command->info('Using first tahun ajaran (not active)');
        }

        $allJuzs = Juz::orderBy('juz_ke')->get();

        if ($allJuzs->isEmpty()) {
            $this->command->warn('Tabel juzs kosong. Jalankan JuzSeeder terlebih dahulu.');
            return;
        }

        $this->command->info("Generating progres hafalan untuk {$siswas->count()} siswa...");

        foreach ($siswas as $index => $siswa) {
            // Generate varied baseline scenarios
            $baselineJuzCount = $this->generateBaselineCount($index);
            $baselineJuzs = $baselineJuzCount > 0
                ? $allJuzs->take($baselineJuzCount)->pluck('id')->toArray()
                : null;

            $baselineSurahs = $baselineJuzCount > 0
                ? $this->generateBaselineSurahsText($baselineJuzCount)
                : null;

            $catatanKualitas = $baselineJuzCount > 0
                ? $this->generateCatatanKualitas()
                : null;

            $rekomendasiTingkat = $this->generateRekomendasiTingkat($baselineJuzCount);

            // Calculate aggregated progress
            $totalJuzTuntas = $baselineJuzCount; // For demo, baseline juz = tuntas
            $juzBerjalan = $totalJuzTuntas < 30 ? $totalJuzTuntas + 1 : null;
            $juzBerjalanPercentage = $juzBerjalan ? $this->generateRandomPercentage() : null;
            $targetKelulusan = 5; // Default from config
            $progressTowardTarget = ($totalJuzTuntas / $targetKelulusan) * 100;

            // Create or update ProgresHafalan
            $progresHafalan = ProgresHafalan::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran_id' => $tahunAjaran->id,
                ],
                [
                    'baseline_juzs' => $baselineJuzs,
                    'baseline_surahs' => $baselineSurahs,
                    'catatan_kualitas_baseline' => $catatanKualitas,
                    'rekomendasi_tingkat' => $rekomendasiTingkat,
                    'total_juz_tuntas' => $totalJuzTuntas,
                    'juz_berjalan' => $juzBerjalan,
                    'juz_berjalan_percentage' => $juzBerjalanPercentage,
                    'progress_toward_target' => min($progressTowardTarget, 100),
                ]
            );

            // Create CapaianJuz records for completed juz
            if ($baselineJuzCount > 0) {
                foreach ($allJuzs->take($baselineJuzCount) as $juz) {
                    CapaianJuz::updateOrCreate(
                        [
                            'progres_hafalan_id' => $progresHafalan->id,
                            'juz_id' => $juz->id,
                        ],
                        [
                            'ayat_tercakup' => $juz->jumlah_ayat,
                            'total_ayat' => $juz->jumlah_ayat,
                            'persentase' => 100.00,
                            'status_tuntas' => true,
                            'tanggal_tuntas' => now()->subDays(rand(30, 180)),
                            'detail_ayat_tercakup' => $this->generateDetailAyatTercakup($juz),
                        ]
                    );
                }
            }

            // Create CapaianJuz record for juz_berjalan (if exists)
            if ($juzBerjalan && $juzBerjalan <= 30) {
                $juzBerjalanModel = $allJuzs->firstWhere('juz_ke', $juzBerjalan);
                if ($juzBerjalanModel) {
                    $ayatTercakup = (int) (($juzBerjalanPercentage / 100) * $juzBerjalanModel->jumlah_ayat);

                    CapaianJuz::updateOrCreate(
                        [
                            'progres_hafalan_id' => $progresHafalan->id,
                            'juz_id' => $juzBerjalanModel->id,
                        ],
                        [
                            'ayat_tercakup' => $ayatTercakup,
                            'total_ayat' => $juzBerjalanModel->jumlah_ayat,
                            'persentase' => $juzBerjalanPercentage,
                            'status_tuntas' => false,
                            'tanggal_tuntas' => null,
                            'detail_ayat_tercakup' => $this->generatePartialDetailAyatTercakup($juzBerjalanModel, $ayatTercakup),
                        ]
                    );
                }
            }

            $this->command->info("✓ Siswa {$siswa->nama}: {$totalJuzTuntas} juz tuntas, juz berjalan: " . ($juzBerjalan ?? 'none'));
        }

        $this->command->info('✅ ProgresHafalanDemoSeeder selesai!');
    }

    /**
     * Generate baseline juz count based on index (varied distribution)
     */
    private function generateBaselineCount(int $index): int
    {
        // Distribution: 20% no baseline, 30% 1-3 juz, 30% 4-7 juz, 20% 8-10 juz
        $rand = $index % 10;

        if ($rand < 2) return 0; // No baseline (20%)
        if ($rand < 5) return rand(1, 3); // Low baseline (30%)
        if ($rand < 8) return rand(4, 7); // Medium baseline (30%)
        return rand(8, 10); // High baseline (20%)
    }

    /**
     * Generate descriptive text for baseline surahs
     */
    private function generateBaselineSurahsText(int $juzCount): string
    {
        $surahs = [
            'Al-Fatihah, Al-Baqarah (sebagian)',
            'Al-Baqarah (lanjutan), Ali Imran (sebagian)',
            'Ali Imran (lanjutan), An-Nisa (sebagian)',
            'An-Nisa (lanjutan), Al-Maidah (sebagian)',
            'Al-Maidah (lanjutan), Al-An\'am (sebagian)',
            'Al-An\'am (lanjutan), Al-A\'raf (sebagian)',
            'Al-A\'raf (lanjutan), Al-Anfal (sebagian)',
            'At-Taubah (sebagian)',
            'At-Taubah (lanjutan), Yunus, Hud (sebagian)',
            'Hud (lanjutan), Yusuf (sebagian)',
        ];

        return implode(', ', array_slice($surahs, 0, min($juzCount, count($surahs))));
    }

    /**
     * Generate catatan kualitas hafalan
     */
    private function generateCatatanKualitas(): string
    {
        $templates = [
            'Hafalan lancar dengan tajwid yang baik.',
            'Hafalan cukup lancar, perlu perbaikan di beberapa ayat panjang.',
            'Hafalan baik, makhraj dan fashahah perlu ditingkatkan.',
            'Hafalan sangat lancar dengan kualitas tajwid yang memuaskan.',
            'Hafalan cukup baik, kadang masih terbata-bata di ayat tertentu.',
        ];

        return $templates[array_rand($templates)];
    }

    /**
     * Generate rekomendasi tingkat based on baseline juz count
     */
    private function generateRekomendasiTingkat(int $juzCount): string
    {
        if ($juzCount === 0) return 'Dasar';
        if ($juzCount <= 3) return 'Dasar';
        if ($juzCount <= 7) return 'Menengah';
        return 'Atas';
    }

    /**
     * Generate random percentage for juz berjalan (10-95%)
     */
    private function generateRandomPercentage(): float
    {
        return round(rand(10, 95) + (rand(0, 99) / 100), 2);
    }

    /**
     * Generate detail ayat tercakup for completed juz
     */
    private function generateDetailAyatTercakup(Juz $juz): array
    {
        // Simplified: return full coverage from start to end
        return [
            [
                'surah_id' => $juz->awal_surah,
                'ayat_mulai' => $juz->awal_ayat,
                'ayat_selesai' => $juz->akhir_ayat,
            ]
        ];
    }

    /**
     * Generate partial detail ayat tercakup for juz berjalan
     */
    private function generatePartialDetailAyatTercakup(Juz $juz, int $ayatTercakup): array
    {
        // Simplified: partial coverage from start
        return [
            [
                'surah_id' => $juz->awal_surah,
                'ayat_mulai' => $juz->awal_ayat,
                'ayat_selesai' => min($juz->awal_ayat + $ayatTercakup - 1, $juz->akhir_ayat),
            ]
        ];
    }
}
