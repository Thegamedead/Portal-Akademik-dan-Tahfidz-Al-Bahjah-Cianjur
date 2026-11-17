<?php

namespace App\Livewire\Admin\Absensi;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\{Kelompok, Absensi, Setoran};
use App\Services\AbsensiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InputAbsensi extends Component
{
    public bool $isOpen = false;
    protected $listeners = ['openInputAbsensi', 'setoranSaved' => 'loadAbsensi'];

    // Props
    public ?int $kelompokId = null;
    public string $tanggal = '';
    public string $waktuMulai = '';
    public string $waktuSelesai = '';
    public bool $isFlexibleDate = false; // New: allow date picker when true

    // State
    public ?Kelompok $kelompok = null;
    public array $absensis = [];
    public array $absensisIdMap = [];
    public bool $isOutsideEditWindow = false;
    public bool $absensiSudahDisimpan = false;
    public bool $justSaved = false; // Flag untuk tracking apakah baru saja save
    public bool $isMinggu = false;
    public bool $isLiburPilihan = false;
    public array $setoranSiswa = [];
    public array $filledDates = []; // New: track which dates have absensi
    public array $liburDates = []; // New: track which dates are marked as libur

    public ?int $targetSiswaId = null;

    public function mount(): void
    {
        // Hydrated in openInputAbsensi
    }

    public function openInputAbsensi(int $kelompokId, ?string $tanggal = null, ?int $siswaId = null): void
    {
        $this->kelompokId = $kelompokId;
        $this->tanggal = $tanggal ?? today()->format('Y-m-d');
        $this->targetSiswaId = $siswaId;
        $this->isFlexibleDate = is_null($tanggal); // If no date provided, enable flexible mode

        $this->kelompok = Kelompok::findOrFail($this->kelompokId);

        $this->setoranSiswa = [];
        $this->justSaved = false; // Reset flag setiap kali modal dibuka
        $this->isMinggu = Carbon::parse($this->tanggal)->isSunday();
        $this->isOutsideEditWindow = !$this->isMinggu && (!Carbon::parse($this->tanggal)->isToday() || now()->endOfDay()->isPast());

        // Load filled dates and libur dates for the current month
        if ($this->isFlexibleDate) {
            $this->loadDateInfo();
        }

        if (!$this->isMinggu) {
            $this->loadAbsensi();
        } else {
            $this->absensis = [];
            $this->setoranSiswa = [];
            $this->absensiSudahDisimpan = false;
            $this->isLiburPilihan = false;
        }

        $this->isOpen = true;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->kelompok = null;
        $this->justSaved = false; // Reset flag saat modal ditutup
        $this->absensiSudahDisimpan = false;
    }

    public function updatedTanggal()
    {
        $this->justSaved = false; // Reset flag saat tanggal berubah
        $this->isMinggu = Carbon::parse($this->tanggal)->isSunday();
        $this->isOutsideEditWindow = !$this->isMinggu && (!Carbon::parse($this->tanggal)->isToday() || now()->endOfDay()->isPast());

        // Reload date info when month changes
        if ($this->isFlexibleDate) {
            $this->loadDateInfo();
        }

        if (!$this->isMinggu) {
            $this->loadAbsensi();
        } else {
            $this->absensis = [];
            $this->setoranSiswa = [];
            $this->absensiSudahDisimpan = false;
            $this->isLiburPilihan = false;
        }
    }

    /**
     * Load filled dates and libur dates for the calendar
     */
    protected function loadDateInfo(): void
    {
        $currentMonth = Carbon::parse($this->tanggal)->startOfMonth();
        $endOfMonth = Carbon::parse($this->tanggal)->endOfMonth();

        // Get all absensi for this kelompok in current month
        $absensis = Absensi::where('kelompok_id', $this->kelompokId)
            ->whereBetween('tanggal', [$currentMonth, $endOfMonth])
            ->select('tanggal', DB::raw('GROUP_CONCAT(DISTINCT status_kehadiran) as statuses'))
            ->groupBy('tanggal')
            ->get();

        $this->filledDates = [];
        $this->liburDates = [];

        foreach ($absensis as $absensi) {
            $dateStr = Carbon::parse($absensi->tanggal)->format('Y-m-d');
            $this->filledDates[] = $dateStr;

            // Check if all statuses are "Libur"
            $statuses = explode(',', $absensi->statuses);
            if (count($statuses) === 1 && $statuses[0] === 'Libur') {
                $this->liburDates[] = $dateStr;
            }
        }
    }

    #[On('setoranSaved')]
    public function refreshSetoranData()
    {
        $this->loadAbsensi();
    }

    /**
     * Load existing absensi and setoran data
     */
    protected function loadAbsensi(): void
    {
        // Load absensi
        $existingAbsensi = Absensi::where('kelompok_id', $this->kelompokId)
            ->whereDate('tanggal', $this->tanggal)
            ->get()
            ->keyBy('siswa_id');

        if ($existingAbsensi->isNotEmpty()) {
            $first = $existingAbsensi->first();
            $this->waktuMulai = $first->waktu_mulai ? $first->waktu_mulai->format('H:i') : '';
            $this->waktuSelesai = $first->waktu_selesai ? $first->waktu_selesai->format('H:i') : '';

            // Check if all are Libur
            $this->isLiburPilihan = $existingAbsensi->every(fn($a) => $a->status_kehadiran->value === 'Libur');
        } else {
            $this->waktuMulai = now()->format('H:i');
            $this->waktuSelesai = now()->addHours(2)->format('H:i');
            $this->isLiburPilihan = false;
        }

        $this->absensiSudahDisimpan = $existingAbsensi->isNotEmpty() &&
            $existingAbsensi->count() === $this->activeSiswas->count();

        // Load setoran for the same date - convert to array to avoid serialization issues
        $absensiIds = $existingAbsensi->pluck('id')->toArray();
        if (!empty($absensiIds)) {
            $setorans = Setoran::with(['absensi', 'surah', 'juz'])
                ->whereIn('absensi_id', $absensiIds)
                ->orderBy('created_at', 'desc')
                ->get();

            // Group by siswa_id and convert to array
            $this->setoranSiswa = [];
            foreach ($setorans as $setoran) {
                $siswaId = $setoran->absensi->siswa_id;
                if (!isset($this->setoranSiswa[$siswaId])) {
                    $this->setoranSiswa[$siswaId] = [];
                }
                $this->setoranSiswa[$siswaId][] = [
                    'id' => $setoran->id,
                    'jenis_setoran' => $setoran->jenis_setoran?->value ?? '-',
                    'juz' => $setoran->juz->juz_ke ?? '-',
                    'surah_awal' => $setoran->surah->nama_latin ?? '-',
                    'ayat_awal' => $setoran->ayat_mulai,
                    'surah_akhir' => $setoran->surah->nama_latin ?? '-',
                    'ayat_akhir' => $setoran->ayat_selesai,
                    'nilai_kelancaran' => $setoran->skor_kelancaran?->value ?? '-',
                    'predikat' => $setoran->predikat?->value ?? '-',
                    'keputusan' => $setoran->keputusan?->value ?? '-',
                ];
            }
        } else {
            $this->setoranSiswa = [];
        }

        // Initialize absensis array for all siswa
        foreach ($this->activeSiswas as $siswa) {
            if (isset($existingAbsensi[$siswa->id])) {
                $this->absensis[$siswa->id] = $existingAbsensi[$siswa->id]->status_kehadiran->value;
                $this->absensisIdMap[$siswa->id] = $existingAbsensi[$siswa->id]->id;
            } else {
                $this->absensis[$siswa->id] = ''; // Empty = not yet selected
                $this->absensisIdMap[$siswa->id] = null;
            }
        }
    }

    public function hadirSemua(): void
    {
        if ($this->isMinggu) {
            $this->dispatch('toast', variant: 'error', message: 'Hari Minggu libur, tidak dapat mengisi absensi.');
            return;
        }
        foreach ($this->activeSiswas as $siswa) {
            $this->absensis[$siswa->id] = 'Hadir';
        }
    }

    public function getCanEditProperty(): bool
    {
        return true;
    }

    public function updated($property, $value): void
    {
        // No auto-save anymore
    }

    public function toggleLibur(): void
    {
        $this->isLiburPilihan = !$this->isLiburPilihan;
        $this->updatedIsLiburPilihan($this->isLiburPilihan);
    }

    public function updatedIsLiburPilihan($value): void
    {
        if ($this->isMinggu) {
            $this->isLiburPilihan = false;
            return;
        }

        if ($value) {
            // Set all to Libur
            foreach ($this->activeSiswas as $siswa) {
                $this->absensis[$siswa->id] = 'Libur';
            }
        } else {
            // Reset to empty
            foreach ($this->activeSiswas as $siswa) {
                $this->absensis[$siswa->id] = '';
            }
            // Auto save empty is essentially deleting them if we were to handle it, but wait,
            // if we turn off libur, we can just delete the session so it starts fresh.
            if ($this->absensiSudahDisimpan) {
                $absensiIds = Absensi::where('kelompok_id', $this->kelompokId)
                    ->whereDate('tanggal', $this->tanggal)
                    ->pluck('id');
                Setoran::whereIn('absensi_id', $absensiIds)->delete();
                Absensi::whereIn('id', $absensiIds)->delete();
                $this->absensiSudahDisimpan = false;
                $this->dispatch('absensiSaved'); // Refresh matrix
            }
        }
    }

    public function updateStatus(int $siswaId, string $status): void
    {
        $this->absensis[$siswaId] = $status;

        // AUTO-MARK LIBUR UNTUK SEMUA SISWA
        // Jika status yang dipilih adalah "Libur", maka otomatis mark semua siswa lain juga sebagai "Libur"
        if ($status === 'Libur') {
            foreach ($this->activeSiswas as $siswa) {
                $this->absensis[$siswa->id] = 'Libur';
            }
            $this->isLiburPilihan = true; // Update toggle state

            $this->dispatch('toast', variant: 'info', message: 'Tanggal ini ditandai sebagai LIBUR untuk semua siswa di kelompok ini.');
        }
    }

    #[On('setoranAdded')]
    public function handleSetoranAdded(int $siswaId, array $entries): void
    {
        // Store in pending and update UI representation
        $this->setoranSiswa[$siswaId] = $entries; // Override existing or append
    }

    #[Computed]
    public function activeSiswas()
    {
        if (!$this->kelompok) return collect();

        $targetDate = \Carbon\Carbon::parse($this->tanggal)->startOfDay();

        $intervalService = app(\App\Services\SiswaKelompokIntervalService::class);
        $validSiswaIds = $intervalService->getValidSiswaForTanggal($this->kelompok, $this->tanggal);

        $validSiswas = \App\Models\Siswa::whereIn('id', $validSiswaIds)->get();

        if ($this->targetSiswaId) {
            $validSiswas = $validSiswas->where('id', $this->targetSiswaId);
        }

        return $validSiswas->sortBy('nama')->values();
    }

    /**
     * Check which students have completed all target hafalan
     * Returns array of siswa_id => boolean
     */
    #[Computed]
    public function siswaCompletedStatus(): array
    {
        $setoranService = app(\App\Services\SetoranService::class);
        $status = [];

        foreach ($this->activeSiswas as $siswa) {
            $status[$siswa->id] = $setoranService->hasCompletedAllTargetHafalan($siswa->id, $this->kelompokId);
        }

        return $status;
    }

    // Auto save absensi method removed as we now defer all saving to the final Simpan click

    public function save()
    {
        if ($this->isMinggu) {
            $this->dispatch('toast', variant: 'error', message: 'Hari Minggu libur, tidak dapat menyimpan absensi.');
            return;
        }

        $absensiService = app(AbsensiService::class);
        $totalSiswa = $this->activeSiswas->count();

        // Filter absensis hanya untuk siswa yang aktif di sesi ini
        $filteredAbsensis = collect($this->absensis)
            ->only($this->activeSiswas->pluck('id')->toArray())
            ->toArray();

        if (!$absensiService->validateAllStudentsMarked($filteredAbsensis, $totalSiswa)) {
            $this->dispatch('toast', variant: 'error', message: 'Semua siswa harus dipilih status kehadirannya.');
            return;
        }

        // Validasi: Siswa yang statusnya "Hadir" harus memiliki setoran
        $siswaHadirTanpaSetoran = [];
        foreach ($filteredAbsensis as $siswaId => $status) {
            if ($status === 'Hadir') {
                // Check if this siswa has setoran data
                if (!isset($this->setoranSiswa[$siswaId]) || empty($this->setoranSiswa[$siswaId])) {
                    // Find siswa name for error message
                    $siswa = $this->activeSiswas->firstWhere('id', $siswaId);
                    if ($siswa) {
                        $siswaHadirTanpaSetoran[] = $siswa->nama;
                    }
                }
            }
        }

        if (!empty($siswaHadirTanpaSetoran)) {
            $namaList = implode(', ', $siswaHadirTanpaSetoran);
            $this->dispatch('toast', variant: 'error', message: "Siswa yang hadir harus memiliki data setoran: {$namaList}");
            return;
        }

        $isAdmin = auth()->guard('admin')->check();
        $adminId = $isAdmin ? auth()->guard('admin')->id() : null;

        // Load kelompok if not loaded
        if (!$this->kelompok && $this->kelompokId) {
            $this->kelompok = Kelompok::find($this->kelompokId);
        }

        // Jika yang login guru, ambil ID-nya, jika admin ambil dari kelompok
        $guruPengisiId = auth()->guard('guru')->check()
            ? auth()->guard('guru')->id()
            : ($this->kelompok?->guru_pembimbing_id ?? 0);

        try {
            $absensiService->saveAbsensiKelompok(
                $this->kelompokId,
                Carbon::parse($this->tanggal),
                $filteredAbsensis,
                $guruPengisiId,
                $isAdmin,
                $adminId,
                $this->waktuMulai ?: null,
                $this->waktuSelesai ?: null
            );

            // After absensi is saved, we need to save the pending setorans.
            // We fetch the newly saved absensi records to get their IDs.
            $savedAbsensis = Absensi::where('kelompok_id', $this->kelompokId)
                ->whereDate('tanggal', $this->tanggal)
                ->get()
                ->keyBy('siswa_id');

            $setoranService = app(\App\Services\SetoranService::class);
            foreach ($this->setoranSiswa as $siswaId => $entries) {
                if (isset($savedAbsensis[$siswaId]) && $savedAbsensis[$siswaId]->status_kehadiran->value === 'Hadir') {
                    // Only save setoran if the student is marked as Hadir
                    // Format entries to match what saveMultipleSetorans expects
                    // The entries coming from ModalSetoran might have extra UI keys (like surah_awal), but saveMultipleSetorans expects the DB columns.
                    // ModalSetoran now will pass the raw DB format along with the UI format.

                    // First, clear existing setorans for this absensi to replace with the new ones from the UI
                    Setoran::where('absensi_id', $savedAbsensis[$siswaId]->id)->delete();

                    $dbEntries = [];
                    foreach ($entries as $entry) {
                        $dbEntries[] = [
                            'jenis_setoran' => $entry['jenis_setoran'],
                            'juz_id' => $entry['juz_id'] ?? \App\Models\Juz::where('juz_ke', $entry['juz'] ?? 0)->value('id'),
                            'surah_id' => $entry['surah_id'] ?? \App\Models\Surah::where('nama_latin', $entry['surah_awal'] ?? '')->value('id'),
                            'ayat_mulai' => $entry['ayat_mulai'] ?? $entry['ayat_awal'],
                            'ayat_selesai' => $entry['ayat_selesai'] ?? $entry['ayat_akhir'],
                            'skor_tajwid' => $entry['skor_tajwid'] ?? 'Lancar',
                            'skor_kelancaran' => $entry['skor_kelancaran'] ?? $entry['nilai_kelancaran'] ?? 'Lancar',
                            'skor_makhraj' => $entry['skor_makhraj'] ?? 'Lancar',
                            'skor_fashahah' => $entry['skor_fashahah'] ?? 'Lancar',
                            'predikat' => $entry['predikat'] ?? 'Lancar',
                            'keputusan' => $entry['keputusan'] ?? 'Lanjut',
                            'catatan' => $entry['catatan'] ?? null,
                        ];
                    }
                    if (!empty($dbEntries)) {
                        $setoranService->saveMultipleSetorans($savedAbsensis[$siswaId]->id, $dbEntries);

                        // Calculate progress hafalan after saving setoran
                        app(\App\Services\ProgresHafalanService::class)->calculateProgres($siswaId);
                    }
                }
            }

            $this->absensiSudahDisimpan = true;
            $this->justSaved = true; // Set flag bahwa baru saja save
            $this->dispatch('toast', variant: 'sukses', message: 'Absensi dan setoran berhasil disimpan.');
            $this->dispatch('absensiSaved');
            $this->loadAbsensi();
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.absensi.input-absensi', [
            'siswaCompletedStatus' => $this->siswaCompletedStatus,
        ]);
    }
}
