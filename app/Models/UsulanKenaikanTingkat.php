<?php

namespace App\Models;

use App\Enums\StatusUsulan;
use App\Enums\TingkatKelompok;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class UsulanKenaikanTingkat extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'siswa_id',
        'guru_pengusul_id',
        'tingkat_asal',
        'tingkat_tujuan',
        'kelompok_asal_id',
        'kelompok_tujuan_id',
        'status_usulan',
        'alasan',
        'tanggal_usul',
        'admin_verifikator_id',
        'tanggal_keputusan',
        'catatan_keputusan',
        'tanggal_penempatan',
    ];

    protected function casts(): array
    {
        return [
            'tingkat_asal' => TingkatKelompok::class,
            'tingkat_tujuan' => TingkatKelompok::class,
            'status_usulan' => StatusUsulan::class,
            'tanggal_usul' => 'datetime',
            'tanggal_keputusan' => 'datetime',
            'tanggal_penempatan' => 'datetime',
        ];
    }

    /**
     * Relationship: UsulanKenaikanTingkat belongs to a Siswa
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Relationship: UsulanKenaikanTingkat belongs to a Guru (proposer)
     */
    public function guruPengusul(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_pengusul_id');
    }

    /**
     * Relationship: UsulanKenaikanTingkat belongs to a Kelompok (source)
     */
    public function kelompokAsal(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_asal_id');
    }

    /**
     * Relationship: UsulanKenaikanTingkat belongs to a Kelompok (target)
     */
    public function kelompokTujuan(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_tujuan_id');
    }

    /**
     * Relationship: UsulanKenaikanTingkat belongs to an Admin (verifier)
     */
    public function adminVerifikator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_verifikator_id');
    }

    /**
     * Scope: Filter proposals by "Diajukan" status
     */
    public function scopeDiajukan($query)
    {
        return $query->where('status_usulan', StatusUsulan::DIAJUKAN);
    }

    /**
     * Scope: Filter proposals by "Disetujui" status
     */
    public function scopeDisetujui($query)
    {
        return $query->where('status_usulan', StatusUsulan::DISETUJUI);
    }

    /**
     * Scope: Filter proposals by "Ditempatkan" status
     */
    public function scopeDitempatkan($query)
    {
        return $query->where('status_usulan', StatusUsulan::DITEMPATKAN);
    }

    /**
     * Scope: Filter proposals by "Ditolak" status
     */
    public function scopeDitolak($query)
    {
        return $query->where('status_usulan', StatusUsulan::DITOLAK);
    }

    /**
     * Scope: Filter proposals by teacher (guru_pengusul_id)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $guruId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByGuru($query, int $guruId)
    {
        return $query->where('guru_pengusul_id', $guruId);
    }

    /**
     * Configure activity logging options
     *
     * Logs all status transitions (creation, approval, rejection, placement)
     * with admin_verifikator_id, timestamps, and catatan_keputusan.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'siswa_id',
                'guru_pengusul_id',
                'tingkat_asal',
                'tingkat_tujuan',
                'kelompok_asal_id',
                'kelompok_tujuan_id',
                'status_usulan',
                'alasan',
                'tanggal_usul',
                'admin_verifikator_id',
                'tanggal_keputusan',
                'catatan_keputusan',
                'tanggal_penempatan',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Usulan kenaikan tingkat dibuat',
                'updated' => 'Usulan kenaikan tingkat diperbarui',
                'deleted' => 'Usulan kenaikan tingkat dihapus',
                default => "Usulan kenaikan tingkat {$eventName}"
            });
    }
}
