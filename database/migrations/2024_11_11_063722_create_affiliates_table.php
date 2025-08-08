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
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('agent_code',50);
            $table->string('assigned_region',50)->nullable();
            $table->integer('total_recruits')->nullable();
            $table->decimal('commission_rate',8,2)->nullable();
            $table->decimal('performance_score',8,2)->nullable();
            $table->integer('monthly_target',false,true)->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
