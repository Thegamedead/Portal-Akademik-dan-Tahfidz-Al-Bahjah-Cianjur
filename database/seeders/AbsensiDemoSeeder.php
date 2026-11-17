<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Kelompok, Siswa, Guru, Absensi};
use App\Enums\StatusKehadiran;
use Carbon\Carbon;

/**
 * Seeder untuk sample data absensi (demo/testing purposes)
 *
 * Generates realistic absensi data dengan berbagai status kehadiran
 * untuk kelompok dan siswa yang ada, covering 2 minggu terakhir
 *
 * Idempotent: dapat dijalankan ulang tanpa duplicate (menggunakan updateOrCreate)
 */
class AbsensiDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active kelompoks with guru pembimbing
        $kelompoks = Kelompok::with(['guruPembimbing', 'siswas' => function($q) {
            $q->where('status_akademik', 'Aktif');
        }])->get();

        if ($kelompoks->isEmpty()) {
            $this->command->warn('Tidak ada kelompok ditemukan. Jalankan kelompok seeder terlebih dahulu.');
            return;
        }

        // Generate absensi untuk 14 hari terakhir (2 minggu)
        $startDate = now()->subDays(13); // 13 days ago + today = 14 days
        $endDate = now();

        $totalAbsensi = 0;

        foreach ($kelompoks as $kelompok) {
            if (!$kelompok->guruPembimbing) {
                $this->command->warn("Kelompok {$kelompok->nama} tidak punya guru pembimbing, skip.");
                continue;
            }

            $siswas = $kelompok->siswas;
            if ($siswas->isEmpty()) {
                $this->command->warn("Kelompok {$kelompok->nama} tidak punya siswa, skip.");
                continue;
            }

            // Generate absensi per hari
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                foreach ($siswas as $siswa) {
                    // Random status dengan distribusi realistis:
                    // 80% Hadir, 10% Izin/Sakit, 10% Alfa
                    $random = rand(1, 100);
                    if ($random <= 80) {
                        $status = StatusKehadiran::Hadir;
                    } elseif ($random <= 90) {
                        $status = rand(0, 1) ? StatusKehadiran::Izin : StatusKehadiran::Sakit;
                    } else {
                        $status = StatusKehadiran::Alfa;
                    }

                    // Calculate alfa counter
                    $lastAbsensi = Absensi::where('siswa_id', $siswa->id)
                        ->where('tanggal', '<', $date->format('Y-m-d'))
                        ->latest('tanggal')
                        ->first();

                    $alfaCounter = 0;
                    if ($status === StatusKehadiran::Alfa) {
                        // Increment dari absensi sebelumnya jika juga Alfa
                        if ($lastAbsensi && $lastAbsensi->status_kehadiran === StatusKehadiran::Alfa) {
                            $alfaCounter = $lastAbsensi->alfa_berturut_turut + 1;
                        } else {
                            $alfaCounter = 1;
                        }
                    }

                    Absensi::updateOrCreate(
                        [
                            'kelompok_id' => $kelompok->id,
                            'siswa_id' => $siswa->id,
                            'tanggal' => $date->format('Y-m-d'),
                        ],
                        [
                            'status_kehadiran' => $status,
                            'guru_pengisi_id' => $kelompok->guru_pembimbing_id,
                            'alfa_berturut_turut' => $alfaCounter,
                            'override_admin' => false,
                            'admin_id' => null,
                        ]
                    );

                    $totalAbsensi++;
                }
            }
        }

        $this->command->info("✅ Berhasil generate {$totalAbsensi} record absensi demo untuk 14 hari terakhir.");
    }
}
