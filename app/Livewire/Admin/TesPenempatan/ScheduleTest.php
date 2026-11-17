<?php

namespace App\Livewire\Admin\TesPenempatan;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Siswa;
use App\Models\Guru;
use App\Services\TesPenempatan\TesPenempatanService;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ScheduleTest extends Component
{
    public $siswa_id = '';
    public $guru_penguji_id = '';
    public $tanggal_tes = '';
    public $waktu_tes = '';

    public bool $show = false;

    protected function rules()
    {
        return [
            'siswa_id' => 'required|exists:siswas,id',
            'guru_penguji_id' => 'required|exists:gurus,id',
            'tanggal_tes' => 'required|date|after_or_equal:today',
            'waktu_tes' => 'nullable|date_format:H:i',
        ];
    }

    #[On('openScheduleModal')]
    public function open($siswaId = null)
    {
        $this->reset(['siswa_id', 'guru_penguji_id', 'tanggal_tes', 'waktu_tes']);
        if ($siswaId) {
            $this->siswa_id = $siswaId;
        }
        $this->tanggal_tes = Carbon::today()->format('Y-m-d');
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
    }

    public function submit(TesPenempatanService $service)
    {
        $this->validate();

        try {
            $service->scheduleTest(
                $this->siswa_id,
                $this->guru_penguji_id,
                $this->tanggal_tes,
                $this->waktu_tes ?: null
            );

            $this->dispatch('toast', variant: 'success', message: 'Tes penempatan berhasil dijadwalkan.');
            $this->dispatch('testScheduled');
            $this->close();
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'general' => $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        $siswas = Siswa::where('status_penempatan', 'Belum Ditempatkan')
            ->where('punya_hafalan_sebelumnya', true)
            ->orderBy('nama')
            ->get();

        $gurus = Guru::where('status_aktif', true)->orderBy('nama')->get();

        return view('livewire.admin.tes-penempatan.schedule-test', [
            'siswas' => $siswas,
            'gurus' => $gurus,
        ]);
    }
}
