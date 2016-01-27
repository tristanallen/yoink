<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Runner extends Model
{

    protected $fillable = array(
        'id',
        'market_id',
        'name',
        'size',
        'price',
        'status'
    );

    protected $table = 'bf_runners';

}