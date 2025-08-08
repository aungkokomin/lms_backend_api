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
        Schema::table('students', function (Blueprint $table) {
            //
            $table->timestamp('grant_applied_at')->nullable();
            $table->timestamp('grant_approved_at')->nullable();
            $table->timestamp('grant_rejected_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            //
            if (Schema::hasColumn('students', 'grant_applied_at')){
                $table->dropColumn('grant_applied_at');
            }
            if (Schema::hasColumn('students', 'grant_approved_at')){
                $table->dropColumn('grant_approved_at');
            }
            if (Schema::hasColumn('students', 'grant_rejected_at')){
                $table->dropColumn('grant_rejected_at');
            }
        });
    }
};
