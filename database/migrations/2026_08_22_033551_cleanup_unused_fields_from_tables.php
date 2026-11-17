<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Remove fields that are not used anywhere in the active codebase.
 *
 * Removed from `gurus`:
 *   - kewarganegaraan, agama, status_pernikahan, foto
 *   - alamat_jalan, alamat_dusun, alamat_rt, alamat_rw
 *   - alamat_desa_kelurahan, alamat_kecamatan, alamat_kabupaten_kota
 *   - alamat_provinsi, alamat_kode_pos
 *   - tahun_lulus, program_studi (education fields not shown in UI)
 *
 * Removed from `siswas`:
 *   - kewarganegaraan, agama, foto
 *   - alamat_jalan, alamat_dusun, alamat_rt, alamat_rw
 *   - alamat_desa_kelurahan, alamat_kecamatan, alamat_kabupaten_kota
 *   - alamat_provinsi, alamat_kode_pos
 */
return new class extends Migration
{
    /** Fields to remove from gurus */
    private array $guruFields = [
        'kewarganegaraan',
        'agama',
        'status_pernikahan',
        'foto',
        'program_studi',
        'tahun_lulus',
        'alamat_jalan',
        'alamat_dusun',
        'alamat_rt',
        'alamat_rw',
        'alamat_desa_kelurahan',
        'alamat_kecamatan',
        'alamat_kabupaten_kota',
        'alamat_provinsi',
        'alamat_kode_pos',
    ];

    /** Fields to remove from siswas */
    private array $siswaFields = [
        'kewarganegaraan',
        'agama',
        'foto',
        'alamat_jalan',
        'alamat_dusun',
        'alamat_rt',
        'alamat_rw',
        'alamat_desa_kelurahan',
        'alamat_kecamatan',
        'alamat_kabupaten_kota',
        'alamat_provinsi',
        'alamat_kode_pos',
    ];

    public function up(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            foreach ($this->guruFields as $field) {
                if (Schema::hasColumn('gurus', $field)) {
                    $table->dropColumn($field);
                }
            }
        });

        Schema::table('siswas', function (Blueprint $table) {
            foreach ($this->siswaFields as $field) {
                if (Schema::hasColumn('siswas', $field)) {
                    $table->dropColumn($field);
                }
            }
        });
    }

    public function down(): void
    {
        // Restore gurus fields
        Schema::table('gurus', function (Blueprint $table) {
            $table->string('kewarganegaraan')->default('Indonesia')->nullable()->after('tempat_lahir');
            $table->string('agama')->nullable()->after('kewarganegaraan');
            $table->enum('status_pernikahan', ['Menikah', 'Belum Menikah', 'Cerai'])->nullable()->after('agama');
            $table->string('foto')->nullable();
            $table->string('program_studi')->nullable();
            $table->string('tahun_lulus', 4)->nullable();
            $table->string('alamat_jalan')->nullable();
            $table->string('alamat_dusun')->nullable();
            $table->string('alamat_rt', 3)->nullable();
            $table->string('alamat_rw', 3)->nullable();
            $table->string('alamat_desa_kelurahan')->nullable();
            $table->string('alamat_kecamatan')->nullable();
            $table->string('alamat_kabupaten_kota')->nullable();
            $table->string('alamat_provinsi')->nullable();
            $table->string('alamat_kode_pos', 5)->nullable();
        });

        // Restore siswas fields
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('kewarganegaraan')->default('Indonesia')->nullable()->after('tempat_lahir');
            $table->string('agama')->nullable()->after('kewarganegaraan');
            $table->string('foto')->nullable();
            $table->string('alamat_jalan')->nullable();
            $table->string('alamat_dusun')->nullable();
            $table->string('alamat_rt', 3)->nullable();
            $table->string('alamat_rw', 3)->nullable();
            $table->string('alamat_desa_kelurahan')->nullable();
            $table->string('alamat_kecamatan')->nullable();
            $table->string('alamat_kabupaten_kota')->nullable();
            $table->string('alamat_provinsi')->nullable();
            $table->string('alamat_kode_pos', 5)->nullable();
        });
    }
};
