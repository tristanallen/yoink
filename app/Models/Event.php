<?php

class Event extends Eloquent
{

    protected $fillable = array(
        'id',
        'market_id',
        'name',
        'date',
    );

    protected $table = 'bf_events';

}