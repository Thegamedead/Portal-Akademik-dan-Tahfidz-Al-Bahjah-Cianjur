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
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn(['kewarganegaraan', 'status_pernikahan', 'foto', 'tahun_lulus']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->string('kewarganegaraan')->nullable()->after('tanggal_lahir');
            $table->string('status_pernikahan')->nullable()->after('kewarganegaraan');
            $table->string('foto')->nullable()->after('status_pernikahan');
            $table->year('tahun_lulus')->nullable()->after('pendidikan_terakhir');
        });
    }
};
