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
        Schema::create('course_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('course_id');
            $table->string('transaction_id',255)->nullable(false);
            $table->decimal('total_amount',10,2);
            $table->date('purchased_date');
            $table->enum('status',['pending','completed','failed']);
            $table->string('payment_method',255);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_purchases');
    }
};
