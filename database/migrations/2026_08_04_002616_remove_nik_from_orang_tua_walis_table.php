<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orang_tua_walis', function (Blueprint $table) {
            if (Schema::hasColumn('orang_tua_walis', 'nik')) {
                $table->dropColumn('nik');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orang_tua_walis', function (Blueprint $table) {
            $table->string('nik', 16)->nullable()->after('email');
        });
    }
};
