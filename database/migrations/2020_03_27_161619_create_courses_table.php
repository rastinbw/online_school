<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('launch_date_year')->nullable();
            $table->string('launch_date_month')->nullable();
            $table->string('launch_date_day')->nullable();
            $table->string('online_day')->nullable();
            $table->string('start_hour')->nullable();
            $table->string('start_min')->nullable();
            $table->string('finish_hour')->nullable();
            $table->string('finish_min')->nullable();
            $table->integer('field_id')->nullable();
            $table->integer('grade_id')->nullable();
            $table->integer('teacher_id')->nullable();
            $table->integer('status')->nullable();
            $table->longText('description')->nullable();
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
        Schema::dropIfExists('courses');
    }
}
