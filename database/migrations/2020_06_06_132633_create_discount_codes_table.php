<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable();
            $table->date('deadline_date')->nullable();
            $table->integer('deadline_date_year')->nullable();
            $table->integer('deadline_date_month')->nullable();
            $table->integer('deadline_date_day')->nullable();
            $table->integer('use_limit')->default(0)->nullable();
            $table->integer('use_count')->default(0)->nullable();
            $table->integer('type')->default(0)->nullable();
            $table->integer('discount_percent')->default(0)->nullable();
            $table->integer('discount_price')->default(0)->nullable();
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
        Schema::dropIfExists('discount_codes');
    }
}
