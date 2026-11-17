<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Livewire\Attributes\Validate;

class ProfilForm extends Component
{
    #[Validate('required|string|max:255')]
    public $nama = '';

    #[Validate('required|email|max:255|unique:gurus,email')]
    public $email = '';

    public $niy = '';

    public function mount()
    {
        $guru = auth('guru')->user();
        $this->nama = $guru->nama;
        $this->email = $guru->email;
        $this->niy = $guru->niy;
    }

    public function save()
    {
        $guru = auth('guru')->user();

        $rules = [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:gurus,email,' . $guru->id,
        ];

        $this->validate($rules);

        $guru->update([
            'nama' => $this->nama,
            'email' => $this->email,
        ]);

        $this->dispatch('close-profil-modal');
        $this->dispatch('profile-updated', [
            'nama' => $this->nama,
            'email' => $this->email,
            'niy' => $this->niy,
            'inisial' => strtoupper(substr($this->nama, 0, 1))
        ]);
        $this->dispatch('toast', variant: 'sukses', message: 'Profil berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.guru.profil-form');
    }
}
