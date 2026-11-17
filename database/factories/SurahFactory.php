<?php

namespace Database\Factories;

use App\Models\Surah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Surah>
 */
class SurahFactory extends Factory
{
    protected $model = Surah::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $surahCounter = 0;
        $surahCounter++;

        return [
            'nama_latin' => $this->faker->words(2, true),
            'nama_arab' => 'ال' . $this->faker->word(),
            'jumlah_ayat' => $this->faker->numberBetween(3, 286),
            'nomor_surah' => $surahCounter,
            'tempat_turun' => $this->faker->randomElement(['Makkiyah', 'Madaniyah']),
            'urutan_mushaf' => $surahCounter,
        ];
    }
}
