# Design Document: Integrated Year-End Rollover System

## Overview

The Integrated Year-End Rollover System is a critical administrative function that automates the complex transition between academic years in a dual-track educational system. Unlike the existing `RolloverModal.php` which only handles Tahfidz group clearing, this integrated system orchestrates five interconnected operations:

1. **Academic Year Activation**: Transitions the system to a new `Tahun_Pelajaran` period
2. **Academic Promotion**: Automatically advances students from Kelas 7→8→9
3. **Graduation Processing**: Marks Kelas 9 students as "Lulus SMP" with graduation dates
4. **Tahfidz Group Clearing**: Removes all Kelompok assignments for active students
5. **Historical Record Creation**: Creates `RiwayatKelompok` audit trail entries

### Key Design Principles

- **Atomicity**: All operations execute within a single database transaction—complete success or complete rollback
- **Idempotency**: Running rollover multiple times with same parameters produces consistent state
- **Auditability**: Complete tracking of what changed, when, and by whom
- **Performance**: Optimized bulk operations to handle 500+ students efficiently
- **Safety**: Pre-execution validation, confirmation workflows, and rollback capability

### Departure from Existing Implementation

The current `RolloverModal.php` is Tahfidz-focused and lacks:
- Academic promotion logic (Kelas progression)
- Graduation status handling
- Comprehensive validation
- Performance optimization for bulk updates
- Detailed audit logging
- Rollback capability

This design replaces and extends the existing implementation with a unified, production-grade system.

## Architecture

### Component Structure

```
┌─────────────────────────────────────────────────────────────┐
│                     Rollover Service Layer                   │
│                                                              │
│  ┌────────────────────────────────────────────────────┐    │
│  │  IntegratedRolloverService                         │    │
│  │  - executeRollover()                               │    │
│  │  - validatePreConditions()                         │    │
│  │  - calculateImpactSummary()                        │    │
│  │  - rollback()                                      │    │
│  └────────────────────────────────────────────────────┘    │
│                          │                                   │
│         ┌────────────────┼────────────────┐                │
│         │                │                │                │
│  ┌──────▼─────┐  ┌──────▼─────┐  ┌──────▼─────┐          │
│  │  Academic  │  │  Tahfidz   │  │   Audit    │          │
│  │ Progression│  │   Group    │  │   Logger   │          │
│  │  Handler   │  │  Handler   │  │            │          │
│  └────────────┘  └────────────┘  └────────────┘          │
└─────────────────────────────────────────────────────────────┘
                          │
┌─────────────────────────▼─────────────────────────────────┐
│                   Livewire UI Layer                        │
│                                                            │
│  ┌──────────────────────────────────────────────────┐    │
│  │  IntegratedRolloverModal                         │    │
│  │  - mount()                                       │    │
│  │  - loadImpactSummary()                          │    │
│  │  - confirmRollover()                            │    │
│  │  - executeRollback()                            │    │
│  └──────────────────────────────────────────────────┘    │
└────────────────────────────────────────────────────────────┘
                          │
┌─────────────────────────▼─────────────────────────────────┐
│                      Data Layer                            │
│                                                            │
│  ┌──────┐  ┌────────┐  ┌──────────┐  ┌───────────────┐  │
│  │Siswa │  │  Kelas │  │Kelompok  │  │RiwayatKelompok│  │
│  └──────┘  └────────┘  └──────────┘  └───────────────┘  │
│  ┌──────────────┐  ┌─────────────────────────────────┐  │
│  │TahunAjaran   │  │   RolloverAuditLog              │  │
│  └──────────────┘  └─────────────────────────────────┘  │
└────────────────────────────────────────────────────────────┘
```

### Layered Architecture

#### 1. Service Layer (`App\Services\IntegratedRolloverService`)

**Responsibilities:**
- Orchestrates the complete rollover workflow
- Enforces business rules and validation
- Manages database transactions
- Coordinates with handler classes
- Provides rollback functionality

**Key Methods:**
```php
public function executeRollover(int $targetTahunAjaranId, int $adminId): RolloverResult
public function validatePreConditions(int $targetTahunAjaranId): ValidationResult
public function calculateImpactSummary(int $targetTahunAjaranId): ImpactSummary
public function rollback(int $rolloverLogId, int $adminId): RollbackResult
```

#### 2. Handler Classes

**`AcademicProgressionHandler`**
- Handles Kelas promotion (7→8→9)
- Processes graduation (Kelas 9 → "Lulus SMP")
- Sets `tanggal_lulus_akademik`
- Returns counts of promoted/graduated students

**`TahfidzGroupHandler`**
- Clears `kelompok_id` for active students
- Updates `status_penempatan` to "Belum Ditempatkan"
- Creates bulk `RiwayatKelompok` records
- Returns counts of affected students

**`RolloverAuditLogger`**
- Creates `RolloverAuditLog` entries
- Logs pre-rollover state snapshot
- Records execution results
- Tracks rollback operations

#### 3. UI Layer (`App\Livewire\Admin\IntegratedRolloverModal`)

**Responsibilities:**
- Displays rollover confirmation dialog
- Shows impact summary (counts of affected students)
- Handles user confirmation/cancellation
- Displays progress indicators
- Shows success/error notifications
- Provides rollback interface

#### 4. Data Layer

**New Model: `RolloverAuditLog`**
```php
Schema::create('rollover_audit_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('initiated_by')->constrained('admins');
    $table->foreignId('source_tahun_ajaran_id')->constrained('tahun_ajarans');
    $table->foreignId('target_tahun_ajaran_id')->constrained('tahun_ajarans');
    $table->integer('kelas_7_to_8_count');
    $table->integer('kelas_8_to_9_count');
    $table->integer('graduated_count');
    $table->integer('kelompok_cleared_count');
    $table->enum('status', ['in_progress', 'completed', 'failed', 'rolled_back']);
    $table->text('error_message')->nullable();
    $table->timestamp('started_at');
    $table->timestamp('completed_at')->nullable();
    $table->foreignId('rolled_back_by')->nullable()->constrained('admins');
    $table->timestamp('rolled_back_at')->nullable();
    $table->timestamps();
});
```

### Transaction Management Strategy

All rollover operations execute within a single database transaction:

```php
DB::transaction(function () use ($targetTahunAjaranId, $adminId) {
    // 1. Acquire exclusive lock
    DB::select('SELECT GET_LOCK(?, 300)', ['rollover_operation']);
    
    // 2. Validate pre-conditions
    $this->validatePreConditions($targetTahunAjaranId);
    
    // 3. Create audit log entry (status: 'in_progress')
    $auditLog = RolloverAuditLog::create([...]);
    
    // 4. Deactivate all Tahun Ajaran
    TahunAjaran::query()->update(['status' => false]);
    
    // 5. Activate target Tahun Ajaran
    TahunAjaran::find($targetTahunAjaranId)->update(['status' => true]);
    
    // 6. Execute academic progression
    $academicResult = $this->academicHandler->processPromotion();
    
    // 7. Execute Tahfidz group clearing
    $tahfidzResult = $this->tahfidzHandler->clearGroups($adminId);
    
    // 8. Update audit log (status: 'completed')
    $auditLog->update([...]);
    
    // 9. Release lock
    DB::select('SELECT RELEASE_LOCK(?)', ['rollover_operation']);
}, 5); // 5 retry attempts
```

