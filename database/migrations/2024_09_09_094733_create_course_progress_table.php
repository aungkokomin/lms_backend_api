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
        Schema::create('course_progress', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id'); // Foreign key linking to users table
            $table->unsignedBigInteger('course_id'); // Foreign key linking to courses table
            $table->integer('completed_modules')->default(0); // Number of completed modules
            $table->integer('total_modules'); // Total number of modules in the course
            $table->decimal('progress_percentage', 5, 2)->default(0); // Progress percentage (calculated)
            $table->timestamps(); // created_at and updated_at fields
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_progress');
    }
};
