<?php

namespace App\Enums;

enum TingkatKelompok: string
{
    case DASAR = 'Dasar';
    case MENENGAH = 'Menengah';
    case ATAS = 'Atas';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::DASAR->value => 'Dasar',
            self::MENENGAH->value => 'Menengah',
            self::ATAS->value => 'Atas',
        ];
    }

    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::values());
    }
}
