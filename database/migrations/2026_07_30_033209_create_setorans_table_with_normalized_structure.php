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
        Schema::create('setorans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absensi_id')->constrained('absensis')->onDelete('cascade');
            $table->enum('jenis_setoran', ['Hafalan Baru', 'Murojah']);
            $table->foreignId('juz_id')->constrained('juzs')->onDelete('restrict');
            $table->foreignId('surah_id')->constrained('surahs')->onDelete('restrict');
            $table->unsignedSmallInteger('ayat_mulai');
            $table->unsignedSmallInteger('ayat_selesai');
            $table->enum('skor_tajwid', ['Lancar', 'Kurang Lancar', 'Tidak Lancar']);
            $table->enum('skor_kelancaran', ['Lancar', 'Kurang Lancar', 'Tidak Lancar']);
            $table->enum('skor_makhraj', ['Lancar', 'Kurang Lancar', 'Tidak Lancar']);
            $table->enum('skor_fashahah', ['Lancar', 'Kurang Lancar', 'Tidak Lancar']);
            $table->enum('predikat', ['Lancar', 'Kurang Lancar', 'Tidak Lancar']);
            $table->enum('keputusan', ['Lanjut', 'Ulang']);
            $table->text('catatan')->nullable();
            $table->unsignedSmallInteger('saran_ayat_mulai')->nullable();
            $table->unsignedSmallInteger('saran_ayat_selesai')->nullable();
            $table->foreignId('saran_surah_id')->nullable()->constrained('surahs')->onDelete('set null');
            $table->foreignId('saran_juz_id')->nullable()->constrained('juzs')->onDelete('set null');
            $table->timestamps();

            // Indexes for query performance and filtering
            $table->index('absensi_id', 'idx_absensi');
            $table->index('jenis_setoran', 'idx_jenis_setoran');
            $table->index('predikat', 'idx_predikat');
            $table->index('keputusan', 'idx_keputusan');
            $table->index('juz_id', 'idx_juz');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setorans');
    }
};
