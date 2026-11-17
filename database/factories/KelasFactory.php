<?php

namespace Database\Factories;

use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

class KelasFactory extends Factory
{
    protected $model = Kelas::class;

    public function definition(): array
    {
        return [
            'tingkat' => $this->faker->randomElement([7, 8, 9]),
            'nama_kelas' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'ruang_kelas' => $this->faker->optional()->numerify('R-##'),
            'jumlah_siswa' => $this->faker->numberBetween(20, 35),
            'nama_wali_kelas' => $this->faker->name(),
        ];
    }
}
