<?php

namespace App\Http\Resources;

use App\Models\Airline;
use App\Models\Enums\FlightType;
use DateTime;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use stdClass;

class UnboundFlightResource extends JsonResource
{

    protected function getHost() {
      $host = trim(Config::get('app.clientImageFileHostPath', null));
      if (!isset($host) || $host == '') return null;
      $host = str_replace('\\', '/', $host);
      $revHost = strrev($host);
      while(str_starts_with($revHost, '/')) {
        $revHost = substr($revHost, 1);
      }
      return strrev($revHost).'/';
    }
    
    protected function airlineLogoInfo($flight) {
      $host = $this->getHost();
      $defaultLogo = 'default';
      $airline = $flight->airline ?? new Airline();
      $null_if = function($v, $a) {
        if (is_null($v) || !isset($v)) return null;
        foreach($a as $av) {
          if ($v == $av) return null;
        }
        return $v;
      };
      return [
        "airline-logo-dark-large" => $host . ($null_if(trim($airline->logo_dark_large), ['']) ?? ($defaultLogo . '-dark-large.png')),
        "airline-logo-dark-small" => $host . ($null_if(trim($airline->logo_dark_small), ['']) ?? ($defaultLogo . '-dark-small.png')),
        "airline-logo-light-large" =>$host . ($null_if(trim($airline->logo_light_large), ['']) ?? ($defaultLogo . '-light-large.png')),
        "airline-logo-light-small" =>$host . ($null_if(trim($airline->logo_light_small), ['']) ?? ($defaultLogo . '-light-small.png'))
      ];
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge([
            'id' => $this->uid,
            'flightId' => $this->flight_guid,
            'numbers' => [$this->flight_number],
            'airlines' => [$this->airline_iata_code],
            'direction' => $this->flight_type,
            'status' => $this->status,
            'time' => $this->calculateTime($this, null),
            'departureTime' => $this->getDepartureTime($this, null),
            'arrivalTime' => $this->getArrivalTime($this, null),
            'belt' => $this->baggage,
            'remark' => $this->remark,
            'codeType' => $this->airline_code_type,
            'airport' => $this->getAirport($this),
            'gate' => $this->gate,
            "isUnbound" => (bool)$this->is_custom,
          ],
          $this->airlineLogoInfo($this),
        );
    }

    private function getAirport($flight) {
      $airport_key = 'arrival_airport_';
      if ($this->isArrival($flight))
        $airport_key = 'departure_airport_';
      return [
        'code' => $flight->{$airport_key . 'code'},
        'city' => $flight->{$airport_key . 'city'},
        'state' => $flight->{$airport_key . 'state_id'}
      ];
    }

    protected function isDeparture($flight) {
      return $flight->flight_type == FlightType::DEPARTURE;
    }

    protected function isArrival($flight) {
      return $flight->flight_type == FlightType::ARRIVAL;
    }

    protected function calculateTime($flight, $flightTimeOverride) {
      if ($this->isArrival($flight))
        return $this->getArrivalTime($flight, $flightTimeOverride);
      if ($this->isDeparture($flight))
        return $this->getDepartureTime($flight, $flightTimeOverride);
    }
    
    protected function getDepartureTime($flight, $flightTimeOverride) {
      if ($this->isArrival($flight) && isset($flightTimeOverride)) {
        return $this->formatTime($flightTimeOverride);
      }
      if (!isset($flight->scheduled_departure_time))
        return null;
      $time = $flight->scheduled_departure_time;
      return $this->formatTime($time);
    }

    protected function getArrivalTime($flight, $flightTimeOverride) {
      if ($this->isArrival($flight) && isset($flightTimeOverride)) {
        return $this->formatTime($flightTimeOverride);
      }
      if (!isset($flight->scheduled_arrival_time))
        return null;
      $time = $flight->scheduled_arrival_time;
      return $this->formatTime($time);
    }

    protected function formatTime($time) {
      return isset($time) ? (new DateTime($time))->format('Y-m-d H:i:s') : null;
    }
}
