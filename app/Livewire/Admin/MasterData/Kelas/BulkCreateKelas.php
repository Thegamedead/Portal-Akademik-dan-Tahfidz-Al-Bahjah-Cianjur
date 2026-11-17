<?php

namespace App\Livewire\Admin\MasterData\Kelas;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Services\MasterData\KelasService;

#[Layout('admin.layouts.app')]
class BulkCreateKelas extends Component
{
    public $tingkat = '7';
    public $jumlah_rombel = 4;
    public $preview = [];

    protected $rules = [
        'tingkat' => 'required|in:7,8,9',
        'jumlah_rombel' => 'required|integer|min:1|max:10',
    ];

    public function mount()
    {
        $this->generatePreview();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->generatePreview();
    }

    public function generatePreview()
    {
        if (!$this->tingkat || !$this->jumlah_rombel) {
            $this->preview = [];
            return;
        }

        $this->preview = [];
        $rombelLetters = range('A', 'Z');

        for ($i = 0; $i < $this->jumlah_rombel; $i++) {
            $this->preview[] = [
                'tingkat' => $this->tingkat,
                'nama_kelas' => $rombelLetters[$i],
                'nama_lengkap' => $this->tingkat . ' - ' . $rombelLetters[$i],
            ];
        }
    }

    public function save(KelasService $service)
    {
        $this->validate();

        try {
            $kelasData = array_map(function ($item) {
                return [
                    'tingkat' => $item['tingkat'],
                    'nama_kelas' => $item['nama_kelas'],
                    'nama_wali_kelas' => 'Belum Ditentukan',
                ];
            }, $this->preview);

            $service->bulkCreate($kelasData);

            $this->dispatch('toast', variant: 'sukses', message: count($kelasData) . ' kelas berhasil ditambahkan');
            return redirect()->route('admin.master-data.kelas.index');
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.master-data.kelas.bulk-create-kelas');
    }
}
