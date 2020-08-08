<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('success')->nullable()->default(0);
            $table->string('title')->nullable();
            $table->string('issue_tracking_no')->nullable();
            $table->string('order_no')->nullable();
            $table->integer('paid_amount')->nullable();
            $table->integer('plan_id')->nullable();
            $table->integer('transaction_payment_type')->nullable();
            $table->string('date_year')->nullable();
            $table->string('date_month')->nullable();
            $table->string('date_day')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
