<?php

namespace App\Livewire\Admin\TesPenempatan;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TesPenempatan;
use App\Models\Guru;
use App\Enums\StatusTesPenempatan;

#[Layout('admin.layouts.app')]
#[Title('Tes Penempatan')]
class IndexTesPenempatan extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $tanggal_mulai = '';
    public string $tanggal_akhir = '';
    public string $guru_penguji_id = '';
    public int $per_page = 15;

    // Bulk selection
    public array $selectedIds = [];
    public bool $selectAll = false;

    protected $listeners = ['testScheduled' => '$refresh', 'testCancelled' => '$refresh'];

    public function updating($field)
    {
        if (in_array($field, ['search', 'status', 'tanggal_mulai', 'tanggal_akhir', 'guru_penguji_id', 'per_page'])) {
            $this->resetPage();
            $this->selectedIds = [];
            $this->selectAll = false;
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->getBaseQuery()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status', 'tanggal_mulai', 'tanggal_akhir', 'guru_penguji_id']);
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function deleteSelected()
    {
        if (empty($this->selectedIds)) return;

        $deleted = TesPenempatan::whereIn('id', $this->selectedIds)
            ->whereIn('status', [StatusTesPenempatan::TERJADWAL->value, StatusTesPenempatan::DIBATALKAN->value])
            ->delete();

        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('toast', variant: 'sukses', message: "{$deleted} data tes berhasil dihapus.");
    }

    public function cancelSingle($id)
    {
        $this->dispatch('openCancelModal', tesId: $id);
    }

    /**
     * Get aggregate stats for the summary cards at the top.
     */
    public function getStats(): array
    {
        $total      = TesPenempatan::count();
        $terjadwal  = TesPenempatan::where('status', StatusTesPenempatan::TERJADWAL)->count();
        $selesai    = TesPenempatan::where('status', StatusTesPenempatan::SELESAI)->count();
        $belumDitempatkan = TesPenempatan::where('status', StatusTesPenempatan::SELESAI)
                            ->whereNull('ditempatkan_at')->count();
        $dibatalkan = TesPenempatan::where('status', StatusTesPenempatan::DIBATALKAN)->count();

        return compact('total', 'terjadwal', 'selesai', 'belumDitempatkan', 'dibatalkan');
    }

    protected function getBaseQuery()
    {
        $query = TesPenempatan::with(['siswa', 'guruPenguji', 'admin', 'finalKelompok'])
            ->orderBy('tanggal_tes', 'desc')
            ->orderBy('waktu_tes', 'desc');

        if ($this->search) {
            $query->whereHas('siswa', fn($q) =>
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('nis', 'like', "%{$this->search}%")
            );
        }

        if ($this->status) $query->where('status', $this->status);
        if ($this->guru_penguji_id) $query->where('guru_penguji_id', $this->guru_penguji_id);
        if ($this->tanggal_mulai) $query->whereDate('tanggal_tes', '>=', $this->tanggal_mulai);
        if ($this->tanggal_akhir) $query->whereDate('tanggal_tes', '<=', $this->tanggal_akhir);

        return $query;
    }

    public function render()
    {
        $tests = $this->getBaseQuery()->paginate($this->per_page);
        $gurus = Guru::where('status_aktif', true)->orderBy('nama')->get();

        return view('livewire.admin.tes-penempatan.index-tes-penempatan', [
            'tests'  => $tests,
            'gurus'  => $gurus,
            'stats'  => $this->getStats(),
        ])->layout('admin.layouts.app', ['title' => 'Manajemen Tes Penempatan']);
    }
}
