<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel kelas: struktur rombongan belajar formal SMP (Tingkat 7/8/9 + Rombel A/B/C/D)
     */
    public function up(): void
    {
        if (Schema::hasTable('kelas')) {
            return;
        }

        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->enum('tingkat', ['7', '8', '9']);
            $table->string('rombel', 2);
            $table->foreignId('wali_kelas_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->unsignedSmallInteger('kapasitas')->default(30);
            $table->timestamps();

            // Unique constraint
            $table->unique(['tahun_ajaran_id', 'tingkat', 'rombel']);

            // Indexes
            $table->index('tingkat');
            $table->index('tahun_ajaran_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
