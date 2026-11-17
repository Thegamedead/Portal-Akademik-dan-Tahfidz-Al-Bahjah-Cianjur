<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Student Model
 *
 * @property int $id
 * @property string $nis
 * @property string|null $nisn
 * @property string $nama
 * @property string $jenis_kelamin
 * @property \Carbon\Carbon|null $tanggal_lahir
 * @property string|null $tempat_lahir
 * @property string|null $nama_ayah
 * @property string|null $nama_ibu
 * @property string $status_akademik
 * @property string|null $status_tahfidz
 * @property string $status_penempatan
 * @property string|null $status_masuk
 * @property \Carbon\Carbon|null $tanggal_masuk
 * @property \Carbon\Carbon|null $tanggal_lulus_akademik
 * @property string|null $asal_sekolah
 * @property string|null $alamat_sekolah_asal
 * @property bool $status_aktif
 * @property bool $akun_aktif
 * @property int|null $kelompok_id
 * @property \Carbon\Carbon|null $tanggal_penempatan
 * @property int|null $kelas_id
 * @property int|null $tahun_ajaran_id
 * @property int|null $reset_oleh
 * @property \Carbon\Carbon|null $reset_pada
 * @property string|null $jenis_keluar
 * @property \Carbon\Carbon|null $tanggal_keluar
 * @property string|null $tujuan_sekolah
 * @property string|null $keterangan_keluar
 * @property int|null $diproses_oleh
 * @property bool|null $punya_hafalan_sebelumnya
 * @property bool|null $perlu_tes_penempatan
 * @property string $password
 * @property string|null $password_text
 * @property string|null $keterangan_akun
 *
 * @property-read Kelompok|null $kelompok
 * @property-read Kelas|null $kelas
 * @property-read TahunAjaran|null $tahunAjaran
 * @property-read ProgresHafalan|null $progresHafalan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Absensi> $absensis
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Setoran> $setorans
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UsulanKenaikanTingkat> $usulanKenaikanTingkats
 * @property-read string $status_gabungan
 * @property-read string $status_gabungan_color
 * @property-read string $status_gabungan_icon
 */
