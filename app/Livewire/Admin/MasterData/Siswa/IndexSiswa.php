<?php

namespace App\Livewire\Admin\MasterData\Siswa;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Kelompok;
use App\Models\TahunAjaran;
use App\Services\MasterData\SiswaService;
use App\Services\ErrorHandlingService;
use App\Enums\StatusAkademik;
use App\Enums\StatusTahfidz;
use App\Enums\StatusPenempatan;
use App\Enums\JenisKelamin;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('admin.layouts.app')]
class IndexSiswa extends Component
{
    use WithPagination;

    // ─── Properties ──────────────────────────────────────────────────────────

    public string $search = '';
    public string $filterStatusAkademik = '';
    public string $filterStatusTahfidz = '';
    public string $filterStatusPenempatan = '';
    public string $filterJenisKelamin = '';
    public string $filterKelas = '';

    public bool $showFilters = false;

    public array $selectedIds = [];
    public bool $selectAll = false;

    public bool $showModalKonfirmasi = false;
    public ?int $siswaIdHapus = null;

    public bool $showModalBulkDelete = false;

    // Exit Registration Properties
    public bool $showModalRegistrasiKeluar = false;
    public ?int $siswaIdRegistrasiKeluar = null;

    // Exit Registration Form Fields
    public string $jenisKeluar = '';
    public string $tanggalKeluar = '';
    public string $tujuanSekolah = '';
    public string $keteranganKeluar = '';

    public bool $showModalKonfirmasiKeluar = false;

    // Entry Registration Properties
    public bool $showModalRegistrasiMasuk = false;
    public ?int $siswaIdRegistrasiMasuk = null;

    // Entry Registration Form Fields
    public string $statusMasuk = '';
    public string $tanggalMasuk = '';
    public string $asalSekolah = '';
    public string $alamatSekolahAsal = '';

    // ─── Form Modal (Create/Edit) Properties ─────────────────────────────────

    public bool $showFormModal = false;
    public bool $showDetailModal = false;
    public bool $isEdit = false;
    public ?int $editId = null;

    // Form fields
    public string $nis = '';
    public string $nisn = '';
    public string $nama = '';
    public string $jenis_kelamin = '';
    public string $tanggal_lahir = '';
    public string $tempat_lahir = '';
    public string $nama_ayah = '';
    public string $nama_ibu = '';
    public string $kelas_id = '';
    public bool $punya_hafalan_sebelumnya = false;

    // Registrasi Masuk
    public string $status_masuk = 'Baru';
    public string $tanggal_masuk = '';
    public string $asal_sekolah = '';

    public ?Siswa $selectedSiswa = null;

    // ─── Bulk Penempatan Kelompok Properties ─────────────────────────────────
    public bool $showModalBulkPenempatan = false;
    public string $bulkKelompokId = '';
    public string $bulkTanggalPenempatan = '';

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    /**
     * Reset pagination when filters change
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedFilterStatusAkademik(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedFilterStatusTahfidz(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedFilterStatusPenempatan(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedFilterJenisKelamin(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    public function updatedFilterKelas(): void
    {
        $this->resetPage();
        $this->clearSelection();
    }

    /**
     * Listener untuk event refresh dari modal atau komponen lain
     */
    protected $listeners = ['siswaSaved' => '$refresh'];

