<?php

namespace App\Livewire\Admin\MasterData\Kelompok;

use App\Models\Kelompok;
use App\Models\TahunAjaran;
use App\Models\Guru;
use App\Services\MasterData\KelompokService;
use App\Enums\TingkatKelompok;
use App\Enums\JenisKelompok;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Rules\ValidGuruPembimbingTahfidz;
use Illuminate\Validation\ValidationException;

#[Layout('admin.layouts.app')]
class IndexKelompok extends Component
{
    use WithPagination;

    // ─── Properties ──────────────────────────────────────────────────────────

    public string $search = '';
    public string $filterTingkat = '';
    public string $filterJenis = '';
    public string $filterTahunAjaran = '';
    public string $filterGuruPembimbing = '';
    public bool $showFilters = false;

    // Bulk selection
    public array $selectedIds = [];
    public bool $selectAll = false;

    public bool $showBulkDeleteConfirm = false;

    // ─── Modal Properties ────────────────────────────────────────────────────
    public bool $showFormModal = false;
    public bool $showDetailModal = false;
    public bool $isEdit = false;
    public ?Kelompok $selectedKelompok = null;
    public bool $showConfirmationDialog = false;

    // ─── Form Properties ─────────────────────────────────────────────────────
    public $nama = '';
    public $jenis = '';
    public $tingkat = '';
    public $guru_pembimbing_id = '';
    public $tanggal_penempatan_guru = '';
    public $originalGuruPembimbingId;
    public $alasanPerubahanGuru = '';

    // ─── Detail Properties ───────────────────────────────────────────────────
    public $riwayatGuruPembimbing = [];
    public $jumlahSiswaDetail = 0;
    public $statusKapasitasDetail = '';

    // ─── Anggota Kelompok Properties ──────────────────────────────────────────────────
    public bool $showAnggotaModal = false;
    public ?Kelompok $kelompokAnggota = null;
    public bool $showKonfirmasiKeluarkan = false;
    public string $searchAnggota = '';
    public array $selectedAnggotaIds = [];
    public bool $selectAllAnggota = false;
    public array $riwayatAnggota = []; // [siswa_id => ['tanggal_masuk'=>..., 'tanggal_keluar'=>...]]

    // ─── Tempatkan Siswa Properties ──────────────────────────────────────────────
    public bool $showTempatkanSiswaModal = false;
    public ?Kelompok $kelompokTujuan = null;
    public string $tanggalPenempatanSiswa = '';
    public array $selectedSiswaIds = [];
    public bool $selectAllSiswa = false;
    public string $searchSiswaTempatkan = '';
    public string $filterStatusSiswa = 'belum_ditempatkan'; // belum_ditempatkan, semua

    // ─── Pengaturan Kelompok Properties ───────────────────────────────────────────────
    public bool $showPengaturanModal = false;
    public bool $showKomponenPenilaianModal = false;
    public int $kapasitas_kelompok_min = 6;
    public int $kapasitas_kelompok_max = 12;
    public $komponen_penilaian = [];  // loose type: Livewire sends false when all checkboxes cleared

    // ─── Target Hafalan Properties ────────────────────────────────────────────────────
    public bool $showTargetHafalanModal = false;
    public array $target_dasar = [];
    public array $target_menengah = [];
    public array $target_atas = [];
    public string $selected_juz_dasar = '';
    public string $selected_juz_menengah = '';
    public string $selected_juz_atas = '';

