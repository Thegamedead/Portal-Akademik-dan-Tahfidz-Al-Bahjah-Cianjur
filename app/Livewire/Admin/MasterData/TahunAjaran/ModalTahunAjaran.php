<?php

namespace App\Livewire\Admin\MasterData\TahunAjaran;

use App\Models\TahunAjaran;
use App\Services\MasterData\TahunAjaranService;
use Livewire\Component;

class ModalTahunAjaran extends Component
{
    // ─── Properties ──────────────────────────────────────────────────────────

    public bool $showModal = false;
    public ?int $tahunAjaranId = null;

    /** Tahun mulai, misal 2025 → otomatis jadi TA 2025/2026 */
    public int $tahun_mulai = 2025;

    /** Apakah langsung dijadikan aktif saat disimpan */
    public bool $langsung_aktif = true;

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    protected $listeners = ['bukaModalTahunAjaran' => 'openModal'];

    public function mount(): void
    {
        $this->tahun_mulai = now()->year;
    }

    // ─── Modal Management ─────────────────────────────────────────────────────

    /**
     * Buka modal (create jika $id=null, edit jika $id diisi)
     */
    public function openModal(?int $id = null): void
    {
        $this->reset(['tahunAjaranId']);
        $this->resetValidation();
        $this->langsung_aktif = true;

        if ($id) {
            $this->tahunAjaranId = $id;
            $ta = TahunAjaran::findOrFail($id);
            // Ambil tahun mulai dari tanggal_mulai
            $this->tahun_mulai = $ta->tanggal_mulai->year;
            $this->langsung_aktif = $ta->status;
        } else {
            $this->tahun_mulai = now()->year;
        }

        $this->showModal = true;
    }

    public function tutupModal(): void
    {
        $this->showModal = false;
        $this->reset(['tahunAjaranId']);
        $this->resetValidation();
    }

    // ─── Save ─────────────────────────────────────────────────────────────────

    public function save(TahunAjaranService $tahunAjaranService): void
    {
        $this->validate([
            'tahun_mulai'    => ['required', 'integer', 'min:2000', 'max:2099'],
            'langsung_aktif' => ['boolean'],
        ], [
            'tahun_mulai.required' => 'Tahun ajaran wajib diisi.',
            'tahun_mulai.integer'  => 'Tahun harus berupa angka bulat.',
            'tahun_mulai.min'      => 'Tahun tidak valid (minimal 2000).',
            'tahun_mulai.max'      => 'Tahun tidak valid (maksimal 2099).',
        ]);

        // Validasi duplikat label
        $label = $this->tahun_mulai . '/' . ($this->tahun_mulai + 1);
        $duplikat = \App\Models\TahunAjaran::where('label', $label)
            ->when($this->tahunAjaranId, fn($q) => $q->where('id', '!=', $this->tahunAjaranId))
            ->exists();

        if ($duplikat) {
            $this->addError('tahun_mulai', "Tahun Ajaran {$label} sudah ada.");
            return;
        }

        try {
            if ($this->tahunAjaranId) {
                // Edit: regenerate label & tanggal dari tahun baru
                $tahunAjaranService->update($this->tahunAjaranId, [
                    'label'           => $this->tahun_mulai . '/' . ($this->tahun_mulai + 1),
                    'tanggal_mulai'   => $this->tahun_mulai . '-07-01',
                    'tanggal_selesai' => ($this->tahun_mulai + 1) . '-06-30',
                    'status'          => $this->langsung_aktif,
                ]);
                $message = 'Tahun Ajaran berhasil diperbarui.';
            } else {
                // Create: cukup dari tahun
                $tahunAjaranService->createFromYear($this->tahun_mulai, $this->langsung_aktif);
                $message = 'Tahun Ajaran ' . $this->tahun_mulai . '/' . ($this->tahun_mulai + 1) . ' berhasil dibuat.';
            }

            $this->dispatch('tahunAjaranSaved');
            $this->dispatch('tahun-ajaran-changed');
            $this->dispatch('toast', variant: 'sukses', message: $message);
            $this->tutupModal();

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Teruskan ke form agar error ditampilkan
            foreach ($e->errors() as $field => $msgs) {
                $this->addError($field, $msgs[0]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: $e->getMessage());
        }
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        // Buat opsi tahun: 3 tahun ke belakang s/d 3 tahun ke depan
        $currentYear = now()->year;
        $tahunOptions = [];
        for ($y = $currentYear - 3; $y <= $currentYear + 3; $y++) {
            $tahunOptions[$y] = $y; // Hanya tampilkan 2024, 2025, 2026 dst
        }

        return view('livewire.admin.master-data.tahun-ajaran.modal-tahun-ajaran', [
            'tahunOptions' => $tahunOptions,
        ]);
    }
}
