<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\ParameterKonfigurasiObserver;

#[ObservedBy([ParameterKonfigurasiObserver::class])]
class ParameterKonfigurasi extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'target_kelulusan',
        'batas_tingkat',
        'kapasitas_kelompok_min',
        'kapasitas_kelompok_max',
        'formula_predikat',
        'ambang_alfa_berturut',
        'hari_efektif_tahfidz',
        'target_hafalan_per_sesi',
        'komponen_penilaian',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'batas_tingkat'          => 'array',
        'hari_efektif_tahfidz'   => 'array',
        'komponen_penilaian'     => 'array',
        'target_hafalan_per_sesi'=> 'array',
        'target_kelulusan'       => 'integer',
        'kapasitas_kelompok_min' => 'integer',
        'kapasitas_kelompok_max' => 'integer',
        'ambang_alfa_berturut'   => 'integer',
    ];

    /**
     * Get the singleton instance
     *
     * @return self
     */
    public static function getInstance(): self
    {
        return static::first() ?? static::create([
            'target_kelulusan' => 5,
            'batas_tingkat' => [
                'Dasar' => [30],
                'Menengah' => [29, 28],
                'Atas' => [27, 26]
            ],
            'kapasitas_kelompok_min' => 5,
            'kapasitas_kelompok_max' => 15,
            'ambang_alfa_berturut' => 3,
            'hari_efektif_tahfidz' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
        ]);
    }
}

