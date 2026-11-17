<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuruTugasTambahan extends Model
{
    use HasFactory;

    protected $table = 'guru_tugas_tambahans';

    protected $fillable = [
        'guru_id',
        'jenis_tugas',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
