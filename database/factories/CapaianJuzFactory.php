<?php

namespace Database\Factories;

use App\Models\CapaianJuz;
use App\Models\Juz;
use App\Models\ProgresHafalan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CapaianJuz>
 */
class CapaianJuzFactory extends Factory
{
    protected $model = CapaianJuz::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalAyat = $this->faker->numberBetween(50, 200);
        $ayatTercakup = $this->faker->numberBetween(0, $totalAyat);
        $persentase = $totalAyat > 0 ? ($ayatTercakup / $totalAyat) * 100 : 0;
        $statusTuntas = $persentase >= 100;

        return [
            'progres_hafalan_id' => ProgresHafalan::factory(),
            'juz_id' => Juz::factory(),
            'ayat_tercakup' => $ayatTercakup,
            'total_ayat' => $totalAyat,
            'persentase' => round($persentase, 2),
            'status_tuntas' => $statusTuntas,
            'tanggal_tuntas' => $statusTuntas ? $this->faker->dateTimeBetween('-1 year', 'now') : null,
            'detail_ayat_tercakup' => [
                [
                    'surah_id' => 1,
                    'ayat_mulai' => 1,
                    'ayat_selesai' => $ayatTercakup,
                ],
            ],
        ];
    }

    /**
     * Indicate that the juz is complete (100% tuntas).
     *
     * @return static
     */
    public function tuntas(): static
    {
        return $this->state(function (array $attributes) {
            $totalAyat = $attributes['total_ayat'];

            return [
                'ayat_tercakup' => $totalAyat,
                'persentase' => 100.00,
                'status_tuntas' => true,
                'tanggal_tuntas' => $this->faker->dateTimeBetween('-1 year', 'now'),
            ];
        });
    }

    /**
     * Indicate that the juz is in progress (partial coverage).
     *
     * @return static
     */
    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            $totalAyat = $attributes['total_ayat'];
            $ayatTercakup = $this->faker->numberBetween(1, $totalAyat - 1);
            $persentase = ($ayatTercakup / $totalAyat) * 100;

            return [
                'ayat_tercakup' => $ayatTercakup,
                'persentase' => round($persentase, 2),
                'status_tuntas' => false,
                'tanggal_tuntas' => null,
            ];
        });
    }

    /**
     * Indicate that the juz has not been started.
     *
     * @return static
     */
    public function notStarted(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'ayat_tercakup' => 0,
                'persentase' => 0.00,
                'status_tuntas' => false,
                'tanggal_tuntas' => null,
                'detail_ayat_tercakup' => [],
            ];
        });
    }
}
