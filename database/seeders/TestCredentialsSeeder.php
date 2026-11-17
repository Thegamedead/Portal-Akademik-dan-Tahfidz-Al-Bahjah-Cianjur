<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelompok;
use App\Models\TahunAjaran;

class TestCredentialsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Test Guru
        $guru = Guru::firstOrCreate(
            ['niy' => 'GURU-001'],
            [
                'nama' => 'Ustadz Fulan, S.Pd',
                'tanggal_lahir' => '1990-01-01',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Jakarta',
                'jabatan' => 'Guru Pembimbing Tahfidz',
                'no_telepon' => '081234567890',
                'email' => 'ustadz.fulan@test.com',
                'tanggal_mulai_kerja' => '2015-07-01',
                'status_aktif' => true,
                'akun_aktif' => true,
            ]
        );

        $tahunAjaran = TahunAjaran::firstOrCreate(
            ['label' => '2023/2024'],
            [
                'tanggal_mulai' => '2023-07-01',
                'tanggal_selesai' => '2024-06-30',
                'status' => true
            ]
        );

        // 2. Create a Test Kelompok
        $kelompok = Kelompok::firstOrCreate(
            ['nama' => 'Kelompok Abu Bakar', 'tahun_ajaran_id' => $tahunAjaran->id],
            [
                'jenis' => 'Banin',
                'tingkat' => 'Menengah',
                'guru_pembimbing_id' => $guru->id,
            ]
        );

        // 3. Create a Test Siswa
        $siswa = Siswa::firstOrCreate(
            ['nis' => 'SISWA-001'],
            [
                'nisn' => '001001001',
                'nama' => 'Ahmad Fulan',
                'tanggal_lahir' => '2010-01-01',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Jakarta',
                'status_akademik' => 'Aktif',
                'status_penempatan' => 'Ditempatkan',
                'status_masuk' => 'Baru',
                'tanggal_masuk' => '2022-07-15',
                'kelompok_id' => $kelompok->id,
            ]
        );

        $this->command->info('');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  GURU LOGIN CREDENTIALS (FOR TESTING)');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  NIY (Username) : ' . $guru->niy);
        $this->command->info('  Tanggal Lahir  : ' . $guru->tanggal_lahir->format('Y-m-d'));
        $this->command->info('  URL            : /guru/login');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('');

        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  SISWA LOGIN CREDENTIALS (FOR TESTING)');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('  NIS (Username) : ' . $siswa->nis);
        $this->command->info('  Tanggal Lahir  : ' . $siswa->tanggal_lahir->format('Y-m-d'));
        $this->command->info('  URL            : /siswa/login');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('');
    }
}
