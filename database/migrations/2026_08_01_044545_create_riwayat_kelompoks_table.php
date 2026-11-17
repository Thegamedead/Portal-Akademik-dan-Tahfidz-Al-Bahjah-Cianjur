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
        Schema::create('riwayat_kelompoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('kelompok_dari_id')->nullable()->constrained('kelompoks')->onDelete('set null');
            $table->foreignId('kelompok_ke_id')->constrained('kelompoks')->onDelete('cascade');
            $table->dateTime('tanggal');
            $table->string('alasan'); // "Kenaikan Tingkat", "Penempatan Awal", "Rollover Tahun Ajaran"
            $table->foreignId('disetujui_oleh')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index('siswa_id');
            $table->index('kelompok_dari_id');
            $table->index('kelompok_ke_id');
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_kelompoks');
    }
};
