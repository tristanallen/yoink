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
    
        $bf = new BetFairApi();
        $output = '';

        $user = BetFairUser::find(1);

        $SESSION_TOKEN = $user->betfair_session;

        //$output = $this->testExample();
        $aAllEventTypes = $bf->getAllEventTypes($this->APP_KEY, $SESSION_TOKEN);

        $iFootballTypeId = $bf->extractFootballEventTypeId($aAllEventTypes);

        $oNext_market = $bf->getNextMarket($this->APP_KEY, $SESSION_TOKEN, $iFootballTypeId);

        foreach ($oNext_market->result as $key => &$oNext) {
            if($oNext->totalMatched > 0){
                $oBook =  $bf->getMarketBook($this->APP_KEY, $SESSION_TOKEN, $oNext->marketId);
                $oEvent = $bf->getEventDetails($this->APP_KEY, $SESSION_TOKEN, $oNext->marketId);
                $oNext->event =$oEvent->result;
                $oNext->book = $oBook->result;
            }
            else{
                unset($oNext_market->result[$key]);
            }
        }


        $aOutput = [];
        $aOutput['event_types'] = $aAllEventTypes;
        $aOutput['football_id'] = $iFootballTypeId;
        $aOutput['next_market'] = $oNext_market->result;

        return view('welcome')->with('output', $aOutput);
    }
    public function testExample()
    {
        $bf = new BetFairApiExample();
        $output = '';

        $user = BetFairUser::find(1);
        $SESSION_TOKEN = $user->betfair_session;
        //$SESSION_TOKEN = "Nq63RqWQTa7MfYjDrdPXlIej6cwtejeH63lue2QIFCs=";
        //$SESSION_TOKEN = $this->login($APP_KEY);

        //$output .= "Session Token: ".$SESSION_TOKEN."\n";
        $output .= "1. Get all Event Types....<br>";
        $allEventTypes = $bf->getAllEventTypes($this->APP_KEY, $SESSION_TOKEN);
        foreach ($allEventTypes as $eType)
        {
            $output .= $eType->eventType->id." -> ".$eType->eventType->name."<br>";
        }

        $output .= "2. Extract Event Type Id for Horse Racing....<br>";

        $horseRacingEventTypeId = $bf->extractHorseRacingEventTypeId($allEventTypes);
        $output .= "3. EventTypeId for Horse Racing is: $horseRacingEventTypeId <br>";
        $output .= "4. Get next horse racing market in the UK....<br>";

        $nextHorseRacingMarket = $bf->getNextUkHorseRacingMarket($this->$APP_KEY, $SESSION_TOKEN, $horseRacingEventTypeId);
        echo "5. Print static marketId, name and runners....<br>";
        $bf->printMarketIdAndRunners($nextHorseRacingMarket);

        echo "\n6. Get volatile info for Market including best 3 exchange prices available...\n";
        $marketBook = $bf->getMarketBook($APP_KEY, $SESSION_TOKEN, $nextHorseRacingMarket->marketId);
        echo "\n7. Print volatile price data along with static runner info....\n";
        $bf->printMarketIdRunnersAndPrices($nextHorseRacingMarket, $marketBook);


        /*echo "\n\n8. Place a bet below minimum stake to prevent the bet actually being placed....\n";
        $betResult = placeBet($APP_KEY, $SESSION_TOKEN, $nextHorseRacingMarket->marketId, $nextHorseRacingMarket->runners[0]->selectionId);
        echo "\n9. Print result of bet....\n\n";*/
        //printBetResult($betResult);

        return $output;
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