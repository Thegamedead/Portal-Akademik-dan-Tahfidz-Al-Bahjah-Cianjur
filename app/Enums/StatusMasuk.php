<?php

namespace App\Enums;

enum StatusMasuk: string
{
    case BARU = 'Baru';
    case PINDAHAN = 'Pindahan';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::BARU->value => 'Baru',
            self::PINDAHAN->value => 'Pindahan',
        ];
    }

    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::values());
    }
}
