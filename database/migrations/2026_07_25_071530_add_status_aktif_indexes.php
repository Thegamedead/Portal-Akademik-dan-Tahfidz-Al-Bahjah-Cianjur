<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan index pada kolom status_aktif di tabel admins, gurus, dan siswas
     * untuk optimasi query yang sering memfilter berdasarkan status aktif.
     *
     * Query yang dioptimasi:
     * - Admin::where('status_aktif', true)->count() (cek single active admin)
     * - Login queries yang memeriksa status_aktif
     * - Listing queries di ManajemenAkun
     */
    public function up(): void
    {
        // Check and add index only if it doesn't exist
        if (!$this->indexExists('admins', 'admins_status_aktif_index')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->index('status_aktif');
            });
        }

        if (!$this->indexExists('gurus', 'gurus_status_aktif_index')) {
            Schema::table('gurus', function (Blueprint $table) {
                $table->index('status_aktif');
            });
        }

        if (!$this->indexExists('siswas', 'siswas_status_aktif_index')) {
            Schema::table('siswas', function (Blueprint $table) {
                $table->index('status_aktif');
            });
        }
    }

    /**
     * Check if an index exists on a table using raw SQL
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}");

        if (($connection['driver'] ?? '') === 'sqlite') {
            // For SQLite, check using pragma
            try {
                $indexes = \DB::select("PRAGMA index_list({$table})");
                foreach ($indexes as $index) {
                    if ($index->name === $indexName) {
                        return true;
                    }
                }
                return false;
            } catch (\Exception $e) {
                return false;
            }
        }

        $database = $connection['database'] ?? '';

        try {
            $result = \DB::select(
                "SELECT COUNT(*) as count
                 FROM information_schema.STATISTICS
                 WHERE table_schema = ?
                 AND table_name = ?
                 AND index_name = ?",
                [$database, $table, $indexName]
            );

            return $result[0]->count > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropIndex(['status_aktif']);
        });

        Schema::table('gurus', function (Blueprint $table) {
            $table->dropIndex(['status_aktif']);
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropIndex(['status_aktif']);
        });
    }
};
