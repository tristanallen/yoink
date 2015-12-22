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
        $params = '{"filter":{"eventTypeIds":["' . $eventTypeId . '"],
              "marketCountries":["GB"],
              "marketStartTime":{"from":"' . date('c') . '"}},
              "sort":"FIRST_TO_START",
              "maxResults":"1"}';
        $jsonResponse = $this->sportsApingRequest($appKey, $sessionToken, 'listMarketCatalogue', $params);

        if (!array_key_exists(0,  $jsonResponse[0]->result))
            return 'No results!';
        else
            return $jsonResponse[0]->result[0];
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
            exit(-1);
        } else {
            return $response;
        }
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