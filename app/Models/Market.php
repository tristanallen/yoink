<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Market extends Model
{

    protected $fillable = array(
        'id',
        'bf_market_id',
        'name',
        'json',
    );

    protected $table = 'bf_markets';

    public static function storeMarket($aMarket)
    {

        $mMarket = Market::where('bf_market_id', $aMarket['marketId'])->first();

        if( $mMarket == null )
        {
            $mMarket = Market::create([
                'bf_market_id' => $aMarket['marketId'],
                'name' => $aMarket['marketName']
            ]);
        }

        $mEvent = Event::where('market_pk', $mMarket->id)->where('date', $aMarket['event']['openDate'])->first();
        if( $mEvent == null )
        {
            Event::create([
                'bf_event_id' => $aMarket['event']['id'],
                'market_pk' => $mMarket->id,
                'name' => $aMarket['event']['name'],
                'date' => $aMarket['event']['openDate']
            ]);
        }

        $mRunner = Runner::where('market_pk', $mMarket->id)->where('size', $aMarket['runner'][0]['availableToLay']['size'])->first();
        if( $mRunner == null )
        {
            Runner::create([
                'bf_runner_id' => $aMarket['runner'][0]['id'],
                'market_pk' => $mMarket->id,
                'name' => $aMarket['runner'][0]['name'],
                'status' => $aMarket['runner'][0]['status'],
                'size' => $aMarket['runner'][0]['availableToLay']['size'],
                'price' => $aMarket['runner'][0]['availableToLay']['price']
            ]);
        }

        return $mMarket;
    }

}