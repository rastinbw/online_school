<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('date_year')->nullable();
            $table->string('date_month')->nullable();
            $table->string('date_day')->nullable();
            $table->string('start_hour')->nullable();
            $table->string('start_min')->nullable();
            $table->string('finish_hour')->nullable();
            $table->string('finish_min')->nullable();
            $table->integer('course_id')->nullable();
            $table->integer('status')->nullable();
            $table->text('video_link')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('sessions');
    }
}
