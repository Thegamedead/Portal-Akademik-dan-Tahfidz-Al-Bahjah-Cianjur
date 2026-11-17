<?php

namespace App\Livewire\Admin\Setoran;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\{Kelompok, Absensi, Juz, Surah, Setoran};
use App\Services\SetoranService;
use App\Enums\{SkorKomponen, Keputusan, JenisSetoran};
use Livewire\Attributes\Computed;
use Carbon\Carbon;
use Illuminate\Support\Collection;

#[Layout('admin.layouts.app')]
#[Title('Input Setoran')]
class InputSetoran extends Component
{
    public int $kelompokId;
    public string $tanggal;
    public ?int $currentSiswaId = null;

    public Collection $siswaHadir;

    // Array of entries for the current student
    public array $entries = [];

    // Store drafts for other students when navigating
    public array $drafts = [];

    public bool $showConfirmDialog = false;
    public bool $isOutsideEditWindow = false;

    public function mount(int $kelompok, ?string $tanggal = null, ?int $siswaId = null)
    {
        $this->kelompokId = $kelompok;
        $this->tanggal = $tanggal ?? now()->format('Y-m-d');
        $this->currentSiswaId = $siswaId;

        // NO AUTHORIZATION CHECK for Admin - can access any kelompok

        // Check if outside edit window (for warning display only, not blocker)
        $tanggalAbsensi = Carbon::parse($this->tanggal);
        $isToday = $tanggalAbsensi->isToday();
        $isBeforeMidnight = now()->format('H:i:s') <= '23:59:59';
        $this->isOutsideEditWindow = !($isToday && $isBeforeMidnight);

        // Load absensi to get siswaHadir
        $absensis = Absensi::with('siswa')
            ->where('kelompok_id', $this->kelompokId)
            ->whereDate('tanggal', $this->tanggal)
            ->where('status_kehadiran', 'Hadir')
            ->get();

        if ($absensis->isEmpty()) {
            $this->dispatch('toast', variant: 'error', message: 'Tidak ada siswa yang hadir pada tanggal ini.');
            return redirect()->route('admin.absensi.index');
        }

        $this->siswaHadir = $absensis->map(function($absensi) {
            return [
                'siswa_id' => $absensi->siswa_id,
                'nama' => $absensi->siswa->nama,
                'nis' => $absensi->siswa->nis,
                'absensi_id' => $absensi->id,
                'is_completed' => Setoran::where('absensi_id', $absensi->id)->exists()
            ];
        });

        if (!$this->currentSiswaId) {
            $this->currentSiswaId = $this->siswaHadir->first()['siswa_id'];
        }

        $this->loadStudentData();
    }

    public function loadStudentData()
    {
        if (isset($this->drafts[$this->currentSiswaId])) {
            $this->entries = $this->drafts[$this->currentSiswaId];
        } else {
            $setoranService = app(SetoranService::class);

            // Pass kelompokId to get suggestion only from current kelompok
            $saran = $setoranService->getLastSaranAyat($this->currentSiswaId, $this->kelompokId);

            $defaultEntry = $this->getEmptyEntry();

            if ($saran) {
                $defaultEntry['juz_id'] = $saran['saran_juz_id'];
                $defaultEntry['surah_id'] = $saran['saran_surah_id'];
                $defaultEntry['ayat_mulai'] = $saran['saran_ayat_mulai'];
                $defaultEntry['ayat_selesai'] = $saran['saran_ayat_selesai'] ?? '';
            }

            $this->entries = [$defaultEntry];
        }
    }

    protected function getEmptyEntry()
    {
        return [
            'jenis_setoran' => JenisSetoran::HafalanBaru->value,
            'juz_id' => '',
            'surah_id' => '',
            'ayat_mulai' => '',
            'ayat_selesai' => '',
            'skor_tajwid' => SkorKomponen::Lancar->value,
            'skor_kelancaran' => SkorKomponen::Lancar->value,
            'skor_makhraj' => SkorKomponen::Lancar->value,
            'skor_fashahah' => SkorKomponen::Lancar->value,
            'predikat' => SkorKomponen::Lancar->value,
            'keputusan' => Keputusan::Lanjut->value,
            'catatan' => '',
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
            $this->entries = array_values($this->entries); // re-index
        }
    }

    public function updated($property, $value)
    {
        if (str_starts_with($property, 'entries.')) {
            $parts = explode('.', $property);
            if (count($parts) == 3 && str_starts_with($parts[2], 'skor_')) {
                $this->updatePredikat($parts[1]);
            }
        }
    }

