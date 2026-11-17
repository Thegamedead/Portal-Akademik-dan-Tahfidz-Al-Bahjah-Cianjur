<?php

namespace App\Enums;

enum StatusPenempatan: string
{
    case TERDAFTAR = 'Belum Ditempatkan';
    case DITEMPATKAN = 'Ditempatkan';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::TERDAFTAR->value => 'Belum Ditempatkan',
            self::DITEMPATKAN->value => 'Ditempatkan',
        ];
    }

    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::values());
    }
}
