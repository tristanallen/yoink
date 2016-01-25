<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bf_markets', function (Blueprint $table) {
        $table->increments('id');
        $table->string('market_id');
        $table->string('name');
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
        Schema::drop('bf_markets');
    }
}