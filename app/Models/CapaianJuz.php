<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * CapaianJuz Model
 *
 * Menyimpan detail capaian per juz untuk setiap siswa. Setiap record merepresentasikan
 * progress hafalan dalam satu juz tertentu, termasuk coverage ayat, persentase completion,
 * dan status tuntas (100% coverage).
 *
 * @property int $id
 * @property int $progres_hafalan_id
 * @property int $juz_id
 * @property int $ayat_tercakup Number of unique ayat covered
 * @property int $total_ayat Total ayat in this juz (from Referensi Quran, denormalized)
 * @property float $persentase Coverage percentage 0.00-100.00
 * @property bool $status_tuntas True when persentase = 100%
 * @property \Illuminate\Support\Carbon|null $tanggal_tuntas Date when juz became 100% complete
 * @property array $detail_ayat_tercakup Array of [surah_id, ayat_mulai, ayat_selesai]
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read ProgresHafalan $progresHafalan
 * @property-read Juz $juz
 */
class CapaianJuz extends Model
{
    use HasFactory;

    protected $table = 'capaian_juzs';

    protected $fillable = [
        'progres_hafalan_id',
        'juz_id',
        'ayat_tercakup',
        'total_ayat',
        'persentase',
        'status_tuntas',
        'tanggal_tuntas',
        'detail_ayat_tercakup',
    ];

    protected function casts(): array
    {
        return [
            'ayat_tercakup' => 'integer',
            'total_ayat' => 'integer',
            'persentase' => 'decimal:2',
            'status_tuntas' => 'boolean',
            'tanggal_tuntas' => 'date',
            'detail_ayat_tercakup' => 'array',
        ];
    }

    /**
     * Relationship: CapaianJuz belongs to a ProgresHafalan
     */
    public function progresHafalan()
    {
        return $this->belongsTo(ProgresHafalan::class);
    }

    /**
     * Relationship: CapaianJuz belongs to a Juz (from Referensi Quran)
     */
    public function juz()
    {
        return $this->belongsTo(Juz::class);
    }

    /**
     * Get validation rules for this model
     *
     * @return array<string, string|array>
     */
    public static function validationRules(): array
    {
        return [
            'progres_hafalan_id' => 'required|exists:progres_hafalans,id',
            'juz_id' => 'required|exists:juzs,id',
            'ayat_tercakup' => 'required|integer|min:0',
            'total_ayat' => 'required|integer|min:1',
            'persentase' => 'required|numeric|min:0|max:100',
            'status_tuntas' => 'required|boolean',
            'tanggal_tuntas' => 'nullable|date',
            'detail_ayat_tercakup' => 'required|array',
        ];
    }

    /**
     * Scope: Get only completed (tuntas) juz
     */
    public function scopeTuntas($query)
    {
        return $query->where('status_tuntas', true);
    }

    /**
     * Scope: Get only incomplete juz
     */
    public function scopeBelumTuntas($query)
    {
        return $query->where('status_tuntas', false);
    }

    /**
     * Check if this juz is complete (100% coverage)
     *
     * @return bool
     */
    public function isTuntas(): bool
    {
        return $this->status_tuntas === true && $this->persentase == 100.00;
    }

    /**
     * Calculate and update persentase based on ayat_tercakup and total_ayat
     *
     * @return void
     */
    public function updatePersentase(): void
    {
        if ($this->total_ayat > 0) {
            $this->persentase = round(($this->ayat_tercakup / $this->total_ayat) * 100, 2);
            $this->save();
        }
    }
}
