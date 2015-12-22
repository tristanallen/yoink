<?php
/**
 * Created by PhpStorm.
 * User: Tris
 * Date: 20/12/2015
 * Time: 18:07
 */

namespace App;

use \Eloquent;


class BetFairUser extends Eloquent{

    protected $table = 'betfair_users';
    protected $fillable = array('user_id', 'betfair_user', 'betfair_password', 'betfair_session');

}