If any step fails, Laravel's transaction automatically rolls back all changes.

### Concurrency Control

**Problem:** Two admins attempting rollover simultaneously could cause data corruption.

**Solution:** Database-level advisory locking using MySQL's `GET_LOCK()`:

```php
// Acquire exclusive lock (timeout: 300 seconds)
$lockAcquired = DB::selectOne('SELECT GET_LOCK(?, ?) as locked', [
    'rollover_operation',
    300
])->locked;

if (!$lockAcquired) {
    throw new RolloverLockException(
        'Rollover sedang diproses oleh admin lain, silakan tunggu'
    );
}

// ... perform rollover ...

// Release lock
DB::select('SELECT RELEASE_LOCK(?)', ['rollover_operation']);
```

**Timeout Handling:**
- Lock expires after 5 minutes if process terminates abnormally
- Manual lock release available via artisan command: `php artisan rollover:release-lock`

## Components and Interfaces

### IntegratedRolloverService

**Location:** `app/Services/IntegratedRolloverService.php`

```php
<?php

namespace App\Services;

use App\Models\TahunAjaran;
use App\Models\Siswa;
use App\Models\RolloverAuditLog;
use App\Services\Handlers\AcademicProgressionHandler;
use App\Services\Handlers\TahfidzGroupHandler;
use App\Services\Handlers\RolloverAuditLogger;
use App\Exceptions\RolloverLockException;
use App\Exceptions\RolloverValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class IntegratedRolloverService
{
    public function __construct(
        private AcademicProgressionHandler $academicHandler,
        private TahfidzGroupHandler $tahfidzHandler,
        private RolloverAuditLogger $auditLogger
    ) {}

    /**
     * Execute the complete integrated rollover process
     *
     * @param int $targetTahunAjaranId The ID of the Tahun Ajaran to activate
     * @param int $adminId The ID of the admin initiating the rollover
     * @return RolloverResult Summary of rollover execution
     * @throws RolloverLockException If another rollover is in progress
     * @throws RolloverValidationException If validation fails
     */
    public function executeRollover(int $targetTahunAjaranId, int $adminId): RolloverResult;

    /**
     * Validate that rollover can be executed safely
     *
     * @param int $targetTahunAjaranId The target academic year
     * @return ValidationResult Contains validation status and error messages
     */
    public function validatePreConditions(int $targetTahunAjaranId): ValidationResult;

    /**
     * Calculate the impact of executing rollover (for confirmation dialog)
     *
     * @param int $targetTahunAjaranId The target academic year
     * @return ImpactSummary Counts of students affected by each operation
     */
    public function calculateImpactSummary(int $targetTahunAjaranId): ImpactSummary;

    /**
     * Rollback a completed rollover operation
     *
     * @param int $rolloverLogId The audit log entry to rollback
     * @param int $adminId The admin performing the rollback
     * @return RollbackResult Summary of rollback execution
     * @throws RolloverValidationException If rollback is not possible
     */
    public function rollback(int $rolloverLogId, int $adminId): RollbackResult;

    /**
     * Check if any rollover operation is currently in progress
     *
     * @return bool True if a rollover is running
     */
    public function isRolloverInProgress(): bool;

    /**
     * Acquire exclusive lock for rollover operation
     *
     * @return bool True if lock acquired successfully
     */
    private function acquireLock(): bool;

    /**
     * Release the rollover operation lock
     *
     * @return void
     */
    private function releaseLock(): void;
}
```

### AcademicProgressionHandler

**Location:** `app/Services/Handlers/AcademicProgressionHandler.php`

```php
<?php

namespace App\Services\Handlers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Enums\StatusAkademik;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class AcademicProgressionHandler
{
    /**
     * Process academic progression for all active students
     *
     * Promotes:
     * - Kelas 7 students → Kelas 8
     * - Kelas 8 students → Kelas 9
     * - Kelas 9 students → Status "Lulus SMP"
     *
     * @return ProgressionResult Contains counts and affected student IDs
     */
    public function processPromotion(): ProgressionResult;

    /**
     * Promote students from one Kelas to the next
     *
     * @param int $fromKelasId Source Kelas ID
     * @param int $toKelasId Target Kelas ID
     * @return int Count of students promoted
     */
    private function promoteKelas(int $fromKelasId, int $toKelasId): int;

    /**
     * Mark Kelas 9 students as graduated
     *
     * @param int $kelas9Id The Kelas 9 ID
     * @param Carbon $graduationDate The graduation date to record
     * @return int Count of students graduated
     */
    private function graduateStudents(int $kelas9Id, Carbon $graduationDate): int;

    /**
     * Validate that student progression is valid
     *
     * @param Siswa $siswa The student to validate
     * @return bool True if student can be promoted
     */
    private function canPromote(Siswa $siswa): bool;
}
```

**ProgressionResult Data Transfer Object:**

```php
<?php

namespace App\DataTransferObjects;

class ProgressionResult
{
    public function __construct(
        public int $kelas7To8Count,
        public int $kelas8To9Count,
        public int $graduatedCount,
        public array $promotedStudentIds,
        public array $graduatedStudentIds
    ) {}
}
```

### TahfidzGroupHandler

**Location:** `app/Services/Handlers/TahfidzGroupHandler.php`

```php
<?php

namespace App\Services\Handlers;

use App\Models\Siswa;
use App\Models\RiwayatKelompok;
use App\Enums\StatusAkademik;
use App\Enums\StatusPenempatan;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class TahfidzGroupHandler
{
    /**
     * Clear Kelompok assignments for all active students
     *
     * Creates RiwayatKelompok records for audit trail
     *
     * @param int $adminId The admin performing the operation
     * @return TahfidzClearResult Contains counts and affected student IDs
     */
    public function clearGroups(int $adminId): TahfidzClearResult;

    /**
     * Bulk create RiwayatKelompok records
     *
     * @param Collection $students Students whose groups were cleared
     * @param int $adminId The admin performing the operation
     * @param Carbon $timestamp The operation timestamp
     * @return void
     */
    private function createRiwayatRecords(
        Collection $students,
        int $adminId,
        Carbon $timestamp
    ): void;

    /**
     * Determine which students should have groups cleared
     *
     * @return Collection Active students with Kelompok assignments
     */
    private function getStudentsForClearing(): Collection;
}
```

**TahfidzClearResult Data Transfer Object:**

```php
<?php

namespace App\DataTransferObjects;

class TahfidzClearResult
{
    public function __construct(
        public int $clearedCount,
        public array $clearedStudentIds,
        public int $riwayatRecordsCreated
    ) {}
}
```

### RolloverAuditLogger

**Location:** `app/Services/Handlers/RolloverAuditLogger.php`

