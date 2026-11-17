<?php

namespace Database\Factories;

use App\Models\Guru;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GuruTugasTambahan>
 */
class GuruTugasTambahanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'guru_id' => Guru::factory(),
            'jenis_tugas' => fake()->randomElement([
                'Wali Kelas',
                'Pembina OSIS',
                'Pembina Pramuka',
                'Koordinator Kurikulum',
                'Koordinator Kesiswaan',
                'Bendahara',
            ]),
        ];
    }
}
