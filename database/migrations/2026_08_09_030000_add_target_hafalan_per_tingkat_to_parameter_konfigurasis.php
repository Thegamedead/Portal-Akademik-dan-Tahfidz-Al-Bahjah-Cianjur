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
        Schema::table('parameter_konfigurasis', function (Blueprint $table) {
            if (!Schema::hasColumn('parameter_konfigurasis', 'target_hafalan_per_sesi')) {
                $table->json('target_hafalan_per_sesi')->nullable()->after('hari_efektif_tahfidz');
            }

            if (!Schema::hasColumn('parameter_konfigurasis', 'komponen_penilaian')) {
                $table->json('komponen_penilaian')->nullable()->after('target_hafalan_per_sesi');
            }
        });

        // No default values set - admin configures manually via UI

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parameter_konfigurasis', function (Blueprint $table) {
            $table->dropColumn(['target_hafalan_per_sesi', 'komponen_penilaian']);
        });
    }
};