```php
<?php

namespace App\Services\Handlers;

use App\Models\RolloverAuditLog;
use App\DataTransferObjects\ProgressionResult;
use App\DataTransferObjects\TahfidzClearResult;
use Illuminate\Support\Carbon;

class RolloverAuditLogger
{
    /**
     * Create initial audit log entry when rollover starts
     *
     * @param int $adminId Admin initiating rollover
     * @param int $sourceTahunAjaranId Current active Tahun Ajaran
     * @param int $targetTahunAjaranId Target Tahun Ajaran
     * @return RolloverAuditLog The created log entry
     */
    public function createInitialLog(
        int $adminId,
        int $sourceTahunAjaranId,
        int $targetTahunAjaranId
    ): RolloverAuditLog;

    /**
     * Update audit log with successful completion data
     *
     * @param RolloverAuditLog $log The log entry to update
     * @param ProgressionResult $academicResult Academic progression results
     * @param TahfidzClearResult $tahfidzResult Tahfidz clearing results
     * @return void
     */
    public function markCompleted(
        RolloverAuditLog $log,
        ProgressionResult $academicResult,
        TahfidzClearResult $tahfidzResult
    ): void;

    /**
     * Update audit log with failure information
     *
     * @param RolloverAuditLog $log The log entry to update
     * @param \Exception $exception The exception that caused failure
     * @return void
     */
    public function markFailed(RolloverAuditLog $log, \Exception $exception): void;

    /**
     * Record rollback operation
     *
     * @param RolloverAuditLog $log The original rollover log
     * @param int $adminId Admin performing rollback
     * @return void
     */
    public function recordRollback(RolloverAuditLog $log, int $adminId): void;
}
```

### IntegratedRolloverModal (Livewire Component)

**Location:** `app/Livewire/Admin/IntegratedRolloverModal.php`

```php
<?php

namespace App\Livewire\Admin;

use App\Models\TahunAjaran;
use App\Services\IntegratedRolloverService;
use Livewire\Component;

class IntegratedRolloverModal extends Component
{
    public bool $isOpen = false;
    public ?int $targetTahunAjaranId = null;
    public ?array $impactSummary = null;
    public bool $isLoading = false;

    public function mount() {}

    /**
     * Open the rollover modal
     */
    public function openModal(): void;

    /**
     * Close the rollover modal
     */
    public function closeModal(): void;

    /**
     * Load impact summary when target year is selected
     */
    public function updatedTargetTahunAjaranId(): void;

    /**
     * Execute the rollover process
     */
    public function confirmRollover(): void;

    /**
     * Execute rollback of the most recent rollover
     */
    public function executeRollback(): void;

    /**
     * Render the component
     */
    public function render(): \Illuminate\View\View;
}
```

### Exception Classes

**RolloverLockException**
```php
<?php

namespace App\Exceptions;

class RolloverLockException extends \RuntimeException
{
    public function __construct(string $message = "Rollover sedang diproses oleh admin lain")
    {
        parent::__construct($message);
    }
}
```

**RolloverValidationException**
```php
<?php

namespace App\Exceptions;

class RolloverValidationException extends \RuntimeException
{
    public function __construct(
        string $message,
        public readonly array $validationErrors = []
    ) {
        parent::__construct($message);
    }
}
```

## Data Models

### Siswa Model Updates

**No schema changes required.** The Siswa model already has all necessary fields:
- `kelas_id` (FK to kelas table)
- `kelompok_id` (FK to kelompoks table, nullable)
- `status_akademik` (enum: Aktif, Lulus SMP, Pindah Sekolah, Keluar)
- `status_penempatan` (enum: Belum Ditempatkan, Ditempatkan)
- `tanggal_lulus_akademik` (date, nullable)
- `tahun_ajaran_id` (FK to tahun_ajarans table, tracks entry year)

**New Model Methods:**

```php
// In Siswa.php

/**
 * Scope: Students eligible for academic promotion
 */
public function scopeEligibleForPromotion($query, int $kelasId)
{
    return $query->where('status_akademik', StatusAkademik::AKTIF->value)
                 ->where('kelas_id', $kelasId);
}

/**
 * Scope: Students with cleared groups pending placement
 */
public function scopePendingPlacement($query)
{
    return $query->where('status_akademik', StatusAkademik::AKTIF->value)
                 ->where('status_penempatan', StatusPenempatan::TERDAFTAR->value)
                 ->whereNull('kelompok_id');
}
```

### RiwayatKelompok Model

**No schema changes required.** Existing fields support rollover:
- `siswa_id` (FK to siswas table)
- `kelompok_dari_id` (nullable FK to kelompoks table)
- `kelompok_ke_id` (nullable FK to kelompoks table)
- `tanggal` (datetime)
- `alasan` (string) - Will use "Rollover Tahun Ajaran"
- `disetujui_oleh` (nullable FK to admins table)

### TahunAjaran Model

**No schema changes required.** Existing fields:
- `label` (string, e.g., "2024-2025")
- `tanggal_mulai` (date)
- `tanggal_selesai` (date)
- `status` (boolean) - Only one can be true at a time

**New Model Methods:**

```php
// In TahunAjaran.php

/**
 * Activate this Tahun Ajaran and deactivate all others
 */
public function activate(): void
{
    DB::transaction(function () {
        self::query()->update(['status' => false]);
        $this->update(['status' => true]);
    });
}

/**
 * Get the currently active Tahun Ajaran
 */
public static function getActive(): ?self
{
    return self::where('status', true)->first();
}
```

### RolloverAuditLog Model (New)

**Migration:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rollover_audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Who and when
            $table->foreignId('initiated_by')
                  ->constrained('admins')
                  ->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            
            // What changed
            $table->foreignId('source_tahun_ajaran_id')
                  ->constrained('tahun_ajarans')
                  ->onDelete('cascade');
            $table->foreignId('target_tahun_ajaran_id')
                  ->constrained('tahun_ajarans')
                  ->onDelete('cascade');
            
            // Impact metrics
            $table->integer('kelas_7_to_8_count')->default(0);
            $table->integer('kelas_8_to_9_count')->default(0);
            $table->integer('graduated_count')->default(0);
            $table->integer('kelompok_cleared_count')->default(0);
            
            // Execution status
            $table->enum('status', [
                'in_progress',
                'completed',
                'failed',
                'rolled_back'
            ])->default('in_progress');
            $table->text('error_message')->nullable();
            $table->text('error_trace')->nullable();
            
            // Rollback tracking
            $table->foreignId('rolled_back_by')
                  ->nullable()
                  ->constrained('admins')
                  ->onDelete('set null');
            $table->timestamp('rolled_back_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('started_at');
            $table->index(['source_tahun_ajaran_id', 'target_tahun_ajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rollover_audit_logs');
    }
};
```

**Model:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolloverAuditLog extends Model
{
    protected $fillable = [
        'initiated_by',
        'source_tahun_ajaran_id',
        'target_tahun_ajaran_id',
        'kelas_7_to_8_count',
        'kelas_8_to_9_count',
        'graduated_count',
        'kelompok_cleared_count',
        'status',
        'error_message',
        'error_trace',
        'started_at',
        'completed_at',
        'rolled_back_by',
        'rolled_back_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'rolled_back_at' => 'datetime',
        'kelas_7_to_8_count' => 'integer',
        'kelas_8_to_9_count' => 'integer',
        'graduated_count' => 'integer',
        'kelompok_cleared_count' => 'integer',
    ];

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'initiated_by');
    }

    public function sourceTahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'source_tahun_ajaran_id');
    }

    public function targetTahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'target_tahun_ajaran_id');
    }

    public function rolledBackBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'rolled_back_by');
    }

    /**
     * Check if this rollover can be rolled back
     */
    public function canRollback(): bool
    {
        return $this->status === 'completed' 
            && $this->completed_at 
            && $this->completed_at->diffInHours(now()) <= 24;
    }

    /**
     * Get total students affected
     */
    public function getTotalAffectedAttribute(): int
    {
        return $this->kelas_7_to_8_count 
            + $this->kelas_8_to_9_count 
            + $this->graduated_count 
            + $this->kelompok_cleared_count;
    }
}
```

