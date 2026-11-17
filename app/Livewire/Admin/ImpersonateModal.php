<?php

namespace App\Livewire\Admin;

use App\Models\Guru;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ImpersonateModal extends Component
{
    public $isOpen = false;
    public $search = '';

    protected $listeners = ['open-impersonate-modal' => 'openModal'];

    public function openModal()
    {
        $this->isOpen = true;
        $this->search = '';
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->search = '';
    }

    public function impersonate($guruId)
    {
        $guru = Guru::where('id', $guruId)
            ->where('status_aktif', true)
            ->where('akun_aktif', true)
            ->whereNotNull('password')
            ->first();

        if ($guru) {
            // Save admin ID in session before impersonating
            session()->put('impersonate_admin_id', auth('admin')->id());
            
            // Login as the selected Guru
            Auth::guard('guru')->login($guru);

            // Redirect to Guru dashboard
            return redirect()->route('guru.dashboard');
        } else {
            $this->dispatch('toast', variant: 'error', message: 'Akun guru tidak ditemukan atau belum digenerate.');
        }
    }

    public function render()
    {
        $query = Guru::where('status_aktif', true)
            ->where('akun_aktif', true)
            ->whereNotNull('password');

        if (!empty(trim($this->search))) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . trim($this->search) . '%')
                  ->orWhere('niy', 'like', '%' . trim($this->search) . '%');
            });
        }

        $gurus = $query->with('kelompoksBimbingan')
            ->orderBy('nama', 'asc')
            ->take(20)
            ->get();

        return view('livewire.admin.impersonate-modal', [
            'gurus' => $gurus
        ]);
    }
}
