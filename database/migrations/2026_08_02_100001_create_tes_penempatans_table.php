<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tes_penempatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('guru_penguji_id')->constrained('gurus')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            
            $table->date('tanggal_tes');
            $table->time('waktu_tes')->nullable();
            
            $table->enum('status', ['Terjadwal', 'Selesai', 'Dibatalkan'])->default('Terjadwal');
            $table->enum('rekomendasi_tingkat', ['Dasar', 'Menengah', 'Atas'])->nullable();
            $table->text('catatan_kualitas')->nullable();
            $table->text('alasan_pembatalan')->nullable();
            
            $table->foreignId('final_kelompok_id')->nullable()->constrained('kelompoks')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('ditempatkan_at')->nullable();
            $table->foreignId('penempatan_by')->nullable()->constrained('admins')->nullOnDelete();
            
            $table->timestamps();
            
            $table->index(['siswa_id', 'status']);
            $table->index(['guru_penguji_id', 'status']);
            $table->index('tanggal_tes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tes_penempatans');
    }
};
