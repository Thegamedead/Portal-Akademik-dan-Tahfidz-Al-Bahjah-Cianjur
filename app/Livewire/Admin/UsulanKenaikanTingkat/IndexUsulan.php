<?php

namespace App\Livewire\Admin\UsulanKenaikanTingkat;

use App\Models\UsulanKenaikanTingkat;
use App\Models\Kelompok;
use App\Enums\StatusUsulan;
use App\Enums\TingkatKelompok;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

/**
 * IndexUsulan Component (Admin Namespace)
 *
 * Display table of all promotion proposals with filtering capabilities
 * for admin review and management.
 *
 * Task 13.1: Implement IndexUsulan component (Admin namespace)
 * Requirements: 3.1, 3.2, 3.3, 3.4, 3.6
 */
#[Layout('admin.layouts.app')]
#[Title('Review Usulan Kenaikan Tingkat')]
class IndexUsulan extends Component
{
    use WithPagination;

    // ─── Properties ──────────────────────────────────────────────────────────

    // Filter properties (URL query parameters)
    public ?string $filterStatus = null;
    public ?string $filterTingkatAsal = null;
    public ?string $filterTingkatTujuan = null;
    public ?int $filterKelompokAsal = null;

    // Search property
    public string $search = '';

    // Bulk action properties
    public array $selectedUsulans = [];
    public bool $selectAll = false;

    // Cancel approval modal properties
    public bool $showCancelModal = false;
    public ?int $usulanIdToCancel = null;
    public string $alasanPembatalan = '';
    public string $cancelType = ''; // 'approval' or 'placement'

    // Delete confirmation modal
    public bool $showDeleteModal = false;

    // Query string configuration for URL params
    protected $queryString = [
        'filterStatus' => ['except' => null],
        'filterTingkatAsal' => ['except' => null],
        'filterTingkatTujuan' => ['except' => null],
        'filterKelompokAsal' => ['except' => null],
        'search' => ['except' => ''],
    ];

    // Pagination theme
    protected $paginationTheme = 'tailwind';

    // ─── Listeners ───────────────────────────────────────────────────────────

    protected $listeners = [
        'usulan-processed' => '$refresh',
        'usulan-placed' => '$refresh',
    ];

    // ─── Data Loading ────────────────────────────────────────────────────────

    /**
     * Get paginated proposals with filters
     *
     * Validates: Requirements 3.1, 3.2, 3.3, 3.4, 3.6
     */
    public function getProposalsProperty()
    {
        $query = UsulanKenaikanTingkat::query()
            ->with([
                'siswa:id,nama,nis,kelas_id',
                'siswa.kelas:id,nama_kelas',
                'guruPengusul:id,nama',
                'kelompokAsal:id,nama',
                'kelompokTujuan:id,nama',
            ]);

        // Apply search filter (nama siswa or NIS)
        if ($this->search) {
            $query->whereHas('siswa', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter (Req 3.2)
        if ($this->filterStatus) {
            $statusEnum = match ($this->filterStatus) {
                'diajukan' => StatusUsulan::DIAJUKAN,
                'disetujui' => StatusUsulan::DISETUJUI,
                'ditempatkan' => StatusUsulan::DITEMPATKAN,
                'ditolak' => StatusUsulan::DITOLAK,
                default => null,
            };

            if ($statusEnum) {
                $query->where('status_usulan', $statusEnum);
            }
        }

        // Apply tingkat_asal filter (Req 3.3)
        if ($this->filterTingkatAsal) {
            $tingkatAsalEnum = TingkatKelompok::tryFrom($this->filterTingkatAsal);
            if ($tingkatAsalEnum) {
                $query->where('tingkat_asal', $tingkatAsalEnum);
            }
        }

        // Apply tingkat_tujuan filter (Req 3.3)
        if ($this->filterTingkatTujuan) {
            $tingkatTujuanEnum = TingkatKelompok::tryFrom($this->filterTingkatTujuan);
            if ($tingkatTujuanEnum) {
                $query->where('tingkat_tujuan', $tingkatTujuanEnum);
            }
        }

        // Apply kelompok_asal filter (Req 3.4)
        if ($this->filterKelompokAsal) {
            $query->where('kelompok_asal_id', $this->filterKelompokAsal);
        }

        return $query->orderBy('tanggal_usul', 'desc')
            ->paginate(15);
    }

    /**
     * Get all kelompok options for filter dropdown
     *
     * Validates: Requirement 3.4
     */
    public function getKelompoksProperty()
    {
        return Kelompok::query()
            ->orderBy('nama')
            ->get(['id', 'nama', 'tingkat']);
    }

    /**
     * Get aggregate stats for the summary cards.
     */
    public function getStats(): array
    {
        $total = UsulanKenaikanTingkat::count();
        $diajukan = UsulanKenaikanTingkat::where('status_usulan', StatusUsulan::DIAJUKAN)->count();
        $disetujui = UsulanKenaikanTingkat::where('status_usulan', StatusUsulan::DISETUJUI)->count();
        $ditempatkan = UsulanKenaikanTingkat::where('status_usulan', StatusUsulan::DITEMPATKAN)->count();
        $ditolak = UsulanKenaikanTingkat::where('status_usulan', StatusUsulan::DITOLAK)->count();

        return compact('total', 'diajukan', 'disetujui', 'ditempatkan', 'ditolak');
    }

    // ─── Filter Actions ──────────────────────────────────────────────────────

    /**
     * Reset all filters
     */
    public function resetFilters(): void
    {
        $this->filterStatus = null;
        $this->filterTingkatAsal = null;
        $this->filterTingkatTujuan = null;
        $this->filterKelompokAsal = null;
        $this->search = '';
        $this->resetPage();
    }

    /**
     * Watch for filter changes and reset pagination
     */
    public function updated($propertyName): void
    {
        if (str_starts_with($propertyName, 'filter') || $propertyName === 'search') {
            $this->resetPage();
        }

        // Watch for selectAll changes
        if ($propertyName === 'selectAll') {
            $this->selectedUsulans = $this->selectAll
                ? $this->proposals->pluck('id')->map(fn($id) => (string) $id)->toArray()
                : [];
        }
    }

    // ─── Bulk Actions ────────────────────────────────────────────────────────

    /**
     * Toggle select all usulans
     */
    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedUsulans = $this->proposals->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedUsulans = [];
        }
    }

