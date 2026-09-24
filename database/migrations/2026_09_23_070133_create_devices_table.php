<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_name');
            $table->string('category'); // e.g. router, switch, server, printer, workstation
            $table->string('status')->default('active'); // active, inactive, maintenance, decommissioned
            $table->string('ip_address')->nullable()->unique();
            $table->string('mac_address')->nullable()->unique();
            $table->string('vlan')->nullable();
            $table->string('manufacturer')->nullable(); // e.g. Cisco, HP, Dell
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable()->unique();
            $table->string('location')->nullable();
            $table->string('rack')->nullable();
            $table->string('port')->nullable();
            $table->string('firmware')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
