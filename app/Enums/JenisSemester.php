<?php

namespace App\Enums;

enum JenisSemester: string
{
    case GANJIL = 'Ganjil';
    case GENAP = 'Genap';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::GANJIL->value => 'Ganjil',
            self::GENAP->value => 'Genap',
        ];
    }

    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::values());
    }
}
