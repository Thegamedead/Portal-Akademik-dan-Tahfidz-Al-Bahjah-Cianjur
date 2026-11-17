<?php

namespace App\Livewire\Siswa;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('siswa.layouts.app')]
#[Title('Profil Siswa')]
class ProfilSiswa extends Component
{
    // ─── Listeners ───────────────────────────────────────────────────────────

    protected $listeners = [
        'student-placed' => '$refresh',  // Refresh component when student is placed to new group
    ];

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\Siswa $siswa */
        $siswa = auth('siswa')->user();

        // Load relationships: kelompok placement
        $siswa->load(['kelompok.guruPembimbing', 'kelompok.tahunAjaran']);

        return view('livewire.siswa.profil-siswa', [
            'siswa' => $siswa,
        ]);
    }
}
