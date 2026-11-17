<?php

namespace App\Livewire\Admin\MasterData\Kelompok;

use App\Models\Guru;
use App\Services\MasterData\KelompokService;
use App\Enums\JenisKelompok;
use App\Enums\TingkatKelompok;
use App\Rules\ValidGuruPembimbingTahfidz;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

#[Layout('admin.layouts.app')]
#[Title('Tambah Kelompok')]
class CreateKelompok extends Component
{
    // ─── Kelompok Properties ──────────────────────────────────────────────────

    public $nama = '';
    public $jenis = '';
    public $tingkat = '';
    public $guru_pembimbing_id = '';

    // ─── Dropdown Data ─────────────────────────────────────────────────────────

    public $pembimbingTahfidzList = [];

    // ─── Validation Rules ──────────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'nama' => 'required|string|max:100',
            'jenis' => ['required', JenisKelompok::validationRule()],
            'tingkat' => ['required', TingkatKelompok::validationRule()],
            'guru_pembimbing_id' => ['required', new ValidGuruPembimbingTahfidz],
        ];
    }

    protected function messages(): array
    {
        return [
            'nama.required' => 'Nama kelompok wajib diisi',
            'nama.max' => 'Nama kelompok maksimal 100 karakter',
            'jenis.required' => 'Jenis kelompok wajib dipilih',
            'jenis.in' => 'Jenis kelompok harus Banin atau Banat',
            'tingkat.required' => 'Tingkat kelompok wajib dipilih',
            'tingkat.in' => 'Tingkat kelompok harus Dasar, Menengah, atau Atas',
            'guru_pembimbing_id.required' => 'Guru pembimbing wajib dipilih',
        ];
    }

    // ─── Lifecycle Hooks ───────────────────────────────────────────────────────

    /**
     * Mount component - load dropdown data
     */
    public function mount(): void
    {
        // Load Guru with jabatan Pembimbing Tahfidz only
        $this->pembimbingTahfidzList = Guru::where('jabatan', 'Guru Pembimbing Tahfidz')
            ->where('status_aktif', true)
            ->orderBy('nama')
            ->get(['id', 'nama', 'niy']);
    }

    // ─── Save Method ───────────────────────────────────────────────────────────

    /**
     * Save new kelompok
     */
    public function save(KelompokService $kelompokService)
    {
        // Validate all fields
        $this->validate();

        try {
            // Prepare kelompok data
            $kelompokData = [
                'nama' => $this->nama,
                'jenis' => $this->jenis,
                'tingkat' => $this->tingkat,
                'guru_pembimbing_id' => $this->guru_pembimbing_id,
            ];

            // Create kelompok via service
            $kelompokService->create($kelompokData);

            // Dispatch success toast
            $this->dispatch('toast', variant: 'sukses', message: 'Kelompok berhasil dibuat');

            // Redirect to index
            return redirect()->route('admin.master-data.kelompok.index');

        } catch (ValidationException $e) {
            // Handle validation exceptions from service
            foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }

            $this->dispatch('toast', variant: 'error', message: 'Validasi gagal. Periksa kembali data yang diisi.');

        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Cancel and return to index
     */
    public function cancel()
    {
        return redirect()->route('admin.master-data.kelompok.index');
    }

    // ─── Render ────────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.master-data.kelompok.create-kelompok', [
            'jenisKelompokOptions' => JenisKelompok::options(),
            'tingkatKelompokOptions' => TingkatKelompok::options(),
        ]);
    }
}
