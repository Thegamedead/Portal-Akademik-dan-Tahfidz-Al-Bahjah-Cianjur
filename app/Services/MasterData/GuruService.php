<?php

namespace App\Services\MasterData;

use App\Models\Guru;
use App\Models\GuruTugasTambahan;
use App\Models\Kelompok;
use App\Traits\ChecksCascadeDelete;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class GuruService
{
    use ChecksCascadeDelete;
    /**
     * Valid jabatan enum values
     */
    private const VALID_JABATAN = [
        'Guru Pembimbing Tahfidz',
        'Guru Mapel',
        'Kepala Sekolah',
        'Tendik'
    ];

    /**
     * Pendidikan levels that require program_studi
     */
    private const PENDIDIKAN_REQUIRE_PRODI = ['D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];

    /**
     * Register new teacher with validation
     *
     * @param array $data
     * @return Guru
     * @throws ValidationException
     */
    public function register(array $data): Guru
    {
        // Validate NIY uniqueness
        if (Guru::where('niy', $data['niy'])->exists()) {
            throw ValidationException::withMessages([
                'niy' => 'NIY sudah terdaftar dalam sistem'
            ]);
        }

        // Validate jabatan enum
        if (!in_array($data['jabatan'], self::VALID_JABATAN)) {
            throw ValidationException::withMessages([
                'jabatan' => 'Jabatan tidak valid. Pilihan: ' . implode(', ', self::VALID_JABATAN)
            ]);
        }

        // Set default status_aktif
        $data['status_aktif'] = $data['status_aktif'] ?? true;

        return Guru::create($data);
    }

    /**
     * Update teacher data
     *
     * @param int $id
     * @param array $data
     * @return Guru
     * @throws ValidationException
     */
    public function update(int $id, array $data): Guru
    {
        $guru = Guru::findOrFail($id);

        // Validate NIY uniqueness (excluding current record)
        if (isset($data['niy']) && $data['niy'] !== $guru->niy) {
            if (Guru::where('niy', $data['niy'])->where('id', '!=', $id)->exists()) {
                throw ValidationException::withMessages([
                    'niy' => 'NIY sudah terdaftar dalam sistem'
                ]);
            }
        }

        // Validate jabatan enum
        if (isset($data['jabatan']) && !in_array($data['jabatan'], self::VALID_JABATAN)) {
            throw ValidationException::withMessages([
                'jabatan' => 'Jabatan tidak valid. Pilihan: ' . implode(', ', self::VALID_JABATAN)
            ]);
        }

        $guru->update($data);
        return $guru->fresh();
    }

    /**
     * Update teacher status and handle soft delete
     *
     * @param int $id
     * @param bool $statusAktif
     * @return Guru
     */
    public function updateStatus(int $id, bool $statusAktif): Guru
    {
        $guru = Guru::findOrFail($id);

        // Update status_aktif
        $guru->status_aktif = $statusAktif;
        $guru->save();

        return $guru->fresh();
    }

    /**
     * Delete teacher with transaction check
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id): bool
    {
        $guru = Guru::withTrashed()->findOrFail($id);

        // Check for dependencies using the trait
        $this->checkDependencies($guru, [
            'kelompoksBimbingan' => 'kelompok yang dibimbing',
            // tugasTambahans are cascade deleted, not a blocking dependency
            // Future modules: 'sesis', 'setorans'
        ], 'Guru');

        return DB::transaction(function () use ($guru) {
            // Cascade delete tugas tambahan
            GuruTugasTambahan::where('guru_id', $guru->id)->delete();

            // Delete foto file if exists
            if ($guru->foto && Storage::exists($guru->foto)) {
                Storage::delete($guru->foto);
            }

            // Permanent delete
            return $guru->forceDelete();
        });
    }

    /**
     * Restore soft-deleted teacher
     *
     * @param int $id
     * @return Guru
     */
    public function restore(int $id): Guru
    {
        $guru = Guru::findOrFail($id);
        $guru->status_aktif = true;
        $guru->save();

        return $guru->fresh();
    }

    /**
     * Add tugas tambahan to guru
     *
     * @param int $guruId
     * @param string $jenisTugas
     * @return GuruTugasTambahan
     * @throws ValidationException
     */
    public function addTugasTambahan(int $guruId, string $jenisTugas): GuruTugasTambahan
    {
        $guru = Guru::findOrFail($guruId);

        // Check if tugas already exists
        $exists = GuruTugasTambahan::where('guru_id', $guruId)
            ->where('jenis_tugas', $jenisTugas)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'jenis_tugas' => 'Tugas tambahan ini sudah terdaftar untuk guru ini'
            ]);
        }

        return GuruTugasTambahan::create([
            'guru_id' => $guruId,
            'jenis_tugas' => $jenisTugas
        ]);
    }

    /**
     * Remove tugas tambahan from guru
     *
     * @param int $tugasTambahanId
     * @return bool
     */
    public function removeTugasTambahan(int $tugasTambahanId): bool
    {
        $tugasTambahan = GuruTugasTambahan::findOrFail($tugasTambahanId);
        return $tugasTambahan->delete();
    }

    /**
     * Get all teachers with filters
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getAllWithFilters(array $filters = [])
    {
        $query = Guru::with('tugasTambahans');

        if (isset($filters['jabatan'])) {
            $query->where('jabatan', $filters['jabatan']);
        }

        if (isset($filters['status_aktif'])) {
            $query->where('status_aktif', $filters['status_aktif']);
        }

        if (isset($filters['jenis_kelamin'])) {
            $query->where('jenis_kelamin', $filters['jenis_kelamin']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('niy', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Get pembimbing tahfidz gurus
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPembimbingTahfidz()
    {
        return Guru::where('jabatan', 'Guru Pembimbing Tahfidz')
            ->where('status_aktif', true)
            ->get();
    }

    /**
     * Get guru's kelompok bimbingan
     *
     * @param int $guruId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getKelompokBimbingan(int $guruId)
    {
        return Kelompok::where('guru_pembimbing_id', $guruId)
            ->with('tahunAjaran')
            ->get();
    }
}
