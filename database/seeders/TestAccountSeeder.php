<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class TestAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat akun test untuk Guru
        Guru::updateOrCreate(
            ['niy' => 'TEST001'],
            [
                'nama' => 'Ahmad Ustadz',
                'niy' => 'TEST001',
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '1985-05-15',
                'tempat_lahir' => 'Jakarta',
                'jabatan' => 'Guru Pembimbing Tahfidz',
                'pendidikan_terakhir' => 'S1',
                'tahun_lulus' => '2008',
                'agama' => 'Islam',
                'status_pernikahan' => 'Menikah',
                'no_telepon' => '081234567890',
                'email' => 'ahmad.ustadz@test.com',
                'status_aktif' => true,
            ]
        );

        // Buat akun test untuk Siswa
        Siswa::updateOrCreate(
            ['nis' => 'TEST001'],
            [
                'nama' => 'Muhammad Rafi',
                'nis' => 'TEST001',
                'nisn' => '1234567890',
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '2010-08-20',
                'tempat_lahir' => 'Bandung',
                'status_masuk' => 'Baru',
                'status_akademik' => 'Aktif',
                'status_tahfidz' => 'Sedang Berjalan',
                'status_penempatan' => 'Belum Ditempatkan',
                'agama' => 'Islam',
                'tanggal_masuk' => '2024-07-15',
            ]
        );

        echo "\n✅ Akun test berhasil dibuat!\n\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📝 LOGIN GURU\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "   URL         : http://127.0.0.1:8000/guru/login\n";
        echo "   NIY         : TEST001\n";
        echo "   Tanggal Lahir: 1985-05-15\n\n";

        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📝 LOGIN SISWA / ORANG TUA\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "   URL         : http://127.0.0.1:8000/siswa/login\n";
        echo "   NIS         : TEST001\n";
        echo "   Tanggal Lahir: 2010-08-20\n\n";
    }
}
