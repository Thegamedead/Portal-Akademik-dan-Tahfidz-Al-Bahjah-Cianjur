<?php

namespace Database\Factories;

use App\Models\Guru;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kelompok>
 */
class KelompokFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => 'Kelompok ' . fake()->randomElement(['Al-Fatihah', 'Al-Baqarah', 'Ali Imran', 'An-Nisa', 'Al-Maidah', 'Al-Anam', 'Al-Araf', 'Al-Anfal']) . ' ' . fake()->numberBetween(1, 9999),
            'jenis' => fake()->randomElement(['Banin', 'Banat']),
            'tingkat' => fake()->randomElement(['Dasar', 'Menengah', 'Atas']),
            'guru_pembimbing_id' => Guru::factory()->pembimbingTahfidz(),
            'tahun_ajaran_id' => TahunAjaran::factory(),
            'kapasitas_min' => 6,
            'kapasitas_maks' => 12,
            'jadwal_hari' => fake()->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']),
            'jadwal_jam' => fake()->randomElement(['07:00-09:00', '09:00-11:00', '13:00-15:00']),
        ];
    }

    public function banin(): static
    {
        return $this->state(['jenis' => 'Banin']);
    }

    public function banat(): static
    {
        return $this->state(['jenis' => 'Banat']);
    }

    public function dasar(): static
    {
        return $this->state(['tingkat' => 'Dasar']);
    }

    public function menengah(): static
    {
        return $this->state(['tingkat' => 'Menengah']);
    }

    public function atas(): static
    {
        return $this->state(['tingkat' => 'Atas']);
    }
}
