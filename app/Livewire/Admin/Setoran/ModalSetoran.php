<?php

namespace App\Livewire\Admin\Setoran;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use App\Models\{Kelompok, Absensi, Juz, Surah, Setoran};
use App\Services\SetoranService;
use App\Enums\{SkorKomponen, JenisSetoran};


class ModalSetoran extends Component
{
    public bool $isOpen = false;
    public ?int $siswaId = null;
    public ?int $kelompokId = null;
    public ?string $tanggal = null;
    public ?string $namaSiswa = null;
    public ?string $nisSiswa = null;
    public ?string $namaKelompok = null;
    public bool $isEditMode = false; // New: track if editing existing absensi

    public array $entries = [];
    public bool $isMengulang = false; // true jika sesi ini adalah sesi mengulang

    // Global Assessment State
    public string $skor_tajwid = 'Lancar';
    public string $skor_kelancaran = 'Lancar';
    public string $skor_makhraj = 'Lancar';
    public string $skor_fashahah = 'Lancar';
    public string $predikat = 'Lancar';
    public string $keputusan = 'Lanjut';
    public string $catatan = '';

    public function mount()
    {
        // Properties will be hydrated via computed methods
    }

    #[On('openSetoranModal')]
    public function openSetoranModal(int $siswaId, int $kelompokId, string $date)
    {
        $this->siswaId = $siswaId;
        $this->kelompokId = $kelompokId;
        $this->tanggal = $date;

        $siswa = \App\Models\Siswa::find($siswaId);
        $kelompok = Kelompok::find($kelompokId);

        $this->namaSiswa = $siswa?->nama;
        $this->nisSiswa = $siswa?->nis;
        $this->namaKelompok = $kelompok?->nama;

        // Check if editing existing absensi
        $existingAbsensi = Absensi::where('siswa_id', $siswaId)
            ->where('kelompok_id', $kelompokId)
            ->whereDate('tanggal', $date)
            ->first();

        $this->isEditMode = !is_null($existingAbsensi);

        // Force refresh computed properties
        unset($this->completedSurahIds);
        unset($this->surahToRepeatIds);
        unset($this->allSurahs);

        $this->loadStudentData();
        $this->isOpen = true;
    }

    #[Computed]
    public function allSurahs()
    {
        return \App\Services\ReferensiCacheService::getSurahs();
    }

