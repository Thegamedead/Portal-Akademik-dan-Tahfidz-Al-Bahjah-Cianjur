<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Database\Seeder;

class ModulSatuSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Utama — updateOrCreate aman untuk FK constraint
        Admin::updateOrCreate(
            ['email' => 'admin.tahfidz@albahjah.com'],
            [
                'nama'         => 'Admin Tahfidz',
                'password'     => \Illuminate\Support\Facades\Hash::make('Adm!n123'),
                'status_aktif' => true,
            ]
        );

        // Admin Tambahan
        Admin::updateOrCreate(['email' => 'admin2@albahjah.com'], ['nama' => 'Admin 2', 'password' => \Illuminate\Support\Facades\Hash::make('password'), 'status_aktif' => true]);
        Admin::updateOrCreate(['email' => 'admin_nonaktif@albahjah.com'], ['nama' => 'Admin Non-Aktif', 'password' => \Illuminate\Support\Facades\Hash::make('password'), 'status_aktif' => false]);

        // No sample guru or siswa - production ready with clean database
    }
}
