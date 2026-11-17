<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * REFACTOR: Mengubah progres_hafalans menjadi akumulatif (tidak per tahun ajaran)
     * - Hapus tahun_ajaran_id (progres jadi lifetime/permanen)
     * - Tambah status_tahfidz (independen dari akademik)
     * - Tambah target_juz (configurable per sekolah)
     * - Tambah tanggal_lulus_tahfidz
     */
    public function up(): void
    {
        // Check if foreign key exists before dropping
        Schema::table('progres_hafalans', function (Blueprint $table) {
            // Check if tahun_ajaran_id column exists
            if (Schema::hasColumn('progres_hafalans', 'tahun_ajaran_id')) {
                // Drop foreign key constraint if exists
                try {
                    $table->dropForeign(['tahun_ajaran_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }

                // Drop unique constraint
                try {
                    $table->dropUnique('progres_hafalans_unique');
                } catch (\Exception $e) {
                    // Unique constraint might not exist, continue
                }
            }
        });

        // Drop kolom tahun_ajaran_id if exists
        if (Schema::hasColumn('progres_hafalans', 'tahun_ajaran_id')) {
            Schema::table('progres_hafalans', function (Blueprint $table) {
                $table->dropColumn('tahun_ajaran_id');
            });
        }

        // Add unique constraint baru & kolom baru
        Schema::table('progres_hafalans', function (Blueprint $table) {
            // Unique constraint: satu siswa = satu record selamanya
            if (!Schema::hasColumn('progres_hafalans', 'target_juz')) {
                $table->unique('siswa_id', 'progres_hafalans_siswa_unique');

                // Target minimal juz untuk lulus program tahfidz (default dari config)
                $table->integer('target_juz')
                    ->default(5)
                    ->after('progress_toward_target')
                    ->comment('Target minimal juz untuk dinyatakan lulus program tahfidz');

                // Status tahfidz: independen dari status akademik
                $table->enum('status_tahfidz', ['Belum Lulus', 'Lulus'])
                    ->default('Belum Lulus')
                    ->after('target_juz')
                    ->comment('Status kelulusan program tahfidz (independen dari akademik)');

                // Tanggal pertama kali mencapai target minimal
                $table->date('tanggal_lulus_tahfidz')
                    ->nullable()
                    ->after('status_tahfidz')
                    ->comment('Tanggal pertama kali siswa mencapai target minimal juz');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progres_hafalans', function (Blueprint $table) {
            // Drop unique constraint baru
            $table->dropUnique('progres_hafalans_siswa_unique');

            // Drop kolom baru
            $table->dropColumn(['target_juz', 'status_tahfidz', 'tanggal_lulus_tahfidz']);
        });

        // Re-add tahun_ajaran_id
        Schema::table('progres_hafalans', function (Blueprint $table) {
            $table->foreignId('tahun_ajaran_id')
                ->after('siswa_id')
                ->constrained('tahun_ajarans')
                ->cascadeOnDelete();

            // Re-add unique constraint lama
            $table->unique(['siswa_id', 'tahun_ajaran_id'], 'progres_hafalans_unique');
        });
    }
};
