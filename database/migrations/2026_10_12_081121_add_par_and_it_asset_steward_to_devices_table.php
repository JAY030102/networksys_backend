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
        $table->string('it_asset_steward')->nullable()->after('sub_par');
        $table->string('par')->nullable()->after('property_no_or_serial_no');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
        $table->dropColumn(['par', 'it_asset_steward']);
    });
    }
};