### Kelas Model

**No schema changes required.** Existing fields support promotion logic:
- `tingkat` (string: "7", "8", or "9")
- `nama_kelas` (string: e.g., "7A", "8B")

**Helper Methods:**

```php
// In Kelas.php

/**
 * Get the next grade level Kelas
 */
public function getNextGradeKelas(): ?self
{
    $nextTingkat = match($this->tingkat) {
        '7' => '8',
        '8' => '9',
        '9' => null, // No next grade after 9
    };
    
    if (!$nextTingkat) {
        return null;
    }
    
    // Assuming same section (e.g., 7A → 8A)
    return self::where('tingkat', $nextTingkat)
               ->where('nama_kelas', str_replace($this->tingkat, $nextTingkat, $this->nama_kelas))
               ->first();
}
```

## Error Handling

### Validation Errors

**Pre-execution validation** catches issues before any database changes:

| Error Condition | Error Message | HTTP Status |
|----------------|---------------|-------------|
| No target Tahun Ajaran selected | "Silakan pilih Tahun Ajaran tujuan" | 422 |
| Target same as current | "Tahun Ajaran tujuan sama dengan yang aktif" | 422 |
| Target Tahun Ajaran not found | "Tahun Ajaran tujuan tidak ditemukan" | 404 |
| No active students | "Tidak ada siswa aktif untuk di-rollover" | 422 |
| Kelas records missing | "Data kelas tidak lengkap" | 500 |
| Invalid Kelas tingkat | "Data tidak valid: Kelas harus 7, 8, atau 9" | 500 |
| Concurrent rollover | "Rollover sedang diproses oleh admin lain" | 409 |

### Runtime Errors

**Transaction-level error handling** ensures atomicity:

```php
try {
    DB::transaction(function () use ($targetTahunAjaranId, $adminId) {
        // Acquire lock
        if (!$this->acquireLock()) {
            throw new RolloverLockException();
        }

        try {
            // Execute rollover operations...
            
        } finally {
            // Always release lock, even on error
            $this->releaseLock();
        }
    }, 5); // 5 retry attempts for deadlock resolution
    
} catch (RolloverLockException $e) {
    // Log and return user-friendly message
    Log::warning('Rollover lock conflict', ['admin_id' => $adminId]);
    throw $e;
    
} catch (\Exception $e) {
    // Log detailed error
    Log::error('Rollover failed', [
        'admin_id' => $adminId,
        'target_tahun_ajaran_id' => $targetTahunAjaranId,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    // Update audit log with failure
    if (isset($auditLog)) {
        $this->auditLogger->markFailed($auditLog, $e);
    }
    
    throw new \RuntimeException(
        'Rollover gagal: ' . $e->getMessage(),
        previous: $e
    );
}
```

### Rollback Error Handling

**Rollback validation** prevents invalid rollback attempts:

| Error Condition | Error Message |
|----------------|---------------|
| No recent rollover | "Tidak ada rollover yang dapat di-rollback" |
| Rollover already rolled back | "Rollover ini sudah di-rollback sebelumnya" |
| Rollover too old (>24 hours) | "Rollover hanya dapat di-rollback dalam 24 jam" |
| Rollover failed | "Tidak dapat rollback rollover yang gagal" |
| New data exists | "Tidak dapat rollback: data baru sudah ditambahkan" |

### User-Facing Error Messages

**Livewire component displays errors** via dialog system:

```php
// Success
$this->dispatch('dialog-open', [
    'type' => 'success',
    'title' => 'Rollover Berhasil',
    'message' => "Sistem berhasil bermigrasi ke {$newTa->label}. 
                  Total {$totalAffected} siswa diproses."
]);

// Error
$this->dispatch('dialog-open', [
    'type' => 'error',
    'title' => 'Rollover Gagal',
    'message' => $exception->getMessage()
]);

// Warning
$this->dispatch('dialog-open', [
    'type' => 'warning',
    'title' => 'Perhatian',
    'message' => 'Tidak ada siswa aktif yang akan di-rollover'
]);
```

## Correctness Properties

The Integrated Year-End Rollover System maintains critical invariants that ensure data integrity and system correctness. These properties must hold true before, during, and after rollover execution.

### Property 1: Single Active Academic Year
Exactly one `TahunAjaran` record has `status = true` at any given time.

**Validates: Requirements 1.2, 1.3, 9.3**

**Enforcement:**
- Pre-rollover validation checks exactly one active year exists
- Transaction atomically deactivates all records before activating target
- Database constraint could enforce (but not implemented for flexibility)

**Verification:**
```php
// Before rollover
assert(TahunAjaran::where('status', true)->count() === 1);

// After rollover
assert(TahunAjaran::where('status', true)->count() === 1);
assert(TahunAjaran::find($targetId)->status === true);
```

### Property 2: Academic Progression Consistency
Students only progress forward through grade levels (7→8→9→Graduated), never backward.

**Validates: Requirements 2.1, 2.2, 2.3, 2.4, 2.5, 3.4**

**Enforcement:**
- Only processes Kelas 7→8, 8→9, and 9→Graduated transitions
- No logic exists for demotion or grade-level reversal
- `canPromote()` validation ensures student is in `StatusAkademik::AKTIF`

**Verification:**
```php
// A student in Kelas 7 before rollover
$siswa = Siswa::where('kelas_id', $kelas7Id)->first();

// Must be in Kelas 8 after rollover
assert($siswa->fresh()->kelas_id === $kelas8Id);

// Never in Kelas 6 or back to 7
assert($siswa->fresh()->kelas_id !== $kelas7Id);
```

### Property 3: Graduation Finality
Once a student's `status_akademik` becomes `StatusAkademik::LULUS_SMP` and `tanggal_lulus_akademik` is set, they are excluded from all future rollovers.

**Validates: Requirements 3.1, 3.2, 3.3, 14.3, 14.4**

**Enforcement:**
- Academic progression only targets `status_akademik = 'Aktif'`
- Tahfidz clearing only targets `status_akademik = 'Aktif'`
- Graduated students are never modified by rollover

**Verification:**
```php
// Student graduates in rollover
$siswa = Siswa::where('kelas_id', $kelas9Id)->first();
// After rollover
assert($siswa->fresh()->status_akademik === StatusAkademik::LULUS_SMP->value);
assert($siswa->fresh()->tanggal_lulus_akademik !== null);

// Subsequent rollover does NOT modify this student
$siswaBeforeSecondRollover = $siswa->fresh();
// Execute second rollover...
assert($siswa->fresh()->status_akademik === $siswaBeforeSecondRollover->status_akademik);
assert($siswa->fresh()->kelas_id === $siswaBeforeSecondRollover->kelas_id);
```

