<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Surah extends Model
{
    use HasFactory;

    protected $table = 'surahs';

    protected $fillable = [
        'nomor_surah',
        'nama_latin',
        'nama_arab',
        'jumlah_ayat',
        'tempat_turun',
        'urutan_mushaf',
    ];

    protected $casts = [
        'nomor_surah' => 'integer',
        'jumlah_ayat' => 'integer',
        'urutan_mushaf' => 'integer',
    ];

    /**
     * Get juzs that contain this surah
     */
    public function juzs(): BelongsToMany
    {
        return $this->belongsToMany(Juz::class, 'juz_surah')
            ->withPivot('ayat_mulai', 'ayat_selesai')
            ->withTimestamps();
    }
}
