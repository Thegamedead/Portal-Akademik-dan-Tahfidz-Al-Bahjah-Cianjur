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
        Schema::create('transkrip_capaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->onDelete('cascade');

            // Snapshot metadata
            $table->date('tanggal_generate');
            $table->string('reason'); // 'target_kelulusan_change', 'batas_tingkat_change', 'manual_admin'
            $table->foreignId('generated_by')->nullable()->constrained('admins');

            // Snapshot data (serialized progress at that point in time)
            $table->integer('total_juz_tuntas_snapshot');
            $table->json('detail_capaian_snapshot'); // Full progress data structure

            // System parameters at time of snapshot (for reference)
            $table->integer('target_kelulusan_at_snapshot');
            $table->json('batas_tingkat_at_snapshot');

            $table->timestamps();

            // Indexes
            $table->index('siswa_id');
            $table->index('tanggal_generate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transkrip_capaians');
    }
};
