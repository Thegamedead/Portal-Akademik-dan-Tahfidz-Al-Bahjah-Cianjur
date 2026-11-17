<?php

namespace App\Enums;

enum SkorKomponen: string
{
    case Lancar = 'Lancar';
    case KurangLancar = 'Kurang Lancar';
    case TidakLancar = 'Tidak Lancar';

    /**
     * Convert enum to numeric value for comparison
     * Lancar = 3 (highest), Kurang Lancar = 2, Tidak Lancar = 1 (lowest)
     */
    public function value(): int
    {
        return match($this) {
            self::Lancar => 3,
            self::KurangLancar => 2,
            self::TidakLancar => 1,
        };
    }

    /**
     * Create enum from minimum numeric value
     * Used for predikat calculation (MIN of 4 scores)
     */
    public static function fromMinValue(int $minValue): self
    {
        return match($minValue) {
            3 => self::Lancar,
            2 => self::KurangLancar,
            1 => self::TidakLancar,
            default => self::TidakLancar,
        };
    }
}
