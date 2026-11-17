<?php

namespace App\Livewire\Shared;

use App\Models\Juz;
use Livewire\Component;

class ReferensiJuz extends Component
{
    public $selectedJuz = null;
    public $juzDetail = null;

    public function selectJuz($juzId)
    {
        $this->selectedJuz = $juzId;

        // Load juz detail with surahs
        $this->juzDetail = Juz::with(['surahs' => function($query) {
            $query->orderBy('nomor_surah');
        }])->find($juzId);
    }

    public function closeDetail()
    {
        $this->selectedJuz = null;
        $this->juzDetail = null;
    }

    public function render()
    {
        // Get all juz ordered by juz_ke
        $juzs = Juz::orderBy('juz_ke')->get();

        // Detect which guard is authenticated
        $guard = null;
        $title = 'Referensi Juz';
        $layout = null;

        if (auth('admin')->check()) {
            $guard = 'admin';
            $layout = 'admin.layouts.app';
        } elseif (auth('guru')->check()) {
            $guard = 'guru';
            $layout = 'guru.layouts.app';
        } elseif (auth('siswa')->check()) {
            $guard = 'siswa';
            $layout = 'siswa.layouts.app';
        }

        return view('livewire.shared.referensi-juz', [
            'juzs' => $juzs,
            'guard' => $guard,
        ])->layout($layout, ['title' => $title]);
    }
}
