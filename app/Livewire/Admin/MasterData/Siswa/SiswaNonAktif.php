<?php

namespace App\Livewire\Admin\MasterData\Siswa;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Enums\StatusAkademik;
use App\Enums\StatusTahfidz;
use App\Enums\JenisKelamin;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('admin.layouts.app')]
#[Title('Siswa Non-Aktif')]
class SiswaNonAktif extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatusAkademik = '';
    public string $filterJenisKelamin = '';
    public bool $showFilters = false;

    // Bulk Actions state
    public array $selectedIds = [];
    public bool $selectAll = false;
    public bool $showModalKonfirmasiBatalkan = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['filterStatusAkademik', 'filterJenisKelamin', 'search']);
        $this->resetPage();
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedIds = $this->getSiswasQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSelectedIds(): void
    {
        $this->selectAll = count($this->selectedIds) === $this->getSiswasQuery()->count();
    }

    private function getSiswasQuery()
    {
        // Get only students who are non-aktif with relations
        $query = Siswa::where('status_aktif', false)->with(['kelompok', 'kelas', 'progresHafalan']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nis', 'like', "%{$this->search}%")
                    ->orWhere('nisn', 'like', "%{$this->search}%")
                    ->orWhere('nama', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatusAkademik) {
            $query->where('status_akademik', $this->filterStatusAkademik);
        }

        if ($this->filterJenisKelamin) {
            $query->where('jenis_kelamin', $this->filterJenisKelamin);
        }

        $query->latest('updated_at');

        return $query;
    }

    public function bukaModalKonfirmasiBatalkan(): void
    {
        if (count($this->selectedIds) === 0) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih minimal 1 siswa untuk dibatalkan status non-aktifnya.');
            return;
        }
        $this->showModalKonfirmasiBatalkan = true;
    }

    public function tutupModalKonfirmasiBatalkan(): void
    {
        $this->showModalKonfirmasiBatalkan = false;
    }

    public function prosesBatalkanBulk(): void
    {
        if (count($this->selectedIds) === 0) return;

        try {
            DB::transaction(function () {
                Siswa::whereIn('id', $this->selectedIds)->update([
                    'status_akademik' => 'Aktif',
                    'status_aktif' => true,
                    'jenis_keluar' => null,
                    'tanggal_keluar' => null,
                    'tujuan_sekolah' => null,
                    'keterangan_keluar' => null,
                ]);
            });

            $count = count($this->selectedIds);
            $this->selectedIds = [];
            $this->selectAll = false;
            $this->tutupModalKonfirmasiBatalkan();
            
            $this->dispatch('toast', variant: 'success', message: "Berhasil mengaktifkan kembali {$count} siswa.");
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: 'Gagal membatalkan status: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $siswas = $this->getSiswasQuery()->paginate(20);

        return view('livewire.admin.master-data.siswa.siswa-non-aktif', [
            'siswas' => $siswas,
            'statusAkademikOptions' => StatusAkademik::options(),
            'jenisKelaminOptions' => JenisKelamin::options(),
        ]);
    }
}
