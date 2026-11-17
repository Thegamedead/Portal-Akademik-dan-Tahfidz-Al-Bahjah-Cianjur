<?php

namespace Database\Factories;

use App\Models\Juz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Juz>
 */
class JuzFactory extends Factory
{
    protected $model = Juz::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $juzCounter = 0;
        $juzCounter++;

        return [
            'juz_ke' => $juzCounter,
            'urutan_hafalan' => $juzCounter,
            'jumlah_ayat' => $this->faker->numberBetween(100, 300), // Realistic range for juz ayat count
            'awal_ayat' => 1,
            'akhir_ayat' => $this->faker->numberBetween(100, 300),
        ];
    }
}
