<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel sekolahs: singleton untuk menyimpan profil identitas sekolah
     */
    public function up(): void
    {
        Schema::create('sekolahs', function (Blueprint $table) {
            $table->id();

            // Identitas sekolah
            $table->string('nama_sekolah');
            $table->string('npsn', 8);
            $table->enum('status_sekolah', ['Negeri', 'Swasta']);
            $table->string('akreditasi');
            $table->enum('naungan', ['Kemenag', 'Kemendikbud', 'Lainnya']);

            // SK pendirian
            $table->string('no_sk_pendirian');
            $table->date('tanggal_sk_pendirian');

            // SK operasional
            $table->string('no_sk_operasional');
            $table->date('tanggal_sk_operasional');

            // Kepala sekolah
            $table->string('nama_kepala_sekolah');
            $table->string('nip_kepala_sekolah');

            // Kontak
            $table->string('no_telepon');
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Address fields (alamat_* prefix convention)
            $table->string('alamat_jalan');
            $table->string('alamat_dusun')->nullable();
            $table->string('alamat_rt', 3)->nullable();
            $table->string('alamat_rw', 3)->nullable();
            $table->string('alamat_desa_kelurahan');
            $table->string('alamat_kecamatan');
            $table->string('alamat_kabupaten_kota');
            $table->string('alamat_provinsi');
            $table->string('alamat_kode_pos', 10);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekolahs');
    }
};
