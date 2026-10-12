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
        Schema::table('devices', function (Blueprint $table) {
        $table->string('property_no_or_serial_no')->nullable()->unique()->after('device_name');
        $table->string('sub_par')->nullable()->after('property_no_or_serial_no');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
        $table->dropColumn(['property_no_or_serial_no', 'sub_par']);
    });
    }
};
