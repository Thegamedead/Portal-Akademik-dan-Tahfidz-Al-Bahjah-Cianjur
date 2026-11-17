<?php

namespace App\Livewire\Admin\MasterData\Guru;

use App\Models\Guru;
use App\Services\ErrorHandlingService;
use App\Enums\JabatanGuru;
use App\Enums\JenisKelamin;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Services\MasterData\GuruService;

#[Layout('admin.layouts.app')]
class GuruNonAktif extends Component
{
    use WithPagination;

    // ─── Properties ──────────────────────────────────────────────────────────

    public string $search = '';
    public string $filterJabatan = '';
    public string $filterJenisKelamin = '';
    public bool $showFilters = false;

    // Bulk selection
    public array $selectedIds = [];
    public bool $selectAll = false;

    public bool $showModalKonfirmasiBatalkan = false;

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingFilterJabatan(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingFilterJenisKelamin(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedIds = $this->getFilteredQuery()->pluck('id')->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSelectedIds(): void
    {
        $totalFiltered = $this->getFilteredQuery()->count();
        $this->selectAll = count($this->selectedIds) === $totalFiltered && $totalFiltered > 0;
    }

    private function getFilteredQuery()
    {
        $query = Guru::where('status_aktif', false);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('niy', 'like', "%{$this->search}%")
                    ->orWhere('nama', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterJabatan) {
            $query->where('jabatan', $this->filterJabatan);
        }

        if ($this->filterJenisKelamin) {
            $query->where('jenis_kelamin', $this->filterJenisKelamin);
        }

        return $query;
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'filterJabatan',
            'filterJenisKelamin'
        ]);
        $this->resetPage();
    }

    // ─── Bulk Batalkan Methods ────────────────────────────────────────────────

    public function bukaModalKonfirmasiBatalkan(): void
    {
        if (count($this->selectedIds) === 0) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih minimal 1 guru untuk dibatalkan status Non-Aktifnya');
            return;
        }
        $this->showModalKonfirmasiBatalkan = true;
    }

    public function tutupModalKonfirmasiBatalkan(): void
    {
        $this->showModalKonfirmasiBatalkan = false;
    }

    public function prosesBatalkanBulk(GuruService $guruService): void
    {
        try {
            foreach ($this->selectedIds as $id) {
                $guruService->updateStatus($id, true);
            }

            $count = count($this->selectedIds);
            $this->selectedIds = [];
            $this->selectAll = false;
            $this->showModalKonfirmasiBatalkan = false;

            $this->dispatch('toast', variant: 'sukses', message: "{$count} guru berhasil diaktifkan kembali.");
            $this->resetPage();
        } catch (\Exception $e) {
            $this->showModalKonfirmasiBatalkan = false;
            ErrorHandlingService::handleInComponent($this, $e, 'mengaktifkan kembali guru');
        }
    }

    /**
     * Get jabatan badge color based on value
     */
    public function getJabatanBadgeColor(string $jabatan): string
    {
        return match($jabatan) {
            'Guru Pembimbing Tahfidz' => 'zamrud',
            'Guru Mapel' => 'slate',
            'Kepala Sekolah' => 'emas',
            'Tendik' => 'slate',
            default => 'slate',
        };
    }

    public function render(): \Illuminate\View\View
    {
        $query = $this->getFilteredQuery();

        // Eager load riwayat guru pembimbing kelompok untuk guru non-aktif
        $query->with(['riwayatGuruPembimbingKelompoks' => function($q) {
            $q->latest('tanggal_mulai')->limit(1)->with('kelompok');
        }]);

        $query->latest();
        $gurus = $query->paginate(20);

        return view('livewire.admin.master-data.guru.guru-non-aktif', [
            'gurus' => $gurus,
            'jabatanOptions' => JabatanGuru::options(),
            'jenisKelaminOptions' => JenisKelamin::options(),
        ]);
    }
}
