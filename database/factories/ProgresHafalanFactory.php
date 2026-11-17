<?php

namespace Database\Factories;

use App\Models\ProgresHafalan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk ProgresHafalan
 */
class ProgresHafalanFactory extends Factory
{
    protected $model = ProgresHafalan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalJuzTuntas = $this->faker->numberBetween(0, 15);
        $targetKelulusan = 5; // Default target

        return [
            'siswa_id' => Siswa::factory(),
            'tahun_ajaran_id' => TahunAjaran::factory(),
            'baseline_juzs' => null,
            'baseline_surahs' => null,
            'catatan_kualitas_baseline' => null,
            'rekomendasi_tingkat' => null,
            'total_juz_tuntas' => $totalJuzTuntas,
            'juz_berjalan' => $totalJuzTuntas < 30 ? $totalJuzTuntas + 1 : null,
            'juz_berjalan_percentage' => $totalJuzTuntas < 30 ? $this->faker->randomFloat(2, 10, 90) : null,
            'progress_toward_target' => min(round(($totalJuzTuntas / $targetKelulusan) * 100, 2), 100),
            'baseline_modified_by' => null,
            'baseline_modified_at' => null,
        ];
    }

    /**
     * Indicate that the progres has baseline data
     */
    public function withBaseline(array $juzIds = [1, 2, 3]): static
    {
        return $this->state(fn (array $attributes) => [
            'baseline_juzs' => $juzIds,
            'baseline_surahs' => 'Al-Fatihah, Al-Baqarah (1-141)',
            'catatan_kualitas_baseline' => 'Lancar, makhraj baik',
            'rekomendasi_tingkat' => 'Menengah',
        ]);
    }

    /**
     * Indicate that the progres has no progress
     */
    public function noProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_juz_tuntas' => 0,
            'juz_berjalan' => null,
            'juz_berjalan_percentage' => null,
            'progress_toward_target' => 0,
        ]);
    }

    /**
     * Indicate that the progres has high progress
     */
    public function highProgress(): static
    {
        $totalJuzTuntas = $this->faker->numberBetween(10, 20);

        return $this->state(fn (array $attributes) => [
            'total_juz_tuntas' => $totalJuzTuntas,
            'juz_berjalan' => $totalJuzTuntas + 1,
            'juz_berjalan_percentage' => $this->faker->randomFloat(2, 50, 95),
            'progress_toward_target' => 100, // Exceeded target
        ]);
    }
}
