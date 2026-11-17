<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add exit tracking fields to siswas table for Registrasi Keluar Siswa feature.
     * Requirements: FR-1.2.1, FR-1.2.2, FR-1.2.3, FR-1.2.4, FR-1.2.5, NFR-2.2.3, NFR-2.4.1
     */
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Exit type enum field - positioned after status_aktif for logical grouping
            $table->enum('jenis_keluar', ['Lulus SMP', 'Pindah Sekolah', 'Meninggal', 'Keluar'])
                  ->nullable()
                  ->after('status_aktif')
                  ->comment('Jenis keberangkatan siswa dari sekolah');

            // Exit date field
            $table->date('tanggal_keluar')
                  ->nullable()
                  ->after('jenis_keluar')
                  ->comment('Tanggal siswa keluar dari sekolah');

            // Destination school (required for transfer cases)
            $table->string('tujuan_sekolah', 255)
                  ->nullable()
                  ->after('tanggal_keluar')
                  ->comment('Nama sekolah tujuan (wajib untuk jenis_keluar = Pindah Sekolah)');

            // Exit notes (required for deceased/expelled cases)
            $table->text('keterangan_keluar')
                  ->nullable()
                  ->after('tujuan_sekolah')
                  ->comment('Catatan keluar (wajib untuk jenis_keluar = Meninggal atau Keluar)');

            // Admin who processed the exit registration (audit trail)
            $table->unsignedBigInteger('diproses_oleh')
                  ->nullable()
                  ->after('keterangan_keluar')
                  ->comment('Admin yang memproses registrasi keluar');

            // Foreign key constraint to admins table with nullOnDelete
            $table->foreign('diproses_oleh')
                  ->references('id')
                  ->on('admins')
                  ->nullOnDelete();

            // Index on jenis_keluar for query optimization (filtering by exit type)
            $table->index('jenis_keluar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['diproses_oleh']);

            // Drop index
            $table->dropIndex(['jenis_keluar']);

            // Drop columns in reverse order
            $table->dropColumn([
                'diproses_oleh',
                'keterangan_keluar',
                'tujuan_sekolah',
                'tanggal_keluar',
                'jenis_keluar',
            ]);
        });
    }
};
