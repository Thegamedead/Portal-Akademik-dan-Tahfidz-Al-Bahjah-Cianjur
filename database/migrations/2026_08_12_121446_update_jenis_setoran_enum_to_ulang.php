<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing 'Murojah' records to 'Ulang'
        DB::table('setorans')
            ->where('jenis_setoran', 'Murojah')
            ->update(['jenis_setoran' => 'Ulang']);

        // Alter the enum column to use new values
        DB::statement("ALTER TABLE setorans MODIFY COLUMN jenis_setoran ENUM('Hafalan Baru', 'Ulang') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Update 'Ulang' back to 'Murojah'
        DB::table('setorans')
            ->where('jenis_setoran', 'Ulang')
            ->update(['jenis_setoran' => 'Murojah']);

        // Restore the old enum values
        DB::statement("ALTER TABLE setorans MODIFY COLUMN jenis_setoran ENUM('Hafalan Baru', 'Murojah') NOT NULL");
    }
};
