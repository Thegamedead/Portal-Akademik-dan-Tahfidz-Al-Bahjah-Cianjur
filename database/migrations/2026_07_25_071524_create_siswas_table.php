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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();

            // Auth module fields
            $table->string('nis')->unique();
            $table->string('nisn')->nullable()->unique();
            $table->string('nama');
            $table->date('tanggal_lahir');

            // Master data demographic fields
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('tempat_lahir');
            $table->string('kewarganegaraan')->default('Indonesia');
            $table->string('agama');
            $table->string('nik', 16)->nullable();
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

            // Status fields
            $table->enum('status_akademik', ['Aktif', 'Lulus SMP', 'Pindah Sekolah', 'Keluar'])->default('Aktif');
            $table->enum('status_tahfidz', ['Sedang Berjalan', 'Lulus Program Tahfidz'])->default('Sedang Berjalan');
            $table->enum('status_penempatan', ['Terdaftar', 'Ditempatkan'])->default('Terdaftar');

            // Enrollment fields
            $table->enum('status_masuk', ['Baru', 'Pindahan'])->default('Baru');
            $table->date('tanggal_masuk')->nullable();
            $table->string('asal_sekolah')->nullable();
            $table->text('alamat_sekolah_asal')->nullable();

            // Auth/status fields
            $table->boolean('status_aktif')->default(true);
            $table->unsignedBigInteger('reset_oleh')->nullable(); // FK ke admins.id
            $table->timestamp('reset_pada')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys (from auth module)
            $table->foreign('reset_oleh')->references('id')->on('admins')->nullOnDelete();

            // Indexes
            $table->index('status_aktif');
            $table->index('status_akademik');
            $table->index('status_penempatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
