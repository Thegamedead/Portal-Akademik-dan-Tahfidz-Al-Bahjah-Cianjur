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
        Schema::table('siswas', function (Blueprint $table) {
            if (Schema::hasColumn('siswas', 'foto')) {
                $table->dropColumn('foto');
            }
            if (Schema::hasColumn('siswas', 'nik')) {
                $table->dropColumn('nik');
            }
            if (Schema::hasColumn('siswas', 'agama')) {
                $table->dropColumn('agama');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('kewarganegaraan');
            $table->string('nik', 16)->nullable()->after('kewarganegaraan');
            $table->string('agama')->after('kewarganegaraan');
        });
    }
};
