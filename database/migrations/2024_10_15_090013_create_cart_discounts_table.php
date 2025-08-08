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
        Schema::create('cart_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cart_id');
            $table->unsignedInteger('grant_code_id');
            $table->decimal('applied_amount',10,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_discounts');
    }
};
