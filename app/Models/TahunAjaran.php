<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajarans';

    protected $fillable = [
        'label',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'status' => 'boolean',
        ];
    }

    /**
     * Get all semesters for this tahun ajaran
     */
    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }

    /**
     * Get all kelompoks for this tahun ajaran
     */
    public function kelompoks()
    {
        return $this->hasMany(Kelompok::class);
    }

    /**
     * Scope a query to only include active tahun ajaran
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // ========================================================================
    // ROLLOVER SYSTEM METHODS
    // ========================================================================

    /**
     * Activate this Tahun Ajaran and deactivate all others
     *
     * This method ensures that only one Tahun Ajaran is active at a time
     * by setting all other records to inactive before activating this one.
     *
     * @return void
     */
    public function activate(): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () {
            // Deactivate all Tahun Ajaran records
            self::query()->update(['status' => false]);

            // Activate this Tahun Ajaran
            $this->update(['status' => true]);
        });
    }

    /**
     * Get the currently active Tahun Ajaran
     *
     * Returns the Tahun Ajaran record with status = true.
     * There should only be one active Tahun Ajaran at any time.
     *
     * @return self|null The active Tahun Ajaran or null if none is active
     */
    public static function getActive(): ?self
    {
        return self::where('status', true)->first();
    }

    /**
     * Get or create Tahun Ajaran by a given date (e.g. tanggal_masuk)
     * Rule: School year starts on July 1 (Month >= 7 -> Year/Year+1; Month < 7 -> Year-1/Year)
     */
    public static function getOrCreateByDate($date): ?self
    {
        if (!$date) return self::getActive();
        $carbon = \Carbon\Carbon::parse($date);

        // 1. Try finding matching by date range
        $tahun = self::where('tanggal_mulai', '<=', $carbon->toDateString())
            ->where('tanggal_selesai', '>=', $carbon->toDateString())
            ->first();

        if ($tahun) {
            return $tahun;
        }

        // 2. Determine academic year label based on July 1 - June 30 cycle
        $startYear = $carbon->month >= 7 ? $carbon->year : $carbon->year - 1;
        $endYear = $startYear + 1;
        $label = "{$startYear}/{$endYear}";

        // Try finding matching label
        $tahun = self::where('label', $label)->first();
        if ($tahun) {
            return $tahun;
        }

        // 3. Create if not found
        return self::create([
            'label'           => $label,
            'tanggal_mulai'   => "{$startYear}-07-01",
            'tanggal_selesai' => "{$endYear}-06-30",
            'status'          => false,
        ]);
    }
}
