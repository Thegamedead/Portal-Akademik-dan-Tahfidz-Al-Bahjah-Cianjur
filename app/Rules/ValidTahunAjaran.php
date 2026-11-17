<?php

namespace App\Rules;

use App\Models\TahunAjaran;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTahunAjaran implements ValidationRule
{
    private bool $mustBeActive;

    public function __construct(bool $mustBeActive = false)
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

        // Check if tahun ajaran exists
        $tahunAjaran = TahunAjaran::find($value);

        if (!$tahunAjaran) {
            $fail('Tahun ajaran yang dipilih tidak ditemukan.');
            return;
        }

        // Check if tahun ajaran must be active
        if ($this->mustBeActive && !$tahunAjaran->status) {
            $fail('Tahun ajaran yang dipilih tidak aktif.');
            return;
        }
    }
}
