<?php

namespace App\Livewire\Admin;

use App\Models\Admin;
use App\Services\AkunService;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Manajemen Akun Admin')]
class ManajemenAkunAdmin extends Component
{
    use WithPagination;

    // ─── Properties ──────────────────────────────────────────────────────────

    public string $cari = '';

    public bool $showModal = false;

    /** @var array{username:string, password:string, password_confirmation:string, status_aktif:bool} */
    public array $formData = [
        'nama'                  => '',
        'email'                 => '',
        'password'              => '',
        'password_confirmation' => '',
        'status_aktif'          => true,
    ];

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    public function updatedCari(): void
    {
        $this->resetPage();
    }

    // ─── Query Helpers ────────────────────────────────────────────────────────

    protected function getAdminQuery()
    {
        return Admin::where(function($q) {
                $q->where('email', 'like', '%' . $this->cari . '%')
                  ->orWhere('nama', 'like', '%' . $this->cari . '%');
            })
            ->orderBy('nama');
    }

    // ─── Modal Tambah Admin ───────────────────────────────────────────────────

    /**
     * Buka modal tambah admin dengan form yang sudah direset.
     */
    public function bukaModalTambah(): void
    {
        $this->formData = [
            'nama'                  => '',
            'email'                 => '',
            'password'              => '',
            'password_confirmation' => '',
            'status_aktif'          => true,
        ];
        $this->resetErrorBag();
        $this->showModal = true;
    }

    /**
     * Tutup modal dan bersihkan form.
     */
    public function tutupModal(): void
    {
        $this->showModal = false;
        $this->formData  = [
            'nama'                  => '',
            'email'                 => '',
            'password'              => '',
            'password_confirmation' => '',
            'status_aktif'          => true,
        ];
        $this->resetErrorBag();
    }

    /**
     * Simpan akun Admin baru.
     */
    public function simpanAkun(AkunService $akunService): void
    {
        $this->validate([
            'formData.nama'     => ['required', 'string', 'max:100'],
            'formData.email'    => ['required', 'email', 'max:150', 'unique:admins,email'],
            'formData.password' => [
                'required',
                'string',
                'max:8',
                'confirmed:formData.password_confirmation',
                \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()->symbols()
            ],
        ], [
            'formData.nama.required'     => 'Nama wajib diisi.',
            'formData.email.required'    => 'Email wajib diisi.',
            'formData.email.email'       => 'Format email tidak valid.',
            'formData.email.unique'      => 'Email sudah digunakan.',
            'formData.password.required' => 'Password wajib diisi.',
            'formData.password.max'      => 'Password maksimal 8 karakter.',
            'formData.password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        $akunService->tambahAdmin([
            'nama'         => $this->formData['nama'],
            'email'        => $this->formData['email'],
            'password'     => $this->formData['password'],
            'status_aktif' => $this->formData['status_aktif'],
        ]);

        $this->dispatch('toast', variant: 'sukses', message: 'Akun Admin baru berhasil ditambahkan.');

        $this->tutupModal();
    }

    // ─── Nonaktifkan Admin ────────────────────────────────────────────────────

    /**
     * Nonaktifkan akun Admin berdasarkan ID.
     *
     * Menangkap RuntimeException ketika hanya ada 1 admin aktif tersisa.
     */
    public function nonaktifkanAdmin(int $id, AkunService $akunService): void
    {
        try {
            $akunService->nonaktifkanAdmin($id, auth('admin')->id());
            $this->dispatch('toast', variant: 'sukses', message: 'Akun Admin berhasil dinonaktifkan.');
            $this->dispatch('$refresh');
        } catch (\RuntimeException $e) {
            $this->dispatch('toast', variant: 'error', message: 'Tidak dapat menonaktifkan. Minimal harus ada 1 Admin aktif.');
        }
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        $admins = $this->getAdminQuery()->paginate(10);

        return view('livewire.admin.manajemen-akun-admin', compact('admins'));
    }
}
