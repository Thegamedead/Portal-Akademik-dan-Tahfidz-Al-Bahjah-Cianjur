<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if index exists using raw SQL (works for both SQLite and MySQL)
        $connection = DB::connection();
        $driver = $connection->getDriverName();

        $indexExists = function (string $indexName) use ($connection, $driver): bool {
            try {
                if ($driver === 'sqlite') {
                    $result = $connection->select("SELECT name FROM sqlite_master WHERE type='index' AND name=?", [$indexName]);
                    return count($result) > 0;
                } else {
                    $database = config('database.connections.mysql.database');
                    $result = $connection->select("SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'activity_log' AND INDEX_NAME = ?", [$database, $indexName]);
                    return count($result) > 0;
                }
            } catch (\Exception $e) {
                return false;
            }
        };

        Schema::table('activity_log', function (Blueprint $table) use ($indexExists) {
            if (!$indexExists('activity_log_log_name_index')) {
                $table->index('log_name');
            }
            if (!$indexExists('activity_log_created_at_index')) {
                $table->index('created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            try {
                $table->dropIndex(['log_name']);
            } catch (\Exception $e) {
                // Index might not exist
            }

            try {
                $table->dropIndex(['created_at']);
            } catch (\Exception $e) {
                // Index might not exist
            }
        });
    }
};
