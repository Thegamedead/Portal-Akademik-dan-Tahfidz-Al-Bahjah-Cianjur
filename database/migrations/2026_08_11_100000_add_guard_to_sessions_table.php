<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('sessions', 'guard')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->string('guard')->nullable()->index()->after('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sessions', 'guard')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropColumn('guard');
            });
        }
    }
};
