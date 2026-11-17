<?php

namespace App\Enums;

enum JenisOrangTuaWali: string
{
    case AYAH = 'Ayah';
    case IBU = 'Ibu';
    case WALI = 'Wali';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::AYAH->value => 'Ayah',
            self::IBU->value => 'Ibu',
            self::WALI->value => 'Wali',
        ];
    }

    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::values());
    }
}
