<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('device_categories', function (Blueprint $table) {
            $table->string('color')->nullable()->after('name');
        });
        Schema::table('device_statuses', function (Blueprint $table) {
            $table->string('color')->nullable()->after('name');
        });
        Schema::table('device_manufacturers', function (Blueprint $table) {
            $table->string('color')->nullable()->after('name');
        });
        Schema::table('device_models', function (Blueprint $table) {
            $table->string('color')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('device_categories', function (Blueprint $table) {
            $table->dropColumn('color');
        });

        Schema::table('device_statuses', function (Blueprint $table) {
            $table->dropColumn('color');
        });

        Schema::table('device_manufacturers', function (Blueprint $table) {
            $table->dropColumn('color');
        });

        Schema::table('device_models', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
