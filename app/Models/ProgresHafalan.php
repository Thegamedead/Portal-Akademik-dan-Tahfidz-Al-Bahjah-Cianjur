<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ProgresHafalan Model
 *
 * Menyimpan progres hafalan siswa secara AKUMULATIF dan PERMANEN (tidak per tahun ajaran).
 * Sistem dual-track: progres tahfidz INDEPENDEN dari status akademik.
 *
 * Key Changes (Refactor 2026-08-11):
 * - Hapus tahun_ajaran_id → Progres lifetime (kelas 7-9)
 * - Tambah target_juz → Minimal juz untuk lulus program (default 5 juz)
 * - Tambah status_tahfidz → Independen dari status akademik
 * - Tambah tanggal_lulus_tahfidz → Kapan pertama kali mencapai target
 *
 * @property int $id
 * @property int $siswa_id
 * @property array|null $baseline_juzs Array of juz IDs from placement test
 * @property string|null $baseline_surahs Descriptive text of covered surahs
 * @property string|null $catatan_kualitas_baseline
 * @property string|null $rekomendasi_tingkat Dasar|Menengah|Atas
 * @property int $total_juz_tuntas Count of completed juz (100% ayat covered)
 * @property int|null $juz_berjalan Current incomplete juz ID
 * @property float|null $juz_berjalan_percentage Percentage 0.00-100.00
 * @property float $progress_toward_target Percentage toward target_kelulusan
 * @property int $target_juz Target minimal juz untuk lulus program tahfidz
 * @property string $status_tahfidz Belum Lulus|Lulus
 * @property \Illuminate\Support\Carbon|null $tanggal_lulus_tahfidz
 * @property int|null $baseline_modified_by Admin ID who last modified baseline
 * @property \Illuminate\Support\Carbon|null $baseline_modified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Siswa $siswa
 * @property-read \Illuminate\Database\Eloquent\Collection<CapaianJuz> $capaianJuzs
 * @property-read Admin|null $baselineModifiedBy
 * @property-read string $kategori_hafalan
 * @property-read float $persentase_target
 */
class ProgresHafalan extends Model
{
    use HasFactory;

    protected $table = 'progres_hafalans';

    protected $fillable = [
        'siswa_id',
        'baseline_juzs',
        'baseline_surahs',
        'catatan_kualitas_baseline',
        'rekomendasi_tingkat',
        'total_juz_tuntas',
        'juz_berjalan',
        'juz_berjalan_percentage',
        'progress_toward_target',
        'target_juz',
        'status_tahfidz',
        'tanggal_lulus_tahfidz',
        'baseline_modified_by',
        'baseline_modified_at',
    ];

    protected function casts(): array
    {
        return [
            'baseline_juzs' => 'array',
            'juz_berjalan_percentage' => 'decimal:2',
            'progress_toward_target' => 'decimal:2',
            'target_juz' => 'integer',
            'tanggal_lulus_tahfidz' => 'date',
            'baseline_modified_at' => 'datetime',
        ];
    }

    /**
     * Relationship: ProgresHafalan belongs to a Siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Relationship: ProgresHafalan has many CapaianJuz (detail per juz)
     */
    public function capaianJuzs()
    {
        return $this->hasMany(CapaianJuz::class);
    }

    /**
     * Relationship: ProgresHafalan baseline modified by Admin
     */
    public function baselineModifiedBy()
    {
        return $this->belongsTo(Admin::class, 'baseline_modified_by');
    }

    /**
     * Check if siswa sudah lulus program tahfidz
     */
    public function isLulusTahfidz(): bool
    {
        return $this->status_tahfidz === 'Lulus';
    }

    /**
     * Get persentase progress terhadap target minimal
     */
    public function getPersentaseTargetAttribute(): float
    {
        if ($this->target_juz <= 0) {
            return 0;
        }

        return ($this->total_juz_tuntas / $this->target_juz) * 100;
    }

