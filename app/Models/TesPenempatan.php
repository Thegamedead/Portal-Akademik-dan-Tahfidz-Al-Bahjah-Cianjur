<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\StatusTesPenempatan;
use App\Enums\RekomendasiTingkat;

class TesPenempatan extends Model
{
    use HasFactory;

    protected $table = 'tes_penempatans';

    protected $fillable = [
        'siswa_id',
        'guru_penguji_id',
        'admin_id',
        'tanggal_tes',
        'waktu_tes',
        'status',
        'rekomendasi_tingkat',
        'catatan_kualitas',
        'alasan_pembatalan',
        'final_kelompok_id',
        'submitted_at',
        'ditempatkan_at',
        'penempatan_by',
    ];

    protected $casts = [
        'tanggal_tes' => 'date',
        'waktu_tes' => 'datetime:H:i',
        'status' => StatusTesPenempatan::class,
        'rekomendasi_tingkat' => RekomendasiTingkat::class,
        'submitted_at' => 'datetime',
        'ditempatkan_at' => 'datetime',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guruPenguji(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_penguji_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function finalKelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class, 'final_kelompok_id');
    }

    public function penempatanBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'penempatan_by');
    }

    public function hasilTes(): HasMany
    {
        return $this->hasMany(HasilTesPenempatan::class, 'tes_penempatan_id');
    }

    public function isEditable(): bool
    {
        return $this->status === StatusTesPenempatan::TERJADWAL;
    }

    public function canBeCancelled(): bool
    {
        return $this->status === StatusTesPenempatan::TERJADWAL;
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => StatusTesPenempatan::SELESAI,
            'submitted_at' => now(),
        ]);
    }

    public function cancel(int $adminId, string $alasan): void
    {
        $this->update([
            'status' => StatusTesPenempatan::DIBATALKAN,
            'admin_id' => $adminId,
            'alasan_pembatalan' => $alasan,
        ]);
    }
}