    /**
     * Surah IDs yang sudah pernah tuntas (keputusan Lanjut, baik dari Hafalan Baru maupun Ulang)
     * KECUALI surah yang lulus di hari yang sama (untuk mode edit)
     */
    #[Computed]
    public function completedSurahIds(): array
    {
        if (!$this->siswaId) return [];

        $query = Setoran::join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
            ->where('absensis.siswa_id', $this->siswaId)
            ->where('setorans.keputusan', 'Lanjut'); // Lanjut dari jenis apapun (Hafalan Baru atau Ulang)

        // Jika mode edit, exclude setoran di tanggal yang sama
        if ($this->isEditMode && $this->tanggal) {
            $query->whereDate('absensis.tanggal', '!=', $this->tanggal);
        }

        return $query->pluck('setorans.surah_id')
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Surah IDs yang perlu diulang (keputusan terakhir adalah "Ulang")
     */
    #[Computed]
    public function surahToRepeatIds(): array
    {
        if (!$this->siswaId) return [];

        // Get all setorans for this student, ordered by date desc
        $setorans = Setoran::join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
            ->where('absensis.siswa_id', $this->siswaId)
            ->select('setorans.*', 'absensis.tanggal')
            ->orderBy('absensis.tanggal', 'desc')
            ->orderBy('setorans.created_at', 'desc')
            ->get();

        // Jika mode edit, exclude setoran di tanggal yang sama
        if ($this->isEditMode && $this->tanggal) {
            $setorans = $setorans->filter(fn($s) =>
                \Carbon\Carbon::parse($s->tanggal)->format('Y-m-d') !== $this->tanggal
            );
        }

        // Group by surah_id, ambil yang terakhir (latest)
        $latestBySurah = $setorans->groupBy('surah_id')->map(fn($group) => $group->first());

        // Filter hanya yang keputusan "Ulang"
        return $latestBySurah
            ->filter(fn($setoran) => $setoran->keputusan?->value === 'Ulang')
            ->pluck('surah_id')
            ->values()
            ->toArray();
    }

    #[Computed]
    public function juzs()
    {
        if (!$this->kelompokId) return collect();

        $allJuzs = \App\Services\ReferensiCacheService::getJuzs();
        $config = \App\Models\ParameterKonfigurasi::first();

        // Fallback to all juz if config not set
        if (!$config || empty($config->batas_tingkat)) {
            return $allJuzs;
        }

        $kelompok = Kelompok::find($this->kelompokId);
        if (!$kelompok) return $allJuzs;

        $tingkat = $kelompok->tingkat;
        $tingkatValue = $tingkat instanceof \BackedEnum ? $tingkat->value : (string) $tingkat;

        $batasTingkat = $config->batas_tingkat[$tingkatValue] ?? [];

        $juzTargets = [];
        if (is_array($batasTingkat) && isset($batasTingkat['juz_mulai'])) {
            // Old format: ['juz_mulai' => X, 'juz_selesai' => Y]
            $juzMulai = (int) ($batasTingkat['juz_mulai'] ?? 1);
            $juzSelesai = (int) ($batasTingkat['juz_selesai'] ?? 30);
            $juzTargets = range($juzMulai, $juzSelesai);
        } elseif (is_array($batasTingkat) && !empty($batasTingkat)) {
            // New format: array of juz numbers (e.g. [30, 29, 28])
            $juzTargets = array_map('intval', array_values($batasTingkat));
        } else {
            // Fallback to all 30 juz if this specific tingkat has no target
            return $allJuzs;
        }

        // Filter by juz_ke (nomor juz), reset indexes for clean Livewire hydration
        return $allJuzs->filter(fn($juz) => in_array((int) $juz->juz_ke, $juzTargets))->values();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->siswaId = null;
        $this->kelompokId = null;
        $this->entries = [];
        $this->isEditMode = false;
        $this->resetAssessment();
    }

    protected function resetAssessment()
    {
        $this->skor_tajwid = 'Lancar';
        $this->skor_kelancaran = 'Lancar';
        $this->skor_makhraj = 'Lancar';
        $this->skor_fashahah = 'Lancar';
        $this->predikat = 'Lancar';
        $this->keputusan = 'Lanjut';
        $this->catatan = '';
        $this->isMengulang = false;
    }

    public function loadStudentData()
    {
        if (!$this->siswaId) return;

        // If edit mode, load existing setoran data
        if ($this->isEditMode && $this->tanggal) {
            $this->loadExistingSetoranData();
            return;
        }

        // Otherwise, load saran for new setoran
        $setoranService = app(SetoranService::class);

        // Pass kelompokId to get suggestion only from current kelompok
        $saran = $setoranService->getLastSaranAyat($this->siswaId, $this->kelompokId);

        $defaultEntry = $this->getEmptyEntry();

        if ($saran) {
            $defaultEntry['juz_id']   = $saran['saran_juz_id'];
            $defaultEntry['surah_id'] = $saran['saran_surah_id'];
            $defaultEntry['ayat_mulai'] = $saran['saran_ayat_mulai'];

            // Always use jumlah_ayat of the surah as ayat_selesai (not the stored saran value)
            if (!empty($saran['saran_surah_id'])) {
                $surah = Surah::find((int) $saran['saran_surah_id']);
                $defaultEntry['ayat_selesai'] = $surah ? $surah->jumlah_ayat : ($saran['saran_ayat_selesai'] ?? '');
            } else {
                $defaultEntry['ayat_selesai'] = $saran['saran_ayat_selesai'] ?? '';
            }

            // Tandai sesi ini sebagai sesi mengulang jika saran berasal dari keputusan Ulang
            $this->isMengulang = $saran['is_mengulang'] ?? false;
        } else {
            $this->isMengulang = false;
        }

        $this->entries = [$defaultEntry];
    }

    protected function loadExistingSetoranData()
    {
        $absensi = Absensi::where('siswa_id', $this->siswaId)
            ->where('kelompok_id', $this->kelompokId)
            ->whereDate('tanggal', $this->tanggal)
            ->first();

        if (!$absensi) {
            // No existing absensi, load saran instead
            $this->isEditMode = false;
            $this->loadStudentData();
            return;
        }

        $setorans = Setoran::where('absensi_id', $absensi->id)
            ->with(['juz', 'surah'])
            ->orderBy('created_at', 'asc')
            ->get();

        if ($setorans->isEmpty()) {
            // No setoran yet, show empty entry
            $this->entries = [$this->getEmptyEntry()];
            $this->isMengulang = false;
            return;
        }

        // Load existing setoran data into entries
        $this->entries = $setorans->map(function($setoran) {
            return [
                'id' => $setoran->id, // Keep ID for tracking
                'jenis_setoran' => $setoran->jenis_setoran?->value ?? 'Hafalan Baru',
                'juz_id' => $setoran->juz_id,
                'surah_id' => $setoran->surah_id,
                'ayat_mulai' => $setoran->ayat_mulai,
                'ayat_selesai' => $setoran->ayat_selesai,
            ];
        })->toArray();

        // Load assessment values from the last setoran
        $lastSetoran = $setorans->last();
        if ($lastSetoran) {
            $this->skor_tajwid = $lastSetoran->skor_tajwid?->value ?? 'Lancar';
            $this->skor_kelancaran = $lastSetoran->skor_kelancaran?->value ?? 'Lancar';
            $this->skor_makhraj = $lastSetoran->skor_makhraj?->value ?? 'Lancar';
            $this->skor_fashahah = $lastSetoran->skor_fashahah?->value ?? 'Lancar';
            $this->predikat = $lastSetoran->predikat?->value ?? 'Lancar';
            $this->keputusan = $lastSetoran->keputusan?->value ?? 'Lanjut';
            $this->catatan = $lastSetoran->catatan ?? '';
        }

        // Check if any setoran is "Ulang" type
        $this->isMengulang = $setorans->contains(fn($s) => $s->jenis_setoran?->value === 'Ulang');
    }

    protected function getEmptyEntry()
    {
        // Default jenis setoran based on session type
        $defaultJenis = $this->isMengulang
            ? JenisSetoran::Ulang->value
            : JenisSetoran::HafalanBaru->value;

        return [
            'jenis_setoran' => $defaultJenis,
            'juz_id' => '',
            'surah_id' => '',
            'ayat_mulai' => '',
            'ayat_selesai' => '',
        ];
    }

    public function addEntry()
    {
        $this->entries[] = $this->getEmptyEntry();
    }

    public function removeEntry($index)
    {
        if (count($this->entries) > 1) {
            unset($this->entries[$index]);
            $this->entries = array_values($this->entries);
        }
    }

    public function updated($property, $value)
    {
        if (in_array($property, ['skor_tajwid', 'skor_kelancaran', 'skor_makhraj', 'skor_fashahah'])) {
            $this->updatePredikat();
        }

        // Reset surah & ayat ketika juz berubah
        if (preg_match('/entries\.(\d+)\.juz_id/', $property, $matches)) {
            $idx = (int) $matches[1];
            $this->entries[$idx]['surah_id']     = '';
            $this->entries[$idx]['ayat_mulai']   = '';
            $this->entries[$idx]['ayat_selesai'] = '';
        }

        // Auto-populate ayat mulai & selesai ketika surah dipilih/diubah
        if (preg_match('/entries\.(\d+)\.surah_id/', $property, $matches)) {
            $idx = (int) $matches[1];

            if (!empty($value)) {
                // Query langsung agar tidak tergantung computed property cache
                $surah = Surah::find((int) $value);

                if ($surah && $surah->jumlah_ayat > 0) {
                    $this->entries[$idx]['ayat_mulai']   = 1;
                    $this->entries[$idx]['ayat_selesai'] = $surah->jumlah_ayat;
                }
            } else {
                $this->entries[$idx]['ayat_mulai']   = '';
                $this->entries[$idx]['ayat_selesai'] = '';
            }
        }
    }

    public function updatePredikat()
    {
        try {
            $skor = [
                SkorKomponen::from($this->skor_tajwid)->value(),
                SkorKomponen::from($this->skor_kelancaran)->value(),
                SkorKomponen::from($this->skor_makhraj)->value(),
                SkorKomponen::from($this->skor_fashahah)->value(),
            ];

            $minSkor = min($skor);
            $this->predikat = SkorKomponen::fromMinValue($minSkor)->value;
        } catch (\Exception $e) {}
    }

    #[Computed]
    public function totalAyatKumulatif()
    {
        $total = 0;
        foreach ($this->entries as $entry) {
            $mulai = (int)($entry['ayat_mulai'] ?? 0);
            $selesai = (int)($entry['ayat_selesai'] ?? 0);

            if ($mulai > 0 && $selesai >= $mulai) {
                $total += ($selesai - $mulai + 1);
            }
        }
        return $total;
    }

    /**
     * Get allowed ayat range hints for each entry
     * Returns array indexed by entry index, each containing min, max, and hint
     */
    public function getAyatRangeHints(): array
    {
        $hints = [];
        $setoranService = app(SetoranService::class);

        foreach ($this->entries as $index => $entry) {
            if (!empty($entry['juz_id']) && !empty($entry['surah_id'])) {
                $hints[$index] = $setoranService->getAllowedAyatRange(
                    (int) $entry['juz_id'],
                    (int) $entry['surah_id']
                );
            } else {
                $hints[$index] = null;
            }
        }

        return $hints;
    }

    public function submit()
    {
        $rules = [
            'entries.*.jenis_setoran' => 'required',
            'entries.*.juz_id' => 'required',
            'entries.*.surah_id' => 'required',
            'skor_tajwid' => 'required',
            'skor_kelancaran' => 'required',
            'skor_makhraj' => 'required',
            'skor_fashahah' => 'required',
            'keputusan' => 'required',
        ];

        $allSurahs = $this->allSurahs();
        foreach ($this->entries as $index => $entry) {
            $maxAyat = null;
            if (!empty($entry['surah_id'])) {
                $surah = $allSurahs->firstWhere('id', (int)$entry['surah_id']);
                if ($surah) {
                    $maxAyat = $surah->jumlah_ayat;
                }
            }

            if ($maxAyat) {
                $rules["entries.{$index}.ayat_mulai"] = "required|numeric|min:1|max:{$maxAyat}";
                $rules["entries.{$index}.ayat_selesai"] = "required|numeric|min:1|max:{$maxAyat}";
            } else {
                $rules["entries.{$index}.ayat_mulai"] = "required|numeric|min:1";
                $rules["entries.{$index}.ayat_selesai"] = "required|numeric|min:1";
            }
        }

        $this->validate($rules, [
            'entries.*.ayat_mulai.max' => 'Maksimal ayat: :max.',
            'entries.*.ayat_selesai.max' => 'Maksimal ayat: :max.',
        ]);

        $setoranService = app(SetoranService::class);

        foreach ($this->entries as $index => $entry) {
            // Validasi range ayat berdasarkan batas juz yang sebenarnya
            $validation = $setoranService->validateAyatRangeInJuz(
                $entry['juz_id'],
                $entry['surah_id'],
                $entry['ayat_mulai'],
                $entry['ayat_selesai']
            );

            if (!$validation['valid']) {
                $this->addError("entries.{$index}.ayat_selesai", $validation['message']);
                return;
            }

            if (!$setoranService->validateSurahInJuz($entry['surah_id'], $entry['juz_id'])) {
                $this->addError("entries.{$index}.surah_id", 'Surah tidak terdapat di Juz ini.');
                return;
            }
        }

        // Fetch surah names for display purposes in InputAbsensi UI
        $allSurahs = $this->allSurahs();
        $entriesToSave = array_map(function($entry) use ($allSurahs) {
            $surah = $allSurahs->firstWhere('id', (int)$entry['surah_id']);
            $juz = \App\Models\Juz::find($entry['juz_id']);

            return array_merge($entry, [
                'surah_awal'      => $surah ? $surah->nama_latin : '-',
                'surah_akhir'     => $surah ? $surah->nama_latin : '-', // currently single surah support
                'juz'             => $juz ? $juz->juz_ke : '-',
                'skor_tajwid'     => $this->skor_tajwid,
                'skor_kelancaran' => $this->skor_kelancaran,
                'skor_makhraj'    => $this->skor_makhraj,
                'skor_fashahah'   => $this->skor_fashahah,
                'predikat'        => $this->predikat,
                'keputusan'       => $this->keputusan,
                'catatan'         => $this->keputusan === 'Ulang' ? $this->catatan : null,
            ]);
        }, $this->entries);

        try {
            // Instead of saving to DB, we dispatch it back to InputAbsensi
            $this->dispatch('setoranAdded', siswaId: $this->siswaId, entries: $entriesToSave);
            $this->closeModal();
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: 'Gagal memproses setoran: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $surahsOptions = [];
        $allSurahs = $this->allSurahs();

        foreach ($this->entries as $index => $entry) {
            if (!empty($entry['juz_id'])) {
                // juz_id in entries stores the primary key (id) of the juz record.
                // The juz_surah pivot uses juz_id as foreign key to juzs.id.
                // So we filter surahs where their juzs relationship contains the matching juz id.
                $juzId = (int)$entry['juz_id'];
                $surahsOptions[$index] = $allSurahs->filter(function($surah) use ($juzId) {
                    return $surah->juzs->contains('id', $juzId);
                })->values();
            } else {
                $surahsOptions[$index] = collect();
            }
        }

        // Build maxAyat map per entry based on selected surah
        $maxAyatMap = [];
        foreach ($this->entries as $index => $entry) {
            if (!empty($entry['surah_id'])) {
                $surah = $allSurahs->firstWhere('id', (int)$entry['surah_id']);
                $maxAyatMap[$index] = $surah?->jumlah_ayat ?? null;
            }
        }

        // Get ayat range hints for UI
        $ayatRangeHints = $this->getAyatRangeHints();

        return view('livewire.admin.setoran.modal-setoran', [
            'surahsOptions'      => $surahsOptions,
            'juzs'               => $this->juzs(),
            'maxAyatMap'         => $maxAyatMap,
            'completedSurahIds'  => $this->completedSurahIds,
            'surahToRepeatIds'   => $this->surahToRepeatIds,
            'ayatRangeHints'     => $ayatRangeHints,
        ]);
    }
}
