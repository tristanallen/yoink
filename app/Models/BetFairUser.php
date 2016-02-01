<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BetFairUser extends Model
{

    protected $fillable = array('user_id', 'betfair_user', 'betfair_password', 'betfair_session');

    protected $table = 'betfair_users';

}