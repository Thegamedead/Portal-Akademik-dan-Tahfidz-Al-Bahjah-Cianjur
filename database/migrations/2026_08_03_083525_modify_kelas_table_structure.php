<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite compatibility: need to drop indexes before dropping columns
        if (DB::getDriverName() === 'sqlite') {
            // SQLite: Drop indexes first, then foreign keys, then columns
            Schema::table('kelas', function (Blueprint $table) {
                try { $table->dropIndex(['tahun_ajaran_id']); } catch (\Exception $e) {}
                try { $table->dropIndex(['tingkat']); } catch (\Exception $e) {}
                try { $table->dropUnique(['tahun_ajaran_id', 'tingkat', 'rombel']); } catch (\Exception $e) {}
                try { $table->dropForeign(['tahun_ajaran_id']); } catch (\Exception $e) {}
                try { $table->dropForeign(['wali_kelas_id']); } catch (\Exception $e) {}
            });
        } else {
            // MySQL: cek FK yang ada sebelum drop (IF EXISTS tidak didukung semua versi MySQL)
            $existingFKs = collect(DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'kelas'
                  AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            "))->pluck('CONSTRAINT_NAME')->toArray();

            $existingIndexes = collect(DB::select("
                SELECT INDEX_NAME
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'kelas'
                  AND INDEX_NAME != 'PRIMARY'
            "))->pluck('INDEX_NAME')->unique()->toArray();

            foreach (['kelas_tahun_ajaran_id_foreign', 'kelas_wali_kelas_id_foreign'] as $fk) {
                if (in_array($fk, $existingFKs)) {
                    DB::statement("ALTER TABLE kelas DROP FOREIGN KEY `{$fk}`");
                }
            }
            foreach (['kelas_tahun_ajaran_id_tingkat_rombel_unique', 'kelas_tingkat_index', 'kelas_tahun_ajaran_id_index'] as $idx) {
                if (in_array($idx, $existingIndexes)) {
                    DB::statement("ALTER TABLE kelas DROP INDEX `{$idx}`");
                }
            }
        }

        // Drop old columns and add new ones
        Schema::table('kelas', function (Blueprint $table) {
            if (Schema::hasColumn('kelas', 'tahun_ajaran_id')) {
                $table->dropColumn(['tahun_ajaran_id', 'rombel', 'wali_kelas_id', 'kapasitas']);
            }

            if (!Schema::hasColumn('kelas', 'nama_kelas')) {
                $table->string('nama_kelas', 10)->after('tingkat');
            }
            if (!Schema::hasColumn('kelas', 'nama_wali_kelas')) {
                $table->string('nama_wali_kelas', 100)->after('nama_kelas');
            }
            if (!Schema::hasColumn('kelas', 'deleted_at')) {
                $table->softDeletes();
            }

            // Add new indexes
            $table->index('tingkat');
            $table->index('nama_kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            // Remove new indexes
            $table->dropIndex(['tingkat']);
            $table->dropIndex(['nama_kelas']);

            // Remove new columns
            $table->dropColumn(['nama_kelas', 'nama_wali_kelas']);
            $table->dropSoftDeletes();

            // Restore old columns
            $table->unsignedBigInteger('tahun_ajaran_id')->after('id');
            $table->string('rombel', 2)->after('tingkat');
            $table->unsignedBigInteger('wali_kelas_id')->nullable()->after('rombel');
            $table->unsignedSmallInteger('kapasitas')->default(30)->after('wali_kelas_id');

            // Restore foreign keys
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans')->onDelete('cascade');
            $table->foreign('wali_kelas_id')->references('id')->on('gurus')->onDelete('set null');

            // Restore unique constraint
            $table->unique(['tahun_ajaran_id', 'tingkat', 'rombel']);

            // Restore indexes
            $table->index('tingkat');
            $table->index('tahun_ajaran_id');
        });
    }
};
