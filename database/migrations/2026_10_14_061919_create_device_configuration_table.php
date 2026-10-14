<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('device_configuration', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name')->unique();
            $table->string('color')->nullable();
            $table->timestamps();
        });
        DB::table('device_configuration')->insert([
            ['key' => 'backed_up', 'name' => 'Backed up', 'color' => '22c55e', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'not_backed_up', 'name' => 'Not backed up', 'color' => 'ef4444', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_configuration');
    }
};
