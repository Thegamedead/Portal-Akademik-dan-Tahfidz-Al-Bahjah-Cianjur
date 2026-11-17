<?php

namespace App\Livewire\Admin;

use Livewire\Component;

/**
 * ProfilForm — form update nama & email admin.
 * Di-render dalam popup "Profil Saya" di layout.
 */
class ProfilForm extends Component
{
    public string $nama  = '';
    public string $email = '';

    public function mount(): void
    {
        $admin       = auth('admin')->user();
        $this->nama  = $admin->nama  ?? '';
        $this->email = $admin->email ?? '';
    }

    public function simpanProfil(): void
    {
        $admin = auth('admin')->user();

        $this->validate([
            'nama'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:admins,email,' . $admin->id],
        ], [
            'nama.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah digunakan oleh akun lain.',
        ]);

        $admin->update([
            'nama'  => $this->nama,
            'email' => $this->email,
        ]);

        $this->dispatch('toast', variant: 'sukses', message: 'Profil berhasil diperbarui.');
        $this->dispatch('close-profil-modal');
        
        // Trigger Alpine JS di app.blade.php untuk update nama, email, dan inisial tanpa reload
        $this->dispatch('profile-updated', 
            nama: $this->nama ?: $this->email, 
            email: $this->email, 
            inisial: strtoupper(substr($this->nama ?: $this->email, 0, 1))
        );
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.profil-form');
    }
}