### Property 4: Group Clearing Completeness
After rollover, all active students have `kelompok_id = null` and `status_penempatan = 'Belum Ditempatkan'`.

**Validates: Requirements 4.1, 4.2, 4.3, 4.4, 4.5**

**Enforcement:**
- `TahfidzGroupHandler->clearGroups()` processes ALL active students
- Bulk update ensures no students are skipped
- Pre-condition validation ensures operation targets correct students

**Verification:**
```php
// After rollover completion
$activeStudents = Siswa::where('status_akademik', StatusAkademik::AKTIF->value)->get();

foreach ($activeStudents as $siswa) {
    assert($siswa->kelompok_id === null);
    assert($siswa->status_penempatan === StatusPenempatan::BELUM_DITEMPATKAN->value);
}
```

### Property 5: Historical Record Integrity
Every Kelompok clearing creates a corresponding `RiwayatKelompok` record for audit trail.

**Validates: Requirements 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7, 11.2**

**Enforcement:**
- `TahfidzGroupHandler->createRiwayatRecords()` creates entries for ALL cleared students
- Uses bulk insert to ensure atomic creation
- Records include timestamp, admin ID, and reason "Rollover Tahun Ajaran"

**Verification:**
```php
// Count students with cleared groups
$clearedCount = Siswa::where('status_akademik', StatusAkademik::AKTIF->value)
                     ->whereNotNull('kelompok_id')
                     ->count();

// After rollover
$riwayatCount = RiwayatKelompok::where('alasan', 'Rollover Tahun Ajaran')
                               ->where('tanggal', '>=', $rolloverStartTime)
                               ->count();

assert($clearedCount === $riwayatCount);
```

### Property 6: Atomic State Transition
Rollover either fully completes (all operations succeed) or fully fails (no operations persist).

**Validates: Requirements 9.1, 9.2, 9.3, 9.4, 9.5, 9.6, 12.10**

**Enforcement:**
- Single `DB::transaction()` wraps all operations
- Any exception triggers automatic rollback
- Audit log status reflects atomic outcome: 'completed' or 'failed'

**Verification:**
```php
// If rollover fails mid-execution
$auditLog = RolloverAuditLog::latest()->first();
assert($auditLog->status === 'failed');

// No partial state changes exist
assert(TahunAjaran::find($targetId)->status === false); // Not activated
assert(Siswa::where('kelas_id', $kelas8Id)->count() === $countBeforeRollover); // No promotions
```

### Property 7: Concurrency Safety
Only one rollover operation can execute at any given time.

**Validates: Requirements 20.1, 20.2, 20.3, 20.4, 20.5, 20.6**

**Enforcement:**
- MySQL advisory lock `GET_LOCK('rollover_operation', 300)` acquired before transaction
- Lock prevents concurrent rollover executions
- Lock automatically released on completion or failure

**Verification:**
```php
// First rollover acquires lock
$result1 = $service->executeRollover($targetId, $adminId);

// Concurrent attempt throws exception
try {
    $result2 = $service->executeRollover($targetId, $adminId);
    assert(false, 'Should have thrown RolloverLockException');
} catch (RolloverLockException $e) {
    assert(true); // Expected behavior
}
```

### Property 8: Rollback Time Window
Rollback is only permitted within 24 hours of successful completion.

**Validates: Requirements 12.1, 12.2**

**Enforcement:**
- `RolloverAuditLog->canRollback()` checks completion timestamp
- Service-level validation enforces 24-hour window
- UI conditionally displays rollback button based on eligibility

**Verification:**
```php
$auditLog = RolloverAuditLog::latest()->where('status', 'completed')->first();

// Within 24 hours
if ($auditLog->completed_at->diffInHours(now()) <= 24) {
    assert($auditLog->canRollback() === true);
    // Rollback allowed
} else {
    assert($auditLog->canRollback() === false);
    // Rollback forbidden
}
```

### Property 9: Audit Trail Completeness
Every rollover execution creates exactly one `RolloverAuditLog` entry with complete metadata.

**Validates: Requirements 13.1, 13.2, 13.3, 13.4, 13.5, 13.6, 13.7**

**Enforcement:**
- Audit log created at transaction start (status: 'in_progress')
- Updated on completion/failure with counts and timestamps
- Never deleted (soft delete not used; historical integrity preserved)

**Verification:**
```php
$countBefore = RolloverAuditLog::count();

// Execute rollover
$service->executeRollover($targetId, $adminId);

$countAfter = RolloverAuditLog::count();
assert($countAfter === $countBefore + 1);

$log = RolloverAuditLog::latest()->first();
assert($log->initiated_by === $adminId);
assert($log->target_tahun_ajaran_id === $targetId);
assert($log->status === 'completed' || $log->status === 'failed');
```

### Property 10: Idempotency of Repeated Execution
Running rollover twice with the same parameters produces the same final state (though discouraged, system remains consistent).

**Validates: Requirements 7.7, 14.4**

**Enforcement:**
- Second execution finds no eligible students (already promoted/graduated/cleared)
- Impact summary shows 0 affected students
- No errors occur; operation completes successfully with no-op result

**Verification:**
```php
// First rollover
$result1 = $service->executeRollover($targetId, $adminId);

// Second rollover (should be no-op)
$result2 = $service->executeRollover($targetId, $adminId);

assert($result2->kelas7To8Count === 0);
assert($result2->kelas8To9Count === 0);
assert($result2->graduatedCount === 0);
assert($result2->kelompokClearedCount === 0);
```

## Property Verification Strategy

These properties are verified through:

1. **Unit Tests:** Test individual property enforcement logic in isolation
2. **Integration Tests:** Verify properties hold across full rollover workflow
3. **Pre-condition Validation:** Runtime checks before execution
4. **Post-condition Assertions:** Database state verification after completion
5. **Audit Log Analysis:** Historical verification via `RolloverAuditLog` records

## Non-Functional Properties

**Performance:**
- Rollover completes in <30 seconds for 500 students
- Bulk operations use chunking (500 records per query)
- Lock timeout prevents indefinite blocking (300 seconds max)

**Reliability:**
- Transaction retry logic (5 attempts) handles transient deadlocks
- Lock cleanup ensures system never permanently blocks
- Error logging captures full context for debugging

**Maintainability:**
- Handler classes isolate concerns (academic, Tahfidz, audit)
- DTOs provide type-safe data transfer
- Clear separation between service, handler, and UI layers

## Testing Strategy

This feature is **NOT suitable for property-based testing** because:
- It performs complex database state transitions with side effects
- It orchestrates multiple interdependent database operations
- It's not testing pure functions with clear input/output behavior
- Operations are one-time, side-effect-heavy, and stateful

Instead, we use a **dual testing approach** combining unit tests and integration tests.

### Unit Testing Strategy

#### 1. Service Layer Tests

**`IntegratedRolloverServiceTest`**

Test isolated service methods:

