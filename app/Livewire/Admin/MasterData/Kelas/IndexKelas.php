<?php

namespace App\Livewire\Admin\MasterData\Kelas;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kelas;

#[Layout('admin.layouts.app')]
class IndexKelas extends Component
{
    use WithPagination;

    public $search = '';
    public $filterTingkat = '';

    // Bulk selection
    public $selectedIds = [];
    public $selectAll = false;

    // Modal fields
    public $showModal = false;
    public $kelasId = null;
    public $tingkat, $nama_kelas, $ruang_kelas, $nama_wali_kelas;

    // Anggota Kelas Modal
    public bool $showAnggotaModal = false;
    public ?Kelas $kelasAnggota = null;
    public string $searchAnggota = '';

    // Bulk delete modal
    public $showBulkDeleteConfirm = false;

    // Modal Luluskan Kelas 9
    public bool $showModalLuluskanKelas9 = false;
    public int $totalSiswaKelas9 = 0;
    public string $tanggalLulus = '';
    public string $keteranganLulus = '';
    public array $previewSiswaKelas9 = [];

    protected function rules(): array
    {
        return [
            'tingkat'        => 'required|in:7,8,9',
            'nama_kelas'     => [
                'required',
                'string',
                'max:10',
                \Illuminate\Validation\Rule::unique('kelas', 'nama_kelas')
                    ->ignore($this->kelasId)
                    ->whereNull('deleted_at'),
            ],
            'ruang_kelas'     => 'nullable|string|max:50',
            'nama_wali_kelas' => 'required|string|max:100',
        ];
    }

    protected $messages = [
        'tingkat.required'        => 'Tingkat harus dipilih.',
        'tingkat.in'              => 'Tingkat harus 7, 8, atau 9.',
        'nama_kelas.required'     => 'Nama kelas harus diisi.',
        'nama_kelas.max'          => 'Nama kelas maksimal 10 karakter.',
        'nama_kelas.unique'       => 'Kelas ini sudah ada. Gunakan nama yang berbeda.',
        'ruang_kelas.max'         => 'Ruang kelas maksimal 50 karakter.',
        'nama_wali_kelas.required' => 'Nama wali kelas harus diisi.',
        'nama_wali_kelas.max'     => 'Nama wali kelas maksimal 100 karakter.',
    ];

