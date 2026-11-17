<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Siswa>
 */
class SiswaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nis' => fake()->unique()->numerify('##########'),
            'nisn' => fake()->optional(0.9, null)->numerify('##########'),
            'nama' => fake()->name(),
            'tanggal_lahir' => fake()->dateTimeBetween('-18 years', '-11 years')->format('Y-m-d'),

            // Master data demographic fields
            'jenis_kelamin' => fake()->randomElement(['Laki-laki', 'Perempuan']),
            'tempat_lahir' => fake()->city(),
            'nama_ayah' => fake()->name('male'),
            'nama_ibu' => fake()->name('female'),

            // Status fields
            'status_akademik' => 'Aktif',
            'status_tahfidz' => 'Sedang Berjalan',
            'status_penempatan' => 'Belum Ditempatkan',

            // Enrollment fields
            'status_masuk' => fake()->randomElement(['Baru', 'Pindahan']),
            'tanggal_masuk' => fake()->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
            'asal_sekolah' => fake()->optional(0.3)->company() . ' School',
            'alamat_sekolah_asal' => fake()->optional(0.3)->address(),

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

    public function ditempatkan(): static
    {
        return $this->state(['status_penempatan' => 'Ditempatkan']);
    }
}
