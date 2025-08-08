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
        Schema::create('user_certifications', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('certification_id');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->string('certificate_file')->nullable(); // Path to certificate PDF/image
            $table->enum('status', ['Issued', 'Expired', 'Revoked'])->default('Issued');
            $table->integer('score')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_certifications');
    }
};
