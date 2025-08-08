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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255); // Title of the video
            $table->text('description')->nullable(); // Description of the video content
            $table->string('video_url', 255); // URL of the video (e.g., S3 or external URL)
            $table->integer('video_duration')->nullable(); // Length of the video in seconds
            $table->enum('status', ['draft','published'])->default('draft');
            $table->timestamps(); // created_at and updated_at fields
            $table->softDeletes(); // deleted_at fields
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
