<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable();
            $table->integer('course_id')->nullable();
            $table->integer('has_negative_score')->nullable();

            // result access
            $table->integer('result_access_type')->nullable();
            $table->integer('result_access_date_year')->nullable();
            $table->integer('result_access_date_month')->nullable();
            $table->integer('result_access_date_day')->nullable();
            $table->integer('result_access_date_hour')->nullable();
            $table->integer('result_access_date_min')->nullable();

            // question and answer access date
            $table->integer('qa_access_type')->nullable();
            $table->integer('qa_access_date_year')->nullable();
            $table->integer('qa_access_date_month')->nullable();
            $table->integer('qa_access_date_day')->nullable();
            $table->integer('qa_access_date_hour')->nullable();
            $table->integer('qa_access_date_min')->nullable();

            // exam holding type and date
            $table->integer('exam_holding_type')->nullable();
            $table->integer('exam_duration')->nullable();
            $table->integer('exam_date_start_year')->nullable();
            $table->integer('exam_date_start_month')->nullable();
            $table->integer('exam_date_start_day')->nullable();
            $table->integer('exam_date_start_hour')->nullable();
            $table->integer('exam_date_start_min')->nullable();
            $table->integer('exam_date_finish_year')->nullable();
            $table->integer('exam_date_finish_month')->nullable();
            $table->integer('exam_date_finish_day')->nullable();
            $table->integer('exam_date_finish_hour')->nullable();
            $table->integer('exam_date_finish_min')->nullable();

            // files
            $table->text('questions_file')->nullable();
            $table->text('answers_file')->nullable();

            // options and factors
            $table->longText('options')->nullable();
            $table->longText('factors')->nullable();

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
        Schema::dropIfExists('tests');
    }
}
