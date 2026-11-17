<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_tes_penempatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tes_penempatan_id')->constrained('tes_penempatans')->cascadeOnDelete();
            $table->foreignId('juz_id')->constrained('juzs')->cascadeOnDelete();
            $table->foreignId('surah_id')->constrained('surahs')->cascadeOnDelete();
            
            $table->unsignedSmallInteger('ayat_mulai');
            $table->unsignedSmallInteger('ayat_selesai');
            
            $table->timestamps();
            
            $table->index('tes_penempatan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_tes_penempatans');
    }
};
