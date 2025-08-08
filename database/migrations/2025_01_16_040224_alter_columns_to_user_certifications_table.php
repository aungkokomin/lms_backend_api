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
        Schema::table('user_certifications', function (Blueprint $table) {
            //
            $table->unsignedInteger('course_id')->nullable()->after('user_id');
            $table->unsignedInteger('module_id')->nullable()->after('course_id');
            $table->renameColumn('user_id', 'student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_certifications', function (Blueprint $table) {
            //
            $table->renameColumn('student_id', 'user_id');
            $table->unsignedInteger('course_id')->change();
            $table->dropColumn('module_id');
        });
    }
};
