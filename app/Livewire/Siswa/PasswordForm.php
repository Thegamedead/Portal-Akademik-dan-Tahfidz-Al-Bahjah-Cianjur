<?php

namespace App\Livewire\Siswa;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * PasswordForm - Popup form untuk ubah password siswa
 */
class PasswordForm extends Component
{
    public string $passwordLama        = '';
    public string $passwordBaru        = '';
    public string $passwordKonfirmasi  = '';

    public function gantiPassword(): void
    {
        $this->validate([
            'passwordLama'       => ['required', 'string'],
            'passwordBaru'       => [
                'required',
                'string',
                'min:8',
                'max:20',
                'different:passwordLama',
                Password::min(8)->mixedCase()->numbers(),
            ],
            'passwordKonfirmasi' => ['required', 'string', 'same:passwordBaru'],
        ], [
            'passwordLama.required'       => 'Password saat ini wajib diisi.',
            'passwordBaru.required'       => 'Password baru wajib diisi.',
            'passwordBaru.min'            => 'Password baru minimal 8 karakter.',
            'passwordBaru.max'            => 'Password baru maksimal 20 karakter.',
            'passwordBaru.different'      => 'Password baru tidak boleh sama dengan password lama.',
            'passwordKonfirmasi.required' => 'Konfirmasi password wajib diisi.',
            'passwordKonfirmasi.same'     => 'Konfirmasi password tidak cocok.',
        ]);

        /** @var \App\Models\Siswa $siswa */
        $siswa = auth('siswa')->user();

        if (! Hash::check($this->passwordLama, $siswa->password)) {
            $this->addError('passwordLama', 'Password saat ini tidak sesuai.');
            return;
        }

        $wasDefault = $siswa->keterangan_akun === 'Password belum diganti';

        $siswa->update([
            'password'        => Hash::make($this->passwordBaru),
            'password_text'   => null,
            'keterangan_akun' => 'Password sudah diganti',
        ]);

        $this->reset(['passwordLama', 'passwordBaru', 'passwordKonfirmasi']);
        $this->resetErrorBag();

        if ($wasDefault) {
            // Password default berhasil diganti, force logout dan paksa login ulang
            auth('siswa')->logout();
            session()->invalidate();
            session()->regenerateToken();

            session()->flash('toast', [
                'variant' => 'sukses',
                'message' => 'Password berhasil diperbarui. Silakan login kembali dengan password baru Anda.',
            ]);

            $this->redirect(route('siswa.login'), navigate: false);
            return;
        }

        // Ganti password biasa (bukan default): cukup toast dan tutup modal
        $this->dispatch('toast', variant: 'sukses', message: 'Password berhasil diperbarui.');
        $this->dispatch('close-password-modal');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.siswa.password-form');
    }
}
