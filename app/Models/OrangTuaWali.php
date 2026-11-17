<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrangTuaWali extends Model
{
    /** @use HasFactory<\Database\Factories\OrangTuaWaliFactory> */
    use HasFactory;

    protected $table = 'orang_tua_walis';

    protected $fillable = [
        'siswa_id',
        'jenis',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'pendidikan',
        'pekerjaan',
        'penghasilan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    /**
     * Relationship: OrangTuaWali belongs to Siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
