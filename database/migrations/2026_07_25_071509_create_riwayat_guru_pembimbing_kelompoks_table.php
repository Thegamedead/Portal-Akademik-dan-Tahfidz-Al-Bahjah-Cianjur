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
        Schema::create('riwayat_guru_pembimbing_kelompoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')->constrained('kelompoks')->cascadeOnDelete();
            $table->foreignId('guru_dari_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('guru_ke_id')->constrained('gurus')->cascadeOnDelete();
            $table->date('tanggal_efektif');
            $table->text('alasan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_guru_pembimbing_kelompoks');
    }
};
