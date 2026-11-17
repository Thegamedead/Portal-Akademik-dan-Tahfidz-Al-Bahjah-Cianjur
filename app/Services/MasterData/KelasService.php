<?php

namespace App\Services\MasterData;

use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Support\Facades\DB;

class KelasService
{
    /**
     * Create single kelas with validation
     */
    public function create(array $data): Kelas
    {
        // Validate uniqueness: (tahun_ajaran_id, tingkat, rombel)
        $exists = Kelas::where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->where('tingkat', $data['tingkat'])
            ->where('rombel', $data['rombel'])
            ->exists();

        if ($exists) {
            throw new \InvalidArgumentException(
                "Kelas {$data['tingkat']}{$data['rombel']} sudah ada untuk tahun ajaran ini"
            );
        }

        // Set default kapasitas if not provided
        $data['kapasitas'] = $data['kapasitas'] ?? 30;

        // Validate wali_kelas_id if provided
        if (!empty($data['wali_kelas_id'])) {
            $guru = Guru::find($data['wali_kelas_id']);
            if (!$guru || !$guru->status_aktif) {
                throw new \InvalidArgumentException('Guru tidak valid atau tidak aktif');
            }
        }

        return Kelas::create($data);
    }

    /**
     * Update kelas with validation
     */
    public function update(int $id, array $data): Kelas
    {
        $kelas = Kelas::findOrFail($id);

        // Validate uniqueness if key fields changed
        if (isset($data['tahun_ajaran_id']) || isset($data['tingkat']) || isset($data['rombel'])) {
            $tahunAjaranId = $data['tahun_ajaran_id'] ?? $kelas->tahun_ajaran_id;
            $tingkat = $data['tingkat'] ?? $kelas->tingkat;
            $rombel = $data['rombel'] ?? $kelas->rombel;

            $exists = Kelas::where('tahun_ajaran_id', $tahunAjaranId)
                ->where('tingkat', $tingkat)
                ->where('rombel', $rombel)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                throw new \InvalidArgumentException(
                    "Kelas {$tingkat}{$rombel} sudah ada untuk tahun ajaran ini"
                );
            }
        }

        // Validate wali_kelas_id if changed
        if (isset($data['wali_kelas_id']) && !empty($data['wali_kelas_id'])) {
            $guru = Guru::find($data['wali_kelas_id']);
            if (!$guru || !$guru->status_aktif) {
                throw new \InvalidArgumentException('Guru tidak valid atau tidak aktif');
            }
        }

        $kelas->update($data);
        return $kelas->fresh();
    }

    /**
     * Delete kelas with siswa check
     */
    public function delete(int $id): bool
    {
        $kelas = Kelas::findOrFail($id);

        // Check if kelas has siswa
        if ($kelas->siswas()->count() > 0) {
            throw new \RuntimeException(
                "Tidak dapat menghapus kelas {$kelas->nama_lengkap} karena masih memiliki {$kelas->jumlah_siswa} siswa"
            );
        }

        return $kelas->delete();
    }

    /**
     * Bulk create kelas with transaction
     */
    public function bulkCreate(array $config): array
    {
        $tahunAjaranId = $config['tahun_ajaran_id'];
        $kelasToCreate = [];
        $conflicts = [];

        // Build list of kelas to create
        foreach (['7', '8', '9'] as $tingkat) {
            $key = "tingkat_{$tingkat}";

            if (!isset($config[$key]) || !$config[$key]['active']) {
                continue;
            }

            $jumlahRombel = $config[$key]['jumlah_rombel'];
            $kapasitas = $config[$key]['kapasitas'] ?? 30;

            // Generate rombel A, B, C, ... based on jumlah
            for ($i = 0; $i < $jumlahRombel; $i++) {
                $rombel = chr(65 + $i); // 65 = 'A'

                // Check for conflicts
                $exists = Kelas::where('tahun_ajaran_id', $tahunAjaranId)
                    ->where('tingkat', $tingkat)
                    ->where('rombel', $rombel)
                    ->exists();

                if ($exists) {
                    $conflicts[] = "{$tingkat}{$rombel}";
                    continue;
                }

                $kelasToCreate[] = [
                    'tahun_ajaran_id' => $tahunAjaranId,
                    'tingkat' => $tingkat,
                    'rombel' => $rombel,
                    'kapasitas' => $kapasitas,
                    'wali_kelas_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // If conflicts found, abort entire operation
        if (!empty($conflicts)) {
            throw new \RuntimeException(
                'Kelas berikut sudah ada: ' . implode(', ', $conflicts) . '. Operasi dibatalkan.'
            );
        }

        // All-or-nothing transaction
        DB::transaction(function () use ($kelasToCreate) {
            foreach ($kelasToCreate as $kelasData) {
                Kelas::create($kelasData);
            }
        });

        return [
            'created_count' => count($kelasToCreate),
            'kelas_list' => array_map(fn($k) => $k['tingkat'] . $k['rombel'], $kelasToCreate),
        ];
    }
}
