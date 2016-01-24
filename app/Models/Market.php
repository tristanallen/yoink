<?php

class Market extends Eloquent
{

    protected $fillable = array(
        'id',
        'name',
    );

    protected $table = 'bf_markets';

}