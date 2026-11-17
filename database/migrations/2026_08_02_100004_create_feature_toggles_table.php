<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_toggles', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        // Insert initial feature toggles
        DB::table('feature_toggles')->insert([
            [
                'key' => 'tes_penempatan',
                'name' => 'Tes Penempatan Siswa',
                'description' => 'Mengaktifkan fitur tes penempatan bagi siswa baru atau pindahan.',
                'is_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_toggles');
    }
};
