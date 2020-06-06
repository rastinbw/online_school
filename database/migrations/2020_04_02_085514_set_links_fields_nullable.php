<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetLinksFieldsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->text('telegram')->nullable()->change();
            $table->text('instagram')->nullable()->change();
            $table->text('email')->nullable()->change();
            $table->text('tel1')->nullable()->change();
            $table->text('tel2')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->text('telegram')->change();
            $table->text('instagram')->change();
            $table->text('email')->change();
            $table->text('tel1')->change();
            $table->text('tel2')->change();
        });
    }
}
