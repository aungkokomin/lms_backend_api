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
        Schema::table('commissions', function (Blueprint $table) {
            //
            $table->decimal('applied_commission_rate', 8, 2)->default(0)->nullable()->after('commission_amount');
            $table->integer('payment_amount')->default(200)->nullable()->after('applied_commission_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            //
            $table->dropColumn('applied_commission_rate');
            $table->dropColumn('payment_amount');
        });
    }
};