```php
// Validation logic
test('validates target tahun ajaran is selected')
test('validates target tahun ajaran exists')
test('validates target is different from current')
test('validates active students exist')
test('validates kelas data integrity')

// Impact calculation
test('calculates correct promotion counts')
test('calculates correct graduation counts')
test('calculates correct kelompok clearing counts')

// Lock management
test('acquires lock successfully')
test('throws exception when lock unavailable')
test('releases lock after completion')
test('releases lock after failure')

// Rollback validation
test('validates rollback within 24 hours')
test('prevents rollback of already rolled back operation')
test('prevents rollback of failed operation')
```

**Mock Dependencies:**
- Mock `AcademicProgressionHandler`
- Mock `TahfidzGroupHandler`
- Mock `RolloverAuditLogger`
- Use in-memory SQLite for database operations

#### 2. Handler Tests

**`AcademicProgressionHandlerTest`**

```php
// Promotion logic
test('promotes kelas 7 students to kelas 8')
test('promotes kelas 8 students to kelas 9')
test('graduates kelas 9 students')
test('skips non-active students')
test('skips students with invalid status')

// Graduation processing
test('sets status akademik to lulus smp')
test('records tanggal lulus akademik')
test('preserves kelas 9 assignment')

// Edge cases
test('handles students without kelas gracefully')
test('handles orphaned kelas references')
```

**`TahfidzGroupHandlerTest`**

```php
// Group clearing
test('clears kelompok id for active students')
test('updates status penempatan to belum ditempatkan')
test('clears tanggal penempatan')
test('preserves graduated students kelompok')

// Riwayat creation
test('creates riwayat kelompok records')
test('records correct kelompok dari id')
test('sets kelompok ke id to null')
test('sets alasan to rollover tahun ajaran')
test('records admin id in disetujui oleh')

// Bulk operations
test('handles 500 students efficiently')
test('chunks riwayat creation correctly')
```

**`RolloverAuditLoggerTest`**

```php
// Log creation
test('creates initial log with in progress status')
test('records correct timestamps')
test('captures source and target tahun ajaran')

// Log updates
test('marks log as completed with counts')
test('marks log as failed with exception details')
test('records rollback information')

// Query methods
test('retrieves most recent rollover')
test('filters by status correctly')
```

#### 3. Model Tests

**`SiswaModelTest`**

```php
// Scopes
test('eligible for promotion scope filters correctly')
test('pending placement scope filters correctly')
```

**`TahunAjaranModelTest`**

```php
// Activation
test('activate deactivates other tahun ajaran')
test('activate sets current to active')
test('get active returns correct record')
```

**`RolloverAuditLogModelTest`**

```php
// Business logic
test('can rollback returns true within 24 hours')
test('can rollback returns false after 24 hours')
test('can rollback returns false if rolled back')
test('total affected calculates correctly')
```

### Integration Testing Strategy

Integration tests use a real database (test database) to verify the complete workflow.

#### 1. End-to-End Rollover Tests

**`IntegratedRolloverIntegrationTest`**

```php
// Full workflow
test('complete rollover executes all operations correctly')
test('activates target tahun ajaran')
test('promotes all kelas 7 students to kelas 8')
test('promotes all kelas 8 students to kelas 9')
test('graduates all kelas 9 students')
test('clears kelompok for all active students')
test('creates riwayat kelompok records')
test('creates audit log entry')

// Transaction behavior
test('rollback on validation failure')
test('rollback on promotion failure')
test('rollback on kelompok clearing failure')

// Data integrity
test('no partial updates on failure')
test('referential integrity maintained')
test('historical data preserved')
```

**Test Setup:**
```php
protected function setUp(): void
{
    parent::setUp();
    
    // Create test data
    $this->currentTa = TahunAjaran::factory()->create(['status' => true]);
    $this->targetTa = TahunAjaran::factory()->create(['status' => false]);
    
    $this->kelas7 = Kelas::factory()->create(['tingkat' => '7']);
    $this->kelas8 = Kelas::factory()->create(['tingkat' => '8']);
    $this->kelas9 = Kelas::factory()->create(['tingkat' => '9']);
    
    // Create students in each grade
    $this->kelas7Students = Siswa::factory()->count(50)
        ->create([
            'kelas_id' => $this->kelas7->id,
            'status_akademik' => StatusAkademik::AKTIF->value,
            'kelompok_id' => Kelompok::factory()
        ]);
    
    // Similar for kelas 8 and 9...
}
```

#### 2. Rollback Integration Tests

**`RolloverRollbackIntegrationTest`**

```php
// Rollback workflow
test('rollback restores previous tahun ajaran')
test('rollback reverts kelas promotions')
test('rollback restores kelompok assignments')
test('rollback restores status akademik')
test('rollback removes riwayat kelompok records')
test('rollback updates audit log')

// Rollback constraints
test('rollback fails after 24 hours')
test('rollback fails if already rolled back')
test('rollback fails if new data added')
```

#### 3. Concurrency Tests

**`RolloverConcurrencyTest`**

```php
test('second rollover waits for first to complete')
test('second rollover fails if first holds lock')
test('lock released after successful rollover')
test('lock released after failed rollover')
test('lock expires after timeout')
```

**Implementation:**
```php
use Illuminate\Support\Facades\ParallelTesting;

test('concurrent rollover attempts', function () {
    $service = app(IntegratedRolloverService::class);
    
    // Start first rollover in background
    $process1 = new Process(['php', 'artisan', 'test:rollover']);
    $process1->start();
    
    // Attempt second rollover
    try {
        $service->executeRollover($this->targetTa->id, $this->admin->id);
        $this->fail('Should have thrown RolloverLockException');
    } catch (RolloverLockException $e) {
        $this->assertEquals(
            'Rollover sedang diproses oleh admin lain',
            $e->getMessage()
        );
    }
    
    $process1->wait();
});
```

#### 4. Performance Tests

**`RolloverPerformanceTest`**

```php
test('rollover completes within 30 seconds for 500 students')
test('rollover completes within 60 seconds for 1000 students')
test('riwayat creation uses batch insertion')
test('database queries are optimized')
```

**Assertions:**
```php
// Measure execution time
$start = microtime(true);
$service->executeRollover($this->targetTa->id, $this->admin->id);
$duration = microtime(true) - $start;

expect($duration)->toBeLessThan(30);

// Count database queries
DB::enableQueryLog();
$service->executeRollover($this->targetTa->id, $this->admin->id);
$queries = DB::getQueryLog();

// Should use bulk updates, not N+1 queries
expect(count($queries))->toBeLessThan(20);
```

### Test Data Factories

**SiswaFactory Enhancements:**

```php
// Factory state for rollover testing
public function inKelas(int $kelasId): static
{
    return $this->state(fn (array $attributes) => [
        'kelas_id' => $kelasId,
        'status_akademik' => StatusAkademik::AKTIF->value,
    ]);
}

public function withKelompok(): static
{
    return $this->state(fn (array $attributes) => [
        'kelompok_id' => Kelompok::factory(),
        'status_penempatan' => StatusPenempatan::DITEMPATKAN->value,
        'tanggal_penempatan' => now()->subMonths(3),
    ]);
}

public function graduated(): static
{
    return $this->state(fn (array $attributes) => [
        'status_akademik' => StatusAkademik::LULUS_SMP->value,
        'tanggal_lulus_akademik' => now(),
    ]);
}
```

