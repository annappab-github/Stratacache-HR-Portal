<?php

namespace App\Actions;

use App\Models\Enums\FlightType;
use App\Actions\Contracts\IGetFlightDataAction;
use App\Http\Resources\Provider\OAGResponseResource;
use DateInterval;
use GuzzleHttp\Client;
use \Psr\Http\Message\ResponseInterface;
use DateTime;
use DateTimeZone;
use Error;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;

class GetOagFlightDataAction implements IGetFlightDataAction {
  private static function getUrlConfiguration() {
    return (object)[
      'account' => Config::get('app.oagAccessAccount'),
      'url' => "http://xml.flightview.com/" . Config::get('app.oagAccessAccount') . "/fvXML.exe?",
      'u' => Config::get('app.oagAccessUser'),
      'p' => Config::get('app.oagAccessSecret'),
    ];
  }
  
  public static function getMockData() {
    return (array)json_decode(file_get_contents(__DIR__.'/mock-oag-flight-data.json'));
  }

  public static function retrieveAllFlightData(?String $airport, ?String $date, ?String $time) {
    return [
      FlightType::ARRIVAL =>  OAGResponseResource::arrivalFlightCollection(self::retrieveArrivalFlightData($airport, $date, $time)),
      FlightType::DEPARTURE =>  OAGResponseResource::departureFlightCollection(self::retrieveDepartureFlightData($airport, $date, $time))
    ];
  }

  public static function retrieveArrivalFlightData(?String $airport, ?String $date, ?String $time) {
    self::validateAirportIataCode($airport);
    $airport = strtolower($airport);
    $uconf = self::getUrlConfiguration();
    $url = $uconf->url . "a=$uconf->u&b=$uconf->p&arrap=$airport&arrdate=$date&arrhr=$time";
    return self::getFlights($url);
  }
  public static function retrieveDepartureFlightData(?String $airport, ?String $date, ?String $time) {
    self::validateAirportIataCode($airport);
    $airport = strtolower($airport);
    $uconf = self::getUrlConfiguration();
    $url = $uconf->url . "a=$uconf->u&b=$uconf->p&depap=$airport&depdate=$date&dephr=$time";
    return self::getFlights($url);
  }

  private static function validateAirportIataCode($airportIataCode) {
    if (!isset($airportIataCode) || strlen($airportIataCode) != 3)
      throw new Error('Invalid or empty airline IATA code configured, cannot continue without a valid IATA code.');
  }

  private static function getFlights(string $url) {
    $result = self::getBodyContentAsObject(self::getRequest($url));
    if (isset($result) and property_exists($result, 'Flights'))
      return $result->Flights;
    return [];
  }

  private static function getBodyContentAsObject(ResponseInterface $response) {
    return json_decode($response->getBody()->getContents());
  }

  private static function getRequest($url) {
    $client = new Client();
    return $client->get($url);
  }

  public static function getParamsArrayForFlightsNow($hoursOffset = 0): array {
    $iataCode = Config::get('app.clientIataCode');
    $currDateTime = new DateTime(Date::now(new DateTimeZone(Config::get('app.clientTimeZone'))));
    $interval = new DateInterval('PT' . strval(abs($hoursOffset)) . 'H');
    if ($hoursOffset < 0)
      $interval->invert = 1;
    $currDateTime->add($interval);
    return [
      isset($iataCode) && strlen($iataCode) > 0 ? $iataCode : null, // IATA Code
      date('Ymd', $currDateTime->getTimestamp()), // Current sys date
      date('Hi', $currDateTime->getTimestamp()) // Current sys time 24hr format
    ];
  }

}