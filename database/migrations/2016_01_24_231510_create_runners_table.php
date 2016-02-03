<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRunnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bf_runners', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('market_pk');
            $table->integer('bf_runner_id');
            $table->string('name');
            $table->string('size');
            $table->string('price');
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
        Schema::drop('bf_runners');
    }
}