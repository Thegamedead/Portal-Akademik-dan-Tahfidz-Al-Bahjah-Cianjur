<?php

namespace App\Enums;

enum StatusPernikahan: string
{
    case MENIKAH = 'Menikah';
    case BELUM_MENIKAH = 'Belum Menikah';
    case CERAI = 'Cerai';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::MENIKAH->value => 'Menikah',
            self::BELUM_MENIKAH->value => 'Belum Menikah',
            self::CERAI->value => 'Cerai',
        ];
    }

    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::values());
    }
}
