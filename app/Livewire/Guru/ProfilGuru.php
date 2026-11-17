<?php

namespace App\Livewire\Guru;

use Livewire\Component;

class ProfilGuru extends Component
{


    // ─── Render ───────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\Guru $guru */
        $guru = auth('guru')->user()->makeVisible(['tanggal_lahir']);

        // Load relationships
        $guru->load(['kelompoks.tahunAjaran']);

        return view('livewire.guru.profil-guru', [
            'guru' => $guru,
        ])->layout('guru.layouts.app', ['title' => 'Profil Saya']);
    }
}
