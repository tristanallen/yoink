<?php

class Runner extends Eloquent
{

    protected $fillable = array(
        'id',
        'market_id',
        'name',
        'size',
        'price',
    );

    protected $table = 'bf_runners';

}