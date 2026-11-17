<?php

namespace App\Services\TesPenempatan;

use App\Models\TesPenempatan;
use App\Models\HasilTesPenempatan;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelompok;
use App\Enums\StatusTesPenempatan;
use App\Services\BaselineManager;
use App\Services\NotifikasiService;
use App\Services\TesPenempatan\ReferensiQuranValidator;
use App\Services\TesPenempatan\BaselineCalculator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Carbon\Carbon;
use Exception;

class TesPenempatanService
{
    public function __construct(
        protected ReferensiQuranValidator $quranValidator,
        protected BaselineCalculator $baselineCalculator,
        protected BaselineManager $baselineManager,
        protected NotifikasiService $notifikasiService
    ) {}

    /**
     * Schedule a new placement test.
     */
    public function scheduleTest(int $siswaId, int $guruPengujiId, string $tanggalTes, ?string $waktuTes = null): TesPenempatan
    {
        $siswa = Siswa::findOrFail($siswaId);


        if ($siswa->status_penempatan !== 'Belum Ditempatkan') {
            throw new InvalidArgumentException("Siswa harus dalam status 'Belum Ditempatkan' untuk dijadwalkan tes.");
        }

        $guru = Guru::findOrFail($guruPengujiId);
        if (!$guru->status_aktif) {
            throw new InvalidArgumentException("Guru penguji harus dalam status aktif.");
        }

        $tanggal = Carbon::parse($tanggalTes);
        if ($tanggal->isPast() && !$tanggal->isToday()) {
            throw new InvalidArgumentException("Tanggal tes tidak boleh di masa lalu.");
        }

        $tes = TesPenempatan::create([
            'siswa_id' => $siswaId,
            'guru_penguji_id' => $guruPengujiId,
            'tanggal_tes' => $tanggalTes,
            'waktu_tes' => $waktuTes,
            'status' => StatusTesPenempatan::TERJADWAL,
        ]);

        $this->notifikasiService->notifyGuruOnAppointed($tes);

        return $tes;
    }

    /**
     * Submit test results (Guru action).
     */
    public function submitTestResults(int $tesPenempatanId, array $hasilData, string $catatanKualitas, string $rekomendasiTingkat): TesPenempatan
    {
        $tes = TesPenempatan::findOrFail($tesPenempatanId);

        if (!$tes->isEditable()) {
            throw new InvalidArgumentException("Tes ini sudah tidak dapat diubah (Status: {$tes->status->value}).");
        }

        if (empty($hasilData)) {
            throw new InvalidArgumentException("Harus ada minimal satu data hasil hafalan.");
        }

        $catatanLength = strlen(trim($catatanKualitas));
        if ($catatanLength < 10 || $catatanLength > 1000) {
            throw new InvalidArgumentException("Catatan kualitas harus antara 10 hingga 1000 karakter.");
        }

        $enumRekomendasi = \App\Enums\RekomendasiTingkat::tryFrom($rekomendasiTingkat);
        if (!$enumRekomendasi) {
            throw new InvalidArgumentException("Rekomendasi tingkat tidak valid.");
        }

        DB::transaction(function () use ($tes, $hasilData, $catatanKualitas, $enumRekomendasi) {
            foreach ($hasilData as $hasil) {
                if (!$this->quranValidator->validateJuzSurahRelation($hasil['juz_id'], $hasil['surah_id'])) {
                    throw new InvalidArgumentException("Surah ID {$hasil['surah_id']} tidak berada dalam Juz ID {$hasil['juz_id']}.");
                }

                $this->quranValidator->validateAyatRange($hasil['surah_id'], $hasil['ayat_mulai'], $hasil['ayat_selesai']);

                HasilTesPenempatan::create([
                    'tes_penempatan_id' => $tes->id,
                    'juz_id' => $hasil['juz_id'],
                    'surah_id' => $hasil['surah_id'],
                    'ayat_mulai' => $hasil['ayat_mulai'],
                    'ayat_selesai' => $hasil['ayat_selesai'],
                ]);
            }

            $tes->update([
                'catatan_kualitas' => $catatanKualitas,
                'rekomendasi_tingkat' => $enumRekomendasi,
            ]);

            $tes->markAsCompleted();
        });

        $this->notifikasiService->notifyOnTestResultsSubmitted($tes);

        return $tes->fresh();
    }

    /**
     * Get available groups for student based on gender and capacity.
     */
    public function getAvailableKelompok(int $siswaId, string $tingkat)
    {
        $siswa = Siswa::findOrFail($siswaId);
        $jenisKelompok = $siswa->jenis_kelamin === 'L' ? 'Banin' : 'Banat';

        $query = Kelompok::where('tingkat', $tingkat)
            ->withCount('siswas')
            ->get()
            ->filter(function ($kelompok) {
                return $kelompok->siswas_count < $kelompok->kapasitas_maks;
            });

        // Filter by jenis if there are results, otherwise return all with that tingkat
        $filtered = $query->filter(fn($k) => $k->jenis === $jenisKelompok)->values();

        return $filtered->isNotEmpty() ? $filtered : $query->values();
    }

