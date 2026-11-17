<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatGuruPembimbingKelompok extends Model
{
    use HasFactory;

    protected $table = 'riwayat_guru_pembimbing_kelompoks';

    protected $fillable = [
        'kelompok_id',
        'guru_dari_id',
        'guru_ke_id',
        'tanggal_efektif',
        'alasan',
    ];

    protected $casts = [
        'tanggal_efektif' => 'date',
    ];

    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class);
    }

    public function guruDari()
    {
        return $this->belongsTo(Guru::class, 'guru_dari_id');
    }

    public function guruKe()
    {
        return $this->belongsTo(Guru::class, 'guru_ke_id');
    }
}
