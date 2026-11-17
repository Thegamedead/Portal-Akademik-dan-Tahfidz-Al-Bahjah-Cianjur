<?php

namespace App\Enums;

enum JenisSetoran: string
{
    case HafalanBaru = 'Hafalan Baru';
    case Ulang = 'Ulang';

    public function label(): string
    {
        return match($this) {
            self::HafalanBaru => 'Hafalan Baru',
            self::Ulang => 'Ulang',
        };
    }
}
