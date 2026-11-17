<?php

namespace App\Enums;

enum StatusTahfidz: string
{
    case SEDANG_BERJALAN = 'Sedang Berjalan';
    case LULUS_PROGRAM = 'Lulus Program Tahfidz';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::SEDANG_BERJALAN->value => 'Sedang Berjalan',
            self::LULUS_PROGRAM->value => 'Lulus Program Tahfidz',
        ];
    }

    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::values());
    }
}
