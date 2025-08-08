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
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('qualification',100);
            $table->unsignedInteger('experienced_years');
            $table->string('specialization',255);
            $table->longText('bio');
            $table->longText('certifications');
            $table->decimal('hourly_rate',8,2);
            $table->string('availability_schedule',255);
            $table->float('rating');
            $table->string('social_links',255);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructors');
    }
};
