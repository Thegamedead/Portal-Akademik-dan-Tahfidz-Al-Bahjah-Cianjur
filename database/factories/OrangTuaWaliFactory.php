<?php

namespace Database\Factories;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrangTuaWali>
 */
class OrangTuaWaliFactory extends Factory
{
    public function definition(): array
    {
        return [
            'siswa_id' => Siswa::factory(),
            'jenis' => fake()->randomElement(['Ayah', 'Ibu', 'Wali']),
            'nama' => fake()->name(),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->dateTimeBetween('-60 years', '-30 years')->format('Y-m-d'),
            'pendidikan' => fake()->randomElement(['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2']),
            'pekerjaan' => fake()->randomElement(['Wiraswasta', 'Karyawan Swasta', 'PNS', 'Guru', 'Buruh', 'Petani', 'Pedagang', 'Ibu Rumah Tangga']),
            'penghasilan' => fake()->numberBetween(1000000, 10000000),
            'no_telepon' => fake()->numerify('08##########'),
            'email' => fake()->optional(0.5)->safeEmail(),
            'nik' => fake()->optional(0.7)->numerify('################'),
        ];
    }

    public function ayah(): static
    {
        return $this->state(['jenis' => 'Ayah']);
    }

    public function ibu(): static
    {
        return $this->state(['jenis' => 'Ibu']);
    }

    public function wali(): static
    {
        return $this->state(['jenis' => 'Wali']);
    }
}
