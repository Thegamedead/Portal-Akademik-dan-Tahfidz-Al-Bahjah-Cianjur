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
        Schema::create('juz_surah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('juz_id')->constrained('juzs')->onDelete('cascade');
            $table->foreignId('surah_id')->constrained('surahs')->onDelete('cascade');
            $table->unsignedSmallInteger('ayat_mulai')->comment('Ayat awal surah dalam juz ini');
            $table->unsignedSmallInteger('ayat_selesai')->comment('Ayat akhir surah dalam juz ini');
            $table->timestamps();

            // Indexes for efficient querying
            $table->index('juz_id', 'idx_juz_id');
            $table->index('surah_id', 'idx_surah_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juz_surah');
    }
};
