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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // The referrer who will receive the commission
            $table->string('referral_id',100)->nullable(); // The referral that generated the commission
            $table->decimal('commission_amount', 8, 2)->nullable(); // Commission amount
            $table->string('description',256)->nullable(); // Description of the commission
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
