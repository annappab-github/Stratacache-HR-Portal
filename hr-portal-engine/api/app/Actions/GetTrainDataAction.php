<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use GuzzleHttp\Client;
use Carbon\Carbon;

class GetTrainDataAction
{
    use AsAction;

    public static function getTrainSimulatorData()
    {
        $currentTime = Carbon::now('Asia/Kolkata');
            $client = new \GuzzleHttp\Client(); 

            $response = $client->request('GET', 'http://10.10.2.84:8000/api/metroSimulationData');
            $trim = substr($response->getBody(), 12);
            $trainData = $currentTime->toTimeString().$trim;
            $train = [];
            array_push($train, $trainData);
            if (str_contains($train[0], 'MessageIdentifier:TST')) { 
                echo 'skip';
            }
            else {
                $result =  $train[0];
                return $result;   
            } 
    }
}
