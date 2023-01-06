<?php

namespace App\Actions\Contracts;

use DateTime;

Interface IGetFlightDataAsyncAction {
  public static function retrieveAllFlightDataAsync(?String $airport, ?DateTime $startTime, ?Array $hoursOffsets);
  public static function retrieveArrivalFlightDataAsync(?String $airport, ?DateTime $startTime, ?Array $hoursOffsets);
  public static function retrieveDepartureFlightDataAsync(?String $airport, ?DateTime $startTime, ?Array $hoursOffsets);
  public static function getParamsArrayForFlightsNow(?Array $hoursOffset): array;
}