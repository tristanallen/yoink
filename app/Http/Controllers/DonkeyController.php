<?php

namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use App\BetFairApi;
use App\BetFairApiExample;
use App\BetFairUser;

class DonkeyController extends Controller
{
    private $APP_KEY = "oxnRnsZGGOjOwj3L";
    private $football_id = 1;

    public function index()
    {
    
        $user = BetFairUser::find(1);

        $SESSION_TOKEN = $user->betfair_session;
       
        $aOutput = [];
        $aOutput['next_market'] = $this->getNextMarketStats($SESSION_TOKEN);

        return view('welcome')->with('output', $aOutput);
    }

    /**
    * returns next availaable market with its event deatils and runners
    **/
    public function getNextMarketStats($psSESSION_TOKEN){

        $bf = new BetFairApi();

        $SESSION_TOKEN = $psSESSION_TOKEN;

        $aAllEventTypes = $bf->getAllEventTypes($this->APP_KEY, $SESSION_TOKEN);

        $iFootballTypeId = $bf->extractFootballEventTypeId($aAllEventTypes);

        $oNext_market = $bf->getNextMarket($this->APP_KEY, $SESSION_TOKEN, $iFootballTypeId);

        $aResult = [];

        foreach ($oNext_market->result as $key => $oNext) {
            $oBook =  $bf->getMarketBook($this->APP_KEY, $SESSION_TOKEN, $oNext->marketId);
            $oBook = $oBook->result;
            $aResult[$key]['marketId'] = $oNext->marketId; 
            $aResult[$key]['marketName'] = $oNext->marketName;
            $aResult[$key]['event'] = $oNext->event;
            $aResult[$key]['bets'] = [];
            
            foreach ($oNext->runners as $k => $bet) {
                if($bet->selectionId == $oBook[0]->runners[$k]->selectionId && $bet->runnerName == 'The Draw'){
    
                    $aRunnerBets = [];
                    $aRunnerBets['id'] = $oBook[0]->runners[$k]->selectionId;
                    $aRunnerBets['name'] = $bet->runnerName;
                    $aRunnerBets['availableToLay'] = !empty($oBook[0]->runners[$k]->ex->availableToLay[0]) ? $oBook[0]->runners[$k]->ex->availableToLay[0] : null;


                    $aResult[$key]['bets'][] = $aRunnerBets;
    
                }
                
            }
        }

        return $aResult;
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