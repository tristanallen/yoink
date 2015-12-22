<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBetfairUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('betfair_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('betfair_user');
            $table->string('betfair_password');
            $table->string('betfair_session');
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('betfair_users');
    }
}
