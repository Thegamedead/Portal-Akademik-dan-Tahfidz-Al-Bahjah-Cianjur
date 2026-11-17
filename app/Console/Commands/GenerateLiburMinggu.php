<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Enums\StatusKehadiran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateLiburMinggu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-libur-minggu {--month= : Bulan spesifik dalam format YYYY-MM} {--backfill= : Backfill sejumlah bulan ke belakang}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate rekam absen status Libur untuk semua hari Minggu bagi siswa aktif';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $monthOpt = $this->option('month');
        $backfillOpt = $this->option('backfill');
        
        $monthsToProcess = [];

        if ($monthOpt) {
            $monthsToProcess[] = $monthOpt;
        } elseif ($backfillOpt) {
            $backfillMonths = (int) $backfillOpt;
            for ($i = 0; $i <= $backfillMonths; $i++) {
                $monthsToProcess[] = now()->subMonths($i)->format('Y-m');
            }
        } else {
            // Default: process current month
            $monthsToProcess[] = now()->format('Y-m');
        }

        // Get all active students with their kelompok
        $siswas = Siswa::where('status_akademik', 'Aktif')
            ->whereNotNull('kelompok_id')
            ->with('kelompok')
            ->get();

        if ($siswas->isEmpty()) {
            $this->info("Tidak ada siswa aktif yang tergabung dalam kelompok.");
            return;
        }

        $now = now();
        $totalInserted = 0;

        foreach ($monthsToProcess as $monthStr) {
            [$year, $month] = explode('-', $monthStr);
            $mulai = Carbon::createFromDate((int)$year, (int)$month, 1)->startOfDay();
            $selesai = $mulai->copy()->endOfMonth();
            
            // Limit to today if processing current month
            if ($selesai->isFuture()) {
                $selesai = now();
            }

            $sundays = [];
            $cursor = $mulai->copy();
            while ($cursor->lte($selesai)) {
                if ($cursor->dayOfWeek === Carbon::SUNDAY) {
                    $sundays[] = $cursor->format('Y-m-d');
                }
                $cursor->addDay();
            }

            if (empty($sundays)) {
                $this->info("Bulan {$monthStr}: Tidak ada hari Minggu yang perlu diproses.");
                continue;
            }

            // Get existing absensi for these sundays for all active siswas
            $siswaIds = $siswas->pluck('id')->toArray();
            $existingAbsensi = Absensi::whereIn('siswa_id', $siswaIds)
                ->whereIn('tanggal', $sundays)
                ->get()
                ->groupBy(fn($a) => $a->siswa_id . '_' . Carbon::parse($a->tanggal)->format('Y-m-d'));

            $newRecords = [];

            foreach ($sundays as $sunday) {
                foreach ($siswas as $siswa) {
                    $key = $siswa->id . '_' . $sunday;
                    if (!isset($existingAbsensi[$key])) {
                        $newRecords[] = [
                            'kelompok_id' => $siswa->kelompok_id,
                            'guru_pengisi_id' => $siswa->kelompok ? $siswa->kelompok->guru_pembimbing_id : 1, // Fallback to 1 if no guru
                            'siswa_id' => $siswa->id,
                            'tanggal' => $sunday,
                            'status_kehadiran' => StatusKehadiran::Libur->value,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }

            if (!empty($newRecords)) {
                // Batch insert using chunks to avoid memory issues
                $chunks = array_chunk($newRecords, 500);
                foreach ($chunks as $chunk) {
                    Absensi::insert($chunk);
                }
                $count = count($newRecords);
                $totalInserted += $count;
                $this->info("Bulan {$monthStr}: Berhasil menambahkan {$count} record libur.");
            } else {
                $this->info("Bulan {$monthStr}: Semua hari Minggu sudah memiliki record absensi.");
            }
        }

        $this->info("Selesai! Total {$totalInserted} record libur baru ditambahkan.");
    }
}
