<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gunakan updateOrCreate agar aman meski ada FK ke tabel lain
        Admin::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'nama'         => 'Administrator',
                'password'     => Hash::make('admin123'),
                'status_aktif' => true,
            ]
        );

        $this->command->info('✅ Admin account created/updated successfully!');
        $this->command->newLine();
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📝 LOGIN ADMIN');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->table(
            ['Field', 'Value'],
            [
                ['URL', 'http://127.0.0.1:8000/admin/login'],
                ['Email', 'admin@test.com'],
                ['Password', 'admin123'],
            ]
        );
        $this->command->newLine();
    }
}