    public function updated(string $field): void
    {
        $formFields = ['nis', 'nisn', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir', 'nama_ayah', 'nama_ibu', 'kelas_id', 'punya_hafalan_sebelumnya', 'status_masuk', 'tanggal_masuk', 'asal_sekolah'];
        if (in_array($field, $formFields)) {
            $this->validateOnly($field, $this->rulesFormModal(), $this->messagesFormModal());
        }
    }

    // ─── Form Modal Rules ─────────────────────────────────────────────────────

    protected function rulesFormModal(): array
    {
        return [
            'nis'           => 'required|digits:9|unique:siswas,nis' . ($this->isEdit ? ',' . $this->editId : ''),
            'nisn'          => 'nullable|digits:9|unique:siswas,nisn' . ($this->isEdit ? ',' . $this->editId : ''),
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => ['required', \App\Enums\JenisKelamin::validationRule()],
            'tanggal_lahir' => 'required|date|date_format:Y-m-d|before:today',
            'tempat_lahir'  => 'required|string|max:100',
            'nama_ayah'     => 'nullable|string|max:255',
            'nama_ibu'      => 'nullable|string|max:255',
            'kelas_id'      => 'nullable|exists:kelas,id',
            'punya_hafalan_sebelumnya' => 'boolean',
            'status_masuk'  => 'required|in:Baru,Pindahan',
            'tanggal_masuk' => 'required|date',
            'asal_sekolah'  => 'nullable|string|max:255',
        ];
    }

    protected function messagesFormModal(): array
    {
        return [
            'nis.required'          => 'NIS wajib diisi',
            'nis.digits'            => 'NIS harus tepat 9 digit angka',
            'nis.unique'            => 'NIS sudah terdaftar dalam sistem',
            'nisn.digits'           => 'NISN harus tepat 9 digit angka',
            'nisn.unique'           => 'NISN sudah terdaftar dalam sistem',
            'nama.required'         => 'Nama wajib diisi',
            'jenis_kelamin.required'=> 'Jenis kelamin wajib dipilih',
            'tanggal_lahir.required'=> 'Tanggal lahir wajib diisi',
            'tanggal_lahir.before'  => 'Tanggal lahir harus sebelum hari ini',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi',
            'status_masuk.required' => 'Status masuk wajib dipilih',
            'status_masuk.in'       => 'Status masuk tidak valid',
            'tanggal_masuk.required'=> 'Tanggal masuk wajib diisi',
            'tanggal_masuk.date'    => 'Format tanggal masuk tidak valid',
        ];
    }

    // ─── Form Modal Methods ───────────────────────────────────────────────────

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->reset(['nis', 'nisn', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir', 'nama_ayah', 'nama_ibu', 'kelas_id', 'punya_hafalan_sebelumnya', 'tanggal_masuk', 'asal_sekolah']);
        $this->punya_hafalan_sebelumnya = false;
        $this->status_masuk = 'Baru';
        $this->isEdit  = false;
        $this->editId  = null;
        $this->showFormModal = true;

        // Dispatch event untuk reinitialize flatpickr
        $this->dispatch('modal-opened');
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $siswa = Siswa::findOrFail($id);

        $this->editId        = $siswa->id;
        $this->nis           = $siswa->nis;
        $this->nisn          = $siswa->nisn ?? '';
        $this->nama          = $siswa->nama;
        $this->jenis_kelamin = $siswa->jenis_kelamin;
        $this->tanggal_lahir = $siswa->tanggal_lahir?->format('Y-m-d') ?? '';
        $this->tempat_lahir  = $siswa->tempat_lahir;
        $this->nama_ayah     = $siswa->nama_ayah ?? '';
        $this->nama_ibu      = $siswa->nama_ibu ?? '';
        $this->kelas_id      = $siswa->kelas_id ?? '';
        $this->punya_hafalan_sebelumnya = (bool) $siswa->punya_hafalan_sebelumnya;
        $this->status_masuk  = $siswa->status_masuk ?? 'Baru';
        $this->tanggal_masuk = $siswa->tanggal_masuk?->format('Y-m-d') ?? '';
        $this->asal_sekolah  = $siswa->asal_sekolah ?? '';

        $this->isEdit = true;
        $this->showFormModal = true;

        // Dispatch event untuk reinitialize flatpickr
        $this->dispatch('modal-opened');
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
    }

    public function saveForm(SiswaService $siswaService): void
    {
        $this->validate($this->rulesFormModal(), $this->messagesFormModal());

        try {
            $data = [
                'nis'           => $this->nis,
                'nisn'          => $this->nisn ?: null,
                'nama'          => $this->nama,
                'jenis_kelamin' => $this->jenis_kelamin,
                'tanggal_lahir' => $this->tanggal_lahir,
                'tempat_lahir'  => $this->tempat_lahir,
                'nama_ayah'     => $this->nama_ayah ?: null,
                'nama_ibu'      => $this->nama_ibu ?: null,
                'kelas_id'      => $this->kelas_id ?: null,
                'punya_hafalan_sebelumnya' => $this->punya_hafalan_sebelumnya,
                'status_masuk'  => $this->status_masuk,
                'tanggal_masuk' => $this->tanggal_masuk ?: null,
                'asal_sekolah'  => $this->asal_sekolah ?: null,
            ];

            if ($this->isEdit) {
                $siswaService->update($this->editId, $data);
                $this->dispatch('toast', variant: 'sukses', message: 'Data siswa berhasil diperbarui');
            } else {
                $siswaService->register($data);
                $this->dispatch('toast', variant: 'sukses', message: 'Siswa berhasil didaftarkan');
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

    // ─── Detail Modal Methods ─────────────────────────────────────────────────

    public function openDetailModal(int $id): void
    {
        $this->selectedSiswa = Siswa::with(['kelompok.guruPembimbing', 'kelas', 'tahunAjaran'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedSiswa  = null;
    }

    // ─── Filter Methods ───────────────────────────────────────────────────────

    /**
     * Reset semua filter
     */
    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'filterStatusAkademik',
            'filterStatusTahfidz',
            'filterStatusPenempatan',
            'filterJenisKelamin',
            'filterKelas'
        ]);
        $this->resetPage();
    }

    // ─── Bulk Selection ───────────────────────────────────────────────────────

    /**
     * Toggle select all siswa on current page
     */
    public function updatedSelectAll(): void
    {
        if ($this->selectAll) {
            // Ambil ID dari halaman saat ini saja menggunakan service
            $queryService = app(\App\Services\MasterData\SiswaQueryService::class);
            $currentPageIds = $queryService->getFilteredSiswas([
                'search' => $this->search,
                'status_akademik' => $this->filterStatusAkademik,
                'status_tahfidz' => $this->filterStatusTahfidz,
                'status_penempatan' => $this->filterStatusPenempatan,
                'jenis_kelamin' => $this->filterJenisKelamin,
                'kelas_id' => $this->filterKelas,
            ])
                ->paginate(20)
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
            $this->selectedIds = $currentPageIds;
        } else {
            $this->selectedIds = [];
        }
    }

    /**
     * Clear all selections
     */
    public function clearSelection(): void
    {
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    /**
     * Check if all items on current page are selected
     */
    public function updatedSelectedIds(): void
    {
        $queryService = app(\App\Services\MasterData\SiswaQueryService::class);
        $currentPageIds = $queryService->getFilteredSiswas([
            'search' => $this->search,
            'status_akademik' => $this->filterStatusAkademik,
            'status_tahfidz' => $this->filterStatusTahfidz,
            'status_penempatan' => $this->filterStatusPenempatan,
            'jenis_kelamin' => $this->filterJenisKelamin,
            'kelas_id' => $this->filterKelas,
        ])
            ->paginate(20)
            ->pluck('id')
            ->toArray();

        // Update selectAll checkbox based on current selection
        $this->selectAll = !empty($currentPageIds) &&
            count(array_intersect($this->selectedIds, $currentPageIds)) === count($currentPageIds);
    }

    /**
     * Open bulk delete confirmation modal
     */
    public function bukaModalBulkDelete(): void
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih minimal 1 siswa untuk dihapus.');
            return;
        }

        $this->showModalBulkDelete = true;
    }

    /**
     * Close bulk delete modal
     */
    public function tutupModalBulkDelete(): void
    {
        $this->showModalBulkDelete = false;
    }

    /**
     * Execute bulk delete
     */
    public function bulkDelete(SiswaService $siswaService): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        try {
            $count = count($this->selectedIds);

            foreach ($this->selectedIds as $id) {
                $siswaService->delete($id);
            }

            $this->dispatch('toast', variant: 'sukses', message: "{$count} siswa berhasil dihapus.");

            $this->clearSelection();
            $this->tutupModalBulkDelete();
            $this->resetPage();
        } catch (\Exception $e) {
            ErrorHandlingService::handleInComponent($this, $e, 'menghapus siswa');
        }
    }

    // ─── Exit Registration Methods ────────────────────────────────────────────

    /**
     * Buka modal registrasi masuk untuk siswa yang dipilih
     *
     * Validasi:
     * - Harus tepat 1 siswa yang dipilih
     * - Siswa harus memiliki status_akademik = 'Aktif'
     */
    public function bukaModalRegistrasiMasuk(): void
    {
        if (count($this->selectedIds) !== 1) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih tepat 1 siswa untuk registrasi masuk.');
            return;
        }

        $siswa = Siswa::find($this->selectedIds[0]);

        if (!$siswa || $siswa->status_akademik !== 'Aktif') {
            $this->dispatch('toast', variant: 'error', message: 'Hanya siswa dengan status Aktif yang dapat diproses registrasi masuk.');
            return;
        }

        $this->siswaIdRegistrasiMasuk = $siswa->id;
        $this->showModalRegistrasiMasuk = true;
        $this->resetFormMasuk();
    }

    /**
     * Tutup modal registrasi masuk dan reset form
     */
    public function tutupModalRegistrasiMasuk(): void
    {
        $this->showModalRegistrasiMasuk = false;
        $this->siswaIdRegistrasiMasuk = null;
        $this->resetFormMasuk();
    }

    /**
     * Reset semua field form registrasi masuk
     */
    private function resetFormMasuk(): void
    {
        $this->statusMasuk = '';
        $this->tanggalMasuk = '';
        $this->asalSekolah = '';
        $this->alamatSekolahAsal = '';
        $this->resetErrorBag();
    }

    /**
     * Validation rules untuk form registrasi masuk
     */
    protected function rulesRegistrasiMasuk(): array
    {
        return [
            'statusMasuk' => ['required', 'in:Baru,Pindahan'],
            'tanggalMasuk' => ['required', 'date'],
            'asalSekolah' => [
                function ($attribute, $value, $fail) {
                    if ($this->statusMasuk === 'Pindahan' && empty($value)) {
                        $fail('Asal Sekolah wajib diisi untuk siswa pindahan.');
                    }
                }
            ],
            'alamatSekolahAsal' => [
                function ($attribute, $value, $fail) {
                    if ($this->statusMasuk === 'Pindahan' && empty($value)) {
                        $fail('Alamat Sekolah Asal wajib diisi untuk siswa pindahan.');
                    }
                }
            ],
        ];
    }

    /**
     * Submit form registrasi masuk
     */
    public function submitRegistrasiMasuk(): void
    {
        $this->validate($this->rulesRegistrasiMasuk());

        if (!$this->siswaIdRegistrasiMasuk) {
            return;
        }

        try {
            $siswa = Siswa::findOrFail($this->siswaIdRegistrasiMasuk);

            // Update data registrasi masuk
            $siswa->update([
                'status_masuk' => $this->statusMasuk,
                'tanggal_masuk' => $this->tanggalMasuk,
                'asal_sekolah' => $this->statusMasuk === 'Pindahan' ? $this->asalSekolah : null,
                'alamat_sekolah_asal' => $this->statusMasuk === 'Pindahan' ? $this->alamatSekolahAsal : null,
            ]);

            $this->dispatch('toast', variant: 'sukses', message: "Registrasi masuk siswa {$siswa->nama} berhasil diperbarui.");

            $this->tutupModalRegistrasiMasuk();
            $this->clearSelection();
            $this->resetPage();
        } catch (\Exception $e) {
            ErrorHandlingService::handleInComponent($this, $e, 'memperbarui registrasi masuk');
        }
    }

    /**
     * Buka modal registrasi keluar untuk siswa yang dipilih
     *
     * Validasi:
     * - Minimal 1 siswa yang dipilih (BULK SUPPORT!)
     * - Semua siswa harus memiliki status_akademik = 'Aktif'
     */
    public function bukaModalRegistrasiKeluar(): void
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih minimal 1 siswa untuk registrasi keluar.');
            return;
        }

