<?php

namespace App\Enums;

enum JenisKelamin: string
{
    case LAKI_LAKI = 'Laki-laki';
    case PEREMPUAN = 'Perempuan';

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
            self::LAKI_LAKI->value => 'Laki-laki',
            self::PEREMPUAN->value => 'Perempuan',
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
