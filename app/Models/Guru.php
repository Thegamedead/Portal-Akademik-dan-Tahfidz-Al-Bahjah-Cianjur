<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $niy
 * @property string $nama
 * @property string $jenis_kelamin
 * @property \Illuminate\Support\Carbon $tanggal_lahir
 * @property string $jabatan
 * @property bool $status_aktif
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kelompok> $kelompoksBimbingan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GuruTugasTambahan> $tugasTambahans
 * @property-read \App\Models\Admin|null $resetOleh
 */
class Guru extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\GuruFactory> */
    use HasFactory, SoftDeletes, Notifiable;

    protected $guard = 'guru';

    protected $fillable = [
        'niy',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'jabatan',
        'no_telepon',
        'email',
        'tanggal_mulai_kerja',
        'status_aktif',
        'akun_aktif',
        'jenis_keluar',
        'tanggal_keluar',
        'keterangan_keluar',
        'diproses_oleh',
        'reset_oleh',
        'reset_pada',
        'password',
        'password_text',
        'keterangan_akun',
    ];

    protected $hidden = ['tanggal_lahir', 'password'];

    protected function casts(): array
    {
        return [
            'status_aktif' => 'boolean',
            'akun_aktif'   => 'boolean',
            'tanggal_lahir' => 'date',
            'tanggal_mulai_kerja' => 'date',
            'tanggal_keluar' => 'date',
            'reset_pada' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Tidak ada remember_token di tabel gurus
    public function getRememberTokenName()
    {
        return null;
    }

    public function resetOleh(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reset_oleh');
    }

    public function tugasTambahans(): HasMany
    {
        return $this->hasMany(GuruTugasTambahan::class);
    }

    public function kelompoksBimbingan(): HasMany
    {
        return $this->hasMany(Kelompok::class, 'guru_pembimbing_id');
    }

    /**
     * Alias untuk kelompoksBimbingan()
     * Untuk konsistensi penamaan dengan kode lain
     */
    public function kelompoks(): HasMany
    {
        return $this->kelompoksBimbingan();
    }

    /**
     * Relasi ke Riwayat Guru Pembimbing Kelompok
     * Untuk melihat history kelompok yang pernah dibimbing
     */
    public function riwayatGuruPembimbingKelompoks(): HasMany
    {
        return $this->hasMany(RiwayatGuruPembimbingKelompok::class, 'guru_pembimbing_id');
    }

    public function scopePembimbingTahfidz($query)
    {
        return $query->where('jabatan', 'Guru Pembimbing Tahfidz');
    }
}
