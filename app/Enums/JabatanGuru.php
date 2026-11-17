<?php

namespace App\Enums;

enum JabatanGuru: string
{
    case PEMBIMBING_TAHFIDZ = 'Guru Pembimbing Tahfidz';
    case GURU_MAPEL = 'Guru Mapel';
    case KEPALA_SEKOLAH = 'Kepala Sekolah';
    case TENDIK = 'Tendik';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::PEMBIMBING_TAHFIDZ->value => 'Guru Pembimbing Tahfidz',
            self::GURU_MAPEL->value => 'Guru Mapel',
            self::KEPALA_SEKOLAH->value => 'Kepala Sekolah',
            self::TENDIK->value => 'Tendik',
        ];
    }

    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::values());
    }
}
