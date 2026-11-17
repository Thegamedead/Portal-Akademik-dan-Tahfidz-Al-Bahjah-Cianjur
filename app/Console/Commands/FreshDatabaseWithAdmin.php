<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class FreshDatabaseWithAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fresh-admin
                            {--force : Force the operation to run in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all tables, run migrations, and seed only Admin account';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Konfirmasi di production
        if ($this->getLaravel()->environment('production') && !$this->option('force')) {
            $this->error('⚠️  Application is in production!');
            $this->warn('Use --force flag to run in production: php artisan db:fresh-admin --force');
            return self::FAILURE;
        }

        $this->info('🔄 Starting fresh database setup with Admin seeder...');
        $this->newLine();

        // Step 1: Drop all tables and run migrations
        $this->warn('⏳ Dropping all tables and running migrations...');
        Artisan::call('migrate:fresh', [], $this->getOutput());
        $this->info('✅ Migrations completed');
        $this->newLine();

        // Step 2: Seed Referensi Quran (Juz & Surah) - DIPERLUKAN untuk sistem
        $this->warn('⏳ Seeding Referensi Quran (Juz & Surah)...');
        try {
            Artisan::call('db:seed', ['--class' => 'JuzsTableSeeder'], $this->getOutput());
            Artisan::call('db:seed', ['--class' => 'SurahsTableSeeder'], $this->getOutput());
            Artisan::call('db:seed', ['--class' => 'JuzSurahSeeder'], $this->getOutput());
            $this->info('✅ Referensi Quran seeded successfully');
        } catch (\Exception $e) {
            $this->warn('⚠️  Referensi Quran seeder skipped: ' . $e->getMessage());
        }
        $this->newLine();

        // Step 3: Seed Admin account
        $this->warn('⏳ Seeding Admin account...');
        Artisan::call('db:seed', ['--class' => 'AdminSeeder'], $this->getOutput());
        $this->newLine();

        // Step 4: Show summary
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('✅ DATABASE FRESH COMPLETED');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        // Show database statistics
        $this->showDatabaseStats();

        $this->newLine();
        $this->info('🚀 Database is ready for manual testing!');
        $this->info('📝 You can now login as Admin and create test data.');
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Show database statistics
     */
    private function showDatabaseStats(): void
    {
        $tables = [
            'admins' => 'Admins',
            'gurus' => 'Guru',
            'siswas' => 'Siswa',
            'kelompoks' => 'Kelompok',
            'kelas' => 'Kelas',
            'juzs' => 'Juz (Referensi)',
            'surahs' => 'Surah (Referensi)',
            'juz_surah' => 'Juz-Surah (Relasi)',
        ];

        $stats = [];
        foreach ($tables as $table => $label) {
            try {
                $count = DB::table($table)->count();
                $stats[] = [$label, $count];
            } catch (\Exception $e) {
                $stats[] = [$label, 'N/A'];
            }
        }

        $this->table(['Table', 'Records'], $stats);
    }
}
