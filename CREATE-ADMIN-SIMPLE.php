<?php
// Script untuk membuat admin sederhana
// Jalankan dengan: php CREATE-ADMIN-SIMPLE.php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

try {
    // Hapus admin lama jika ada
    Admin::truncate();

    // Buat admin baru yang simple
    $admin = Admin::create([
        'nama' => 'Administrator',
        'email' => 'admin@tahfidz.com',
        'password' => Hash::make('Admin123!'),
        'status_aktif' => true,
    ]);

    echo "✅ Admin berhasil dibuat!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📝 LOGIN ADMIN:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Email: admin@tahfidz.com\n";
    echo "Password: Admin123!\n";
    echo "URL: /admin/login\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
