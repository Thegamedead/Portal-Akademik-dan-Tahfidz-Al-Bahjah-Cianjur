<?php

namespace App\Observers;

use App\Models\ParameterKonfigurasi;
use App\Services\TranskripService;
use Illuminate\Support\Facades\Log;

class ParameterKonfigurasiObserver
{
    /**
     * TranskripService instance
     *
     * @var TranskripService
     */
    protected TranskripService $transkripService;

    /**
     * Constructor
     *
     * @param TranskripService $transkripService
     */
    public function __construct(TranskripService $transkripService)
    {
        $this->transkripService = $transkripService;
    }

    /**
     * Handle the ParameterKonfigurasi "created" event.
     */
    public function created(ParameterKonfigurasi $parameterKonfigurasi): void
    {
        //
    }

    /**
     * Handle the ParameterKonfigurasi "updating" event.
     * This fires BEFORE the model is saved, allowing us to capture old values before the change.
     */
    public function updating(ParameterKonfigurasi $parameterKonfigurasi): void
    {
        // Check if target_kelulusan changed
        if ($parameterKonfigurasi->isDirty('target_kelulusan')) {
            $oldValue = $parameterKonfigurasi->getOriginal('target_kelulusan');
            $newValue = $parameterKonfigurasi->target_kelulusan;

            Log::info('Parameter target_kelulusan changing', [
                'old_value' => $oldValue,
                'new_value' => $newValue,
            ]);

            // Create snapshot for all active students BEFORE the parameter change is saved
            try {
                $snapshotCount = $this->transkripService->createSnapshot('target_kelulusan_change');

                Log::info('Snapshot created before target_kelulusan change', [
                    'snapshot_count' => $snapshotCount,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                ]);

                // Log to activity log (using spatie/laravel-activitylog if installed)
                // Package will be installed when audit log module is implemented
                if (class_exists('Spatie\\Activitylog\\Facades\\Activity')) {
                    \Spatie\Activitylog\Facades\Activity::log('Snapshot created before target_kelulusan change')
                        ->performedOn($parameterKonfigurasi)
                        ->withProperties([
                            'old_value' => $oldValue,
                            'new_value' => $newValue,
                            'snapshot_count' => $snapshotCount,
                        ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to create snapshot before target_kelulusan change', [
                    'error' => $e->getMessage(),
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                ]);
            }
        }

        // Check if batas_tingkat changed
        if ($parameterKonfigurasi->isDirty('batas_tingkat')) {
            $oldValue = $parameterKonfigurasi->getOriginal('batas_tingkat');
            $newValue = $parameterKonfigurasi->batas_tingkat;

            Log::info('Parameter batas_tingkat changing', [
                'old_value' => $oldValue,
                'new_value' => $newValue,
            ]);

            // Create snapshot for all active students BEFORE the parameter change is saved
            try {
                $snapshotCount = $this->transkripService->createSnapshot('batas_tingkat_change');

                Log::info('Snapshot created before batas_tingkat change', [
                    'snapshot_count' => $snapshotCount,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                ]);

                // Log to activity log (using spatie/laravel-activitylog if installed)
                // Package will be installed when audit log module is implemented
                if (class_exists('Spatie\\Activitylog\\Facades\\Activity')) {
                    \Spatie\Activitylog\Facades\Activity::log('Snapshot created before batas_tingkat change')
                        ->performedOn($parameterKonfigurasi)
                        ->withProperties([
                            'old_value' => $oldValue,
                            'new_value' => $newValue,
                            'snapshot_count' => $snapshotCount,
                        ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to create snapshot before batas_tingkat change', [
                    'error' => $e->getMessage(),
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                ]);
            }
        }
    }

    /**
     * Handle the ParameterKonfigurasi "updated" event.
     * This fires AFTER the model is saved.
     * Note: Snapshot creation happens in updating() event, not here.
     */
    public function updated(ParameterKonfigurasi $parameterKonfigurasi): void
    {
        // Snapshot creation moved to updating() event to capture old parameter values
    }

    /**
     * Handle the ParameterKonfigurasi "deleted" event.
     */
    public function deleted(ParameterKonfigurasi $parameterKonfigurasi): void
    {
        //
    }

    /**
     * Handle the ParameterKonfigurasi "restored" event.
     */
    public function restored(ParameterKonfigurasi $parameterKonfigurasi): void
    {
        //
    }

    /**
     * Handle the ParameterKonfigurasi "force deleted" event.
     */
    public function forceDeleted(ParameterKonfigurasi $parameterKonfigurasi): void
    {
        //
    }
}

