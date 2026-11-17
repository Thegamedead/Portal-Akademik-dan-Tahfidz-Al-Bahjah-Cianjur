<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Consolidated migration: auth fields + master data fields
     */
    public function up(): void
    {
        Schema::create('gurus', function (Blueprint $table) {
            $table->id();

            // Auth module fields
            $table->string('niy')->unique();
            $table->string('nama');
            $table->date('tanggal_lahir');

            // Master data demographic fields
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('tempat_lahir');
            $table->string('kewarganegaraan')->default('Indonesia');
            $table->string('agama');
            $table->enum('status_pernikahan', ['Menikah', 'Belum Menikah', 'Cerai']);

            // Education fields
            $table->enum('pendidikan_terakhir', ['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3']);
            $table->string('program_studi')->nullable();
            $table->string('tahun_lulus', 4);

            // Position field
            $table->enum('jabatan', ['Guru Pembimbing Tahfidz', 'Guru Mapel', 'Kepala Sekolah', 'Tendik']);

            // Contact fields
            $table->string('no_telepon');
            $table->string('email')->nullable();
            $table->string('foto')->nullable();

            // Address fields (alamat_* prefix convention)
            $table->string('alamat_jalan')->nullable();
            $table->string('alamat_dusun')->nullable();
            $table->string('alamat_rt', 3)->nullable();
            $table->string('alamat_rw', 3)->nullable();
            $table->string('alamat_desa_kelurahan')->nullable();
            $table->string('alamat_kecamatan')->nullable();
            $table->string('alamat_kabupaten_kota')->nullable();
            $table->string('alamat_provinsi')->nullable();
            $table->string('alamat_kode_pos', 5)->nullable();

            // Auth/status fields
            $table->boolean('status_aktif')->default(true);
            $table->unsignedBigInteger('reset_oleh')->nullable(); // FK ke admins.id
            $table->timestamp('reset_pada')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('reset_oleh')->references('id')->on('admins')->nullOnDelete();

            // Indexes
            $table->index('status_aktif');
            $table->index('jabatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gurus');
    }
};
