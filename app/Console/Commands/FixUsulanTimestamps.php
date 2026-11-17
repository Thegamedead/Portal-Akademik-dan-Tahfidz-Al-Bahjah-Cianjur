<?php

namespace App\Console\Commands;

use App\Models\UsulanKenaikanTingkat;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Carbon\Carbon;

#[Signature('fix:usulan-timestamps')]
#[Description('Fix usulan kenaikan tingkat records with 00:00:00 timestamps')]
class FixUsulanTimestamps extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Scanning for usulan records with 00:00:00 timestamps...');

        // Find all usulan where tanggal_usul has time 00:00:00
        $usulansToFix = UsulanKenaikanTingkat::whereNotNull('tanggal_usul')
            ->get()
            ->filter(function ($usulan) {
                // Check if time is exactly 00:00:00
                return $usulan->tanggal_usul->format('H:i:s') === '00:00:00';
            });

        if ($usulansToFix->isEmpty()) {
            $this->info('✅ No records need fixing. All tanggal_usul have proper timestamps.');
            return Command::SUCCESS;
        }

        $this->warn("Found {$usulansToFix->count()} records with 00:00:00 timestamps.");

        if (!$this->confirm('Do you want to update these records to use 08:00:00 (8 AM) as default time?', true)) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $progressBar = $this->output->createProgressBar($usulansToFix->count());
        $progressBar->start();

        $updated = 0;
        foreach ($usulansToFix as $usulan) {
            // Set time to 08:00:00 (reasonable default for proposal submissions)
            $newTimestamp = Carbon::parse($usulan->tanggal_usul->format('Y-m-d') . ' 08:00:00');

            // Update without triggering model events to avoid audit logs
            $usulan->timestamps = false;
            $usulan->tanggal_usul = $newTimestamp;
            $usulan->save();

            $updated++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info("✅ Successfully updated {$updated} records!");
        $this->info('💡 All tanggal_usul with 00:00:00 have been set to 08:00:00');

        return Command::SUCCESS;
    }
}
