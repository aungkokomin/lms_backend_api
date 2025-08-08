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
            $table->decimal('discounted_fee', 10, 2)->default(0)->after('amount');
            $table->enum('is_discounted', ['true','false'])->default('false')->after('discounted_fee');
            $table->unsignedBigInteger('grant_code')->nullable()->after('is_discounted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            //
            $table->dropColumn('discounted_fee');
            $table->dropColumn('is_discounted');
            $table->dropColumn('grant_code');
        });
    }
};
