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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_uid', 100)->unique();
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['active', 'completed', 'rejected', 'expired'])->default('active');
            $table->decimal('order_price', 8, 2)->default(0);
            $table->string('transaction_id')->nullable();
            $table->json('order_items')->nullable();
            $table->timestamp('order_date')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
