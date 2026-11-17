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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')->constrained('kelompoks')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('status_kehadiran', ['Hadir', 'Izin', 'Sakit', 'Alfa']);
            $table->foreignId('guru_pengisi_id')->constrained('gurus')->onDelete('restrict');
            $table->unsignedTinyInteger('alfa_berturut_turut')->default(0);
            $table->boolean('override_admin')->default(false);
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamps();

            // Unique constraint: prevent duplicate attendance for same student-kelompok-date
            $table->unique(['kelompok_id', 'siswa_id', 'tanggal'], 'unique_absensi');

            // Indexes for query performance
            $table->index(['kelompok_id', 'tanggal'], 'idx_kelompok_tanggal');
            $table->index(['siswa_id', 'tanggal'], 'idx_siswa_tanggal');
            $table->index('guru_pengisi_id', 'idx_guru_pengisi');
            $table->index('status_kehadiran', 'idx_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
