<?php

namespace App\Livewire\Admin\MasterData\Kelompok;

use App\Models\Kelompok;
use App\Services\MasterData\KelompokService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('admin.layouts.app')]
class DetailKelompok extends Component
{
    // ─── Properties ────────────────────────────────────────────────────────────

    public Kelompok $kelompok;
    public $riwayatGuruPembimbing = [];
    public $jumlahSiswa = 0;
    public $statusKapasitas = '';

    // ─── Lifecycle ─────────────────────────────────────────────────────────────

    /**
     * Mount component with kelompok data
     *
     * @param int $id Kelompok ID
     */
    public function mount(int $id, KelompokService $kelompokService): void
    {
        // Load kelompok with relationships
        $this->kelompok = Kelompok::with([
            'guruPembimbing',
            'tahunAjaran',
            'siswas' => function ($query) {
                $query->where('status_penempatan', 'Ditempatkan')
                    ->orderBy('nama');
            }
        ])->findOrFail($id);

        // Get riwayat guru pembimbing
        $this->riwayatGuruPembimbing = $kelompokService->getRiwayatGuruPembimbing($id);

        // Get member count and capacity status
        $this->jumlahSiswa = $kelompokService->getMemberCount($id);
        $this->statusKapasitas = $kelompokService->getCapacityStatus($id);
    }

    // ─── Helper Methods ────────────────────────────────────────────────────────

    /**
     * Get status kapasitas badge color
     */
    public function getStatusKapasitasColor(): string
    {
        return match ($this->statusKapasitas) {
            'Tersedia' => 'zamrud',
            'Penuh' => 'delima',
            default => 'slate',
        };
    }

    /**
     * Get jenis badge color
     */
    public function getJenisBadgeColor(): string
    {
        return match ($this->kelompok->jenis) {
            'Banin' => 'slate',
            'Banat' => 'slate',
            default => 'slate',
        };
    }

    /**
     * Get tingkat badge color
     */
    public function getTingkatBadgeColor(): string
    {
        return match ($this->kelompok->tingkat) {
            'Dasar' => 'zamrud',
            'Menengah' => 'emas',
            'Atas' => 'slate',
            default => 'slate',
        };
    }

    /**
     * Format date for display
     */
    public function formatTanggal($date): string
    {
        if (!$date) {
            return '-';
        }

        return \Carbon\Carbon::parse($date)->format('d M Y');
    }

    // ─── Actions ───────────────────────────────────────────────────────────────

    /**
     * Navigate to edit page
     */
    public function edit()
    {
        return redirect()->route('admin.master-data.kelompok.edit', $this->kelompok->id);
    }

    /**
     * Navigate back to index
     */
    public function kembali()
    {
        return redirect()->route('admin.master-data.kelompok.index');
    }

    // ─── Render ────────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.master-data.kelompok.detail-kelompok');
    }
}
