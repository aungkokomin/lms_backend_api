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
        Schema::table('payments', function (Blueprint $table) {
            //
            $table->longText('payment_reference')->nullable();  // Reference for the payment
            $table->string('payment_method')->nullable()->change();  // Method used for the payment
            $table->enum('status', ['pending', 'completed', 'failed','refunded'])->default('pending')->change();  // Status of the payment
            $table->string('order_uid')->nullable();  // Unique ID for the order
            $table->decimal('amount', 10, 2);  // Total amount paid
            $table->string('transaction_id')->unique();  // Unique ID for the payment transaction
            $table->string('currency')->default('USD');  // Currency of the transaction
            $table->text('payment_details')->nullable();  // Additional payment details, e.g., provider details
            $table->timestamp('payment_date')->nullable();  // Date and time the payment was made
            $table->timestamp('completed_at')->nullable();  // Date and time payment was completed
            $table->dropColumn('course_purchases_id');
            $table->dropColumn('payment_gateway_response');  // Reference for the payment
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            //
            $table->unsignedInteger('course_purchases_id');
            $table->longText('payment_gateway_response');
            $table->dropColumn('payment_reference');
            // $table->string('payment_method')->change();
            // $table->enum('status', ['pending', 'completed', 'failed'])->change();
            $table->dropColumn('order_uid');
            $table->dropColumn('amount');
            $table->dropColumn('transaction_id');
            $table->dropColumn('currency');
            $table->dropColumn('payment_details');
            $table->dropColumn('payment_date');
            $table->dropColumn('completed_at');
        });
    }
};
