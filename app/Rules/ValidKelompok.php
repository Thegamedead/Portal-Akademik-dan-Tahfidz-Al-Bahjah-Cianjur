<?php

namespace App\Rules;

use App\Models\Kelompok;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidKelompok implements ValidationRule
{
    private bool $checkCapacity;

    public function __construct(bool $checkCapacity = false)
    {
        $this->checkCapacity = $checkCapacity;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Let required rule handle this
        }

        // Check if kelompok exists
        $kelompok = Kelompok::find($value);

        if (!$kelompok) {
            $fail('Kelompok yang dipilih tidak ditemukan.');
            return;
        }

        // Check capacity if requested
        if ($this->checkCapacity) {
            $jumlahSiswa = $kelompok->siswas()->count();
            if ($jumlahSiswa >= $kelompok->kapasitas_maks) {
                $fail('Kelompok sudah penuh, tidak dapat menambah siswa lagi.');
                return;
            }
        }
    }
}