    /**
     * Place student based on test results.
     */
    public function placeStudent(int $tesPenempatanId, ?int $kelompokId, int $adminId, ?string $tanggalPenempatan = null): TesPenempatan
    {
        $tes = TesPenempatan::findOrFail($tesPenempatanId);
        $siswa = $tes->siswa;

        if ($tes->status !== StatusTesPenempatan::SELESAI) {
            throw new InvalidArgumentException("Hanya tes yang sudah selesai yang dapat diproses penempatannya.");
        }

        DB::transaction(function () use ($tes, $siswa, $kelompokId, $adminId, $tanggalPenempatan) {
            // Calculate baseline
            $baselineData = $this->baselineCalculator->calculateFromTestResults($tes->id);

            // REFACTOR 2026-08-11: Hapus tahunAjaranId dependency
            // Baseline sekarang lifetime (tidak per tahun ajaran)

            // Store baseline with proper data structure
            $this->baselineManager->storeBaseline(
                $siswa->id,
                [
                    'juzs_covered' => $baselineData['complete_juzs'],
                    'surahs_covered' => 'Dari hasil tes penempatan: ' . count($baselineData['complete_juzs']) . ' juz lengkap',
                    'catatan_kualitas' => $tes->catatan_kualitas,
                    'rekomendasi_tingkat' => $tes->rekomendasi_tingkat?->value,
                ]
            );

            if ($kelompokId) {
                $kelompok = Kelompok::findOrFail($kelompokId);
                $currentMembers = $kelompok->siswas()->count();

                if ($currentMembers >= $kelompok->kapasitas_maks) {
                    throw new Exception("Kelompok sudah penuh.");
                }

                $siswa->update([
                    'kelompok_id' => $kelompokId,
                    'status_penempatan' => 'Ditempatkan',
                    'perlu_tes_penempatan' => false,
                ]);

                $tes->update([
                    'final_kelompok_id' => $kelompokId,
                    'ditempatkan_at' => $tanggalPenempatan ? Carbon::parse($tanggalPenempatan) : now(),
                    'penempatan_by' => $adminId,
                ]);
            } else {
                $siswa->update([
                    'status_penempatan' => 'Belum Ditempatkan',
                    'perlu_tes_penempatan' => false,
                ]);
            }
        });

        $this->notifikasiService->notifyStudentOnPlaced($tes);

        return $tes->fresh();
    }

    /**
     * Cancel a scheduled test.
     */
    public function cancelTest(int $tesPenempatanId, int $adminId, string $alasanPembatalan): TesPenempatan
    {
        $tes = TesPenempatan::findOrFail($tesPenempatanId);

        if (!$tes->canBeCancelled()) {
            throw new InvalidArgumentException("Tes ini sudah tidak dapat dibatalkan.");
        }

        if (strlen(trim($alasanPembatalan)) < 20) {
            throw new InvalidArgumentException("Alasan pembatalan minimal 20 karakter.");
        }

        $tes->cancel($adminId, $alasanPembatalan);

        $this->notifikasiService->notifyOnTestCancelled($tes);

        return $tes;
    }

    /**
     * Update test results (Admin override).
     */
    public function updateTestResults(int $tesPenempatanId, array $hasilData, string $catatanKualitas, string $rekomendasiTingkat, string $alasanPerubahan, int $adminId): TesPenempatan
    {
        $tes = TesPenempatan::findOrFail($tesPenempatanId);

        if ($tes->status !== StatusTesPenempatan::SELESAI) {
            throw new InvalidArgumentException("Hanya tes yang sudah selesai yang dapat diubah hasilnya.");
        }

        if (empty(trim($alasanPerubahan))) {
            throw new InvalidArgumentException("Alasan perubahan wajib diisi.");
        }

        $enumRekomendasi = \App\Enums\RekomendasiTingkat::tryFrom($rekomendasiTingkat);

        DB::transaction(function () use ($tes, $hasilData, $catatanKualitas, $enumRekomendasi, $alasanPerubahan, $adminId) {
            // Delete old results
            $tes->hasilTes()->delete();

            // Insert new results
            foreach ($hasilData as $hasil) {
                if (!$this->quranValidator->validateJuzSurahRelation($hasil['juz_id'], $hasil['surah_id'])) {
                    throw new InvalidArgumentException("Surah ID {$hasil['surah_id']} tidak berada dalam Juz ID {$hasil['juz_id']}.");
                }

                $this->quranValidator->validateAyatRange($hasil['surah_id'], $hasil['ayat_mulai'], $hasil['ayat_selesai']);

                HasilTesPenempatan::create([
                    'tes_penempatan_id' => $tes->id,
                    'juz_id' => $hasil['juz_id'],
                    'surah_id' => $hasil['surah_id'],
                    'ayat_mulai' => $hasil['ayat_mulai'],
                    'ayat_selesai' => $hasil['ayat_selesai'],
                ]);
            }

            $tes->update([
                'catatan_kualitas' => $catatanKualitas,
                'rekomendasi_tingkat' => $enumRekomendasi,
            ]);

            // Log audit change
            if (function_exists('activity')) {
                activity()
                    ->performedOn($tes)
                    ->causedBy($adminId)
                    ->withProperties(['alasan_perubahan' => $alasanPerubahan])
                    ->log('Admin mengubah hasil tes penempatan.');
            }
        });

        return $tes->fresh();
    }
}
