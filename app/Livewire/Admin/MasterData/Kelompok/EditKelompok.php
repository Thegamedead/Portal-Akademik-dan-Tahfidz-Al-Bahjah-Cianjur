<?php

namespace App\Livewire\Admin\MasterData\Kelompok;

use App\Models\Guru;
use App\Models\Kelompok;
use App\Services\MasterData\KelompokService;
use App\Rules\ValidGuruPembimbingTahfidz;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;

#[Layout('admin.layouts.app')]
class EditKelompok extends Component
{
    // ─── Kelompok Properties ──────────────────────────────────────────────────

    public $kelompokId;
    public $nama = '';
    public $jenis = '';
    public $tingkat = '';
    public $guru_pembimbing_id = '';

    // ─── Guru Pembimbing Change Tracking ───────────────────────────────────────

    public $originalGuruPembimbingId;
    public $showConfirmationDialog = false;
    public $alasanPerubahanGuru = '';

    // ─── Dropdown Data ─────────────────────────────────────────────────────────

    public $pembimbingTahfidzList = [];

    // ─── Validation Rules ──────────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'nama' => 'required|string|max:100',
            'jenis' => 'required|in:Banin,Banat',
            'tingkat' => 'required|in:Dasar,Menengah,Atas',
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
     * Mount component - load kelompok data and dropdown data
     */
    public function mount(int $id): void
    {
        $this->kelompokId = $id;

        // Load kelompok data
        $kelompok = Kelompok::findOrFail($id);

        // Fill component properties
        $this->nama = $kelompok->nama;
        $this->jenis = $kelompok->jenis;
        $this->tingkat = $kelompok->tingkat;
        $this->guru_pembimbing_id = $kelompok->guru_pembimbing_id;

        // Store original guru pembimbing ID for comparison
        $this->originalGuruPembimbingId = $kelompok->guru_pembimbing_id;

        // Load Guru with jabatan Pembimbing Tahfidz only
        $this->pembimbingTahfidzList = Guru::where('jabatan', 'Guru Pembimbing Tahfidz')
            ->where('status_aktif', true)
            ->orderBy('nama')
            ->get(['id', 'nama', 'niy']);
    }

    // ─── Update Method ─────────────────────────────────────────────────────────

    /**
     * Initiate update - check if guru pembimbing changed
     */
    public function initiateUpdate()
    {
        // Validate all fields first
        $this->validate();

        // Check if guru pembimbing has changed
        if ($this->guru_pembimbing_id != $this->originalGuruPembimbingId) {
            // Show confirmation dialog
            $this->showConfirmationDialog = true;
        } else {
            // No guru pembimbing change, proceed with update
            $this->performUpdate();
        }
    }

    /**
     * Cancel confirmation dialog
     */
    public function cancelConfirmation()
    {
        $this->showConfirmationDialog = false;
        $this->alasanPerubahanGuru = '';
    }

    /**
     * Confirm guru pembimbing change and proceed with update
     */
    public function confirmUpdate()
    {
        // Validate alasan if guru pembimbing changed
        if (empty($this->alasanPerubahanGuru)) {
            $this->dispatch('toast', variant: 'error', message: 'Alasan perubahan guru pembimbing wajib diisi');
            return;
        }

        $this->showConfirmationDialog = false;
        $this->performUpdate($this->alasanPerubahanGuru);
    }

    /**
     * Perform the actual update
     */
    private function performUpdate(?string $alasanPerubahanGuru = null)
    {
        try {
            // Prepare kelompok data
            $kelompokData = [
                'nama' => $this->nama,
                'jenis' => $this->jenis,
                'tingkat' => $this->tingkat,
                'guru_pembimbing_id' => $this->guru_pembimbing_id,
            ];

            // Update kelompok via service
            $kelompokService = app(KelompokService::class);
            $kelompokService->update($this->kelompokId, $kelompokData, $alasanPerubahanGuru);

            // Clear alasan after successful update
            $this->alasanPerubahanGuru = '';

            // Dispatch success toast
            $this->dispatch('toast', variant: 'sukses', message: 'Kelompok berhasil diperbarui');

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
        return view('livewire.admin.master-data.kelompok.edit-kelompok');
    }
}
