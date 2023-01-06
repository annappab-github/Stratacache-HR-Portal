<?php

namespace App\Http\Resources;

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Enums\FlightType;
use App\Models\MasterFlightOverride;

class EditFlightRecordResource extends ConsolidatedOAGFlightRecordResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      $isBound = !$this->override->is_custom;
      $result = array_merge([
        "id" => $this->override->uid,
        "flightId" => $this->u_id,
        "scheduled" => $this->calculateTime($this, null),
        "estimated" => $this->calculateTime($this, null),
        "claim" => $this->baggage,
        "checkIn" => null,
        "status" => $this->status,
        "flightType" => $this->flight_type,
        "gate" => $this->get_primary_airport_gate(),
        "overrides" => (new FlightOverrideResource($this->override))->toArray($request),
        "isUnbound" => (bool)$this->override->is_custom,
      ],
      $this->getCodeshares($this),
      $this->getAirlineInfo($this, $this->override),
      $this->getFlattenedAirportInfo($this, $this->override));
      unset($result['overrides']['uid']);
      unset($result['overrides']['isUnbound']);
      return $result;
    }

    protected function getFlattenedAirportInfo($flight, $flightOverrideModel=null) {
      $airportInfo = $this->getAirportInfo($flight);
      $airportOverrideAirport= $flight->get_primary_airport() ?? new Airport();
      return [
        'airportState' => $airportInfo['airport']['state'] ?? $airportOverrideAirport->state_id,
        'airportCity' => $airportInfo['airport']['city'] ?? $airportOverrideAirport->city,
        'airportCode' => $airportInfo["airport"]["code"] ?? $airportOverrideAirport->code,
        'airportCodeType' => $airportInfo["airport"]["codeType"] ?? $airportOverrideAirport->code_type,
        'airportName' => $airportInfo["airport"]["name"] ?? $airportOverrideAirport->name
      ];
    }

    private function getCodeshares($flight) {
      $result = ["codeshares" => []];
      if (isset($flight->codeshares)) {
        foreach($flight->codeshares as $codeshare) {
          $result['codeshares'][] = [
            "airlineCode" => $codeshare->airline_code,
            "airlineCodeType" => $codeshare->airline_code_type,
            "airlineName" => $codeshare->airline_code,
            "flightNumber" =>$codeshare->airline_name
          ];
        }
      }
      return $result;
    }

    private function getAirlineInfo($flight, $flightOverrideModel=null) {
      $flightOverrideModel = $flightOverrideModel ?? new MasterFlightOverride();
      $result = [
        "flightNumber" => $flight->flight_number ?? $flightOverrideModel->flight_number,
        "airlineCode" => $flight->airline_code ?? $flightOverrideModel->airline_iata_code,
        "airlineCodeType" => $flight->airline_code_type ?? $flightOverrideModel->airline_code_type,
      ];
      $result["airlineName"] = null;
      $airline = Airline::select('name')
        ->where('code', $result['airlineCode'])
        ->where('code_type', $result['airlineCodeType'])
        ->first();
        $result["airlineName"] = $airline->name;
      return $result;
    }
}
