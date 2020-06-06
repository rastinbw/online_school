<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_accesses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('student_id')->nullable();
            $table->integer('course_id')->nullable();
            $table->integer('has_access')->nullable()->default(1);
            $table->integer('changeable')->nullable()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_accesses');
    }
}
