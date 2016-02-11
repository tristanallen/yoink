<?php

namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use App\BetFairApi;
use App\Models\BetFairUser;
use App\Models\Event;
use App\Models\Market;
use App\Models\Runner;

class DonkeyController extends Controller
{
    private $APP_KEY = "oxnRnsZGGOjOwj3L";
    private $football_id = 1;
    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function index()
    {

        $SESSION_TOKEN = $this->getSessionToken();
       
        $aOutput = [];
        $aOutput['next_market'] = $this->getNextMarketStats();

        return view('welcome')->with('output', $aOutput);
    }

    public function getSessionToken(){

        $user = BetFairUser::find(1);

        $SESSION_TOKEN = $user->betfair_session;

        return $SESSION_TOKEN;
    }

    /**
    * returns next availaable market with its event deatils and runners
    **/
    public function getNextMarketStats(){

        $bf = new BetFairApi();

        $SESSION_TOKEN = $this->getSessionToken();

        $aAllEventTypes = $bf->getAllEventTypes($this->APP_KEY, $SESSION_TOKEN);

        $iFootballTypeId = $bf->extractFootballEventTypeId($aAllEventTypes);

        $oNext_market = $bf->getNextMarket($this->APP_KEY, $SESSION_TOKEN, $iFootballTypeId);

        foreach ($oNext_market as $key => &$oNext) {

            $oBook =  $bf->getMarketBook($this->APP_KEY, $SESSION_TOKEN, $oNext['marketId']);
            $oNext['runner'] = $oBook;
        }

        return $oNext_market;
    }


    public function getMarketBook($piMarketId = null){

        $bf = new BetFairApi();

        $SESSION_TOKEN = $this->getSessionToken();

        $iMarketId = isset($piMarketId) ? $piMarketId : (double)$this->request->input('marketId');

        $oBook =  $bf->getMarketBook($this->APP_KEY, $SESSION_TOKEN, $iMarketId);

        $oBook = $oBook->result;

        return $oBook;
    }

    public function storeAllMarkets()
    {
        $this->storeMarket();
    }

    public function storeMarket(){
        $aMarket = $this->request->input('market');


        $mMarket = Market::where('market_id', $aMarket['marketId'])->first();

        if( $mMarket == null )
        {
            $mMarket = Market::create([
                'market_id' => $aMarket['marketId'],
                'name' => $aMarket['marketName']
            ]);
        }

        $mEvent = Event::where('market_id', $mMarket->id)->where('date', $aMarket['event']['openDate'])->first();
        if( $mEvent == null )
        {
            Event::create([
                'id' => $aMarket['event']['id'],
                'market_id' => $mMarket->id,
                'name' => $aMarket['event']['name'],
                'date' => $aMarket['event']['openDate']
            ]);
        }

        foreach($aMarket['runner'] as $runner){

            $mRunner = Runner::where('market_id', $mMarket->id)->where('size',$runner['availableToLay']['size'])->first();

            if( $mRunner == null )
            {
                Runner::create([
                    'id' => $runner['id'],
                    'market_id' => $mMarket->id,
                    'name' => $runner['name'],
                    'status' => $runner['status'],
                    'size' => $runner['availableToLay']['size'],
                    'price' => $runner['availableToLay']['price']
                ]);
            }
        }

        return response()->json(['market' => $mMarket]);
    }


    public function getStoredMartkets(){

        $mMarket = Market::all();

        $aMarket = $mMarket->toArray();

        foreach ($aMarket as $key => &$market) {
            $mEvent = Event::where('market_pk', $market['id'])->first();
            if( !empty($mEvent)){
                $market['event'] = $mEvent->toArray();
            }

            $amRunner = Runner::where('market_pk', $market['id'])->with('Lays')->with('Backs')->get();

             if( !empty($amRunner)){
                $market['runner'] = $amRunner->toArray();
            }
        }

        return view('stored-markets')->with('markets', $aMarket);
    }

