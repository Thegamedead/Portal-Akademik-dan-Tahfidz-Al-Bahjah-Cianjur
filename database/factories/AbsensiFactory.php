<?php

namespace Database\Factories;

use App\Enums\StatusKehadiran;
use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Kelompok;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Absensi>
 */
class AbsensiFactory extends Factory
{
    protected $model = Absensi::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kelompok_id' => Kelompok::factory(),
            'siswa_id' => Siswa::factory(),
            'tanggal' => fake()->date(),
            'status_kehadiran' => fake()->randomElement([
                StatusKehadiran::Hadir,
                StatusKehadiran::Izin,
                StatusKehadiran::Sakit,
                StatusKehadiran::Alfa,
            ]),
            'guru_pengisi_id' => Guru::factory(),
            'alfa_berturut_turut' => 0,
            'override_admin' => false,
            'admin_id' => null,
        ];
    }

    /**
     * Indicate that the absensi status is Hadir.
     */
    public function hadir(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_kehadiran' => StatusKehadiran::Hadir,
            'alfa_berturut_turut' => 0,
        ]);
    }

    /**
     * Indicate that the absensi status is Alfa.
     */
    public function alfa(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_kehadiran' => StatusKehadiran::Alfa,
        ]);
    }
}
