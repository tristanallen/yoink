<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    protected $fillable = array(
        'id',
        'market_id',
        'name',
        'date',
    );

    protected $table = 'bf_events';

}