<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ParameterKonfigurasi;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ParameterKonfigurasi>
 */
class ParameterKonfigurasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'target_kelulusan' => 5,
            'batas_tingkat' => [
                'dasar' => 0,
                'menengah' => 10,
                'atas' => 20,
            ],
            'kapasitas_kelompok_min' => 6,
            'kapasitas_kelompok_max' => 12,
            'formula_predikat' => 'minimum',
            'ambang_alfa_berturut' => 3,
            'hari_efektif_tahfidz' => [
                'senin' => true,
                'selasa' => true,
                'rabu' => true,
                'kamis' => true,
                'jumat' => true,
                'sabtu' => false,
                'minggu' => false,
            ],
        ];
    }
}
