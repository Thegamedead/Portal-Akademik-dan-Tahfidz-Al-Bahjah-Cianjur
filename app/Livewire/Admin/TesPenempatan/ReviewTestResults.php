<?php

namespace App\Livewire\Admin\TesPenempatan;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\TesPenempatan;
use App\Services\TesPenempatan\TesPenempatanService;
use App\Services\TesPenempatan\BaselineCalculator;
use Illuminate\Support\Facades\Auth;

#[Layout('admin.layouts.app')]
#[Title('Review Hasil Tes')]
class ReviewTestResults extends Component
{
    public $tesPenempatanId;
    public $tes;
    public $baselineData = [];

    public $kelompok_id = '';
    public $selectedTingkat = '';
    public $tanggal_penempatan = '';
    public $availableKelompoks = [];

    public function mount($id, BaselineCalculator $calculator)
    {
        $this->tesPenempatanId = $id;
        $this->tes = TesPenempatan::with(['siswa', 'guruPenguji', 'hasilTes.surah', 'hasilTes.juz'])->findOrFail($id);

        if ($this->tes->status !== \App\Enums\StatusTesPenempatan::SELESAI) {
            abort(403, 'Tes ini belum selesai atau sudah dibatalkan.');
        }

        $this->selectedTingkat = $this->tes->rekomendasi_tingkat?->value ?? 'Dasar';
        $this->tanggal_penempatan = now()->format('Y-m-d');
        $this->baselineData = $calculator->calculateFromTestResults($id);

        $this->loadKelompoks();
    }

    public function updatedSelectedTingkat()
    {
        $this->loadKelompoks();
        $this->kelompok_id = ''; // reset selection
    }

    protected function loadKelompoks()
    {
        $service = app(TesPenempatanService::class);
        $this->availableKelompoks = $service->getAvailableKelompok($this->tes->siswa_id, $this->selectedTingkat);
    }

    public function submitPlacement(TesPenempatanService $service)
    {
        $this->validate([
            'tanggal_penempatan' => 'required|date',
            'kelompok_id' => 'nullable',
        ], [
            'tanggal_penempatan.required' => 'Tanggal penempatan wajib diisi.',
        ]);

        try {
            $adminId = Auth::guard('admin')->id();

            $service->placeStudent(
                $this->tes->id,
                $this->kelompok_id ?: null,
                $adminId,
                $this->tanggal_penempatan
            );

            session()->flash('toast', ['variant' => 'success', 'message' => 'Siswa berhasil ditempatkan.']);
            return redirect()->route('admin.tes-penempatan.index');
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: 'Gagal menempatkan siswa: ' . $e->getMessage());
        }
    }

    public function exportPdf()
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.tes-penempatan-result', [
            'tes' => $this->tes,
            'baselineData' => $this->baselineData
        ]);

        $fileName = 'Hasil_Tes_' . str_replace(' ', '_', $this->tes->siswa->nama) . '.pdf';

        $this->dispatch('download-file', [
            'content' => base64_encode($pdf->output()),
            'fileName' => $fileName,
            'mimeType' => 'application/pdf'
        ]);

        // Wait, app.blade.php handles download-file using a blob. But if we send base64, we have to decode it in JS.
        // Actually, Livewire's download response:
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName);
    }

    public function render()
    {
        return view('livewire.admin.tes-penempatan.review-test-results')->layout('admin.layouts.app', ['title' => 'Review & Penempatan Siswa']);
    }
}
