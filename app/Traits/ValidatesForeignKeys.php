<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

trait ValidatesForeignKeys
{
    /**
     * Validate that a foreign key reference exists and is valid
     *
     * @param string $modelClass The model class to check against
     * @param int|string $id The ID to validate
     * @param string $fieldName The field name for error messages
     * @param array $conditions Additional conditions to check (optional)
     * @param string|null $customMessage Custom error message (optional)
     * @return Model The found model instance
     * @throws ValidationException If validation fails
     */
    protected function validateForeignKey(
        string $modelClass,
        int|string $id,
        string $fieldName,
        array $conditions = [],
        ?string $customMessage = null
    ): Model {
        // Check if ID is provided
        if (empty($id)) {
            throw ValidationException::withMessages([
                $fieldName => $customMessage ?? ucfirst(str_replace('_', ' ', $fieldName)) . ' wajib dipilih'
            ]);
        }

        // Find the model
        $query = $modelClass::where('id', $id);

        // Apply additional conditions
        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        $model = $query->first();

        if (!$model) {
            // Generate meaningful error message based on model type
            $entityName = $this->getEntityDisplayName($modelClass);
            $defaultMessage = $entityName . ' yang dipilih tidak ditemukan atau tidak valid';

            throw ValidationException::withMessages([
                $fieldName => $customMessage ?? $defaultMessage
            ]);
        }

        return $model;
    }

    /**
     * Validate multiple foreign key references at once
     *
     * @param array $validations Array of validation configs
     * @return array Array of validated models
     * @throws ValidationException If any validation fails
     */
    protected function validateMultipleForeignKeys(array $validations): array
    {
        $validatedModels = [];

        foreach ($validations as $validation) {
            $model = $this->validateForeignKey(
                $validation['model'],
                $validation['id'],
                $validation['field'],
                $validation['conditions'] ?? [],
                $validation['message'] ?? null
            );

            $validatedModels[$validation['field']] = $model;
        }

        return $validatedModels;
    }

    /**
     * Validate that a Guru has the correct jabatan for a role
     *
     * @param int $guruId
     * @param string|array $requiredJabatan
     * @param string $fieldName
     * @return \App\Models\Guru
     * @throws ValidationException
     */
    protected function validateGuruJabatan(int $guruId, string|array $requiredJabatan, string $fieldName = 'guru_pembimbing_id'): \App\Models\Guru
    {
        $guru = $this->validateForeignKey(
            \App\Models\Guru::class,
            $guruId,
            $fieldName,
            ['status_aktif' => true],
            'Guru yang dipilih tidak ditemukan atau sudah tidak aktif'
        );

        $jabatanArray = is_array($requiredJabatan) ? $requiredJabatan : [$requiredJabatan];

        if (!in_array($guru->jabatan, $jabatanArray)) {
            $jabatanText = is_array($requiredJabatan)
                ? implode(' atau ', $requiredJabatan)
                : $requiredJabatan;

            throw ValidationException::withMessages([
                $fieldName => "Guru yang dipilih harus memiliki jabatan {$jabatanText}"
            ]);
        }

        return $guru;
    }

    /**
     * Validate that a TahunAjaran exists and is accessible
     *
     * @param int $tahunAjaranId
     * @param string $fieldName
     * @param bool $mustBeActive Whether the tahun ajaran must be active
     * @return \App\Models\TahunAjaran
     * @throws ValidationException
     */
    protected function validateTahunAjaran(int $tahunAjaranId, string $fieldName = 'tahun_ajaran_id', bool $mustBeActive = false): \App\Models\TahunAjaran
    {
        $conditions = $mustBeActive ? ['status' => true] : [];
        $customMessage = $mustBeActive
            ? 'Tahun ajaran yang dipilih tidak aktif'
            : 'Tahun ajaran yang dipilih tidak ditemukan';

        return $this->validateForeignKey(
            \App\Models\TahunAjaran::class,
            $tahunAjaranId,
            $fieldName,
            $conditions,
            $customMessage
        );
    }

    /**
     * Validate that a Kelompok exists and has capacity (if checking for student placement)
     *
     * @param int $kelompokId
     * @param string $fieldName
     * @param bool $checkCapacity Whether to check if kelompok has available capacity
     * @return \App\Models\Kelompok
     * @throws ValidationException
     */
    protected function validateKelompok(int $kelompokId, string $fieldName = 'kelompok_id', bool $checkCapacity = false): \App\Models\Kelompok
    {
        $kelompok = $this->validateForeignKey(
            \App\Models\Kelompok::class,
            $kelompokId,
            $fieldName,
            [],
            'Kelompok yang dipilih tidak ditemukan'
        );

        if ($checkCapacity) {
            $jumlahSiswa = $kelompok->siswas()->count();
            if ($jumlahSiswa >= $kelompok->kapasitas_maks) {
                throw ValidationException::withMessages([
                    $fieldName => 'Kelompok sudah penuh, tidak dapat menambah siswa lagi'
                ]);
            }
        }

        return $kelompok;
    }

    /**
     * Validate that a Siswa exists and is active
     *
     * @param int $siswaId
     * @param string $fieldName
     * @param bool $mustBeActive Whether student must have status_akademik = Aktif
     * @return \App\Models\Siswa
     * @throws ValidationException
     */
    protected function validateSiswa(int $siswaId, string $fieldName = 'siswa_id', bool $mustBeActive = true): \App\Models\Siswa
    {
        $conditions = $mustBeActive ? ['status_akademik' => 'Aktif'] : [];
        $customMessage = $mustBeActive
            ? 'Siswa yang dipilih tidak aktif atau sudah lulus/pindah/keluar'
            : 'Siswa yang dipilih tidak ditemukan';

        return $this->validateForeignKey(
            \App\Models\Siswa::class,
            $siswaId,
            $fieldName,
            $conditions,
            $customMessage
        );
    }

    /**
     * Get display name for entity based on model class
     *
     * @param string $modelClass
     * @return string
     */
    private function getEntityDisplayName(string $modelClass): string
    {
        $entityNames = [
            \App\Models\TahunAjaran::class => 'Tahun ajaran',
            \App\Models\Semester::class => 'Semester',
            \App\Models\Siswa::class => 'Siswa',
            \App\Models\Guru::class => 'Guru',
            \App\Models\Kelompok::class => 'Kelompok',
            \App\Models\OrangTuaWali::class => 'Orang tua/wali',
            \App\Models\GuruTugasTambahan::class => 'Tugas tambahan guru',
            \App\Models\ReferensiQuran::class => 'Referensi Qur\'an',
        ];

        return $entityNames[$modelClass] ?? 'Data';
    }
}
