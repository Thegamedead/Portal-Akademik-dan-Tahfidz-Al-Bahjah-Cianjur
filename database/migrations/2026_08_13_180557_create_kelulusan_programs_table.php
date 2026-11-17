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
        Schema::create('kelulusan_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('guru_pengusul_id')->nullable()->constrained('gurus')->onDelete('set null');
            $table->foreignId('kelompok_id')->constrained('kelompoks')->onDelete('cascade')->comment('Kelompok terakhir (Tingkat Atas)');
            $table->date('tanggal_tuntas')->comment('Tanggal selesai target hafalan');
            $table->timestamp('tanggal_usul')->useCurrent();
            $table->enum('status_kelulusan', ['Diajukan', 'Disetujui', 'Ditolak'])->default('Diajukan');
            $table->text('catatan')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('tanggal_keputusan')->nullable();
            $table->timestamps();

            // Indexes untuk performa query
            $table->index('siswa_id');
            $table->index('status_kelulusan');
            $table->index('tanggal_usul');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelulusan_programs');
    }
};
