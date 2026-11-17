<?php

namespace App\Livewire\Guru\TesPenempatan;

use App\Models\TesPenempatan;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('guru.layouts.app')]
#[Title('Hasil Tes Penempatan')]
class HasilTes extends Component
{
    public $tesId;
    public $tes;
    public $siswa;
    public $hasilTes;

    public function mount($id)
    {
        $this->tesId = $id;
        $this->tes = TesPenempatan::with(['siswa', 'guruPenguji', 'hasilTes.juz', 'hasilTes.surah', 'finalKelompok'])
            ->findOrFail($id);

        // Authorization: hanya guru penguji yang bisa akses
        if ($this->tes->guru_penguji_id !== auth('guru')->id()) {
            abort(403, 'Anda tidak memiliki akses ke tes ini.');
        }

        $this->siswa = $this->tes->siswa;
        $this->hasilTes = $this->tes->hasilTes;
    }

    public function kembali()
    {
        return redirect()->route('guru.tes-penempatan.index');
    }

    public function render()
    {
        return view('livewire.guru.tes-penempatan.hasil-tes');
    }
}
