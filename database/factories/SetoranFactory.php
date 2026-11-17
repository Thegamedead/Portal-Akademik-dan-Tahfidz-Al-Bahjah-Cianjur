<?php

namespace Database\Factories;

use App\Enums\JenisSetoran;
use App\Enums\Keputusan;
use App\Enums\SkorKomponen;
use App\Models\Absensi;
use App\Models\Juz;
use App\Models\Setoran;
use App\Models\Surah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setoran>
 */
class SetoranFactory extends Factory
{
    protected $model = Setoran::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'absensi_id' => Absensi::factory(),
            'jenis_setoran' => fake()->randomElement([
                JenisSetoran::HafalanBaru,
                JenisSetoran::Ulang,
            ]),
            'juz_id' => Juz::factory(),
            'surah_id' => Surah::factory(),
            'ayat_mulai' => 1,
            'ayat_selesai' => 10,
            'skor_tajwid' => fake()->randomElement([
                SkorKomponen::Lancar,
                SkorKomponen::KurangLancar,
                SkorKomponen::TidakLancar,
            ]),
            'skor_kelancaran' => fake()->randomElement([
                SkorKomponen::Lancar,
                SkorKomponen::KurangLancar,
                SkorKomponen::TidakLancar,
            ]),
            'skor_makhraj' => fake()->randomElement([
                SkorKomponen::Lancar,
                SkorKomponen::KurangLancar,
                SkorKomponen::TidakLancar,
            ]),
            'skor_fashahah' => fake()->randomElement([
                SkorKomponen::Lancar,
                SkorKomponen::KurangLancar,
                SkorKomponen::TidakLancar,
            ]),
            'predikat' => SkorKomponen::Lancar,
            'keputusan' => fake()->randomElement([
                Keputusan::Lanjut,
                Keputusan::Ulang,
            ]),
            'catatan' => fake()->optional()->sentence(),
            'saran_ayat_mulai' => null,
            'saran_ayat_selesai' => null,
            'saran_surah_id' => null,
            'saran_juz_id' => null,
        ];
    }

    /**
     * Indicate that the setoran is Hafalan Baru with Lanjut decision.
     */
    public function hafalanBaruLanjut(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_setoran' => JenisSetoran::HafalanBaru,
            'keputusan' => Keputusan::Lanjut,
        ]);
    }

    /**
     * Indicate that the setoran is Ulang (review).
     */
    public function ulang(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_setoran' => JenisSetoran::Ulang,
        ]);
    }
}
