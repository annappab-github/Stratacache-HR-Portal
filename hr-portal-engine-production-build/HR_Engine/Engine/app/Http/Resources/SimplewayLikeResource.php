<?php

namespace App\Http\Resources;

use App\Models\Enums\FlightType;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SimplewayAirportResource;
use App\Models\Airline;

class SimplewayLikeResource extends JsonResource
{

    private function getHost() {
      $host = trim(env('IMAGE_FILE_HOST_PATH', null));
      if (!isset($host) || $host == '') return null;
      $host = str_replace('\\', '/', $host);
      $revHost = strrev($host);
      while(str_starts_with($revHost, '/')) {
        $revHost = substr($revHost, 1);
      }
      return strrev($revHost).'/';
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      // IATA Only
      // Data Archive (do we? how long?)
      // Field Updates (who did it [type], when)
      $primaryFlightNumber = $this->flight_number;
      $primaryAirline = Airline::where('code', $this->airline_code)->where('code_type', $this->airline_code_type)->first();
      $defaultAirline = Airline::where('code', '`DEF`')->where('code_type', '`DEF`')->first();
      $host = $this->getHost();
      $primaryAirportKey = 'airport_arriving';
      $gate = $this->departure_gate;
      if ($this->isArrival())
        $primaryAirportKey = 'airport_departing';
        $gate = $this->arrival_gate;
      $this->load($primaryAirportKey)->with('codeshares')->with('codeshares.airline')->get();
      $time = $this->calculateTime($this);
      $arrivalTime = $this->getArrivalTime($this);
      $departureTime = $this->getDepartureTime($this);
      $result = [
        'id' => $this->u_id,
        "airline-logo-dark-large" => isset($primaryAirline) && isset($host) ? $host.$primaryAirline->logo_dark_large : (isset($host) ? $host.$defaultAirline->logo_dark_large : null),
        "airline-logo-dark-small" => isset($primaryAirline) && isset($host) ? $host.$primaryAirline->logo_dark_small : (isset($host) ? $host.$defaultAirline->logo_dark_small : null),
        "airline-logo-light-large" => isset($primaryAirline) && isset($host) ? $host.$primaryAirline->logo_light_large : (isset($host) ? $host.$defaultAirline->logo_light_large : null),
        "airline-logo-light-small" => isset($primaryAirline) && isset($host) ? $host.$primaryAirline->logo_light_small : (isset($host) ? $host.$defaultAirline->logo_light_small : null),
        "numbers" => [],
        "airlines" => [],
        "direction" => $this->flight_type,
        "airport" => new SimplewayAirportResource($this->{$primaryAirportKey}),
        "status" => $this->status,
        "time" => $time,
        "departureTime" => $departureTime,
        "arrivalTime" => $arrivalTime,
        "gate" => $gate,
        "belt" => $this->baggage,
        "remark" => ""
      ];
      $primaryFlightAirlineCode = $this->airline_code;
      if (isset($primaryFlightNumber)) array_push($result["numbers"], $primaryFlightNumber);
      if (isset($primaryFlightAirlineCode)) array_push($result["airlines"], $primaryFlightAirlineCode);
      if ($this->codeshares->count() > 0) {
        if (isset($this->codeshares->code) )
          array_push($result["numbers"], $this->codeshares->code);
        if (isset($this->codeshares->flight_number) ) array_push($result["airlines"], $this->codeshares->flight_number);
      }
      return $result;
    }

    private function calculateTime($masterFlightRecord) {
      $statusTimeKey = $masterFlightRecord->status.'_time_utc';
      $resultingTime = null;
      if ($masterFlightRecord->isArrival())
        $resultingTime = $masterFlightRecord->scheduled_arrival_utc;
      if ($masterFlightRecord->isDeparture())
        $resultingTime = $masterFlightRecord->scheduled_departure_utc;
      if (isset($masterFlightRecord->{$statusTimeKey}))
        $resultingTime = $masterFlightRecord->{$statusTimeKey};
      return $this->formatTime($resultingTime);
    }

    private function getDepartureTime($masterFlightRecord) {
      $time = $masterFlightRecord->scheduled_departure_utc;
      if ($masterFlightRecord->isDeparture())
        $time = $this->getStatusTime($masterFlightRecord, $time);
      return $this->formatTime($time);
    }

    private function getArrivalTime($masterFlightRecord) {
      $time = $masterFlightRecord->scheduled_arrival_utc;
      if ($masterFlightRecord->isArrival())
        $time = $this->getStatusTime($masterFlightRecord, $time);
      return $this->formatTime($time);
    }

    private function getStatusTime($masterFlightRecord, $defaultTime=null) {
      // TODO: Fix this, needs to have logic around what's availabe to get most accurate times.
      $statusTimeKey = $masterFlightRecord->status.'_time_utc';
      if(isset($masterFlightRecord->{$statusTimeKey}))
        return $masterFlightRecord->{$statusTimeKey};
      return $defaultTime;
    }

    private function formatTime($time) {
      return isset($time) ? (new DateTime($time))->setTimezone(new DateTimeZone(env('CLIENT_TIMEZONE', 'New_York')))->format('Y-m-d H:i:s'): null;
    }
}
