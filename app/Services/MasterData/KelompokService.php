<?php

namespace App\Services\MasterData;

use App\Models\Kelompok;
use App\Models\Guru;
use App\Models\TahunAjaran;
use App\Models\Siswa;
use App\Models\RiwayatGuruPembimbingKelompok;
use App\Traits\ValidatesForeignKeys;
use App\Traits\ChecksCascadeDelete;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KelompokService
{
    use ValidatesForeignKeys, ChecksCascadeDelete;

    /**
     * Create kelompok with validation
     *
     * @param array $data
     * @return Kelompok
     * @throws ValidationException
     */
    public function create(array $data): Kelompok
    {
        // Validate foreign key references with meaningful messages
        $this->validateGuruJabatan(
            $data['guru_pembimbing_id'],
            'Guru Pembimbing Tahfidz',
            'guru_pembimbing_id'
        );

        // Validate name uniqueness (global, not per tahun ajaran)
        $exists = Kelompok::where('nama', $data['nama'])->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'nama' => 'Nama kelompok sudah ada'
            ]);
        }

        // Auto-assign to active tahun ajaran if not provided
        if (!isset($data['tahun_ajaran_id'])) {
            $activeTahunAjaran = TahunAjaran::where('status', true)->first();

            if (!$activeTahunAjaran) {
                throw ValidationException::withMessages([
                    'tahun_ajaran_id' => 'Tidak ada tahun ajaran aktif. Harap aktifkan tahun ajaran terlebih dahulu.'
                ]);
            }

            $data['tahun_ajaran_id'] = $activeTahunAjaran->id;
        }

        // Capacity fields are no longer required from form
        // Remove them if they exist in data to avoid validation errors
        unset($data['kapasitas_min']);
        unset($data['kapasitas_maks']);

        return Kelompok::create($data);
    }

    /**
     * Update kelompok and record guru pembimbing history if changed
     *
     * @param int $id
     * @param array $data
     * @param string|null $alasanPerubahanGuru
     * @return Kelompok
     * @throws ValidationException
     */
    public function update(int $id, array $data, ?string $alasanPerubahanGuru = null): Kelompok
    {
        $kelompok = Kelompok::findOrFail($id);

        // Validate name uniqueness (global, excluding current record)
        if (isset($data['nama'])) {
            $exists = Kelompok::where('nama', $data['nama'])
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'nama' => 'Nama kelompok sudah ada'
                ]);
            }
        }

        // Validate guru is Pembimbing Tahfidz if guru is being changed
        if (isset($data['guru_pembimbing_id'])) {
            $this->validateGuruJabatan(
                $data['guru_pembimbing_id'],
                'Guru Pembimbing Tahfidz',
                'guru_pembimbing_id'
            );
        }

        // Capacity fields are no longer required from form
        // Remove them if they exist in data to avoid validation errors
        unset($data['kapasitas_min']);
        unset($data['kapasitas_maks']);

        return DB::transaction(function () use ($kelompok, $data, $alasanPerubahanGuru) {
            // If guru_pembimbing_id changed, record in riwayat
            if (isset($data['guru_pembimbing_id']) && $data['guru_pembimbing_id'] != $kelompok->guru_pembimbing_id) {
                RiwayatGuruPembimbingKelompok::create([
                    'kelompok_id' => $kelompok->id,
                    'guru_dari_id' => $kelompok->guru_pembimbing_id,
                    'guru_ke_id' => $data['guru_pembimbing_id'],
                    'tanggal_efektif' => now()->toDateString(),
                    'alasan' => $alasanPerubahanGuru ?? 'Pergantian guru pembimbing'
                ]);
            }

            $kelompok->update($data);
            return $kelompok->fresh();
        });
    }

    /**
     * Delete kelompok with transaction check
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id): bool
    {
        $kelompok = Kelompok::findOrFail($id);

        // Check for dependencies using the trait
        $this->checkDependencies($kelompok, [
            'siswas' => 'siswa yang ditempatkan',
            // Future modules: 'sesis', 'absensis', 'setorans'
        ], 'Kelompok');

        return DB::transaction(function () use ($kelompok) {
            // Delete riwayat guru pembimbing
            RiwayatGuruPembimbingKelompok::where('kelompok_id', $kelompok->id)->delete();

            // Delete kelompok
            return $kelompok->delete();
        });
    }

    /**
     * Get member count (students with status_penempatan = Ditempatkan)
     *
     * @param int $kelompokId
     * @return int
     */
    public function getMemberCount(int $kelompokId): int
    {
        return Siswa::where('kelompok_id', $kelompokId)
            ->where('status_penempatan', 'Ditempatkan')
            ->count();
    }

    /**
     * Get kelompok capacity status
     *
     * @param int $kelompokId
     * @return string 'Tersedia' or 'Penuh' or 'N/A' if no capacity set
     */
    public function getCapacityStatus(int $kelompokId): string
    {
        $kelompok = Kelompok::findOrFail($kelompokId);

        // If no capacity limit set, always return 'Tersedia'
        if (is_null($kelompok->kapasitas_maks)) {
            return 'Tersedia';
        }

        $memberCount = $this->getMemberCount($kelompokId);

        return $memberCount >= $kelompok->kapasitas_maks ? 'Penuh' : 'Tersedia';
    }

    /**
     * Assign siswa to kelompok
     *
     * @param int $siswaId
     * @param int $kelompokId
     * @return Siswa
     * @throws \Exception
     */
    public function assignSiswa(int $siswaId, int $kelompokId): Siswa
    {
        // Validate foreign key references
        $siswa = $this->validateSiswa($siswaId, 'siswa_id', true);
        $kelompok = $this->validateKelompok($kelompokId, 'kelompok_id', true);

        $siswa->kelompok_id = $kelompokId;
        $siswa->status_penempatan = 'Ditempatkan';
        $siswa->save();

        return $siswa->fresh();
    }

    /**
     * Remove siswa from kelompok
     *
     * @param int $siswaId
     * @return Siswa
     */
    public function removeSiswa(int $siswaId): Siswa
    {
        $siswa = $this->validateSiswa($siswaId, 'siswa_id', false);
        $siswa->kelompok_id = null;
        $siswa->status_penempatan = 'Belum Ditempatkan';
        $siswa->save();

        return $siswa->fresh();
    }

    /**
     * Get all kelompok with filters
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getAllWithFilters(array $filters = [])
    {
        $query = Kelompok::with(['guruPembimbing', 'tahunAjaran']);

        if (isset($filters['tingkat'])) {
            $query->where('tingkat', $filters['tingkat']);
        }

        if (isset($filters['jenis'])) {
            $query->where('jenis', $filters['jenis']);
        }

        if (isset($filters['tahun_ajaran_id'])) {
            $query->where('tahun_ajaran_id', $filters['tahun_ajaran_id']);
        }

        if (isset($filters['guru_pembimbing_id'])) {
            $query->where('guru_pembimbing_id', $filters['guru_pembimbing_id']);
        }

        return $query;
    }

    /**
     * Get kelompok by guru pembimbing
     *
     * @param int $guruId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByGuruPembimbing(int $guruId)
    {
        return Kelompok::where('guru_pembimbing_id', $guruId)
            ->with(['tahunAjaran', 'siswas'])
            ->get();
    }

    /**
     * Get riwayat guru pembimbing for kelompok
     *
     * @param int $kelompokId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRiwayatGuruPembimbing(int $kelompokId)
    {
        return RiwayatGuruPembimbingKelompok::where('kelompok_id', $kelompokId)
            ->with(['guruDari', 'guruKe'])
            ->orderBy('tanggal_efektif', 'desc')
            ->get();
    }
}
