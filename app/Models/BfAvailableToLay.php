<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BfAvailableToLay extends Model
{

    protected $fillable = array(
        'id',
        'runner_pk',
        'size',
        'price',
        'status',
    );

    protected $table = 'bf_available_to_lay';

}