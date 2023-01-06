<?php

namespace App\Http\Resources;

use App\Models\Enums\FlightType;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SimplewayAirportResource;
use App\Models\Airline;
use App\Models\MasterFlightOverride;
use Illuminate\Support\Facades\Config;

class ConsolidatedOAGFlightRecordResource extends JsonResource
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

    protected function getAirlineLogos($flight) {
      $host = $this->getHost();
      $airline = $this->airline ?? new Airline();
      return [
        "airline-logo-dark-large" => $host . ($airline->logo_dark_small ?? 'default-dark-large.png'),
        "airline-logo-dark-small" => $host . ($airline->logo_dark_large ?? 'default-dark-small.png'),
        "airline-logo-light-large" =>$host . ($airline->logo_light_small ?? 'default-light-large.png'),
        "airline-logo-light-small" =>$host . ($airline->logo_light_large ?? 'default-light-small.png')
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
      $flightOverride = $this->override;
      // dump([$this->u_id, $flightOverride->gate, $this->resource->get_primary_airport_gate()]);
      $result = array_merge([
        'id' => $flightOverride->uid ?? $this->u_id,
        "flightId" => $this->u_id,
        "numbers" => [],
        "airlines" => [],
        "direction" => $this->flight_type,
        "status" => $flightOverride->status ?? $this->status,
        "time" => $this->calculateTime($this, $flightOverride->estimated_flight_time),
        "departureTime" => $this->getDepartureTime($this, $flightOverride->estimated_flight_time),
        "arrivalTime" => $this->getArrivalTime($this, $flightOverride->estimated_flight_time),
        "belt" => $flightOverride->baggage ?? $this->baggage,
        "remark" => $flightOverride->remarks,
        "codeType" => $this->airline_code_type,
        "isUnbound" => (bool)$flightOverride->is_custom,
        "gate" => $flightOverride->gate ?? $this->resource->get_primary_airport_gate(),
      ],
      $this->getAirlineLogos($this),
      $this->getAirportInfo($this));
      array_push($result["numbers"], $this->flight_number);
      array_push($result["airlines"], $this->airline_code);
      if (isset($this->codeshares)) {
        foreach($this->codeshares as $codeshare) {
          array_push($result["numbers"], $codeshare->flight_number);
          array_push($result["airlines"], $codeshare->airline_code);
        }
      }
      return $result;
    }

    protected function getAirportInfo($master_flight) {
      $airport_data = $master_flight->get_primary_airport();
      $airport = [
        "code" => $airport_data->code,
        "codeType" => $airport_data->code_type,
        "city" => $airport_data->city,
        "state" => $airport_data->state_id,
      ];
      if (isset($airport_data->city)){
        $city = strrev($airport_data->city);
        $startSlashPos = strpos($city, "/");
        if ($startSlashPos !== false && $startSlashPos == 0) {
          $city = substr_replace($city, "", $startSlashPos, 1);
        }
        $city = strrev(str_replace("/", " / ", $city));
        $airport["city"] = $city;
      }
      return [
        "airport" => $airport
      ];
    }

    protected function calculateTime($flight, $flightTimeOverride) {
      if ($this->isArrival())
        return $this->getArrivalTime($flight, $flightTimeOverride);
      if ($this->isDeparture())
        return $this->getDepartureTime($flight, $flightTimeOverride);
    }

    protected function getDepartureTime($flight, $flightTimeOverride) {
      if ($this->isDeparture() && isset($flightTimeOverride)) {
        return $this->formatTime($flightTimeOverride);
      }
      if (!isset($flight->scheduled_departure_utc))
        return null;
      $time = $flight->scheduled_departure_utc;
      if (isset($flight->out_gate_time_utc))
        $time = $flight->out_gate_time_utc;
      return $this->formatTime($time);
    }

    protected function getArrivalTime($flight, $flightTimeOverride) {
      if ($this->isArrival() && isset($flightTimeOverride)) {
        return $this->formatTime($flightTimeOverride);
      }
      if (!isset($flight->scheduled_arrival_utc))
        return null;
      $time = $flight->scheduled_arrival_utc;
      if (isset($flight->in_gate_time_utc))
        $time = $flight->in_gate_time_utc;
      return $this->formatTime($time);
    }

    protected function formatTime($time) {
      return isset($time) ? (new DateTime($time))->format('Y-m-d H:i:s') : null;
    }
}
