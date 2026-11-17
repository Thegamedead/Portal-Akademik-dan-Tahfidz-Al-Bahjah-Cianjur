<?php

namespace App\Livewire\Admin;

use Illuminate\Validation\Rules\Password;
use Livewire\Component;

/**
 * PasswordForm — form ubah password admin (hanya ganti password).
 * Di-render dalam popup "Ubah Password" di layout.
 */
class PasswordForm extends Component
{
    public string $passwordLama           = '';
    public string $passwordBaru           = '';
    public string $passwordBaruKonfirmasi = '';

    public function gantiPassword(): void
    {
        $this->validate([
            'passwordLama'           => ['required'],
            'passwordBaru'           => [
                'required',
                'size:8',
                'different:passwordLama',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'passwordBaruKonfirmasi' => ['required', 'same:passwordBaru'],
        ], [
            'passwordLama.required'           => 'Password lama wajib diisi.',
            'passwordBaru.required'           => 'Password baru wajib diisi.',
            'passwordBaru.size'               => 'Password baru harus tepat 8 karakter.',
            'passwordBaru.different'          => 'Password baru tidak boleh sama dengan password lama.',
            'passwordBaruKonfirmasi.required' => 'Konfirmasi password wajib diisi.',
            'passwordBaruKonfirmasi.same'     => 'Konfirmasi password tidak cocok.',
        ]);

        $admin = auth('admin')->user();

        if (! \Illuminate\Support\Facades\Hash::check($this->passwordLama, $admin->password)) {
            $this->addError('passwordLama', 'Password lama tidak sesuai.');
            return;
        }

        $admin->update(['password' => \Illuminate\Support\Facades\Hash::make($this->passwordBaru)]);

        $this->dispatch('toast', variant: 'sukses', message: 'Password berhasil diperbarui.');
        $this->dispatch('close-password-modal');

        $this->passwordLama           = '';
        $this->passwordBaru           = '';
        $this->passwordBaruKonfirmasi = '';
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.password-form');
    }
}
