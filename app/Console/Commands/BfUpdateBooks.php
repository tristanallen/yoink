<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\BetFairApi;
use App\Models\BetFairUser;
use App\Models\Event;
use Carbon\Carbon;
use App\Models\Market;
use App\Models\Runner;

class BfUpdateBooks extends Command
{
    private $APP_KEY = "oxnRnsZGGOjOwj3L";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bf:updateBooks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $amEvent = Event::whereBetween('date', [Carbon::now()->subHour(), Carbon::now()->addHours(2)])->get();

        foreach($amEvent as $mEvent){

            $mMarket = Market::where('id', $mEvent->market_id)->first();

            $aBook =  $bf->getMarketBook($this->APP_KEY, $SESSION_TOKEN, $mMarket->market_id);

            foreach($aBook as $runner){

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
            };

        }

        $this->info('new books saved');
    }
}
