<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update existing data dulu
        DB::table('siswas')
            ->where('status_penempatan', 'Terdaftar')
            ->update(['status_penempatan' => 'Belum Ditempatkan']);

        // 2. Ubah definisi ENUM kolom
        DB::statement("ALTER TABLE siswas MODIFY COLUMN status_penempatan ENUM('Belum Ditempatkan', 'Ditempatkan') NOT NULL DEFAULT 'Belum Ditempatkan'");
    }

    public function down(): void
    {
        // Kembalikan data
        DB::table('siswas')
            ->where('status_penempatan', 'Belum Ditempatkan')
            ->update(['status_penempatan' => 'Terdaftar']);

        // Kembalikan definisi ENUM
        DB::statement("ALTER TABLE siswas MODIFY COLUMN status_penempatan ENUM('Terdaftar', 'Ditempatkan') NOT NULL DEFAULT 'Terdaftar'");
    }
};
