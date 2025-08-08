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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name',30);
            $table->string('NRIC_number',30)->nullable();
            $table->string('nationality',30)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->longText('address')->nullable();
            $table->string('zip_code', 10)->nullable();
            $table->string('email');
            $table->string('phone_number')->nullable();
            $table->string('city',30)->nullable();
            $table->string('referral_id');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->boolean('gender')->default(0);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
