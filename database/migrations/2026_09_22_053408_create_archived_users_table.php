<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archived_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_user_id')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('username');
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->string('email');
            $table->string('mobile_number');
            $table->string('birthdate')->nullable();
            $table->string('gender')->nullable();
            $table->text('address')->nullable();
            $table->string('role');

            $table->enum('archive_type', ['rejected', 'terminated']);
            $table->text('reason')->nullable();
            $table->foreignId('actioned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('actioned_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archived_users');
    }
};
