<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class HasilTesPenempatan extends Model
{
    use HasFactory;

    protected $table = 'hasil_tes_penempatans';

    protected $fillable = [
        'tes_penempatan_id',
        'juz_id',
        'surah_id',
        'ayat_mulai',
        'ayat_selesai',
    ];

    protected static function booted()
    {
        static::saving(function ($hasil) {
            if ($hasil->ayat_mulai > $hasil->ayat_selesai) {
                throw new InvalidArgumentException("Ayat mulai ({$hasil->ayat_mulai}) tidak boleh lebih besar dari ayat selesai ({$hasil->ayat_selesai}).");
            }
            if ($hasil->ayat_mulai < 1) {
                throw new InvalidArgumentException("Ayat mulai harus minimal 1.");
            }
        });
    }

    public function getAyatCountAttribute(): int
    {
        return ($this->ayat_selesai - $this->ayat_mulai) + 1;
    }

    public function tesPenempatan(): BelongsTo
    {
        return $this->belongsTo(TesPenempatan::class, 'tes_penempatan_id');
    }

    public function juz(): BelongsTo
    {
        return $this->belongsTo(Juz::class);
    }

    public function surah(): BelongsTo
    {
        return $this->belongsTo(Surah::class);
    }
}
