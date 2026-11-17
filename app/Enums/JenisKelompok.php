<?php

namespace App\Enums;

enum JenisKelompok: string
{
    case BANIN = 'Banin';
    case BANAT = 'Banat';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::BANIN->value => 'Banin (Putra)',
            self::BANAT->value => 'Banat (Putri)',
        ];
    }

    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::values());
    }
}
