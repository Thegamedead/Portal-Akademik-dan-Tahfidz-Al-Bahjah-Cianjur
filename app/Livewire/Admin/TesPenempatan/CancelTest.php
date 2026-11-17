<?php

namespace App\Livewire\Admin\TesPenempatan;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\TesPenempatan;
use App\Services\TesPenempatan\TesPenempatanService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class CancelTest extends Component
{
    public $tesPenempatanId = null;
    public $alasan_pembatalan = '';
    public bool $show = false;
    
    public ?TesPenempatan $tes = null;

    protected $rules = [
        'alasan_pembatalan' => 'required|string|min:20',
    ];

    #[On('openCancelModal')]
    public function open($tesId)
    {
        $this->reset(['alasan_pembatalan']);
        $this->tesPenempatanId = $tesId;
        $this->tes = TesPenempatan::with('siswa')->find($tesId);
        
        if (!$this->tes || !$this->tes->canBeCancelled()) {
            $this->dispatch('toast', variant: 'error', message: 'Tes ini tidak dapat dibatalkan.');
            return;
        }
        
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
        $this->tes = null;
    }

    public function submit(TesPenempatanService $service)
    {
        $this->validate();

        try {
            $adminId = Auth::guard('admin')->id();
            
            $service->cancelTest(
                $this->tesPenempatanId,
                $adminId,
                $this->alasan_pembatalan
            );

            $this->dispatch('toast', variant: 'success', message: 'Tes penempatan berhasil dibatalkan.');
            $this->dispatch('testCancelled');
            $this->close();
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'general' => $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.tes-penempatan.cancel-test');
    }
}
