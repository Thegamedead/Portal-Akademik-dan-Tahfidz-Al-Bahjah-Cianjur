<?php

namespace App\Livewire\Guru\TesPenempatan;

use App\Models\TesPenempatan;
use App\Models\Juz;
use App\Models\Surah;
use Livewire\Component;
use Livewire\WithPagination;

class IndexTesPenempatan extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    // Bulk selection
    public array $selectedIds = [];
    public bool $selectAll = false;

    // Modal state
    public bool $showModalTes = false;
    public ?int $currentTesId = null;
    public ?TesPenempatan $currentTes = null;

    // Form data
    public array $hasil_hafalan = [];
    public string $catatan_kualitas = '';
    public string $rekomendasi_tingkat = '';

    // Options
    public array $juzOptions = [];
    public array $surahOptions = [];

    // Pending test IDs to process
    public array $pendingTesIds = [];

    public function mount()
    {
        $this->juzOptions = Juz::orderBy('juz_ke')->get()->toArray();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterStatus']);
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $query = TesPenempatan::whereHas('siswa', function($q) {
                    $q->where('status_akademik', 'Aktif');
                })
                ->where('guru_penguji_id', auth('guru')->id())
                ->where('status', 'Terjadwal');

            if ($this->search) {
                $query->whereHas('siswa', function($q) {
                    $q->where('nama', 'like', "%{$this->search}%")
                      ->orWhere('nis', 'like', "%{$this->search}%");
                });
            }

            $this->selectedIds = $query->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function openBulkTestModal()
    {
        if (count($this->selectedIds) === 1) {
            $this->pendingTesIds = []; // No longer using pending IDs since it's 1 by 1
            $this->loadTes($this->selectedIds[0]);
            $this->showModalTes = true;
        } else {
            $this->dispatch('toast', variant: 'warning', message: 'Silakan pilih tepat satu tes.');
        }
    }

    public function loadTes($tesId)
    {
        $this->currentTesId = $tesId;
        $this->currentTes = TesPenempatan::with('siswa')->findOrFail($tesId);

        // Reset form
        $this->hasil_hafalan = [
            [
                'juz_id' => '',
                'surah_id' => '',
                'ayat_mulai' => '',
                'ayat_selesai' => '',
            ]
        ];
        $this->catatan_kualitas = '';
        $this->rekomendasi_tingkat = '';
        $this->resetValidation();
    }

    public function updatedHasilHafalan($value, $key)
    {
        $parts = explode('.', $key);
        $index = (int) $parts[0];
        $field = $parts[1] ?? null;

        if ($field === 'juz_id' && !empty($this->hasil_hafalan[$index]['juz_id'])) {
            $this->loadSurahOptions($index, $this->hasil_hafalan[$index]['juz_id']);
            $this->hasil_hafalan[$index]['surah_id'] = '';
            $this->hasil_hafalan[$index]['ayat_mulai'] = '';
            $this->hasil_hafalan[$index]['ayat_selesai'] = '';
        }
    }

    public function loadSurahOptions($index, $juzId)
    {
        $juz = Juz::with(['surahs' => function ($q) {
            $q->orderBy('nomor_surah');
        }])->find($juzId);

        $this->surahOptions[$index] = $juz ? $juz->surahs->toArray() : [];
    }

    public function addHafalan()
    {
        $this->hasil_hafalan[] = [
            'juz_id' => '',
            'surah_id' => '',
            'ayat_mulai' => '',
            'ayat_selesai' => '',
        ];
    }

    public function removeHafalan($index)
    {
        unset($this->hasil_hafalan[$index]);
        $this->hasil_hafalan = array_values($this->hasil_hafalan);
    }

    protected function rules()
    {
        return [
            'hasil_hafalan' => 'required|array|min:1',
            'hasil_hafalan.*.juz_id' => 'required|exists:juzs,id',
            'hasil_hafalan.*.surah_id' => 'required|exists:surahs,id',
            'hasil_hafalan.*.ayat_mulai' => 'required|integer|min:1',
            'hasil_hafalan.*.ayat_selesai' => 'required|integer|gte:hasil_hafalan.*.ayat_mulai',
            'catatan_kualitas' => 'required|string|min:10|max:1000',
            'rekomendasi_tingkat' => 'required|in:Dasar,Menengah,Atas',
        ];
    }

    protected function messages()
    {
        return [
            'hasil_hafalan.required' => 'Minimal harus ada satu hasil hafalan',
            'hasil_hafalan.*.juz_id.required' => 'Juz wajib dipilih',
            'hasil_hafalan.*.surah_id.required' => 'Surah wajib dipilih',
            'hasil_hafalan.*.ayat_mulai.required' => 'Ayat mulai wajib diisi',
            'hasil_hafalan.*.ayat_selesai.required' => 'Ayat selesai wajib diisi',
            'hasil_hafalan.*.ayat_selesai.gte' => 'Ayat selesai harus >= ayat mulai',
            'catatan_kualitas.required' => 'Catatan kualitas wajib diisi',
            'catatan_kualitas.min' => 'Catatan kualitas minimal 10 karakter',
            'catatan_kualitas.max' => 'Catatan kualitas maksimal 1000 karakter',
            'rekomendasi_tingkat.required' => 'Rekomendasi tingkat wajib dipilih',
        ];
    }

    public function submitTes(\App\Services\TesPenempatan\TesPenempatanService $service, \App\Services\SetoranService $setoranService)
    {
        $this->validate();

        foreach ($this->hasil_hafalan as $index => $hasil) {
            $validation = $setoranService->validateAyatRangeInJuz(
                (int) $hasil['juz_id'],
                (int) $hasil['surah_id'],
                (int) $hasil['ayat_mulai'],
                (int) $hasil['ayat_selesai']
            );

            if (!$validation['valid']) {
                $this->addError("hasil_hafalan.{$index}.ayat_selesai", $validation['message']);
                return;
            }
        }

        try {
            $service->submitTestResults(
                $this->currentTesId,
                $this->hasil_hafalan,
                $this->catatan_kualitas,
                $this->rekomendasi_tingkat
            );

            $this->showModalTes = false;
            $this->selectedIds = [];
            $this->selectAll = false;
            session()->flash('success', 'Hasil tes berhasil disimpan.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function closeTesModal()
    {
        $this->showModalTes = false;
    }

    public function getAyatRangeHints(): array
    {
        $hints = [];
        if (!$this->showModalTes) return $hints;

        $setoranService = app(\App\Services\SetoranService::class);

        foreach ($this->hasil_hafalan as $index => $hasil) {
            if (!empty($hasil['juz_id']) && !empty($hasil['surah_id'])) {
                $hints[$index] = $setoranService->getAllowedAyatRange(
                    (int) $hasil['juz_id'],
                    (int) $hasil['surah_id']
                );
            } else {
                $hints[$index] = null;
            }
        }
        return $hints;
    }

    public function render()
    {
        $query = TesPenempatan::with(['siswa.kelas', 'guruPenguji', 'finalKelompok'])
            ->whereHas('siswa', function($q) {
                // Guru bisa lihat tes yang dia uji
                $q->where('status_akademik', 'Aktif');
            })
            ->where('guru_penguji_id', auth('guru')->id());

        // Apply search
        if ($this->search) {
            $query->whereHas('siswa', function($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('nis', 'like', "%{$this->search}%");
            });
        }

        // Apply filter status
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $tesPenempatans = $query->orderBy('tanggal_tes', 'desc')
                                ->orderBy('waktu_tes', 'desc')
                                ->paginate(15);

        return view('livewire.guru.tes-penempatan.index-tes-penempatan', [
            'tesPenempatans' => $tesPenempatans,
            'ayatRangeHints' => $this->getAyatRangeHints(),
        ])->layout('guru.layouts.app', ['title' => 'Tes Penempatan']);
    }
}
