<?php

namespace App\Services\MasterData;

use App\Models\Siswa;
use App\Traits\ValidatesForeignKeys;
use App\Traits\ChecksCascadeDelete;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SiswaService
{
    use ValidatesForeignKeys, ChecksCascadeDelete;

    /**
     * Register new student
     *
     * @param array $siswaData
     * @return Siswa
     * @throws ValidationException
     */
    public function register(array $siswaData): Siswa
    {
        // Validate NIS uniqueness
        if (Siswa::where('nis', $siswaData['nis'])->exists()) {
            throw ValidationException::withMessages([
                'nis' => 'NIS sudah terdaftar dalam sistem'
            ]);
        }

        // Validate NISN uniqueness (if provided)
        if (isset($siswaData['nisn']) && !empty($siswaData['nisn'])) {
            if (Siswa::where('nisn', $siswaData['nisn'])->exists()) {
                throw ValidationException::withMessages([
                    'nisn' => 'NISN sudah terdaftar dalam sistem'
                ]);
            }
        }

        // Validate Nama + Tanggal Lahir uniqueness to prevent duplicate student
        if (Siswa::where('nama', $siswaData['nama'])
                 ->where('tanggal_lahir', $siswaData['tanggal_lahir'])
                 ->exists()) {
            throw ValidationException::withMessages([
                'nama' => 'Siswa dengan nama dan tanggal lahir ini sudah terdaftar dalam sistem'
            ]);
        }

        // Set default statuses
        $siswaData['status_akademik'] = $siswaData['status_akademik'] ?? 'Aktif';
        $siswaData['status_tahfidz'] = $siswaData['status_tahfidz'] ?? 'Sedang Berjalan';
        $siswaData['status_penempatan'] = $siswaData['status_penempatan'] ?? 'Belum Ditempatkan';
        $siswaData['status_aktif'] = $siswaData['status_aktif'] ?? true;

        // ✅ Set tahun_ajaran_id based on tanggal_masuk
        if (!isset($siswaData['tahun_ajaran_id']) && isset($siswaData['tanggal_masuk'])) {
            $tanggalMasuk = \Carbon\Carbon::parse($siswaData['tanggal_masuk']);

            // Logic: Juli-Desember = Semester 1, Januari-Juni = Semester 2
            // Tahun ajaran format: "YYYY/YYYY+1" (e.g., "2026/2027")
            $tahunAjaranLabel = $tanggalMasuk->month >= 7
                ? $tanggalMasuk->year . '/' . ($tanggalMasuk->year + 1)
                : ($tanggalMasuk->year - 1) . '/' . $tanggalMasuk->year;

            // Find tahun ajaran by label
            $tahunAjaran = \App\Models\TahunAjaran::where('label', $tahunAjaranLabel)->first();

            // If not found, fallback to active tahun ajaran
            if (!$tahunAjaran) {
                $tahunAjaran = \App\Models\TahunAjaran::where('status', true)->first();
            }

            $siswaData['tahun_ajaran_id'] = $tahunAjaran?->id;
        }

        $isFeatureEnabled = \App\Models\FeatureToggle::isEnabled('tes_penempatan');

        // Ensure default value and respect feature toggle
        if (!$isFeatureEnabled) {
            $siswaData['punya_hafalan_sebelumnya'] = false;
        } else {
            $siswaData['punya_hafalan_sebelumnya'] = filter_var($siswaData['punya_hafalan_sebelumnya'] ?? false, FILTER_VALIDATE_BOOLEAN);
        }

        return DB::transaction(function () use ($siswaData) {
            // Create siswa
            $siswa = Siswa::create($siswaData);

            // Jika siswa tidak punya hafalan sebelumnya, otomatis catat baseline 0 dan siap masuk tingkat dasar
            if (!$siswa->punya_hafalan_sebelumnya) {
                // Auto-set baseline to 0 for active tahun ajaran
                $activeTahunAjaran = \App\Models\TahunAjaran::where('status', true)->first();

                if ($activeTahunAjaran) {
                    app(\App\Services\BaselineManager::class)->storeBaseline(
                        $siswa->id,
                        [
                            'tahun_ajaran_id' => $activeTahunAjaran->id,
                            'juzs_covered' => [],
                            'surahs_covered' => 'Belum ada hafalan (baseline 0 juz)'
                        ]
                    );
                }
            }

            return $siswa->fresh();
        });
    }

    /**
     * Update student data
     *
     * @param int $id
     * @param array $siswaData
     * @return Siswa
     * @throws ValidationException
     */
    public function update(int $id, array $siswaData): Siswa
    {
        $siswa = Siswa::findOrFail($id);

        // Validate NIS uniqueness (excluding current record)
        if (isset($siswaData['nis']) && $siswaData['nis'] !== $siswa->nis) {
            if (Siswa::where('nis', $siswaData['nis'])->where('id', '!=', $id)->exists()) {
                throw ValidationException::withMessages([
                    'nis' => 'NIS sudah terdaftar dalam sistem'
                ]);
            }
        }

        // Validate NISN uniqueness (excluding current record)
        if (isset($siswaData['nisn']) && !empty($siswaData['nisn']) && $siswaData['nisn'] !== $siswa->nisn) {
            if (Siswa::where('nisn', $siswaData['nisn'])->where('id', '!=', $id)->exists()) {
                throw ValidationException::withMessages([
                    'nisn' => 'NISN sudah terdaftar dalam sistem'
                ]);
            }
        }

        // Validate Nama + Tanggal Lahir uniqueness (excluding current record)
        if (isset($siswaData['nama']) && isset($siswaData['tanggal_lahir'])) {
            $isDuplicate = Siswa::where('nama', $siswaData['nama'])
                ->where('tanggal_lahir', $siswaData['tanggal_lahir'])
                ->where('id', '!=', $id)
                ->exists();

            if ($isDuplicate) {
                throw ValidationException::withMessages([
                    'nama' => 'Siswa dengan nama dan tanggal lahir ini sudah terdaftar dalam sistem'
                ]);
            }
        }

        return DB::transaction(function () use ($siswa, $siswaData) {
            // Update siswa
            $siswa->update($siswaData);

            return $siswa->fresh();
        });
    }

    /**
     * Update student status and handle soft delete
     *
     * @param int $id
     * @param string $newStatus
     * @return Siswa
     * @throws \Exception
     */
    public function updateStatus(int $id, string $newStatus): Siswa
    {
        $siswa = Siswa::findOrFail($id);

        // If status is Lulus SMP/Pindah Sekolah/Keluar, perform soft delete
        if (in_array($newStatus, ['Lulus SMP', 'Pindah Sekolah', 'Keluar'])) {
            $siswa->status_akademik = $newStatus;
            $siswa->status_aktif = false;
            $siswa->save();
            $siswa->delete(); // Soft delete
        } else {
            $siswa->status_akademik = $newStatus;
            $siswa->status_aktif = true;
            $siswa->save();
        }

        return $siswa->fresh();
    }

    /**
     * Delete student with transaction check
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function delete(int $id): bool
    {
        $siswa = Siswa::withTrashed()->findOrFail($id);

        // Check for dependencies using the trait
        // Note: kelompok relationship check handled separately due to nullable FK
        if ($siswa->kelompok_id) {
            throw new \RuntimeException(
                'Tidak dapat menghapus siswa karena masih terdaftar dalam kelompok. Keluarkan siswa dari kelompok terlebih dahulu.'
            );
        }

        // Check for transaction dependencies (will be implemented in future modules)
        // Future: 'absensis', 'setorans', 'progres'

        return DB::transaction(function () use ($siswa) {
            // Delete foto file if exists
            if ($siswa->foto && Storage::exists($siswa->foto)) {
                Storage::delete($siswa->foto);
            }

            // Permanent delete
            return $siswa->forceDelete();
        });
    }

    /**
     * Restore soft-deleted student
     *
     * @param int $id
     * @return Siswa
     */
    public function restore(int $id): Siswa
    {
        $siswa = Siswa::onlyTrashed()->findOrFail($id);
        $siswa->restore();
        $siswa->status_aktif = true;
        $siswa->status_akademik = 'Aktif';
        $siswa->save();

        return $siswa->fresh();
    }

    /**
     * Get all students with filters
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getAllWithFilters(array $filters = [])
    {
        $query = Siswa::with(['kelompok']);

        if (isset($filters['status_akademik'])) {
            $query->where('status_akademik', $filters['status_akademik']);
        }

        if (isset($filters['status_tahfidz'])) {
            $query->where('status_tahfidz', $filters['status_tahfidz']);
        }

        if (isset($filters['status_penempatan'])) {
            $query->where('status_penempatan', $filters['status_penempatan']);
        }

        if (isset($filters['jenis_kelamin'])) {
            $query->where('jenis_kelamin', $filters['jenis_kelamin']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        if (isset($filters['kelompok_id'])) {
            $query->where('kelompok_id', $filters['kelompok_id']);
        }

        return $query;
    }

    /**
     * Assign student to kelompok with validation
     *
     * @param int $siswaId
     * @param int $kelompokId
     * @return Siswa
     * @throws ValidationException
     */
    public function assignToKelompok(int $siswaId, int $kelompokId): Siswa
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
     * Remove student from kelompok
     *
     * @param int $siswaId
     * @return Siswa
     * @throws ValidationException
     */
    public function removeFromKelompok(int $siswaId): Siswa
    {
        $siswa = $this->validateSiswa($siswaId, 'siswa_id', false);
        $siswa->kelompok_id = null;
        $siswa->status_penempatan = 'Belum Ditempatkan';

        return $siswa->fresh();
    }

    /**
     * Get students by kelompok
     *
     * @param int $kelompokId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByKelompok(int $kelompokId)
    {
        return Siswa::where('kelompok_id', $kelompokId)
            ->where('status_penempatan', 'Ditempatkan')
            ->get();
    }

    /**
     * Process bulk penempatan siswa ke kelompok
     */
    public function bulkPenempatan(array $siswaIds, int $kelompokId, string $tanggalPenempatan, int $adminId): void
    {
        DB::transaction(function () use ($siswaIds, $kelompokId, $tanggalPenempatan, $adminId) {
            $kelompok = \App\Models\Kelompok::withCount('siswas')->findOrFail($kelompokId);
            $jumlahSiswaDipilih = count($siswaIds);
            $sisaKapasitas = $kelompok->kapasitas_maks - $kelompok->siswas_count;

            if ($sisaKapasitas < $jumlahSiswaDipilih) {
                throw new \Exception("Kapasitas kelompok tidak cukup. Tersisa {$sisaKapasitas} slot, Anda pilih {$jumlahSiswaDipilih} siswa.");
            }

            $siswas = Siswa::whereIn('id', $siswaIds)->get();

            // Validasi: Cek siswa yang sudah ditempatkan di kelompok manapun (termasuk kelompok yang sama)
            $sudahDitempatkan = $siswas->whereNotNull('kelompok_id');
            if ($sudahDitempatkan->count() > 0) {
                // Cek apakah ada yang sudah di kelompok yang sama
                $sudahDiKelompokIni = $sudahDitempatkan->where('kelompok_id', $kelompokId);
                if ($sudahDiKelompokIni->count() > 0) {
                    $names = $sudahDiKelompokIni->pluck('nama')->take(3)->implode(', ');
                    throw new \Exception("Beberapa siswa sudah ada di kelompok ini ({$names}...). Tidak perlu ditempatkan ulang.");
                }

                // Cek siswa yang ada di kelompok lain
                $names = $sudahDitempatkan->pluck('nama')->take(3)->implode(', ');
                throw new \Exception("Beberapa siswa sudah ditempatkan di kelompok lain ({$names}...). Batalkan penempatan lama dulu atau pilih siswa yang belum ditempatkan.");
            }

            $jenisKelompok = $kelompok->jenis;
            $expectedGender = $jenisKelompok === 'Banin' ? 'Laki-laki' : 'Perempuan';

            $wrongGender = $siswas->where('jenis_kelamin', '!=', $expectedGender);
            if ($wrongGender->count() > 0) {
                $names = $wrongGender->pluck('nama')->take(3)->implode(', ');
                throw new \Exception("Beberapa siswa tidak sesuai jenis kelompok ({$names}...). Kelompok {$jenisKelompok} hanya untuk siswa {$expectedGender}.");
            }

            Siswa::whereIn('id', $siswaIds)->update([
                'kelompok_id' => $kelompokId,
                'status_penempatan' => 'Ditempatkan',
                'tanggal_penempatan' => $tanggalPenempatan,
            ]);

            foreach ($siswas as $siswa) {
                \App\Models\RiwayatKelompok::create([
                    'siswa_id' => $siswa->id,
                    'kelompok_dari_id' => null,
                    'kelompok_ke_id' => $kelompokId,
                    'tanggal' => $tanggalPenempatan,
                    'alasan' => 'Penempatan Bulk',
                    'disetujui_oleh' => $adminId,
                ]);
            }
        });
    }

    /**
     * Get target tingkat recommendations for selected siswas
     */
    public function getSiswaTingkatRecommendations(array $siswaIds): array
    {
        $tingkatSiswa = [];
        $siswasData = Siswa::with([
            'usulanKenaikanTingkats' => function($q) {
                $q->latest('created_at');
            },
            'riwayatKelompoks' => function($q) {
                $q->whereNotNull('kelompok_dari_id')->latest('created_at')->with('kelompokDari');
            }
        ])->whereIn('id', $siswaIds)->get();

        foreach ($siswasData as $siswa) {
            $usulanTerakhir = $siswa->usulanKenaikanTingkats->first();
            $riwayatTerakhir = $siswa->riwayatKelompoks->first();

            $targetTingkat = 'Dasar';
            $statusLabel = 'Siswa Baru (belum pernah ditempatkan)';

            if ($usulanTerakhir && in_array($usulanTerakhir->status_usulan->value ?? $usulanTerakhir->status_usulan, ['Diajukan', 'Disetujui'])) {
                $targetTingkat = $usulanTerakhir->tingkat_tujuan->value ?? $usulanTerakhir->tingkat_tujuan;
                $asal = $usulanTerakhir->tingkat_asal->value ?? $usulanTerakhir->tingkat_asal ?? 'Sebelumnya';
                $statusUsulan = $usulanTerakhir->status_usulan->value ?? $usulanTerakhir->status_usulan;
                $statusLabel = "Lulusan Tingkat {$asal} (" . ($statusUsulan === 'Diajukan' ? 'Menunggu Persetujuan' : 'Disetujui') . ")";
            } elseif ($riwayatTerakhir && $riwayatTerakhir->kelompokDari) {
                $tingkatAsal = $riwayatTerakhir->kelompokDari->tingkat->value ?? $riwayatTerakhir->kelompokDari->tingkat;
                $targetTingkat = $tingkatAsal;
                $statusLabel = "Eks-Kelompok Tingkat {$tingkatAsal}";
            }

            $tingkatSiswa[$siswa->id] = [
                'target_tingkat' => $targetTingkat,
                'status_label' => $statusLabel
            ];
        }

        return $tingkatSiswa;
    }

    /**
     * Proses registrasi siswa keluar
     */
    public function registrasiKeluar(int $siswaId, string $status, string $tanggal, ?string $alasan): void
    {
        DB::transaction(function () use ($siswaId, $status, $tanggal, $alasan) {
            $siswa = Siswa::findOrFail($siswaId);
            $kelompokLama = $siswa->kelompok_id;

            $siswa->update([
                'status_akademik' => $status,
                'status_aktif' => false,
                'tanggal_keluar' => $tanggal,
                'kelompok_id' => null,
                'status_penempatan' => 'Belum Ditempatkan',
            ]);

            \App\Models\RiwayatSiswa::create([
                'siswa_id' => $siswa->id,
                'tanggal' => $tanggal,
                'status' => $status,
                'alasan' => $alasan,
            ]);

            if ($kelompokLama) {
                \App\Models\RiwayatKelompok::create([
                    'siswa_id' => $siswa->id,
                    'kelompok_dari_id' => $kelompokLama,
                    'kelompok_ke_id' => null,
                    'tanggal' => now(),
                    'alasan' => 'Siswa ' . $status,
                ]);
            }
        });
    }
}
