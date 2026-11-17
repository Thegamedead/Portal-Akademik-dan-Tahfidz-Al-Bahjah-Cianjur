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
            $table->dropColumn('kewarganegaraan');
        });

        Schema::table('orang_tua_walis', function (Blueprint $table) {
            $table->dropColumn(['no_telepon', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('kewarganegaraan')->nullable()->after('tanggal_lahir');
        });

        Schema::table('orang_tua_walis', function (Blueprint $table) {
            $table->string('no_telepon')->nullable()->after('penghasilan');
            $table->string('email')->nullable()->after('no_telepon');
        });
    }
};
