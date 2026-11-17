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
        Schema::create('riwayat_guru_pembimbings', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel kelompoks (CASCADE on delete)
            $table->foreignId('kelompok_id')
                  ->constrained('kelompoks')
                  ->cascadeOnDelete();
                  
            // Guru lama (nullable) dan Guru baru
            $table->foreignId('guru_dari_id')
                  ->nullable()
                  ->constrained('gurus')
                  ->nullOnDelete();
                  
            $table->foreignId('guru_ke_id')
                  ->nullable()
                  ->constrained('gurus')
                  ->nullOnDelete();
                  
            $table->date('tanggal_efektif');
            
            $table->text('alasan')->nullable();
            
            // Siapa admin yang melakukan perubahan (opsional)
            $table->foreignId('admin_id')
                  ->nullable()
                  ->constrained('admins')
                  ->nullOnDelete();
                  
            $table->timestamps();
            
            // Indexes untuk pencarian cepat
            $table->index(['kelompok_id', 'tanggal_efektif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_guru_pembimbings');
    }
};
