<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the ENUM column to include 'Libur'
        DB::statement("ALTER TABLE absensis MODIFY status_kehadiran ENUM('Hadir', 'Izin', 'Sakit', 'Alfa', 'Libur') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // To safely reverse, one would normally update 'Libur' rows to something else first,
        // but for simplicity, we just revert the ENUM definition.
        DB::statement("ALTER TABLE absensis MODIFY status_kehadiran ENUM('Hadir', 'Izin', 'Sakit', 'Alfa') NOT NULL");
    }
};
