<?php

namespace App\Rules;

use App\Models\Guru;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidGuruPembimbingTahfidz implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Let required rule handle this
        }

        // Check if guru exists
        $guru = Guru::find($value);

        if (!$guru) {
            $fail('Guru yang dipilih tidak ditemukan.');
            return;
        }

        // Check if guru is active
        if (!$guru->status_aktif) {
            $fail('Guru yang dipilih sudah tidak aktif.');
            return;
        }

        // Check if guru has correct jabatan
        if ($guru->jabatan !== 'Guru Pembimbing Tahfidz') {
            $fail('Guru yang dipilih harus memiliki jabatan Guru Pembimbing Tahfidz.');
            return;
        }
    }
}
