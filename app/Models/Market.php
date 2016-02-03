<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Market extends Model
{

    protected $fillable = array(
        'id',
        'market_id',
        'name',
        'json',
    );

    protected $table = 'bf_markets';

}