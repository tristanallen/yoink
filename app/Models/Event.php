<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{

    protected $fillable = array(
        'id',
        'market_pk',
        'bf_event_id',
        'name',
        'date',
        'json'
    );

    protected $table = 'bf_events';

}