    public function updatePredikat($index)
    {
        $entry = $this->entries[$index];

        try {
            $skor = [
                SkorKomponen::from($entry['skor_tajwid'])->value(),
                SkorKomponen::from($entry['skor_kelancaran'])->value(),
                SkorKomponen::from($entry['skor_makhraj'])->value(),
                SkorKomponen::from($entry['skor_fashahah'])->value(),
            ];

            $minSkor = min($skor);
            $this->entries[$index]['predikat'] = SkorKomponen::fromMinValue($minSkor)->value;
        } catch (\Exception $e) {
            // Do nothing if enum from fails temporarily
        }
    }

    public function navigateToSiswa($siswaId)
    {
        // Save current entries to draft
        $this->drafts[$this->currentSiswaId] = $this->entries;

        $this->currentSiswaId = $siswaId;
        $this->loadStudentData();
    }

    #[Computed]
    public function allSurahs()
    {
        return \App\Services\ReferensiCacheService::getSurahs();
    }

    #[Computed]
    public function juzs()
    {
        return \App\Services\ReferensiCacheService::getJuzs();
    }

    public function submit()
    {
        $this->validate([
            'entries.*.jenis_setoran' => 'required',
            'entries.*.juz_id' => 'required',
            'entries.*.surah_id' => 'required',
            'entries.*.ayat_mulai' => 'required|numeric|min:1',
            'entries.*.ayat_selesai' => 'required|numeric|min:1',
            'entries.*.skor_tajwid' => 'required',
            'entries.*.skor_kelancaran' => 'required',
            'entries.*.skor_makhraj' => 'required',
            'entries.*.skor_fashahah' => 'required',
            'entries.*.keputusan' => 'required',
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

        $this->showConfirmDialog = true;
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

    public function cancelSave()
    {
        $this->showConfirmDialog = false;
    }

    public function save()
    {
        $setoranService = app(SetoranService::class);
        $siswaData = collect($this->siswaHadir)->firstWhere('siswa_id', $this->currentSiswaId);

        if (!$siswaData) {
            $this->dispatch('toast', variant: 'error', message: 'Siswa tidak valid.');
            return;
        }

        try {
            // Admin can save anytime - no edit window restriction
            $setoranService->saveMultipleSetorans($siswaData['absensi_id'], $this->entries);

            // Mark completed in local state
            $key = collect($this->siswaHadir)->search(fn($s) => $s['siswa_id'] == $this->currentSiswaId);
            $newSiswaHadir = $this->siswaHadir->toArray();
            $newSiswaHadir[$key]['is_completed'] = true;
            $this->siswaHadir = collect($newSiswaHadir);

            // Clear drafts for this student
            unset($this->drafts[$this->currentSiswaId]);

            $this->showConfirmDialog = false;

            // Find next student who hasn't completed
            $nextSiswa = $this->siswaHadir->firstWhere('is_completed', false);

            if ($nextSiswa) {
                $this->dispatch('toast', variant: 'sukses', message: 'Setoran berhasil disimpan. Lanjut ke siswa berikutnya.');
                $this->navigateToSiswa($nextSiswa['siswa_id']);
            } else {
                $this->dispatch('toast', variant: 'sukses', message: 'Semua setoran siswa hadir telah selesai diisi.');
                $this->redirect(route('admin.absensi.index'));
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan setoran: ' . $e->getMessage());
            $this->showConfirmDialog = false;
        }
    }

    public function render()
    {
        $surahsOptions = [];
        $allSurahs = $this->allSurahs;
        foreach ($this->entries as $index => $entry) {
            if (!empty($entry['juz_id'])) {
                $surahsOptions[$index] = $allSurahs->filter(function($surah) use ($entry) {
                    return collect($surah->juzs)->contains('id', (int)$entry['juz_id']);
                });
            } else {
                $surahsOptions[$index] = collect();
            }
        }

        // Get ayat range hints for UI
        $ayatRangeHints = $this->getAyatRangeHints();

        return view('livewire.admin.setoran.input-setoran', [
            'surahsOptions' => $surahsOptions,
            'juzs' => $this->juzs,
            'kelompok' => Kelompok::with('guruPembimbing')->findOrFail($this->kelompokId),
            'ayatRangeHints' => $ayatRangeHints,
        ]);
    }
}
