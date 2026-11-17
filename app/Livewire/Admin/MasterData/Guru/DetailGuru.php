<?php

namespace App\Livewire\Admin\MasterData\Guru;

use App\Models\Guru;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('admin.layouts.app')]
class DetailGuru extends Component
{
    // ─── Properties ────────────────────────────────────────────────────────────

    public Guru $guru;

    // ─── Lifecycle ─────────────────────────────────────────────────────────────

    /**
     * Mount component with guru data
     *
     * @param int $id Guru ID
     */
    public function mount(int $id): void
    {
        // Load guru with relationships
        $this->guru = Guru::with(['tugasTambahans', 'kelompoksBimbingan.tahunAjaran'])
            ->findOrFail($id);
    }

    // ─── Helper Methods ────────────────────────────────────────────────────────

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor(bool $statusAktif): string
    {
        return $statusAktif ? 'zamrud' : 'delima';
    }

    /**
     * Get status text
     */
    public function getStatusText(bool $statusAktif): string
    {
        return $statusAktif ? 'Aktif' : 'Tidak Aktif';
    }

    // ─── Actions ───────────────────────────────────────────────────────────────

    /**
     * Navigate to edit page
     */
    public function edit()
    {
        return redirect()->route('admin.master-data.guru.edit', $this->guru->id);
    }

    /**
     * Navigate back to index
     */
    public function kembali()
    {
        return redirect()->route('admin.master-data.guru.index');
    }

    // ─── Render ────────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.master-data.guru.detail-guru');
    }
}
