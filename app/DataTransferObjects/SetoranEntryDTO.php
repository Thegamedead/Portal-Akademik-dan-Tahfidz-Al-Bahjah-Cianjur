<?php

namespace App\DataTransferObjects;

use App\Enums\{JenisSetoran, SkorKomponen, Keputusan};

/**
 * Data Transfer Object for Setoran Entry
 *
 * Provides type-safe data transfer between layers
 */
readonly class SetoranEntryDTO
{
    public function __construct(
        public JenisSetoran $jenisSetoran,
        public int $juzId,
        public int $surahId,
        public int $ayatMulai,
        public int $ayatSelesai,
        public SkorKomponen $skorTajwid,
        public SkorKomponen $skorKelancaran,
        public SkorKomponen $skorMakhraj,
        public SkorKomponen $skorFashahah,
        public SkorKomponen $predikat,
        public Keputusan $keputusan,
        public ?string $catatan = null,
        public ?int $saranAyatMulai = null,
        public ?int $saranAyatSelesai = null,
        public ?int $saranSurahId = null,
        public ?int $saranJuzId = null,
    ) {}

    /**
     * Create DTO from array data
     *
     * @param array<string, mixed> $data Raw array data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            jenisSetoran: JenisSetoran::from($data['jenis_setoran']),
            juzId: (int) $data['juz_id'],
            surahId: (int) $data['surah_id'],
            ayatMulai: (int) $data['ayat_mulai'],
            ayatSelesai: (int) $data['ayat_selesai'],
            skorTajwid: SkorKomponen::from($data['skor_tajwid']),
            skorKelancaran: SkorKomponen::from($data['skor_kelancaran']),
            skorMakhraj: SkorKomponen::from($data['skor_makhraj']),
            skorFashahah: SkorKomponen::from($data['skor_fashahah']),
            predikat: SkorKomponen::from($data['predikat']),
            keputusan: Keputusan::from($data['keputusan']),
            catatan: $data['catatan'] ?? null,
            saranAyatMulai: isset($data['saran_ayat_mulai']) ? (int) $data['saran_ayat_mulai'] : null,
            saranAyatSelesai: isset($data['saran_ayat_selesai']) ? (int) $data['saran_ayat_selesai'] : null,
            saranSurahId: isset($data['saran_surah_id']) ? (int) $data['saran_surah_id'] : null,
            saranJuzId: isset($data['saran_juz_id']) ? (int) $data['saran_juz_id'] : null,
        );
    }

    /**
     * Convert DTO to array for database storage
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'jenis_setoran' => $this->jenisSetoran->value,
            'juz_id' => $this->juzId,
            'surah_id' => $this->surahId,
            'ayat_mulai' => $this->ayatMulai,
            'ayat_selesai' => $this->ayatSelesai,
            'skor_tajwid' => $this->skorTajwid->value,
            'skor_kelancaran' => $this->skorKelancaran->value,
            'skor_makhraj' => $this->skorMakhraj->value,
            'skor_fashahah' => $this->skorFashahah->value,
            'predikat' => $this->predikat->value,
            'keputusan' => $this->keputusan->value,
            'catatan' => $this->catatan,
            'saran_ayat_mulai' => $this->saranAyatMulai,
            'saran_ayat_selesai' => $this->saranAyatSelesai,
            'saran_surah_id' => $this->saranSurahId,
            'saran_juz_id' => $this->saranJuzId,
        ];
    }

    /**
     * Validate that ayat range is within Juz boundaries
     *
     * @return bool
     */
    public function isValidAyatRange(): bool
    {
        return $this->ayatMulai > 0
            && $this->ayatSelesai >= $this->ayatMulai;
    }
}
