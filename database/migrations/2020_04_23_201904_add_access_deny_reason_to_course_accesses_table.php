<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccessDenyReasonToCourseAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_accesses', function (Blueprint $table) {
            $table->integer('access_deny_reason')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_accesses', function (Blueprint $table) {
            $table->dropColumn(['access_deny_reason']);
        });
    }
}
