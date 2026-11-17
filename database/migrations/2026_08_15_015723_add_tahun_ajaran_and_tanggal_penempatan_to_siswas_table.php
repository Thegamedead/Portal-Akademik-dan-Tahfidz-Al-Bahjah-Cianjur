<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan field tracking untuk:
     * - tahun_ajaran_id: Tahun ajaran ketika siswa didaftarkan
     * - tanggal_penempatan: Tanggal siswa ditempatkan ke kelompok tahfidz
     */
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Tahun ajaran saat siswa didaftarkan
            $table->foreignId('tahun_ajaran_id')
                ->nullable()
                ->after('kelas_id')
                ->constrained('tahun_ajarans')
                ->nullOnDelete();

            // Tanggal penempatan ke kelompok tahfidz
            $table->date('tanggal_penempatan')
                ->nullable()
                ->after('kelompok_id')
                ->comment('Tanggal siswa ditempatkan ke kelompok tahfidz');

            // Index untuk performa query
            $table->index('tahun_ajaran_id');
            $table->index('tanggal_penempatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropIndex(['tahun_ajaran_id']);
            $table->dropIndex(['tanggal_penempatan']);
            $table->dropColumn(['tahun_ajaran_id', 'tanggal_penempatan']);
        });
    }
};
