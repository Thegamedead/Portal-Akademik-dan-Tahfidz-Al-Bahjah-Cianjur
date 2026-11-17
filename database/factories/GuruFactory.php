<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guru>
 */
class GuruFactory extends Factory
{
    public function definition(): array
    {
        return [
            'niy' => fake()->unique()->numerify('##########'),
            'nama' => fake()->name(),
            'tanggal_lahir' => fake()->dateTimeBetween('-55 years', '-25 years')->format('Y-m-d'),

            // Master data demographic fields
            'jenis_kelamin' => fake()->randomElement(['Laki-laki', 'Perempuan']),
            'tempat_lahir' => fake()->city(),

            // Position field
            'jabatan' => fake()->randomElement(['Guru Pembimbing Tahfidz', 'Guru Mapel', 'Tendik']),

            // Contact fields
            'no_telepon' => fake()->numerify('08##########'),
            'email' => fake()->optional(0.7)->safeEmail(),

            // Auth/status fields
            'status_aktif' => true,
            'reset_oleh' => null,
            'reset_pada' => null,
        ];
    }

    public function nonaktif(): static
    {
        return $this->state(['status_aktif' => false]);
    }

    public function pembimbingTahfidz(): static
    {
        return $this->state(['jabatan' => 'Guru Pembimbing Tahfidz']);
    }
}
