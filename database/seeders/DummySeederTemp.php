<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Guru;

class DummySeederTemp extends Seeder
{
    public function run(): void
    {
        Siswa::factory()->count(100)->create([
            'status_penempatan' => 'Belum Ditempatkan',
            'status_akademik' => 'Aktif',
            'kelompok_id' => null,
            'punya_hafalan_sebelumnya' => fn() => fake()->boolean(40), // 40% punya hafalan
        ]);

        Guru::factory()->count(50)->create([
            'status_aktif' => true,
        ]);
    }
}
