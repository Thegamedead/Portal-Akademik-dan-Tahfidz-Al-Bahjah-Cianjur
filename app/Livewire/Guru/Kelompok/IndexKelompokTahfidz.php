<?php

namespace App\Livewire\Guru\Kelompok;

use App\Models\Kelompok;
use Livewire\Component;
use Livewire\WithPagination;

class IndexKelompokTahfidz extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterTingkat = '';
    public string $filterJenis = '';

    // Bulk selection
    public array $selectedIds = [];
    public bool $selectAll = false;

    // Modal Anggota
    public bool $showAnggotaModal = false;
    public ?int $selectedKelompokId = null;
    public string $searchAnggota = '';
    public $anggotaList = [];
    public $kelompokNama = '';
    public $kelompokTingkat = '';
    public $kelompokJenis = '';
    public $guruNama = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingFilterTingkat(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingFilterJenis(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterTingkat', 'filterJenis']);
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $guru = auth('guru')->user();
            $this->selectedIds = Kelompok::where('guru_pembimbing_id', $guru->id)
                ->when($this->search, fn($q) => $q->where('nama', 'like', "%{$this->search}%"))
                ->when($this->filterTingkat, fn($q) => $q->where('tingkat', $this->filterTingkat))
                ->when($this->filterJenis, fn($q) => $q->where('jenis', $this->filterJenis))
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSelectedIds(): void
    {
        $guru = auth('guru')->user();
        $totalFiltered = Kelompok::where('guru_pembimbing_id', $guru->id)
            ->when($this->search, fn($q) => $q->where('nama', 'like', "%{$this->search}%"))
            ->when($this->filterTingkat, fn($q) => $q->where('tingkat', $this->filterTingkat))
            ->when($this->filterJenis, fn($q) => $q->where('jenis', $this->filterJenis))
            ->count();

        $this->selectAll = count($this->selectedIds) === $totalFiltered && $totalFiltered > 0;
    }

    public function bukaAnggotaModal($kelompokId): void
    {
        $kelompokId = (int) $kelompokId;
        $kelompok   = Kelompok::with(['guruPembimbing', 'siswas.kelas'])->findOrFail($kelompokId);

        // Verifikasi akses
        if ($kelompok->guru_pembimbing_id != auth('guru')->id()) {
            return;
        }

        // Ambil tanggal masuk dari riwayat_kelompoks (hanya record masuk ke kelompok ini)
        $riwayats = \App\Models\RiwayatKelompok::where('kelompok_ke_id', $kelompokId)
            ->orderBy('tanggal', 'asc')
            ->get()
            ->groupBy('siswa_id');

        $this->selectedKelompokId = $kelompokId;
        $this->kelompokNama       = $kelompok->nama;
        $this->kelompokTingkat    = $kelompok->tingkat;
        $this->kelompokJenis      = $kelompok->jenis;
        $this->guruNama           = $kelompok->guruPembimbing?->nama ?? '-';

        $this->anggotaList = $kelompok->siswas->map(function ($s) use ($kelompokId, $riwayats) {
            $history = $riwayats->get($s->id, collect());

            // Tanggal masuk = record terakhir masuk ke kelompok ini
            $masukRecord = $history->where('kelompok_ke_id', $kelompokId)->last();
            $tanggalMasuk = $masukRecord
                ? \Carbon\Carbon::parse($masukRecord->tanggal)->format('d/m/Y')
                : '—';

            return [
                'id'            => $s->id,
                'nama'          => $s->nama,
                'nis'           => $s->nis ?? '-',
                'kelas_nama'    => $s->kelas?->nama_kelas ?? '-',
                'tanggal_masuk' => $tanggalMasuk,
            ];
        })->values()->toArray();

        $this->searchAnggota = '';
        $this->showAnggotaModal = true;
    }

    public function tutupAnggotaModal(): void
    {
        $this->showAnggotaModal  = false;
        $this->selectedKelompokId = null;
        $this->anggotaList       = [];
        $this->kelompokNama      = '';
        $this->kelompokTingkat   = '';
        $this->kelompokJenis     = '';
        $this->guruNama          = '';
        $this->searchAnggota     = '';
    }

    public function render()
    {
        // Guru hanya lihat kelompok yang dia bimbing
        $guru = auth('guru')->user();

        $query = Kelompok::with(['guruPembimbing', 'tahunAjaran'])
            ->where('guru_pembimbing_id', $guru->id)
            ->withCount(['siswas' => function($q) {
                $q->where('status_penempatan', 'Ditempatkan');
            }]);

        // Apply search
        if ($this->search) {
            $query->where('nama', 'like', "%{$this->search}%");
        }

        // Apply filter tingkat
        if ($this->filterTingkat) {
            $query->where('tingkat', $this->filterTingkat);
        }

        // Apply filter jenis
        if ($this->filterJenis) {
            $query->where('jenis', $this->filterJenis);
        }

        $kelompoks = $query->orderBy('tingkat')
                          ->orderBy('jenis')
                          ->orderBy('nama')
                          ->paginate(15);

        return view('livewire.guru.kelompok.index-kelompok-tahfidz', [
            'kelompoks' => $kelompoks,
        ])->layout('guru.layouts.app', ['title' => 'Kelompok Tahfidz']);
    }
}
