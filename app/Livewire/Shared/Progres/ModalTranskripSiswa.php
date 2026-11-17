<?php

namespace App\Livewire\Shared\Progres;

use Livewire\Component;
use App\Models\Siswa;
use App\Services\ProgresHafalanService;
use App\Services\TranskripService;

/**
 * ModalTranskripSiswa
 *
 * Komponen modal shared untuk menampilkan transkrip capaian siswa (snapshot).
 * Digunakan oleh Guru dan Admin.
 *
 * Requirements: 5.2, 6.1
 */
class ModalTranskripSiswa extends Component
{
    /** @var bool State untuk menampilkan modal */
    public bool $isOpen = false;

    /** @var int|null ID siswa yang sedang dilihat */
    public ?int $siswaId = null;

    /** @var array Data transkrip terbaru */
    public array $transkrip = [];

    /** @var Siswa|null Data siswa */
    public ?Siswa $siswa = null;

    protected TranskripService $transkripService;

    public function boot(TranskripService $transkripService): void
    {
        $this->transkripService = $transkripService;
    }

    /**
     * Listener untuk event open modal
     */
    protected $listeners = [
        'openModalTranskrip' => 'loadAndShow'
    ];

    /**
     * Load data and show modal
     */
    public function loadAndShow(int $siswaId): void
    {
        $this->siswaId = $siswaId;
        $this->siswa = Siswa::with('kelompok')->findOrFail($siswaId);
        
        // Load latest transcript
        $latest = $this->transkripService->getLatestTranscript($siswaId);
        
        if ($latest) {
            $this->transkrip = [
                'tanggal_generate' => $latest->tanggal_generate,
                'reason' => $latest->reason,
                'target_kelulusan' => $latest->target_kelulusan_at_snapshot,
                'detail_capaian' => $latest->detail_capaian_snapshot,
            ];
        } else {
            $this->transkrip = [];
        }

        $this->isOpen = true;
    }

    /**
     * Close modal
     */
    public function close(): void
    {
        $this->isOpen = false;
        $this->siswaId = null;
        $this->siswa = null;
        $this->transkrip = [];
    }

    public function render()
    {
        return view('livewire.shared.progres.modal-transkrip-siswa');
    }
}
