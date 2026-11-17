<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordForm extends Component
{
    public string $passwordLama           = '';
    public string $passwordBaru           = '';
    public string $passwordBaruKonfirmasi = '';

    public function gantiPassword(): void
    {
        $this->validate([
            'passwordLama'           => [
                'required',
                'string',
            ],
            'passwordBaru'           => [
                'required',
                'string',
                'min:8',
                'max:20',
                'different:passwordLama',
                Password::min(8)->mixedCase()->numbers(),
            ],
            'passwordBaruKonfirmasi' => ['required', 'same:passwordBaru'],
        ], [
            'passwordLama.required'           => 'Password lama wajib diisi.',
            'passwordBaru.required'           => 'Password baru wajib diisi.',
            'passwordBaru.min'                => 'Password baru minimal 8 karakter.',
            'passwordBaru.max'                => 'Password baru maksimal 20 karakter.',
            'passwordBaru.different'          => 'Password baru tidak boleh sama dengan password lama.',
            'passwordBaruKonfirmasi.required' => 'Konfirmasi password wajib diisi.',
            'passwordBaruKonfirmasi.same'     => 'Konfirmasi password tidak cocok.',
        ]);

        $guru = auth('guru')->user();

        if (! Hash::check($this->passwordLama, $guru->password)) {
            $this->addError('passwordLama', 'Password lama tidak sesuai.');
            return;
        }

        $wasDefault = $guru->keterangan_akun === 'Password belum diganti';

        $guru->update([
            'password'        => Hash::make($this->passwordBaru),
            'password_text'   => null,
            'keterangan_akun' => 'Password sudah diganti',
        ]);

        $this->passwordLama           = '';
        $this->passwordBaru           = '';
        $this->passwordBaruKonfirmasi = '';

        if ($wasDefault) {
            // Password default berhasil diganti, force logout dan paksa login ulang
            auth('guru')->logout();
            session()->invalidate();
            session()->regenerateToken();

            session()->flash('toast', [
                'variant' => 'sukses',
                'message' => 'Password berhasil diperbarui. Silakan login kembali dengan password baru Anda.',
            ]);

            $this->redirect(route('guru.login'), navigate: false);
            return;
        }

        // Ganti password biasa: cukup toast dan tutup modal
        // Regenerate session untuk menghindari session issue
        session()->regenerate();

        $this->dispatch('toast', variant: 'sukses', message: 'Password berhasil diperbarui.');
        $this->dispatch('close-password-modal');
    }

    public function render()
    {
        return view('livewire.guru.password-form');
    }
}
