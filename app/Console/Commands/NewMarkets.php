<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\BetFairApi;
use App\Models\BetFairUser;
use App\Models\Event;
use App\Models\Market;
use App\Models\Runner;

class NewMarkets extends Command
{

    private $APP_KEY = "oxnRnsZGGOjOwj3L";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:newMarket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'retrieves new market list';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $bf = new BetFairApi();

        $user = BetFairUser::find(1);

        $SESSION_TOKEN = $user->betfair_session;

        $aAllEventTypes = $bf->getAllEventTypes($this->APP_KEY, $SESSION_TOKEN);

        $iFootballTypeId = $bf->extractFootballEventTypeId($aAllEventTypes);

        $oNext_market = $bf->getNextMarket($this->APP_KEY, $SESSION_TOKEN, $iFootballTypeId);

        foreach ($oNext_market as $key => &$oNext) {

            $oBook =  $bf->getMarketBook($this->APP_KEY, $SESSION_TOKEN, $oNext['marketId']);
            $oNext['runner'] = $oBook;
        }

        $this->info('got '.count($oNext_market).' new markets');

        foreach($oNext_market as $aMarket){

            $mMarket = Market::where('market_id', $aMarket['marketId'])->first();

            if( $mMarket == null )
            {
                $mMarket = Market::create([
                    'market_id' => $aMarket['marketId'],
                    'name' => $aMarket['marketName']
                ]);
            }
            $this->info('new market saved');

            $mEvent = Event::where('market_id', $mMarket->id)->where('date', $aMarket['event']->openDate)->first();
            if( $mEvent == null )
            {
                Event::create([
                    'id' => $aMarket['event']->id,
                    'market_id' => $mMarket->id,
                    'name' => $aMarket['event']->name,
                    'date' => $aMarket['event']->openDate
                ]);
            }


            $this->info('new event saved');

            foreach($aMarket['runner'] as $runner){

                $mRunner = Runner::where('market_id', $mMarket->id)->where('size',$runner['availableToLay']->size)->where('size',$runner['availableToLay']->size)->where('status', $runner['status'])->first();

                if( $mRunner == null )
                {
                    Runner::create([
                        'id' => $runner['id'],
                        'market_id' => $mMarket->id,
                        'name' => $runner['name'],
                        'status' => $runner['status'],
                        'size' => $runner['availableToLay']->size,
                        'price' => $runner['availableToLay']->price
                    ]);
                }

                $this->info('new runner saved');
            }
        }
    }
}
