<?php

namespace App\Enums;

enum StatusUsulan: string
{
    case DIAJUKAN = 'Diajukan';
    case DISETUJUI = 'Disetujui';
    case DITEMPATKAN = 'Ditempatkan';
    case DITOLAK = 'Ditolak';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::DIAJUKAN->value => 'Diajukan',
            self::DISETUJUI->value => 'Disetujui',
            self::DITEMPATKAN->value => 'Ditempatkan',
            self::DITOLAK->value => 'Ditolak',
        ];
    }

    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::values());
    }
}