    /**
     * Normalize komponen_penilaian: Livewire sends false when all checkboxes are unchecked.
     */
    public function updatedKomponenPenilaian($value): void
    {
        if (!is_array($this->komponen_penilaian)) {
            $this->komponen_penilaian = [];
        }
    }

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    /**
     * Reset pagination and selection when search changes
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    /**
     * Reset pagination when filters change
     */
    public function updatingFilterTingkat(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingFilterJenis(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingFilterTahunAjaran(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingFilterGuruPembimbing(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    /**
     * Handle select all checkbox
     */
    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedIds = Kelompok::query()
                ->when($this->search, fn($q) =>
                    $q->where('nama', 'like', "%{$this->search}%")
                      ->orWhereHas('guruPembimbing', fn($q) =>
                        $q->where('nama', 'like', "%{$this->search}%")
                      )
                )
                ->when($this->filterTingkat, fn($q) =>
                    $q->where('tingkat', $this->filterTingkat)
                )
                ->when($this->filterJenis, fn($q) =>
                    $q->where('jenis', $this->filterJenis)
                )
                ->when($this->filterTahunAjaran, fn($q) =>
                    $q->where('tahun_ajaran_id', $this->filterTahunAjaran)
                )
                ->when($this->filterGuruPembimbing, fn($q) =>
                    $q->where('guru_pembimbing_id', $this->filterGuruPembimbing)
                )
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    /**
     * Update selectAll based on selectedIds
     */
    public function updatedSelectedIds(): void
    {
        $totalFiltered = Kelompok::query()
            ->when($this->search, fn($q) =>
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhereHas('guruPembimbing', fn($q) =>
                    $q->where('nama', 'like', "%{$this->search}%")
                  )
            )
            ->when($this->filterTingkat, fn($q) =>
                $q->where('tingkat', $this->filterTingkat)
            )
            ->when($this->filterJenis, fn($q) =>
                $q->where('jenis', $this->filterJenis)
            )
            ->when($this->filterTahunAjaran, fn($q) =>
                $q->where('tahun_ajaran_id', $this->filterTahunAjaran)
            )
            ->when($this->filterGuruPembimbing, fn($q) =>
                $q->where('guru_pembimbing_id', $this->filterGuruPembimbing)
            )
            ->count();

        $this->selectAll = count($this->selectedIds) === $totalFiltered && $totalFiltered > 0;
    }

    /**
     * Reset guru pembimbing when jenis changes
     */
    public function updatedJenis(): void
    {
        $this->guru_pembimbing_id = '';
    }

    /**
     * Listener untuk event refresh dari modal atau komponen lain
     */
    protected $listeners = ['kelompokSaved' => '$refresh'];

    // ─── Validation Rules ──────────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'nama' => 'required|string|max:100',
            'jenis' => ['required', JenisKelompok::validationRule()],
            'tingkat' => ['required', TingkatKelompok::validationRule()],
            'guru_pembimbing_id' => ['required', new ValidGuruPembimbingTahfidz],
            'tanggal_penempatan_guru' => 'required|date',
        ];
    }

    protected function messages(): array
    {
        return [
            'nama.required' => 'Nama kelompok wajib diisi',
            'nama.max' => 'Nama kelompok maksimal 100 karakter',
            'jenis.required' => 'Jenis kelompok wajib dipilih',
            'jenis.in' => 'Jenis kelompok tidak valid',
            'tingkat.required' => 'Tingkat kelompok wajib dipilih',
            'tingkat.in' => 'Tingkat kelompok tidak valid',
            'guru_pembimbing_id.required' => 'Guru pembimbing wajib dipilih',
            'tanggal_penempatan_guru.required' => 'Tanggal penempatan wajib diisi',
            'tanggal_penempatan_guru.date' => 'Format tanggal tidak valid',
        ];
    }

    // ─── Modal Actions ────────────────────────────────────────────────────────

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showFormModal = true;

        // Dispatch browser event to reinitialize flatpickr
        $this->dispatch('reinit-flatpickr');
    }

    public function openEditModal(int $id): void
    {
        $this->resetForm();
        $this->isEdit = true;

        $kelompok = Kelompok::findOrFail($id);
        $this->selectedKelompok = $kelompok;

        $this->nama = $kelompok->nama;
        $this->jenis = $kelompok->jenis;
        $this->tingkat = $kelompok->tingkat;
        $this->guru_pembimbing_id = $kelompok->guru_pembimbing_id;
        $this->tanggal_penempatan_guru = $kelompok->tanggal_penempatan_guru?->format('Y-m-d') ?? '';
        $this->originalGuruPembimbingId = $kelompok->guru_pembimbing_id;

        $this->showFormModal = true;

        // Dispatch browser event to reinitialize flatpickr
        $this->dispatch('reinit-flatpickr');
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->showConfirmationDialog = false;
        $this->resetForm();
    }

    public function openDetailModal(int $id, KelompokService $kelompokService): void
    {
        $this->selectedKelompok = Kelompok::with([
            'guruPembimbing',
            'tahunAjaran',
            'siswas' => function ($query) {
                $query->where('status_penempatan', 'Ditempatkan')
                    ->orderBy('nama');
            }
        ])->findOrFail($id);

        $this->riwayatGuruPembimbing = $kelompokService->getRiwayatGuruPembimbing($id);
        $this->jumlahSiswaDetail = $kelompokService->getMemberCount($id);
        $this->statusKapasitasDetail = $kelompokService->getCapacityStatus($id);

        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedKelompok = null;
    }

    private function resetForm(): void
    {
        $this->reset([
            'nama', 'jenis', 'tingkat', 'guru_pembimbing_id', 'tanggal_penempatan_guru',
            'originalGuruPembimbingId', 'alasanPerubahanGuru', 'showConfirmationDialog'
        ]);
        $this->resetErrorBag();
        $this->resetValidation();
        $this->selectedKelompok = null;
    }

    #[Computed]
    public function formGuruPembimbings()
    {
        return Guru::where('jabatan', 'Guru Pembimbing Tahfidz')
            ->where('status_aktif', true)
            ->when($this->jenis === 'Banin', fn($q) => $q->where('jenis_kelamin', 'Laki-laki'))
            ->when($this->jenis === 'Banat', fn($q) => $q->where('jenis_kelamin', 'Perempuan'))
            ->with(['kelompoksBimbingan' => function($q) {
                // Jika edit, exclude kelompok yang sedang diedit
                if ($this->isEdit && $this->selectedKelompok) {
                    $q->where('id', '!=', $this->selectedKelompok->id);
                }
            }])
            ->orderBy('nama')
            ->get();
    }

    // ─── Save Methods ─────────────────────────────────────────────────────────

    public function saveForm()
    {
        $this->validate();

        if ($this->isEdit && $this->guru_pembimbing_id != $this->originalGuruPembimbingId) {
            $this->showConfirmationDialog = true;
        } else {
            $this->performSave();
        }
    }

    public function cancelConfirmation()
    {
        $this->showConfirmationDialog = false;
        $this->alasanPerubahanGuru = '';
    }

    public function confirmUpdate()
    {
        if (empty($this->alasanPerubahanGuru)) {
            $this->dispatch('toast', variant: 'error', message: 'Alasan perubahan guru pembimbing wajib diisi');
            return;
        }

        $this->showConfirmationDialog = false;
        $this->performSave($this->alasanPerubahanGuru);
    }

    private function performSave(?string $alasanPerubahanGuru = null)
    {
        try {
            $kelompokData = [
                'nama' => $this->nama,
                'jenis' => $this->jenis,
                'tingkat' => $this->tingkat,
                'guru_pembimbing_id' => $this->guru_pembimbing_id,
                'tanggal_penempatan_guru' => $this->tanggal_penempatan_guru,
            ];

            $kelompokService = app(KelompokService::class);

            if ($this->isEdit) {
                $kelompokService->update($this->selectedKelompok->id, $kelompokData, $alasanPerubahanGuru);
                $this->dispatch('toast', variant: 'sukses', message: 'Kelompok berhasil diperbarui');
            } else {
                $kelompokService->create($kelompokData);
                $this->dispatch('toast', variant: 'sukses', message: 'Kelompok berhasil ditambahkan');
            }

            $this->closeFormModal();
            $this->resetPage();
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
            $this->dispatch('toast', variant: 'error', message: 'Validasi gagal. Periksa kembali data yang diisi.');
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ─── Filter Methods ───────────────────────────────────────────────────────

    /**
     * Reset semua filter
     */
    public function resetFilters(): void
    {
        $this->reset([
            'filterTingkat',
            'filterJenis',
            'filterTahunAjaran',
            'filterGuruPembimbing'
        ]);
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    // ─── Bulk Actions ─────────────────────────────────────────────────────────

    /**
     * Open bulk delete confirmation modal
     */
    public function openBulkDeleteConfirm(): void
    {
        if (count($this->selectedIds) === 0) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih minimal 1 kelompok untuk dihapus');
            return;
        }
        $this->showBulkDeleteConfirm = true;
    }

    /**
     * Close bulk delete confirmation modal
     */
    public function closeBulkDeleteConfirm(): void
    {
        $this->showBulkDeleteConfirm = false;
    }

    /**
     * Process bulk delete
     */
    public function bulkDelete(KelompokService $kelompokService): void
    {
        try {
            $count = count($this->selectedIds);

            foreach ($this->selectedIds as $id) {
                $kelompokService->delete($id);
            }

            $this->selectedIds = [];
            $this->selectAll = false;
            $this->showBulkDeleteConfirm = false;

            $this->dispatch('toast', variant: 'sukses', message: "{$count} kelompok berhasil dihapus");
            $this->resetPage();
        } catch (\Exception $e) {
            $this->showBulkDeleteConfirm = false;
            $this->dispatch('toast', variant: 'error', message: 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    // ─── Helper Methods ───────────────────────────────────────────────────────

    /**
     * Get tingkat badge color based on value
     */
    public function getTingkatBadgeColor(string $tingkat): string
    {
        return match($tingkat) {
            'Dasar' => 'slate',
            'Menengah' => 'zamrud',
            'Atas' => 'emas',
            default => 'slate',
        };
    }

    /**
     * Get jenis badge color based on value
     */
    public function getJenisBadgeColor(string $jenis): string
    {
        return match($jenis) {
            'Banin' => 'zamrud',
            'Banat' => 'emas',
            default => 'slate',
        };
    }

    /**
     * Get status kapasitas badge color
     */
    public function getStatusKapasitasBadgeColor(string $statusKapasitas): string
    {
        return match($statusKapasitas) {
            'Tersedia' => 'zamrud',
            'Penuh' => 'delima',
            default => 'slate',
        };
    }

    // ─── Anggota Kelompok Methods ─────────────────────────────────────────────

    public function bukaAnggotaModal(int $kelompokId): void
    {
        $this->kelompokAnggota = Kelompok::with(['guruPembimbing', 'siswas.kelas'])->findOrFail($kelompokId);

        // Ambil tanggal masuk dari riwayat_kelompoks (hanya record masuk ke kelompok ini)
        $this->riwayatAnggota = \App\Models\RiwayatKelompok::where('kelompok_ke_id', $kelompokId)
            ->orderBy('tanggal', 'asc')
            ->get()
            ->groupBy('siswa_id')
            ->map(function ($history) use ($kelompokId) {
                $masukRecord = $history->where('kelompok_ke_id', $kelompokId)->last();
                return [
                    'tanggal_masuk' => $masukRecord ? \Carbon\Carbon::parse($masukRecord->tanggal)->format('d/m/Y') : null,
                ];
            })
            ->toArray();

        $this->searchAnggota = '';
        $this->showAnggotaModal = true;
    }

    public function tutupAnggotaModal(): void
    {
        $this->showAnggotaModal = false;
        $this->kelompokAnggota = null;
        $this->riwayatAnggota = [];
        $this->showKonfirmasiKeluarkan = false;
        $this->searchAnggota = '';
        $this->selectedAnggotaIds = [];
        $this->selectAllAnggota = false;
    }

    public function updatedSelectAllAnggota($value): void
    {
        if ($value && $this->kelompokAnggota) {
            $anggotaList = $this->kelompokAnggota->siswas;
            if ($this->searchAnggota) {
                $s = strtolower($this->searchAnggota);
                $anggotaList = $anggotaList->filter(fn($sw) =>
                    str_contains(strtolower($sw->nama), $s) ||
                    str_contains(strtolower($sw->nis ?? ''), $s)
                );
            }
            $this->selectedAnggotaIds = $anggotaList->pluck('id')->toArray();
        } else {
            $this->selectedAnggotaIds = [];
        }
    }

    public function updatedSelectedAnggotaIds(): void
    {
        if (!$this->kelompokAnggota) return;

        $anggotaList = $this->kelompokAnggota->siswas;
        if ($this->searchAnggota) {
            $s = strtolower($this->searchAnggota);
            $anggotaList = $anggotaList->filter(fn($sw) =>
                str_contains(strtolower($sw->nama), $s) ||
                str_contains(strtolower($sw->nis ?? ''), $s)
            );
        }
        $this->selectAllAnggota = count($this->selectedAnggotaIds) === $anggotaList->count() && $anggotaList->count() > 0;
    }

    public function konfirmasiKeluarkanBulk(): void
    {
        if (count($this->selectedAnggotaIds) === 0) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih minimal 1 siswa untuk dikeluarkan');
            return;
        }
        $this->showKonfirmasiKeluarkan = true;
    }

    public function batalKeluarkan(): void
    {
        $this->showKonfirmasiKeluarkan = false;
    }

    public function keluarkanAnggota(): void
    {
        if (count($this->selectedAnggotaIds) === 0) return;

        try {
            $count = count($this->selectedAnggotaIds);

            \Illuminate\Support\Facades\DB::transaction(function () {
                \App\Models\Siswa::whereIn('id', $this->selectedAnggotaIds)->update([
                    'kelompok_id'      => null,
                    'status_penempatan' => 'Belum Ditempatkan',
                ]);
            });

            // Refresh data kelompok di modal
            $this->kelompokAnggota = Kelompok::with(['guruPembimbing', 'siswas.kelas'])
                ->findOrFail($this->kelompokAnggota->id);

            $this->selectedAnggotaIds = [];
            $this->selectAllAnggota = false;
            $this->showKonfirmasiKeluarkan = false;

            $this->dispatch('toast', variant: 'sukses', message: "{$count} siswa berhasil dikeluarkan dari kelompok.");
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: 'Gagal mengeluarkan anggota: ' . $e->getMessage());
        }
    }

    // ─── Pengaturan Kelompok Methods ──────────────────────────────────────────

    public function bukaPengaturanModal(): void
    {
        $config = \App\Models\ParameterKonfigurasi::first();
        if ($config) {
            $this->kapasitas_kelompok_min = $config->kapasitas_kelompok_min ?? 6;
            $this->kapasitas_kelompok_max = $config->kapasitas_kelompok_max ?? 12;
            $this->komponen_penilaian = $config->komponen_penilaian ?? ['skor_tajwid', 'skor_kelancaran', 'skor_makhraj', 'skor_fashahah'];
        } else {
            $this->komponen_penilaian = ['skor_tajwid', 'skor_kelancaran', 'skor_makhraj', 'skor_fashahah'];
        }
        $this->showPengaturanModal = true;
    }

    public function tutupPengaturanModal(): void
    {
        $this->showPengaturanModal = false;
    }

    public function savePengaturanKelompok(): void
    {
        $this->validate([
            'kapasitas_kelompok_min' => 'required|numeric|min:1|max:50',
            'kapasitas_kelompok_max' => 'required|numeric|min:1|max:50|gte:kapasitas_kelompok_min',
            'komponen_penilaian' => 'array',
        ], [
            'kapasitas_kelompok_max.gte' => 'Kapasitas maksimal harus lebih besar atau sama dengan kapasitas minimal.',
        ]);

        $config = \App\Models\ParameterKonfigurasi::first();
        if ($config) {
            $config->kapasitas_kelompok_min = $this->kapasitas_kelompok_min;
            $config->kapasitas_kelompok_max = $this->kapasitas_kelompok_max;
            $config->komponen_penilaian = count($this->komponen_penilaian) > 0 ? $this->komponen_penilaian : ['skor_kelancaran'];
            $config->save();
            $this->dispatch('toast', variant: 'sukses', message: 'Kapasitas dan Parameter Tahfidz berhasil diperbarui.');
            $this->tutupPengaturanModal();
        } else {
            $this->dispatch('toast', variant: 'error', message: 'Data Parameter Konfigurasi tidak ditemukan.');
        }
    }

    public function bukaKomponenPenilaianModal(): void
    {
        $config = \App\Models\ParameterKonfigurasi::first();
        if ($config) {
            $this->komponen_penilaian = $config->komponen_penilaian ?? ['skor_tajwid', 'skor_kelancaran', 'skor_makhraj', 'skor_fashahah'];
        } else {
            $this->komponen_penilaian = ['skor_tajwid', 'skor_kelancaran', 'skor_makhraj', 'skor_fashahah'];
        }
        $this->showKomponenPenilaianModal = true;
    }

    public function tutupKomponenPenilaianModal(): void
    {
        $this->showKomponenPenilaianModal = false;
    }

    public function saveKomponenPenilaian(array $selected = []): void
    {
        $selected = array_values(array_filter($selected));

        if (count($selected) < 1) {
            $this->addError('komponen_penilaian', 'Pilih minimal satu komponen penilaian.');
            return;
        }

        $config = \App\Models\ParameterKonfigurasi::first();
        if ($config) {
            $config->komponen_penilaian = $selected;
            $config->save();
            $this->komponen_penilaian = $selected;
            $this->dispatch('toast', variant: 'sukses', message: 'Komponen Penilaian berhasil diperbarui.');
            $this->tutupKomponenPenilaianModal();
        } else {
            $this->dispatch('toast', variant: 'error', message: 'Data Parameter Konfigurasi tidak ditemukan.');
        }
    }

    // ─── Target Hafalan Methods ────────────────────────────────────────────────────────

    public function bukaTargetHafalanModal(): void
    {
        $config = \App\Models\ParameterKonfigurasi::first();
        if ($config) {
            $batas = $config->batas_tingkat ?? [];
            $this->target_dasar   = $batas['Dasar']   ?? [];
            $this->target_menengah = $batas['Menengah'] ?? [];
            $this->target_atas    = $batas['Atas']    ?? [];
        }
        $this->selected_juz_dasar = '';
        $this->selected_juz_menengah = '';
        $this->selected_juz_atas = '';
        $this->showTargetHafalanModal = true;
    }

    public function tutupTargetHafalanModal(): void
    {
        $this->showTargetHafalanModal = false;
    }

    public function addJuz(string $tingkat): void
    {
        $selectedProperty = 'selected_juz_' . strtolower($tingkat);
        $targetProperty   = 'target_'        . strtolower($tingkat);
        $val = $this->$selectedProperty;

        if ($val) {
            $val = (int) $val;
            $allSelected = array_merge($this->target_dasar, $this->target_menengah, $this->target_atas);
            if (!in_array($val, $allSelected)) {
                $arr = $this->$targetProperty;
                $arr[] = $val;
                sort($arr);
                $this->$targetProperty = $arr;
            } else {
                $this->dispatch('toast', variant: 'error', message: 'Juz tersebut sudah ditambahkan di tingkat lain!');
            }
            $this->$selectedProperty = '';
        }
    }

    public function removeJuz(string $tingkat, int $juz): void
    {
        $targetProperty = 'target_' . strtolower($tingkat);
        $this->$targetProperty = array_values(array_filter($this->$targetProperty, fn($val) => $val != $juz));
    }



    public function saveTargetHafalan(): void
    {
        $totalTarget = count($this->target_dasar) + count($this->target_menengah) + count($this->target_atas);

        $config = \App\Models\ParameterKonfigurasi::first();
        if ($config) {
            $config->batas_tingkat = [
                'Dasar'    => array_map('intval', (array) $this->target_dasar),
                'Menengah' => array_map('intval', (array) $this->target_menengah),
                'Atas'     => array_map('intval', (array) $this->target_atas),
            ];
            $config->target_kelulusan = $totalTarget;
            $config->save();

            // Sync all existing student progress records to use the new target
            \App\Models\ProgresHafalan::query()->update(['target_juz' => $totalTarget]);

            // Clear cache so it will be recalculated on next load
            \Illuminate\Support\Facades\Artisan::call('cache:clear');

            $this->dispatch('toast', variant: 'sukses', message: 'Target Hafalan per tingkat berhasil diperbarui.');
            $this->tutupTargetHafalanModal();
        } else {
            $this->dispatch('toast', variant: 'error', message: 'Data Parameter Konfigurasi tidak ditemukan.');
        }
    }

    public function runJuzSeeder(): void
    {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'JuzSeeder']);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'JuzsTableSeeder']);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'SurahsTableSeeder']);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'JuzSurahSeeder']);

        // Bersihkan cache agar dropdown juz mengambil data terbaru dari database
        \App\Services\ReferensiCacheService::clearReferensiCache();

        $this->dispatch('toast', variant: 'sukses', message: 'Data Referensi Al-Quran berhasil diisi (30 Juz & 114 Surah).');
        $this->bukaTargetHafalanModal();
    }

    // ─── Tempatkan Siswa Methods ──────────────────────────────────────────────

    public function bukaTempatkanSiswaModal(int $kelompokId): void
    {
        $this->kelompokTujuan = Kelompok::with(['guruPembimbing', 'tahunAjaran'])->findOrFail($kelompokId);
        $this->tanggalPenempatanSiswa = now()->format('Y-m-d');
        $this->selectedSiswaIds = [];
        $this->selectAllSiswa = false;
        $this->searchSiswaTempatkan = '';
        $this->filterStatusSiswa = 'belum_ditempatkan';
        $this->showTempatkanSiswaModal = true;
    }

    public function tutupTempatkanSiswaModal(): void
    {
        $this->showTempatkanSiswaModal = false;
        $this->kelompokTujuan = null;
        $this->tanggalPenempatanSiswa = '';
        $this->selectedSiswaIds = [];
        $this->selectAllSiswa = false;
        $this->searchSiswaTempatkan = '';
        $this->filterStatusSiswa = 'belum_ditempatkan';
    }

    public function updatedSelectAllSiswa($value): void
    {
        if ($value) {
            $siswaList = $this->getAvailableSiswaQuery()->get();
            $this->selectedSiswaIds = $siswaList->pluck('id')->toArray();
        } else {
            $this->selectedSiswaIds = [];
        }
    }

    public function updatedSelectedSiswaIds(): void
    {
        $totalAvailable = $this->getAvailableSiswaQuery()->count();
        $this->selectAllSiswa = count($this->selectedSiswaIds) === $totalAvailable && $totalAvailable > 0;
    }

    private function getAvailableSiswaQuery()
    {
        if (!$this->kelompokTujuan) {
            return \App\Models\Siswa::whereRaw('1 = 0'); // Empty query
        }

        $query = \App\Models\Siswa::where('status_akademik', 'Aktif');

        // Filter berdasarkan status - DEFAULT: Belum Ditempatkan saja
        if ($this->filterStatusSiswa === 'belum_ditempatkan') {
            $query->where('status_penempatan', 'Belum Ditempatkan');
        }
        // Note: Jika 'semua', tidak ada filter status_penempatan
        // Tapi validasi di service akan tetap reject siswa yang sudah ditempatkan

        // Filter jenis kelamin sesuai kelompok (WAJIB)
        if ($this->kelompokTujuan->jenis === 'Banin') {
            $query->where('jenis_kelamin', 'Laki-laki');
        } elseif ($this->kelompokTujuan->jenis === 'Banat') {
            $query->where('jenis_kelamin', 'Perempuan');
        }

        // Search
        if ($this->searchSiswaTempatkan) {
            $search = $this->searchSiswaTempatkan;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('nama');
    }

    public function prosesTempatkanSiswa(): void
    {
        if (!$this->kelompokTujuan || count($this->selectedSiswaIds) === 0) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih minimal 1 siswa untuk ditempatkan');
            return;
        }

        $this->validate([
            'tanggalPenempatanSiswa' => 'required|date',
        ], [
            'tanggalPenempatanSiswa.required' => 'Tanggal penempatan wajib diisi',
            'tanggalPenempatanSiswa.date' => 'Format tanggal tidak valid',
        ]);

        try {
            $siswaService = app(\App\Services\MasterData\SiswaService::class);
            $adminId = auth('admin')->id();

            $count = count($this->selectedSiswaIds);

            // Gunakan method yang sama dengan halaman siswa
            $siswaService->bulkPenempatan(
                $this->selectedSiswaIds,
                $this->kelompokTujuan->id,
                $this->tanggalPenempatanSiswa,
                $adminId
            );

            $this->dispatch('toast', variant: 'sukses', message: "{$count} siswa berhasil ditempatkan ke kelompok {$this->kelompokTujuan->nama}");
            $this->tutupTempatkanSiswaModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: 'Gagal menempatkan siswa: ' . $e->getMessage());
        }
    }

    // ─── Render ───────────────────────────────────────────────────────────────


    /**
     * Render komponen dengan daftar kelompok, jumlah siswa, status kapasitas, dan filter
     */
    public function render(): \Illuminate\View\View
    {
        $query = Kelompok::with(['guruPembimbing', 'tahunAjaran', 'siswas']);

        // Apply search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhereHas('guruPembimbing', fn($q) =>
                    $q->where('nama', 'like', "%{$this->search}%")
                  );
            });
        }

        // Apply filters
        if ($this->filterTingkat) {
            $query->where('tingkat', $this->filterTingkat);
        }

        if ($this->filterJenis) {
            $query->where('jenis', $this->filterJenis);
        }

        if ($this->filterTahunAjaran) {
            $query->where('tahun_ajaran_id', $this->filterTahunAjaran);
        }

        if ($this->filterGuruPembimbing) {
            $query->where('guru_pembimbing_id', $this->filterGuruPembimbing);
        }

        // Order by most recently saved/updated at the bottom
        $query->orderBy('updated_at', 'asc');

        // Paginate
        $kelompoks = $query->paginate(20);

        // Get data for filter dropdowns
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $guruPembimbings = Guru::where('jabatan', 'Guru Pembimbing Tahfidz')
            ->where('status_aktif', true)
            ->orderBy('nama')
            ->get();

        $semuaJuz = \App\Models\ReferensiQuran::orderBy('juz_ke', 'asc')->get();

        return view('livewire.admin.master-data.kelompok.index-kelompok', [
            'kelompoks'             => $kelompoks,
            'tahunAjarans'          => $tahunAjarans,
            'guruPembimbings'       => $guruPembimbings,
            'tingkatKelompokOptions' => TingkatKelompok::options(),
            'jenisKelompokOptions'  => JenisKelompok::options(),
            'semuaJuz'              => $semuaJuz,
        ]);
    }
}
