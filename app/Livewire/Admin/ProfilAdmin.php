<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class ProfilAdmin extends Component
{
    // ─── Properties ──────────────────────────────────────────────────────────

    // Form Profil
    public string $nama = '';
    public string $email = '';

    // Form Ganti Password
    public string $passwordLama = '';
    public string $passwordBaru = '';
    public string $passwordBaruKonfirmasi = '';

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $admin = auth('admin')->user();
        $this->nama  = $admin->nama  ?? '';
        $this->email = $admin->email ?? '';
    }

    // ─── Methods ─────────────────────────────────────────────────────────────

    /**
     * Update profil Admin (nama dan email).
     */
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
    }

    /**
     * Ganti password Admin yang sedang login.
     *
     * Alur:
     * 1. Validasi field (format & konfirmasi)
     * 2. Verifikasi password lama via Hash::check
     * 3. Update hash password baru ke database
     * 4. Dispatch toast sukses
     */
    public function gantiPassword(): void
    {
        $this->validate([
            'passwordLama'           => ['required'],
            'passwordBaru'           => [
                'required',
                'different:passwordLama',
                'size:8',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'passwordBaruKonfirmasi' => ['required', 'same:passwordBaru'],
        ], [
            'passwordLama.required'           => 'Password lama wajib diisi.',
            'passwordBaru.required'           => 'Password baru wajib diisi.',
            'passwordBaru.different'          => 'Password baru tidak boleh sama dengan password lama.',
            'passwordBaru.size'               => 'Password baru harus 8 karakter.',
            'passwordBaruKonfirmasi.required' => 'Konfirmasi password wajib diisi.',
            'passwordBaruKonfirmasi.same'     => 'Konfirmasi password tidak cocok dengan password baru.',
        ]);

        $admin = auth('admin')->user();

        if (! Hash::check($this->passwordLama, $admin->password)) {
            $this->addError('passwordLama', 'Password lama tidak sesuai.');
            return;
        }

        $admin->update(['password' => Hash::make($this->passwordBaru)]);

        $this->dispatch('toast', variant: 'sukses', message: 'Password berhasil diperbarui.');

        // Kosongkan form setelah sukses
        $this->passwordLama = '';
        $this->passwordBaru = '';
        $this->passwordBaruKonfirmasi = '';
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.profil-admin');
    }
}
