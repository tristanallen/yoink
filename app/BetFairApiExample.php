<?php
/**
 * Created by PhpStorm.
 * User: Tris
 * Date: 20/12/2015
 * Time: 14:19
 */

namespace App;


class BetFairApiExample {

    private $DEBUG = true;

    public function getAllEventTypes($appKey, $sessionToken)
    {
        $this->log( "Getting event types: " . "\n");
        $jsonResponse = $this->sportsApingRequest($appKey, $sessionToken, 'listEventTypes', '{"filter":{}}');
        return $jsonResponse[0]->result;
    }
    public function extractHorseRacingEventTypeId($allEventTypes)
    {
        foreach ($allEventTypes as $eventType) {
            if ($eventType->eventType->name == 'Horse Racing') {
                return $eventType->eventType->id;
            }
        }
    }
    public function getNextUkHorseRacingMarket($appKey, $sessionToken, $horseRacingEventTypeId)
    {
        $params = '{"filter":{"eventTypeIds":["' . $horseRacingEventTypeId . '"],
              "marketCountries":["GB"],
              "marketTypeCodes":["WIN"],
              "marketStartTime":{"from":"' . date('c') . '"}},
              "sort":"FIRST_TO_START",
              "maxResults":"1",
              "marketProjection":["RUNNER_DESCRIPTION"]}';
        $jsonResponse = $this->sportsApingRequest($appKey, $sessionToken, 'listMarketCatalogue', $params);
        return $jsonResponse[0]->result[0];
    }
    public function printMarketIdAndRunners($nextHorseRacingMarket)
    {
        $this->log( "MarketId: " . $nextHorseRacingMarket->marketId . "\n");
        $this->log( "MarketName: " . $nextHorseRacingMarket->marketName . "\n\n");
        foreach ($nextHorseRacingMarket->runners as $runner) {
            $this->log( "SelectionId: " . $runner->selectionId . " RunnerName: " . $runner->runnerName . "\n");
        }
    }
    public function getMarketBook($appKey, $sessionToken, $marketId)
    {
        $params = '{"marketIds":["' . $marketId . '"], "priceProjection":{"priceData":["EX_BEST_OFFERS"]}}';
        $jsonResponse = $this->sportsApingRequest($appKey, $sessionToken, 'listMarketBook', $params);
        return $jsonResponse[0]->result[0];
    }
    public function printAvailablePrices($selectionId, $marketBook)
    {
        // Get selection
        foreach ($marketBook->runners as $runner)
            if ($runner->selectionId == $selectionId) break;
        $this->log( "\nAvailable to Back: \n");
        foreach ($runner->ex->availableToBack as $availableToBack)
            $this->log( $availableToBack->size . "@" . $availableToBack->price . " | ");
        $this->log( "\n\nAvailable to Lay: \n");
        foreach ($runner->ex->availableToLay as $availableToLay)
            $this->log( $availableToLay->size . "@" . $availableToLay->price . " | ");
    }
    public function printMarketIdRunnersAndPrices($nextHorseRacingMarket, $marketBook)
    {
        $this->log( "MarketId: " . $nextHorseRacingMarket->marketId );
        $this->log( "MarketName: " . $nextHorseRacingMarket->marketName);
        foreach ($nextHorseRacingMarket->runners as $runner) {
            $this->log( "\n\n\n===============================================================================\n");
            $this->log( "SelectionId: " . $runner->selectionId . " RunnerName: " . $runner->runnerName . "\n");
            $this->log( $this->printAvailablePrices($runner->selectionId, $marketBook) . "\n");
        }
    }
    public function placeBet($appKey, $sessionToken, $marketId, $selectionId)
    {
        $params = '{"marketId":"' . $marketId . '",
                "instructions":
                     [{"selectionId":"' . $selectionId . '",
                       "handicap":"0",
                       "side":"BACK",
                       "orderType":
                       "LIMIT",
                       "limitOrder":{"size":"1",
                                    "price":"1000",
                                    "persistenceType":"LAPSE"}
                       }], "customerRef":"fsdf"}';
        $jsonResponse = $this->sportsApingRequest($appKey, $sessionToken, 'placeOrders', $params);
        return $jsonResponse[0]->result;
    }
    public function printBetResult($betResult)
    {
        $this->log( "Status: " . $betResult->status);
        if ($betResult->status == 'FAILURE') {
            $this->log( "\nErrorCode: " . $betResult->errorCode);
            $this->log( "\n\nInstruction Status: " . $betResult->instructionReports[0]->status);
            $this->log( "\nInstruction ErrorCode: " . $betResult->instructionReports[0]->errorCode);
        } else
            $this->log( "Warning!!! Bet placement succeeded !!!");
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