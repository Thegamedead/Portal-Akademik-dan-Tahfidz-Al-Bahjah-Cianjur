<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migration for capaian_juzs table - stores detailed juz completion data
     * per student per juz.
     *
     * References: Requirements 3.1, 3.2, 3.3, 3.4, 4.2 from requirements.md
     */
    public function up(): void
    {
        Schema::create('capaian_juzs', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('progres_hafalan_id')->constrained('progres_hafalans')->onDelete('cascade');
            $table->foreignId('juz_id')->constrained('juzs')->onDelete('cascade');

            // Coverage data
            $table->integer('ayat_tercakup')->default(0)->comment('Number of unique ayat covered');
            $table->integer('total_ayat')->comment('From Referensi Quran (denormalized for performance)');
            $table->decimal('persentase', 5, 2)->default(0)->comment('(ayat_tercakup / total_ayat) * 100');

            // Completion status
            $table->boolean('status_tuntas')->default(false)->comment('True when persentase = 100%');
            $table->date('tanggal_tuntas')->nullable()->comment('Date when juz became 100% complete');

            // Detailed coverage (for integrity validation)
            $table->json('detail_ayat_tercakup')->comment('Array of [surah_id, ayat_mulai, ayat_selesai]');

            $table->timestamps();

            // Unique constraint: one record per progres_hafalan per juz
            $table->unique(['progres_hafalan_id', 'juz_id'], 'capaian_juzs_unique');

            // Indexes for performance (filtering/sorting)
            $table->index('status_tuntas');
            $table->index('persentase');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capaian_juzs');
    }
};
