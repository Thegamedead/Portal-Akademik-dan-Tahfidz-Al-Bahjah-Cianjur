<?php

namespace App\Livewire\Admin\MasterData\Kelas;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kelas;

#[Layout('admin.layouts.app')]
class DetailKelas extends Component
{
    use WithPagination;

    public $kelasId;
    public $kelas;
    public $activeTab = 'info';
    public $searchSiswa = '';

    public function mount($id)
    {
        $this->kelasId = $id;
        $this->loadKelas();
    }

    public function loadKelas()
    {
        $this->kelas = Kelas::with(['tahunAjaran', 'waliKelas'])
            ->withCount('siswas')
            ->findOrFail($this->kelasId);
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        $siswas = collect();

        if ($this->activeTab === 'siswas') {
            $siswas = $this->kelas->siswas()
                ->when($this->searchSiswa, fn($q) =>
                    $q->where('nama', 'like', "%{$this->searchSiswa}%")
                      ->orWhere('nis', 'like', "%{$this->searchSiswa}%")
                )
                ->orderBy('nama')
                ->paginate(20);
        }

        return view('livewire.admin.master-data.kelas.detail-kelas', [
            'siswas' => $siswas,
        ]);
    }
}
