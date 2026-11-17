<?php

namespace App\Livewire\Admin\MasterData\Siswa;

use App\Models\Siswa;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
class DetailSiswa extends Component
{
    use WithPagination;

    // ─── Properties ────────────────────────────────────────────────────────────

    public Siswa $siswa;
    public string $activeTab = 'data'; // data, riwayat_kelompok, riwayat_absensi


    // ─── Lifecycle ─────────────────────────────────────────────────────────────

    /**
     * Mount component with siswa data
     *
     * @param int $id Siswa ID
     */
    public function mount(int $id): void
    {
        // Load siswa with relationships
        $this->siswa = Siswa::with([
            'kelompok.guruPembimbing',
            'kelas',
            'riwayatKelompoks.kelompokDari',
            'riwayatKelompoks.kelompokKe',
            'riwayatKelompoks.disetujuiOleh'
        ])->findOrFail($id);
    }

    // ─── Tab Navigation ────────────────────────────────────────────────────────

    /**
     * Switch active tab
     */
    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage(); // Reset pagination when switching tabs
    }

    /**
     * Get paginated riwayat absensi
     */
    public function getRiwayatAbsensiProperty()
    {
        return \App\Models\Absensi::with(['kelompok', 'guruPengisi'])
            ->where('siswa_id', $this->siswa->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(15);
    }

    // ─── Helper Methods ────────────────────────────────────────────────────────

    /**
     * Get status badge color based on status value
     */
    public function getStatusBadgeColor(string $type, string $value): string
    {
        $colorMap = [
            'status_akademik' => [
                'Aktif' => 'zamrud',
                'Lulus SMP' => 'emas',
                'Pindah Sekolah' => 'slate',
                'Keluar' => 'delima',
            ],
            'status_tahfidz' => [
                'Sedang Berjalan' => 'zamrud',
                'Lulus Program Tahfidz' => 'emas',
            ],
            'status_penempatan' => [
                'Belum Ditempatkan' => 'slate',
                'Ditempatkan' => 'zamrud',
            ],
        ];

        return $colorMap[$type][$value] ?? 'slate';
    }

    // ─── Actions ───────────────────────────────────────────────────────────────

    /**
     * Navigate back to index
     */
    public function kembali()
    {
        return redirect()->route('admin.master-data.siswa.index');
    }

    // ─── Render ────────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.master-data.siswa.detail-siswa');
    }
}
