<?php
/**
 * Created by PhpStorm.
 * User: Tris
 * Date: 20/12/2015
 * Time: 14:19
 */

namespace App;


class BetFairApi {

    private $DEBUG = true;
    private $APP_KEY = "oxnRnsZGGOjOwj3L";

    public function getAllEventTypes($appKey, $sessionToken)
    {
        $this->log( "Getting event types: " . "\n");
        $jsonResponse = $this->sportsApingRequest($appKey, $sessionToken, 'listEventTypes', '{"filter":{}}');
        return $jsonResponse[0]->result;
    }

    public function extractFootballEventTypeId($allEventTypes)
    {
        foreach ($allEventTypes as $eventType) {
            if ($eventType->eventType->name == 'Soccer') {
                return $eventType->eventType->id;
            }
        }
    }
    public function getNextMarket($appKey, $sessionToken, $eventTypeId)
    {

        $params = '
            {
                "filter":
                    {
                        "eventTypeIds":["' . $eventTypeId . '"],
                        "marketCountries":["GB"],
                        "marketStartTime":{"from":"' . date('c') . '"},
                        "marketTypeCodes" : ["MATCH_ODDS"]
                    },
                    "sort":"FIRST_TO_START",
                    "maxResults":"10",
                    "marketProjection":["EVENT", "RUNNER_METADATA"]
                    

            }';

        $jsonResponse = $this->sportsApingRequest($appKey, $sessionToken, 'listMarketCatalogue', $params);
        
        if (!array_key_exists(0,  $jsonResponse[0]->result)){
            return 'No results!';
        }
        else{
            $oNext_market =  $jsonResponse[0];

            $aResult = [];
            foreach ($oNext_market->result as $key => $oNext) {


                $aResult[$key]['marketId'] = $oNext->marketId;
                $aResult[$key]['marketName'] = $oNext->marketName;
                $aResult[$key]['json'] = json_encode($oNext);
                $aResult[$key]['event'] = $oNext->event;
            }

            return $aResult;

        }

    }


    /**
    * return pricing details for a given market
    **/
    public function getMarketBook($appKey, $sessionToken, $marketId)
    {
        $params = '
            {
                "marketIds":["' . $marketId . '"],
                "currencyCode": "GBP",
                "locale" : "en",
                "priceProjection":{"priceData":["EX_BEST_OFFERS"]}
            }';
        $jsonResponse = $this->sportsApingRequest($appKey, $sessionToken, 'listMarketBook', $params);

        $aReturnArray = [];
        foreach ($jsonResponse[0]->result[0]->runners as $k => $runner) {

            if($runner->selectionId == 58805){

                $aRunnerBets = [];
                $aRunnerBets['id'] = $runner->selectionId;
                $aRunnerBets['name'] ='the draw';

                $aRunnerBets['status'] = $runner->status;
                $aRunnerBets['json'] = json_encode($runner);


                foreach($runner->ex->availableToLay as $k => $lay ){

                    $aRunnerBets['availableToLay'][$k]['price'] = $lay->price;
                    $aRunnerBets['availableToLay'][$k]['size'] = $lay->size;


                };

                foreach($runner->ex->availableToBack as $k => $back ){
                    $aRunnerBets['availableToBack'][$k]['price'] = $back->price;
                    $aRunnerBets['availableToBack'][$k]['size'] = $back->size;


                };

                $aReturnArray[] = $aRunnerBets;


            }


        }

        return $aReturnArray;
    }

    public function getEventDetails($appKey, $sessionToken, $marketId)
    {
        $params = '
            {
                "filter": {
                    "marketIds":["' . $marketId . '"] 
                }
            }';
        $jsonResponse = $this->sportsApingRequest($appKey, $sessionToken, 'listEvents', $params);
        return $jsonResponse[0];
    }

    public function sportsApingRequest($appKey, $sessionToken, $operation, $params)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.betfair.com/exchange/betting/json-rpc/v1");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Application: ' . $appKey,
            'X-Authentication: ' . $sessionToken,
            'Accept: application/json',
            'Content-Type: application/json'
        ));
        $postData =
            '[{ "jsonrpc": "2.0", "method": "SportsAPING/v1.0/' . $operation . '", "params" :' . $params . ', "id": 1}]';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $this->debug('Post Data: ' . $postData);

        $response = json_decode(curl_exec($ch));
        $this->debug('Response: ' . json_encode($response));
        curl_close($ch);
        if (isset($response[0]->error)) {
            $this->log( 'Call to api-ng failed: ' . "\n" );
            $this->log(  'Response: ' . json_encode($response));
            return $response;
            exit(-1);
        } else {
            return $response;
        }
    }

    /**
     * @param $user
     * @return mixed
     */
    public function newSession($user)
    {
        $postData = 'username=' . $user->betfair_user . '&password=' . $user->betfair_password;
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


        $decoded = json_decode($response);
        return $decoded;
    }

    public function debug($debugString)
    {
        if ($this->DEBUG) {
            file_put_contents("debug_log.txt", $debugString."\n\n", FILE_APPEND | LOCK_EX);
        }
    }
    public function log($logString)
    {
        if ($this->DEBUG) {
            file_put_contents("log.txt", $logString."", FILE_APPEND | LOCK_EX);
        }
    }
}