<?php

namespace Database\Factories;

use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Semester>
 */
class SemesterFactory extends Factory
{
    public function definition(): array
    {
        $jenis = fake()->randomElement(['Ganjil', 'Genap']);

        return [
            'tahun_ajaran_id' => TahunAjaran::factory(),
            'jenis' => $jenis,
            'tanggal_mulai' => $jenis === 'Ganjil' ? '2024-07-01' : '2025-01-01',
            'tanggal_selesai' => $jenis === 'Ganjil' ? '2024-12-31' : '2025-06-30',
            'status_aktif' => false,
        ];
    }

    public function ganjil(): static
    {
        return $this->state([
            'jenis' => 'Ganjil',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
        ]);
    }

    public function genap(): static
    {
        return $this->state([
            'jenis' => 'Genap',
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-06-30',
        ]);
    }

    public function active(): static
    {
        return $this->state(['status_aktif' => true]);
    }
}
