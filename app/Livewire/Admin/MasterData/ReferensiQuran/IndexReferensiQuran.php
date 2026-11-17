<?php

namespace App\Livewire\Admin\MasterData\ReferensiQuran;

use App\Models\ReferensiQuran;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('admin.layouts.app')]
class IndexReferensiQuran extends Component
{
    public $showModal = false;
    public $selectedJuz = null;
    public $detailSurahs = [];

    public function openDetail($juz_ke)
    {
        $juzData = ReferensiQuran::where('juz_ke', $juz_ke)->first();
        if (!$juzData) return;

        $this->selectedJuz = $juz_ke;
        $this->detailSurahs = [];

        for ($surah = $juzData->awal_surah; $surah <= $juzData->akhir_surah; $surah++) {
            $namaSurah = ReferensiQuran::getNamaSurah($surah);
            
            if ($surah == $juzData->awal_surah && $surah == $juzData->akhir_surah) {
                $awal = $juzData->awal_ayat;
                $akhir = $juzData->akhir_ayat;
            } elseif ($surah == $juzData->awal_surah) {
                $awal = $juzData->awal_ayat;
                $akhir = ReferensiQuran::getTotalAyatSurah($surah);
            } elseif ($surah == $juzData->akhir_surah) {
                $awal = 1;
                $akhir = $juzData->akhir_ayat;
            } else {
                $awal = 1;
                $akhir = ReferensiQuran::getTotalAyatSurah($surah);
            }
            
            $jumlah = ($akhir - $awal) + 1;

            $this->detailSurahs[] = [
                'nama' => $namaSurah,
                'awal' => $awal,
                'akhir' => $akhir,
                'jumlah' => $jumlah
            ];
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedJuz = null;
        $this->detailSurahs = [];
    }

    /**
     * Render the component.
     * Display 30 juz reference data in table format (read-only).
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $referensiQurans = ReferensiQuran::orderBy('juz_ke', 'asc')->get();

        return view('livewire.admin.master-data.referensi-quran.index-referensi-quran', [
            'referensiQurans' => $referensiQurans,
        ]);
    }
}
