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
        Schema::table('kelas', function (Blueprint $table) {
            $table->string('ruang_kelas')->nullable()->after('nama_kelas');
            $table->integer('jumlah_siswa')->nullable()->comment('Kapasitas maksimal siswa')->after('ruang_kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn(['ruang_kelas', 'jumlah_siswa']);
        });
    }
};
