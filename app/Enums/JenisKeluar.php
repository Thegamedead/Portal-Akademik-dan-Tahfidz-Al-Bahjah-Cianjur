<?php

namespace App\Enums;

enum JenisKeluar: string
{
    case LULUS_SMP = 'Lulus SMP';
    case PINDAH_SEKOLAH = 'Pindah Sekolah';
    case MENINGGAL = 'Meninggal';
    case KELUAR = 'Keluar';

    /**
     * Get all enum values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get enum options for dropdown (value => label)
     */
    public static function options(): array
    {
        return [
            self::LULUS_SMP->value => 'Lulus SMP',
            self::PINDAH_SEKOLAH->value => 'Pindah Sekolah',
            self::MENINGGAL->value => 'Meninggal',
            self::KELUAR->value => 'Keluar (DO)',
        ];
    }

    /**
     * Get validation rule string
     */
    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::values());
    }
}
