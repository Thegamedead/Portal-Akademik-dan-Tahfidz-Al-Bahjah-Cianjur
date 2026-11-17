<?php

namespace App\Livewire\Guru\TesPenempatan;

use App\Models\TesPenempatan;
use App\Models\Juz;
use App\Models\Surah;
use App\Services\TesPenempatan\TesPenempatanService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Gate;

#[Layout('guru.layouts.app')]
#[Title('Lakukan Tes Penempatan')]
class LakukanTes extends Component
{
    public $tesId;
    public $tes;
    public $siswa;

    // Form data
    public $hasil_hafalan = [];
    public $catatan_kualitas = '';
    public $rekomendasi_tingkat = '';

    // Options
    public $juzOptions = [];
    public $surahOptions = [];

    public function mount($id)
    {
        $this->tesId = $id;
        $this->tes = TesPenempatan::with(['siswa', 'guruPenguji'])->findOrFail($id);

        // Authorization: hanya guru penguji yang bisa akses
        if ($this->tes->guru_penguji_id !== auth('guru')->id()) {
            abort(403, 'Anda tidak memiliki akses ke tes ini.');
        }

        // Hanya tes dengan status Terjadwal yang bisa dilakukan
        if ($this->tes->status->value !== 'Terjadwal') {
            session()->flash('error', 'Tes ini sudah tidak dapat dilakukan (Status: ' . $this->tes->status->value . ')');
            return redirect()->route('guru.tes-penempatan.index');
        }

        $this->siswa = $this->tes->siswa;

        // Load Juz options
        $this->juzOptions = Juz::orderBy('juz_ke')->get();

        // Initialize with one empty hasil
        $this->hasil_hafalan = [
            [
                'juz_id' => '',
                'surah_id' => '',
                'ayat_mulai' => '',
                'ayat_selesai' => '',
            ]
        ];
    }

    public function updatedHasilHafalan($value, $key)
    {
        // Parse key: hasil_hafalan.0.juz_id -> [0, 'juz_id']
        $parts = explode('.', $key);
        $index = (int) $parts[0];
        $field = $parts[1] ?? null;

        // When juz_id changes, load surah options for that juz
        if ($field === 'juz_id' && !empty($this->hasil_hafalan[$index]['juz_id'])) {
            $this->loadSurahOptions($index, $this->hasil_hafalan[$index]['juz_id']);
            // Reset surah and ayat
            $this->hasil_hafalan[$index]['surah_id'] = '';
            $this->hasil_hafalan[$index]['ayat_mulai'] = '';
            $this->hasil_hafalan[$index]['ayat_selesai'] = '';
        }
    }

    public function loadSurahOptions($index, $juzId)
    {
        $juz = Juz::with(['surahs' => function ($q) {
            $q->orderBy('nomor_surah');
        }])->find($juzId);

        $this->surahOptions[$index] = $juz ? $juz->surahs : collect();
    }

    public function addHafalan()
    {
        $this->hasil_hafalan[] = [
            'juz_id' => '',
            'surah_id' => '',
            'ayat_mulai' => '',
            'ayat_selesai' => '',
        ];
    }

    public function removeHafalan($index)
    {
        unset($this->hasil_hafalan[$index]);
        $this->hasil_hafalan = array_values($this->hasil_hafalan); // Re-index array
    }

    protected function rules()
    {
        $rules = [
            'hasil_hafalan' => 'required|array|min:1',
            'hasil_hafalan.*.juz_id' => 'required|exists:juzs,id',
            'hasil_hafalan.*.surah_id' => 'required|exists:surahs,id',
            'hasil_hafalan.*.ayat_mulai' => 'required|integer|min:1',
            'hasil_hafalan.*.ayat_selesai' => 'required|integer|gte:hasil_hafalan.*.ayat_mulai',
            'catatan_kualitas' => 'required|string|min:10|max:1000',
            'rekomendasi_tingkat' => 'required|in:Dasar,Lanjutan,Mahir',
        ];

        return $rules;
    }

    protected function messages()
    {
        return [
            'hasil_hafalan.required' => 'Minimal harus ada satu hasil hafalan',
            'hasil_hafalan.*.juz_id.required' => 'Juz wajib dipilih',
            'hasil_hafalan.*.surah_id.required' => 'Surah wajib dipilih',
            'hasil_hafalan.*.ayat_mulai.required' => 'Ayat mulai wajib diisi',
            'hasil_hafalan.*.ayat_selesai.required' => 'Ayat selesai wajib diisi',
            'hasil_hafalan.*.ayat_selesai.gte' => 'Ayat selesai harus >= ayat mulai',
            'catatan_kualitas.required' => 'Catatan kualitas wajib diisi',
            'catatan_kualitas.min' => 'Catatan kualitas minimal 10 karakter',
            'catatan_kualitas.max' => 'Catatan kualitas maksimal 1000 karakter',
            'rekomendasi_tingkat.required' => 'Rekomendasi tingkat wajib dipilih',
        ];
    }

    public function submit(\App\Services\TesPenempatan\TesPenempatanService $service, \App\Services\SetoranService $setoranService)
    {
        $this->validate();

        foreach ($this->hasil_hafalan as $index => $hasil) {
            $validation = $setoranService->validateAyatRangeInJuz(
                (int) $hasil['juz_id'],
                (int) $hasil['surah_id'],
                (int) $hasil['ayat_mulai'],
                (int) $hasil['ayat_selesai']
            );

            if (!$validation['valid']) {
                $this->addError("hasil_hafalan.{$index}.ayat_selesai", $validation['message']);
                return;
            }
        }

        try {
            $service->submitTestResults(
                $this->tesId,
                $this->hasil_hafalan,
                $this->catatan_kualitas,
                $this->rekomendasi_tingkat
            );

            session()->flash('success', 'Hasil tes berhasil disimpan.');
            return redirect()->route('guru.tes-penempatan.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('guru.tes-penempatan.index');
    }

    public function getAyatRangeHints(): array
    {
        $hints = [];
        $setoranService = app(\App\Services\SetoranService::class);

        foreach ($this->hasil_hafalan as $index => $hasil) {
            if (!empty($hasil['juz_id']) && !empty($hasil['surah_id'])) {
                $hints[$index] = $setoranService->getAllowedAyatRange(
                    (int) $hasil['juz_id'],
                    (int) $hasil['surah_id']
                );
            } else {
                $hints[$index] = null;
            }
        }

        return $hints;
    }

    public function render()
    {
        return view('livewire.guru.tes-penempatan.lakukan-tes', [
            'ayatRangeHints' => $this->getAyatRangeHints(),
        ]);
    }
}
