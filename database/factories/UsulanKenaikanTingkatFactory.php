<?php

namespace Database\Factories;

use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelompok;
use App\Models\Admin;
use App\Enums\StatusUsulan;
use App\Enums\TingkatKelompok;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UsulanKenaikanTingkat>
 */
class UsulanKenaikanTingkatFactory extends Factory
{
    public function definition(): array
    {
        $tingkatAsal = fake()->randomElement(['Dasar', 'Menengah']);
        $tingkatTujuan = $tingkatAsal === 'Dasar' ? 'Menengah' : 'Atas';

        return [
            'siswa_id' => Siswa::factory(),
            'guru_pengusul_id' => Guru::factory(),
            'tingkat_asal' => $tingkatAsal,
            'tingkat_tujuan' => $tingkatTujuan,
            'kelompok_asal_id' => Kelompok::factory(),
            'kelompok_tujuan_id' => null,
            'status_usulan' => StatusUsulan::DIAJUKAN,
            'alasan' => fake()->paragraph(),
            'tanggal_usul' => now(),
            'admin_verifikator_id' => null,
            'tanggal_keputusan' => null,
            'catatan_keputusan' => null,
            'tanggal_penempatan' => null,
        ];
    }

    /**
     * State for an approved proposal
     */
    public function disetujui(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status_usulan' => StatusUsulan::DISETUJUI,
                'admin_verifikator_id' => Admin::factory(),
                'tanggal_keputusan' => now(),
            ];
        });
    }

    /**
     * State for a rejected proposal
     */
    public function ditolak(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status_usulan' => StatusUsulan::DITOLAK,
                'admin_verifikator_id' => Admin::factory(),
                'tanggal_keputusan' => now(),
                'catatan_keputusan' => fake()->paragraph(),
            ];
        });
    }

    /**
     * State for a placed proposal
     */
    public function ditempatkan(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status_usulan' => StatusUsulan::DITEMPATKAN,
                'admin_verifikator_id' => Admin::factory(),
                'tanggal_keputusan' => now(),
                'kelompok_tujuan_id' => Kelompok::factory(),
                'tanggal_penempatan' => now(),
            ];
        });
    }
}
