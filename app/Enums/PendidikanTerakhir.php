<?php

namespace App\Enums;

enum PendidikanTerakhir: string
{
    case SD = 'SD';
    case SMP = 'SMP';
    case SMA = 'SMA';
    case D1 = 'D1';
    case D2 = 'D2';
    case D3 = 'D3';
    case D4 = 'D4';
    case S1 = 'S1';
    case S2 = 'S2';
    case S3 = 'S3';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return [
            self::SD->value => 'SD',
            self::SMP->value => 'SMP',
            self::SMA->value => 'SMA',
            self::D1->value => 'D1',
            self::D2->value => 'D2',
            self::D3->value => 'D3',
            self::D4->value => 'D4',
            self::S1->value => 'S1',
            self::S2->value => 'S2',
            self::S3->value => 'S3',
        ];
    }

    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::values());
    }

    /**
     * Check if pendidikan requires program_studi
     */
    public function requiresProgramStudi(): bool
    {
        return in_array($this->value, ['D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3']);
    }

    /**
     * Get pendidikan levels that require program_studi
     */
    public static function levelsRequiringProgramStudi(): array
    {
        return ['D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];
    }
}
