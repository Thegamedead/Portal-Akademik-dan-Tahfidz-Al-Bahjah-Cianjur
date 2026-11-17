<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model
{
    use HasFactory;

    protected $table = 'kelompoks';

    protected $fillable = [
        'nama',
        'jenis',
        'tingkat',
        'guru_pembimbing_id',
        'tanggal_penempatan_guru',
        'tahun_ajaran_id',
        'kapasitas_min',
        'kapasitas_maks',
    ];

    protected $casts = [
        'kapasitas_min' => 'integer',
        'kapasitas_maks' => 'integer',
        'tanggal_penempatan_guru' => 'date',
    ];

    /**
     * Relasi ke Guru Pembimbing
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function guruPembimbing()
    {
        return $this->belongsTo(Guru::class, 'guru_pembimbing_id');
    }

    /**
     * Alias relasi ke Guru Pembimbing
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function guru()
    {
        return $this->guruPembimbing();
    }

    /**
     * Relasi ke Tahun Ajaran
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    /**
     * Relasi ke Siswa yang ditempatkan di kelompok ini
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siswas()
    {
        return $this->hasMany(Siswa::class)
            ->where('status_penempatan', 'Ditempatkan');
    }

    /**
     * Relasi ke Riwayat Guru Pembimbing Kelompok
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function riwayatGuruPembimbings()
    {
        return $this->hasMany(RiwayatGuruPembimbingKelompok::class);
    }

    /**
     * Accessor untuk jumlah siswa saat ini
     *
     * @return int
     */
    public function getJumlahSiswaAttribute(): int
    {
        return $this->siswas()->count();
    }

    /**
     * Global config cache untuk parameter konfigurasi
     *
     * @var ParameterKonfigurasi|null
     */
    protected static ?ParameterKonfigurasi $globalConfig = null;

    /**
     * Get global configuration untuk kapasitas kelompok
     *
     * @return ParameterKonfigurasi|null
     */
    protected static function getGlobalConfig(): ?ParameterKonfigurasi
    {
        if (self::$globalConfig === null) {
            self::$globalConfig = \App\Models\ParameterKonfigurasi::first();
        }
        return self::$globalConfig;
    }

    /**
     * Accessor untuk kapasitas minimum dengan fallback ke global config
     *
     * @param int|null $value
     * @return int
     */
    public function getKapasitasMinAttribute(?int $value): int
    {
        $config = self::getGlobalConfig();
        return $config ? $config->kapasitas_kelompok_min : ($value ?? 6);
    }

    /**
     * Accessor untuk kapasitas maksimum dengan fallback ke global config
     *
     * @param int|null $value
     * @return int
     */
    public function getKapasitasMaksAttribute(?int $value): int
    {
        $config = self::getGlobalConfig();
        return $config ? $config->kapasitas_kelompok_max : ($value ?? 12);
    }

    /**
     * Accessor untuk status kapasitas kelompok
     *
     * @return string
     */
    public function getStatusKapasitasAttribute(): string
    {
        return $this->jumlah_siswa >= $this->kapasitas_maks ? 'Penuh' : 'Tersedia';
    }
}
