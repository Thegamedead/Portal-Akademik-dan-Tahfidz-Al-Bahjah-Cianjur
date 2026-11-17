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
        Schema::create('rollover_audit_logs', function (Blueprint $table) {
            $table->id();

            // Who and when
            $table->foreignId('initiated_by')
                  ->constrained('admins')
                  ->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();

            // What changed
            $table->foreignId('source_tahun_ajaran_id')
                  ->constrained('tahun_ajarans')
                  ->onDelete('cascade');
            $table->foreignId('target_tahun_ajaran_id')
                  ->constrained('tahun_ajarans')
                  ->onDelete('cascade');

            // Impact metrics
            $table->integer('kelas_7_to_8_count')->default(0);
            $table->integer('kelas_8_to_9_count')->default(0);
            $table->integer('graduated_count')->default(0);
            $table->integer('kelompok_cleared_count')->default(0);

            // Execution status
            $table->enum('status', [
                'in_progress',
                'completed',
                'failed',
                'rolled_back'
            ])->default('in_progress');
            $table->text('error_message')->nullable();
            $table->text('error_trace')->nullable();

            // Rollback tracking
            $table->foreignId('rolled_back_by')
                  ->nullable()
                  ->constrained('admins')
                  ->onDelete('set null');
            $table->timestamp('rolled_back_at')->nullable();

            $table->timestamps();

            // Indexes for performance optimization
            $table->index('status');
            $table->index('started_at');
            $table->index(['source_tahun_ajaran_id', 'target_tahun_ajaran_id'], 'idx_rollover_tahun_ajaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rollover_audit_logs');
    }
};
