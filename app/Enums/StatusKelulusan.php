<?php

namespace App\Enums;

enum StatusKelulusan: string
{
    case DIAJUKAN = 'Diajukan';
    case DISETUJUI = 'Disetujui';
    case DITOLAK = 'Ditolak';
    case DIBATALKAN = 'Dibatalkan';

    public function label(): string
    {
        return match($this) {
            self::DIAJUKAN => 'Diajukan',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak',
            self::DIBATALKAN => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DIAJUKAN => 'blue',
            self::DISETUJUI => 'green',
            self::DITOLAK => 'red',
            self::DIBATALKAN => 'gray',
        };
    }

    public function badge(): string
    {
        return match($this) {
            self::DIAJUKAN => 'bg-blue-100 text-blue-800 border-blue-200',
            self::DISETUJUI => 'bg-green-100 text-green-800 border-green-200',
            self::DITOLAK => 'bg-red-100 text-red-800 border-red-200',
            self::DIBATALKAN => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }
}