### Manual Testing Checklist

- [ ] Open rollover modal from admin dashboard
- [ ] Select target Tahun Ajaran from dropdown
- [ ] Verify impact summary displays correct counts
- [ ] Confirm rollover execution
- [ ] Verify success notification displays
- [ ] Check TahunAjaran status changed correctly
- [ ] Verify student Kelas values updated
- [ ] Verify graduated students marked correctly
- [ ] Verify Kelompok assignments cleared
- [ ] Check RiwayatKelompok records created
- [ ] Verify audit log entry created
- [ ] Test rollback functionality
- [ ] Verify rollback restores previous state
- [ ] Test concurrent rollover attempts (different browser sessions)
- [ ] Test error scenarios (invalid data, missing records)
- [ ] Performance test with 500+ students

## Implementation Sequence

### Phase 1: Data Layer (Week 1)

1. **Day 1-2: Database Migration**
   - Create `rollover_audit_logs` migration
   - Add indexes for performance
   - Test migration up/down

2. **Day 3-4: Model Creation**
   - Create `RolloverAuditLog` model
   - Add relationships
   - Write model unit tests
   - Update existing models with helper methods

3. **Day 5: Data Transfer Objects**
   - Create `ProgressionResult` DTO
   - Create `TahfidzClearResult` DTO
   - Create `ValidationResult` DTO
   - Create `ImpactSummary` DTO
   - Create `RollbackResult` DTO

### Phase 2: Service Layer (Week 2)

4. **Day 1-2: Handler Classes**
   - Implement `AcademicProgressionHandler`
   - Write handler unit tests
   - Implement `TahfidzGroupHandler`
   - Write handler unit tests

5. **Day 3: Audit Logger**
   - Implement `RolloverAuditLogger`
   - Write logger unit tests

6. **Day 4-5: Main Service**
   - Implement `IntegratedRolloverService`
   - Implement lock management
   - Write service unit tests
   - Write integration tests

### Phase 3: UI Layer (Week 3)

7. **Day 1-2: Livewire Component**
   - Create `IntegratedRolloverModal` component
   - Implement modal logic
   - Add progress indicators

8. **Day 3: Blade Templates**
   - Create modal view
   - Create confirmation dialog
   - Create impact summary display
   - Add loading states

9. **Day 4: Integration**
   - Wire up component to admin dashboard
   - Test complete user flow
   - Add error handling

10. **Day 5: Rollback UI**
    - Add rollback button
    - Create rollback confirmation dialog
    - Test rollback flow

### Phase 4: Testing & Refinement (Week 4)

11. **Day 1-2: Integration Testing**
    - Write end-to-end tests
    - Write rollback tests
    - Write concurrency tests
    - Write performance tests

12. **Day 3: Manual Testing**
    - Follow manual testing checklist
    - Test with production-like data volumes
    - Test error scenarios

13. **Day 4: Documentation**
    - Write user documentation
    - Create admin guide
    - Document troubleshooting steps

14. **Day 5: Deployment Preparation**
    - Review code
    - Performance optimization
    - Security review
    - Staging deployment

### Phase 5: Deployment & Monitoring (Week 5)

15. **Production Deployment**
    - Deploy migration
    - Deploy code
    - Monitor error logs
    - Monitor performance metrics

## Performance Optimization

### Database Query Optimization

**1. Bulk Updates Instead of Individual Updates**

```php
// BAD: N+1 queries
foreach ($students as $student) {
    $student->update(['kelas_id' => $newKelasId]);
}

// GOOD: Single bulk update
Siswa::whereIn('id', $studentIds)
     ->update(['kelas_id' => $newKelasId]);
```

**2. Chunked RiwayatKelompok Insertion**

```php
// Insert 500 records at a time to avoid memory issues
$riwayatData = [];
foreach ($students as $student) {
    $riwayatData[] = [
        'siswa_id' => $student->id,
        'kelompok_dari_id' => $student->kelompok_id,
        'kelompok_ke_id' => null,
        'tanggal' => now(),
        'alasan' => 'Rollover Tahun Ajaran',
        'disetujui_oleh' => $adminId,
        'created_at' => now(),
        'updated_at' => now(),
    ];
}

foreach (array_chunk($riwayatData, 500) as $chunk) {
    RiwayatKelompok::insert($chunk);
}
```

**3. Eager Loading to Prevent N+1**

```php
// Load relationships upfront
$students = Siswa::with(['kelas', 'kelompok'])
                 ->where('status_akademik', StatusAkademik::AKTIF->value)
                 ->get();
```

**4. Index Optimization**

```php
// Add composite indexes for common queries
Schema::table('siswas', function (Blueprint $table) {
    $table->index(['status_akademik', 'kelas_id']);
    $table->index(['status_akademik', 'kelompok_id']);
});
```

### Memory Management

**Stream Large Datasets:**

```php
// For very large student counts, use cursor for memory efficiency
Siswa::where('status_akademik', StatusAkademik::AKTIF->value)
     ->cursor()
     ->chunk(100)
     ->each(function ($chunk) {
         // Process chunk
     });
```

### Expected Performance Metrics

| Students | Expected Duration | Database Queries | Memory Usage |
|----------|------------------|------------------|--------------|
| 100      | < 5 seconds      | < 15 queries     | < 50 MB      |
| 500      | < 30 seconds     | < 20 queries     | < 100 MB     |
| 1000     | < 60 seconds     | < 25 queries     | < 150 MB     |

## Security Considerations

### Authorization

**Middleware Protection:**

```php
Route::middleware(['auth:admin'])->group(function () {
    // Only authenticated admins can access rollover
});
```

**Service-Level Authorization:**

```php
public function executeRollover(int $targetTahunAjaranId, int $adminId): RolloverResult
{
    // Verify admin is authenticated
    if (auth('admin')->id() !== $adminId) {
        throw new UnauthorizedException('Admin ID mismatch');
    }
    
    // Additional permission checks if role-based access control exists
    if (!auth('admin')->user()->can('execute-rollover')) {
        throw new ForbiddenException('Insufficient permissions');
    }
    
    // Proceed with rollover
}
```

### Audit Trail

**Complete tracking** of who did what and when:

```php
// Every rollover creates permanent audit log
RolloverAuditLog::create([
    'initiated_by' => $adminId,
    'source_tahun_ajaran_id' => $sourceTa->id,
    'target_tahun_ajaran_id' => $targetTa->id,
    'started_at' => now(),
    // ... counts and status
]);

// Every rollback is also logged
$auditLog->update([
    'rolled_back_by' => $adminId,
    'rolled_back_at' => now(),
    'status' => 'rolled_back'
]);
```

### Input Validation

**Strict validation** before any database changes:

```php
// Validate Tahun Ajaran ID
if (!TahunAjaran::where('id', $targetTahunAjaranId)->exists()) {
    throw new ValidationException('Tahun Ajaran tidak ditemukan');
}

// Validate not same as current
$currentTa = TahunAjaran::getActive();
if ($currentTa->id === $targetTahunAjaranId) {
    throw new ValidationException('Tahun Ajaran tujuan sama dengan yang aktif');
}

// Validate data integrity
$invalidStudents = Siswa::where('status_akademik', StatusAkademik::AKTIF->value)
                        ->whereNotIn('kelas_id', Kelas::pluck('id'))
                        ->count();
                        
if ($invalidStudents > 0) {
    throw new ValidationException('Terdapat data siswa dengan kelas tidak valid');
}
```

