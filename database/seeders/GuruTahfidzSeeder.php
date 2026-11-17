<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guru;
use Illuminate\Support\Facades\DB;

class GuruTahfidzSeeder extends Seeder
{
    public function run(): void
    {
        $tempatLahir = [
            'Bandung', 'Jakarta', 'Bekasi', 'Bogor', 'Depok', 'Tangerang',
            'Cirebon', 'Sumedang', 'Tasikmalaya', 'Garut', 'Ciamis', 'Sukabumi'
        ];

        // 20 Nama Guru Laki-laki
        $namaLakiLaki = [
            'Ahmad Fauzi', 'Budi Santoso', 'Dedi Mulyadi', 'Eko Prasetyo', 'Hendra Gunawan',
            'Irfan Hakim', 'Joko Widodo', 'Kurnia Rahman', 'Lukman Hakim', 'Mulyadi Iskandar',
            'Nugroho Adi', 'Oki Setiana', 'Rahmat Hidayat', 'Rizki Ramadhan', 'Samsul Bahri',
            'Taufik Hidayat', 'Usman Ali', 'Wahyu Setiawan', 'Yusuf Ibrahim', 'Zaenal Arifin'
        ];

        // 20 Nama Guru Perempuan
        $namaPerempuan = [
            'Ani Suryani', 'Dewi Lestari', 'Erni Rahayu', 'Fitri Handayani', 'Indah Permata',
            'Kartika Sari', 'Lilis Suryani', 'Mega Wati', 'Nining Widiastuti', 'Putri Ayu',
            'Ratna Sari', 'Siti Nurhaliza', 'Titik Puspa', 'Uci Sanusi', 'Vina Panduwinata',
            'Wulan Guritno', 'Yanti Nuraini', 'Zahra Amalia', 'Aisyah Azzahra', 'Bunga Citra'
        ];

        DB::beginTransaction();

        try {
            $allGuru = [];

            // Generate Guru Laki-laki
            foreach ($namaLakiLaki as $index => $nama) {
                $allGuru[] = [
                    'nama' => $nama,
                    'jenis_kelamin' => 'Laki-laki',
                    'niy' => '2024' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                ];
            }

            // Generate Guru Perempuan
            foreach ($namaPerempuan as $index => $nama) {
                $allGuru[] = [
                    'nama' => $nama,
                    'jenis_kelamin' => 'Perempuan',
                    'niy' => '2024' . str_pad($index + 21, 3, '0', STR_PAD_LEFT),
                ];
            }

            foreach ($allGuru as $index => $guru) {
                $birthYear = rand(1980, 1995);
                $birthMonth = rand(1, 12);
                $birthDay = rand(1, 28);
                $tanggalLahir = sprintf('%d-%02d-%02d', $birthYear, $birthMonth, $birthDay);

                $mulaiKerjaYear = rand(2015, 2024);
                $tanggalMulaiKerja = sprintf('%d-%02d-%02d', $mulaiKerjaYear, rand(1, 12), rand(1, 28));

                // Generate phone number
                $phonePrefix = ['0812', '0813', '0821', '0822', '0851', '0852'];
                $noTelepon = $phonePrefix[array_rand($phonePrefix)] . rand(10000000, 99999999);

                // Generate email
                $emailName = strtolower(str_replace(' ', '.', $guru['nama']));
                $email = $emailName . '@tahfidz.sch.id';

                Guru::updateOrCreate(
                    ['niy' => $guru['niy']],
                    [
                        'nama' => $guru['nama'],
                        'jenis_kelamin' => $guru['jenis_kelamin'],
                        'tanggal_lahir' => $tanggalLahir,
                        'tempat_lahir' => $tempatLahir[$index % count($tempatLahir)],
                        'jabatan' => 'Guru Pembimbing Tahfidz',
                        'no_telepon' => $noTelepon,
                        'email' => $email,
                        'tanggal_mulai_kerja' => $tanggalMulaiKerja,
                        'status_aktif' => true,
                    ]
                );
            }

            DB::commit();
            $this->command->info('✅ Berhasil menambahkan 40 guru (20 laki-laki, 20 perempuan)!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
