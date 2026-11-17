<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Mengubah format nama kelas dari "7-7A" atau "8-8A" menjadi "7A" atau "8A"
     * Mengambil bagian setelah tanda "-" terakhir
     */
    public function up(): void
    {
        // Update semua kelas yang memiliki format dengan tanda "-"
        DB::table('kelas')->get()->each(function ($kelas) {
            if (str_contains($kelas->nama_kelas, '-')) {
                // Ambil bagian setelah tanda "-" terakhir
                $namaKelasBaruFormat = substr(strrchr($kelas->nama_kelas, '-'), 1);

                DB::table('kelas')
                    ->where('id', $kelas->id)
                    ->update(['nama_kelas' => $namaKelasBaruFormat]);

                echo "Updated: {$kelas->nama_kelas} → {$namaKelasBaruFormat}\n";
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * Tidak bisa dikembalikan karena format asli sudah hilang
     */
    public function down(): void
    {
        // Tidak ada rollback - silakan restore dari backup database jika diperlukan
    }
};
