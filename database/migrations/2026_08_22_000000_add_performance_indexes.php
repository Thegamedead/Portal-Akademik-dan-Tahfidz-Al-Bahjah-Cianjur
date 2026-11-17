<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds performance indexes for frequently queried columns
     */
    public function up(): void
    {
        // Absensis table - Dashboard and history queries
        Schema::table('absensis', function (Blueprint $table) {
            // For dashboard queries: WHERE kelompok_id = X AND tanggal = Y
            $table->index(['kelompok_id', 'tanggal'], 'idx_absensis_kelompok_tanggal');

            // For student history: WHERE siswa_id = X AND tanggal >= Y
            $table->index(['siswa_id', 'tanggal'], 'idx_absensis_siswa_tanggal');

            // For status filtering: WHERE status_kehadiran = X
            $table->index('status_kehadiran', 'idx_absensis_status');
        });

        // Setorans table - Progress calculation queries
        Schema::table('setorans', function (Blueprint $table) {
            // For progress queries: WHERE absensi_id IN (...) AND keputusan = X
            $table->index(['absensi_id', 'keputusan'], 'idx_setorans_absensi_keputusan');

            // For juz progress: WHERE juz_id = X
            $table->index('juz_id', 'idx_setorans_juz');

            // For surah progress: WHERE surah_id = X
            $table->index('surah_id', 'idx_setorans_surah');
        });

        // Progres Hafalans table - Ranking and filtering
        Schema::table('progres_hafalans', function (Blueprint $table) {
            // For ranking: ORDER BY total_juz_tuntas DESC
            $table->index('total_juz_tuntas', 'idx_progres_juz_tuntas');

            // For target queries: WHERE target_juz = X
            $table->index('target_juz', 'idx_progres_target');
        });

        // Siswas table - Filtering and searching
        Schema::table('siswas', function (Blueprint $table) {
            // For kelompok queries: WHERE kelompok_id = X AND status_akademik = Y
            $table->index(['kelompok_id', 'status_akademik'], 'idx_siswas_kelompok_status');

            // For penempatan queries: WHERE status_penempatan = X
            $table->index('status_penempatan', 'idx_siswas_penempatan');

            // For tahun ajaran queries: WHERE tahun_ajaran_id = X
            $table->index('tahun_ajaran_id', 'idx_siswas_tahun_ajaran');
        });

        // Kelompoks table - Dashboard queries
        Schema::table('kelompoks', function (Blueprint $table) {
            // For active year queries: WHERE tahun_ajaran_id = X
            $table->index('tahun_ajaran_id', 'idx_kelompoks_tahun_ajaran');

            // For guru queries: WHERE guru_pembimbing_id = X
            $table->index('guru_pembimbing_id', 'idx_kelompoks_guru');
        });

        // Usulan Kenaikan Tingkats table - Status filtering
        Schema::table('usulan_kenaikan_tingkats', function (Blueprint $table) {
            // For status queries: WHERE status_usulan = X
            $table->index('status_usulan', 'idx_usulan_status');

            // For student queries: WHERE siswa_id = X
            $table->index('siswa_id', 'idx_usulan_siswa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropIndex('idx_absensis_kelompok_tanggal');
            $table->dropIndex('idx_absensis_siswa_tanggal');
            $table->dropIndex('idx_absensis_status');
        });

        Schema::table('setorans', function (Blueprint $table) {
            $table->dropIndex('idx_setorans_absensi_keputusan');
            $table->dropIndex('idx_setorans_juz');
            $table->dropIndex('idx_setorans_surah');
        });

        Schema::table('progres_hafalans', function (Blueprint $table) {
            $table->dropIndex('idx_progres_juz_tuntas');
            $table->dropIndex('idx_progres_target');
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropIndex('idx_siswas_kelompok_status');
            $table->dropIndex('idx_siswas_penempatan');
            $table->dropIndex('idx_siswas_tahun_ajaran');
        });

        Schema::table('kelompoks', function (Blueprint $table) {
            $table->dropIndex('idx_kelompoks_tahun_ajaran');
            $table->dropIndex('idx_kelompoks_guru');
        });

        Schema::table('usulan_kenaikan_tingkats', function (Blueprint $table) {
            $table->dropIndex('idx_usulan_status');
            $table->dropIndex('idx_usulan_siswa');
        });
    }
};
