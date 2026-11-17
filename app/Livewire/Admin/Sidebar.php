<?php

namespace App\Livewire\Admin;

use App\Models\Sekolah;
use Livewire\Component;
use Livewire\Attributes\On;

class Sidebar extends Component
{
    public $sekolah;

    public function mount()
    {
        $this->sekolah = Sekolah::getInstance();
    }

    #[On('sekolah-updated')]
    public function refreshSekolah()
    {
        $this->sekolah = Sekolah::getInstance();
    }

    public function render()
    {
        return view('livewire.admin.sidebar');
    }
}