### SQL Injection Prevention

Laravel's query builder and Eloquent ORM automatically prevent SQL injection, but for raw queries:

```php
// NEVER do this:
DB::select("SELECT * FROM siswas WHERE kelas_id = " . $kelasId);

// ALWAYS use parameter binding:
DB::select("SELECT * FROM siswas WHERE kelas_id = ?", [$kelasId]);
```

### Concurrent Modification Protection

Database-level locking prevents race conditions:

```php
// Advisory lock prevents concurrent rollover
DB::select('SELECT GET_LOCK(?, ?)', ['rollover_operation', 300]);
```

## Deployment Strategy

### Pre-Deployment Checklist

- [ ] All tests passing (unit + integration)
- [ ] Performance benchmarks met
- [ ] Security review completed
- [ ] Database backup created
- [ ] Rollback plan documented
- [ ] Staging environment tested
- [ ] User documentation prepared

### Deployment Steps

1. **Database Migration**
   ```bash
   php artisan migrate --step
   ```

2. **Deploy Code**
   ```bash
   git pull origin main
   composer install --no-dev --optimize-autoloader
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Verify Deployment**
   ```bash
   php artisan tinker
   >>> \App\Services\IntegratedRolloverService::class
   >>> \App\Models\RolloverAuditLog::count()
   ```

4. **Monitor Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Rollback Plan

If critical issues are discovered post-deployment:

1. **Code Rollback**
   ```bash
   git revert HEAD
   git push origin main
   composer install
   php artisan cache:clear
   ```

2. **Database Rollback**
   ```bash
   php artisan migrate:rollback --step=1
   ```

3. **Restore Old Component**
   - Temporarily re-enable old `RolloverModal.php`
   - Update routes to use old component

### Post-Deployment Monitoring

**Key Metrics to Monitor:**

- Rollover execution duration
- Database transaction success rate
- Lock acquisition failures
- Error rates in `RolloverAuditLog`
- User-reported issues

**Monitoring Queries:**

```sql
-- Check recent rollovers
SELECT * FROM rollover_audit_logs 
ORDER BY started_at DESC 
LIMIT 10;

-- Check failure rate
SELECT status, COUNT(*) 
FROM rollover_audit_logs 
GROUP BY status;

-- Check performance
SELECT 
    AVG(TIMESTAMPDIFF(SECOND, started_at, completed_at)) as avg_duration_seconds
FROM rollover_audit_logs 
WHERE status = 'completed';
```

## Future Enhancements

### Phase 2 Features (Post-MVP)

1. **Scheduled Rollover**
   - Allow admins to schedule rollover for specific date/time
   - Send email notifications before execution
   - Automatic execution via Laravel scheduler

2. **Rollover Preview Mode**
   - Dry-run capability to simulate rollover without committing
   - Show detailed report of what would change
   - Export preview report to PDF

3. **Partial Rollover**
   - Rollover specific Kelas only (e.g., only Kelas 7→8)
   - Rollover by sections (e.g., only Kelas 7A)

4. **Multi-Year Rollover History**
   - Dashboard showing rollover history across multiple years
   - Trend analysis of student progression
   - Graduation rate visualization

5. **Rollover Notifications**
   - Email notifications to teachers about new Kelas assignments
   - SMS notifications to parents about student promotion
   - Dashboard alerts for pending placement tasks

6. **Advanced Rollback**
   - Extended rollback window (72 hours instead of 24)
   - Selective rollback (revert only specific operations)
   - Rollback with data preservation

7. **Rollover Analytics**
   - Student progression reports
   - Cohort analysis (track Kelas 7 entry year through graduation)
   - Retention rate calculations

## Appendix: SQL Queries

### Rollover Execution Queries

**1. Activate Target Tahun Ajaran**
```sql
-- Deactivate all
UPDATE tahun_ajarans SET status = 0;

-- Activate target
UPDATE tahun_ajarans SET status = 1 WHERE id = ?;
```

**2. Promote Kelas 7 to Kelas 8**
```sql
UPDATE siswas 
SET kelas_id = ? 
WHERE status_akademik = 'Aktif' 
  AND kelas_id = ?;
```

**3. Graduate Kelas 9 Students**
```sql
UPDATE siswas 
SET status_akademik = 'Lulus SMP',
    tanggal_lulus_akademik = NOW()
WHERE status_akademik = 'Aktif' 
  AND kelas_id = ?;
```

**4. Clear Kelompok Assignments**
```sql
UPDATE siswas 
SET kelompok_id = NULL,
    status_penempatan = 'Belum Ditempatkan',
    tanggal_penempatan = NULL
WHERE status_akademik = 'Aktif' 
  AND kelompok_id IS NOT NULL;
```

**5. Bulk Insert RiwayatKelompok**
```sql
INSERT INTO riwayat_kelompoks 
  (siswa_id, kelompok_dari_id, kelompok_ke_id, tanggal, alasan, disetujui_oleh, created_at, updated_at)
VALUES 
  (?, ?, NULL, NOW(), 'Rollover Tahun Ajaran', ?, NOW(), NOW()),
  -- ... repeat for each student
```

### Reporting Queries

**Rollover Impact Summary**
```sql
SELECT 
    (SELECT COUNT(*) FROM siswas WHERE status_akademik = 'Aktif' AND kelas_id = ?) as kelas_7_count,
    (SELECT COUNT(*) FROM siswas WHERE status_akademik = 'Aktif' AND kelas_id = ?) as kelas_8_count,
    (SELECT COUNT(*) FROM siswas WHERE status_akademik = 'Aktif' AND kelas_id = ?) as kelas_9_count,
    (SELECT COUNT(*) FROM siswas WHERE status_akademik = 'Aktif' AND kelompok_id IS NOT NULL) as with_kelompok_count;
```

**Student Progression History**
```sql
SELECT 
    s.nis,
    s.nama,
    s.tahun_ajaran_id as entry_year,
    k.tingkat as current_kelas,
    s.status_akademik,
    s.tanggal_lulus_akademik
FROM siswas s
LEFT JOIN kelas k ON s.kelas_id = k.id
WHERE s.tahun_ajaran_id = ?
ORDER BY s.nama;
```

**Rollover Audit History**
```sql
SELECT 
    ral.*,
    a.name as admin_name,
    ta_source.label as source_year,
    ta_target.label as target_year
FROM rollover_audit_logs ral
JOIN admins a ON ral.initiated_by = a.id
JOIN tahun_ajarans ta_source ON ral.source_tahun_ajaran_id = ta_source.id
JOIN tahun_ajarans ta_target ON ral.target_tahun_ajaran_id = ta_target.id
ORDER BY ral.started_at DESC
LIMIT 50;
```

---

**Document Version:** 1.0  
**Last Updated:** 2024-01-XX  
**Author:** AI Assistant (Kiro)  
**Status:** Ready for Review
