<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambah dua kolom baru pada tabel gurus dan siswas:
     *  - akun_aktif       : bool — apakah pengguna boleh login (terpisah dari status_aktif akademik)
     *  - keterangan_akun  : string — catatan admin, e.g. "Password diubah sendiri oleh pengguna"
     */
    public function up(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->boolean('akun_aktif')->default(true)->after('status_aktif');
            $table->string('keterangan_akun')->nullable()->after('password_text');
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->boolean('akun_aktif')->default(true)->after('status_aktif');
            $table->string('keterangan_akun')->nullable()->after('password_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn(['akun_aktif', 'keterangan_akun']);
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['akun_aktif', 'keterangan_akun']);
        });
    }
};
