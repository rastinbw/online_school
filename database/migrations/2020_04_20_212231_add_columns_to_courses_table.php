<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('room_id')->nullable();
            $table->string('room_link')->nullable();
            $table->integer('sessions_number')->nullable();
            $table->integer('guest_login')->nullable()->default(0);
            $table->integer('guest_limit')->nullable()->default(0);
            $table->integer('op_login_first')->nullable()->default(0);
            $table->integer('max_users')->nullable()->default(0);
            $table->integer('is_online')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['room_id']);
            $table->dropColumn(['room_link']);
            $table->dropColumn(['sessions_number']);
            $table->dropColumn(['guest_login']);
            $table->dropColumn(['guest_limit']);
            $table->dropColumn(['op_login_first']);
            $table->dropColumn(['max_users']);
            $table->dropColumn(['is_online']);
        });
    }
}
