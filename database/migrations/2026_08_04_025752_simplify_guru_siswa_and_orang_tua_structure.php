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
        // 1. Remove agama and all alamat_* fields from gurus table
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn([
                'agama',
                'alamat_jalan',
                'alamat_dusun',
                'alamat_rt',
                'alamat_rw',
                'alamat_desa_kelurahan',
                'alamat_kecamatan',
                'alamat_kabupaten_kota',
                'alamat_provinsi',
                'alamat_kode_pos',
            ]);
        });

        // 2. Remove all alamat_* fields from siswas table
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn([
                'alamat_jalan',
                'alamat_dusun',
                'alamat_rt',
                'alamat_rw',
                'alamat_desa_kelurahan',
                'alamat_kecamatan',
                'alamat_kabupaten_kota',
                'alamat_provinsi',
                'alamat_kode_pos',
            ]);
        });

        // 3. Add nama_ayah and nama_ibu to siswas table
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('nama_ayah')->nullable()->after('tempat_lahir');
            $table->string('nama_ibu')->nullable()->after('nama_ayah');
        });

        // 4. Drop orang_tua_walis table (no longer needed)
        Schema::dropIfExists('orang_tua_walis');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore orang_tua_walis table
        Schema::create('orang_tua_walis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->enum('jenis', ['Ayah', 'Ibu', 'Wali']);
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('pendidikan', 50);
            $table->string('pekerjaan', 100);
            $table->string('penghasilan', 50);
            $table->timestamps();
        });

        // Remove nama_ayah and nama_ibu from siswas
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['nama_ayah', 'nama_ibu']);
        });

        // Restore alamat fields to siswas
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('alamat_jalan')->nullable();
            $table->string('alamat_dusun', 100)->nullable();
            $table->string('alamat_rt', 5)->nullable();
            $table->string('alamat_rw', 5)->nullable();
            $table->string('alamat_desa_kelurahan', 100)->nullable();
            $table->string('alamat_kecamatan', 100)->nullable();
            $table->string('alamat_kabupaten_kota', 100)->nullable();
            $table->string('alamat_provinsi', 100)->nullable();
            $table->string('alamat_kode_pos', 10)->nullable();
        });

        // Restore agama and alamat fields to gurus
        Schema::table('gurus', function (Blueprint $table) {
            $table->string('agama', 50)->nullable();
            $table->string('alamat_jalan')->nullable();
            $table->string('alamat_dusun', 100)->nullable();
            $table->string('alamat_rt', 5)->nullable();
            $table->string('alamat_rw', 5)->nullable();
            $table->string('alamat_desa_kelurahan', 100)->nullable();
            $table->string('alamat_kecamatan', 100)->nullable();
            $table->string('alamat_kabupaten_kota', 100)->nullable();
            $table->string('alamat_provinsi', 100)->nullable();
            $table->string('alamat_kode_pos', 10)->nullable();
        });
    }
};
