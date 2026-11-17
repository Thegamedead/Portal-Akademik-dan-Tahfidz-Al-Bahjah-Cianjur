<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parameter_konfigurasis', function (Blueprint $table) {
            $table->id();

            // Target kelulusan & tingkat
            $table->integer('target_kelulusan')->default(5);
            $table->json('batas_tingkat'); // {Dasar: {juz_mulai, juz_selesai}, Menengah: {...}, Atas: {...}}

            // Kapasitas kelompok
            $table->integer('kapasitas_kelompok_min')->default(6);
            $table->integer('kapasitas_kelompok_max')->default(12);

            // Komponen penilaian (formula: skor terendah dari 4 komponen)
            $table->string('formula_predikat')->default('min'); // 'min' = skor terendah

            // Notifikasi
            $table->integer('ambang_alfa_berturut')->default(3); // Default 3 sesi berturut-turut

            // Jadwal efektif kegiatan tahfidz (JSON array of day names)
            $table->json('hari_efektif_tahfidz')->nullable(); // ['Senin', 'Selasa', ...]

            $table->timestamps();
        });

        // No default data inserted - admin configures manually via UI

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parameter_konfigurasis');
    }
};
