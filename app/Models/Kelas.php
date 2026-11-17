<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kelas';

    protected $fillable = [
        'tingkat',
        'nama_kelas',
        'ruang_kelas',
        'jumlah_siswa',
        'nama_wali_kelas',
    ];

    /**
     * Relationships
     */
    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    /**
     * Virtual Accessors
     */
    public function getNamaLengkapAttribute(): string
    {
        // nama_kelas sudah berformat "7A", tidak perlu concat dengan tingkat lagi
        return $this->nama_kelas;
    }

    public function getJumlahSiswaAktifAttribute(): int
    {
        return $this->siswas()->count();
    }

    /**
     * Scopes
     */
    public function scopeByTingkat($query, string $tingkat)
    {
        return $query->where('tingkat', $tingkat);
    }

    // ========================================================================
    // ROLLOVER SYSTEM HELPERS
    // ========================================================================

    /**
     * Get the next grade level Kelas
     *
     * Attempts to find the Kelas for the next grade level with the same section.
     * For example:
     * - Kelas 7A → Kelas 8A
     * - Kelas 8B → Kelas 9B
     * - Kelas 9 → null (no next grade after graduation)
     *
     * @return self|null The next grade Kelas or null if no next grade exists
     */
    public function getNextGradeKelas(): ?self
    {
        // Determine next grade level
        $nextTingkat = match($this->tingkat) {
            '7' => '8',
            '8' => '9',
            '9' => null, // No next grade after 9 (graduation level)
            default => null,
        };

        // If no next grade exists (e.g., Kelas 9), return null
        if (!$nextTingkat) {
            return null;
        }

        // Extract the section letter from nama_kelas (e.g., "7A" → "A")
        // Assuming nama_kelas format is "{tingkat}{section}" like "7A", "8B"
        $section = str_replace($this->tingkat, '', $this->nama_kelas);

        // Find the next grade Kelas with the same section
        // Example: "7" + "A" → "8A"
        $nextNamaKelas = $nextTingkat . $section;

        return self::where('tingkat', $nextTingkat)
                   ->where('nama_kelas', $nextNamaKelas)
                   ->first();
    }
}
