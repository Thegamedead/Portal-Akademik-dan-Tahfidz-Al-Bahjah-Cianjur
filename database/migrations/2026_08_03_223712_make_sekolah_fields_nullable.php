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
        Schema::table('sekolahs', function (Blueprint $table) {
            // Make fields nullable that were removed from the form
            $table->enum('naungan', ['Kemenag', 'Kemendikbud', 'Lainnya'])->nullable()->change();
            $table->string('no_sk_pendirian')->nullable()->change();
            $table->date('tanggal_sk_pendirian')->nullable()->change();
            $table->string('no_sk_operasional')->nullable()->change();
            $table->date('tanggal_sk_operasional')->nullable()->change();
            $table->string('nama_kepala_sekolah')->nullable()->change();
            $table->string('nip_kepala_sekolah')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            // Revert back to NOT NULL
            $table->enum('naungan', ['Kemenag', 'Kemendikbud', 'Lainnya'])->nullable(false)->change();
            $table->string('no_sk_pendirian')->nullable(false)->change();
            $table->date('tanggal_sk_pendirian')->nullable(false)->change();
            $table->string('no_sk_operasional')->nullable(false)->change();
            $table->date('tanggal_sk_operasional')->nullable(false)->change();
            $table->string('nama_kepala_sekolah')->nullable(false)->change();
            $table->string('nip_kepala_sekolah')->nullable(false)->change();
        });
    }
};
