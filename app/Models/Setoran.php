<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\{JenisSetoran, SkorKomponen, Keputusan};

class Setoran extends Model
{
    use HasFactory;

    protected $table = 'setorans';

    protected $fillable = [
        'absensi_id',
        'jenis_setoran',
        'juz_id',
        'surah_id',
        'ayat_mulai',
        'ayat_selesai',
        'skor_tajwid',
        'skor_kelancaran',
        'skor_makhraj',
        'skor_fashahah',
        'predikat',
        'keputusan',
        'catatan',
        'saran_ayat_mulai',
        'saran_ayat_selesai',
        'saran_surah_id',
        'saran_juz_id',
    ];

    protected $casts = [
        'jenis_setoran' => JenisSetoran::class,
        'skor_tajwid' => SkorKomponen::class,
        'skor_kelancaran' => SkorKomponen::class,
        'skor_makhraj' => SkorKomponen::class,
        'skor_fashahah' => SkorKomponen::class,
        'predikat' => SkorKomponen::class,
        'keputusan' => Keputusan::class,
        'ayat_mulai' => 'integer',
        'ayat_selesai' => 'integer',
        'saran_ayat_mulai' => 'integer',
        'saran_ayat_selesai' => 'integer',
    ];

    // Relationships

    /**
     * Get the absensi that owns the setoran
     */
    public function absensi(): BelongsTo
    {
        return $this->belongsTo(Absensi::class);
    }

    /**
     * Get the juz for this setoran
     */
    public function juz(): BelongsTo
    {
        return $this->belongsTo(Juz::class);
    }

    /**
     * Get the surah for this setoran
     */
    public function surah(): BelongsTo
    {
        return $this->belongsTo(Surah::class);
    }

    /**
     * Get the suggested surah for next session (nullable)
     */
    public function saranSurah(): BelongsTo
    {
        return $this->belongsTo(Surah::class, 'saran_surah_id');
    }

    /**
     * Get the suggested juz for next session (nullable)
     */
    public function saranJuz(): BelongsTo
    {
        return $this->belongsTo(Juz::class, 'saran_juz_id');
    }

    // Business Logic Methods

    /**
     * Calculate predikat from 4 component scores
     * Req 7: predikat = MIN(all 4 scores)
     *
     * @param SkorKomponen $tajwid
     * @param SkorKomponen $kelancaran
     * @param SkorKomponen $makhraj
     * @param SkorKomponen $fashahah
     * @return SkorKomponen
     */
    public static function calculatePredikat(
        ?SkorKomponen $tajwid = null,
        ?SkorKomponen $kelancaran = null,
        ?SkorKomponen $makhraj = null,
        ?SkorKomponen $fashahah = null
    ): SkorKomponen {
        $scores = [];
        if ($tajwid) $scores[] = $tajwid->value();
        if ($kelancaran) $scores[] = $kelancaran->value();
        if ($makhraj) $scores[] = $makhraj->value();
        if ($fashahah) $scores[] = $fashahah->value();

        if (empty($scores)) {
            return SkorKomponen::Lancar; // Fallback jika tidak ada komponen yang dinilai
        }

        $minValue = min($scores);

        return SkorKomponen::fromMinValue($minValue);
    }

    /**
     * Generate suggestion for next session
     * Req 9: Based on keputusan
     * - If Ulang: repeat same ayat
     * - If Lanjut: suggest next ayat in sequence
     */
    public function generateSaranAyat(): void
    {
        if ($this->keputusan === Keputusan::Ulang) {
            // Repeat same ayat
            $this->saran_ayat_mulai = $this->ayat_mulai;
            $this->saran_ayat_selesai = $this->ayat_selesai;
            $this->saran_surah_id = $this->surah_id;
            $this->saran_juz_id = $this->juz_id;
        } else {
            // Calculate next ayat in sequence
            $nextAyat = $this->ayat_selesai + 1;
            $surah = $this->surah;

            if ($nextAyat <= $surah->jumlah_ayat) {
                // Same surah
                $this->saran_ayat_mulai = $nextAyat;
                $this->saran_ayat_selesai = min($nextAyat + 2, $surah->jumlah_ayat); // suggest ~3 ayat
                $this->saran_surah_id = $this->surah_id;
                $this->saran_juz_id = $this->juz_id;
            } else {
                // Move to next surah in curriculum
                $nextSurah = Surah::where('urutan_mushaf', '>', $surah->urutan_mushaf)
                    ->orderBy('urutan_mushaf')
                    ->first();

                if ($nextSurah) {
                    $this->saran_ayat_mulai = 1;
                    $this->saran_ayat_selesai = min(3, $nextSurah->jumlah_ayat);
                    $this->saran_surah_id = $nextSurah->id;
                    // Find juz for this surah
                    $this->saran_juz_id = $nextSurah->juzs()->first()?->id;
                }
            }
        }
    }

    /**
     * Check if this setoran contributes to juz progress
     * Req 5: Only Hafalan Baru + Lanjut counts
     * Ulang (review) NEVER contributes, even if keputusan = Lanjut
     *
     * @return bool
     */
    public function contributesToProgress(): bool
    {
        return $this->jenis_setoran === JenisSetoran::HafalanBaru
            && $this->keputusan === Keputusan::Lanjut;
    }
}
