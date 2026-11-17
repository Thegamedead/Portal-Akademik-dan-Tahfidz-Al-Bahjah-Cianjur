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
        // Drop setorans table to recreate with new structure
        Schema::dropIfExists('setorans');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed - old structure is obsolete
    }
};
