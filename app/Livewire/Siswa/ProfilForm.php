<?php

namespace App\Livewire\Siswa;

use Livewire\Component;

/**
 * ProfilForm - Popup form untuk edit profil siswa (nama saja, read-only lainnya)
 */
class ProfilForm extends Component
{
    public function render(): \Illuminate\View\View
    {
        $siswa = auth('siswa')->user();
        $siswa->load(['kelompok.guruPembimbing']);

        return view('livewire.siswa.profil-form', ['siswa' => $siswa]);
    }
}
