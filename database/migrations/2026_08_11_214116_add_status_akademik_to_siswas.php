<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * ADD: tanggal_lulus_akademik untuk tracking kapan lulus SMP
     *
     * Note: status_akademik sudah ada di create_siswas_table migration,
     * kita hanya tambah tanggal lulus akademik saja.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('siswas', 'tanggal_lulus_akademik')) {
            Schema::table('siswas', function (Blueprint $table) {
                // Tanggal lulus akademik (SMP)
                $table->date('tanggal_lulus_akademik')
                    ->nullable()
                    ->after('status_tahfidz')
                    ->comment('Tanggal kelulusan akademik (tamat SMP kelas 9)');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            if (Schema::hasColumn('siswas', 'tanggal_lulus_akademik')) {
                $table->dropColumn('tanggal_lulus_akademik');
            }
        });
    }
};
