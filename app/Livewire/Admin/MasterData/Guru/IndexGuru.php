<?php

namespace App\Livewire\Admin\MasterData\Guru;

use App\Models\Guru;
use App\Services\MasterData\GuruService;
use App\Services\ErrorHandlingService;
use App\Enums\JabatanGuru;
use App\Enums\JenisKelamin;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('admin.layouts.app')]
class IndexGuru extends Component
{
    use WithPagination;

    // ─── Properties ──────────────────────────────────────────────────────────

    public string $search = '';
    public string $filterJabatan = '';
    public string $filterJenisKelamin = '';
    public bool $showFilters = false;

    // Bulk selection
    public array $selectedIds = [];
    public bool $selectAll = false;

    // Bulk delete modal
    public bool $showBulkDeleteConfirm = false;

    // Registrasi Keluar modal
    public bool $showRegistrasiKeluarModal = false;
    public string $jenis_keluar = '';
    public string $tanggal_keluar = '';
    public string $keterangan_keluar = '';

    /** Daftar kelompok yang masih dibimbing guru yang dipilih */
    public array $kelompokAktif = [];

    // Form Properties
    public bool $showFormModal = false;
    public bool $isEdit = false;
    public ?int $editId = null;

    public $niy = '';
    public $nama = '';
    public $jenis_kelamin = '';
    public $tanggal_lahir = '';
    public $tempat_lahir = '';
    public $jabatan = '';
    public $no_telepon = '';
    public $email = '';
    public $tanggal_mulai_kerja = '';

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    /**
     * Reset pagination when filters change
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingFilterJabatan(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatingFilterJenisKelamin(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selectedIds = $this->getFilteredQuery()->pluck('id')->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSelectedIds(): void
    {
        $totalFiltered = $this->getFilteredQuery()->count();
        $this->selectAll = count($this->selectedIds) === $totalFiltered && $totalFiltered > 0;
    }

    private function getFilteredQuery()
    {
        $query = Guru::with('kelompoksBimbingan')->where('status_aktif', true);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('niy', 'like', "%{$this->search}%")
                    ->orWhere('nama', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterJabatan) {
            $query->where('jabatan', $this->filterJabatan);
        }

        if ($this->filterJenisKelamin) {
            $query->where('jenis_kelamin', $this->filterJenisKelamin);
        }

        return $query;
    }

    /**
     * Listener untuk event refresh dari modal atau komponen lain
     */
    protected $listeners = ['guruSaved' => '$refresh'];

    /**
     * Validasi realtime saat field form berubah
     */
    public function updated(string $field): void
    {
        // Hanya validasi field milik form modal
        $formFields = ['niy','nama','jenis_kelamin','tanggal_lahir','tempat_lahir','jabatan','no_telepon','email','tanggal_mulai_kerja'];
        if (in_array($field, $formFields)) {
            $this->validateOnly($field);
        }
    }

