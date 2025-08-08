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
        Schema::create('video_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign key linking to users table
            $table->unsignedBigInteger('video_id'); // Foreign key linking to videos table
            $table->integer('progress_time'); // Progress in seconds (how much of the video has been watched)
            $table->timestamp('last_watched_at')->nullable(); // Timestamp of the last time the video was watched
            $table->timestamps(); // created_at and updated_at fields
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_progress');
    }
};
