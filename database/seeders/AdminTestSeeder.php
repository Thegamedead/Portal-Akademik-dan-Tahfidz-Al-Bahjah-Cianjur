<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder untuk akun admin testing
 *
 * Creates a test admin account for manual testing purposes
 * Credentials: admin / password123
 */
class AdminTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update admin test account
        Admin::updateOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('password123'),
                'status_aktif' => true,
            ]
        );

        $this->command->info('✅ Admin test account created successfully!');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  ADMIN LOGIN CREDENTIALS (FOR TESTING)');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  Username: admin');
        $this->command->info('  Password: password123');
        $this->command->info('  URL: /admin/login');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('');
        $this->command->warn('⚠️  IMPORTANT: Change this password in production!');
    }
}
