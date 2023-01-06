<?php

namespace App\Actions;

use App\Models\Enums\FlightType;
use App\Actions\Contracts\IGetFlightDataAction;
use App\Actions\Contracts\IGetFlightDataAsyncAction;
use App\Http\Resources\Provider\OAGResponseResource;
use DateInterval;
use GuzzleHttp\Client;
use \Psr\Http\Message\ResponseInterface;
use DateTime;
use DateTimeZone;
use Error;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;

class GetOagFlightDataAsyncAction implements IGetFlightDataAsyncAction {
  private static function getUrlConfiguration() {
    return (object)[
      'account' => Config::get('app.oagAccessAccount'),
      'url' => "http://xml.flightview.com/" . Config::get('app.oagAccessAccount') . "/fvXML.exe?",
      'u' => Config::get('app.oagAccessUser'),
      'p' => Config::get('app.oagAccessSecret'),
    ];
  }
  
  private function getMockData() {
    return (array)json_decode(file_get_contents(__DIR__.'/mock-oag-flight-data.json'));
  }

  public static function retrieveAllFlightDataAsync(?String $airport, ?DateTime $startTime, ?Array $hoursOffsets = [0]) {
    $actionHandler = (new GetOagFlightDataAsyncAction);
    /*self::validateAirportIataCode($airport);
    $client = new Client(['base_uri' => (self::getUrlConfiguration())->url]);
    $promises = $actionHandler->getAllFlightClientRequests($client, $airport, $startTime, $hoursOffsets);*/

    $promises = $actionHandler->getMockData();
    // Wait for the requests to complete, even if some of them fail
    $results = [
      FlightType::ARRIVAL => [],
      FlightType::DEPARTURE => [],
    ];
    $arrivalCount = count($promises[FlightType::ARRIVAL]);
    Utils::all([...$promises[FlightType::ARRIVAL], ...$promises[FlightType::DEPARTURE]])
      ->then(function (array $responses) use (&$results, $arrivalCount, $promises) {
        //Log::debug($responses);
        for($i = 0; $i < count($responses); $i++) {
          $key = FlightType::ARRIVAL;
          if ($i >= $arrivalCount) {
            $key = FlightType::DEPARTURE;
          }
          //$flight = json_decode($responses[$i]->getBody());
          $flight = $responses[$i];
          //Log::debug($flight);
          array_push($results[$key], $flight);
          // dump(['_ID' => $key, 'CNT' => count($flight->Flights), 'PRM' => $promises[$key.'qs']]);
        }
      })->wait();
    $results[FlightType::ARRIVAL] = OAGResponseResource::arrivalFlightCollection($results[FlightType::ARRIVAL]);
    $results[FlightType::DEPARTURE] = OAGResponseResource::departureFlightCollection($results[FlightType::DEPARTURE]);
    return $results;
  }

  private function getRequestQueryParams($airport, $startTime, $offsets, $isArrival=true) {
    $uconf = self::getUrlConfiguration();
    $key = 'dep';
    if ($isArrival)
      $key = 'arr';
    return array_map(function($o) use ($key, $airport, $startTime, $uconf) {
      $offsetDateTime = $this->applyDateTimeOffset($startTime, $o);
      return [
        'a' => $uconf->u,
        'b' => $uconf->p,
        $key . 'ap' => $airport,
        $key . 'date' => $this->formatDate($offsetDateTime),
        $key . 'hr' => $this->formatTime($offsetDateTime)
      ];
    }, $offsets);
  }

  private function getArrivalsQueryParams($airport, $startTime, $offsets) {
    return $this->getRequestQueryParams($airport, $startTime, $offsets, true);
  }

  private function getDeparturesQueryParams($airport, $startTime, $offsets) {
    return $this->getRequestQueryParams($airport, $startTime, $offsets, false);
  }

  private function getAllFlightClientRequests(Client $client, $airport, $startTime, $offsets) {
    return [
      FlightType::ARRIVAL => [...$this->getArrivalClientRequests($client, $airport, $startTime, $offsets)[FlightType::ARRIVAL]],
      FlightType::DEPARTURE => [...$this->getDepartureClientRequests($client, $airport, $startTime, $offsets)[FlightType::DEPARTURE]],
    ];
  }

  private function getArrivalClientRequests(Client $client, $airport, $startTime, $offsets) {
    $qs = $this->getArrivalsQueryParams($airport, $startTime, $offsets);
    return [FlightType::ARRIVAL => array_map(function($qs) use ($client) {
      return $client->getAsync('?', ['query' => $qs]);
    }, $qs)];
  }

  private function getDepartureClientRequests(Client $client, $airport, $startTime, $offsets) {
    $qs = $this->getDeparturesQueryParams($airport, $startTime, $offsets);
    return [FlightType::DEPARTURE => array_map(function($qs) use ($client) {
      return $client->getAsync('?', ['query' => $qs]);
    }, $qs)];
  }

  public static function retrieveArrivalFlightDataAsync(?String $airport, ?DateTime $startTime, ?Array $hoursOffsets = [0]) {
    // self::validateAirportIataCode($airport);
    // $airport = strtolower($airport);
    // $uconf = self::getUrlConfiguration();
    // $url = $uconf->url . "a=$uconf->u&b=$uconf->p&arrap=$airport&arrdate=$date&arrhr=$time";
    // return self::getFlights($url);
  }
  public static function retrieveDepartureFlightDataAsync(?String $airport, ?DateTime $startTime, ?Array $hoursOffsets = [0]) {
    // self::validateAirportIataCode($airport);
    // $airport = strtolower($airport);
    // $uconf = self::getUrlConfiguration();
    // $url = $uconf->url . "a=$uconf->u&b=$uconf->p&depap=$airport&depdate=$date&dephr=$time";
    // return self::getFlights($url);
  }

  private static function validateAirportIataCode($airportIataCode) {
    if (!isset($airportIataCode) || strlen($airportIataCode) != 3)
      throw new Error('Invalid or empty airline IATA code configured, cannot continue without a valid IATA code.');
  }

  private function applyDateTimeOffset($dateTime, $offset) {
    $dateTimeClone = clone $dateTime;
    $interval = new DateInterval('PT' . strval(abs($offset)) . 'H');
    if ($offset < 0)
      $interval->invert = 1;
    $dateTimeClone->add($interval);
    return $dateTimeClone;
  }

  private function formatDate($dateTime) {
    return date('Ymd', $dateTime->getTimestamp()); // Current sys date
  }

  private function formatTime($dateTime) {
    return date('Hi', $dateTime->getTimestamp()); // Current sys time 24hr format
  }

  public static function getParamsArrayForFlightsNow(?Array $hoursOffset = [0]): array {
    $iataCode = Config::get('app.clientIataCode');
    $currDateTime = new DateTime(Date::now(new DateTimeZone(Config::get('app.clientTimeZone'))));
    return [
      isset($iataCode) && strlen($iataCode) > 0 ? $iataCode : null, // IATA Code
      $currDateTime,
      $hoursOffset
    ];
  }

}