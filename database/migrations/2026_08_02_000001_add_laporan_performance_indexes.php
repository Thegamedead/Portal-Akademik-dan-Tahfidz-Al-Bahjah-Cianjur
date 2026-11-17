<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add performance indexes to report-heavy columns.
 * Supports Requirement 12.3 (Database indexes on filter columns).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Absensi indexes
        Schema::table('absensis', function (Blueprint $table) {
            if (!$this->indexExists('absensis', 'absensis_siswa_id_tanggal_index')) {
                $table->index(['siswa_id', 'tanggal'], 'absensis_siswa_id_tanggal_index');
            }
            if (!$this->indexExists('absensis', 'absensis_kelompok_tanggal_index')) {
                $table->index(['kelompok_id', 'tanggal'], 'absensis_kelompok_tanggal_index');
            }
        });

        // Setorans indexes
        Schema::table('setorans', function (Blueprint $table) {
            if (!$this->indexExists('setorans', 'setorans_absensi_predikat_index')) {
                $table->index(['absensi_id', 'predikat'], 'setorans_absensi_predikat_index');
            }
        });

        // Progres Hafalan indexes
        Schema::table('progres_hafalans', function (Blueprint $table) {
            if (!$this->indexExists('progres_hafalans', 'progres_hafalans_siswa_tahun_index')) {
                $table->index(['siswa_id', 'tahun_ajaran_id'], 'progres_hafalans_siswa_tahun_index');
            }
        });

        // Usulan Kenaikan Tingkat indexes
        Schema::table('usulan_kenaikan_tingkats', function (Blueprint $table) {
            if (!$this->indexExists('usulan_kenaikan_tingkats', 'usulan_status_tanggal_index')) {
                $table->index(['status_usulan', 'tanggal_usul'], 'usulan_status_tanggal_index');
            }
        });

        // Siswas indexes
        Schema::table('siswas', function (Blueprint $table) {
            if (!$this->indexExists('siswas', 'siswas_kelompok_status_index')) {
                $table->index(['kelompok_id', 'status_akademik'], 'siswas_kelompok_status_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropIndexIfExists('absensis_siswa_id_tanggal_index');
            $table->dropIndexIfExists('absensis_kelompok_tanggal_index');
        });

        Schema::table('setorans', function (Blueprint $table) {
            $table->dropIndexIfExists('setorans_absensi_predikat_index');
        });

        Schema::table('progres_hafalans', function (Blueprint $table) {
            $table->dropIndexIfExists('progres_hafalans_siswa_tahun_index');
        });

        Schema::table('usulan_kenaikan_tingkats', function (Blueprint $table) {
            $table->dropIndexIfExists('usulan_status_tanggal_index');
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropIndexIfExists('siswas_kelompok_status_index');
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $conn = Schema::getConnection();
            $dbSchemaManager = $conn->getDoctrineSchemaManager();
            $indexes = $dbSchemaManager->listTableIndexes($table);
            return array_key_exists($indexName, $indexes);
        } catch (\Throwable) {
            return false;
        }
    }
};
