<?php

namespace App\Services\MasterData;

use App\Models\TahunAjaran;
use App\Models\Kelompok;
use App\Traits\ChecksCascadeDelete;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TahunAjaranService
{
    use ChecksCascadeDelete;

    /**
     * Create new tahun ajaran with validation
     * Ensures only one active tahun ajaran at a time
     *
     * @param array $data
     * @return TahunAjaran
     * @throws ValidationException
     */
    public function create(array $data): TahunAjaran
    {
        // Validate date range
        if (isset($data['tanggal_mulai']) && isset($data['tanggal_selesai'])) {
            if ($data['tanggal_mulai'] >= $data['tanggal_selesai']) {
                throw ValidationException::withMessages([
                    'tanggal_selesai' => 'Tanggal selesai harus setelah tanggal mulai'
                ]);
            }
        }

        return DB::transaction(function () use ($data) {
            // If this tahun ajaran is active, deactivate all others
            if (isset($data['status']) && $data['status']) {
                TahunAjaran::where('status', true)->update(['status' => false]);
                
                // Deactivate all semesters globally to prevent mismatch
                \App\Models\Semester::query()->update(['status_aktif' => false]);
            }

            $tahunAjaran = TahunAjaran::create($data);

            // Automatically create 2 default semesters for this academic year
            $startYear = \Carbon\Carbon::parse($tahunAjaran->tanggal_mulai)->year;
            $endYear = \Carbon\Carbon::parse($tahunAjaran->tanggal_selesai)->year;

            // Semester Ganjil (Juli - Desember)
            $tahunAjaran->semesters()->create([
                'jenis' => 'Ganjil',
                'tanggal_mulai' => "{$startYear}-07-01",
                'tanggal_selesai' => "{$startYear}-12-31",
                'status_aktif' => $tahunAjaran->status ? true : false, // Make Ganjil active if TA is active
            ]);

            // Semester Genap (Januari - Juni)
            $tahunAjaran->semesters()->create([
                'jenis' => 'Genap',
                'tanggal_mulai' => "{$endYear}-01-01",
                'tanggal_selesai' => "{$endYear}-06-30",
                'status_aktif' => false, // Default inactive
            ]);

            return $tahunAjaran;
        });
    }

    /**
     * Update tahun ajaran with cascade status update to semesters
     *
     * @param int $id
     * @param array $data
     * @return TahunAjaran
     * @throws ValidationException
     */
    public function update(int $id, array $data): TahunAjaran
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        // Validate date range
        if (isset($data['tanggal_mulai']) && isset($data['tanggal_selesai'])) {
            if ($data['tanggal_mulai'] >= $data['tanggal_selesai']) {
                throw ValidationException::withMessages([
                    'tanggal_selesai' => 'Tanggal selesai harus setelah tanggal mulai'
                ]);
            }
        }

        return DB::transaction(function () use ($tahunAjaran, $data, $id) {
            // If activating this tahun ajaran, deactivate all others
            if (isset($data['status']) && $data['status']) {
                TahunAjaran::where('id', '!=', $id)->where('status', true)->update(['status' => false]);
                
                // Deactivate all semesters globally to prevent mismatch
                \App\Models\Semester::query()->update(['status_aktif' => false]);
                
                // Automatically activate the Ganjil semester (or first available) for the newly activated year
                $semesterToActivate = $tahunAjaran->semesters()->where('jenis', 'Ganjil')->first() 
                                   ?? $tahunAjaran->semesters()->first();
                                   
                if ($semesterToActivate) {
                    $semesterToActivate->update(['status_aktif' => true]);
                }
            }

            // If deactivating, deactivate all related semesters
            if (isset($data['status']) && !$data['status']) {
                $tahunAjaran->semesters()->update(['status_aktif' => false]);
            }

            $tahunAjaran->update($data);
            return $tahunAjaran->fresh();
        });
    }

    /**
     * Delete tahun ajaran with transaction validation check
     * Checks for related transactions before deletion
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id): bool
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        // Check for dependencies using the trait
        $this->checkDependencies($tahunAjaran, [
            'kelompoks' => 'kelompok',
            // Future modules will add: 'sesis', 'absensis', 'setorans'
        ], 'Tahun Ajaran');

        // If no dependencies, cascade delete semesters and delete tahun ajaran
        return DB::transaction(function () use ($tahunAjaran) {
            // Cascade delete semesters
            $tahunAjaran->semesters()->delete();

            // Delete tahun ajaran
            return $tahunAjaran->delete();
        });
    }

    /**
     * Create tahun ajaran from a single year integer.
     * Auto-generates label, tanggal_mulai (Jul 1), tanggal_selesai (Jun 30 next year).
     *
     * @param  int   $year    Starting year, e.g. 2025 → TA 2025/2026
     * @param  bool  $aktif   Whether to immediately activate this TA
     * @return TahunAjaran
     * @throws ValidationException
     */
    public function createFromYear(int $year, bool $aktif = true): TahunAjaran
    {
        $label = $year . '/' . ($year + 1);

        // Cek apakah label sudah ada
        if (TahunAjaran::where('label', $label)->exists()) {
            throw ValidationException::withMessages([
                'tahun_mulai' => "Tahun Ajaran {$label} sudah ada.",
            ]);
        }

        return $this->create([
            'label'          => $label,
            'tanggal_mulai'  => $year . '-07-01',
            'tanggal_selesai'=> ($year + 1) . '-06-30',
            'status'         => $aktif,
        ]);
    }

    /**
     * Get active tahun ajaran
     *
     * @return TahunAjaran|null
     */
    public function getActive(): ?TahunAjaran
    {
        return TahunAjaran::where('status', true)->first();
    }

    /**
     * Get all tahun ajaran with semester count
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithSemesterCount()
    {
        return TahunAjaran::withCount('semesters')->orderBy('tanggal_mulai', 'desc')->get();
    }
}
