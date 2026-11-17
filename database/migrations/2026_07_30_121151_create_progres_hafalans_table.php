<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migration for progres_hafalans table - stores baseline and aggregated progress
     * per student per academic year.
     *
     * References: Requirements 1.1, 1.2, 1.4, 1.5, 4.1, 4.5 from requirements.md
     */
    public function up(): void
    {
        Schema::create('progres_hafalans', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->onDelete('cascade');

            // Baseline from placement test (nullable for students without initial hafalan)
            $table->json('baseline_juzs')->nullable()->comment('Array of juz IDs from placement test');
            $table->text('baseline_surahs')->nullable()->comment('Descriptive text of covered surahs');
            $table->text('catatan_kualitas_baseline')->nullable();
            $table->string('rekomendasi_tingkat')->nullable();

            // Aggregated progress (auto-calculated from setoran)
            $table->integer('total_juz_tuntas')->default(0)->comment('Count of completed juz');
            $table->integer('juz_berjalan')->nullable()->comment('Current incomplete juz');
            $table->decimal('juz_berjalan_percentage', 5, 2)->nullable()->comment('Percentage 0.00-100.00');
            $table->decimal('progress_toward_target', 5, 2)->default(0)->comment('% toward target_kelulusan');

            // Audit trail for baseline modifications (Admin only)
            $table->foreignId('baseline_modified_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('baseline_modified_at')->nullable();

            $table->timestamps();

            // Unique constraint: one record per siswa per tahun ajaran
            $table->unique(['siswa_id', 'tahun_ajaran_id'], 'progres_hafalans_unique');

            // Indexes for performance (sorting/filtering)
            $table->index('total_juz_tuntas');
            $table->index('progress_toward_target');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progres_hafalans');
    }
};
