<?php

namespace App\Livewire\Guru\UsulanKenaikan;

use App\Models\UsulanKenaikanTingkat;
use Livewire\Component;
use Livewire\WithPagination;

class IndexUsulan extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterTingkatAsal = '';
    public string $filterTingkatTujuan = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTingkatAsal(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTingkatTujuan(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterStatus', 'filterTingkatAsal', 'filterTingkatTujuan']);
        $this->resetPage();
    }

    public function render()
    {
        $guru = auth('guru')->user();

        // Guru melihat usulan yang dia buat (berdasarkan guru_pengusul_id)
        // Bukan berdasarkan kelompok siswa saat ini, karena jika siswa lulus/pindah, kelompoknya bisa berubah atau null
        $query = UsulanKenaikanTingkat::with(['siswa.kelas', 'siswa.kelompok', 'guruPengusul', 'kelompokAsal'])
            ->where('guru_pengusul_id', $guru->id);

        // Apply search
        if ($this->search) {
            $query->whereHas('siswa', function($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('nis', 'like', "%{$this->search}%");
            });
        }

        // Apply filter status
        if ($this->filterStatus) {
            $query->where('status_usulan', $this->filterStatus);
        }

        // Apply filter tingkat asal
        if ($this->filterTingkatAsal) {
            $query->where('tingkat_asal', $this->filterTingkatAsal);
        }

        // Apply filter tingkat tujuan
        if ($this->filterTingkatTujuan) {
            $query->where('tingkat_tujuan', $this->filterTingkatTujuan);
        }

        $usulans = $query->orderBy('tanggal_usul', 'desc')->paginate(15);

        return view('livewire.guru.usulan-kenaikan.index-usulan', [
            'usulans' => $usulans,
        ])->layout('guru.layouts.app', ['title' => 'Usulan Kenaikan Tingkat']);
    }
}
