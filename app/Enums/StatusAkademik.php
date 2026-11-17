<?php

namespace App\Enums;

enum StatusAkademik: string
{
    case AKTIF = 'Aktif';
    case LULUS_SMP = 'Lulus SMP';
    case PINDAH_SEKOLAH = 'Pindah Sekolah';
    case KELUAR = 'Keluar';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::AKTIF->value => 'Aktif',
            self::LULUS_SMP->value => 'Lulus SMP',
            self::PINDAH_SEKOLAH->value => 'Pindah Sekolah',
            self::KELUAR->value => 'Keluar',
        ];
    }

    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::values());
    }
}
