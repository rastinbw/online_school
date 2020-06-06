<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('installment_type_id')->nullable();
            $table->integer('installment_id')->nullable();
            $table->string('authority')->nullable();
            $table->string('card_pan_hash')->nullable();
            $table->string('card_pan_mask')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['installment_type_id']);
            $table->dropColumn(['installment_id']);
            $table->dropColumn(['authority']);
            $table->dropColumn(['card_pan_hash']);
            $table->dropColumn(['card_pan_mask']);
        });
    }
}