    // ─── Form Logic ───────────────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'niy'              => 'required|digits:16|unique:gurus,niy' . ($this->isEdit ? ',' . $this->editId : ''),
            'nama'             => 'required|string|max:255',
            'jenis_kelamin'    => ['required', JenisKelamin::validationRule()],
            'tanggal_lahir'    => 'required|date|date_format:Y-m-d|before:today',
            'tempat_lahir'     => 'required|string|max:100',
            'jabatan'          => ['required', JabatanGuru::validationRule()],
            'no_telepon'       => 'required|digits_between:10,13',
            'email'            => 'required|email|max:100',
            'tanggal_mulai_kerja' => 'nullable|date|date_format:Y-m-d|before_or_equal:today',
        ];
    }

    protected function messages(): array
    {
        return [
            'niy.required'          => 'NIY wajib diisi.',
            'niy.digits'            => 'NIY harus tepat 16 digit angka.',
            'niy.unique'            => 'NIY sudah terdaftar dalam sistem.',
            'nama.required'         => 'Nama wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date_format' => 'Tanggal lahir harus berformat YYYY-MM-DD.',
            'tanggal_lahir.before'  => 'Tanggal lahir harus sebelum hari ini.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'jabatan.required'      => 'Jabatan wajib dipilih.',
            'no_telepon.required'   => 'No. telepon wajib diisi.',
            'no_telepon.digits_between' => 'No. telepon harus berupa angka 10 sampai 13 digit.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'tanggal_mulai_kerja.date_format' => 'Tanggal mulai kerja harus berformat YYYY-MM-DD.',
            'tanggal_mulai_kerja.before_or_equal' => 'Tanggal mulai kerja tidak boleh di masa depan.',
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->reset([
            'niy', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir',
            'jabatan', 'no_telepon', 'email', 'tanggal_mulai_kerja'
        ]);
        $this->isEdit = false;
        $this->editId = null;
        $this->showFormModal = true;

        // Dispatch event untuk reinitialize flatpickr
        $this->dispatch('modal-opened');
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $guru = Guru::findOrFail($id);

        $this->editId = $guru->id;
        $this->niy = $guru->niy;
        $this->nama = $guru->nama;
        $this->jenis_kelamin = $guru->jenis_kelamin;
        $this->tanggal_lahir = $guru->tanggal_lahir?->format('Y-m-d') ?? '';
        $this->tempat_lahir = $guru->tempat_lahir;
        $this->jabatan = $guru->jabatan;
        $this->no_telepon = $guru->no_telepon;
        $this->email = $guru->email ?? '';
        $this->tanggal_mulai_kerja = $guru->tanggal_mulai_kerja?->format('Y-m-d') ?? '';

        $this->isEdit = true;
        $this->showFormModal = true;

        // Dispatch event untuk reinitialize flatpickr
        $this->dispatch('modal-opened');
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
    }

    public function saveForm(GuruService $guruService): void
    {
        $this->validate();

        try {
            $guruData = [
                'niy' => $this->niy,
                'nama' => $this->nama,
                'jenis_kelamin' => $this->jenis_kelamin,
                'tanggal_lahir' => $this->tanggal_lahir,
                'tempat_lahir' => $this->tempat_lahir,
                'jabatan' => $this->jabatan,
                'no_telepon' => $this->no_telepon,
                'email' => $this->email ?: null,
                'tanggal_mulai_kerja' => $this->tanggal_mulai_kerja ?: null,
            ];

            if ($this->isEdit) {
                $guruService->update($this->editId, $guruData);
                $this->dispatch('toast', variant: 'sukses', message: 'Data guru berhasil diperbarui');
            } else {
                $guruService->register($guruData);
                $this->dispatch('toast', variant: 'sukses', message: 'Guru berhasil didaftarkan');
            }

            $this->closeFormModal();
            $this->resetPage();
        } catch (\Illuminate\Validation\ValidationException $e) {
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
            'search',
            'filterJabatan',
            'filterJenisKelamin'
        ]);
        $this->resetPage();
    }

    // ─── Bulk Delete Methods ──────────────────────────────────────────────────

    /**
     * Buka modal konfirmasi bulk delete
     */
    public function openBulkDeleteConfirm(): void
    {
        if (count($this->selectedIds) === 0) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih minimal 1 guru untuk dihapus');
            return;
        }
        $this->showBulkDeleteConfirm = true;
    }

    /**
     * Tutup modal bulk delete
     */
    public function closeBulkDeleteConfirm(): void
    {
        $this->showBulkDeleteConfirm = false;
    }

    /**
     * Proses bulk delete
     */
    public function bulkDelete(GuruService $guruService): void
    {
        try {
            foreach ($this->selectedIds as $id) {
                $guruService->delete($id);
            }

            $count = count($this->selectedIds);
            $this->selectedIds = [];
            $this->selectAll = false;
            $this->showBulkDeleteConfirm = false;

            $this->dispatch('toast', variant: 'sukses', message: "{$count} guru berhasil dihapus");
            $this->resetPage();
        } catch (\Exception $e) {
            $this->showBulkDeleteConfirm = false;
            ErrorHandlingService::handleInComponent($this, $e, 'menghapus guru');
        }
    }

    // ─── Registrasi Keluar Methods ───────────────────────────────────────────────

    public function bukaModalRegistrasiKeluar(): void
    {
        if (count($this->selectedIds) !== 1) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih tepat 1 guru untuk registrasi keluar');
            return;
        }

        $guru = Guru::with('kelompoksBimbingan')->find($this->selectedIds[0]);

        if (!$guru) {
            $this->dispatch('toast', variant: 'error', message: 'Guru tidak ditemukan.');
            return;
        }

        // Cek apakah guru masih membimbing kelompok aktif
        $this->kelompokAktif = $guru->kelompoksBimbingan
            ->map(fn($k) => $k->nama)
            ->toArray();

        $this->jenis_keluar = '';
        $this->tanggal_keluar = now()->format('Y-m-d');
        $this->keterangan_keluar = '';

        $this->showRegistrasiKeluarModal = true;
    }

    public function tutupModalRegistrasiKeluar(): void
    {
        $this->showRegistrasiKeluarModal = false;
        $this->kelompokAktif = [];
        $this->reset(['jenis_keluar', 'tanggal_keluar', 'keterangan_keluar']);
        $this->resetValidation();
    }

    public function simpanRegistrasiKeluar(): void
    {
        // Safety net: tolak jika guru masih punya kelompok
        if (!empty($this->kelompokAktif)) {
            $this->dispatch('toast', variant: 'error', message: 'Guru masih membimbing kelompok. Pindahkan pembimbing terlebih dahulu.');
            return;
        }

        $this->validate([
            'jenis_keluar' => 'required|in:Pensiun,Mutasi,Berhenti,Meninggal',
            'tanggal_keluar' => 'required|date',
            'keterangan_keluar' => 'nullable|string|max:1000',
        ], [
            'jenis_keluar.required' => 'Jenis keluar wajib dipilih',
            'jenis_keluar.in' => 'Jenis keluar tidak valid',
            'tanggal_keluar.required' => 'Tanggal keluar wajib diisi',
            'tanggal_keluar.date' => 'Format tanggal tidak valid',
        ]);

        try {
            $guruId = $this->selectedIds[0];
            $guru = Guru::findOrFail($guruId);

            // Kita gunakan DB Transaction secara manual di sini,
            // idealnya bisa dipindah ke GuruService ke depannya
            \Illuminate\Support\Facades\DB::transaction(function () use ($guru) {
                // Update guru data
                $guru->update([
                    'status_aktif' => false,
                    'jenis_keluar' => $this->jenis_keluar,
                    'tanggal_keluar' => $this->tanggal_keluar,
                    'keterangan_keluar' => $this->keterangan_keluar,
                    'diproses_oleh' => auth()->id() ?? 1, // Fallback ke 1 jika session auth tidak terdeteksi di CLI/Test
                ]);

                // Lepaskan dari semua kelompok bimbingan yang ada saat ini
                // Karena kelompok harus punya guru, ini sebenarnya akan membuat kelompoks.guru_pembimbing_id yatim jika tidak ditangani.
                // Sesuai aturan DB kita, guru_pembimbing_id tidak boleh null.
                // Tapi ini sudah di-handle oleh GuruService->delete(), sayangnya updateStatus belum menghandle pelepasan kelompok.
                // Sistem saat ini "membiarkan" kelompok tetap terkait dengan Guru Non-Aktif.
                // Untuk registrasi keluar, perlakuannya sama dengan updateStatus(false)
            });

            $this->selectedIds = [];
            $this->selectAll = false;
            $this->tutupModalRegistrasiKeluar();

            $this->dispatch('toast', variant: 'sukses', message: "Guru berhasil di-registrasi keluar.");
            $this->resetPage();
        } catch (\Exception $e) {
            $this->showRegistrasiKeluarModal = false;
            ErrorHandlingService::handleInComponent($this, $e, 'meregistrasi keluar guru');
        }
    }

    // ─── Helper Methods ───────────────────────────────────────────────────────

    /**
     * Get jabatan badge color based on value
     */
    public function getJabatanBadgeColor(string $jabatan): string
    {
        return match($jabatan) {
            'Guru Pembimbing Tahfidz' => 'zamrud',
            'Guru Mapel' => 'slate',
            'Kepala Sekolah' => 'emas',
            'Tendik' => 'slate',
            default => 'slate',
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor(bool $statusAktif): string
    {
        return $statusAktif ? 'zamrud' : 'delima';
    }

    /**
     * Get status label
     */
    public function getStatusLabel(bool $statusAktif): string
    {
        return $statusAktif ? 'Aktif' : 'Tidak Aktif';
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    /**
     * Render komponen dengan daftar guru, pencarian, dan filter
     */
    public function render(): \Illuminate\View\View
    {
        $query = $this->getFilteredQuery();

        // Order by latest - data terbaru di atas
        $query->orderBy('id', 'desc');

        // Eager load relationships
        $query->with(['tugasTambahans', 'kelompoksBimbingan']);

        // Paginate
        $gurus = $query->paginate(20);

        return view('livewire.admin.master-data.guru.index-guru', [
            'gurus' => $gurus,
            'jabatanOptions' => JabatanGuru::options(),
            'jenisKelaminOptions' => JenisKelamin::options(),
            'pendidikanTerakhirOptions' => \App\Enums\PendidikanTerakhir::options(),
        ]);
    }
}