    public function getStoredMarket($id){

        $mMarket = Market::where('id', $id)->first();
        $aMarket = $mMarket->toArray();

        $mEvent = Event::where('market_pk', $id)->first();
        if( !empty($mEvent)){
            $aMarket['event'] = $mEvent->toArray();
        }
        $amRunner = Runner::where('market_pk', $id)->with('Lays')->with('Backs')->get();

        if( !empty($amRunner)){
            $aMarket['runner'] = $amRunner->toArray();
        }

        $startDate = date_create($aMarket['event']['date']);

        $runnerDate = [];
        $runnerLay = [];
        $runnerBack = [];
        $runnerProfit = [];
        $stakeAdded = false;
        foreach($aMarket['runner'] as $key => $runner){

            if(array_key_exists(0, $runner['lays']) && $runner['lays'][0]['price'] < 40) {
                $runnerDate[] = [(date_diff($startDate, date_create($runner['lays'][0]['created_at']))->format('%i'))];
                $runnerLay[] = [$runner['lays'][0]['price']];
                $runnerBack[] = [$runner['backs'][0]['price']];

                if((date_diff($startDate, date_create($runner['lays'][0]['created_at']))->format('%i')) < 5 && !$stakeAdded){
                    $stakeAdded = true;
                    $liability = ($runner['lays'][0]['price'] - 1) * 10;
                }

                if( !empty($liability) ){
                    $backStake = $liability - ($runner['backs'][0]['price'] - 1);
                    $runnerProfit[] = 10 - $backStake;

                }
                else{
                    $runnerProfit[] = 0;
                }


            }

            if (sizeof($runnerLay) > 1){
                if($runnerLay[sizeof($runnerLay)-1] == $runnerLay[sizeof($runnerLay)-2]  ) {

                    array_pop($runnerDate);
                    array_pop($runnerLay);
                    array_pop($runnerBack);
                    array_pop($runnerProfit);
                }
            }


        }
      //  exit;

        //return var_dump($runner1);
        //return var_dump($aMarket);

        $chartData = [
            'labels' => $runnerDate,
				'datasets' => [
					[
                        'label' => 'LAY',
						'fillColor'=> 'rgba(255,255,255,0)',
						'strokeColor'=> 'rgba(246, 148, 170, 1)',
						'pointColor'=> 'rgba(246, 148, 170, 1)',
						'pointStrokeColor'=> '#fff',
						'pointHighlightFill'=> '#fff',
						'data'=> $runnerLay
					],
                    [
                        'label' => 'BACK',
                        'fillColor'=> 'rgba(255,255,255,0)',
                        'strokeColor'=> 'rgba(166, 216, 255, 1)',
                        'pointColor'=> 'rgba(166, 216, 255, 1)',
                        'pointStrokeColor'=> '#fff',
                        'pointHighlightFill'=> '#fff',
                        'data'=> $runnerBack
                    ],
                    [
                        'label' => 'PROFIT',
                        'fillColor'=> 'rgba(255,255,255,0)',
                        'strokeColor'=> 'rgba(166, 200, 255, 1)',
                        'pointColor'=> 'rgba(166,, 255, 1)',
                        'pointStrokeColor'=> '#fff',
                        'pointHighlightFill'=> '#fff',
                        'data'=> $runnerProfit
                    ]


				]
			];

        return view('market')->with('market', $aMarket)->with('chartData', json_encode($chartData))->with('liability', $liability);
    }

    /**
    * stores odds for given market
    **/
    public function updateMarketBook( $poBook = null){

        $dMarketId = (double)$this->request->input('marketId');

        $mMarket = Market::where('market_pk', $dMarketId)->first();

        $oBook = $this->getMarketBook($dMarketId);

        foreach ($oBook[0]->runners as $key => $value) {

            $mExistingRunner = Runner::where('market_pk', $mMarket->id )->where('id' , $value->selectionId)->first();

            if($mExistingRunner){
                foreach($value->ex->availableToLay as $lay ){


                    //if( $lay->size != $mExistingBook->size && $lay->price != $mExistingBook->price && $value->status != $mExistingBook->status ){
                        $runner = [
                            'id' => $value->selectionId,
                            'market_id' => $mExistingBook->market_id,
                            'name' =>$mExistingBook->name,
                            'status' => $oBook[0]->status,
                            'size' => $lay->size,
                            'price' => $lay->price
                        ];
 
                        Runner::create([
                           'id' => $runner['id'],
                           'market_pk' => $runner['market_pk'],
                           'name' => $runner['name'],
                           'status' => $runner['status'],
                           'size' => $runner['size'],
                           'price' => $runner['price']
                        ]);

                    //}
                    
                };

            }
            
            
        }


        /*
        Runner::create([
               'id' => $aMarket['runner'][0]['id'],
               'market_pk' => $mMarket->id,
               'name' => $aMarket['runner'][0]['name'],
               'status' => $aMarket['runner'][0]['status'],
               'size' => $aMarket['runner'][0]['availableToLay']['size'],
               'price' => $aMarket['runner'][0]['availableToLay']['price']
            ]);
        */    

    }

    public function login($userId){

        $user = BetFairUser::find($userId);

        $bf = new BetFairApi();

        $decoded = $bf->newSession($user);

        $user->betfair_session = $decoded->token;
        $user->save();

        return $decoded->token;
    }




}