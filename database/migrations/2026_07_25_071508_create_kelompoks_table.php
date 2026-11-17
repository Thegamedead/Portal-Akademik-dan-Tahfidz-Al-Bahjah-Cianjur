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
        Schema::create('kelompoks', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis', ['Banin', 'Banat']);
            $table->enum('tingkat', ['Dasar', 'Menengah', 'Atas']);
            $table->foreignId('guru_pembimbing_id')->constrained('gurus');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans');
            $table->integer('kapasitas_min')->default(6);
            $table->integer('kapasitas_maks')->default(12);
            $table->string('jadwal_hari')->nullable();
            $table->string('jadwal_jam')->nullable();
            $table->timestamps();

            // Unique constraint: nama must be unique within the same tahun_ajaran
            $table->unique(['nama', 'tahun_ajaran_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompoks');
    }
};
