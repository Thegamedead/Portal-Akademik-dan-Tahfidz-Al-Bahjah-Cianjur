<?php

namespace Database\Factories;

use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TahunAjaran>
 */
class TahunAjaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0;
        $tahunMulai = 2020 + ($counter++);
        $tahunSelesai = $tahunMulai + 1;

        return [
            'label' => "{$tahunMulai}/{$tahunSelesai}",
            'tanggal_mulai' => "{$tahunMulai}-07-01",
            'tanggal_selesai' => "{$tahunSelesai}-06-30",
            'status' => false,
        ];
    }

    /**
     * Indicate that the tahun ajaran is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => true,
        ]);
    }

    /**
     * Indicate that the tahun ajaran is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }
}