    /**
     * Get count of selected usulans
     */
    public function getSelectedCountProperty(): int
    {
        return count($this->selectedUsulans);
    }

    /**
     * Open delete confirmation modal
     */
    public function openDeleteModal(): void
    {
        if (empty($this->selectedUsulans)) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih minimal 1 usulan untuk dihapus.');
            return;
        }

        $this->showDeleteModal = true;
    }

    /**
     * Close delete confirmation modal
     */
    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
    }

    /**
     * Execute bulk delete
     */
    public function bulkDelete(): void
    {
        if (empty($this->selectedUsulans)) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih minimal 1 usulan untuk dihapus.');
            return;
        }

        try {
            // Only allow delete for "Ditolak" status
            $usulans = UsulanKenaikanTingkat::whereIn('id', $this->selectedUsulans)->get();

            $cannotDelete = $usulans->filter(function ($usulan) {
                return $usulan->status_usulan !== StatusUsulan::DITOLAK;
            });

            if ($cannotDelete->isNotEmpty()) {
                $this->dispatch('toast', variant: 'error', message: 'Hanya usulan dengan status "Ditolak" yang dapat dihapus.');
                return;
            }

            // Soft delete
            UsulanKenaikanTingkat::whereIn('id', $this->selectedUsulans)->delete();

            // Log activity
            foreach ($usulans as $usulan) {
                activity()
                    ->performedOn($usulan)
                    ->withProperties([
                        'siswa_nama' => $usulan->siswa->nama ?? null,
                        'status_usulan' => $usulan->status_usulan->value,
                        'causer_type' => 'Admin',
                        'causer_id' => auth()->guard('admin')->id(),
                    ])
                    ->log('Usulan kenaikan tingkat dihapus (bulk delete)');
            }

            $this->dispatch('toast', variant: 'sukses', message: count($this->selectedUsulans) . ' usulan berhasil dihapus.');

            $this->selectedUsulans = [];
            $this->selectAll = false;
            $this->showDeleteModal = false;
            $this->resetPage();

        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ─── Helper Methods ──────────────────────────────────────────────────────

    /**
     * Get badge color based on status
     *
     * Validates: UI design system color coding
     *
     * @param StatusUsulan $status
     * @return array ['bg' => string, 'text' => string]
     */
    public function getStatusBadgeColor(StatusUsulan $status): array
    {
        return match ($status) {
            StatusUsulan::DIAJUKAN => [
                'bg' => '#5B7A8C',      // Biru Slate (neutral)
                'text' => '#FFFFFF',
            ],
            StatusUsulan::DISETUJUI => [
                'bg' => '#1B5E4F',      // Zamrud (approved)
                'text' => '#FFFFFF',
            ],
            StatusUsulan::DITEMPATKAN => [
                'bg' => '#1B5E4F',      // Zamrud (completed)
                'text' => '#FFFFFF',
            ],
            StatusUsulan::DITOLAK => [
                'bg' => '#9B3A3A',      // Delima (rejected)
                'text' => '#FFFFFF',
            ],
        };
    }

    /**
     * Determine action button for each status
     *
     * Validates: Requirement 3.6
     *
     * @param UsulanKenaikanTingkat $usulan
     * @return array ['label' => string, 'action' => string, 'color' => string, 'secondary' => array|null]
     */
    public function getActionButton(UsulanKenaikanTingkat $usulan): array
    {
        return match ($usulan->status_usulan) {
            StatusUsulan::DIAJUKAN => [
                'label' => 'Review',
                'action' => 'review',
                'color' => 'zamrud',  // Zamrud for primary action
                'secondary' => null,
            ],
            StatusUsulan::DISETUJUI => [
                'label' => 'Tempatkan',
                'action' => 'place',
                'color' => 'zamrud',  // Zamrud for primary action
                'secondary' => [
                    'label' => 'Batalkan',
                    'action' => 'cancel',
                    'type' => 'approval',  // Cancel approval
                    'color' => 'delima',  // Delima for cancel action
                ],
            ],
            StatusUsulan::DITEMPATKAN => [
                'label' => 'Lihat Detail',
                'action' => 'view',
                'color' => 'slate',   // Biru Slate for neutral action
                'secondary' => [
                    'label' => 'Batalkan Penempatan',
                    'action' => 'cancel',
                    'type' => 'placement',  // Cancel placement
                    'color' => 'delima',  // Delima for cancel action
                ],
            ],
            StatusUsulan::DITOLAK => [
                'label' => 'Lihat Detail',
                'action' => 'view',
                'color' => 'slate',   // Biru Slate for neutral action
                'secondary' => null,
            ],
        };
    }

    // ─── Navigation Actions ──────────────────────────────────────────────────

    /**
     * Navigate to review modal
     */
    public function openReview(int $usulanId): void
    {
        $this->dispatch('open-review-modal', usulanId: $usulanId);
    }

    /**
     * Navigate to placement modal
     */
    public function openPlacement(int $usulanId): void
    {
        $this->dispatch('open-placement-modal', usulanId: $usulanId);
    }

    /**
     * Navigate to detail view
     */
    public function viewDetail(int $usulanId): void
    {
        $this->dispatch('open-detail-modal', usulanId: $usulanId);
    }

    // ─── Cancel Approval Actions ─────────────────────────────────────────────

    /**
     * Open cancel approval confirmation modal
     */
    public function openCancelModal(int $usulanId, string $type = 'approval'): void
    {
        $this->usulanIdToCancel = $usulanId;
        $this->cancelType = $type; // 'approval' or 'placement'
        $this->alasanPembatalan = '';
        $this->showCancelModal = true;
        $this->resetErrorBag();
    }

    /**
     * Close cancel approval modal
     */
    public function closeCancelModal(): void
    {
        $this->showCancelModal = false;
        $this->usulanIdToCancel = null;
        $this->cancelType = '';
        $this->alasanPembatalan = '';
        $this->resetErrorBag();
    }

    /**
     * Execute cancel (either approval or placement based on cancelType)
     */
    public function cancelAction(): void
    {
        if ($this->cancelType === 'placement') {
            $this->cancelPlacement();
        } else {
            $this->cancelApproval();
        }
    }

    /**
     * Execute cancel approval
     */
    public function cancelApproval(): void
    {
        // Validate alasan pembatalan
        $this->validate([
            'alasanPembatalan' => 'required|string|min:10',
        ], [
            'alasanPembatalan.required' => 'Alasan pembatalan wajib diisi.',
            'alasanPembatalan.min' => 'Alasan pembatalan minimal 10 karakter.',
        ]);

        try {
            $adminId = auth()->guard('admin')->id();

            $service = app(\App\Services\Contracts\UsulanKenaikanTingkatServiceInterface::class);
            $service->cancelApproval(
                $this->usulanIdToCancel,
                $adminId,
                $this->alasanPembatalan
            );

            $this->dispatch('toast', variant: 'sukses', message: 'Persetujuan usulan berhasil dibatalkan.');
            $this->closeCancelModal();
            $this->resetPage();

        } catch (\App\Exceptions\InvalidStatusTransitionException $e) {
            $this->addError('alasanPembatalan', $e->getMessage());
        } catch (\Exception $e) {
            $this->addError('alasanPembatalan', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Execute cancel placement (rollback penempatan)
     */
    public function cancelPlacement(): void
    {
        // Validate alasan pembatalan
        $this->validate([
            'alasanPembatalan' => 'required|string|min:10',
        ], [
            'alasanPembatalan.required' => 'Alasan pembatalan wajib diisi.',
            'alasanPembatalan.min' => 'Alasan pembatalan minimal 10 karakter.',
        ]);

        try {
            $adminId = auth()->guard('admin')->id();

            $service = app(\App\Services\Contracts\UsulanKenaikanTingkatServiceInterface::class);
            $service->cancelPlacement(
                $this->usulanIdToCancel,
                $adminId,
                $this->alasanPembatalan
            );

            $this->dispatch('toast', variant: 'sukses', message: 'Pembatalan berhasil diproses. Siswa dikembalikan ke kelompok asal dan usulan kembali ke status Diajukan.');
            $this->closeCancelModal();
            $this->resetPage();

        } catch (\App\Exceptions\InvalidStatusTransitionException $e) {
            $this->addError('alasanPembatalan', $e->getMessage());
        } catch (\Exception $e) {
            $this->addError('alasanPembatalan', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ─── Render ──────────────────────────────────────────────────────────────

    /**
     * Render the component
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.usulan-kenaikan-tingkat.index-usulan', [
            'proposals' => $this->proposals,
            'kelompoks' => $this->kelompoks,
            'statusOptions' => StatusUsulan::options(),
            'tingkatOptions' => TingkatKelompok::options(),
        ]);
    }
}
