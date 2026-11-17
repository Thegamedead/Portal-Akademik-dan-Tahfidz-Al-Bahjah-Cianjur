<?php

/**
 * Manual Test Script for Parameter Konfigurasi Snapshot Integration
 *
 * This script demonstrates the snapshot creation trigger when parameter_konfigurasi is updated.
 * Run this in Laravel Tinker to verify the integration manually.
 *
 * Usage:
 * php artisan tinker
 * include 'tests/Manual/ParameterKonfigurasiSnapshotManualTest.php';
 *
 * Steps:
 * 1. Verify initial state (number of students, number of snapshots)
 * 2. Update target_kelulusan parameter
 * 3. Verify snapshots were created for all active students
 * 4. Verify snapshot data structure
 * 5. Check activity logs
 */

use App\Models\ParameterKonfigurasi;
use App\Models\TranskripCapaian;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Log;

echo "=== Parameter Konfigurasi Snapshot Integration Manual Test ===\n\n";

// Step 1: Verify initial state
echo "Step 1: Initial State\n";
echo "--------------------\n";

$activeStudents = Siswa::where('status_akademik', 'Aktif')
    ->where('status_penempatan', 'Ditempatkan')
    ->count();
echo "Active students (status_akademik='Aktif' AND status_penempatan='Ditempatkan'): {$activeStudents}\n";

$initialSnapshotCount = TranskripCapaian::count();
echo "Initial snapshot count: {$initialSnapshotCount}\n";

$activeTahunAjaran = TahunAjaran::where('status', true)->first();
if ($activeTahunAjaran) {
    echo "Active tahun ajaran: {$activeTahunAjaran->tahun} {$activeTahunAjaran->semester}\n";
} else {
    echo "⚠️  WARNING: No active tahun ajaran found. Snapshots cannot be created.\n";
}

$parameterKonfigurasi = ParameterKonfigurasi::getInstance();
echo "Current target_kelulusan: {$parameterKonfigurasi->target_kelulusan}\n";
echo "Current batas_tingkat: " . json_encode($parameterKonfigurasi->batas_tingkat) . "\n\n";

// Step 2: Update target_kelulusan
echo "Step 2: Update target_kelulusan from {$parameterKonfigurasi->target_kelulusan} to 7\n";
echo "--------------------------------------------------------\n";

$oldValue = $parameterKonfigurasi->target_kelulusan;
$parameterKonfigurasi->update(['target_kelulusan' => 7]);

echo "✓ Parameter updated successfully\n\n";

// Step 3: Verify snapshots were created
echo "Step 3: Verify Snapshots Created\n";
echo "---------------------------------\n";

$newSnapshotCount = TranskripCapaian::count();
$snapshotsCreated = $newSnapshotCount - $initialSnapshotCount;
echo "New snapshot count: {$newSnapshotCount}\n";
echo "Snapshots created: {$snapshotsCreated}\n";

if ($snapshotsCreated === $activeStudents) {
    echo "✓ Correct: {$snapshotsCreated} snapshots created for {$activeStudents} active students\n\n";
} else {
    echo "⚠️  WARNING: Expected {$activeStudents} snapshots, but {$snapshotsCreated} were created\n\n";
}

// Step 4: Verify snapshot data structure
echo "Step 4: Verify Snapshot Data Structure\n";
echo "---------------------------------------\n";

$recentSnapshots = TranskripCapaian::where('reason', 'target_kelulusan_change')
    ->latest()
    ->take(3)
    ->get();

if ($recentSnapshots->isEmpty()) {
    echo "⚠️  No snapshots found with reason 'target_kelulusan_change'\n\n";
} else {
    echo "Showing {$recentSnapshots->count()} most recent snapshots:\n\n";

    foreach ($recentSnapshots as $index => $snapshot) {
        echo "Snapshot " . ($index + 1) . ":\n";
        echo "  - siswa_id: {$snapshot->siswa_id}\n";
        echo "  - tanggal_generate: {$snapshot->tanggal_generate}\n";
        echo "  - reason: {$snapshot->reason}\n";
        echo "  - total_juz_tuntas_snapshot: {$snapshot->total_juz_tuntas_snapshot}\n";
        echo "  - target_kelulusan_at_snapshot: {$snapshot->target_kelulusan_at_snapshot}\n";
        echo "  - detail_capaian_snapshot keys: " . implode(', ', array_keys($snapshot->detail_capaian_snapshot)) . "\n";

        // Verify structure
        $requiredKeys = ['total_juz_tuntas', 'juz_berjalan', 'juz_berjalan_percentage', 'progress_toward_target', 'capaian_juz'];
        $missingKeys = array_diff($requiredKeys, array_keys($snapshot->detail_capaian_snapshot));

        if (empty($missingKeys)) {
            echo "  ✓ Snapshot has correct data structure\n";
        } else {
            echo "  ⚠️  Missing keys: " . implode(', ', $missingKeys) . "\n";
        }
        echo "\n";
    }
}

// Step 5: Check recent logs (if possible)
echo "Step 5: Check Logs\n";
echo "------------------\n";
echo "Check the Laravel log file for entries about snapshot creation.\n";
echo "Expected log entries:\n";
echo "  - 'Parameter target_kelulusan changed'\n";
echo "  - 'Snapshot created after target_kelulusan change'\n\n";

// Restore original value for safety
echo "Restoring original target_kelulusan value: {$oldValue}\n";
$parameterKonfigurasi->update(['target_kelulusan' => $oldValue]);
echo "✓ Original value restored\n\n";

echo "=== Test Complete ===\n";
echo "\nNote: This test modified parameter_konfigurasi and created snapshot records.\n";
echo "The parameter was restored, but snapshot records remain in the database.\n";