        // Validasi semua siswa harus aktif
        $siswas = Siswa::whereIn('id', $this->selectedIds)->get();
        $nonAktif = $siswas->where('status_akademik', '!=', 'Aktif');

        if ($nonAktif->count() > 0) {
            $names = $nonAktif->pluck('nama')->take(3)->implode(', ');
            $this->dispatch('toast', variant: 'error', message: "Beberapa siswa berstatus tidak aktif ({$names}...). Hanya siswa Aktif yang dapat diproses.");
            return;
        }

        // Set untuk bulk (tidak perlu siswaIdRegistrasiKeluar lagi)
        $this->showModalRegistrasiKeluar = true;
        $this->resetFormKeluar();
    }

    /**
     * Tutup modal registrasi keluar dan reset form
     */
    public function tutupModalRegistrasiKeluar(): void
    {
        $this->showModalRegistrasiKeluar = false;
        $this->resetFormKeluar();
    }

    /**
     * Reset semua field form registrasi keluar
     */
    private function resetFormKeluar(): void
    {
        $this->jenisKeluar = '';
        $this->tanggalKeluar = '';
        $this->tujuanSekolah = '';
        $this->keteranganKeluar = '';
        $this->resetErrorBag();
    }

    /**
     * Helper method untuk mengecek apakah siswa yang dipilih berstatus Aktif
     *
     * @return bool
     */
    public function getIsSelectedSiswaAktifProperty(): bool
    {
        if (count($this->selectedIds) !== 1) {
            return false;
        }

        // Cache result untuk menghindari multiple queries
        static $cache = [];
        $id = $this->selectedIds[0];

        if (!isset($cache[$id])) {
            $siswa = Siswa::select('id', 'status_akademik')->find($id);
            $cache[$id] = $siswa && $siswa->status_akademik === 'Aktif';
        }

        return $cache[$id];
    }

    /**
     * Validation rules untuk form registrasi keluar
     *
     * @return array
     */
    protected function rulesRegistrasiKeluar(): array
    {
        return [
            'jenisKeluar' => ['required', \App\Enums\JenisKeluar::validationRule()],
            'tanggalKeluar' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (!$this->siswaIdRegistrasiKeluar) {
                        return;
                    }

                    $siswa = Siswa::find($this->siswaIdRegistrasiKeluar);
                    $tanggalMasuk = $siswa?->tanggal_masuk;
                    if ($tanggalMasuk && $value < $tanggalMasuk->format('Y-m-d')) {
                        $fail('Tanggal Keluar tidak boleh lebih awal dari Tanggal Masuk.');
                    }
                }
            ],
            'tujuanSekolah' => [
                function ($attribute, $value, $fail) {
                    if ($this->jenisKeluar === 'Pindah Sekolah' && empty($value)) {
                        $fail('Tujuan Sekolah wajib diisi untuk jenis keluar Pindah Sekolah.');
                    }
                }
            ],
            'keteranganKeluar' => [
                function ($attribute, $value, $fail) {
                    if (in_array($this->jenisKeluar, ['Meninggal', 'Keluar']) && empty($value)) {
                        $jenis = $this->jenisKeluar === 'Meninggal' ? 'Meninggal' : 'Keluar (DO)';
                        $fail("Keterangan wajib diisi untuk jenis keluar {$jenis}.");
                    }
                }
            ],
        ];
    }

    /**
     * Submit form registrasi keluar
     *
     * Validasi form dan buka modal konfirmasi jika valid
     */
    public function submitRegistrasiKeluar(): void
    {
        // Update validation untuk bulk - tanggalKeluar tidak perlu cek per siswa
        $this->validate([
            'jenisKeluar' => ['required', \App\Enums\JenisKeluar::validationRule()],
            'tanggalKeluar' => ['required', 'date'],
            'tujuanSekolah' => [
                function ($attribute, $value, $fail) {
                    if ($this->jenisKeluar === 'Pindah Sekolah' && empty($value)) {
                        $fail('Tujuan Sekolah wajib diisi untuk jenis keluar Pindah Sekolah.');
                    }
                }
            ],
            'keteranganKeluar' => [
                function ($attribute, $value, $fail) {
                    if (in_array($this->jenisKeluar, ['Meninggal', 'Keluar']) && empty($value)) {
                        $jenis = $this->jenisKeluar === 'Meninggal' ? 'Meninggal' : 'Keluar (DO)';
                        $fail("Keterangan wajib diisi untuk jenis keluar {$jenis}.");
                    }
                }
            ],
        ]);

        $this->showModalKonfirmasiKeluar = true;
    }

    /**
     * Tutup modal konfirmasi keluar
     */
    public function tutupModalKonfirmasiKeluar(): void
    {
        $this->showModalKonfirmasiKeluar = false;
    }

    public function prosesRegistrasiKeluar(\App\Services\MasterData\SiswaService $service): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        try {
            $count = 0;
            $errors = [];

            foreach ($this->selectedIds as $siswaId) {
                try {
                    $service->registrasiKeluar(
                        $siswaId,
                        $this->jenisKeluar,
                        $this->tanggalKeluar,
                        $this->keteranganKeluar
                    );
                    $count++;
                } catch (\Exception $e) {
                    $siswa = Siswa::find($siswaId);
                    $errors[] = ($siswa ? $siswa->nama : "ID {$siswaId}") . ': ' . $e->getMessage();
                }
            }

            if ($count > 0) {
                $this->dispatch('toast', variant: 'sukses', message: "{$count} siswa berhasil diproses registrasi keluar.");
            }

            if (!empty($errors)) {
                $errorMsg = 'Beberapa siswa gagal: ' . implode('; ', array_slice($errors, 0, 3));
                $this->dispatch('toast', variant: 'error', message: $errorMsg);
            }

            $this->tutupModalKonfirmasiKeluar();
            $this->tutupModalRegistrasiKeluar();
            $this->clearSelection();
            $this->resetPage();
        } catch (\Exception $e) {
            ErrorHandlingService::handleInComponent($this, $e, 'memproses registrasi keluar');
        }
    }

    // ─── Modal Konfirmasi Hapus ───────────────────────────────────────────────

    /**
     * Cek apakah siswa yang dipilih valid untuk ditempatkan ke kelompok:
     * - Minimal 1 siswa dipilih
     * - Tidak ada siswa yang telah Lulus Program Tahfidz
     * - Tidak ada siswa yang sudah ditempatkan di kelompok
     */
    public function getCanPlaceKelompokProperty(): bool
    {
        if (empty($this->selectedIds)) {
            return false;
        }

        $siswas = Siswa::whereIn('id', $this->selectedIds)->get();

        // 1. Siswa lulus tidak dapat ditempatkan
        $hasLulus = $siswas->contains(function ($s) {
            return $s->status_tahfidz === 'Lulus Program Tahfidz' || $s->status_tahfidz === 'Lulus';
        });

        if ($hasLulus) {
            return false;
        }

        // 2. Siswa yang sudah punya kelompok tidak dapat ditempatkan lagi
        $alreadyPlaced = $siswas->contains(function ($s) {
            return !empty($s->kelompok_id) || $s->status_penempatan === 'Ditempatkan';
        });

        if ($alreadyPlaced) {
            return false;
        }

        return true;
    }

    /**
     * Buka modal bulk penempatan
     *
     * Validasi:
     * - Minimal 1 siswa dipilih
     * - Tidak ada siswa yang telah lulus program tahfidz
     * - Semua siswa harus belum ditempatkan (kelompok_id = null)
     * - Check kapasitas kelompok
     */
    public function bukaModalBulkPenempatan(): void
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih minimal 1 siswa untuk ditempatkan ke kelompok.');
            return;
        }

        $siswas = Siswa::whereIn('id', $this->selectedIds)->get();

        // Validasi 1: Siswa yang lulus program tahfidz tidak dapat ditempatkan
        $lulusSiswa = $siswas->filter(function ($s) {
            return $s->status_tahfidz === 'Lulus Program Tahfidz' || $s->status_tahfidz === 'Lulus';
        });

        if ($lulusSiswa->count() > 0) {
            $names = $lulusSiswa->pluck('nama')->take(3)->implode(', ');
            $this->dispatch('toast', variant: 'error', message: "Siswa yang telah lulus program tahfidz ({$names}) tidak dapat ditempatkan ke kelompok lagi.");
            return;
        }

        // Validasi 2: Semua siswa harus belum ditempatkan
        $sudahDitempatkan = $siswas->whereNotNull('kelompok_id');

        if ($sudahDitempatkan->count() > 0) {
            $names = $sudahDitempatkan->pluck('nama')->take(3)->implode(', ');
            $suffix = $sudahDitempatkan->count() > 3 ? '...' : '';
            $count = $sudahDitempatkan->count();

            $this->dispatch('toast', variant: 'error', message: "{$count} siswa sudah ditempatkan di kelompok ({$names}{$suffix}). Hanya siswa yang belum ditempatkan yang bisa diproses bulk.");
            return;
        }

        $this->bulkKelompokId = '';
        $this->bulkTanggalPenempatan = now()->format('Y-m-d'); // Set default to today
        $this->showModalBulkPenempatan = true;
    }

    /**
     * Tutup modal bulk penempatan
     */
    public function tutupModalBulkPenempatan(): void
    {
        $this->showModalBulkPenempatan = false;
        $this->bulkKelompokId = '';
        $this->bulkTanggalPenempatan = '';
    }

    /**
     * Proses bulk penempatan siswa ke kelompok
     */
    public function prosesBulkPenempatan(\App\Services\MasterData\SiswaService $siswaService): void
    {
        $this->validate([
            'bulkKelompokId' => 'required|exists:kelompoks,id',
            'bulkTanggalPenempatan' => 'required|date|date_format:Y-m-d',
        ], [
            'bulkKelompokId.required' => 'Kelompok wajib dipilih',
            'bulkKelompokId.exists' => 'Kelompok tidak valid',
            'bulkTanggalPenempatan.required' => 'Tanggal penempatan wajib diisi',
            'bulkTanggalPenempatan.date' => 'Format tanggal tidak valid',
            'bulkTanggalPenempatan.date_format' => 'Format tanggal harus Y-m-d',
        ]);

        if (empty($this->selectedIds)) {
            return;
        }

        try {
            $adminId = auth()->guard('admin')->id();

            $siswaService->bulkPenempatan(
                $this->selectedIds,
                $this->bulkKelompokId,
                $this->bulkTanggalPenempatan,
                $adminId
            );

            $count = count($this->selectedIds);
            $this->dispatch('toast', variant: 'sukses', message: "{$count} siswa berhasil ditempatkan ke kelompok.");

            $this->tutupModalBulkPenempatan();
            $this->clearSelection();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: $e->getMessage());
        }
    }

    /**
     * Render komponen dengan daftar siswa, pencarian, dan filter
     */
    public function render(\App\Services\MasterData\SiswaQueryService $queryService, \App\Services\MasterData\SiswaService $siswaService): \Illuminate\View\View
    {
        $siswas = $queryService->getFilteredSiswas([
            'search' => $this->search,
            'status_akademik' => $this->filterStatusAkademik,
            'status_tahfidz' => $this->filterStatusTahfidz,
            'status_penempatan' => $this->filterStatusPenempatan,
            'jenis_kelamin' => $this->filterJenisKelamin,
            'kelas_id' => $this->filterKelas,
        ])->paginate(20);

        // Get active tahun ajaran
        $activeTahunAjaran = TahunAjaran::where('status', true)->first();

        // Get kelas for filter
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        // Siswa untuk Registrasi Keluar
        $siswaRegistrasiKeluar = $this->siswaIdRegistrasiKeluar ? Siswa::find($this->siswaIdRegistrasiKeluar) : null;

        // Kelompok untuk bulk penempatan - SMART FILTERING
        $kelompokListBulk = collect();
        $tingkatSiswa = []; // Map siswa_id => tingkat terakhir

        if (!empty($this->selectedIds)) {
            // Dapatkan rekomendasi tingkat dari Service
            $tingkatSiswa = $siswaService->getSiswaTingkatRecommendations($this->selectedIds);

            // Tentukan tingkat kelompok yang harus ditampilkan
            $uniqueTingkat = array_values(array_unique(array_column($tingkatSiswa, 'target_tingkat')));

            // Jika semua siswa memiliki target tingkat yang sama, filter kelompok
            if (count($uniqueTingkat) === 1 && $uniqueTingkat[0]) {
                $kelompokListBulk = Kelompok::where('tingkat', $uniqueTingkat[0])
                    ->when($activeTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $activeTahunAjaran->id))
                    ->withCount('siswas')
                    ->with('guruPembimbing')
                    ->orderBy('jenis')
                    ->orderBy('nama')
                    ->get();
            } else {
                // Siswa memiliki target tingkat yang berbeda-beda, tampilkan semua kelompok
                $kelompokListBulk = Kelompok::when($activeTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $activeTahunAjaran->id))
                    ->withCount('siswas')
                    ->with('guruPembimbing')
                    ->orderBy('tingkat')
                    ->orderBy('jenis')
                    ->orderBy('nama')
                    ->get();
            }
        }

        return view('livewire.admin.master-data.siswa.index-siswa', [
            'siswas'                     => $siswas,
            'statusAkademikOptions'      => StatusAkademik::options(),
            'statusTahfidzOptions'       => StatusTahfidz::options(),
            'statusPenempatanOptions'    => StatusPenempatan::options(),
            'jenisKelaminOptions'        => JenisKelamin::options(),
            'kelasList'                  => $kelasList,
            'siswaRegistrasiKeluar'      => $siswaRegistrasiKeluar,
            'kelompokListBulk'           => $kelompokListBulk,
            'tingkatSiswa'               => $tingkatSiswa,
        ]);
    }
}
