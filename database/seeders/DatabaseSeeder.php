<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Only seed essential data: Admin and Quran reference data
        $this->call([
            ModulSatuSeeder::class,  // Contains admin seeder
            JuzSeeder::class,
            JuzsTableSeeder::class,
            SurahsTableSeeder::class,
            JuzSurahSeeder::class,
            // SiswaKelas7to9Seeder::class,  // Commented out - no test students
            // GuruTahfidzSeeder::class,     // Commented out - no test teachers
        ]);
    }
}
