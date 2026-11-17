<?php

namespace Database\Factories;

use App\Models\Sekolah;
use Illuminate\Database\Eloquent\Factories\Factory;

class SekolahFactory extends Factory
{
    protected $model = Sekolah::class;

    public function definition(): array
    {
        return [
            'nama_sekolah' => 'SMP Islam Terpadu Al-Hijrah',
            'npsn' => $this->faker->numerify('########'),
            'status_sekolah' => $this->faker->randomElement(['Negeri', 'Swasta']),
            'akreditasi' => $this->faker->randomElement(['A', 'B', 'C', 'Belum Terakreditasi']),
            'naungan' => 'Kementerian Agama',
            'no_sk_pendirian' => $this->faker->numerify('SK/###/####'),
            'tanggal_sk_pendirian' => $this->faker->date(),
            'no_sk_operasional' => $this->faker->numerify('SK/###/####'),
            'tanggal_sk_operasional' => $this->faker->date(),
            'nama_kepala_sekolah' => $this->faker->name(),
            'nip_kepala_sekolah' => $this->faker->numerify('####################'),
            'no_telepon' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'website' => $this->faker->optional()->url(),
            'alamat_jalan' => $this->faker->streetAddress(),
            'alamat_dusun' => $this->faker->optional()->word(),
            'alamat_rt' => $this->faker->numerify('00#'),
            'alamat_rw' => $this->faker->numerify('00#'),
            'alamat_desa_kelurahan' => $this->faker->city(),
            'alamat_kecamatan' => $this->faker->city(),
            'alamat_kabupaten_kota' => $this->faker->city(),
            'alamat_provinsi' => $this->faker->state(),
            'alamat_kode_pos' => $this->faker->postcode(),
        ];
    }
}
