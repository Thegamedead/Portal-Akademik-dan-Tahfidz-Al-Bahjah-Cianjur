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
        Schema::create('orang_tua_walis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->enum('jenis', ['Ayah', 'Ibu', 'Wali']);
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('pendidikan');
            $table->string('pekerjaan');
            $table->string('penghasilan');
            $table->string('no_telepon');
            $table->string('email')->nullable();
            $table->string('nik', 16)->nullable();
            $table->timestamps();

            // Unique constraint: one siswa cannot have duplicate jenis
            $table->unique(['siswa_id', 'jenis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orang_tua_walis');
    }
};
