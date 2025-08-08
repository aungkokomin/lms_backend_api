<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToAffiliatesTable extends Migration
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->string('identification_no')->nullable();
            $table->string('full_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('org_name')->nullable();
            $table->string('country')->nullable();
            $table->dateTime('affiliate_applied_at')->after('last_accessed_at')->nullable();
            $table->dateTime('affiliate_approved_at')->after('affiliate_applied_at')->nullable();
            $table->dateTime('affiliate_rejected_at')->after('affiliate_approved_at')->nullable();
        });
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->dropColumn([
                'identification_no',
                'full_name',
                'phone_number',
                'org_name',
                'country',
                'applied_at',
                'approved_at',
                'rejected_at'
            ]);
        });
    }
}