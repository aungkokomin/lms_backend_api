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
        Schema::table('user_grant_codes', function (Blueprint $table) {
            //
            $table->string('applied_by')->nullable();
            $table->string('used_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_grant_codes', function (Blueprint $table) {
            //
            $table->dropColumn('applied_by');
            $table->dropColumn('used_by');
        });
    }
};