    public function updated($field): void
    {
        $this->validateOnly($field);
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = Kelas::query()
                ->when($this->search, fn($q) =>
                    $q->where('nama_kelas', 'like', "%{$this->search}%")
                      ->orWhere('nama_wali_kelas', 'like', "%{$this->search}%")
                )
                ->when($this->filterTingkat, fn($q) =>
                    $q->where('tingkat', $this->filterTingkat)
                )
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSelectedIds()
    {
        $totalFiltered = Kelas::query()
            ->when($this->search, fn($q) =>
                $q->where('nama_kelas', 'like', "%{$this->search}%")
                  ->orWhere('nama_wali_kelas', 'like', "%{$this->search}%")
            )
            ->when($this->filterTingkat, fn($q) =>
                $q->where('tingkat', $this->filterTingkat)
            )
            ->count();

        $this->selectAll = count($this->selectedIds) === $totalFiltered && $totalFiltered > 0;
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingFilterTingkat()
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function render()
    {
        $kelas = Kelas::query()
            ->withCount([
                'siswas as siswas_aktif_count' => fn($q) => $q->where('status_aktif', true),
                'siswas as siswas_non_aktif_count' => fn($q) => $q->where('status_aktif', false),
                'siswas as siswas_count',
            ])
            ->when($this->search, fn($q) =>
                $q->where('nama_kelas', 'like', "%{$this->search}%")
                  ->orWhere('nama_wali_kelas', 'like', "%{$this->search}%")
            )
            ->when($this->filterTingkat, fn($q) =>
                $q->where('tingkat', $this->filterTingkat)
            )
            ->orderBy('updated_at', 'asc')
            ->paginate(20);

        return view('livewire.admin.master-data.kelas.index-kelas', [
            'kelas' => $kelas,
        ]);
    }

    public function openModal($id = null)
    {
        $this->resetForm();

        if ($id) {
            $this->kelasId = $id;
            $kelas = Kelas::findOrFail($id);
            $this->fill($kelas->only([
                'tingkat', 'nama_kelas', 'ruang_kelas', 'nama_wali_kelas'
            ]));
        }

        $this->showModal = true;
    }

    public function viewDetail($id)
    {
        // For now, viewing detail opens the same modal as edit
        // In a full implementation, this could open a different read-only modal
        $this->openModal($id);
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'tingkat' => $this->tingkat,
                'nama_kelas' => $this->nama_kelas,
                'ruang_kelas' => $this->ruang_kelas,
                'nama_wali_kelas' => $this->nama_wali_kelas,
            ];

            if ($this->kelasId) {
                $kelas = Kelas::findOrFail($this->kelasId);
                $kelas->update($data);
                $message = 'Kelas berhasil diperbarui';
            } else {
                Kelas::create($data);
                $message = 'Kelas berhasil ditambahkan';
            }

            $this->showModal = false;
            $this->dispatch('toast', variant: 'sukses', message: $message);
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function bukaAnggotaModal(int $kelasId): void
    {
        $this->kelasAnggota = Kelas::with(['siswas' => function($q) {
            $q->orderBy('nama', 'asc');
        }])->findOrFail($kelasId);
        $this->searchAnggota = '';
        $this->showAnggotaModal = true;
    }

    public function tutupAnggotaModal(): void
    {
        $this->showAnggotaModal = false;
        $this->kelasAnggota = null;
        $this->searchAnggota = '';
    }

    public function openBulkDeleteConfirm()
    {
        if (count($this->selectedIds) === 0) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih minimal 1 kelas untuk dihapus');
            return;
        }
        $this->showBulkDeleteConfirm = true;
    }

    public function closeBulkDeleteConfirm()
    {
        $this->showBulkDeleteConfirm = false;
    }

    public function bulkDelete()
    {
        try {
            Kelas::whereIn('id', $this->selectedIds)->delete();

            $count = count($this->selectedIds);
            $this->selectedIds = [];
            $this->selectAll = false;
            $this->showBulkDeleteConfirm = false;

            $this->dispatch('toast', variant: 'sukses', message: "{$count} kelas berhasil dihapus");
            $this->resetPage();
        } catch (\Exception $e) {
            $this->showBulkDeleteConfirm = false;
            $this->dispatch('toast', variant: 'error', message: 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Buka modal konfirmasi kelulusan semua siswa aktif kelas 9
     */
    public function bukaModalLuluskanKelas9(): void
    {
        $this->tanggalLulus = now()->format('Y-m-d');
        $this->keteranganLulus = 'Lulusan Kelas 9 (Lulus SMP)';

        $siswaQuery = \App\Models\Siswa::where('status_aktif', true)
            ->whereHas('kelas', function ($q) {
                $q->where('tingkat', '9')
                  ->orWhere('nama_kelas', 'like', '9%');
            })
            ->with(['kelas', 'progresHafalan'])
            ->orderBy('nama');

        $this->totalSiswaKelas9 = $siswaQuery->count();
        $this->previewSiswaKelas9 = $siswaQuery->get()->map(function ($s) {
            return [
                'id'             => $s->id,
                'nama'           => $s->nama,
                'nis'            => $s->nis,
                'kelas'          => $s->kelas?->nama_kelas ?? '9',
                'status_tahfidz' => $s->status_tahfidz,
                'total_juz'      => $s->progresHafalan?->total_juz_tuntas ?? 0,
            ];
        })->toArray();

        $this->showModalLuluskanKelas9 = true;
    }

    /**
     * Tutup modal konfirmasi kelulusan kelas 9
     */
    public function tutupModalLuluskanKelas9(): void
    {
        $this->showModalLuluskanKelas9 = false;
        $this->previewSiswaKelas9 = [];
    }

    /**
     * Proses kelulusan massal seluruh siswa aktif kelas 9
     */
    public function prosesLuluskanKelas9(): void
    {
        $this->validate([
            'tanggalLulus' => 'required|date',
        ], [
            'tanggalLulus.required' => 'Tanggal kelulusan wajib diisi.',
            'tanggalLulus.date'     => 'Format tanggal kelulusan tidak valid.',
        ]);

        $adminId = auth()->guard('admin')->id();

        $siswaIds = \App\Models\Siswa::where('status_aktif', true)
            ->whereHas('kelas', function ($q) {
                $q->where('tingkat', '9')
                  ->orWhere('nama_kelas', 'like', '9%');
            })
            ->pluck('id')
            ->toArray();

        if (empty($siswaIds)) {
            $this->dispatch('toast', variant: 'warning', message: 'Tidak ditemukan siswa aktif di Kelas 9.');
            $this->tutupModalLuluskanKelas9();
            return;
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($siswaIds, $adminId) {
                \App\Models\Siswa::whereIn('id', $siswaIds)->update([
                    'status_akademik'   => 'Lulus SMP',
                    'status_aktif'      => false,
                    'jenis_keluar'      => 'Lulus SMP',
                    'tanggal_keluar'    => $this->tanggalLulus,
                    'keterangan_keluar' => $this->keteranganLulus ?: 'Lulusan Kelas 9 (Lulus SMP)',
                    'diproses_oleh'     => $adminId,
                    'kelompok_id'       => null,
                    'status_penempatan' => 'Belum Ditempatkan',
                ]);
            });

            $count = count($siswaIds);
            $this->tutupModalLuluskanKelas9();
            $this->dispatch('toast', variant: 'sukses', message: "Berhasil meluluskan {$count} siswa Kelas 9 per tanggal " . \Carbon\Carbon::parse($this->tanggalLulus)->format('d/m/Y') . ". Data siswa telah dipindahkan ke Siswa Non-Aktif.");
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: 'Gagal memproses kelulusan: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->reset([
            'kelasId', 'tingkat', 'nama_kelas', 'ruang_kelas', 'nama_wali_kelas'
        ]);
        $this->resetValidation();
    }
}