class Siswa extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\SiswaFactory> */
    use HasFactory, SoftDeletes, Notifiable;

    protected $guard = 'siswa';

    protected $fillable = [
        'nis',
        'nisn',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'nama_ayah',
        'nama_ibu',
        'status_akademik',
        'status_tahfidz',
        'status_penempatan',
        'status_masuk',
        'tanggal_masuk',
        'tanggal_lulus_akademik',
        'asal_sekolah',
        'alamat_sekolah_asal',
        'status_aktif',
        'akun_aktif',
        'kelompok_id',
        'tanggal_penempatan',
        'kelas_id',
        'tahun_ajaran_id',
        'reset_oleh',
        'reset_pada',
        'jenis_keluar',
        'tanggal_keluar',
        'tujuan_sekolah',
        'keterangan_keluar',
        'diproses_oleh',
        'punya_hafalan_sebelumnya',
        'perlu_tes_penempatan',
        'password',
        'password_text',
        'keterangan_akun',
    ];

    protected $hidden = ['tanggal_lahir', 'password'];

    protected static function booted(): void
    {
        static::saving(function ($siswa) {
            if ($siswa->tanggal_masuk && empty($siswa->tahun_ajaran_id)) {
                $tahun = \App\Models\TahunAjaran::getOrCreateByDate($siswa->tanggal_masuk);
                if ($tahun) {
                    $siswa->tahun_ajaran_id = $tahun->id;
                }
            }
        });
    }

    protected function casts(): array
    {
        return [
            'status_aktif' => 'boolean',
            'akun_aktif'   => 'boolean',
            'tanggal_lahir' => 'date',
            'tanggal_masuk' => 'date',
            'tanggal_keluar' => 'date',
            'tanggal_penempatan' => 'date', // ✅ Tambahan baru
            'tanggal_lulus_akademik' => 'date', // ✅ Tambahan baru
            'reset_pada' => 'datetime',
            'punya_hafalan_sebelumnya' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // Tidak ada remember_token di tabel siswas
    public function getRememberTokenName(): ?string
    {
        return null;
    }

    // ========================================================================
    // RELATIONSHIPS
    // ========================================================================

    /**
     * Relationship: Siswa belongs to a Kelompok
     */
    public function kelompok(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }

    /**
     * Relationship: Siswa belongs to a Kelas (Academic Class)
     */
    public function kelas(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Relationship: Siswa belongs to a TahunAjaran (registration year)
     */
    public function tahunAjaran(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    /**
     * Relationship: Siswa reset by Admin
     */
    public function resetOleh(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reset_oleh');
    }

    /**
     * Relationship: Admin who processed exit registration
     */
    public function diprosesOleh(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'diproses_oleh');
    }

    /**
     * Relationship: Siswa has many ProgresHafalan
     */
    public function progresHafalans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProgresHafalan::class);
    }

    /**
     * Relationship: Siswa has many Absensi
     */
    public function absensis(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    /**
     * Relationship: Siswa has many Setoran through Absensi
     */
    public function setorans(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Setoran::class, Absensi::class);
    }

    /**
     * Relationship: Siswa has one latest ProgresHafalan
     */
    public function progresHafalan(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProgresHafalan::class)->latestOfMany();
    }

    /**
     * Relationship: Siswa has many TranskripCapaian (historical snapshots)
     */
    public function transkripCapaians(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TranskripCapaian::class);
    }

    /**
     * Relationship: Siswa has one TesPenempatan (placement test result)
     *
     * Note: This relationship is for Module 7 (Tes Penempatan).
     * Returns the most recent placement test result for this student.
     */
    public function tesPenempatan(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TesPenempatan::class)->latestOfMany();
    }

    /**
     * Relationship: Siswa has many TesPenempatan (placement test history)
     */
    public function tesPenempatans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TesPenempatan::class);
    }

    /**
     * Relationship: Siswa has many RiwayatKelompok (group movement history)
     */
    public function riwayatKelompoks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\RiwayatKelompok::class)->orderBy('tanggal', 'desc');
    }

    /**
     * Relationship: Siswa has many UsulanKenaikanTingkat
     */
    public function usulanKenaikanTingkats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\UsulanKenaikanTingkat::class);
    }

    /**
     * Relationship: Siswa has many KelulusanProgram
     */
    public function kelulusanPrograms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\KelulusanProgram::class);
    }

    /**
     * Relationship: Siswa has one latest KelulusanProgram
     */
    public function kelulusanProgram(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\KelulusanProgram::class)->latestOfMany();
    }

    // ========================================================================
    // QUERY SCOPES
    // ========================================================================

    /**
     * Scope: Filter active students (status_akademik = 'Aktif')
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status_akademik', 'Aktif');
    }

    /**
     * Scope: Filter placed students (status_penempatan = 'Ditempatkan')
     */
    public function scopePlaced(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status_penempatan', 'Ditempatkan');
    }

    /**
     * Scope: Search by identifier (NIS, NISN, or name)
     */
    public function scopeSearchByIdentifier(\Illuminate\Database\Eloquent\Builder $query, string $search): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nis', 'like', "%{$search}%")
              ->orWhere('nisn', 'like', "%{$search}%")
              ->orWhere('nama', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Filter by kelompok
     */
    public function scopeInKelompok(\Illuminate\Database\Eloquent\Builder $query, int $kelompokId): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('kelompok_id', $kelompokId);
    }

    /**
     * Scope: Filter unplaced students
     */
    public function scopeUnplaced(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status_penempatan', 'Belum Ditempatkan')
                     ->whereNull('kelompok_id');
    }

    // ========================================================================
    // DUAL-TRACK SYSTEM HELPERS (Refactor 2026-08-11)
    // ========================================================================

    /**
     * Check if siswa sudah lulus akademik (tamat SMP)
     */
    public function isLulusAkademik(): bool
    {
        return $this->status_akademik === 'Lulus SMP';
    }

    /**
     * Check if siswa sudah lulus tahfidz (dari progres_hafalans)
     */
    public function isLulusTahfidz(): bool
    {
        return $this->progresHafalan?->isLulusTahfidz() ?? false;
    }

    /**
     * Get status gabungan (akademik + tahfidz)
     *
     * @return string
     */
    public function getStatusGabunganAttribute(): string
    {
        $akademik = $this->isLulusAkademik();
        $tahfidz = $this->isLulusTahfidz();

        if ($akademik && $tahfidz) {
            return 'Lulus Akademik & Tahfidz';
        } elseif ($akademik && !$tahfidz) {
            return 'Lulus Akademik (Tahfidz Belum)';
        } elseif (!$akademik && $tahfidz) {
            return 'Lulus Tahfidz (Akademik Belum)';
        } else {
            return 'Belum Lulus Akademik & Tahfidz';
        }
    }

    /**
     * Get badge color untuk status gabungan
     */
    public function getStatusGabunganColorAttribute(): string
    {
        $akademik = $this->isLulusAkademik();
        $tahfidz = $this->isLulusTahfidz();

        if ($akademik && $tahfidz) {
            return 'success'; // Hijau
        } elseif ($akademik || $tahfidz) {
            return 'warning'; // Kuning (salah satu lulus)
        } else {
            return 'secondary'; // Abu-abu
        }
    }

    /**
     * Get icon untuk status gabungan
     */
    public function getStatusGabunganIconAttribute(): string
    {
        $akademik = $this->isLulusAkademik();
        $tahfidz = $this->isLulusTahfidz();

        if ($akademik && $tahfidz) {
            return '🎓🌟'; // Kedua-duanya lulus
        } elseif ($akademik) {
            return '🎓'; // Lulus akademik saja
        } elseif ($tahfidz) {
            return '🌟'; // Lulus tahfidz saja
        } else {
            return '📚'; // Belum lulus
        }
    }

    /**
     * Scope: Filter siswa yang lulus akademik
     */
    public function scopeLulusAkademik(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status_akademik', 'Lulus SMP');
    }

    /**
     * Scope: Filter siswa yang lulus tahfidz
     */
    public function scopeLulusTahfidz(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereHas('progresHafalan', function ($q) {
            $q->where('status_tahfidz', 'Lulus');
        });
    }

    /**
     * Scope: Filter siswa yang lulus keduanya (akademik + tahfidz)
     */
    public function scopeLulusKeduanya(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status_akademik', 'Lulus SMP')
            ->whereHas('progresHafalan', function ($q) {
                $q->where('status_tahfidz', 'Lulus');
            });
    }

    /**
     * Scope: Filter siswa yang belum lulus keduanya
     */
    public function scopeBelumLulusKeduanya(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status_akademik', '!=', 'Lulus SMP')
            ->whereHas('progresHafalan', function ($q) {
                $q->where('status_tahfidz', 'Belum Lulus');
            });
    }

    // ========================================================================
    // ROLLOVER SYSTEM SCOPES
    // ========================================================================

    /**
     * Scope: Students eligible for academic promotion
     *
     * Filters students who are:
     * - Active (status_akademik = 'Aktif')
     * - In the specified Kelas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $kelasId The Kelas ID to filter by
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEligibleForPromotion(\Illuminate\Database\Eloquent\Builder $query, int $kelasId): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status_akademik', \App\Enums\StatusAkademik::AKTIF->value)
                     ->where('kelas_id', $kelasId);
    }

    /**
     * Scope: Students with cleared groups pending placement
     *
     * Filters students who are:
     * - Active (status_akademik = 'Aktif')
     * - Not placed (status_penempatan = 'Belum Ditempatkan')
     * - Have no Kelompok assignment (kelompok_id is null)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePendingPlacement(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status_akademik', \App\Enums\StatusAkademik::AKTIF->value)
                     ->where('status_penempatan', \App\Enums\StatusPenempatan::TERDAFTAR->value)
                     ->whereNull('kelompok_id');
    }
}
