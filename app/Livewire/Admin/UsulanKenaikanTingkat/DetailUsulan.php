<?php

namespace App\Livewire\Admin\UsulanKenaikanTingkat;

use App\Models\UsulanKenaikanTingkat;
use App\Models\ParameterKonfigurasi;
use Livewire\Component;

class DetailUsulan extends Component
{
    public ?int $usulanId = null;
    public ?UsulanKenaikanTingkat $usulan = null;
    public bool $showModal = false;

    protected $listeners = [
        'open-detail-modal' => 'openModal',
    ];

    public function openModal(int $usulanId): void
    {
        $this->usulanId = $usulanId;
        $this->loadUsulan();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->usulanId = null;
        $this->usulan = null;
    }

    private function loadUsulan(): void
    {
        $this->usulan = UsulanKenaikanTingkat::with([
            'siswa.progresHafalans',
            'siswa',
            'guruPengusul',
            'kelompokAsal',
            'kelompokTujuan',
            'adminVerifikator'
        ])->findOrFail($this->usulanId);
    }

    public function getRequiredThreshold(): int
    {
        if (!$this->usulan) return 0;
        $config = ParameterKonfigurasi::getInstance();
        $batasTingkat = $config->batas_tingkat;
        $tingkatKey = $this->usulan->tingkat_tujuan->value;

        if (isset($batasTingkat[$tingkatKey]) && is_array($batasTingkat[$tingkatKey])) {
            if ($tingkatKey === 'Dasar') return 0;
            if ($tingkatKey === 'Menengah') return count($batasTingkat['Dasar'] ?? []);
            if ($tingkatKey === 'Atas') return count($batasTingkat['Dasar'] ?? []) + count($batasTingkat['Menengah'] ?? []);
        }
        return 0;
    }

    public function getTotalJuzTuntas(): int
    {
        if (!$this->usulan || !$this->usulan->siswa) return 0;
        $progres = $this->usulan->siswa->progresHafalans()->first();
        return $progres ? ($progres->total_juz_tuntas ?? 0) : 0;
    }

    public function hasProgresData(): bool
    {
        return $this->usulan && $this->usulan->siswa && $this->usulan->siswa->progresHafalans()->exists();
    }

    public function getProgresDataInfo(): string
    {
        if (!$this->usulan || !$this->usulan->siswa) return '';
        $progres = $this->usulan->siswa->progresHafalans()->first();
        if ($progres && $progres->updated_at) {
            return "Data hafalan akumulatif (terakhir diperbarui: " . $progres->updated_at->format('d M Y') . ")";
        }
        return "Data hafalan akumulatif";
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.usulan-kenaikan-tingkat.detail-usulan', [
            'requiredThreshold' => $this->getRequiredThreshold(),
            'totalJuzTuntas' => $this->getTotalJuzTuntas(),
            'hasProgresData' => $this->hasProgresData(),
            'progresDataInfo' => $this->getProgresDataInfo(),
        ]);
    }
}
