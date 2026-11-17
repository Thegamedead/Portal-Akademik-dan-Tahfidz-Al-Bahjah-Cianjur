<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan foreign key constraint untuk kolom guru_id di tabel admins.
     * Constraint ini memastikan guru_id selalu merujuk ke record valid di tabel gurus,
     * atau NULL jika guru dihapus (ON DELETE SET NULL).
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->foreign('guru_id')
                  ->references('id')
                  ->on('gurus')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['guru_id']);
        });
    }
};
