<?php

namespace App\Livewire\Admin;

use App\Models\FeatureToggle;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\ParameterKonfigurasi;
use App\Models\Sekolah;
use App\Services\MasterData\TahunAjaranService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

#[Layout('admin.layouts.app')]
class PengaturanAdmin extends Component
{
    use WithFileUploads;

    // Section 2: Set Existing Year Active
    public ?int $selectedExistingYear = null;

    // Section 3: Create New Year
    public string $newStartYear = '';

    // Delete confirmation
    public bool $showDeleteConfirm = false;
    public ?int $tahunAjaranIdToDelete = null;

    // Properties for Branding
    public $logo;
    public $existingLogo;

    public function mount()
    {
        // Auto-add columns if they don't exist (fallback for environments without CLI)
        if (!Schema::hasColumn('sekolahs', 'logo')) {
            Schema::table('sekolahs', function (Blueprint $table) {
                $table->string('logo')->nullable();
            });
        }
        if (!Schema::hasColumn('parameter_konfigurasis', 'komponen_penilaian')) {
            Schema::table('parameter_konfigurasis', function (Blueprint $table) {
                $table->json('komponen_penilaian')->nullable();
            });
            // Update existing record with defaults
            $config = ParameterKonfigurasi::first();
            if ($config) {
                $config->komponen_penilaian = ['skor_tajwid', 'skor_kelancaran', 'skor_makhraj', 'skor_fashahah'];
                $config->save();
            }
        }

        $config = ParameterKonfigurasi::first();

        $sekolah = Sekolah::first();
        if ($sekolah) {
            $this->existingLogo = $sekolah->logo;
        }
    }

    public function toggleFeature($key)
    {
        $feature = FeatureToggle::where('key', $key)->first();
        if ($feature) {
            $feature->is_enabled = !$feature->is_enabled;
            $feature->save();

            $status = $feature->is_enabled ? 'diaktifkan' : 'dinonaktifkan';
            $this->dispatch('toast', variant: 'sukses', message: "Fitur {$feature->name} berhasil {$status}.");
        }
    }

    /**
     * Section 2: Set an existing tahun ajaran as active
     */
    public function setActiveTahunAjaran(TahunAjaranService $service)
    {
        $this->validate([
            'selectedExistingYear' => 'required|exists:tahun_ajarans,id'
        ], [
            'selectedExistingYear.required' => 'Silakan pilih tahun ajaran.',
            'selectedExistingYear.exists' => 'Tahun ajaran tidak ditemukan.',
        ]);

        $tahunAjaran = TahunAjaran::findOrFail($this->selectedExistingYear);

        // Update status using service to ensure proper transaction
        $service->update($tahunAjaran->id, ['status' => true]);

        $this->selectedExistingYear = null;
        $this->dispatch('toast', variant: 'sukses', message: "Tahun Ajaran {$tahunAjaran->label} berhasil diaktifkan.");
        $this->dispatch('tahun-ajaran-changed'); // Trigger topbar reload
    }

    /**
     * Section 3: Create new tahun ajaran
     */
    public function generateTahunAjaran(TahunAjaranService $service)
    {
        $this->validate([
            'newStartYear' => 'required|numeric|min:2020|max:2100'
        ], [
            'newStartYear.required' => 'Tahun awal wajib diisi.',
            'newStartYear.numeric'  => 'Format tahun harus berupa angka.',
        ]);

        $start = (int) $this->newStartYear;

        try {
            // Gunakan fungsi createFromYear yang sudah disederhanakan
            $service->createFromYear($start, true);

            $this->newStartYear = '';
            $this->dispatch('toast', variant: 'sukses', message: "Tahun Ajaran {$start}/" . ($start + 1) . " & 2 Semester berhasil dibuat dan diaktifkan.");
            $this->dispatch('tahun-ajaran-changed'); // Trigger topbar reload
        } catch (\Illuminate\Validation\ValidationException $e) {
            foreach ($e->errors() as $field => $msgs) {
                $this->dispatch('toast', variant: 'error', message: $msgs[0]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: $e->getMessage());
        }
    }

    /**
     * Section 4: Delete tahun ajaran
     */
    public function confirmDelete(int $id)
    {
        $this->tahunAjaranIdToDelete = $id;
        $this->showDeleteConfirm = true;
    }

    public function cancelDelete()
    {
        $this->tahunAjaranIdToDelete = null;
        $this->showDeleteConfirm = false;
    }

    public function deleteTahunAjaran(TahunAjaranService $service)
    {
        if (!$this->tahunAjaranIdToDelete) {
            return;
        }

        try {
            $tahunAjaran = TahunAjaran::findOrFail($this->tahunAjaranIdToDelete);
            $label = $tahunAjaran->label;

            $service->delete($this->tahunAjaranIdToDelete);

            $this->dispatch('toast', variant: 'sukses', message: "Tahun Ajaran {$label} berhasil dihapus.");
            $this->dispatch('tahun-ajaran-changed'); // Trigger topbar reload

            $this->cancelDelete();
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: $e->getMessage());
            $this->cancelDelete();
        }
    }

    public function setActiveSemester(int $semesterId)
    {
        Semester::query()->update(['status_aktif' => false]);
        Semester::where('id', $semesterId)->update(['status_aktif' => true]);
        $this->dispatch('toast', variant: 'sukses', message: "Semester Aktif berhasil diperbarui.");
    }


    public function updatedLogo()
    {
        $this->saveBranding();
    }


    public function saveBranding()
    {
        $this->validate([
            'logo' => 'nullable|image|max:2048', // max 2MB
        ]);

        $sekolah = Sekolah::first() ?? new Sekolah();

        if ($this->logo) {
            if ($sekolah->logo && \Storage::disk('public')->exists($sekolah->logo)) {
                \Storage::disk('public')->delete($sekolah->logo);
            }
            $path = $this->logo->store('branding', 'public');
            $sekolah->logo = $path;
            $this->existingLogo = $path;
        }

        $sekolah->save();
        $this->logo = null;

        $logoUrl = $sekolah->logo ? \Storage::url($sekolah->logo) : '';
        $this->dispatch('logo-updated', url: $logoUrl);
        $this->dispatch('toast', variant: 'sukses', message: 'Identitas berhasil diperbarui.');
    }



    public function render()
    {
        $features = FeatureToggle::all();
        $activeTa = TahunAjaran::with('semesters')->where('status', true)->first();
        $allTahunAjaran = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();

        return view('livewire.admin.pengaturan-admin', [
            'features' => $features,
            'activeTa' => $activeTa,
            'allTahunAjaran' => $allTahunAjaran,
        ])->title('Pengaturan');
    }
}
