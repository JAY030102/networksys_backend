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
        // xxxx_create_device_models_table.php
        Schema::create('device_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturer_id')->constrained('device_manufacturers')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->unique(['manufacturer_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_models');
    }
};
