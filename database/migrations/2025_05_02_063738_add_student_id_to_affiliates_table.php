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
        Schema::table('affiliates', function (Blueprint $table) {
            //
            $table->string('custom_student_id')->after('id')->nullable();
            // change full_name column length to 255
            $table->string('full_name', 255)->nullable()->change();
            $table->string('identification_no',20)->nullable()->change();
            $table->string('phone_number',20)->nullable()->change();
            $table->string('org_name',255)->nullable()->change();
            $table->string('country',255)->nullable()->change();
            // change 'identification_no' to 'NRIC_number'
            $table->renameColumn('identification_no', 'NRIC_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            //
            $table->dropColumn('custom_student_id');
            $table->renameColumn('NRIC_number', 'identification_no');
        });
    }
};
