<?php

namespace App\Services\MasterData;

use App\Models\Sekolah;

class SekolahService
{
    /**
     * Get singleton instance or return empty model
     */
    public function getOrNew(): Sekolah
    {
        return Sekolah::getInstance() ?? new Sekolah();
    }

    /**
     * Create or update singleton with validation
     */
    public function save(array $data): Sekolah
    {
        // Validate NPSN format (8 digits)
        if (!preg_match('/^\d{8}$/', $data['npsn'])) {
            throw new \InvalidArgumentException('NPSN harus 8 digit angka');
        }

        // Validate email format if provided
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Format email tidak valid');
        }

        // Validate website URL if provided
        if (!empty($data['website']) && !filter_var($data['website'], FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Format website tidak valid');
        }

        return Sekolah::createOrUpdateSingleton($data);
    }
}
