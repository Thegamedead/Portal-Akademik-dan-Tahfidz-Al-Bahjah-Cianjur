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
        Schema::create('usulan_kenaikan_tingkats', function (Blueprint $table) {
            $table->id();

            // Student and proposal information
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('guru_pengusul_id');

            // Level information
            $table->enum('tingkat_asal', ['Dasar', 'Menengah', 'Atas']);
            $table->enum('tingkat_tujuan', ['Dasar', 'Menengah', 'Atas']);

            // Group information
            $table->unsignedBigInteger('kelompok_asal_id')->nullable();
            $table->unsignedBigInteger('kelompok_tujuan_id')->nullable();

            // Status and justification
            $table->enum('status_usulan', ['Diajukan', 'Disetujui', 'Ditempatkan', 'Ditolak'])->default('Diajukan');
            $table->text('alasan');
            $table->dateTime('tanggal_usul');

            // Admin decision fields
            $table->unsignedBigInteger('admin_verifikator_id')->nullable();
            $table->dateTime('tanggal_keputusan')->nullable();
            $table->text('catatan_keputusan')->nullable();

            // Placement timestamp
            $table->dateTime('tanggal_penempatan')->nullable();

            $table->timestamps();

            // Foreign key constraints
            $table->foreign('siswa_id')->references('id')->on('siswas')->cascadeOnDelete();
            $table->foreign('guru_pengusul_id')->references('id')->on('gurus')->cascadeOnDelete();
            $table->foreign('kelompok_asal_id')->references('id')->on('kelompoks')->nullOnDelete();
            $table->foreign('kelompok_tujuan_id')->references('id')->on('kelompoks')->nullOnDelete();
            $table->foreign('admin_verifikator_id')->references('id')->on('admins')->nullOnDelete();

            // Indexes for performance
            $table->index('siswa_id');
            $table->index('guru_pengusul_id');
            $table->index('status_usulan');
            $table->index('tingkat_tujuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usulan_kenaikan_tingkats');
    }
};
