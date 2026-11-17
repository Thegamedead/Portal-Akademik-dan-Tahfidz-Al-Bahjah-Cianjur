<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Remove jadwal_hari and jadwal_jam from kelompoks table.
 * These fields are not used in this project — scheduling is not a feature.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelompoks', function (Blueprint $table) {
            $table->dropColumn(['jadwal_hari', 'jadwal_jam']);
        });
    }

    public function down(): void
    {
        Schema::table('kelompoks', function (Blueprint $table) {
            $table->string('jadwal_hari')->nullable();
            $table->string('jadwal_jam')->nullable();
        });
    }
};
