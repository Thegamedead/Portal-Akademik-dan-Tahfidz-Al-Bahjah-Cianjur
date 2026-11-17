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
        Schema::table('gurus', function (Blueprint $table) {
            $table->string('password')->nullable()->after('tanggal_lahir');
            $table->string('password_text')->nullable()->after('password');
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->string('password')->nullable()->after('tanggal_lahir');
            $table->string('password_text')->nullable()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn(['password', 'password_text']);
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['password', 'password_text']);
        });
    }
};
