<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * RiwayatKelompok Model
 *
 * Logs all group movements for students. Created automatically when:
 * - Student is promoted to a new level (alasan: "Kenaikan Tingkat")
 * - New student is placed after placement test (alasan: "Penempatan Awal")
 * - Student is moved during year rollover (alasan: "Rollover Tahun Ajaran")
 * - Student is removed from group pending placement (kelompok_ke_id will be NULL)
 *
 * @property int $id
 * @property int $siswa_id
 * @property int|null $kelompok_dari_id Previous group (NULL for initial placement)
 * @property int|null $kelompok_ke_id New group (NULL if removed but not yet placed)
 * @property \Illuminate\Support\Carbon $tanggal Movement date
 * @property string $alasan Reason for movement
 * @property int|null $disetujui_oleh Admin ID who approved the movement
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Siswa $siswa
 * @property-read Kelompok|null $kelompokDari
 * @property-read Kelompok|null $kelompokKe
 * @property-read Admin|null $disetujuiOleh
 */
class RiwayatKelompok extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'kelompok_dari_id',
        'kelompok_ke_id',
        'tanggal',
        'alasan',
        'disetujui_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'datetime',
        ];
    }

    /**
     * Relationship: RiwayatKelompok belongs to a Siswa
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Relationship: RiwayatKelompok belongs to a Kelompok (source)
     */
    public function kelompokDari(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_dari_id');
    }

    /**
     * Relationship: RiwayatKelompok belongs to a Kelompok (destination)
     */
    public function kelompokKe(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_ke_id');
    }

    /**
     * Relationship: RiwayatKelompok approved by Admin
     */
    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'disetujui_oleh');
    }
}
