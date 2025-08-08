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
        Schema::create('video_streaming_access', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign key linking to users table
            $table->unsignedBigInteger('video_id'); // Foreign key linking to videos table
            $table->string('access_token', 255); // Unique token for the video session (e.g., pre-signed URL or session token)
            $table->timestamp('expires_at')->nullable(); // Expiration time of the token
            $table->timestamps(); // created_at and updated_at fields
            $table->softDeletes(); // deleted_at fields
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_streaming_access');
    }
};
