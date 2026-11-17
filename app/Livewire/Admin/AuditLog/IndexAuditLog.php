<?php

namespace App\Livewire\Admin\AuditLog;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Carbon;

#[Layout('admin.layouts.app')]
#[Title('Log Aktivitas')]
class IndexAuditLog extends Component
{
    use WithPagination;

    public string $filterLogName = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';
    public string $filterCauserType = '';
    public string $filterSearch = '';

    public ?int $detailActivityId = null;
    public bool $showDetailModal = false;

    protected $queryString = [
        'filterLogName'    => ['except' => ''],
        'filterDateFrom'   => ['except' => ''],
        'filterDateTo'     => ['except' => ''],
        'filterCauserType' => ['except' => ''],
        'filterSearch'     => ['except' => ''],
    ];

    public function updatedFilterLogName(): void    { $this->resetPage(); }
    public function updatedFilterDateFrom(): void   { $this->resetPage(); }
    public function updatedFilterDateTo(): void     { $this->resetPage(); }
    public function updatedFilterCauserType(): void { $this->resetPage(); }
    public function updatedFilterSearch(): void     { $this->resetPage(); }

    public function showDetail(int $id): void
    {
        $this->detailActivityId = $id;
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->detailActivityId = null;
    }

    public function clearAllLogs(): void
    {
        Activity::truncate();
        $this->dispatch('toast', variant: 'sukses', message: 'Semua log aktivitas berhasil dibersihkan.');
        $this->resetPage();
    }

    public function render()
    {
        $query = Activity::query()
            ->with(['causer', 'subject'])
            ->when($this->filterLogName, fn($q) => $q->where('log_name', $this->filterLogName))
            ->when($this->filterDateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn($q) => $q->whereDate('created_at', '<=', $this->filterDateTo))
            ->when($this->filterCauserType, fn($q) => $q->where('causer_type', 'like', '%' . $this->filterCauserType . '%'))
            ->when($this->filterSearch, fn($q) => $q->where('description', 'like', '%' . $this->filterSearch . '%'))
            ->orderByDesc('created_at');

        $activities = $query->paginate(50);

        $detailActivity = null;
        if ($this->detailActivityId) {
            $detailActivity = Activity::find($this->detailActivityId);
        }

        $logNames = Activity::distinct()->pluck('log_name');

        return view('livewire.admin.audit-log.index-audit-log', [
            'activities'     => $activities,
            'detailActivity' => $detailActivity,
            'logNames'       => $logNames,
        ])->layout('admin.layouts.app', ['title' => 'Log Aktivitas Sistem']);
    }
}
