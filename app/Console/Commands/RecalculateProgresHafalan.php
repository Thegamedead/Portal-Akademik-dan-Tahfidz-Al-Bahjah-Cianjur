<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\ProgresHafalan;
use App\Services\ProgresHafalanService;
use Illuminate\Support\Facades\DB;

class RecalculateProgresHafalan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'progres:recalculate {--siswa_id= : Specific siswa ID to recalculate (optional)} {--all : Recalculate all siswa}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate progres hafalan for siswa (useful when Observer missed events or data imported from old system)';

    protected ProgresHafalanService $progresService;

    /**
     * Execute the console command.
     */
    public function handle(ProgresHafalanService $progresService)
    {
        $this->progresService = $progresService;

        $siswaId = $this->option('siswa_id');
        $recalculateAll = $this->option('all');

        if ($siswaId) {
            // Recalculate specific siswa
            return $this->recalculateSingleSiswa((int) $siswaId);
        }

        if ($recalculateAll) {
            // Recalculate all siswa
            return $this->recalculateAllSiswa();
        }

        // Default: recalculate siswa with setoran but total_juz_tuntas = 0 (orphaned data)
        return $this->recalculateOrphanedSiswa();
    }

    /**
     * Recalculate single siswa
     */
    protected function recalculateSingleSiswa(int $siswaId): int
    {
        $siswa = Siswa::find($siswaId);

        if (!$siswa) {
            $this->error("❌ Siswa dengan ID {$siswaId} tidak ditemukan.");
            return self::FAILURE;
        }

        $this->info("🔄 Recalculating progres hafalan untuk: {$siswa->nama} (NIS: {$siswa->nis})");

        try {
            $result = $this->progresService->calculateProgres($siswaId);

            $this->info("✅ Berhasil! Total Juz Tuntas: {$result['total_juz_tuntas']}");
            $this->line("   Juz Berjalan: " . ($result['juz_berjalan'] ?? 'N/A') . " ({$result['juz_berjalan_percentage']}%)");
            $this->line("   Progress Menuju Target: {$result['progress_toward_target']}%");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    /**
     * Recalculate all siswa
     */
    protected function recalculateAllSiswa(): int
    {
        $this->info("🔄 Recalculating progres hafalan untuk SEMUA siswa...");

        $siswas = Siswa::whereIn('status_akademik', ['Aktif', 'Lulus', 'Keluar'])
            ->orderBy('nama')
            ->get();

        if ($siswas->isEmpty()) {
            $this->warn("⚠️  Tidak ada siswa ditemukan.");
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($siswas->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($siswas as $siswa) {
            try {
                $this->progresService->calculateProgres($siswa->id);
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "{$siswa->nama} (ID: {$siswa->id}): {$e->getMessage()}";
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Selesai! Berhasil: {$successCount} siswa");

        if ($errorCount > 0) {
            $this->error("❌ Error: {$errorCount} siswa");
            $this->newLine();
            $this->line("Detail errors:");
            foreach ($errors as $error) {
                $this->line("  - {$error}");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Recalculate siswa with orphaned data (has setoran but total_juz_tuntas = 0)
     */
    protected function recalculateOrphanedSiswa(): int
    {
        $this->info("🔍 Mencari siswa dengan data setoran tapi total_juz_tuntas = 0...");

        // Find siswa with qualifying setoran but total_juz_tuntas = 0
        $siswaIds = DB::table('setorans')
            ->join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
            ->join('progres_hafalans', 'absensis.siswa_id', '=', 'progres_hafalans.siswa_id')
            ->where('setorans.jenis_setoran', 'Hafalan Baru')
            ->where('setorans.keputusan', 'Lanjut')
            ->where('progres_hafalans.total_juz_tuntas', 0)
            ->select('progres_hafalans.siswa_id')
            ->distinct()
            ->pluck('siswa_id');

        if ($siswaIds->isEmpty()) {
            $this->info("✅ Tidak ada data orphaned. Semua progres hafalan sudah up-to-date!");
            return self::SUCCESS;
        }

        $this->warn("⚠️  Ditemukan {$siswaIds->count()} siswa dengan data orphaned.");
        $this->newLine();

        $siswas = Siswa::whereIn('id', $siswaIds)->orderBy('nama')->get();

        $bar = $this->output->createProgressBar($siswas->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;
        $results = [];

        foreach ($siswas as $siswa) {
            try {
                $result = $this->progresService->calculateProgres($siswa->id);
                $successCount++;

                $results[] = [
                    'Nama' => $siswa->nama,
                    'NIS' => $siswa->nis,
                    'Total Juz Tuntas' => $result['total_juz_tuntas'],
                    'Progress' => $result['progress_toward_target'] . '%',
                ];
            } catch (\Exception $e) {
                $errorCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Selesai! Berhasil merecalculate {$successCount} siswa");

        if ($errorCount > 0) {
            $this->error("❌ Error: {$errorCount} siswa");
        }

        if (!empty($results)) {
            $this->newLine();
            $this->table(['Nama', 'NIS', 'Total Juz Tuntas', 'Progress'], $results);
        }

        return self::SUCCESS;
    }
}
