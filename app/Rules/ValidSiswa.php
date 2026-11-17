<?php

namespace App\Rules;

use App\Models\Siswa;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidSiswa implements ValidationRule
{
    private bool $mustBeActive;

    public function __construct(bool $mustBeActive = true)
    {
        $this->mustBeActive = $mustBeActive;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Let required rule handle this
        }

        // Check if siswa exists
        $siswa = Siswa::find($value);

        if (!$siswa) {
            $fail('Siswa yang dipilih tidak ditemukan.');
            return;
        }

        // Check if siswa must be active
        if ($this->mustBeActive && $siswa->status_akademik !== 'Aktif') {
            $fail('Siswa yang dipilih tidak aktif atau sudah lulus/pindah/keluar.');
            return;
        }
    }
}
