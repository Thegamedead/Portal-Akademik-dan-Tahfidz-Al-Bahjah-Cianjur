<?php

namespace App\Enums;

enum StatusKehadiran: string
{
    case Hadir = 'Hadir';
    case Izin = 'Izin';
    case Sakit = 'Sakit';
    case Alfa = 'Alfa';
    case Libur = 'Libur';
}
