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
        Schema::create('referensi_qurans', function (Blueprint $table) {
            $table->id();
            $table->integer('juz_ke')->unique(); // 1-30
            $table->integer('urutan_hafalan');
            $table->string('awal_surah');
            $table->integer('awal_ayat');
            $table->string('akhir_surah');
            $table->integer('akhir_ayat');
            $table->integer('jumlah_ayat');
            // No timestamps - static reference data
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referensi_qurans');
    }
};
