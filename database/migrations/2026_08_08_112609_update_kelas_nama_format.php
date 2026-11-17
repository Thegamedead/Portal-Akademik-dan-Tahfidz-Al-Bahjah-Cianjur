<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update format nama kelas dari "7-7A" menjadi "7A"
        $kelasRecords = DB::table('kelas')->whereNotNull('nama_kelas')->get();

        foreach ($kelasRecords as $kelas) {
            // Ambil bagian setelah tanda "-" terakhir
            $namaBaru = substr(strrchr($kelas->nama_kelas, '-'), 1);

            // Jika ada hasil (ada tanda "-"), update record
            if ($namaBaru !== false && $namaBaru !== '') {
                DB::table('kelas')
                    ->where('id', $kelas->id)
                    ->update(['nama_kelas' => $namaBaru]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak ada rollback karena data format lama sudah tidak tersimpan
        // Migration ini hanya mengubah format display, bukan struktur data
    }
};
