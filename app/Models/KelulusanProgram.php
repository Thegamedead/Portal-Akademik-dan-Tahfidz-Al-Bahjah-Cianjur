<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\StatusKelulusan;

class KelulusanProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'guru_pengusul_id',
        'kelompok_id',
        'tanggal_tuntas',
        'tanggal_usul',
        'status_kelulusan',
        'catatan',
        'disetujui_oleh',
        'tanggal_keputusan',
    ];

    protected $casts = [
        'tanggal_tuntas' => 'date',
        'tanggal_usul' => 'datetime',
        'tanggal_keputusan' => 'datetime',
        'status_kelulusan' => StatusKelulusan::class,
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guruPengusul(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_pengusul_id');
    }

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'disetujui_oleh');
    }
}
