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
        Schema::table('riwayat_kelompoks', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['kelompok_ke_id']);

            // Modify column to be nullable
            $table->foreignId('kelompok_ke_id')->nullable()->change();

            // Re-add foreign key constraint with nullable
            $table->foreign('kelompok_ke_id')
                  ->references('id')
                  ->on('kelompoks')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_kelompoks', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['kelompok_ke_id']);

            // Revert column to NOT NULL
            $table->foreignId('kelompok_ke_id')->nullable(false)->change();

            // Re-add foreign key constraint
            $table->foreign('kelompok_ke_id')
                  ->references('id')
                  ->on('kelompoks')
                  ->onDelete('cascade');
        });
    }
};
