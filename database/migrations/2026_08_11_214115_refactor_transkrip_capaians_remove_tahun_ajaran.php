<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * REFACTOR: Transkrip tidak lagi terikat tahun ajaran
     * - Hapus tahun_ajaran_id
     * - Transkrip dicatat berdasarkan tanggal generate saja
     */
    public function up(): void
    {
        Schema::table('transkrip_capaians', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['tahun_ajaran_id']);
        });

        // Drop kolom tahun_ajaran_id
        Schema::table('transkrip_capaians', function (Blueprint $table) {
            $table->dropColumn('tahun_ajaran_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add tahun_ajaran_id
        Schema::table('transkrip_capaians', function (Blueprint $table) {
            $table->foreignId('tahun_ajaran_id')
                ->after('siswa_id')
                ->constrained('tahun_ajarans')
                ->cascadeOnDelete();
        });
    }
};
