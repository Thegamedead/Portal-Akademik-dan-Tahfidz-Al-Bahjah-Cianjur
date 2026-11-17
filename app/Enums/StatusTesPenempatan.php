<?php

namespace App\Enums;

enum StatusTesPenempatan: string
{
    case TERJADWAL = 'Terjadwal';
    case SELESAI = 'Selesai';
    case DIBATALKAN = 'Dibatalkan';
}