    /**
     * Get kategori hafalan berdasarkan jumlah juz
     */
    public function getKategoriHafalanAttribute(): string
    {
        $juz = $this->total_juz_tuntas;
        $target = $this->target_juz;

        // Ambil dari config jika ada
        $kategoris = config('tahfidz.kategori_hafalan', []);

        foreach ($kategoris as $kategori) {
            if ($juz >= $kategori['min_juz'] && $juz <= $kategori['max_juz']) {
                return $kategori['label'];
            }
        }

        // Fallback manual
        if ($juz < $target) {
            return 'Belum Mencapai Target';
        } elseif ($juz == $target) {
            return 'Memenuhi Target';
        } elseif ($juz < 10) {
            return 'Di Atas Target';
        } elseif ($juz < 20) {
            return 'Excellent';
        } elseif ($juz < 30) {
            return 'Tingkat Tinggi';
        } else {
            return 'Hafidz 30 Juz';
        }
    }

    /**
     * Get warna badge untuk kategori hafalan
     */
    public function getKategoriColorAttribute(): string
    {
        $juz = $this->total_juz_tuntas;
        $kategoris = config('tahfidz.kategori_hafalan', []);

        foreach ($kategoris as $kategori) {
            if ($juz >= $kategori['min_juz'] && $juz <= $kategori['max_juz']) {
                return $kategori['color'] ?? 'gray';
            }
        }

        return 'gray';
    }

    /**
     * Cek apakah sudah mencapai target minimal
     */
    public function hasMencapaiTarget(): bool
    {
        return $this->total_juz_tuntas >= $this->target_juz;
    }

    /**
     * Get sisa juz untuk mencapai target
     */
    public function getSisaJuzAttribute(): int
    {
        return max(0, $this->target_juz - $this->total_juz_tuntas);
    }

    /**
     * Auto-update status tahfidz jika sudah mencapai target
     * (dipanggil dari service saat update progres)
     */
    public function checkAndUpdateStatusTahfidz(): bool
    {
        // Jika belum lulus dan sudah mencapai target
        if ($this->status_tahfidz === 'Belum Lulus' && $this->hasMencapaiTarget()) {
            $this->status_tahfidz = 'Lulus';
            $this->tanggal_lulus_tahfidz = now();
            $this->save();

            return true; // Status berubah
        }

        return false; // Status tidak berubah
    }

    /**
     * Get validation rules for this model
     *
     * @return array<string, string|array>
     */
    public static function validationRules(): array
    {
        return [
            'siswa_id' => 'required|exists:siswas,id',
            'baseline_juzs' => 'nullable|array',
            'baseline_surahs' => 'nullable|string',
            'catatan_kualitas_baseline' => 'nullable|string',
            'rekomendasi_tingkat' => 'nullable|in:Dasar,Menengah,Atas',
            'target_juz' => 'nullable|integer|min:1|max:30',
            'status_tahfidz' => 'nullable|in:Belum Lulus,Lulus',
        ];
    }

    /**
     * Scope untuk filter siswa yang sudah lulus tahfidz
     */
    public function scopeLulusTahfidz($query)
    {
        return $query->where('status_tahfidz', 'Lulus');
    }

    /**
     * Scope untuk filter siswa yang belum lulus tahfidz
     */
    public function scopeBelumLulusTahfidz($query)
    {
        return $query->where('status_tahfidz', 'Belum Lulus');
    }

    /**
     * Scope untuk filter berdasarkan kategori hafalan
     */
    public function scopeKategori($query, string $kategori)
    {
        $kategoris = config('tahfidz.kategori_hafalan', []);

        if (isset($kategoris[$kategori])) {
            $min = $kategoris[$kategori]['min_juz'];
            $max = $kategoris[$kategori]['max_juz'];

            return $query->whereBetween('total_juz_tuntas', [$min, $max]);
        }

        return $query;
    }
}
