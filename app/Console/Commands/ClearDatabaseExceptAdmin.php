<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Signature('db:clear-except-admin {--force : Force the operation without confirmation}')]
#[Description('Clear all database tables except admin credentials')]
class ClearDatabaseExceptAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  PERINGATAN: Perintah ini akan menghapus SEMUA DATA kecuali kredensial admin. Apakah Anda yakin?')) {
                $this->info('Operasi dibatalkan.');
                return;
            }
        }

        $this->info('🔄 Memulai proses pembersihan database...');

        try {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Daftar tabel yang akan dikosongkan (semua kecuali admins)
            $tablesToClear = [
                // Master Data
                'tahun_ajarans',
                'semesters',
                'kelas',
                'gurus',
                'guru_tugas_tambahans',
                'guru_riwayat_pembimbings',
                'siswas',
                'orang_tua_walis',
                'kelompoks',
                'kelompok_siswas',

                // Tahfidz Data
                'referensi_qurans',
                'capaian_juzs',
                'sesis',
                'absensis',
                'setorans',
                'detail_setorans',
                'progres_hafalans',
                'transkrip_capaian_hafalans',

                // Tes Penempatan
                'tes_penempatans',
                'detail_tes_penempatans',
                'baseline_hafalans',

                // Usulan Kenaikan Tingkat
                'usulan_kenaikan_tingkats',
                'detail_usulan_kenaikan_tingkats',

                // Laporan
                'laporan_absensis',
                'laporan_progres_hafalans',
                'laporan_setorans',
                'laporan_kelulusans',

                // System
                'activity_log',
                'sessions',
                'cache',
                'cache_locks',
                'jobs',
                'job_batches',
                'failed_jobs',
                'password_reset_tokens',
                'personal_access_tokens',
            ];

            $clearedCount = 0;
            $skippedCount = 0;

            foreach ($tablesToClear as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->line("  ✓ Tabel '{$table}' telah dikosongkan");
                    $clearedCount++;
                } else {
                    $this->line("  ⊘ Tabel '{$table}' tidak ditemukan (skip)", 'comment');
                    $skippedCount++;
                }
            }

            // Enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->newLine();
            $this->info("✅ Selesai! {$clearedCount} tabel berhasil dikosongkan.");
            if ($skippedCount > 0) {
                $this->comment("   {$skippedCount} tabel tidak ditemukan (belum dibuat).");
            }
            $this->info('📌 Kredensial admin tetap tersimpan di tabel "admins".');
            $this->newLine();
            $this->comment('💡 Tips: Jalankan seeder untuk mengisi data referensi:');
            $this->comment('   php artisan db:seed --class=JuzSeeder');

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->error('❌ Terjadi kesalahan: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
