<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Admin;

echo "🔍 CHECKING ADMIN ACCOUNTS\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$admins = Admin::all();

if ($admins->isEmpty()) {
    echo "❌ NO ADMIN FOUND!\n";
    echo "Run: php artisan db:seed --class=AdminSeeder\n";
} else {
    echo "✅ Found " . $admins->count() . " admin(s):\n\n";

    foreach ($admins as $admin) {
        echo "📧 Email: " . $admin->email . "\n";
        echo "👤 Nama: " . $admin->nama . "\n";
        echo "🔓 Status: " . ($admin->status_aktif ? 'Aktif' : 'Nonaktif') . "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    }

    echo "\n🚀 LOGIN INFO:\n";
    $firstAdmin = $admins->first();
    echo "Email: " . $firstAdmin->email . "\n";
    echo "Password: Admin123!\n";
    echo "URL: /admin/login\n";
}
