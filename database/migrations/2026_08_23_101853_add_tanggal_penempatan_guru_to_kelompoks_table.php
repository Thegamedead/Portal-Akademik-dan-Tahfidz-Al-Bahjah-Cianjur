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
        Schema::table('kelompoks', function (Blueprint $table) {
            $table->date('tanggal_penempatan_guru')->nullable()->after('guru_pembimbing_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelompoks', function (Blueprint $table) {
            $table->dropColumn('tanggal_penempatan_guru');
        });
    }
};
