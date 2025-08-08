<?php

use App\Models\Certificates;
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
            $table->string('grade')->nullable()->after('score');
            $table->renameColumn('score', 'gpa_score');
            $table->date('issue_date')->nullable()->change();
            $table->enum('status', ['Issued', 'Expired', 'Revoked','Pending','In Review'])->default('Pending')->change();
            $table->dropColumn('certification_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_certifications', function (Blueprint $table) {
            //
            $table->dropColumn('grade');
            $table->renameColumn('gpa_score', 'score');
            $table->date('issue_date')->change();
            $table->unsignedBigInteger('certification_id')->nullable();
        });
    }
};
