<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Juz extends Model
{
    use HasFactory;

    protected $table = 'juzs';

    protected $fillable = [
        'juz_ke',
        'urutan_hafalan',
        'jumlah_ayat',
        'awal_surah',
        'awal_ayat',
        'akhir_surah',
        'akhir_ayat',
    ];

    protected $casts = [
        'juz_ke' => 'integer',
        'urutan_hafalan' => 'integer',
    ];

    /**
     * Get surahs that belong to this juz
     */
    public function surahs(): BelongsToMany
    {
        return $this->belongsToMany(Surah::class, 'juz_surah')
            ->withPivot('ayat_mulai', 'ayat_selesai')
            ->withTimestamps();
    }
}
