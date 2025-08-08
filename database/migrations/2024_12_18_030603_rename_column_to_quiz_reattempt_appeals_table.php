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
        Schema::table('quiz_reattempt_appeals', function (Blueprint $table) {
            //
            $table->renameColumn('user_id','student_id');
            $table->timestamp('decision_date')->nullable()->change();
            $table->renameColumn('decision_date','approved_at');
            $table->timestamp('rejected_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_reattempt_appeals', function (Blueprint $table) {
            //
            $table->renameColumn('student_id','user_id');
            $table->renameColumn('approved_at','decision_date');
            $table->dropColumn('rejected_at');
        });
    }
};
