<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;

/**
 * Audit Log Viewer (Admin only).
 * Supports Requirement 11.4: Admin audit log viewer.
 */
class AuditLogViewer extends Component
{
    use WithPagination;

    public $filters = [
        'causer_type' => '',
        'description' => '',
        'tanggal_mulai' => '',
        'tanggal_akhir' => '',
        'per_page' => 25,
    ];

    public function mount()
    {
        $this->filters['tanggal_mulai'] = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->filters['tanggal_akhir'] = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->filters = [
            'causer_type' => '',
            'description' => '',
            'tanggal_mulai' => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'tanggal_akhir' => Carbon::now()->endOfMonth()->format('Y-m-d'),
            'per_page' => 25,
        ];
        $this->resetPage();
    }

    public function render()
    {
        if (!class_exists(Activity::class)) {
            return view('livewire.admin.audit-log-viewer', [
                'logs' => collect(),
                'activityLogInstalled' => false,
            ])->layout('admin.layouts.app');
        }

        $query = Activity::with('causer')->latest();

        if (!empty($this->filters['causer_type'])) {
            $query->where('causer_type', 'like', '%' . $this->filters['causer_type'] . '%');
        }
        if (!empty($this->filters['description'])) {
            $query->where('description', 'like', '%' . $this->filters['description'] . '%');
        }
        if (!empty($this->filters['tanggal_mulai'])) {
            $query->whereDate('created_at', '>=', $this->filters['tanggal_mulai']);
        }
        if (!empty($this->filters['tanggal_akhir'])) {
            $query->whereDate('created_at', '<=', $this->filters['tanggal_akhir']);
        }

        $logs = $query->paginate($this->filters['per_page']);

        return view('livewire.admin.audit-log-viewer', [
            'logs' => $logs,
            'activityLogInstalled' => true,
        ])->layout('admin.layouts.app');
    }
}
