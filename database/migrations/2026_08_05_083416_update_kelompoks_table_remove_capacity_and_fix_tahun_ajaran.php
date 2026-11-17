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
        Schema::table('kelompoks', function (Blueprint $table) {
            // Make capacity fields nullable (keep columns for historical data)
            $table->integer('kapasitas_min')->nullable()->default(null)->change();
            $table->integer('kapasitas_maks')->nullable()->default(null)->change();

            // Make tahun_ajaran_id nullable to fix default value error
            // Get current active tahun ajaran as fallback
            $activeTahunAjaran = DB::table('tahun_ajarans')
                ->where('status', true)
                ->first();

            if ($activeTahunAjaran) {
                // Set default to current active tahun ajaran
                $table->foreignId('tahun_ajaran_id')
                    ->default($activeTahunAjaran->id)
                    ->change();
            } else {
                // If no active tahun ajaran, make it nullable
                $table->foreignId('tahun_ajaran_id')
                    ->nullable()
                    ->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelompoks', function (Blueprint $table) {
            // Restore original non-nullable capacity with defaults
            $table->integer('kapasitas_min')->default(6)->change();
            $table->integer('kapasitas_maks')->default(12)->change();

            // Restore original non-nullable tahun_ajaran_id
            $table->foreignId('tahun_ajaran_id')->change();
        });
    }
};
