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
        Schema::create('juzs', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('juz_ke')->unique()->comment('Nomor juz 1-30');
            $table->unsignedTinyInteger('urutan_hafalan')->comment('Urutan hafalan yang dapat dikustomisasi');
            $table->unsignedSmallInteger('jumlah_ayat')->comment('Total ayat dalam juz ini');
            $table->unsignedTinyInteger('awal_surah')->nullable()->comment('Surah awal juz');
            $table->unsignedTinyInteger('awal_ayat')->nullable()->comment('Ayat awal juz');
            $table->unsignedTinyInteger('akhir_surah')->nullable()->comment('Surah akhir juz');
            $table->unsignedTinyInteger('akhir_ayat')->nullable()->comment('Ayat akhir juz');
            $table->timestamps();

            // Index for querying by juz number
            $table->index('juz_ke', 'idx_juz_ke');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juzs');
    }
};
