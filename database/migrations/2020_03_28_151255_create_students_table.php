<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('national_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('parent_phone_number')->nullable();
            $table->string('parent_code')->nullable();
            $table->string('password')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->integer('gender')->nullable();
            $table->integer('field_id')->nullable();
            $table->integer('grade_id')->nullable();
            $table->text('enrollment_certificate_image')->nullable();
            $table->text('national_card_image')->nullable();
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
        Schema::dropIfExists('students');
    }
}
