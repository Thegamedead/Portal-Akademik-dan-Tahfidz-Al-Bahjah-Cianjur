<?php

namespace App\Services\MasterData;

use App\Models\Kelas;
use App\Models\Siswa;

class KelasIntegrationService
{
    /**
     * Update siswa kelas assignment
     */
    public function assignSiswaToKelas(int $siswaId, ?int $kelasId): Siswa
    {
        $siswa = Siswa::findOrFail($siswaId);

        // Validate kelas exists if not null
        if ($kelasId !== null) {
            $kelas = Kelas::findOrFail($kelasId);
        }

        $siswa->update(['kelas_id' => $kelasId]);

        return $siswa->fresh();
    }

    /**
     * Get capacity warning for kelas
     */
    public function getCapacityStatus(int $kelasId): array
    {
        $kelas = Kelas::with('siswas')->findOrFail($kelasId);

        return [
            'jumlah_siswa' => $kelas->jumlah_siswa,
            'kapasitas' => $kelas->kapasitas,
            'is_over_capacity' => $kelas->is_over_capacity,
            'percentage' => ($kelas->jumlah_siswa / $kelas->kapasitas) * 100,
        ];
    }
}
