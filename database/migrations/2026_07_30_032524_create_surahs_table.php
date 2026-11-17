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
        Schema::create('surahs', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('nomor_surah')->unique()->comment('Nomor surah 1-114');
            $table->string('nama_latin', 50)->comment('Nama surah dalam huruf latin');
            $table->string('nama_arab', 50)->comment('Nama surah dalam huruf arab');
            $table->unsignedSmallInteger('jumlah_ayat')->comment('Total ayat dalam surah');
            $table->enum('tempat_turun', ['Makkiyah', 'Madaniyah'])->comment('Tempat turunnya surah');
            $table->unsignedTinyInteger('urutan_mushaf')->comment('Urutan surah dalam mushaf (1-114)');
            $table->timestamps();

            // Indexes for querying
            $table->index('nomor_surah', 'idx_nomor_surah');
            $table->index('urutan_mushaf', 'idx_urutan_mushaf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surahs');
    }
};
