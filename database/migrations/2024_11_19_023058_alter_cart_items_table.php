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
        //
        Schema::table('cart_items',function(Blueprint $table){
            $table->dropColumn('course_id');
            $table->dropColumn('course_price');
            $table->string('itemable_type',100);
            $table->unsignedBigInteger('itemable_id');
            $table->decimal('item_price',8,2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('cart_items',function(Blueprint $table){
            $table->unsignedBigInteger('course_id');
            $table->decimal('course_price',8,2);
            $table->dropColumn('itemable_type');
            $table->dropColumn('itemable_id');
            $table->dropColumn('item_price');
        });
    }
};
