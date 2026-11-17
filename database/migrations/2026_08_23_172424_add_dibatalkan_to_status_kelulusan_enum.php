<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kelulusan_programs', function (Blueprint $table) {
            DB::statement("ALTER TABLE kelulusan_programs MODIFY COLUMN status_kelulusan ENUM('Diajukan', 'Disetujui', 'Ditolak', 'Dibatalkan') NOT NULL DEFAULT 'Diajukan'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelulusan_programs', function (Blueprint $table) {
            // Can't easily drop enum value in MySQL safely without recreating it, but this allows rollback if we don't use Dibatalkan.
            DB::statement("ALTER TABLE kelulusan_programs MODIFY COLUMN status_kelulusan ENUM('Diajukan', 'Disetujui', 'Ditolak') NOT NULL DEFAULT 'Diajukan'");
        });
    }
};
