<?php

namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use App\BetFairApi;
use App\BetFairApiExample;
use App\BetFairUser;
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

        $aResult = [];

        foreach ($oNext_market->result as $key => $oNext) {
        
            $aResult[$key]['marketId'] = $oNext->marketId; 
            $aResult[$key]['marketName'] = $oNext->marketName;
            $aResult[$key]['event'] = $oNext->event;
            $aResult[$key]['runner'] = [];
            
            //$oBook =  $bf->getMarketBook($this->APP_KEY, $SESSION_TOKEN, $oNext->marketId);
            $oBook = $this->getMarketBook($oNext->marketId);

            foreach ($oNext->runners as $k => $bet) {
                if($bet->selectionId == $oBook[0]->runners[$k]->selectionId && $bet->runnerName == 'The Draw'){
                    $aRunnerBets = [];
                    $aRunnerBets['id'] = $oBook[0]->runners[$k]->selectionId;
                    $aRunnerBets['name'] = $bet->runnerName;
                    $aRunnerBets['availableToLay'] = !empty($oBook[0]->runners[$k]->ex->availableToLay[0]) ? $oBook[0]->runners[$k]->ex->availableToLay[0] : null;
                    $aRunnerBets['status'] = $oBook[0]->runners[$k]->status;
                    $aResult[$key]['runner'][] = $aRunnerBets;
    
                }
                
            }
        }

        return $aResult;
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

        $mMarket = Market::storeMarket($aMarket);
        /*
        dump($aMarket);
        exit;
        */

        return response()->json(['market' => $mMarket]);
    }


    public function getStoredMartkets(){

        $mMarket = Market::all();

        $aMarket = $mMarket->toArray();
        // todo : talk to tris about models as market_id in event and runners is the market pk but market has a market_id so is confusing
        foreach ($aMarket as $key => &$market) {
            $mEvent = Event::where('market_pk', $market['id'])->first();
            if( !empty($mEvent)){
                $market['event'] = $mEvent->toArray();
            }
            $amRunner = Runner::where('market_pk', $market['id'])->get();
             if( !empty($amRunner)){
                $market['runner'] = $amRunner->toArray();
            }
        }

        return view('stored-markets')->with('markets', $aMarket);
    }

    public function getStoredMarket($id){

        $mMarket = Market::where('id', $id)->first();

        return view('market')->with('market', $mMarket->toArray());
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
                    if( $lay->size != $mExistingRunner->size && $lay->price != $mExistingRunner->price && $value->status != $mExistingRunner->status ){
                        $runner = [
                            'id' => $value->selectionId,
                            'market_pk' => $mExistingRunner->market_pk,
                            'name' =>$mExistingRunner->name,
                            'status' => $value->status,
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
                    }
                    
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
        exit;
    }

    public function login($userId){

        $user = BetFairUser::find($userId);

        $postData = 'username='.$user->betfair_user.'&password='.$user->betfair_password;
        //$postData = ["username"=>"furryfool", "password"=>"B3tting5ucks"];

        $output = '';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://identitysso.betfair.com/api/login");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Application: ' . $this->APP_KEY,
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        $output .= 'Post Data: ' . $postData;
        $output .= 'Response: ' . $response;

        $decoded = json_decode($response);

        $user->betfair_session = $decoded->token;
        $user->save();

        return $decoded->token;
    }


}