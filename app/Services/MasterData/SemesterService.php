<?php

namespace App\Services\MasterData;

use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Traits\ValidatesForeignKeys;
use App\Traits\ChecksCascadeDelete;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SemesterService
{
    use ValidatesForeignKeys, ChecksCascadeDelete;

    /**
     * Create new semester with validation
     * Ensures dates are within tahun ajaran range and jenis is unique per tahun ajaran
     *
     * @param array $data
     * @return Semester
     * @throws ValidationException
     */
    public function create(array $data): Semester
    {
        // Validate foreign key reference with meaningful message
        $tahunAjaran = $this->validateTahunAjaran(
            $data['tahun_ajaran_id'],
            'tahun_ajaran_id'
        );

        // Validate dates are within tahun ajaran range
        if (isset($data['tanggal_mulai']) && \Carbon\Carbon::parse($data['tanggal_mulai'])->lt($tahunAjaran->tanggal_mulai)) {
            throw ValidationException::withMessages([
                'tanggal_mulai' => 'Tanggal mulai semester harus berada dalam rentang Tahun Ajaran'
            ]);
        }

        if (isset($data['tanggal_selesai']) && \Carbon\Carbon::parse($data['tanggal_selesai'])->gt($tahunAjaran->tanggal_selesai)) {
            throw ValidationException::withMessages([
                'tanggal_selesai' => 'Tanggal selesai semester harus berada dalam rentang Tahun Ajaran'
            ]);
        }

        // Validate date range
        if (isset($data['tanggal_mulai']) && isset($data['tanggal_selesai'])) {
            if ($data['tanggal_mulai'] >= $data['tanggal_selesai']) {
                throw ValidationException::withMessages([
                    'tanggal_selesai' => 'Tanggal selesai harus setelah tanggal mulai'
                ]);
            }
        }

        // Check for unique jenis per tahun ajaran
        $existingSemester = Semester::where('tahun_ajaran_id', $data['tahun_ajaran_id'])
            ->where('jenis', $data['jenis'])
            ->exists();

        if ($existingSemester) {
            throw ValidationException::withMessages([
                'jenis' => "Semester {$data['jenis']} sudah ada untuk Tahun Ajaran ini"
            ]);
        }

        return Semester::create($data);
    }

    /**
     * Update semester with validation
     *
     * @param int $id
     * @param array $data
     * @return Semester
     * @throws ValidationException
     */
    public function update(int $id, array $data): Semester
    {
        $semester = Semester::findOrFail($id);
        $tahunAjaranId = $data['tahun_ajaran_id'] ?? $semester->tahun_ajaran_id;

        // Validate tahun ajaran if it's being changed
        if (isset($data['tahun_ajaran_id'])) {
            $tahunAjaran = $this->validateTahunAjaran(
                $tahunAjaranId,
                'tahun_ajaran_id'
            );
        } else {
            $tahunAjaran = $semester->tahunAjaran;
        }

        // Validate dates are within tahun ajaran range
        $tanggalMulai = $data['tanggal_mulai'] ?? $semester->tanggal_mulai;
        $tanggalSelesai = $data['tanggal_selesai'] ?? $semester->tanggal_selesai;

        if ($tanggalMulai < $tahunAjaran->tanggal_mulai) {
            throw ValidationException::withMessages([
                'tanggal_mulai' => 'Tanggal mulai semester harus berada dalam rentang Tahun Ajaran'
            ]);
        }

        if ($tanggalSelesai > $tahunAjaran->tanggal_selesai) {
            throw ValidationException::withMessages([
                'tanggal_selesai' => 'Tanggal selesai semester harus berada dalam rentang Tahun Ajaran'
            ]);
        }

        // Validate date range
        if ($tanggalMulai >= $tanggalSelesai) {
            throw ValidationException::withMessages([
                'tanggal_selesai' => 'Tanggal selesai harus setelah tanggal mulai'
            ]);
        }

        // Check for unique jenis per tahun ajaran (excluding current record)
        if (isset($data['jenis'])) {
            $existingSemester = Semester::where('tahun_ajaran_id', $tahunAjaranId)
                ->where('jenis', $data['jenis'])
                ->where('id', '!=', $id)
                ->exists();

            if ($existingSemester) {
                throw ValidationException::withMessages([
                    'jenis' => "Semester {$data['jenis']} sudah ada untuk Tahun Ajaran ini"
                ]);
            }
        }

        $semester->update($data);
        return $semester->fresh();
    }

    /**
     * Delete semester
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id): bool
    {
        $semester = Semester::findOrFail($id);

        // Check for transaction dependencies (will be implemented in future modules)
        // Future: check for 'sesis', 'absensis', 'setorans' related to this semester
        // For now, allow deletion if no dependencies found

        return $semester->delete();
    }

    /**
     * Get all semesters with tahun ajaran info
     *
     * @param int|null $tahunAjaranId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithTahunAjaran(?int $tahunAjaranId = null)
    {
        $query = Semester::with('tahunAjaran')->orderBy('tanggal_mulai', 'desc');

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        return $query->get();
    }

    /**
     * Get active semester
     *
     * @return Semester|null
     */
    public function getActive(): ?Semester
    {
        return Semester::where('status_aktif', true)->first();
    }
}
