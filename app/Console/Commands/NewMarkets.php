<?php

namespace App\Console\Commands;

use App\Models\AvailableToLay;
use Illuminate\Console\Command;
use App\BetFairApi;
use App\Models\BetFairUser;
use App\Models\Event;
use App\Models\Market;
use App\Models\Runner;
use App\Models\BfAvailableToBack;
use App\Models\BfAvailableToLay;

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

            $mMarket = Market::where('bf_market_id', $aMarket['marketId'])->first();

            if( $mMarket == null )
            {
                $mMarket = Market::create([
                    'bf_market_id' => $aMarket['marketId'],
                    'name' => $aMarket['marketName'],
                    'json' => $aMarket['json'],
                ]);
            }
            $this->info('new market saved');

            $mEvent = Event::where('market_pk', $mMarket->id)->where('date', $aMarket['event']->openDate)->first();
            if( $mEvent == null )
            {
                Event::create([
                    'market_pk' => $mMarket->id,
                    'name' => $aMarket['event']->name,
                    'date' => $aMarket['event']->openDate,
                    'json' => json_encode($aMarket['event']),
                ]);
            }


            $this->info('new event saved');

            foreach($aMarket['runner'] as $key => $runner){

                $mRunner = Runner::where('market_pk', $mMarket->id)->first();

                if( $mRunner == null )
                {
                    $mRunner = Runner::create([
                        'bf_runner_id' => $runner['id'],
                        'market_pk' => $mMarket->id,
                        'name' => $runner['name'],
                        'status' => $runner['status'],
                        'json' => $runner['json'],
                    ]);

                    foreach($runner['availableToLay'] as $l => $lay){
                        BfAvailableToLay::create([
                            'runner_pk' =>  $mRunner->id,
                            'size' => $lay['size'],
                            'price' => $lay['price'],
                        ]);
                    }

                    foreach($runner['availableToBack'] as $l => $back){
                        BfAvailableToBack::create([
                            'runner_pk' =>  $mRunner->id,
                            'size' => $back['size'],
                            'price' => $back['price'],
                        ]);
                    }
                }




                $this->info('new runner saved');
            }
        }
    }
}
