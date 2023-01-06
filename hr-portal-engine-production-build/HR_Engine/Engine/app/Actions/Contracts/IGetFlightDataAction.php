<?php

namespace App\Actions\Contracts;

Interface IGetFlightDataAction {
  public static function retrieveAllFlightData(?String $airport, ?String $data, ?String $time);
  public static function retrieveArrivalFlightData(?String $airport, ?String $data, ?String $time);
  public static function retrieveDepartureFlightData(?String $airport, ?String $data, ?String $time);
  public static function getParamsArrayForFlightsNow(): array;
}