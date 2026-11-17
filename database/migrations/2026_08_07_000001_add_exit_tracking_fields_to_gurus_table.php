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
            // Exit type enum field
            $table->enum('jenis_keluar', ['Pensiun', 'Mutasi', 'Berhenti', 'Meninggal'])
                  ->nullable()
                  ->after('status_aktif')
                  ->comment('Jenis keluarnya guru dari sekolah');

            // Exit date field
            $table->date('tanggal_keluar')
                  ->nullable()
                  ->after('jenis_keluar')
                  ->comment('Tanggal guru keluar dari sekolah');

            // Exit notes
            $table->text('keterangan_keluar')
                  ->nullable()
                  ->after('tanggal_keluar')
                  ->comment('Catatan keluar');

            // Admin who processed the exit registration
            $table->unsignedBigInteger('diproses_oleh')
                  ->nullable()
                  ->after('keterangan_keluar')
                  ->comment('Admin yang memproses registrasi keluar');

            // Foreign key constraint
            $table->foreign('diproses_oleh')
                  ->references('id')
                  ->on('admins')
                  ->nullOnDelete();

            // Index
            $table->index('jenis_keluar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropForeign(['diproses_oleh']);
            $table->dropIndex(['jenis_keluar']);
            
            $table->dropColumn([
                'diproses_oleh',
                'keterangan_keluar',
                'tanggal_keluar',
                'jenis_keluar',
            ]);
        });
    }
};
